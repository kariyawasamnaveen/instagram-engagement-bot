import mysql.connector
import json
import asyncio
import logging
import re
import random
import sys
import argparse
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
    "Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1",
    "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1",
    "Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 13; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36"
]

def get_user_agent(acc, cursor):
    if not acc.get('user_agent'):
        ua = random.choice(USER_AGENTS)
        cursor.execute("UPDATE ig_accounts SET user_agent = %s WHERE id = %s", (ua, acc['id']))
        acc['user_agent'] = ua
    return acc['user_agent']

async def human_scroll(page, distance):
    steps = random.randint(3, 7)
    step_distance = distance / steps
    for _ in range(steps):
        await page.mouse.wheel(0, step_distance + random.randint(-20, 20))
        await asyncio.sleep(random.uniform(0.1, 0.4))
    await asyncio.sleep(random.uniform(1.0, 2.5))


async def check_subscription(browser, username):
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
        context = await browser.new_context(
            proxy=PROXY_SETTINGS,
            user_agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"
        )
        page = await context.new_page()
        # Block images, media, and fonts to speed up and reduce bandwidth
        await page.route("**/*", lambda route: route.abort() if route.request.resource_type in ["image", "media", "font"] else route.continue_())
        await Stealth().apply_stealth_async(page)
        
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

async def warmup_account(page, acc):
    debug_log(f"Warming up account {acc['username']}...")
    try:
        await page.goto("https://www.instagram.com/", wait_until="domcontentloaded", timeout=60000)
        await asyncio.sleep(random.uniform(5, 10))
        await handle_popups(page)
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

async def perform_action(browser, acc, task, cursor, db):
    target = task.get('target', '')
    if target.endswith('instagram.com//') or len(target.split('/')) <= 3:
        return False, "INVALID_URL"

    # Daily limits check
    today = datetime.date.today()
    last_reset = acc.get('last_daily_reset')
    daily_actions = acc.get('daily_actions', 0)
    
    if last_reset != today:
        cursor.execute("UPDATE ig_accounts SET daily_actions = 0, last_daily_reset = %s WHERE id = %s", (today, acc['id']))
        db.commit()
        daily_actions = 0
        
    if daily_actions >= 40:
        return False, "DAILY_LIMIT_REACHED"

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
        
    context = await browser.new_context(**context_args)
    try:
        if acc.get('cookies'):
            await context.add_cookies(json.loads(acc['cookies']))
        
        page = await context.new_page()
        # Block images, media, fonts, and tracking scripts
        await page.route("**/*", lambda route: route.abort() if route.request.resource_type in ["image", "media", "font"] or any(x in route.request.url for x in ["analytics", "tracking", "logging", "facebook.com/tr"]) else route.continue_())
        await Stealth().apply_stealth_async(page)
        
        # Warmup phase
        await warmup_account(page, acc)
        
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
            await page.goto(target, wait_until="domcontentloaded", timeout=60000)
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
                await page.goto(target, wait_until="domcontentloaded", timeout=60000)
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
                    debug_log("Organic Simulation: Scrolling to first profile post thumbnail.")
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
            await asyncio.sleep(random.uniform(1.0, 2.0))
            await handle_popups(page)
            
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
            await comment_input.type(comment_text, delay=random.uniform(50, 150))
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


