import asyncio
import json
import subprocess
import os
import time
import requests
import base64
from playwright.async_api import async_playwright

ADVANCED_STEALTH_JS = """
Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
"""

def update_db(username, status_code, cookies_list=None):
    if cookies_list:
        cookies_json = json.dumps(cookies_list)
        b64_cookies = base64.b64encode(cookies_json.encode('utf-8')).decode('utf-8')
        py_script = f"""
import mysql.connector
import base64
db = mysql.connector.connect(host='localhost',user='root',password=os.getenv('DB_PASS', 'secret'),database='smm_db')
cursor = db.cursor()
cookies_str = base64.b64decode('{b64_cookies}').decode('utf-8')
cursor.execute("UPDATE ig_accounts SET status = %s, cookies = %s WHERE username = %s", ({status_code}, cookies_str, '{username}'))
db.commit()
"""
    else:
        py_script = f"""
import mysql.connector
db = mysql.connector.connect(host='localhost',user='root',password=os.getenv('DB_PASS', 'secret'),database='smm_db')
cursor = db.cursor()
cursor.execute("UPDATE ig_accounts SET status = %s WHERE username = %s", ({status_code}, '{username}'))
db.commit()
"""
    cmd = ["sshpass", "-p", "YOUR_DB_PASSWORD", "ssh", "-o", "StrictHostKeyChecking=no", "root@YOUR_SERVER_IP", "python3", "-"]
    subprocess.run(cmd, input=py_script, text=True, capture_output=True)

async def handle_popups(page):
    print("Checking for popups (Save Info, Notifications, Cookies, etc)...")
    try:
        popup_terms = [
            "Not Now", "Not now", "Save Info", "Save info", "Cancel", "Skip", "Close", 
            "No thanks", "Decline", "Decline optional cookies", "Aceptar cookies", 
            "Cookies", "Allow essential", "Turn On", "Turn on"
        ]
        
        for term in popup_terms:
            buttons = page.locator(f'button:has-text("{term}"), div[role="button"]:has-text("{term}")')
            count = await buttons.count()
            for i in range(count):
                btn = buttons.nth(i)
                try:
                    if await btn.is_visible(timeout=1000):
                        print(f"--> Found '{term}' button. Clicking it to dismiss popup...")
                        await btn.click(timeout=3000, force=True)
                        await asyncio.sleep(2)
                except:
                    pass
                    
        close_svgs = page.locator('svg[aria-label="Close"], svg[aria-label="Dismiss"], svg[aria-label="Cerrar"], svg[aria-label="Schließen"]')
        count = await close_svgs.count()
        for i in range(count):
            svg = close_svgs.nth(i)
            try:
                if await svg.is_visible(timeout=1000):
                    print("--> Found a Close (X) icon. Clicking it...")
                    parent = svg.locator('xpath=..')
                    if await parent.is_visible():
                        await parent.click(timeout=3000, force=True)
                    else:
                        await svg.click(timeout=3000, force=True)
                    await asyncio.sleep(2)
            except:
                pass
    except Exception as e:
        print(f"Error while dismissing popups: {e}")

async def try_login(page, username, password, two_factor_secret=None):
    print(f"\n---> ATTEMPTING FRESH LOGIN FOR {username} <---")
    try:
        await page.context.clear_cookies()
        print("Navigating to Login Page...")
        await page.goto("https://www.instagram.com/accounts/login/", wait_until="domcontentloaded", timeout=60000)
        await asyncio.sleep(5)
        
        await handle_popups(page)
        
        try:
            login_splash = page.locator('button:has-text("Log in"), button:has-text("Log In"), a:has-text("Log in"), a:has-text("Log In")')
            if await login_splash.count() > 0:
                for i in range(await login_splash.count()):
                    btn = login_splash.nth(i)
                    if await btn.is_visible(timeout=1000):
                        print("--> Found splash 'Log in' button. Clicking it...")
                        await btn.click(timeout=3000, force=True)
                        await asyncio.sleep(3)
                        break
        except:
            pass
            
        print("Entering credentials...")
        username_locators = ['input[name="username"]', 'input[aria-label*="username"]', 'input[type="text"]']
        password_locators = ['input[name="password"]', 'input[aria-label*="Password"]', 'input[type="password"]']
        
        user_field = None
        for loc in username_locators:
            if await page.locator(loc).count() > 0:
                user_field = page.locator(loc).nth(0)
                break
                
        pass_field = None
        for loc in password_locators:
            if await page.locator(loc).count() > 0:
                pass_field = page.locator(loc).nth(0)
                break
                
        if not user_field or not pass_field:
            print("❌ Could not find login input fields on the screen!")
            return False, "Inputs not found"

        await user_field.fill(username, timeout=10000)
        await asyncio.sleep(1)
        await pass_field.fill(password, timeout=10000)
        await asyncio.sleep(1)
        
        print("Clicking Log In...")
        login_submit = page.locator('button[type="submit"], button:has-text("Log in"), div[role="button"]:has-text("Log in")')
        await login_submit.nth(0).click()
        
        await asyncio.sleep(10)
        
        content = await page.content()
        content_lower = content.lower()
        
        if "incorrect password" in content_lower or "sorry, your password was incorrect" in content_lower:
            print("❌ Login Failed: Incorrect Password!")
            return False, "Wrong Password"
            
        if "suspended" in content_lower or "action blocked" in content_lower or "disabled" in content_lower or "/challenge/" in page.url or "/suspended/" in page.url:
            print("❌ Login Failed: Account is Suspended/Blocked!")
            return False, "Suspended"
            
        await handle_popups(page)
        await asyncio.sleep(4)
        
        content_lower2 = (await page.content()).lower()
        if "go to your authentication app" in content_lower2 or "enter the 6-digit code" in content_lower2:
            print("⚠️ Detected 2FA Screen!")
            if two_factor_secret:
                try:
                    import pyotp
                    totp = pyotp.TOTP(two_factor_secret.replace(' ', ''))
                    code = totp.now()
                    print(f"--> Generating 2FA code: {code}")
                    
                    code_input = page.locator('input[name="verificationCode"], input[aria-label*="Security Code"], input[aria-label*="code"], input[type="tel"]')
                    if await code_input.count() > 0:
                        await code_input.nth(0).fill(code)
                        await asyncio.sleep(1)
                        print("Clicking Confirm/Continue for 2FA...")
                        confirm_btn = page.locator('button:has-text("Confirm"), button:has-text("Continue")')
                        if await confirm_btn.count() > 0:
                            await confirm_btn.nth(0).click()
                        await asyncio.sleep(10)
                except Exception as e:
                    print(f"Failed to bypass 2FA: {e}")
            else:
                print("❌ 2FA required but no secret key found in DB!")
                return False, "2FA Required"

        await handle_popups(page)
        
        if '/accounts/login/' not in page.url and 'Log in' not in await page.title():
            print("✅ Login Successful!")
            return True, "Success"
            
        print("⚠️ Login Failed: Unknown reason or stuck on login page.")
        return False, "Unknown Error"
        
    except Exception as e:
        print(f"Error during login attempt: {e}")
        return False, str(e)

