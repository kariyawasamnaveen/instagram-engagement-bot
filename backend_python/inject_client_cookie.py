import asyncio
import json
from playwright.async_api import async_playwright
import mysql.connector

DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': 'Root@123',
    'database': 'smm_db'
}

PROXY = {"server": "http://YOUR_AIRPROXY_SERVER:30909", "username": "YOUR_AIRPROXY_USER", "password": "YOUR_AIRPROXY_PASS_3"}
SESSIONID = "67547559813%3AIXKuMrEkA6gzzf%3A23%3AAYiEGI7BnMmKf0ekVDKiwXY-uiLT18s5NEBNciibZA"

async def main():
    async with async_playwright() as p:
        print("Launching browser context...")
        profile_dir = "/var/www/html/app/modules/cron/controllers/python_worker/profiles/client_test_account"
        
        context = await p.chromium.launch_persistent_context(
            user_data_dir=profile_dir,
            headless=True,
            proxy=PROXY,
            locale="en-US"
        )
        
        # Inject the cookie
        cookie = {
            "name": "sessionid",
            "value": SESSIONID,
            "domain": ".instagram.com",
            "path": "/",
            "httpOnly": True,
            "secure": True,
            "sameSite": "Lax"
        }
        await context.add_cookies([cookie])
        print("Cookie injected!")
        
        page = context.pages[0] if context.pages else await context.new_page()
        
        print("Navigating to Instagram to verify login...")
        await page.goto("https://www.instagram.com/", wait_until="domcontentloaded", timeout=30000)
        await asyncio.sleep(5)
        
        # Check if logged in
        html = await page.content()
        if "Log in" in html and "Sign up" in html and "Forgot password" in html:
            print("FAILED: Cookie expired or invalid. Still on login screen.")
            await context.close()
            return
            
        print("SUCCESS: Logged in successfully using Session Cookie!")
        
        # Try to get the username
        username = "Unknown"
        try:
            # Look for the profile link in the sidebar/nav
            profile_link = await page.locator("a[href^='/'][href$='/']:has(img)").first.get_attribute("href", timeout=5000)
            if profile_link:
                username = profile_link.strip("/")
                print(f"Extracted Username: {username}")
        except Exception as e:
            print("Could not extract username from DOM:", e)
            
        await context.close()
        
        if username and username != "Unknown":
            # Add to database
            db = mysql.connector.connect(**DB_CONFIG)
            cursor = db.cursor()
            try:
                cursor.execute("INSERT IGNORE INTO ig_accounts (username, password, status, daily_actions) VALUES (%s, %s, 1, 0)", (username, "SESSION_INJECTED"))
                db.commit()
                print(f"Account {username} added/updated in database with status 1.")
            except Exception as e:
                print("DB Error:", e)
            finally:
                cursor.close()
                db.close()

asyncio.run(main())
