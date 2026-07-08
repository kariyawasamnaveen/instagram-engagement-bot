import asyncio
import urllib.request
import json
import logging
import mysql.connector
import argparse
import re
import random
import time

def rotate_airproxy_ip():
    url = "https://airproxy.io/api/proxy/change_ip/?format=json&id=1834&key=YOUR_AIRPROXY_API_KEY"
    try:
        logging.info("Requesting AirProxy IP rotation...")
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req, timeout=15) as response:
            data = json.loads(response.read().decode())
            if data.get('status') == 'ok':
                logging.info(f"AirProxy IP rotated successfully. Waiting {data.get('wait_time', 10)}s for modem...")
                time.sleep(12)
            else:
                logging.warning(f"AirProxy IP rotation returned non-ok: {data}")
    except Exception as e:
        logging.error(f"AirProxy IP rotation request failed: {e}")

from playwright.async_api import async_playwright
from playwright_stealth import Stealth

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('/var/log/smm_python_worker_1.log'),
        logging.StreamHandler()
    ]
)

db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'Root@123',
    'database': 'smm_db',
    'raise_on_warnings': True
}

# Initialize Stealth
stealth_obj = Stealth()

DEVICE_FINGERPRINTS = [
    {"ua": "Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1", "vp": {"width": 393, "height": 852}, "is_mobile": True},
    {"ua": "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1", "vp": {"width": 430, "height": 932}, "is_mobile": True},
    {"ua": "Mozilla/5.0 (Linux; Android 13; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Mobile Safari/537.36", "vp": {"width": 384, "height": 826}, "is_mobile": True},
    {"ua": "Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36", "vp": {"width": 412, "height": 915}, "is_mobile": True},
    {"ua": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36", "vp": {"width": 1920, "height": 1080}, "is_mobile": False},
    {"ua": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36", "vp": {"width": 1440, "height": 900}, "is_mobile": False},
    {"ua": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Safari/605.1.15", "vp": {"width": 1280, "height": 800}, "is_mobile": False}
]


async def type_like_human(page, selector, text):
    """Simulates human typing with random delays between keys."""
    await page.click(selector)
    for char in text:
        await page.keyboard.type(char, delay=random.randint(50, 200))
        await asyncio.sleep(random.uniform(0.05, 0.15))

async def simulate_mouse_move(page):
    """Simulates realistic human mouse trajectories across the screen."""
    try:
        width = page.viewport_size['width'] if page.viewport_size else 1280
        height = page.viewport_size['height'] if page.viewport_size else 720
        
        # Generate 3-5 random waypoints
        points = random.randint(3, 5)
        for _ in range(points):
            x = random.randint(int(width * 0.1), int(width * 0.9))
            y = random.randint(int(height * 0.1), int(height * 0.9))
            await page.mouse.move(x, y, steps=random.randint(5, 15))
            await asyncio.sleep(random.uniform(0.2, 0.7))
    except Exception as e:
        logging.debug(f"Mouse simulation error: {e}")

async def simulate_human_scroll(page):
    """Simulates natural scrolling behavior."""
    scroll_times = random.randint(3, 7)
    for _ in range(scroll_times):
        direction = 1 if random.random() > 0.1 else -1 # 90% scroll down, 10% scroll up
        distance = random.randint(200, 600) * direction
        await page.mouse.wheel(0, distance)
        await asyncio.sleep(random.uniform(1.5, 3.5))

async def simulate_human_browsing(page):
    """Simulates a user browsing the home feed or explore before taking action."""
    logging.info("Simulating human pre-action browsing...")
    try:
        await page.goto("https://www.instagram.com/", wait_until="domcontentloaded", timeout=45000)
        await asyncio.sleep(random.uniform(4, 8))
        await simulate_human_scroll(page)
        await simulate_mouse_move(page)
        
        # Randomly interact with Explore, Stories, or Feed posts
        choice = random.choice(['explore', 'feed', 'scroll_more'])
        if choice == 'explore':
            explore_link = page.locator('a[href*="/explore"]').first
            if await explore_link.is_visible(timeout=3000):
                await simulate_mouse_move(page)
                await explore_link.click()
                await asyncio.sleep(random.uniform(5, 10))
                await simulate_human_scroll(page)
        elif choice == 'feed':
            article = page.locator('article').first
            if await article.is_visible(timeout=3000):
                await simulate_mouse_move(page)
                await article.hover()
                await asyncio.sleep(random.uniform(3, 7))
        else:
            await simulate_human_scroll(page)
            await asyncio.sleep(random.uniform(2, 5))
    except Exception as e:
        logging.warning(f"Human browsing simulation failed/skipped: {e}")

async def intercept_route(route):
    if route.request.resource_type in ["image", "media", "font"]:
        await route.abort()
    else:
        await route.continue_()

async def update_account_cookies(acc_id, cookies):
    try:
        db = mysql.connector.connect(**db_config)
        cursor = db.cursor()
        cursor.execute("UPDATE ig_accounts SET cookies = %s WHERE id = %s", (json.dumps(cookies), acc_id))
        db.commit()
        cursor.close()
        db.close()
        logging.info(f"Updated cookies for account ID {acc_id}")
    except Exception as e:
        logging.error(f"Failed to update cookies in DB: {e}")

async def login_account(page, username, password, acc_id):
    logging.info(f"Attempting human-like login for {username}...")
    try:
        await page.goto("https://www.instagram.com/accounts/login/", wait_until="domcontentloaded", timeout=60000)
        await asyncio.sleep(random.uniform(3, 6))
        
        # Check if already logged in (sometimes happens if cookies partially work)
        if "login" not in page.url:
             logging.info("Already logged in during login attempt.")
             return True

        await simulate_mouse_move(page)
        await type_like_human(page, 'input[name="username"]', username)
        await asyncio.sleep(random.uniform(1, 2))
        await type_like_human(page, 'input[name="password"]', password)
        await asyncio.sleep(random.uniform(1, 3))
        
        await simulate_mouse_move(page)
        await page.click('button[type="submit"]')
        
        # Wait for navigation or error
        await asyncio.sleep(12)
        
        content = await page.content()
        if "Save Your Login Info?" in content or "home" in page.url or "feed" in page.url or ("challenge" not in page.url and "suspended" not in page.url and ("instagram.com" in page.url and "login" not in page.url)):
            logging.info(f"Login successful for {username}")
            cookies = await page.context.cookies()
            await update_account_cookies(acc_id, cookies)
            return True
        else:
            logging.warning(f"Login failed for {username}. Check for challenge/checkpoint.")
            return False
    except Exception as e:
        logging.error(f"Login error for {username}: {e}")
        return False

async def perform_action(browser, acc, task):
    fp = random.choice(DEVICE_FINGERPRINTS)
    context = await browser.new_context(
        user_agent=fp['ua'],
        viewport=fp['vp'],
        is_mobile=fp['is_mobile'],
        has_touch=fp['is_mobile']
    )
    logging.info(f"Using Device Fingerprint: {fp['ua']} | Mobile: {fp['is_mobile']}")
    # Enable Data Saving
    await context.route("**/*", intercept_route)
    
    try:
        if acc.get('cookies'):
            try:
                await context.add_cookies(json.loads(acc['cookies']))
            except Exception as ce:
                logging.warning(f"Failed to load cookies for {acc['username']}: {ce}")
        
        page = await context.new_page()
        # Apply Stealth
        await stealth_obj.apply_stealth_async(page)
        
        action = task['action']
        target = task['target']
        
        # 50% chance to simulate browsing before action to avoid detection patterns
        if random.random() > 0.5:
            await simulate_human_browsing(page)
            await asyncio.sleep(random.uniform(2, 5))

        logging.info(f"Navigating to target: {target}")
        try:
            await page.goto(target, wait_until="domcontentloaded", timeout=45000)
        except Exception as e:
            err_str = str(e)
            if "ERR_TOO_MANY_REDIRECTS" in err_str:
                logging.warning(f"Redirect loop for {acc['username']}. Clearing cookies.")
                await context.clear_cookies()
                await page.goto(target, wait_until="domcontentloaded", timeout=45000)
            elif any(pe in err_str for pe in ["ERR_PROXY_CONNECTION_FAILED", "ERR_TUNNEL_CONNECTION_FAILED", "ERR_TIMED_OUT", "Timeout"]):
                logging.error(f"Proxy/Timeout error navigating to target for {acc['username']}: {err_str}")
                await context.close()
                return False, "PROXY_ERROR"
            else:
                raise e
        
        await asyncio.sleep(random.uniform(4, 8))
        content = await page.content()
        
        # Check for Login Wall
        if "login/?next=" in page.url or "Login • Instagram" in content or "log in to see" in content.lower():
            logging.info(f"Login wall detected for account {acc['username']}. Attempting re-login.")
            if await login_account(page, acc['username'], acc['password'], acc['id']):
                # Retry target after login
                await page.goto(target, wait_until="domcontentloaded", timeout=45000)
                await asyncio.sleep(random.uniform(5, 10))
                content = await page.content()
            else:
                await context.close()
                return False, "LOGIN_FAILED"

        # Check for block/challenge
        block_keywords = ["Account suspended", "Action Blocked", "Your account has been disabled", "Help us confirm you own this account", "challenge/?next=", "Suspicious activity", "checkpoint"]
        for kw in block_keywords:
            if kw in content or kw.lower() in page.url.lower():
                logging.error(f"Account {acc['username']} BLOCKED or CHALLENGED: {kw}")
                await context.close()
                return False, "ACCOUNT_BLOCKED"

        try:
            # Close popups randomly (like a human would)
            close_btn = page.locator('svg[aria-label="Close"]').first
            if await close_btn.is_visible(timeout=3000):
                await simulate_mouse_move(page)
                await asyncio.sleep(random.uniform(1, 3))
                await close_btn.click()
        except: pass

        # Simulate human dwell time and inspection on target page
        await simulate_human_scroll(page)
        await simulate_mouse_move(page)
        await asyncio.sleep(random.uniform(3, 7))

        if action == 'follow':
            follow_btn = page.get_by_role("button", name=re.compile(r"^(Follow( Back)?|Seguir|Folgen|Segui)$", re.IGNORECASE)).first
            if not await follow_btn.is_visible(timeout=5000):
                if await page.get_by_role("button", name=re.compile(r"^(Requested|Following|Siguiendo|Folge ich|Segui già)$", re.IGNORECASE)).first.is_visible(timeout=5000):
                    await context.close()
                    return True, "Already followed"
                await context.close()
                return False, "Follow button not found"
            
            # Hover before click
            await simulate_mouse_move(page)
            await follow_btn.hover()
            await asyncio.sleep(random.uniform(0.5, 1.5))
            await follow_btn.click(timeout=15000, force=True)
            await asyncio.sleep(random.uniform(4, 7))
            
            # Verify action
            if await page.get_by_role("button", name=re.compile(r"^(Requested|Following|Siguiendo|Folge ich|Segui già)$", re.IGNORECASE)).first.is_visible(timeout=5000):
                await context.close()
                return True, "Followed successfully"
            else:
                await context.close()
                return False, "Follow action did not register"
            
        elif action == 'like':
            unlike_btn = page.locator('svg[aria-label="Unlike"], svg[aria-label="Ya no me gusta"]').first
            if await unlike_btn.is_visible(timeout=5000):
                await context.close()
                return True, "Already liked"
            
            like_btn = page.locator('svg[aria-label="Like"], svg[aria-label="Me gusta"], svg[aria-label="Super"]').first
            if await like_btn.is_visible(timeout=5000):
                await simulate_mouse_move(page)
                await like_btn.hover()
                await asyncio.sleep(random.uniform(0.5, 1.5))
                await like_btn.click(timeout=15000, force=True)
                await asyncio.sleep(random.uniform(4, 7))
                
                # Verify like
                if await page.locator('svg[aria-label="Unlike"], svg[aria-label="Ya no me gusta"]').first.is_visible(timeout=5000):
                    await context.close()
                    return True, "Liked successfully"
                else:
                    await context.close()
                    return False, "Like action did not register"
            
            # Fallback: Double click on the media
            try:
                media = page.locator('article img, article video').first
                if await media.is_visible(timeout=2000):
                    await simulate_mouse_move(page)
                    await media.hover()
                    await asyncio.sleep(random.uniform(0.5, 1.5))
                    await media.dblclick(force=True)
                    await asyncio.sleep(random.uniform(4, 7))
                    await context.close()
                    return True, "Liked via double-click"
            except: pass
            
            await context.close()
            return False, "Like button not found"
            
        await context.close()
        return False, "Unknown action"
    except Exception as e:
        await context.close()
        err_str = str(e)
        if any(pe in err_str for pe in ["ERR_PROXY_CONNECTION_FAILED", "ERR_TUNNEL_CONNECTION_FAILED", "ERR_TIMED_OUT", "Timeout"]):
            return False, "PROXY_ERROR"
        return False, err_str

async def worker_loop(browser):
    logging.info("Worker started with advanced human behavior and watchdog recovery logic...")
    while True:
        db = None
        try:
            db = mysql.connector.connect(**db_config, autocommit=False)
            cursor = db.cursor(dictionary=True)
            
            # 1. Clear stuck tasks (Self-Healing)
            cursor.execute("UPDATE automation_tasks SET status = 0 WHERE status = 1 AND changed < NOW() - INTERVAL 15 MINUTE")
            db.commit()
            
            # 2. Get a task
            cursor.execute("SELECT * FROM automation_tasks WHERE status = 0 AND (run_after IS NULL OR run_after <= NOW()) ORDER BY id ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
            task = cursor.fetchone()
            
            if task:
                # 3. Get an account (Rotation logic)
                task_data_json = task.get('data')
                target_gender = 'all'
                if task_data_json:
                    try:
                        import json
                        task_data = json.loads(task_data_json)
                        target_gender = task_data.get('tag_filter', 'all')
                    except:
                        pass
                
                if target_gender in ['male', 'female']:
                    cursor.execute("SELECT * FROM ig_accounts WHERE status = 1 AND gender = %s ORDER BY last_action_at ASC LIMIT 1 FOR UPDATE SKIP LOCKED", (target_gender,))
                else:
                    cursor.execute("SELECT * FROM ig_accounts WHERE status = 1 ORDER BY last_action_at ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
                
                acc = cursor.fetchone()
                
                if acc:
                    # Reserve both
                    cursor.execute("UPDATE automation_tasks SET status = 1, changed = NOW() WHERE id = %s", (task['id'],))
                    cursor.execute("UPDATE ig_accounts SET last_action_at = NOW() WHERE id = %s", (acc['id'],))
                    db.commit()
                    
                    try:
                        # Task timeout of 5 minutes
                        await asyncio.to_thread(rotate_airproxy_ip)
                        success, msg = await asyncio.wait_for(perform_action(browser, acc, task), timeout=300)
                        
                        db.reconnect()
                        cursor = db.cursor(dictionary=True)
                        
                        if msg in ["ACCOUNT_BLOCKED", "LOGIN_FAILED"]:
                            # Auto-Recovery Watchdog: Mark account as status = 3 (Banned/Checkpoint/Logged Out)
                            status_msg = "BANNED/CHECKPOINT" if msg == "ACCOUNT_BLOCKED" else "Expired/Logged Out"
                            cursor.execute("UPDATE ig_accounts SET status = 3, status_message = %s, updated = NOW() WHERE id = %s", (status_msg, acc['id']))
                            # Put task back in queue immediately to be picked up by another healthy account
                            cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                            logging.warning(f"Account {acc['username']} flagged as {status_msg}. Task {task['id']} re-queued.")
                        elif msg == "PROXY_ERROR":
                            # Proxy issue: Keep account active, retry task later, cool down worker
                            cursor.execute("UPDATE ig_accounts SET last_action_at = NOW() WHERE id = %s", (acc['id'],))
                            cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                            logging.warning(f"Proxy error for account {acc['username']}. Task {task['id']} re-queued. Cooling down.")
                            db.commit()
                            await asyncio.sleep(15)
                        elif success:
                            cursor.execute("UPDATE automation_tasks SET status = 2, error_message = %s, changed = NOW() WHERE id = %s", (msg, task['id']))
                            logging.info(f"Task {task['id']} completed successfully by {acc['username']}: {msg}")
                        else:
                            # Retry logic for normal task failures (e.g. button not found)
                            retries = task.get('retries', 0)
                            if retries < 3:
                                cursor.execute("UPDATE automation_tasks SET status = 0, retries = %s, run_after = NOW() + INTERVAL 5 MINUTE, error_message = %s, changed = NOW() WHERE id = %s", (retries + 1, msg, task['id']))
                                logging.info(f"Task {task['id']} failed ({msg}). Retrying ({retries+1}/3).")
                            else:
                                cursor.execute("UPDATE automation_tasks SET status = 3, error_message = %s, changed = NOW() WHERE id = %s", (msg, task['id']))
                                logging.error(f"Task {task['id']} failed permanently after 3 retries: {msg}")
                        db.commit()
                    except Exception as e:
                        logging.error(f"Task {task['id']} exception: {e}")
                        db.reconnect()
                        cursor = db.cursor(dictionary=True)
                        retries = task.get('retries', 0)
                        cursor.execute("UPDATE automation_tasks SET status = 0, retries = %s, run_after = NOW() + INTERVAL 5 MINUTE, error_message = %s, changed = NOW() WHERE id = %s", (retries + 1, str(e), task['id']))
                        db.commit()
                else:
                    # No accounts available, cool down
                    cursor.execute("UPDATE automation_tasks SET status = 0, run_after = NOW() + INTERVAL 5 MINUTE, error_message = 'No accounts available', changed = NOW() WHERE id = %s", (task['id'],))
                    db.commit()
                    await asyncio.sleep(30)
            
            cursor.close()
            db.close()
        except Exception as e:
            logging.error(f"Loop error: {e}")
            if db and db.is_connected():
                db.rollback()
                db.close()
        # Randomized loop delay
        await asyncio.sleep(random.uniform(10, 20))

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch(
            headless=True, 
            args=['--no-sandbox', '--disable-dev-shm-usage', '--disable-blink-features=AutomationControlled'],
            proxy={
                "server": "http://YOUR_AIRPROXY_SERVER:30909",
                "username": "YOUR_AIRPROXY_USER",
                "password": "bCOBQdbBaLdNKl"
            }
        )
        await worker_loop(browser)

if __name__ == "__main__":
    asyncio.run(main())
