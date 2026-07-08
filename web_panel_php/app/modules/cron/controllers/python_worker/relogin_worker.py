import asyncio
import json
import logging
import mysql.connector
from datetime import datetime
import pyotp
import random
import time
import requests
from playwright.async_api import async_playwright

logging.basicConfig(level=logging.INFO, format='[%(asctime)s] RELOGIN BOT: %(message)s', datefmt='%Y-%m-%d %H:%M:%S')

def rotate_proxy():
    try:
        logging.info("Rotating AirProxy IP...")
        res = requests.get("https://airproxy.io/api/proxy/change_ip/?format=json&id=3301&key=YOUR_AIRPROXY_API_KEY", timeout=15)
        logging.info(f"AirProxy rotation response: {res.text}")
        time.sleep(25) # Wait for proxy to fully rotate and stabilize
        return True
    except Exception as e:
        logging.error(f"Failed to rotate AirProxy IP: {e}")
        return False

db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'Root@123',
    'database': 'smm_db'
}

PROXY_SETTINGS = {'server': 'http://YOUR_AIRPROXY_SERVER:11001', 'username': 'YOUR_AIRPROXY_USER', 'password': 'YOUR_AIRPROXY_PASS_1'}

async def perform_relogin(browser, acc, cursor, db):
    username = acc['username']
    password = acc['password']
    two_factor_secret = acc.get('two_factor_secret')
    
    proxy_url = f"http://{PROXY_SETTINGS['username']}:{PROXY_SETTINGS['password']}@YOUR_SERVER_IP:11001" if "airproxy" not in PROXY_SETTINGS['server'] else f"http://{PROXY_SETTINGS['username']}:{PROXY_SETTINGS['password']}@YOUR_AIRPROXY_SERVER:11001"
    
    # Use random user-agent
    user_agents = [
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
    ]
    ua = acc.get('user_agent') or random.choice(user_agents)

    context = await browser.new_context(
        proxy={"server": proxy_url},
        user_agent=ua,
        locale="en-US"
    )
    
    try:
        page = await context.new_page()
        logging.info(f"Navigating to Instagram login for {username}...")
        
        await page.goto("https://www.instagram.com/accounts/login/", timeout=60000, wait_until="networkidle")
        await asyncio.sleep(random.uniform(2, 4))
        
        # Handle cookie popups if any
        try:
            selectors = [
                'button:has-text("Decline optional cookies")',
                'button:has-text("Allow all cookies")',
                'div[role="button"]:has-text("Decline optional cookies")',
                'div[role="button"]:has-text("Allow all cookies")'
            ]
            for sel in selectors:
                btn = page.locator(sel).first
                if await btn.is_visible(timeout=2000):
                    logging.info(f"Found cookie popup {sel} for {username}, clicking...")
                    await btn.click(force=True)
                    await asyncio.sleep(2)
                    # Break after first successful click
                    break
        except Exception as e:
            logging.info(f"Cookie popup error: {e}")

        # Ensure popup is gone, otherwise try Escape
        try:
            if await page.locator('text="Cookies from other companies"').is_visible(timeout=1000):
                await page.keyboard.press("Escape")
                await asyncio.sleep(1)
        except: pass


        # Enter username
        username_field = page.locator('input[name="username"], input[aria-label*="username"], input[type="text"]').first
        await username_field.fill(username)
        await asyncio.sleep(random.uniform(0.5, 1.5))
        
        # Enter password
        password_field = page.locator('input[name="password"], input[aria-label*="Password"], input[type="password"]').first
        await password_field.fill(password)
        await asyncio.sleep(random.uniform(0.5, 1.5))
        
        # Click login
        login_btn = page.locator('button[type="submit"], button:has-text("Log in"), button:has-text("Log In"), div[role="button"]:has-text("Log in")').first
        try:
            if await login_btn.is_visible(timeout=5000):
                await login_btn.click()
            else:
                await page.keyboard.press("Enter")
        except:
            await page.keyboard.press("Enter")
        logging.info(f"Clicked login for {username}. Waiting for response...")
        
        # Wait for navigation or error or 2FA
        await asyncio.sleep(random.uniform(5, 8))
        
        # Check if 2FA is requested
        try:
            is_2fa = False
            two_factor_input = None
            
            # Check for the 2FA page text or specific inputs using substring match without quotes
            if await page.locator('text=authentication app').first.is_visible(timeout=5000):
                is_2fa = True
            elif await page.locator('text=6-digit code').first.is_visible(timeout=1000):
                is_2fa = True
            
            selectors = ['input[name="verificationCode"]', 'input[aria-label*="Code"]', 'input[aria-label*="code"]', 'input[autocomplete="one-time-code"]', 'input[type="tel"]']
            for sel in selectors:
                field = page.locator(sel).first
                if await field.is_visible(timeout=1000):
                    is_2fa = True
                    two_factor_input = field
                    break
                    
            if is_2fa:
                logging.info(f"2FA requested for {username}. Generating code...")
                key = acc.get('two_factor_key') or two_factor_secret
                if not key:
                    logging.info(f"No valid 2FA secret found for {username}!")
                    await context.close()
                    return False, "MISSING_2FA_SECRET"
                
                totp = pyotp.TOTP(key.replace(" ", ""))
                code = totp.now()
                
                if two_factor_input:
                    try:
                        await two_factor_input.fill(code)
                    except:
                        await page.keyboard.type(code)
                else:
                    # Fallback to typing directly if input field wasn't clearly identified
                    # Press Tab a few times to try to focus the input if it's not focused
                    await page.keyboard.press("Tab")
                    await asyncio.sleep(0.5)
                    await page.keyboard.press("Tab")
                    await asyncio.sleep(0.5)
                    await page.keyboard.type(code)
                    
                await asyncio.sleep(1)
                
                confirm_btn = page.locator('button:has-text("Confirm"), button:has-text("Submit"), button[type="button"]:has-text("Confirm")').first
                if await confirm_btn.is_visible(timeout=2000):
                    await confirm_btn.click()
                else:
                    await page.keyboard.press("Enter")
                    
                await asyncio.sleep(5)
        except Exception as e:
            logging.info(f"2FA handling error: {e}")
            
        # Check if login failed (wrong password etc)
        error_msg = page.locator('p[id="slfErrorAlert"]')
        if await error_msg.is_visible(timeout=2000):
            err_text = await error_msg.inner_text()
            logging.error(f"Login failed for {username}: {err_text}")
            await context.close()
            return False, "INVALID_CREDENTIALS"
            
        # Check if successful (Save Info prompt or Home page)
        save_info_btn = page.locator('button:has-text("Save Info"), button:has-text("Not Now")').first
        if await save_info_btn.is_visible(timeout=10000):
            await save_info_btn.click()
            await asyncio.sleep(3)
            
        # Verify we are logged in by checking if nav elements exist
        if await page.locator('svg[aria-label="Home"]').is_visible(timeout=10000):
            logging.info(f"Successfully logged into {username}!")
            
            # Extract cookies
            cookies = await context.cookies()
            cookie_str = json.dumps(cookies)
            
            # Update DB
            cursor.execute("UPDATE ig_accounts SET cookies = %s, status = 1, status_message = 'Relogged in successfully', updated = NOW() WHERE id = %s", (cookie_str, acc['id']))
            db.commit()
            
            await context.close()
            return True, "SUCCESS"
            
        logging.error(f"Failed to verify login success for {username}. Page title: {await page.title()}")
        try:
            await page.screenshot(path=f"/var/www/html/app/logs/relogin_verify_error_{username}.png")
            logging.info(f"Saved verify error screenshot to /var/www/html/app/logs/relogin_verify_error_{username}.png")
        except: pass
        await context.close()
        return False, "VERIFICATION_FAILED"
        
    except Exception as e:
        logging.error(f"Exception during relogin for {username}: {e}")
        try:
            await page.screenshot(path=f"/var/www/html/app/logs/relogin_error_{username}.png")
            logging.info(f"Saved error screenshot to /var/www/html/app/logs/relogin_error_{username}.png")
        except: pass
        await context.close()
        return False, str(e)

