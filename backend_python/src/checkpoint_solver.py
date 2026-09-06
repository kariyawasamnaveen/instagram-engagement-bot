import asyncio
import json
import os
import random
import re
import imaplib
import email
from email.header import decode_header
import mysql.connector
import pyotp
from playwright.async_api import async_playwright
import logging
from worker import get_user_agent, Stealth

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.StreamHandler()
    ]
)

APP_DIR = "/var/www/html"
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "Root@123",
    "database": "smm_db",
    "ssl_disabled": True
}

PROXY_SETTINGS = {
    "server": "http://YOUR_AIRPROXY_SERVER:11001",
    "username": "YOUR_AIRPROXY_USER",
    "password": "YOUR_AIRPROXY_PASS_1"
}

def debug_log(msg):
    logging.info(msg)

async def human_type(locator, text):
    await locator.click(force=True)
    await asyncio.sleep(random.uniform(0.1, 0.3))
    for char in text:
        await locator.type(char, delay=random.uniform(50, 150))
        if random.random() > 0.9:
            await asyncio.sleep(random.uniform(0.2, 0.5))

async def get_email_code(email_addr, password, max_retries=6, delay=10):
    domain = email_addr.split('@')[1].lower()
    imap_server = "imap.gmail.com"
    if "hotmail" in domain or "outlook" in domain or "live" in domain:
        imap_server = "imap-mail.outlook.com"
    elif "yahoo" in domain:
        imap_server = "imap.mail.yahoo.com"
    else:
        # Default fallback, might not work for all custom domains without exact host
        imap_server = f"imap.{domain}"
        
    debug_log(f"Attempting IMAP login to {imap_server} for {email_addr}")
    for attempt in range(max_retries):
        try:
            mail = imaplib.IMAP4_SSL(imap_server)
            mail.login(email_addr, password)
            mail.select("inbox")
            
            # Search for emails from Instagram in the last few hours
            status, messages = mail.search(None, '(FROM "instagram.com" UNSEEN)')
            if not messages[0]:
                status, messages = mail.search(None, '(FROM "instagram.com")')
                
            email_ids = messages[0].split()
            if email_ids:
                # Get the latest email
                latest_email_id = email_ids[-1]
                status, msg_data = mail.fetch(latest_email_id, '(RFC822)')
                for response_part in msg_data:
                    if isinstance(response_part, tuple):
                        msg = email.message_from_bytes(response_part[1])
                        subject, encoding = decode_header(msg["Subject"])[0]
                        if isinstance(subject, bytes):
                            subject = subject.decode(encoding if encoding else "utf-8")
                        
                        body = ""
                        if msg.is_multipart():
                            for part in msg.walk():
                                if part.get_content_type() == "text/plain" or part.get_content_type() == "text/html":
                                    try:
                                        body += part.get_payload(decode=True).decode()
                                    except:
                                        pass
                        else:
                            try:
                                body = msg.get_payload(decode=True).decode()
                            except:
                                pass
                                
                        # Extract 6 digit code from body or subject
                        match = re.search(r'\b(\d{6})\b', subject + " " + body)
                        if match:
                            code = match.group(1)
                            debug_log(f"Found Instagram Code: {code}")
                            mail.logout()
                            return code
            mail.logout()
        except Exception as e:
            debug_log(f"IMAP Error on attempt {attempt+1}: {e}")
            
        debug_log(f"Code not found. Waiting {delay}s...")
        await asyncio.sleep(delay)
        
    return None

