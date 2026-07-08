import os
import sys
import time
import json
import random
import logging
import datetime
import mysql.connector
from instagrapi import Client
from instagrapi.exceptions import (
    ClientError,
    LoginRequired,
    ChallengeRequired,
    FeedbackRequired,
    PleaseWaitFewMinutes
)
import requests

# Setup logging
logging.basicConfig(level=logging.INFO, format='[%(asctime)s] API WORKER: %(message)s', datefmt='%Y-%m-%d %H:%M:%S')

# DB Connection Details
DB_HOST = '127.0.0.1'
DB_USER = 'root'
DB_PASS = 'Root@123'
DB_NAME = 'smm_db'

def get_db_connection():
    try:
        return mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASS,
            database=DB_NAME,
            charset='utf8mb4'
        )
    except Exception as e:
        logging.error(f"DB Connection Error: {e}")
        return None

def debug_log(message):
    try:
        db = get_db_connection()
        if db:
            cursor = db.cursor()
            cursor.execute("INSERT INTO order_debug_logs (log_message) VALUES (%s)", (message,))
            db.commit()
            cursor.close()
            db.close()
    except: pass
    logging.info(message)

def get_proxy():
    # AirProxy format for requests
    return "http://YOUR_AIRPROXY_USER:YOUR_AIRPROXY_PASS_2@YOUR_AIRPROXY_SERVER:30909"

def rotate_proxy():
    try:
        logging.info("Rotating AirProxy IP...")
        res = requests.get("https://airproxy.io/api/proxy/change_ip/?format=json&id=1834&key=YOUR_AIRPROXY_API_KEY", timeout=15)
        logging.info(f"AirProxy rotation response: {res.text}")
        time.sleep(25)
    except Exception as e:
        logging.error(f"AirProxy rotation failed: {e}")

def get_client(acc):
    username = acc['username']
    password = acc['password']
    session_file = f"/var/www/html/app/modules/cron/controllers/python_worker/sessions/{username}.json"
    
    cl = Client()
    # Set proxy
    cl.set_proxy(get_proxy())
    
    # Check if session exists
    if os.path.exists(session_file):
        try:
            cl.load_settings(session_file)
            cl.login(username, password)
            cl.get_timeline_feed() # Validate session
            return cl
        except Exception as e:
            logging.warning(f"Session invalid for {username}. Re-logging in... Error: {e}")
            os.remove(session_file)
            
    # Fresh login
    try:
        cl.login(username, password)
        cl.dump_settings(session_file)
        return cl
    except ChallengeRequired as e:
        debug_log(f"ACCOUNT ERROR: {username} - Challenge Required")
        return "CHALLENGE_REQUIRED"
    except FeedbackRequired as e:
        debug_log(f"ACCOUNT ERROR: {username} - Action Blocked (Feedback Required)")
        return "FEEDBACK_REQUIRED"
    except Exception as e:
        debug_log(f"ACCOUNT ERROR: {username} - Login Failed: {e}")
        return "LOGIN_FAILED"

last_account_id = None

