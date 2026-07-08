import mysql.connector
import json
import asyncio
import logging
import re
import random
import sys
import argparse
import pytz
import datetime
import threading
import os

LAST_ACTIVITY = datetime.datetime.now()

def clean_zombie_processes():
    try:
        # Kill any chrome processes that have been running for more than 15 minutes (900 seconds)
        os.system("ps -eo etimes,pid,comm | awk '$1 > 900 && $3 == \"chrome\" {print $2}' | xargs -r kill -9")
    except:
        pass

def watchdog_timer():
    global LAST_ACTIVITY
    while True:
        global LAST_ACTIVITY
        LAST_ACTIVITY = datetime.datetime.now()
        time.sleep(60)
        clean_zombie_processes()
        diff = (datetime.datetime.now() - LAST_ACTIVITY).total_seconds()
        if diff > 600:  # 10 minutes
            logging.error(f"WATCHDOG TRIGGERED! Script frozen for {diff} seconds. Force restarting...")
            os._exit(1)

def check_current_ip():
    try:
        proxy_url = f"http://{PROXY_SETTINGS['username']}:{PROXY_SETTINGS['password']}@YOUR_AIRPROXY_SERVER:11001"
        resp = requests.get('https://api.ipify.org', proxies={"http": proxy_url, "https": proxy_url}, timeout=10)
        return resp.text.strip()
    except Exception as e:
        logging.error(f"IP check error: {e}")
        return None

def rotate_proxy_ip_with_verify():
    old_ip = check_current_ip()
    logging.info(f"[ANTI-BAN] Current IP before rotation: {old_ip}. Triggering rotation...")
    for attempt in range(3):
        try:
            requests.get("https://airproxy.io/api/proxy/change_ip/?format=json&id=3301&key=YOUR_AIRPROXY_API_KEY", timeout=15)
        except Exception as e:
            logging.error(f"AirProxy API error: {e}")
            
        for _ in range(8):  # Check every 5s for 40s
            time.sleep(5)
            new_ip = check_current_ip()
            if new_ip and new_ip != old_ip:
                logging.info(f"[ANTI-BAN] SUCCESS: IP successfully rotated to {new_ip}!")
                return True
        logging.warning("[ANTI-BAN] IP did not change. Retrying rotation...")
        
    logging.error("[CRITICAL] Failed to rotate IP after 3 attempts! Entering Safe Sleep for 5 minutes...")
    time.sleep(300)
    return False


def acquire_proxy_lock(db, script_name):
    cursor = db.cursor(dictionary=True)
    while True:
        global LAST_ACTIVITY
        LAST_ACTIVITY = datetime.datetime.now()
        cursor.execute("SELECT * FROM proxy_locks WHERE id = 1")
        row = cursor.fetchone()
        if not row:
            cursor.execute("INSERT INTO proxy_locks (id, script_name, locked_at) VALUES (1, %s, NOW())", (script_name,))
            db.commit()
            cursor.close()
            return
        
        if (datetime.datetime.now() - row['locked_at']).total_seconds() < 30:
            time.sleep(2)
        else:
            cursor.execute("UPDATE proxy_locks SET script_name = %s, locked_at = NOW() WHERE id = 1", (script_name,))
            db.commit()
            cursor.close()
            return

DRY_RUN = False

def parse_spintax(text):
    import re, random
    pattern = re.compile(r'{([^{}]*)}')
    while True:
        global LAST_ACTIVITY
        LAST_ACTIVITY = datetime.datetime.now()
        match = pattern.search(text)
        if not match:
            break
        choices = match.group(1).split('|')
        text = text[:match.start()] + random.choice(choices) + text[match.end():]
    return text

from playwright.async_api import async_playwright
import datetime
from playwright_stealth import Stealth

# Logging setup
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s', stream=sys.stdout)

import os
APP_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../../'))
LOG_FILE = os.path.join(APP_DIR, 'logs', 'order_debug.log')

def debug_log(msg):
    try:
        with open(LOG_FILE, 'a') as f:
            time_str = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            f.write(f"\033[31m[{time_str}] PYTHON WORKER: {msg}\033[0m\n")
    except:
        pass

# Color Codes for Anti-Ban Features
COLORS = {
    'cyan': '\033[36m',
    'green': '\033[32m',
    'yellow': '\033[33m',
    'magenta': '\033[35m',
    'blue': '\033[34m',
    'light_green': '\033[92m',
    'light_cyan': '\033[96m',
    'red': '\033[31m',
    'yellow_bold': '\033[1;33m',
    'blue_bold': '\033[1;34m',
    'magenta_bold': '\033[1;35m',
    'cyan_bold': '\033[1;36m',
    'white_bold': '\033[1;37m',
    'reset': '\033[0m'
}

def log_feature(feature_name, msg, color='cyan'):
    c = COLORS.get(color, COLORS['cyan'])
    r = COLORS['reset']
    formatted_msg = f"{c}[ANTI-BAN: {feature_name}] {msg}{r}"
    logging.info(formatted_msg)
    try:
        with open(LOG_FILE, 'a') as f:
            time_str = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            f.write(f"[{time_str}] {formatted_msg}\n")
    except:
        pass

db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'Root@123',
    'database': 'smm_db'
}

import requests
import time
PROXY_SETTINGS = {'server': 'http://YOUR_AIRPROXY_SERVER:11001', 'username': 'YOUR_AIRPROXY_USER', 'password': 'YOUR_AIRPROXY_PASS_1'}

USER_AGENTS = [
    "Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 13; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 14; Pixel 7 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Mobile Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36"
]

ADVANCED_STEALTH_JS = """
    Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
    window.navigator.chrome = { runtime: {} };
    Object.defineProperty(navigator, 'hardwareConcurrency', { get: () => Math.random() > 0.5 ? 8 : 4 });
    Object.defineProperty(navigator, 'deviceMemory', { get: () => Math.random() > 0.5 ? 8 : 4 });
    
    // WebGL Spoofing to hide Headless/Server environment
    const getParameterProxyHandler = {
        apply: function(target, thisArg, argumentsList) {
            const param = argumentsList[0];
            // UNMASKED_VENDOR_WEBGL
            if (param === 37445) return 'Google Inc. (NVIDIA)';
            // UNMASKED_RENDERER_WEBGL
            if (param === 37446) return 'ANGLE (NVIDIA, NVIDIA GeForce RTX 3060 Direct3D11 vs_5_0 ps_5_0, D3D11)';
            return Reflect.apply(target, thisArg, argumentsList);
        }
    };
    if (window.WebGLRenderingContext) {
        window.WebGLRenderingContext.prototype.getParameter = new Proxy(window.WebGLRenderingContext.prototype.getParameter, getParameterProxyHandler);
    }
    if (window.WebGL2RenderingContext) {
        window.WebGL2RenderingContext.prototype.getParameter = new Proxy(window.WebGL2RenderingContext.prototype.getParameter, getParameterProxyHandler);
    }

    // Canvas Spoofing
    const originalToDataURL = HTMLCanvasElement.prototype.toDataURL;
    HTMLCanvasElement.prototype.toDataURL = function() {
        const ctx = this.getContext('2d');
        if (ctx) {
            const imageData = ctx.getImageData(0, 0, this.width, this.height);
            for (let i = 0; i < imageData.data.length; i += 4) {
                imageData.data[i] = imageData.data[i] ^ 1; // Inject tiny noise
            }
            ctx.putImageData(imageData, 0, 0);
        }
        return originalToDataURL.apply(this, arguments);
    };

    // AudioContext Spoofing
    const originalCreateOscillator = window.AudioContext.prototype.createOscillator;
    if (originalCreateOscillator) {
        window.AudioContext.prototype.createOscillator = function() {
            const osc = originalCreateOscillator.apply(this, arguments);
            const originalStart = osc.start;
            osc.start = function() {
                osc.frequency.value += Math.random() * 0.1; // Add tiny noise to freq
                return originalStart.apply(this, arguments);
            };
            return osc;
        };
    }
"""