async def solve_checkpoint(p, acc, db, cursor):
    debug_log(f"Starting Checkpoint Solver for {acc['username']}")
    
    if not acc.get('two_factor_secret'):
        debug_log(f"WARNING: No 2FA secret found for {acc['username']}. Proceeding anyway to see if direct login works.")

    ua = get_user_agent(acc, cursor)
    db.commit()
    
    is_mobile = "Mobile" in ua or "iPhone" in ua or "Android" in ua
    context_args = {
        "proxy": PROXY_SETTINGS,
        "user_agent": ua,
        "locale": "en-US"
    }
    if is_mobile:
        context_args["viewport"] = {"width": 390, "height": 844}
        context_args["is_mobile"] = True
        context_args["has_touch"] = True
        
    profile_dir = os.path.join(APP_DIR, 'app', 'modules', 'cron', 'controllers', 'python_worker', 'profiles', acc['username'])
    if not os.path.exists(profile_dir):
        os.makedirs(profile_dir)
        
    fingerprint = acc.get('device_fingerprint')
    if fingerprint:
        if isinstance(fingerprint, str):
            fingerprint = json.loads(fingerprint)
        context_args["timezone_id"] = fingerprint.get("timezone", "America/New_York")
        context_args["viewport"] = fingerprint.get("viewport", {"width": 390, "height": 844})

    context = await p.chromium.launch_persistent_context(
        user_data_dir=profile_dir,
        headless=True,
        args=['--no-sandbox', '--disable-dev-shm-usage', '--disable-blink-features=AutomationControlled'],
        **context_args
    )
    
    try:
        pages = context.pages
        page = pages[0] if len(pages) > 0 else await context.new_page()
        await page.route("**/*", lambda route: route.abort() if route.request.resource_type in ["image", "media", "font"] else route.continue_())
        await Stealth().apply_stealth_async(page)
        
        debug_log(f"Navigating to Instagram Login...")
        try:
            await page.goto("https://www.instagram.com/accounts/login/", wait_until="domcontentloaded", timeout=60000)
        except Exception as e:
            if "ERR_TUNNEL_CONNECTION_FAILED" in str(e) or "ERR_PROXY_CONNECTION_FAILED" in str(e):
                debug_log(f"PROXY ERROR: Connection Failed! Exception: {e}")
                await context.close()
                return False
            raise
            
        await asyncio.sleep(random.uniform(5, 8))
        
        # Check if already logged in by some miracle
        if await page.locator('svg[aria-label="Home"], svg[aria-label="Search"]').first.is_visible(timeout=5000):
            debug_log(f"Account {acc['username']} is already logged in!")
            cookies = await context.cookies()
            cursor.execute("UPDATE ig_accounts SET status = 1, cookies = %s, status_message = 'Recovered' WHERE id = %s", (json.dumps(cookies), acc['id']))
            db.commit()
            await context.close()
            return True

        from worker import handle_popups
        await handle_popups(page)
        await asyncio.sleep(2)

        # Enter Username and Password
        user_input = page.locator('input[name="username"]')
        pass_input = page.locator('input[name="password"]')
        
        if await user_input.is_visible():
            await human_type(user_input, acc['username'])
            await asyncio.sleep(1)
            await human_type(pass_input, acc['password'])
            await asyncio.sleep(1)
            
            await pass_input.press("Enter")
            debug_log("Submitted login credentials using Enter key.")
            
            # Wait for either home page, 2FA prompt, or error
            await asyncio.sleep(10)
        else:
            debug_log("Login inputs not found, might be at checkpoint already.")

        # Handle "Save your login info"
        save_info = page.locator('button:has-text("Save Info"), button:has-text("Not Now")').first
        if await save_info.is_visible(timeout=5000):
            await save_info.click()
            await asyncio.sleep(3)

        # Check for "Send Security Code" button (Email/SMS challenge)
        send_code_btn = page.locator('button:has-text("Send Security Code")')
        if await send_code_btn.is_visible(timeout=5000):
            debug_log("Challenge detected. Clicking 'Send Security Code'...")
            await send_code_btn.click()
            await asyncio.sleep(5)

        # Check for 2FA or Email Verification input
        security_code_input = page.locator('input[name="verificationCode"], input[name="security_code"], input[aria-label*="Security Code"], input[aria-label*="verification"]')
        
        if await security_code_input.is_visible(timeout=10000):
            content = await page.content()
            code_submitted = False
            
            if "authenticator app" in content.lower() or "two-factor" in content.lower():
                if acc.get('two_factor_secret'):
                    debug_log("2FA Checkpoint detected! Generating code...")
                    totp = pyotp.TOTP(acc['two_factor_secret'].replace(" ", ""))
                    code = totp.now()
                    debug_log(f"Generated 2FA Code: {code}")
                    await human_type(security_code_input, code)
                    code_submitted = True
                else:
                    debug_log(f"2FA Checkpoint detected for {acc['username']} but NO SECRET provided!")
            elif "email" in content.lower() or "sent a code to" in content.lower() or "@" in content.lower():
                if acc.get('email') and acc.get('email_password'):
                    debug_log(f"Email Verification Checkpoint detected! Fetching code via IMAP...")
                    code = await get_email_code(acc['email'], acc['email_password'])
                    if code:
                        debug_log(f"Entering Email Code: {code}")
                        await human_type(security_code_input, code)
                        code_submitted = True
                    else:
                        debug_log("Failed to fetch email code.")
                else:
                    debug_log(f"Email Checkpoint detected but NO EMAIL PASSWORD provided in DB!")
            else:
                # Ambiguous, try 2FA first if we have it
                if acc.get('two_factor_secret'):
                    debug_log("Ambiguous Checkpoint. Trying 2FA...")
                    totp = pyotp.TOTP(acc['two_factor_secret'].replace(" ", ""))
                    await human_type(security_code_input, totp.now())
                    code_submitted = True
                elif acc.get('email') and acc.get('email_password'):
                    debug_log("Ambiguous Checkpoint. Trying Email Verification...")
                    code = await get_email_code(acc['email'], acc['email_password'])
                    if code:
                        await human_type(security_code_input, code)
                        code_submitted = True
            
            if code_submitted:
                await asyncio.sleep(1)
                confirm_btn = page.locator('button:has-text("Confirm"), button:has-text("Submit")')
                if await confirm_btn.is_visible():
                    await confirm_btn.click()
                else:
                    await security_code_input.press("Enter")
                debug_log("Submitted code. Waiting for response...")
                await asyncio.sleep(10)
            else:
                debug_log("Cannot proceed with checkpoint. Closing.")
                await context.close()
                return False

        # Final Check: Are we on the home page now?
        if await page.locator('svg[aria-label="Home"], svg[aria-label="Search"], a[href="/explore/"]').first.is_visible(timeout=15000):
            debug_log(f"SUCCESS: Checkpoint solved and logged into {acc['username']}!")
            cookies = await context.cookies()
            cursor.execute("UPDATE ig_accounts SET status = 1, cookies = %s, status_message = 'Recovered' WHERE id = %s", (json.dumps(cookies), acc['id']))
            db.commit()
            await context.close()
            return True
        else:
            # Maybe blocked or wrong password
            content = await page.content()
            if "incorrect" in content.lower():
                debug_log(f"FAILED: Incorrect password for {acc['username']}")
            elif "suspended" in content.lower() or "disabled" in content.lower():
                debug_log(f"FAILED: Account {acc['username']} is permanently suspended.")
                cursor.execute("UPDATE ig_accounts SET status = 4, status_message = 'Suspended' WHERE id = %s", (acc['id'],))
                db.commit()
            else:
                debug_log(f"FAILED: Unknown error after login attempt for {acc['username']}")
                await page.screenshot(path=f"error_{acc['username']}_login.png")
            
            await context.close()
            return False
            
    except Exception as e:
        debug_log(f"Exception during checkpoint solver for {acc['username']}: {str(e)}")
        await context.close()
        return False