def main():
    global last_account_id
    logging.info("API Worker Started...")
    while True:
        db = get_db_connection()
        if not db:
            time.sleep(10)
            continue
            
        cursor = db.cursor(dictionary=True)
        try:
            # Fetch a pending task
            cursor.execute("SELECT * FROM automation_tasks WHERE status = 0 AND (run_after IS NULL OR run_after <= NOW()) ORDER BY id ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
            task = cursor.fetchone()
            
            if task:
                task_id = task['id']
                order_id = task['order_id']
                action = task['action']
                target = task['target']
                
                # --- START APP TO BACKEND DEBUG LOGGING ---
                logging.info(f"========== NEW ORDER INITIATED ==========")
                logging.info(f"Task ID: {task_id} | Order ID: {order_id} | Action: {action}")
                
                # 1. Print Raw App Data Payload from Database
                raw_data_str = task.get('data', '{}')
                logging.info(f"1. RAW APP DATA (Backend Payload): {raw_data_str}")
                
                # 2. Print Worker Extracted Data
                try:
                    parsed_data = json.loads(raw_data_str) if raw_data_str else {}
                except:
                    parsed_data = {}
                    
                delivery_speed = parsed_data.get('delivery_speed', 'steady')
                demographic = parsed_data.get('demographic', 'mixed')
                comment_strategy = parsed_data.get('comment_strategy', 'ai_generated')
                custom_comments = parsed_data.get('custom_comments', '')
                algo_saves = parsed_data.get('algo_saves', False)
                story_reposts = parsed_data.get('story_reposts', False)
                push_strategy = parsed_data.get('push_strategy', '12h_momentum')
                
                logging.info(f"2. WORKER EXTRACTED DATA:")
                logging.info(f"   - Target Link/Username: {target}")
                logging.info(f"   - Delivery Velocity: {delivery_speed}")
                logging.info(f"   - Target Demographic: {demographic}")
                logging.info(f"   - Comment Strategy: {comment_strategy}")
                if comment_strategy == 'custom_list' and custom_comments:
                    logging.info(f"   - Custom Comments: {custom_comments.replace(chr(10), ' | ')}")
                if action == 'viral_multiplier':
                    logging.info(f"   - Algorithm Saves: {algo_saves}")
                    logging.info(f"   - Story Reposts: {story_reposts}")
                    logging.info(f"   - Push Strategy: {push_strategy}")
                logging.info(f"=========================================")
                # --- END DEBUG LOGGING ---
                
                cursor.execute("UPDATE automation_tasks SET status = 1, changed = NOW() WHERE id = %s", (task_id,))
                cursor.execute("UPDATE orders SET status = 'processing' WHERE id = %s AND status IN ('pending', 'inprogress')", (order_id,))
                db.commit()
                
                # Get active account
                cursor.execute("SELECT * FROM ig_accounts WHERE status = 1 ORDER BY last_action_at ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
                acc = cursor.fetchone()
                
                if acc:
                    if last_account_id != acc['id']:
                        logging.info(f"Account changed to {acc['username']}. Rotating AirProxy IP...")
                        rotate_proxy()
                        last_account_id = acc['id']
                    else:
                        logging.info(f"Continuing with same account {acc['username']}. Skipping proxy rotation to preserve IP session.")
                
                if not acc:
                    logging.warning("No active accounts available!")
                    cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task_id,))
                    db.commit()
                    time.sleep(30)
                    continue
                    
                cl = get_client(acc)
                if isinstance(cl, str):
                    # Block the account
                    cursor.execute("UPDATE ig_accounts SET status = 3 WHERE id = %s", (acc['id'],))
                    # Retry task
                    cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task_id,))
                    db.commit()
                    logging.warning(f"Account {acc['username']} blocked ({cl}). Switched status to 3.")
                    continue
                    
                # Perform Action
                success = False
                error_msg = ""
                
                try:
                    if action == 'follow':
                        # Target should be a username or URL
                        # Extract username if URL
                        target_username = target.split('instagram.com/')[-1].split('/')[0].split('?')[0]
                        user_id = cl.user_id_from_username(target_username)
                        result = cl.user_follow(user_id)
                        if result:
                            success = True
                        else:
                            error_msg = "Follow API returned false"
                    
                    elif action == 'like':
                        # Target is a post URL
                        media_pk = cl.media_pk_from_url(target)
                        result = cl.media_like(media_pk)
                        if result:
                            success = True
                        else:
                            error_msg = "Like API returned false"
                    
                    elif action == 'comment':
                        media_pk = cl.media_pk_from_url(target)
                        comment_text = ""
                        
                        if comment_strategy == 'custom_list' and custom_comments:
                            comments_list = [c.strip() for c in custom_comments.split(chr(10)) if c.strip()]
                            if len(comments_list) <= 1:
                                comma_split = [c.strip() for c in custom_comments.split(',') if c.strip()]
                                if len(comma_split) > 1:
                                    comments_list = comma_split
                            if comments_list:
                                comment_text = random.choice(comments_list)
                                
                        if not comment_text:
                            # Fallback to AI Generated safe comments (30 generic)
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
                            
                        result = cl.media_comment(media_pk, comment_text)
                        if result:
                            success = True
                            logging.info(f"Successfully posted comment: {comment_text}")
                        else:
                            error_msg = "Comment API returned false"
                            
                    elif action == 'viral_multiplier':
                        media_pk = cl.media_pk_from_url(target)
                        success = True # Assume success unless an enabled step fails
                        
                        if algo_saves:
                            save_res = cl.media_save(media_pk)
                            if save_res:
                                logging.info(f"Algorithm Save successful for {media_pk}")
                            else:
                                success = False
                                error_msg = "Save API returned false"
                                
                        if story_reposts and success:
                            # Download and upload
                            media_info = cl.media_info(media_pk)
                            dl_path = ""
                            try:
                                if media_info.media_type == 1: # Photo
                                    dl_path = cl.photo_download(media_pk, "/tmp")
                                    if dl_path:
                                        cl.photo_upload_to_story(dl_path)
                                        logging.info("Story Repost (Photo) successful")
                                else:
                                    # Video or Reel
                                    dl_path = cl.video_download(media_pk, "/tmp")
                                    if dl_path:
                                        cl.video_upload_to_story(dl_path)
                                        logging.info("Story Repost (Video) successful")
                            except Exception as re_e:
                                success = False
                                error_msg = f"Story Repost failed: {re_e}"
                            finally:
                                # Clean up downloaded file
                                if dl_path and os.path.exists(dl_path):
                                    os.remove(dl_path)
                            
                except FeedbackRequired:
                    error_msg = "ACTION_BLOCKED"
                    cursor.execute("UPDATE ig_accounts SET status = 3 WHERE id = %s", (acc['id'],))
                except PleaseWaitFewMinutes:
                    error_msg = "RATE_LIMITED"
                except Exception as e:
                    error_msg = str(e)
                    
                # Post-action updates
                if success:
                    logging.info(f"Task {task_id} SUCCESSFUL.")
                    cursor.execute("UPDATE automation_tasks SET status = 2, error_message = NULL, changed = NOW() WHERE id = %s", (task_id,))
                    cursor.execute("UPDATE ig_accounts SET last_action_at = NOW() WHERE id = %s", (acc['id'],))
                    
                    # Recalculate remains
                    cursor.execute("SELECT quantity FROM orders WHERE id = %s", (order_id,))
                    order_row = cursor.fetchone()
                    if order_row:
                        qty = int(order_row['quantity'])
                        cursor.execute("SELECT COUNT(*) as success_count FROM automation_tasks WHERE order_id = %s AND status = 2", (order_id,))
                        success_count = int(cursor.fetchone()['success_count'])
                        new_remains = max(0, qty - success_count)
                        cursor.execute("UPDATE orders SET remains = %s WHERE id = %s", (new_remains, order_id))
                        if new_remains == 0:
                            cursor.execute("UPDATE orders SET status = 'completed' WHERE id = %s", (order_id,))
                            
                    # Delay logic
                    delivery_speed = 'steady'
                    push_strategy = '12h_momentum'
                    try:
                        if task.get('data'):
                            td = json.loads(task['data'])
                            delivery_speed = td.get('delivery_speed', 'steady')
                            push_strategy = td.get('push_strategy', '12h_momentum')
                    except: pass
                    
                    if action == 'follow':
                        if delivery_speed == 'organic':
                            delay_seconds = random.randint(180, 300)
                        else:
                            delay_seconds = random.randint(60, 120)
                    elif action == 'comment':
                        if delivery_speed == 'organic':
                            delay_seconds = random.randint(90, 180)
                        else:
                            delay_seconds = random.randint(45, 90)
                    elif action == 'viral_multiplier':
                        if push_strategy == '24h_organic':
                            delay_seconds = random.randint(100, 150)
                        else:
                            delay_seconds = random.randint(45, 75)
                    else:
                        if delivery_speed == 'organic':
                            delay_seconds = random.randint(60, 120)
                        else:
                            delay_seconds = random.randint(15, 30)
                            
                    cursor.execute("UPDATE automation_tasks SET run_after = NOW() + INTERVAL %s SECOND WHERE order_id = %s AND status = 0 AND (run_after IS NULL OR run_after < NOW() + INTERVAL %s SECOND)", (delay_seconds, order_id, delay_seconds))
                
                else:
                    retries = task.get('retries', 0)
                    if retries < 3:
                        logging.info(f"Task {task_id} failed. Retrying... Error: {error_msg}")
                        cursor.execute("UPDATE automation_tasks SET status = 0, retries = %s, run_after = NOW() + INTERVAL 5 MINUTE, error_message = %s, changed = NOW() WHERE id = %s", (retries + 1, error_msg, task_id))
                    else:
                        logging.error(f"Task {task_id} failed permanently. Error: {error_msg}")
                        cursor.execute("UPDATE automation_tasks SET status = 3, error_message = %s, changed = NOW() WHERE id = %s", (error_msg, task_id))
                        
                db.commit()
                
            else:
                # No tasks found
                time.sleep(10)
                
        except Exception as e:
            logging.error(f"Worker Loop Error: {e}")
            time.sleep(10)
        finally:
            cursor.close()
            db.close()

if __name__ == "__main__":
    main()