def get_user_agent(acc, cursor):
    if not acc.get('user_agent'):
        ua = random.choice(USER_AGENTS)
        cursor.execute("UPDATE ig_accounts SET user_agent = %s WHERE id = %s", (ua, acc['id']))
        acc['user_agent'] = ua
    return acc['user_agent']


async def human_type(locator, text):
    for char in text:
        # 5% chance of typo
        if random.random() < 0.05 and char.isalpha():
            wrong_char = chr(ord(char) + random.choice([-1, 1]))
            if wrong_char.isalpha():
                await locator.press(wrong_char)
                await asyncio.sleep(random.uniform(0.1, 0.3))
                await locator.press('Backspace')
                await asyncio.sleep(random.uniform(0.1, 0.2))
        await locator.type(char, delay=random.uniform(50, 150))
        if char == ' ':
            await asyncio.sleep(random.uniform(0.1, 0.4))
async def human_scroll(page, distance):
    steps = random.randint(5, 12)
    step_distance = distance / steps
    
    # Simulate touch drag instead of wheel if possible, or bezier wheel
    for _ in range(steps):
        # Adding slight horizontal variation
        await page.mouse.wheel(random.randint(-10, 10), step_distance + random.randint(-15, 30))
        await asyncio.sleep(random.uniform(0.05, 0.2))
    await asyncio.sleep(random.uniform(1.0, 2.5))

async def simulate_reading_caption(page):
    try:
        # Read the first large block of text which is usually the caption
        caption_el = page.locator('h1[dir="auto"], span[dir="auto"]').first
        if await caption_el.is_visible(timeout=3000):
            text = await caption_el.text_content()
            if text:
                word_count = len(text.split())
                # Average reading speed ~4 words per second
                delay = min(word_count * 0.25, 20.0) 
                if delay > 3.0:
                    log_feature("Context-Aware Reading", f"Pausing for {delay:.1f}s to read a {word_count}-word caption.", "blue_bold")
                    await asyncio.sleep(delay)
    except:
        pass

async def handle_challenge(page, acc, cursor, db):
    current_url = page.url
    if "challenge" in current_url or "checkpoint" in current_url:
        log_feature("IMAP Auto-Resolve", f"ACCOUNT {acc['username']} HIT CHECKPOINT/CHALLENGE! Attempting Auto-Resolve...", "red")
        try:
            # Click "Send Security Code"
            send_btn = page.locator('button:has-text("Send security code"), button:has-text("Send Security Code")').first
            if await send_btn.is_visible(timeout=5000):
                await send_btn.click(timeout=5000)
                debug_log(f"Clicked Send Security Code for {acc['username']}")
                await asyncio.sleep(15) # Wait for email to arrive
                
                email_addr = acc.get('email')
                email_pass = acc.get('email_password')
                if email_addr and email_pass:
                    import imaplib, email, re
                    # Automatically determine IMAP host if not explicitly given
                    domain = email_addr.split('@')[-1]
                    # We default to mail.domain or imap.firstmail.ltd for common bot emails
                    imap_host = "imap.firstmail.ltd" if "firstmail" in domain else f"mail.{domain}"
                    
                    try:
                        mail = imaplib.IMAP4_SSL(imap_host, 993)
                        mail.login(email_addr, email_pass)
                        mail.select('inbox')
                        status, messages = mail.search(None, 'ALL')
                        mail_ids = messages[0].split()
                        
                        if mail_ids:
                            latest_email_id = mail_ids[-1]
                            status, msg_data = mail.fetch(latest_email_id, '(RFC822)')
                            for response_part in msg_data:
                                if isinstance(response_part, tuple):
                                    msg = email.message_from_bytes(response_part[1])
                                    subject = email.header.decode_header(msg['Subject'])[0][0]
                                    if isinstance(subject, bytes): subject = subject.decode()
                                    
                                    # Instagram code subjects usually contain the code
                                    match = re.search(r'\b(\d{6})\b', subject)
                                    if match:
                                        code = match.group(1)
                                        debug_log(f"Extracted IG Security Code: {code}")
                                        
                                        code_input = page.locator('input[name="security_code"]').first
                                        if await code_input.is_visible(timeout=5000):
                                            await human_type(code_input, code)
                                            await asyncio.sleep(1)
                                            submit_btn = page.locator('button:has-text("Submit")').first
                                            await submit_btn.click(timeout=5000)
                                            await asyncio.sleep(10)
                                            debug_log(f"Successfully resolved checkpoint for {acc['username']}!")
                                            return True
                    except Exception as imap_err:
                        debug_log(f"IMAP Error during challenge resolve: {imap_err}")
        except Exception as e:
            debug_log(f"Error handling challenge: {e}")
            
    return False


async def check_subscription(p, username):
    db = mysql.connector.connect(**db_config, autocommit=True)
    cursor = db.cursor(dictionary=True)
    cursor.execute("SELECT * FROM ig_accounts WHERE status = 1 AND cookies IS NOT NULL ORDER BY RAND() LIMIT 5")
    accounts = cursor.fetchall()
    cursor.close()
    db.close()
    
    if not accounts:
        logging.error("No authenticated accounts found for detection.")
        return False

    for acc in accounts:
        logging.info(f"Attempting detection with account: {acc['username']}")
        profile_dir = os.path.join(APP_DIR, 'modules', 'cron', 'controllers', 'python_worker', 'profiles', acc['username'])
        if not os.path.exists(profile_dir):
            os.makedirs(profile_dir)
        context = await p.chromium.launch_persistent_context(
            user_data_dir=profile_dir,
            headless=True,
            proxy=PROXY_SETTINGS,
            user_agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"
        )
        pages = context.pages
        page = pages[0] if len(pages) > 0 else await context.new_page()
        # Block images, media, and fonts to speed up and reduce bandwidth
        await page.route("**/*", lambda route: route.abort() if route.request.resource_type in ["image", "media", "font"] else route.continue_())
        await Stealth().apply_stealth_async(page)
        await context.add_init_script(ADVANCED_STEALTH_JS)
        
        try:
            if acc.get('cookies'):
                await context.add_cookies(json.loads(acc['cookies']))
            
            await page.goto(f"https://www.instagram.com/{username}/", wait_until="domcontentloaded", timeout=60000)
            await asyncio.sleep(10)
            
            if "Sorry, this page isn't available" in await page.content() or "Page Not Found" in await page.content():
                logging.warning(f"Account {acc['username']} hit Page Not Available. Rotating...")
                await context.close()
                continue
                
            latest_post = page.locator('article a[href*="/p/"]').first
            if await latest_post.is_visible(timeout=15000):
                href = await latest_post.get_attribute('href')
                shortcode = href.split('/')[-2]
                result = {
                    "shortcode": shortcode,
                    "media_id": shortcode,
                    "url": f"https://www.instagram.com{href}"
                }
                print(json.dumps(result))
                await context.close()
                return True
            
            logging.warning(f"No post found with {acc['username']}.")
            await context.close()
        except Exception as e:
            logging.error(f"Detection error with {acc['username']}: {e}")
            await context.close()
            
    print(json.dumps({"error": "Failed to detect post with all rotated accounts"}))
    return False

async def human_type(locator, text):
    for char in text:
        await locator.type(char, delay=random.uniform(50, 150))
        if char == ' ':
            await asyncio.sleep(random.uniform(0.1, 0.4))