async def main():
    while True:
        try:
            db = mysql.connector.connect(**db_config, autocommit=True)
            cursor = db.cursor(dictionary=True)
            
            # Find accounts that are expired (status 3) or explicitly requested (status 5)
            # Limit to 1 at a time to prevent IP bans
            cursor.execute("SELECT * FROM ig_accounts WHERE status = 3 ORDER BY updated ASC LIMIT 1")
            acc = cursor.fetchone()
            
            if not acc:
                # No accounts need relogin right now
                db.close()
                await asyncio.sleep(60)
                continue
                
            logging.info(f"Found expired account: {acc['username']} (ID: {acc['id']}). Starting relogin process...")
            
            # Rotate proxy before each login attempt to prevent Instagram IP blocks
            rotate_proxy()
            
            async with async_playwright() as p:
                browser = await p.chromium.launch(
                    headless=True,
                    args=[
                        '--disable-blink-features=AutomationControlled',
                        '--no-sandbox',
                        '--disable-dev-shm-usage'
                    ]
                )
                
                success, msg = await perform_relogin(browser, acc, cursor, db)
                
                if not success:
                    # Mark as failed (status 4) so we don't retry forever, unless it's a proxy issue
                    if "INVALID_CREDENTIALS" in msg or "MISSING_2FA_SECRET" in msg:
                        logging.warning(f"Marking {acc['username']} as permanently failed (status 4).")
                        cursor.execute("UPDATE ig_accounts SET status = 4, status_message = %s, updated = NOW() WHERE id = %s", (f"Auto-relogin failed: {msg}", acc['id']))
                        db.commit()
                    else:
                        logging.warning(f"Relogin failed for {acc['username']} with transient error. Keeping status 3.")
                        cursor.execute("UPDATE ig_accounts SET status_message = %s, updated = NOW() WHERE id = %s", (f"Relogin error: {msg}", acc['id']))
                        db.commit()
                
                await browser.close()
                
            db.close()
            
            # Wait a bit before trying the next account to be safe
            logging.info("Waiting 30 seconds before checking for next account...")
            await asyncio.sleep(30)
            
        except Exception as e:
            logging.error(f"Main loop error: {e}")
            await asyncio.sleep(60)

if __name__ == "__main__":
    logging.info("Starting Auto-Relogin Bot...")
    asyncio.run(main())
