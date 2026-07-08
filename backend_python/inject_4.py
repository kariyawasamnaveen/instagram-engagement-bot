import base64
import json
import asyncio
from playwright.async_api import async_playwright
import mysql.connector

# The base64 tokens from the user
tokens = [
    "eyJkc191c2VyX2lkIjoiNTI2NzU4MjM5NDciLCJzZXNzaW9uaWQiOiI1MjY3NTgyMzk0NyUzQUVEUlJDWWx0ZHZncXM0JTNBMTglM0FBWWpub0UwczhYYUZWakFTR3ZXeU81OTIzeE1SUEFOTWJxT1U4S0dURUEifQ==",
    "eyJkc191c2VyX2lkIjoiNTQwNDI3MzIzNzUiLCJzZXNzaW9uaWQiOiI1NDA0MjczMjM3NSUzQVRDYTZKYm1UNTM0ZTZFJTNBMCUzQUFZaGNVVW9tTFplZ3pxdE0yYkozam5VOS0wbm13OG9scmpkSTMxenJiUSJ9",
    "eyJkc191c2VyX2lkIjoiNzM3MjY2MzYxNTMiLCJzZXNzaW9uaWQiOiI3MzcyNjYzNjE1MyUzQWR2MDFNMVJHYmk5a2w5JTNBMTclM0FBWWlMaUlVMzloWDFjekNJNllOSms1SmwzN3JJN0JkM3AzXzRUWHREekEifQ=="
]

DB_CONFIG = {'host': 'localhost', 'user': 'root', 'password': 'Root@123', 'database': 'smm_db'}
PROXY = {"server": "http://YOUR_AIRPROXY_SERVER:30909", "username": "YOUR_AIRPROXY_USER", "password": "YOUR_AIRPROXY_PASS_3"}

async def process_account(token_b64):
    decoded = base64.b64decode(token_b64).decode('utf-8')
    data = json.loads(decoded)
    sessionid = data['sessionid']
    ds_user_id = data['ds_user_id']
    
    print(f"\nProcessing ds_user_id: {ds_user_id}")
    
    async with async_playwright() as p:
        profile_dir = f"/var/www/html/app/modules/cron/controllers/python_worker/profiles/account_{ds_user_id}"
        context = await p.chromium.launch_persistent_context(
            user_data_dir=profile_dir,
            headless=True,
            proxy=PROXY,
            locale="en-US"
        )
        
        cookie = {
            "name": "sessionid",
            "value": sessionid,
            "domain": ".instagram.com",
            "path": "/",
            "httpOnly": True,
            "secure": True,
            "sameSite": "Lax"
        }
        await context.add_cookies([cookie])
        
        page = context.pages[0] if context.pages else await context.new_page()
        await page.goto("https://www.instagram.com/", wait_until="domcontentloaded", timeout=45000)
        await asyncio.sleep(5)
        
        html = await page.content()
        if "Log in" in html and "Sign up" in html:
            print("FAILED: Cookie expired.")
            await context.close()
            return
            
        username = "Unknown"
        try:
            profile_link = await page.locator("a[href^='/'][href$='/']:has(img)").first.get_attribute("href", timeout=5000)
            if profile_link:
                username = profile_link.strip("/")
                print(f"SUCCESS! Username: {username}")
        except Exception as e:
            print("Could not extract username:", e)
            
        await context.close()
        
        if username != "Unknown":
            db = mysql.connector.connect(**DB_CONFIG)
            cursor = db.cursor()
            cursor.execute("INSERT IGNORE INTO ig_accounts (username, password, status, daily_actions) VALUES (%s, %s, 1, 0)", (username, "SESSION_INJECTED"))
            db.commit()
            cursor.close()
            db.close()
            print(f"Added {username} to database.")

async def main():
    for t in tokens:
        await process_account(t)

asyncio.run(main())