async def login_to_instagram(page, context, acc, cursor, db):
    debug_log(f"Starting auto-login sequence for {acc['username']}...")
    try:
        # Navigate directly to login to avoid facebook sync traps
        await page.goto("https://www.instagram.com/accounts/login/", wait_until="domcontentloaded", timeout=60000)
        await asyncio.sleep(random.uniform(4, 7))
        
        username_input = page.locator('input[name="username"]').first
        password_input = page.locator('input[name="password"]').first
        
        if await username_input.is_visible(timeout=5000):
            debug_log("Entering credentials safely...")
            await human_type(username_input, acc['username'])
            await asyncio.sleep(random.uniform(1.0, 2.5))
            await human_type(password_input, acc['password'])
            await asyncio.sleep(random.uniform(1.0, 2.0))
            
            login_btn = page.locator('button[type="submit"]').first
            if await login_btn.is_visible():
                await login_btn.click(delay=random.uniform(100, 300))
                debug_log("Clicked Login. Waiting for response...")
                await asyncio.sleep(15)
                
                # Check if challenge is present
                await handle_challenge(page, acc, cursor, db)
                
                if '/accounts/login/' not in page.url and 'challenge' not in page.url and 'checkpoint' not in page.url:
                    debug_log(f"SUCCESS! {acc['username']} logged in successfully!")
                    new_cookies = await context.cookies()
                    cookies_json = json.dumps(new_cookies)
                    cursor.execute("UPDATE ig_accounts SET cookies = %s, status = 1 WHERE id = %s", (cookies_json, acc['id']))
                    db.commit()
                    return True
                elif 'suspicious' in await page.content():
                    debug_log(f"FAILED: {acc['username']} flagged for Suspicious Activity.")
                    cursor.execute("UPDATE ig_accounts SET status = 2 WHERE id = %s", (acc['id'],))
                    db.commit()
                    return False
                else:
                    debug_log(f"FAILED: {acc['username']} could not log in. URL: {page.url}")
                    cursor.execute("UPDATE ig_accounts SET status = 3 WHERE id = %s", (acc['id'],))
                    db.commit()
                    return False
        else:
            debug_log("Could not find login fields.")
            return False
            
    except Exception as e:
        debug_log(f"Error during auto-login for {acc['username']}: {e}")
        return False



async def perform_ghost_activity(p, acc, cursor, db):
    global DRY_RUN
    if DRY_RUN:
        debug_log(f"[DRY RUN] Simulating ghost activity (warmup) for {acc['username']}")
        logging.info(f"[DRY RUN] Simulating ghost activity (warmup) for {acc['username']}")
        await asyncio.sleep(random.uniform(10, 20))
        return
        
    log_feature("Ghost Activity", f"Starting Ghost Activity (Warm-up) for {acc['username']}", "white_bold")
    ua = get_user_agent(acc, cursor)
    db.commit()
    is_mobile = "Mobile" in ua or "iPhone" in ua or "Android" in ua
    context_args = {"proxy": PROXY_SETTINGS, "user_agent": ua, "locale": "en-US"}
    if is_mobile:
        context_args["viewport"] = {"width": 390, "height": 844}
        context_args["is_mobile"] = True
        context_args["has_touch"] = True

    profile_dir = os.path.join(APP_DIR, 'modules', 'cron', 'controllers', 'python_worker', 'profiles', acc['username'])
    if not os.path.exists(profile_dir):
        os.makedirs(profile_dir)
        
    fingerprint = acc.get('device_fingerprint')
    if fingerprint:
        if isinstance(fingerprint, str): fingerprint = json.loads(fingerprint)
        context_args["timezone_id"] = fingerprint.get("timezone", "America/New_York")
        context_args["viewport"] = fingerprint.get("viewport", {"width": 390, "height": 844})
        
    try:
        context = await p.chromium.launch_persistent_context(
            user_data_dir=profile_dir,
            headless=True,
            args=['--no-sandbox', '--disable-dev-shm-usage', '--disable-blink-features=AutomationControlled', '--enforce-webrtc-ip-permission-check', '--force-webrtc-ip-handling-policy=disable_non_proxied_udp'],
            **context_args
        )
        if acc.get('cookies'):
            try: await context.add_cookies(json.loads(acc['cookies']))
            except: pass
        pages = context.pages
        page = pages[0] if len(pages) > 0 else await context.new_page()
        # Allow images during warmup so the bot looks more human
        # await page.route("**/*", lambda route: route.abort() if route.request.resource_type in ["media", "font"] else route.continue_())
        await Stealth().apply_stealth_async(page)
        await context.add_init_script(ADVANCED_STEALTH_JS)
        log_feature("JS/Hardware Spoofing", "Injected Canvas, WebGL & AudioContext spoofing scripts", "magenta_bold")
        
        try:
            await page.goto("https://www.instagram.com/", wait_until="domcontentloaded", timeout=40000)
        except Exception:
            debug_log(f"Timeout during initial goto for {acc['username']}, checking URL anyway...")
            pass
            
        await asyncio.sleep(random.uniform(3, 7))
        
        is_logged_out = False
        login_selectors = [
            'a[href*="/accounts/login/"]',
            'button:has-text("Log in")',
            'button:has-text("Log In")',
            'a:has-text("Log in")',
            'a:has-text("Log In")',
            'span:has-text("Log in")',
            'span:has-text("Log In")',
            'input[name="username"]'
        ]
        for selector in login_selectors:
            try:
                if await page.locator(selector).first.is_visible(timeout=2000):
                    is_logged_out = True
                    break
            except: pass
            
        if 'login' in page.url.lower() or 'login_sync' in page.url.lower() or 'Login' in await page.title():
            is_logged_out = True

        if is_logged_out:
            debug_log(f"Account {acc['username']} is logged out. Triggering Auto-Login...")
            success = await login_to_instagram(page, context, acc, cursor, db)
            if not success:
                debug_log(f"Auto-login failed for {acc['username']}.")
                await context.close()
                return
                
        await handle_challenge(page, acc, cursor, db)
        await handle_popups(page)
        
        # Randomly View Stories
        if random.random() > 0.4:
            story_btn = page.locator('button:has(img[alt*="story"]), div[role="button"]:has(img[alt*="story"])').first
            if await story_btn.is_visible(timeout=3000):
                await story_btn.click(timeout=5000, force=True)
                await asyncio.sleep(random.uniform(6, 12))
                close_btn = page.locator('svg[aria-label="Close"]').first
                if await close_btn.is_visible(timeout=3000):
                    await close_btn.click(timeout=5000, force=True)
                await asyncio.sleep(random.uniform(2, 4))
        
        is_deep_session = random.random() < 0.30
        scroll_count = random.randint(35, 50) if is_deep_session else random.randint(15, 30)
        if is_deep_session:
            debug_log(f"[ANTI-BAN] Deep Session activated for {acc['username']}! Extending dwell time.")
            
        # Scroll and random interactions
        for _ in range(scroll_count):
            await human_scroll(page, random.randint(300, 800))
            
            # 1. Dwell Time (Simulate reading caption)
            if random.random() < 0.4:
                dwell_time = random.uniform(25, 60)
                debug_log(f"[ANTI-BAN] Dwelling (reading) for {dwell_time:.1f}s...")
                await asyncio.sleep(dwell_time)
                
            # 2. Carousel Swiping
            if random.random() < 0.3:
                next_btn = page.locator('button[aria-label="Next"]').first
                if await next_btn.is_visible(timeout=2000):
                    debug_log("[ANTI-BAN] Swiping carousel image...")
                    await next_btn.click(timeout=3000, force=True)
                    await asyncio.sleep(random.uniform(2, 5))
                    
            # 3. Post Saving (Bookmarking)
            if random.random() < 0.15:
                save_btn = page.locator('svg[aria-label="Save"]').first
                if await save_btn.is_visible(timeout=2000):
                    await save_btn.click(timeout=3000, force=True)
                    debug_log("[ANTI-BAN] Saved (Bookmarked) a post.")
                    await asyncio.sleep(random.uniform(2, 4))
                    
            # 4. Incomplete Typing (Comment Abort)
            if random.random() < 0.10:
                comment_btn = page.locator('svg[aria-label="Comment"]').first
                if await comment_btn.is_visible(timeout=2000):
                    try:
                        await comment_btn.click(timeout=3000, force=True)
                        await asyncio.sleep(random.uniform(1, 3))
                        textarea = page.locator('textarea[placeholder*="Add a comment"]').first
                        if await textarea.is_visible(timeout=3000):
                            gibberish = "".join(random.choices("abcdefghijklmnopqrstuvwxyz ", k=random.randint(3, 7)))
                            await human_type(textarea, gibberish)
                            await asyncio.sleep(random.uniform(2, 4))
                            for _ in range(len(gibberish)):
                                await textarea.press("Backspace")
                                await asyncio.sleep(random.uniform(0.05, 0.15))
                            debug_log("[ANTI-BAN] Aborted comment (Incomplete Typing simulation).")
                            await page.go_back()
                    except: pass
                    
            # 5. Random Like
            if random.random() > 0.6:
                like_btn = page.locator('svg[aria-label="Like"]').first
                if await like_btn.is_visible(timeout=2000):
                    await like_btn.click(timeout=3000, force=True)
                    debug_log(f"Warmed up: Liked a random post for {acc['username']}")
                    await asyncio.sleep(random.uniform(3, 6))
                    
            # 6. Organic Profile Visit
            if random.random() < 0.2:
                try:
                    profile_link = page.locator('a[role="link"][tabindex="0"] > span').first
                    if await profile_link.is_visible(timeout=2000):
                        await profile_link.click(timeout=3000, force=True)
                        debug_log("[ANTI-BAN] Visiting profile organically...")
                        await asyncio.sleep(random.uniform(3, 6))
                        await human_scroll(page, random.randint(300, 800))
                        await asyncio.sleep(random.uniform(2, 4))
                        await page.go_back(wait_until="domcontentloaded")
                        debug_log("[ANTI-BAN] Returned to feed.")
                        await asyncio.sleep(random.uniform(2, 4))
                except: pass
        
        if random.random() > 0.5 or is_deep_session:
            target_page = "/reels/" if is_deep_session else "/explore/"
            explore = page.locator(f'a[href="{target_page}"]').first
            if await explore.is_visible(timeout=3000):
                await explore.click(timeout=5000, force=True)
                debug_log(f"[ANTI-BAN] Navigating to {target_page}...")
                await asyncio.sleep(random.uniform(3, 7))
                if is_deep_session:
                    try:
                        debug_log("[ANTI-BAN] Navigating to /reels/...")
                        await page.goto("https://www.instagram.com/reels/", wait_until="domcontentloaded", timeout=40000)
                        await asyncio.sleep(5)
                        reels_watch_time = random.uniform(60, 180)
                        debug_log(f"[ANTI-BAN] Watching Reels for {reels_watch_time:.1f}s...")
                        await asyncio.sleep(reels_watch_time)
                        # Random scroll in reels
                        for _ in range(random.randint(3, 8)):
                            await human_scroll(page, random.randint(500, 900))
                            await asyncio.sleep(random.uniform(10, 30))
                    except Exception as e:
                        debug_log(f"Deep Session Reels error: {e}")        
        await context.close()
        debug_log(f"Ghost Activity completed for {acc['username']}")
    except Exception as e:
        debug_log(f"Ghost Activity error for {acc['username']}: {e}")
        try: await context.close()
        except: pass