async def worker_loop(browser):
    logging.info("Worker started...")
    while True:
        try:
            db = mysql.connector.connect(**db_config, autocommit=False)
            cursor = db.cursor(dictionary=True)
            
            cursor.execute("UPDATE automation_tasks SET status = 0 WHERE status = 1 AND changed < NOW() - INTERVAL 10 MINUTE")
            db.commit()
            
            cursor.execute("SELECT * FROM automation_tasks WHERE status = 0 AND (run_after IS NULL OR run_after <= NOW()) ORDER BY id ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
            task = cursor.fetchone()
            
            if task:
                debug_log(f"Picked up task {task['id']} for Order {task['order_id']} (Action: {task.get('action')})")
                cursor.execute("UPDATE automation_tasks SET status = 1, changed = NOW() WHERE id = %s", (task['id'],))
                cursor.execute("UPDATE orders SET status = 'processing' WHERE id = %s AND status IN ('pending', 'inprogress')", (task['order_id'],))
                db.commit()
                
                try:
                    logging.info("Rotating AirProxy IP...")
                    requests.get("https://airproxy.io/api/proxy/change_ip/?format=json&id=3301&key=YOUR_AIRPROXY_API_KEY", timeout=15)
                    time.sleep(25)
                except Exception as e:
                    debug_log(f"PROXY ERROR: AirProxy rotation failed! {e}")
                    logging.error(f"AirProxy rotation failed: {e}")

                cursor.execute("SELECT * FROM ig_accounts WHERE status = 1 ORDER BY last_action_at ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
                acc = cursor.fetchone()
                
                if acc:
                    try:
                        success, msg = await asyncio.wait_for(perform_action(browser, acc, task, cursor, db), timeout=300)
                        
                        if msg in ("ACCOUNT_BLOCKED", "ACTION_BLOCKED_SILENT", "ACTION_BLOCKED_API"):
                            debug_log(f"ACCOUNT ERROR: Account {acc['username']} BLOCKED ({msg}). Switching account.")
                            # Block account so it is not used again
                            cursor.execute("UPDATE ig_accounts SET status = 3 WHERE id = %s", (acc['id'],))
                            cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                            logging.warning(f"Account {acc['username']} BLOCKED ({msg}). Switching account for task {task['id']}")
                        elif msg == "COOKIES_EXPIRED":
                            cursor.execute("UPDATE ig_accounts SET status = 3 WHERE id = %s", (acc['id'],))
                            cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                        elif msg == "ERR_TUNNEL_CONNECTION_FAILED":
                            cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                        elif success:
                            debug_log(f"Task {task['id']} SUCCESSFUL. Updating Order {task['order_id']} remains.")
                            logging.info(f"Task {task['id']} success. Updating order {task['order_id']} remains.")
                            cursor.execute("UPDATE automation_tasks SET status = 2, error_message = %s, changed = NOW() WHERE id = %s", (msg, task['id']))
                            cursor.execute("UPDATE ig_accounts SET last_action_at = NOW(), daily_actions = daily_actions + 1 WHERE id = %s", (acc['id'],))
                            
                            # Recalculate remains to avoid race conditions
                            cursor.execute("SELECT quantity FROM orders WHERE id = %s", (task['order_id'],))
                            order_row = cursor.fetchone()
                            if order_row:
                                qty = int(order_row['quantity'])
                                cursor.execute("SELECT COUNT(*) as success_count FROM automation_tasks WHERE order_id = %s AND status = 2", (task['order_id'],))
                                success_count = int(cursor.fetchone()['success_count'])
                                new_remains = max(0, qty - success_count)
                                
                                cursor.execute("UPDATE orders SET remains = %s WHERE id = %s", (new_remains, task['order_id']))
                                if new_remains > 0:
                                    cursor.execute("UPDATE orders SET status = 'inprogress' WHERE id = %s", (task['order_id'],))
                                else:
                                    cursor.execute("UPDATE orders SET status = 'completed' WHERE id = %s", (task['order_id'],))
                            
                            # Add delay based on delivery_speed option and action type
                            import random
                            import json
                            delivery_speed = 'steady'
                            action_type = task.get('action', 'like')
                            try:
                                if task.get('data'):
                                    task_data = json.loads(task['data'])
                                    delivery_speed = task_data.get('delivery_speed', 'steady')
                            except:
                                pass
                                
                            if action_type == 'follow':
                                if delivery_speed == 'organic': # Organic Human (Safe)
                                    delay_seconds = random.randint(180, 300)
                                else: # steady or default -> Steady Scale
                                    delay_seconds = random.randint(60, 120)
                            else: # like
                                if delivery_speed == 'organic': # Natural Drip (Safe)
                                    delay_seconds = random.randint(60, 120)
                                else: # instant or default -> Accelerated
                                    delay_seconds = random.randint(15, 30)
                                    
                            cursor.execute("UPDATE automation_tasks SET run_after = NOW() + INTERVAL %s SECOND WHERE order_id = %s AND status = 0 AND (run_after IS NULL OR run_after < NOW() + INTERVAL %s SECOND)", (delay_seconds, task['order_id'], delay_seconds))
                        elif msg == "INVALID_URL":
                            debug_log(f"TASK ERROR: Task {task['id']} failed permanently: INVALID_URL")
                            cursor.execute("UPDATE automation_tasks SET status = 3, error_message = %s, changed = NOW() WHERE id = %s", (msg, task['id']))
                            logging.error(f"Task {task['id']} failed permanently: INVALID_URL")
                        else:
                            retries = task.get('retries', 0)
                            if retries < 3:
                                debug_log(f"TASK ERROR: Task {task['id']} failed. Retrying ({retries+1}/3). Error: {msg}")
                                cursor.execute("UPDATE automation_tasks SET status = 0, retries = %s, run_after = NOW() + INTERVAL 5 MINUTE, error_message = %s, changed = NOW() WHERE id = %s", (retries + 1, msg, task['id']))
                                logging.info(f"Task {task['id']} failed. Retrying ({retries+1}/3) in 5 mins. Error: {msg}")
                            else:
                                debug_log(f"TASK ERROR: Task {task['id']} failed permanently after 3 retries. Error: {msg}")
                                cursor.execute("UPDATE automation_tasks SET status = 3, error_message = %s, changed = NOW() WHERE id = %s", (msg, task['id']))
                        
                        db.commit()
                    except asyncio.TimeoutError:
                        retries = task.get('retries', 0)
                        if retries < 3:
                            debug_log(f"TASK ERROR: Task {task['id']} TIMEOUT. Retrying ({retries+1}/3).")
                            cursor.execute("UPDATE automation_tasks SET status = 0, retries = %s, run_after = NOW() + INTERVAL 5 MINUTE, error_message = 'Timeout', changed = NOW() WHERE id = %s", (retries + 1, task['id']))
                        else:
                            debug_log(f"TASK ERROR: Task {task['id']} TIMEOUT permanently.")
                            cursor.execute("UPDATE automation_tasks SET status = 3, error_message = 'Timeout', changed = NOW() WHERE id = %s", (task['id'],))
                        db.commit()
                        
                        # Check if order is fully processed
                        cursor.execute("SELECT COUNT(*) as pending_tasks FROM automation_tasks WHERE order_id = %s AND status IN (0, 1)", (task['order_id'],))
                        pending_count = cursor.fetchone()['pending_tasks']
                        
                        if pending_count == 0:
                            cursor.execute("SELECT id, status, remains, quantity FROM orders WHERE id = %s", (task['order_id'],))
                            order_data = cursor.fetchone()
                            if order_data and order_data['status'] not in ('completed', 'canceled', 'partial'):
                                remains = int(order_data['remains'])
                                quantity = int(order_data['quantity'])
                                
                                if remains >= quantity:
                                    cursor.execute("UPDATE orders SET status = 'canceled' WHERE id = %s", (order_data['id'],))
                                elif remains > 0 and remains < quantity:
                                    cursor.execute("UPDATE orders SET status = 'partial' WHERE id = %s", (order_data['id'],))
                                
                                db.commit()
                else:
                    cursor.execute("UPDATE automation_tasks SET status = 0, run_after = NOW() + INTERVAL 10 MINUTE, error_message = 'No accounts available', changed = NOW() WHERE id = %s", (task['id'],))
                    db.commit()
            
            cursor.close()
            db.close()
        except Exception as e:
            logging.error(f"Loop error: {e}")
        await asyncio.sleep(10)

async def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--check-subscription', type=str, help='Check for new posts of username')
    args = parser.parse_args()

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True, args=['--no-sandbox', '--disable-dev-shm-usage'])
        
        if args.check_subscription:
            await check_subscription(browser, args.check_subscription)
        else:
            await worker_loop(browser)

if __name__ == "__main__":
    asyncio.run(main())
