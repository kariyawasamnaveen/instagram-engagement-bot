import os
import sys
import time
import random
import logging
import datetime
import mysql.connector
from instagrapi import Client
import requests

logging.basicConfig(level=logging.INFO, format='[%(asctime)s] WARMUP: %(message)s', datefmt='%Y-%m-%d %H:%M:%S')

DB_HOST = 'localhost'
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

def get_proxy():
    return "http://dts:dts@151.82.162.239:1834"

def rotate_proxy():
    try:
        logging.info("Rotating AirProxy IP for Warmup...")
        res = requests.get("https://airproxy.io/api/proxy/change_ip/?format=json&id=1834&key=YOUR_AIRPROXY_API_KEY", timeout=15)
        time.sleep(25)
    except Exception as e:
        logging.error(f"Proxy rotation failed: {e}")

def get_client(acc):
    username = acc['username']
    password = acc['password']
    session_file = f"/var/www/html/app/modules/cron/controllers/python_worker/sessions/{username}.json"
    
    cl = Client()
    cl.set_proxy(get_proxy())
    
    if os.path.exists(session_file):
        try:
            cl.load_settings(session_file)
            cl.login(username, password)
            return cl
        except:
            os.remove(session_file)
            
    try:
        cl.login(username, password)
        cl.dump_settings(session_file)
        return cl
    except Exception as e:
        logging.error(f"Login failed for {username}: {e}")
        return None

def main():
    logging.info("Auto Warm-up Engine Started...")
    while True:
        db = get_db_connection()
        if not db:
            time.sleep(60)
            continue
            
        cursor = db.cursor(dictionary=True)
        try:
            # Find an active account that hasn't been warmed up in the last 6 hours
            cursor.execute("SELECT * FROM ig_accounts WHERE status = 1 AND (last_warmup_at IS NULL OR last_warmup_at < NOW() - INTERVAL 6 HOUR) ORDER BY last_warmup_at ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
            acc = cursor.fetchone()
            
            if acc:
                logging.info(f"Warming up account: {acc['username']}")
                cursor.execute("UPDATE ig_accounts SET last_warmup_at = NOW() WHERE id = %s", (acc['id'],))
                db.commit()
                
                rotate_proxy()
                cl = get_client(acc)
                
                if cl:
                    # 1. Fetch Timeline Feed
                    logging.info(f"{acc['username']}: Fetching timeline feed...")
                    feed = cl.timeline_feed()
                    time.sleep(random.randint(5, 15))
                    
                    # 2. Like 1 or 2 random timeline posts
                    if 'feed_items' in feed:
                        items = [i for i in feed['feed_items'] if 'media_or_ad' in i and i['media_or_ad'].get('has_liked') == False]
                        if items:
                            target = random.choice(items)['media_or_ad']
                            pk = target['pk']
                            logging.info(f"{acc['username']}: Liking post {pk}")
                            cl.media_like(pk)
                            time.sleep(random.randint(10, 20))
                            
                    # 3. Fetch User Stories
                    logging.info(f"{acc['username']}: Checking stories...")
                    reels = cl.reels_tray()
                    if reels.get('tray'):
                        # View 1 random story
                        user_id = reels['tray'][0]['user']['pk']
                        cl.user_stories(user_id)
                        time.sleep(random.randint(5, 10))
                        
                    logging.info(f"{acc['username']}: Warmup completed successfully.")
                else:
                    logging.warning(f"{acc['username']}: Skipping warmup due to login failure.")
            else:
                logging.info("No accounts need warming up right now. Sleeping for 15 mins...")
                time.sleep(900)
                
        except Exception as e:
            logging.error(f"Warmup Loop Error: {e}")
            time.sleep(60)
        finally:
            cursor.close()
            db.close()
            
        time.sleep(60)

if __name__ == "__main__":
    main()