async def handle_popups(page):
    try:
        popup_terms = [
            "Not Now", "Not now", "Cancel", "Close", "Dismiss",
            "Ahora no", "Cancelar", "Cerrar",
            "Später", "Abbrechen", "Schließen",
            "Plus tard", "Annuler", "Fermer",
            "Не сейчас", "Отмена", "Закрыть",
            "Lain Kali", "Batal", "Tutup",
            "Accept", "Accept All", "Aceptar", "Alle akzeptieren",
            "Allow essential and optional cookies", "Allow all cookies",
            "Decline optional cookies", "Aceptar cookies", "Cookies", "Decline",
            "Allow essential"
        ]
        
        for term in popup_terms:
            buttons = page.locator(f'button:has-text("{term}"), div[role="button"]:has-text("{term}")')
            count = await buttons.count()
            for i in range(count):
                btn = buttons.nth(i)
                try:
                    if await btn.is_visible():
                        debug_log(f"Dismissing popup: Clicked '{term}' button.")
                        await btn.click(timeout=3000, force=True)
                        await asyncio.sleep(1)
                except:
                    pass
                    
        close_svgs = page.locator('svg[aria-label="Close"], svg[aria-label="Dismiss"], svg[aria-label="Cerrar"], svg[aria-label="Schließen"]')
        count = await close_svgs.count()
        for i in range(count):
            btn = close_svgs.nth(i)
            try:
                if await btn.is_visible():
                    debug_log("Dismissing popup: Clicked SVG Close button parent.")
                    parent = btn.locator('xpath=..')
                    if await parent.is_visible():
                        await parent.click(timeout=3000, force=True)
                    else:
                        await btn.click(timeout=3000, force=True)
                    await asyncio.sleep(1)
            except:
                pass
    except Exception as e:
        debug_log(f"Error while dismissing popups: {e}")

async def save_error_screenshot(page, acc, action, task_id):
    try:
        import os
        error_filename = f"error_{acc.get('username')}_{action}_{task_id}.png"
        error_path = os.path.join(os.path.dirname(__file__), error_filename)
        await page.screenshot(path=error_path)
        debug_log(f"ERROR SCREENSHOT SAVED to: {error_path}")
    except Exception as e:
        debug_log(f"Failed to save error screenshot: {e}")

async def warmup_account(page, acc, cursor, db):
    debug_log(f"Warming up account {acc['username']}...")
    try:
        await page.goto("https://www.instagram.com/", wait_until="domcontentloaded", timeout=60000)
        await asyncio.sleep(random.uniform(5, 10))
        
        await handle_challenge(page, acc, cursor, db)
        await handle_popups(page)
        
        # Automated Profile Building removed as per user request.

        # Random scrolls
        for _ in range(random.randint(2, 4)):
            await human_scroll(page, random.randint(300, 800))
        # Sometimes click on explore
        if random.random() > 0.5:
            explore = page.locator('a[href="/explore/"]').first
            if await explore.is_visible(timeout=3000):
                try:
                    await explore.click(timeout=5000, force=True)
                    await asyncio.sleep(random.uniform(3, 7))
                    await handle_popups(page)
                    await human_scroll(page, random.randint(200, 500))
                except:
                    pass
    except Exception as e:
        debug_log(f"Warmup error for {acc['username']}: {e}")

