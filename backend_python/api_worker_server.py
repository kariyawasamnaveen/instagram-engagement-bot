import asyncio
import mysql.connector
import os
import time
import requests
import json
import logging
import random
from datetime import datetime, timedelta
from instagrapi import Client

# ===================== CONFIG =====================
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'Root@123',
    'database': 'smm_db',
    'ssl_disabled': True
}

PROXY_SETTINGS = {
    'server': 'http://YOUR_AIRPROXY_SERVER:11001',
    'username': 'YOUR_AIRPROXY_USER',
    'password': 'YOUR_AIRPROXY_PASS_1'
}

APP_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../../'))
LOG_FILE = os.path.join(APP_DIR, 'logs', 'api_worker_debug.log')

# Setup logging
logging.basicConfig(
    filename=LOG_FILE,
    level=logging.INFO,
    format='[%(asctime)s] API WORKER: %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S'
)

# Helper to log to console and file
def log_msg(msg):
    print(msg)
    logging.info(msg)

def get_db_connection():
    try:
        return mysql.connector.connect(**db_config)
    except Exception as e:
        log_msg(f"DB Connection Error: {e}")
        return None

LAST_ROTATION_TIME = 0

def rotate_proxy(force=False):
    global LAST_ROTATION_TIME
    now = time.time()
    if not force and (now - LAST_ROTATION_TIME < 180):
        # 3 minute cooldown to prevent 429 rate limits
        return False
        
    try:
        res = requests.get('https://airproxy.io/api/proxy/change_ip/?format=json&id=3301&key=YOUR_AIRPROXY_API_KEY', timeout=10)
        if res.status_code == 200:
            log_msg("[PROXY ROTATION] Successfully rotated AirProxy IP.")
            LAST_ROTATION_TIME = now
            time.sleep(15)  # Wait for proxy to stabilize
            return True
        else:
            log_msg(f"[PROXY ROTATION FAILED] Status code: {res.status_code}")
            return False
    except Exception as e:
        log_msg(f"[PROXY ROTATION ERROR] {str(e)}")
        return False

def get_proxy_dict():
    return {
        'http': f"http://{PROXY_SETTINGS['username']}:{PROXY_SETTINGS['password']}@YOUR_AIRPROXY_SERVER:11001",
        'https': f"http://{PROXY_SETTINGS['username']}:{PROXY_SETTINGS['password']}@YOUR_AIRPROXY_SERVER:11001"
    }