async def check_accounts():
    print("Fetching ALL Deactivated/Unchecked accounts (Status 0, 3, 4) from the server...")
    
    cmd = [
        "sshpass", "-p", "YOUR_DB_PASSWORD", "ssh", "-o", "StrictHostKeyChecking=no", 
        "root@YOUR_SERVER_IP", 
        "python3 -c \"import mysql.connector, json; db=mysql.connector.connect(host='localhost',user='root',password=os.getenv('DB_PASS', 'secret'),database='smm_db'); cursor=db.cursor(dictionary=True); cursor.execute('SELECT username, password, cookies, user_agent, two_factor_secret FROM ig_accounts WHERE status IN (0, 3, 4)'); print(json.dumps(cursor.fetchall()))\""
    ]
    
    result = subprocess.run(cmd, capture_output=True, text=True)
    try:
        output = result.stdout
        json_start = output.find('[')
        json_end = output.rfind(']') + 1
        accounts = json.loads(output[json_start:json_end])
    except Exception as e:
        print("Failed to fetch accounts from database.")
        return

    print(f"Successfully loaded {len(accounts)} accounts to test. Starting Visual Checker...")
    
    async with async_playwright() as p:
        for acc in accounts:
            attempt = 0
            while True:
                print(f"\n==========================================")
                print(f"Checking Account: {acc['username']} (Attempt {attempt+1})")
                print(f"==========================================")
                
                if attempt == 0:
                    print("Rotating AirProxy IP to keep account safe...")
                    try:
                        requests.get("https://airproxy.io/api/proxy/change_ip/?format=json&id=1834&key=YOUR_AIRPROXY_API_KEY", timeout=15)
                        time.sleep(20) 
                    except Exception as e:
                        print(f"Proxy rotation warning: {e}")
                
                profile_path = os.path.join(os.getcwd(), 'visual_profiles', acc['username'])
                
                context_args = {
                    "proxy": {
                        "server": "http://YOUR_AIRPROXY_SERVER:30909",
                        "username": "YOUR_AIRPROXY_USER",
                        "password": "YOUR_AIRPROXY_PASS_2"
                    },
                    "user_agent": acc.get('user_agent') or "Mozilla/5.0 (Linux; Android 13; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36",
                    "viewport": {"width": 390, "height": 844},
                    "is_mobile": True,
                    "has_touch": True
                }
                
                print("Launching Browser...")
                try:
                    context = await p.chromium.launch_persistent_context(
                        user_data_dir=profile_path,
                        headless=False,
                        args=[
                            '--disable-blink-features=AutomationControlled',
                            '--disable-infobars', 
                            '--restore-last-session=false', 
                            '--hide-crash-restore-bug', 
                            '--disable-session-crashed-bubble'
                        ],
                        **context_args
                    )
                    
                    if acc.get('cookies'):
                        try:
                            await context.add_cookies(json.loads(acc['cookies']))
                            print("Injected saved session cookies.")
                        except:
                            pass
                    
                    page = context.pages[0] if len(context.pages) > 0 else await context.new_page()
                    await context.add_init_script(ADVANCED_STEALTH_JS)
                    
                    print("Navigating to Instagram...")
                    needs_login = False
                    
                    page_load_failed = False
                    try:
                        await page.goto("https://www.instagram.com/", wait_until="domcontentloaded", timeout=60000)
                        await asyncio.sleep(8) 
                    except Exception as e:
                        err_msg = str(e)
                        if "ERR_TOO_MANY_REDIRECTS" in err_msg:
                            print(f"Detected Corrupt Cookies or Redirect Loop!")
                            needs_login = True
                        else:
                            print(f"Page Load Error: {e}")
                            page_load_failed = True
                            err_str_for_retry = err_msg
                    
                    if page_load_failed:
                        await context.close()
                        if "ERR_" in err_str_for_retry or "Timeout" in err_str_for_retry:
                            print("🔄 Proxy not ready yet! Waiting 10 seconds and retrying...")
                            time.sleep(10)
                            attempt += 1
                            continue
                        else:
                            break
                    
                    if not needs_login:
                        title_lower = (await page.title()).lower()
                        content_lower = (await page.content()).lower()
                        if 'about:blank' in page.url or '/accounts/login/' in page.url or 'log in' in title_lower:
                            print("Account is logged out (URL/Title matched).")
                            needs_login = True
                        elif await page.locator('input[name="password"]').count() > 0 or "log into instagram" in content_lower:
                            print("Account is logged out (Login form detected on page).")
                            needs_login = True
                    
                    if needs_login:
                        success, reason = await try_login(page, acc['username'], acc['password'], acc.get('two_factor_secret'))
                        if success:
                            print("✅ RESULT: Login Successful! Account is now ACTIVE.")
                            new_cookies = await context.cookies()
                            update_db(acc['username'], 1, new_cookies)
                            await context.close()
                            break
                        else:
                            if reason == "Suspended":
                                print("❌ RESULT: Account is SUSPENDED!")
                                update_db(acc['username'], 4)
                                await context.close()
                                break
                            else:
                                print(f"⚠️ RESULT: Could not login ({reason})")
                                await context.close()
                                if "ERR_HTTP" in reason or "Timeout" in reason or "net::ERR_" in reason:
                                    print("🔄 Network error! Retrying the SAME account now...")
                                    continue
                                else:
                                    break
                    
                    else:
                        await handle_popups(page)
                        await asyncio.sleep(3)
                        await handle_popups(page)
                        
                        print("Analyzing page to check if account is active or suspended...")
                        content = await page.content()
                        
                        is_suspended = False
                        is_logged_out = False
                        
                        block_keywords = ["Account suspended", "Action Blocked", "Your account has been disabled", "Help us confirm you own this account", "Suspended"]
                        for kw in block_keywords:
                            if kw in content:
                                is_suspended = True
                                break
                                
                        if '/suspended/' in page.url or '/challenge/' in page.url:
                            is_suspended = True
                            
                        title_lower2 = (await page.title()).lower()
                        if '/accounts/login/' in page.url or 'log in' in title_lower2 or await page.locator('input[name="password"]').count() > 0:
                            is_logged_out = True
                        
                        print("\n" + "*"*50)
                        if is_suspended:
                            print(f"❌ RESULT: Account {acc['username']} is SUSPENDED or BLOCKED!")
                            update_db(acc['username'], 4)
                            print("*"*50 + "\n")
                            await context.close()
                            break
                        elif is_logged_out:
                            print(f"⚠️ RESULT: Account {acc['username']} is LOGGED OUT (Cookies Expired)!")
                            success, reason = await try_login(page, acc['username'], acc['password'], acc.get('two_factor_secret'))
                            if success:
                                print("✅ RESULT: Auto-Login Successful! Account is now ACTIVE.")
                                new_cookies = await context.cookies()
                                update_db(acc['username'], 1, new_cookies)
                                print("*"*50 + "\n")
                                await context.close()
                                break
                            elif reason == "Suspended":
                                update_db(acc['username'], 4)
                                print("*"*50 + "\n")
                                await context.close()
                                break
                            else:
                                print(f"⚠️ RESULT: Could not auto-login ({reason})")
                                print("*"*50 + "\n")
                                await context.close()
                                if "ERR_HTTP" in reason or "Timeout" in reason or "net::ERR_" in reason:
                                    print("🔄 Network error! Retrying the SAME account now...")
                                    continue
                                else:
                                    break
                        else:
                            print(f"✅ RESULT: Account {acc['username']} is ACTIVE AND WORKING PERFECTLY!")
                            update_db(acc['username'], 1)
                            print("*"*50 + "\n")
                            await context.close()
                            break
                    
                except Exception as e:
                    print(f"Error checking {acc['username']}: {e}")
                    try:
                        await context.close()
                    except:
                        pass
                    
                    err_str = str(e)
                    if "ERR_HTTP" in err_str or "Timeout" in err_str or "net::ERR_" in err_str:
                        print("🔄 Network/Proxy error detected! Waiting 10 seconds and retrying...")
                        time.sleep(10)
                        attempt += 1
                        continue
                    else:
                        break

if __name__ == "__main__":
    asyncio.run(check_accounts())