async def perform_action(p, acc, task, cursor, db):
    target = task.get('target', '')
    action = task.get('action', '')
    
    global DRY_RUN
    if DRY_RUN:
        debug_log(f"[DRY RUN] Simulating {action} on {target} for account {acc['username']}")
        logging.info(f"[DRY RUN] Simulating {action} on {target} for account {acc['username']}")
        delay = random.uniform(5, 15)
        await asyncio.sleep(delay)
        return True, "Dry run successful"
        
    if action == 'follow' and not target.startswith('http'):
        target = f"https://www.instagram.com/{target.strip('/')}/"
        task['target'] = target
        
    if target.endswith('instagram.com//') or len(target.split('/')) <= 3:
        return False, "INVALID_URL"

    # Daily limits check
    today = datetime.date.today()
    last_reset = acc.get('last_daily_reset')
    daily_actions = acc.get('daily_actions', 0)
    daily_limit = acc.get('daily_limit')
    
    # Progressive Warm-up Limits
    days_alive = 30 # default
    if acc.get('created'):
        days_alive = (today - acc['created'].date()).days
    
    # Base limit: Max 40, Min 5, increases by 5 per day
    base_limit = min(40, max(5, days_alive * 5))
    
    if last_reset != today or daily_limit is None:
        daily_limit = random.randint(max(5, base_limit - 5), base_limit + 5)
        log_feature("Progressive Limits", f"Set daily limit to {daily_limit} (Account Age: {days_alive} days)", "green")
        cursor.execute("UPDATE ig_accounts SET daily_actions = 0, last_daily_reset = %s, daily_limit = %s WHERE id = %s", (today, daily_limit, acc['id']))
        db.commit()
        daily_actions = 0
        
    if daily_actions >= daily_limit:
        return False, "DAILY_LIMIT_REACHED"
        
    # Chronobiology Sleep Hours (12 AM to 8 AM local time)
    fingerprint = acc.get('device_fingerprint')
    if fingerprint:
        if isinstance(fingerprint, str): fingerprint = json.loads(fingerprint)
        tz = fingerprint.get("timezone", "America/New_York")
        import pytz

        try:
            local_time = datetime.datetime.now(pytz.timezone(tz))
            if 0 <= local_time.hour < 8:
                log_feature("Chronobiology Sleep", f"Account {acc['username']} is sleeping ({local_time.hour}:{local_time.minute:02d} in {tz})", "magenta")
                return False, "SLEEP_MODE_ACTIVE"
        except: pass

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
        
    profile_dir = os.path.join(APP_DIR, 'modules', 'cron', 'controllers', 'python_worker', 'profiles', acc['username'])
    if not os.path.exists(profile_dir):
        os.makedirs(profile_dir)
        
    fingerprint = acc.get('device_fingerprint')
    if fingerprint:
        if isinstance(fingerprint, str):
            fingerprint = json.loads(fingerprint)
    else:
        fingerprint = {
            "timezone": random.choice(['America/New_York', 'America/Los_Angeles', 'Europe/London', 'Europe/Paris', 'Asia/Dubai', 'Asia/Kolkata']),
            "viewport": {"width": 390, "height": 844} if is_mobile else {"width": 1280, "height": 720},
        }
        cursor.execute("UPDATE ig_accounts SET device_fingerprint=%s WHERE id=%s", (json.dumps(fingerprint), acc['id']))
        db.commit()

    context_args["timezone_id"] = fingerprint.get("timezone", "America/New_York")
    context_args["viewport"] = fingerprint.get("viewport", {"width": 390, "height": 844})
    
    context = await p.chromium.launch_persistent_context(
        user_data_dir=profile_dir,
        headless=True,
        args=['--no-sandbox', '--disable-dev-shm-usage', '--disable-blink-features=AutomationControlled', '--enforce-webrtc-ip-permission-check', '--force-webrtc-ip-handling-policy=disable_non_proxied_udp'],
        **context_args
    )
    try:
        # For persistent contexts, cookies are usually saved, but we can sync them just in case
        if acc.get('cookies'):
            try:
                await context.add_cookies(json.loads(acc['cookies']))
            except:
                pass
        
        pages = context.pages
        page = pages[0] if len(pages) > 0 else await context.new_page()
        # Block images, media, fonts, stylesheets, and tracking scripts to save RAM/CPU
        await page.route("**/*", lambda route: route.abort() if route.request.resource_type in ["image", "media", "font", "stylesheet"] or any(x in route.request.url for x in ["analytics", "tracking", "logging", "facebook.com/tr", "pixel"]) else route.continue_())
        await Stealth().apply_stealth_async(page)
        await context.add_init_script(ADVANCED_STEALTH_JS)
        
        await page.goto("https://www.instagram.com/", wait_until="domcontentloaded", timeout=60000)
        await asyncio.sleep(random.uniform(5, 10))
            # Handle random Challenge/Checkpoint right after login/load
        await handle_challenge(page, acc, cursor, db)
            
        await handle_popups(page)
            
        # Organic pre-task warm-up and Ghost Activity Dilution
        if random.random() > 0.2:
            await warmup_account(page, acc, cursor, db)
        
        action = task['action']
        target = task['target']
        
        proxy_url = f"http://{PROXY_SETTINGS['username']}:{PROXY_SETTINGS['password']}@YOUR_SERVER_IP:11001" if "airproxy" not in PROXY_SETTINGS['server'] else f"http://{PROXY_SETTINGS['username']}:{PROXY_SETTINGS['password']}@YOUR_AIRPROXY_SERVER:11001"
        proxies = {"http": proxy_url, "https": proxy_url}
        try:
            ip_resp = requests.get('https://api.ipify.org', proxies=proxies, timeout=5)
            current_ip = ip_resp.text
        except Exception:
            current_ip = "Unknown IP"
            
        debug_log(f"Connecting via proxy (IP: {current_ip}) and navigating to {target}")
        try:
            await page.goto(target, referer="https://www.google.com/", wait_until="domcontentloaded", timeout=60000)
            await asyncio.sleep(10)
        except Exception as e:
            if "ERR_TUNNEL_CONNECTION_FAILED" in str(e) or "ERR_PROXY_CONNECTION_FAILED" in str(e):
                debug_log(f"PROXY ERROR: Connection Failed during navigation! Exception: {e}")
                await context.close()
                return False, "ERR_TUNNEL_CONNECTION_FAILED"
            raise

        debug_log(f"Successfully loaded. Landed on URL: {page.url}")
        title = await page.title()
        debug_log(f"Page Title: {title}")

        # Check if logged in by looking for login elements or text
        is_logged_out = False
        login_selectors = [
            'a[href*="/accounts/login/"]',
            'button:has-text("Log in")',
            'button:has-text("Log In")',
            'a:has-text("Log in")',
            'a:has-text("Log In")',
            'span:has-text("Log in")',
            'span:has-text("Log In")'
        ]
        for selector in login_selectors:
            try:
                if await page.locator(selector).first.is_visible(timeout=2000):
                    is_logged_out = True
                    debug_log(f"Logged out state detected via selector: {selector}")
                    break
            except:
                pass
                
        if '/accounts/login/' in page.url or 'Login' in title or 'Log in' in title:
            is_logged_out = True
            
        if is_logged_out:
            debug_log(f"COOKIES EXPIRED for Account {acc['username']}! Marking as Needs Relogin.")
            await context.close()
            return False, "COOKIES_EXPIRED"
        
        if page.url == "https://www.instagram.com/" or page.url == "https://www.instagram.com":
            debug_log("Redirected to Instagram home. Retrying goto...")
            logging.info("Redirected to home. Retrying goto...")
            try:
                await page.goto(target, referer="https://www.google.com/", wait_until="domcontentloaded", timeout=60000)
                await asyncio.sleep(10)
            except Exception as e:
                if "ERR_TUNNEL_CONNECTION_FAILED" in str(e) or "ERR_PROXY_CONNECTION_FAILED" in str(e):
                    debug_log(f"PROXY ERROR: Connection Failed during retry! Exception: {e}")
                    await context.close()
                    return False, "ERR_TUNNEL_CONNECTION_FAILED"
                raise
            
            debug_log(f"Retry URL: {page.url}")
            title_after = await page.title()
            is_logged_out_retry = False
            for selector in login_selectors:
                try:
                    if await page.locator(selector).first.is_visible(timeout=2000):
                        is_logged_out_retry = True
                        break
                except:
                    pass
            if '/accounts/login/' in page.url or 'Login' in title_after or 'Log in' in title_after:
                is_logged_out_retry = True
                
            if is_logged_out_retry:
                debug_log(f"COOKIES EXPIRED for Account {acc['username']} on retry!")
                await context.close()
                return False, "COOKIES_EXPIRED"
        
        content = await page.content()
        block_keywords = ["Account suspended", "Action Blocked", "Your account has been disabled", "Help us confirm you own this account"]
        for kw in block_keywords:
            if kw in content:
                await context.close()
                return False, "ACCOUNT_BLOCKED"
        
        if '/suspended/' in page.url or '/challenge/' in page.url or 'update_risky_contactpoint' in page.url:
            await context.close()
            return False, "ACCOUNT_BLOCKED"

        await handle_popups(page)


        if action == 'follow':
            # ACTION RATIO: 50% chance to like 1-2 home feed posts before following
            if random.random() > 0.5:
                log_feature("Action Ratio", "Liking 1-2 home feed posts before following target.", "light_green")
                try:
                    await page.goto("https://www.instagram.com/", wait_until="domcontentloaded", timeout=60000)
                    await asyncio.sleep(random.uniform(4, 7))
                    await human_scroll(page, random.randint(300, 600))
                    like_btns = page.locator('svg[aria-label="Like"], svg[aria-label="Mi piace"], svg[aria-label="Me gusta"], svg[aria-label="Curtir"]')
                    count = await like_btns.count()
                    if count > 0:
                        for i in range(min(count, random.randint(1, 2))):
                            btn = like_btns.nth(i)
                            if await btn.is_visible():
                                await btn.click(timeout=3000, force=True)
                                await asyncio.sleep(random.uniform(2, 5))
                                await human_scroll(page, random.randint(200, 400))
                except Exception as e:
                    debug_log(f"Action Ratio Warning: Feed like failed: {e}")
                
                # Now go to target
                debug_log(f"Navigating to target {target}")
                await page.goto(target, referer="https://www.google.com/", wait_until="domcontentloaded", timeout=60000)
                await asyncio.sleep(10)

            # We forced locale="en-US", so it should always be English, but we include fallbacks just in case.
            follow_pattern = re.compile(r"^(Follow|Segui|Seguir|Ikuti|S'abonner|Folgen|Takip Et|Подписаться)", re.IGNORECASE)
            following_pattern = re.compile(r"^(Requested|Following|Richiesta|Inviata|Segui|Siguiendo|Pendiente|Mengikuti|Abonné|Abonnements|Folge|Gefolgt|Takipdesin|İstek|Вы подписаны|Запрос)", re.IGNORECASE)
            
            await handle_popups(page)
            
            # Check first if already followed before doing any organic simulation to save time/resources
            if await page.locator('button, div[role="button"], a[role="button"]').filter(has_text=following_pattern).first.is_visible(timeout=5000):
                await context.close()
                return True, "Already followed/requested"
                
            # Organic post viewing discovery
            try:
                first_post = page.locator('a[href*="/p/"], a[href*="/reel/"]').first
                if await first_post.is_visible(timeout=3000):
                    log_feature("Organic Post Viewing", "Clicking first profile post thumbnail before following.", "cyan")
                    await first_post.scroll_into_view_if_needed()
                    await asyncio.sleep(random.uniform(1.0, 2.0))
                    
                    debug_log("Organic Simulation: Clicking first profile post thumbnail.")
                    await first_post.click(timeout=5000, force=True)
                    await asyncio.sleep(random.uniform(3.0, 6.0)) # Simulate viewing the post
                    
                    # Close the post viewer modal
                    close_svg = page.locator('svg[aria-label="Close"], svg[aria-label="Cerrar"], svg[aria-label="Schließen"]').first
                    if await close_svg.is_visible(timeout=3000):
                        parent = close_svg.locator('xpath=..')
                        if await parent.is_visible():
                            await parent.click(timeout=3000, force=True)
                        else:
                            await close_svg.click(timeout=3000, force=True)
                        debug_log("Organic Simulation: Closed post viewer modal.")
                    else:
                        # Fallback: Go back in history if modal close button not found
                        await page.go_back(wait_until="domcontentloaded")
                        debug_log("Organic Simulation: Modal close not found, navigated back.")
                    await asyncio.sleep(random.uniform(1.0, 2.0))
            except Exception as sim_e:
                debug_log(f"Organic Simulation Warning: Profile post discovery skipped: {sim_e}")
                
            await page.evaluate("window.scrollTo(0, 0)")
            await asyncio.sleep(random.uniform(4, 10))
            await handle_popups(page)
            
            # Simulated Human Context-Aware Reading
            await simulate_reading_caption(page)
            
            # Locate follow button after simulation to avoid stale element reference
            follow_btn = page.locator('button, div[role="button"], a[role="button"]').filter(has_text=follow_pattern).first
            
            if not await follow_btn.is_visible(timeout=5000):
                if await page.locator('button, div[role="button"], a[role="button"]').filter(has_text=following_pattern).first.is_visible(timeout=5000):
                    await context.close()
                    return True, "Already followed/requested"
                await context.close()
                return False, "Follow button not found"
            
            # Setup API interception for follows
            api_blocked = False
            async def handle_follow_response(response):
                nonlocal api_blocked
                if "friendships/create/" in response.url and response.request.method == "POST":
                    try:
                        json_data = await response.json()
                        # If status is fail, it's a hard block
                        if json_data.get('status') == 'fail':
                            api_blocked = True
                            debug_log(f"FOLLOW API BLOCKED! Response: {json_data}")
                        # If status is ok, check friendship_status to see if it was silently dropped
                        elif json_data.get('status') == 'ok' and 'friendship_status' in json_data:
                            fs = json_data['friendship_status']
                            if not fs.get('following') and not fs.get('outgoing_request'):
                                api_blocked = True
                                debug_log(f"FOLLOW API GHOSTED! Response: {json_data}")
                    except:
                        pass
            page.on("response", handle_follow_response)
            
            await follow_btn.click(timeout=15000, force=True)
            
            # Wait 10 seconds for API response and Anti-Spam engine to settle
            await asyncio.sleep(10)
            
            if api_blocked:
                debug_log("API Interception detected Action Block for Follow!")
                await context.close()
                return False, "ACTION_BLOCKED_API"
            
            # Exact regex for Following/Requested state in multiple languages
            following_exact = re.compile(r"^(Requested|Following|Richiesta effettuata|Inviata|Segui già|Siguiendo|Pendiente|Mengikuti|Abonné\(e\)|Abonnements|Folge ich|Gefolgt|Takipdesin|İstek Gönderildi|Вы подписаны|Запрос отправлен)$", re.IGNORECASE)
            
            # Reload page to check if Instagram ghosted the follow
            await page.reload(wait_until="domcontentloaded", timeout=30000)
            await asyncio.sleep(5)
            
            # We strictly use get_by_role with EXACT match to avoid matching the "Following" count link
            if await page.get_by_role("button", name=following_exact, exact=True).first.is_visible(timeout=5000):
                # Green SUCCESS print in console & log!
                success_msg = f"\033[92mSUCCESS: Follow placed successfully for target: {target} using account: {acc['username']} (Task: {task['id']}, Order: {task['order_id']})\033[0m"
                debug_log(success_msg)
                print(success_msg)
                await context.close()
                return True, "Followed successfully"
            else:
                debug_log("DOM Validation detected Action Block (Ghosted Follow)!")
                await context.close()
                return False, "ACTION_BLOCKED_SILENT"
            
        elif action == 'like':
            await human_scroll(page, 300)
            await handle_popups(page)
            unlike_btn = page.locator('svg[aria-label="Unlike"], svg[aria-label="Non mi piace più"], svg[aria-label="Ya no me gusta"], svg[aria-label="Descurtir"]').first
            if await unlike_btn.is_visible(timeout=5000):
                await context.close()
                return True, "Already liked"
            
            # Setup API interception
            api_blocked = False
            async def handle_response(response):
                nonlocal api_blocked
                if "likes/like/" in response.url and response.request.method == "POST":
                    try:
                        json_data = await response.json()
                        if json_data.get('status') == 'fail':
                            api_blocked = True
                            debug_log(f"API BLOCKED! Response: {json_data}")
                    except:
                        pass
            page.on("response", handle_response)
            
            like_btn = page.locator('svg[aria-label="Like"], svg[aria-label="Mi piace"], svg[aria-label="Me gusta"], svg[aria-label="Curtir"]').first
            if await like_btn.is_visible(timeout=5000):
                await like_btn.click(timeout=15000, force=True)
                
                # Wait 10 seconds for API response and Anti-Spam engine to settle
                await asyncio.sleep(10)
                
                if api_blocked:
                    debug_log("API Interception detected Action Block!")
                    await context.close()
                    return False, "ACTION_BLOCKED_API"
                
                # Reload page to check if Instagram ghosted the like
                await page.reload(wait_until="domcontentloaded", timeout=30000)
                await asyncio.sleep(5)
                
                unlike_btn_after = page.locator('svg[aria-label="Unlike"], svg[aria-label="Non mi piace più"], svg[aria-label="Ya no me gusta"], svg[aria-label="Descurtir"]').first
                if await unlike_btn_after.is_visible(timeout=5000):
                    # Green SUCCESS print in console & log!
                    success_msg = f"\033[92mSUCCESS: Like placed successfully for target: {target} using account: {acc['username']} (Task: {task['id']}, Order: {task['order_id']})\033[0m"
                    debug_log(success_msg)
                    print(success_msg)
                    await context.close()
                    return True, "Liked successfully"
                else:
                    debug_log("DOM Validation detected Action Block (Ghosted Like)!")
                    await context.close()
                    return False, "ACTION_BLOCKED_SILENT"
            
            logging.info(f"Like btn not found. URL: {page.url}, Title: {await page.title()}")
            await context.close()
            return False, "Like button not found"

        elif action in ['repost', 'share']:
            await human_scroll(page, 200)
            await handle_popups(page)
            
            # Click the Share (Paper Airplane) button
            share_btn = page.locator('svg[aria-label="Share"], svg[aria-label="Share Post"], svg[aria-label="Share Reel"], svg[aria-label="Share Video"]').first
            if await share_btn.is_visible(timeout=5000):
                await share_btn.click()
                await asyncio.sleep(random.uniform(2.0, 3.0))
                
                # Instead of Copy Link, let's send it in a DM to the post owner so they get a notification!
                try:
                    # Get post owner's username from the header
                    post_owner = await page.locator('header a').first.text_content()
                    if post_owner:
                        post_owner = post_owner.strip()
                        debug_log(f"Found post owner: {post_owner}, sending share DM...")
                        
                        # Type in the search box
                        search_input = page.locator('input[placeholder="Search"], input[placeholder="Search..."]').first
                        if await search_input.is_visible(timeout=5000):
                            await search_input.type(post_owner, delay=100)
                            await asyncio.sleep(2)
                            
                            # Click the first search result
                            first_result = page.locator('div[role="button"]:has-text("' + post_owner + '"), div[role="dialog"] span:has-text("' + post_owner + '")').first
                            if await first_result.is_visible(timeout=5000):
                                await first_result.click()
                                await asyncio.sleep(1)
                                
                                # Click Send button
                                send_btn = page.locator('button:has-text("Send")').first
                                if await send_btn.is_visible(timeout=5000):
                                    await send_btn.click()
                                    await asyncio.sleep(2)
                                    
                                    success_msg = f"\033[92mSUCCESS: {action.capitalize()} (DM) placed successfully for target: {target} to @{post_owner} using account: {acc['username']} (Task: {task['id']}, Order: {task['order_id']})\033[0m"
                                    debug_log(success_msg)
                                    print(success_msg)
                                    await context.close()
                                    return True, f"{action.capitalize()}ed successfully via DM"
                except Exception as e:
                    debug_log(f"Failed to share via DM: {e}. Falling back to Copy Link.")
                
                # Fallback to Copy Link if DM fails
                copy_link_btn = page.locator('div[role="button"]:has-text("Copy link"), div[role="button"]:has-text("Copy Link")').first
                if await copy_link_btn.is_visible(timeout=5000):
                    await copy_link_btn.click()
                    success_msg = f"\033[92mSUCCESS: {action.capitalize()} (Copy Link) placed successfully for target: {target} using account: {acc['username']} (Task: {task['id']}, Order: {task['order_id']})\033[0m"
                    debug_log(success_msg)
                    print(success_msg)
                    await context.close()
                    return True, f"{action.capitalize()}ed successfully"
                else:
                    return False, "Copy link button not found in Share menu"
            else:
                return False, "Share button not found"

        elif action == 'comment':
            await human_scroll(page, 200)
            await handle_popups(page)
            
            # Setup API interception for comments
            api_blocked = False
            async def handle_comment_response(response):
                nonlocal api_blocked
                if "comments/" in response.url and response.request.method == "POST":
                    try:
                        json_data = await response.json()
                        if json_data.get('status') == 'fail':
                            api_blocked = True
                            debug_log(f"COMMENT API BLOCKED! Response: {json_data}")
                    except:
                        pass
            page.on("response", handle_comment_response)
            
            comment_text = ""
            comment_strategy = 'ai_generated'
            custom_comments = ''
            try:
                if task.get('data'):
                    task_data = json.loads(task['data'])
                    comment_strategy = task_data.get('comment_strategy', 'ai_generated')
                    custom_comments = task_data.get('custom_comments', '')
            except:
                pass
                
            if comment_strategy == 'custom_list' and custom_comments:
                comments_list = [c.strip() for c in custom_comments.split('\n') if c.strip()]
                if len(comments_list) <= 1:
                    comma_split = [c.strip() for c in custom_comments.split(',') if c.strip()]
                    if len(comma_split) > 1:
                        comments_list = comma_split
                if comments_list:
                    comment_text = parse_spintax(random.choice(comments_list))
                    
            if not comment_text:
                # Fallback to AI Generated safe comments
                ai_comments = [
                    "Great shot! 🔥", "Love this ❤️", "Amazing content 👏", "Awesome post! 🙌", 
                    "This is so cool! 🤩", "Beautiful! ✨", "Such a vibe! 💯", "Wow, love it! 😍",
                    "Incredible! 🌟", "Keep it up! 💪", "Stunning! 📸", "So inspiring! 💡",
                    "Perfect! 👌", "Absolutely love this! 💖", "Brilliant! 🏆", "Love the aesthetics! 🎨",
                    "This made my day! ☀️", "Superb! ⚡", "What a great capture! 🖼️", "Vibes are immaculate ✨",
                    "Really nicely done! 🎯", "This is everything! 💫", "So good! 🔥", "Loving this so much! 🤍",
                    "Top tier content! 👑", "Obsessed with this! 🥰", "So beautiful! 🌸", "This is fire! 🔥",
                    "Great feed! 👍", "Incredible shot! 🚀"
                ]
                comment_text = random.choice(ai_comments)
                
            comment_input = None
            comment_selectors = [
                'textarea[placeholder*="Add a comment"]',
                'textarea[aria-label*="Add a comment"]',
                'textarea[placeholder*="comment"]',
                'textarea[aria-label*="comment"]',
                'div[contenteditable="true"][aria-label*="comment"]',
                'div[contenteditable="true"][placeholder*="comment"]',
                'div[contenteditable="true"][aria-label*="Comment"]',
                'div[contenteditable="true"][placeholder*="Comment"]',
                'div[contenteditable="true"]',
                'div[role="textbox"]',
                '[placeholder*="Add a comment"]',
                '[aria-label*="Add a comment"]',
                'textarea'
            ]
            
            # First check if textarea is directly visible
            for selector in comment_selectors:
                try:
                    el = page.locator(selector).first
                    if await el.is_visible(timeout=2000):
                        comment_input = el
                        break
                except:
                    continue
                    
            # If not visible directly, we might be on a mobile viewport. Let's find and click the comment balloon icon first!
            if not comment_input:
                debug_log("Textarea not directly visible. Searching for comment icon/button...")
                comment_btn = None
                comment_btn_selectors = [
                    'svg[aria-label="Comment"]',
                    'svg[aria-label="Commenta"]',
                    'svg[aria-label="Comentar"]',
                    'svg[aria-label="Commenter"]',
                    'a[href*="/comments/"]',
                    'button:has(svg[aria-label="Comment"])'
                ]
                for selector in comment_btn_selectors:
                    try:
                        el = page.locator(selector).first
                        if await el.is_visible(timeout=3000):
                            comment_btn = el
                            break
                    except:
                        continue
                        
                if comment_btn:
                    debug_log("Comment icon/button found. Clicking it...")
                    # Click parent if tag is svg
                    tag_name = await comment_btn.evaluate("el => el.tagName.toLowerCase()")
                    if tag_name == "svg":
                        parent = comment_btn.locator('xpath=..')
                        if await parent.is_visible():
                            await parent.click(timeout=5000, force=True)
                        else:
                            await comment_btn.click(timeout=5000, force=True)
                    else:
                        await comment_btn.click(timeout=5000, force=True)
                    await asyncio.sleep(random.uniform(2.0, 4.0))
                    
                    # Search for textarea again
                    for selector in comment_selectors:
                        try:
                            el = page.locator(selector).first
                            if await el.is_visible(timeout=5000):
                                comment_input = el
                                break
                        except:
                            continue
                    
            if not comment_input:
                await save_error_screenshot(page, acc, action, task['id'])
                await context.close()
                return False, "Comment input field not found"
                
            await comment_input.scroll_into_view_if_needed()
            await comment_input.click(force=True)
            await asyncio.sleep(random.uniform(1.0, 2.0))
            
            # Human-like typing with randomized keyboard speed
            await human_type(comment_input, comment_text)
            await asyncio.sleep(random.uniform(1.0, 2.5))
            
            post_btn = None
            post_selectors = [
                'button:has-text("Post")',
                'div[role="button"]:has-text("Post")',
                'span:has-text("Post")',
                'button[type="submit"]',
                'button:has-text("Pubblica")',
                'button:has-text("Publicar")',
                'button:has-text("Teilen")',
                'button:has-text("Publier")',
                'button:has-text("Send")',
                'span:has-text("Pubblica")',
                'span:has-text("Publicar")'
            ]
            for selector in post_selectors:
                try:
                    el = page.locator(selector).first
                    if await el.is_visible(timeout=3000):
                        post_btn = el
                        break
                except:
                    continue
                    
            if post_btn:
                await post_btn.click(timeout=10000, force=True)
                debug_log("Clicked Comment Post button.")
            else:
                await comment_input.press("Enter")
                debug_log("Post button not found. Pressed Enter to submit comment.")
                
            # Wait 10 seconds for API response and Anti-Spam engine to settle
            await asyncio.sleep(10)
            
            if api_blocked:
                debug_log("API Interception detected Action Block for Comment!")
                await context.close()
                return False, "ACTION_BLOCKED_API"
                
            # Reload page to check if Instagram ghosted the comment
            await page.reload(wait_until="domcontentloaded", timeout=30000)
            await asyncio.sleep(5)
            await handle_popups(page)
            
            # Mobile Comment Drawer Re-Verification: Check if textarea/input is visible. 
            # If not, comment drawer is closed on reload, so we must re-open it to verify DOM content.
            textarea_visible = False
            for selector in comment_selectors:
                try:
                    if await page.locator(selector).first.is_visible(timeout=2000):
                        textarea_visible = True
                        break
                except:
                    pass
            
            if not textarea_visible:
                debug_log("Re-verification: Textarea not visible after reload. Attempting to open comment drawer/balloon for verification...")
                comment_btn = None
                comment_btn_selectors = [
                    'svg[aria-label="Comment"]',
                    'svg[aria-label="Commenta"]',
                    'svg[aria-label="Comentar"]',
                    'svg[aria-label="Commenter"]',
                    'a[href*="/comments/"]',
                    'button:has(svg[aria-label="Comment"])'
                ]
                for selector in comment_btn_selectors:
                    try:
                        el = page.locator(selector).first
                        if await el.is_visible(timeout=3000):
                            comment_btn = el
                            break
                    except:
                        continue
                if comment_btn:
                    try:
                        tag_name = await comment_btn.evaluate("el => el.tagName.toLowerCase()")
                        if tag_name == "svg":
                            parent = comment_btn.locator('xpath=..')
                            if await parent.is_visible():
                                await parent.click(timeout=5000, force=True)
                            else:
                                await comment_btn.click(timeout=5000, force=True)
                        else:
                            await comment_btn.click(timeout=5000, force=True)
                        debug_log("Re-verification: Comment drawer opened successfully.")
                        await asyncio.sleep(random.uniform(2.0, 4.0))
                    except Exception as re_e:
                        debug_log(f"Re-verification Warning: Failed to open comment drawer: {re_e}")
            
            content_after = await page.content()
            if comment_text in content_after:
                # Green SUCCESS print in console & log!
                success_msg = f"\033[92mSUCCESS: Comment placed successfully for target: {target} using account: {acc['username']} (Comment: '{comment_text}') (Task: {task['id']}, Order: {task['order_id']})\033[0m"
                debug_log(success_msg)
                print(success_msg)
                await context.close()
                return True, "Commented successfully"
            else:
                debug_log("DOM Validation detected Action Block (Ghosted Comment)!")
                await context.close()
                return False, "ACTION_BLOCKED_SILENT"
            
        await context.close()
        return False, "Unknown action"
    except Exception as e:
        try:
            await save_error_screenshot(page, acc, task.get('action', 'unknown'), task.get('id', 0))
        except:
            pass
        await context.close()
        err_msg = str(e)
        if "ERR_TOO_MANY_REDIRECTS" in err_msg:
            debug_log(f"COOKIES EXPIRED (Redirect Loop) for Account {acc.get('username')}! Marking as Needs Relogin.")
            return False, "COOKIES_EXPIRED"
        return False, err_msg