async def fetch_task():
    db = get_db_connection()
    if not db: return None
    cursor = db.cursor(dictionary=True)
    task = None
    try:
        db.start_transaction()
        cursor.execute("SELECT * FROM automation_tasks WHERE status = 0 AND action IN ('like', 'follow') AND (run_after IS NULL OR run_after <= NOW()) ORDER BY id ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
        task = cursor.fetchone()
        if task:
            cursor.execute("UPDATE automation_tasks SET status = 1 WHERE id = %s", (task['id'],))
            db.commit()
    except Exception as e:
        db.rollback()
        log_msg(f"Error fetching task: {e}")
    finally:
        cursor.close()
        db.close()
    return task

async def update_task_status(task, account, status, error_msg=None, retry=False):
    db = get_db_connection()
    if not db: return
    cursor = db.cursor(dictionary=True)
    try:
        if retry:
            # Bugfix: Status must be set back to 0 (Pending) so fetch_task selects it on retry.
            cursor.execute("UPDATE automation_tasks SET status = 0, retries = retries + 1, error_message = %s, run_after = DATE_ADD(NOW(), INTERVAL 5 MINUTE), changed = NOW() WHERE id = %s", (error_msg, task['id']))
        else:
            cursor.execute("UPDATE automation_tasks SET status = %s, error_message = %s, changed = NOW() WHERE id = %s", (status, error_msg, task['id']))
            
        # Account auto-blocking on auth/login failures
        if status == 3 and error_msg and account:
            err_lower = error_msg.lower()
            if any(term in err_lower for term in ['login_required', 'challenge_required', 'checkpoint', '403', 'bearer', 'suspended', 'consent_required', 'feedback_required', 'block']):
                log_msg(f"[AUTO-BLOCK] Blocking account {account['username']} due to auth error: {error_msg}")
                cursor.execute("UPDATE ig_accounts SET status = 3, status_message = %s WHERE id = %s", (error_msg, account['id']))
            
        if status == 2:
            # Update account
            cursor.execute("UPDATE ig_accounts SET last_action_at = NOW(), daily_actions = daily_actions + 1 WHERE id = %s", (account['id'],))
            
            # Recalculate remains to avoid race conditions
            cursor.execute("SELECT quantity FROM orders WHERE id = %s", (task['order_id'],))
            order_row = cursor.fetchone()
            if order_row:
                quantity = int(order_row["quantity"])
                cursor.execute("SELECT COUNT(*) as completed FROM automation_tasks WHERE order_id = %s AND status = 2", (task['order_id'],))
                completed = cursor.fetchone()['completed']
                remains = quantity - int(completed)
                if remains < 0:
                    remains = 0
                cursor.execute("UPDATE orders SET remains = %s WHERE id = %s", (remains, task['order_id']))
                
                # Update next task's run_after
                cursor.execute("SELECT orders.delivery_speed FROM orders WHERE orders.id = %s", (task['order_id'],))
                service_data = cursor.fetchone()
                
                if service_data:
                    delay_seconds = 0
                    delivery_speed = service_data.get('delivery_speed', 'instant')
                    if delivery_speed == 'organic':
                        delay_seconds = 60
                    else:
                        delay_seconds = 15
                            
                    cursor.execute("UPDATE automation_tasks SET run_after = NOW() + INTERVAL %s SECOND WHERE order_id = %s AND status = 0 AND (run_after IS NULL OR run_after < NOW() + INTERVAL %s SECOND)", (delay_seconds, task['order_id'], delay_seconds))
            
        # Check if order is fully processed
        cursor.execute("SELECT COUNT(*) as pending_tasks FROM automation_tasks WHERE order_id = %s AND status IN (0, 1)", (task['order_id'],))
        pending_count = cursor.fetchone()['pending_tasks']
        if pending_count == 0:
            cursor.execute("SELECT id, remains, quantity FROM orders WHERE id = %s", (task['order_id'],))
            order_data = cursor.fetchone()
            if order_data:
                remains = int(order_data["remains"])
                quantity = int(order_data["quantity"])
                if remains >= quantity:
                    cursor.execute("UPDATE orders SET status = 'canceled' WHERE id = %s", (order_data['id'],))
                elif 0 < remains < quantity:
                    cursor.execute("UPDATE orders SET status = 'partial' WHERE id = %s", (order_data['id'],))
                else:
                    cursor.execute("UPDATE orders SET status = 'completed' WHERE id = %s", (order_data['id'],))
        db.commit()
    except Exception as e:
        log_msg(f"Error updating task {task['id']}: {e}")
    finally:
        cursor.close()
        db.close()

async def get_api_account():
    db = get_db_connection()
    if not db: return None
    cursor = db.cursor(dictionary=True)
    account = None
    try:
        db.start_transaction()
        cursor.execute("SELECT * FROM ig_accounts WHERE status = 1 AND username LIKE '+91%' ORDER BY last_action_at ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
        account = cursor.fetchone()
        if account:
            cursor.execute("UPDATE ig_accounts SET last_action_at = NOW() WHERE id = %s", (account['id'],))
            db.commit()
    except Exception as e:
        db.rollback()
        log_msg(f"Error fetching API account: {e}")
    finally:
        cursor.close()
        db.close()
    return account

CLIENTS_CACHE = {}

async def execute_task(task, account):
    action = task['action']
    target_url = task['target']
    username = account['username']
    password = account.get('password')
    log_msg(f"Executing API task {task['id']} ({action}) using account {username}...")
    
    if username not in CLIENTS_CACHE:
        if not password:
            return False, "Missing password in database for instagrapi login"
        
        cl = Client()
        proxy_url = f"http://{PROXY_SETTINGS['username']}:{PROXY_SETTINGS['password']}@YOUR_AIRPROXY_SERVER:11001"
        cl.set_proxy(proxy_url)
        
        try:
            log_msg(f"Logging in {username} via instagrapi...")
            cl.login(username, password)
            CLIENTS_CACHE[username] = cl
        except Exception as e:
            err_msg = str(e)
            log_msg(f"Instagrapi Login Failed for {username}: {err_msg}")
            return False, f"Login failed: {err_msg}"
            
    cl = CLIENTS_CACHE[username]
    
    try:
        if action == 'follow':
            target_username = target_url.strip('/').split('/')[-1]
            if '?' in target_username:
                target_username = target_username.split('?')[0]
                
            user_id = cl.user_id_from_username(target_username)
            if not user_id:
                return False, "User ID not found via instagrapi"
                
            # Warm up
            cl.user_info(user_id)
            delay = random.uniform(3.0, 6.0)
            log_msg(f"[WARM UP] Fetched user info for {target_username}, waiting {delay:.1f}s...")
            await asyncio.sleep(delay)
            
            cl.user_follow(user_id)
            log_msg(f"[API SUCCESS] Followed {target_username} ({user_id}) with {username}")
            return True, "Success"
            
        elif action == 'like':
            import re
            m = re.search(r'/(?:p|reel|tv)/([A-Za-z0-9_-]+)', target_url)
            if not m:
                log_msg(f'[URL PARSE ERROR] Cannot extract shortcode from: {target_url}')
                return False, 'Invalid URL - cannot extract shortcode'
            shortcode = m.group(1)
            
            media_pk = cl.media_pk_from_url(target_url)
            if not media_pk:
                return False, "Media PK not found via instagrapi"
                
            # Warm up
            cl.media_info(media_pk)
            delay = random.uniform(3.0, 6.0)
            log_msg(f"[WARM UP] Fetched media info for {shortcode}, waiting {delay:.1f}s...")
            await asyncio.sleep(delay)
            
            cl.media_like(media_pk)
            log_msg(f"[API SUCCESS] Liked {shortcode} ({media_pk}) with {username}")
            return True, "Success"
            
        else:
            log_msg(f"Unsupported action: {action}")
            return False, f"Unsupported action: {action}"
            
    except Exception as e:
        log_msg(f"API Exception: {str(e)}")
        return False, str(e)

def is_proxy_error(err_msg):
    if not err_msg:
        return False
    err_lower = err_msg.lower()
    return any(term in err_lower for term in ['proxy', 'tunnel', 'connection failed', 'remotedisconnected', '503 service', '502 bad gateway', '504 gateway'])

async def worker_loop():
    log_msg("=== API Worker Started ===")
    consecutive_empty = 0
    
    while True:
        task = await fetch_task()
        if not task:
            consecutive_empty += 1
            await asyncio.sleep(10)
            continue
            
        consecutive_empty = 0
        
        # Get an API account
        account = await get_api_account()
        if not account:
            log_msg(f"No valid API (+91) accounts available for task {task['id']}. Re-queueing.")
            await update_task_status(task, account, 0, "No API accounts available", retry=True)
            await asyncio.sleep(15)
            continue
            
        # Rotate proxy before doing the task to prevent IP bans (subject to 3 min cooldown)
        rotate_proxy()
            
        success, err = await execute_task(task, account)
        
        if success:
            await update_task_status(task, account, 2, "Completed via API")
            
            # Anti-ban sleep (random 30-60 seconds)
            delay = 30 + (time.time() % 30)
            log_msg(f"[ANTI-BAN] Sleeping for {delay:.1f}s before next API task...")
            await asyncio.sleep(delay)
        else:
            if is_proxy_error(err):
                log_msg(f"[PROXY ERROR DETECTED] {err}. Forcing proxy rotation.")
                rotate_proxy(force=True)
            await update_task_status(task, account, 3, err, retry=True)

if __name__ == "__main__":
    asyncio.run(worker_loop())