async def main():
    debug_log("Account Recovery Engine Started...")
    while True:
        try:
            db = mysql.connector.connect(**DB_CONFIG)
            cursor = db.cursor(dictionary=True)
            
            # Unblock soft-blocked accounts if cooldown has expired
            cursor.execute("UPDATE ig_accounts SET status = 1, status_message = 'Soft Block Expired' WHERE status = 5 AND cooldown_until <= NOW()")
            db.commit()
            
            # Get all blocked accounts (status 3)
            cursor.execute("SELECT * FROM ig_accounts WHERE status = 3 ORDER BY id DESC")
            accounts = cursor.fetchall()
            
            if not accounts:
                debug_log("No blocked accounts found. Sleeping for 30 minutes...")
                cursor.close()
                db.close()
                await asyncio.sleep(1800)
                continue

            debug_log(f"Found {len(accounts)} accounts to recover.")
            
            async with async_playwright() as p:
                for acc in accounts:
                    await solve_checkpoint(p, acc, db, cursor)
                    
            cursor.close()
            db.close()
            
            debug_log("Recovery cycle complete. Sleeping for 30 minutes...")
            await asyncio.sleep(1800)
            
        except Exception as e:
            debug_log(f"Engine Error: {str(e)}. Retrying in 5 minutes...")
            await asyncio.sleep(300)

if __name__ == "__main__":
    asyncio.run(main())