last_account_id = None

async def warmup_loop(p):
    global last_account_id
    logging.info("Warmup Server started...")
    while True:
        global LAST_ACTIVITY
        LAST_ACTIVITY = datetime.datetime.now()
        try:
            db = mysql.connector.connect(**db_config, autocommit=False)
            cursor = db.cursor(dictionary=True)
            
            cursor.execute("SELECT * FROM ig_accounts WHERE (status = 1 OR status = 0) AND (last_warmup_at IS NULL OR last_warmup_at < NOW() - INTERVAL 1 HOUR) ORDER BY last_warmup_at ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
            warmup_acc = cursor.fetchone()
            if warmup_acc:
                if last_account_id != warmup_acc['id']:
                    log_feature("Proxy Rotation", f"Account changed to {warmup_acc['username']} for WARMUP. Rotating AirProxy IP...", "yellow_bold")
                    acquire_proxy_lock(db, "warmup")
                    success = rotate_proxy_ip_with_verify()
                    if not success:
                        # Skip this cycle if proxy failed to protect account
                        cursor.close()
                        db.close()
                        continue
                    last_account_id = warmup_acc['id']
                    
                try:
                    await asyncio.wait_for(perform_ghost_activity(p, warmup_acc, cursor, db), timeout=1200)
                except asyncio.TimeoutError:
                    debug_log(f"Ghost Activity for {warmup_acc['username']} reached max duration (20m). Ending session cleanly.")
                
                cursor.execute("UPDATE ig_accounts SET last_warmup_at = NOW() WHERE id = %s", (warmup_acc['id'],))
                db.commit()
            
            cursor.close()
            db.close()
        except Exception as e:
            logging.error(f"Loop error: {e}")
            debug_log(f"Loop error: {type(e).__name__} - {e}")
        await asyncio.sleep(15)

async def main():
    global DRY_RUN
    parser = argparse.ArgumentParser()
    parser.add_argument('--check-subscription', type=str, help='Check for new posts of username')
    parser.add_argument('--dry-run', action='store_true', help='Simulate tasks without connecting to Instagram (Safe Mode)')
    args = parser.parse_args()

    if args.dry_run:
        DRY_RUN = True
        logging.info("=========================================")
        logging.info("   DRY RUN (SIMULATION) MODE ACTIVATED   ")
        logging.info("   No network requests will be sent to   ")
        logging.info("   Instagram. Database will be updated.  ")
        logging.info("=========================================")

    threading.Thread(target=watchdog_timer, daemon=True).start()
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True, args=['--no-sandbox', '--disable-dev-shm-usage'])
        
        if args.check_subscription:
            await check_subscription(p, args.check_subscription)
        else:
            await warmup_loop(p)

if __name__ == "__main__":
    asyncio.run(main())
