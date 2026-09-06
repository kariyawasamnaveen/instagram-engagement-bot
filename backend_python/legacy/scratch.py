import os
import re

with open('/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/remote_worker.py', 'r') as f:
    content = f.read()

# 1. Main function to pass p instead of browser
content = content.replace('async def worker_loop(browser):', 'async def worker_loop(p):')
content = content.replace('await worker_loop(browser)', 'await worker_loop(p)')
content = content.replace('async def perform_action(browser, acc, task, cursor, db):', 'async def perform_action(p, acc, task, cursor, db):')
content = content.replace('perform_action(browser, acc, task, cursor, db)', 'perform_action(p, acc, task, cursor, db)')

content = content.replace('async def check_subscription(browser, username):', 'async def check_subscription(p, username):')
content = content.replace('await check_subscription(browser, args.check_subscription)', 'await check_subscription(p, args.check_subscription)')

# 2. Modify perform_action to use launch_persistent_context
old_context_block = """    context = await browser.new_context(**context_args)
    try:
        if acc.get('cookies'):
            await context.add_cookies(json.loads(acc['cookies']))"""

new_context_block = """    profile_dir = os.path.join(APP_DIR, 'app', 'modules', 'cron', 'controllers', 'python_worker', 'profiles', acc['username'])
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
        args=['--no-sandbox', '--disable-dev-shm-usage', '--disable-blink-features=AutomationControlled'],
        **context_args
    )
    try:
        # For persistent contexts, cookies are usually saved, but we can sync them just in case
        if acc.get('cookies'):
            try:
                await context.add_cookies(json.loads(acc['cookies']))
            except:
                pass"""
content = content.replace(old_context_block, new_context_block)

# 3. Modify check_subscription to use launch_persistent_context
old_check_sub_context = """        context = await browser.new_context(
            proxy=PROXY_SETTINGS,
            user_agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"
        )
        page = await context.new_page()"""
new_check_sub_context = """        profile_dir = os.path.join(APP_DIR, 'app', 'modules', 'cron', 'controllers', 'python_worker', 'profiles', acc['username'])
        if not os.path.exists(profile_dir):
            os.makedirs(profile_dir)
        context = await p.chromium.launch_persistent_context(
            user_data_dir=profile_dir,
            headless=True,
            proxy=PROXY_SETTINGS,
            user_agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"
        )
        pages = context.pages
        page = pages[0] if len(pages) > 0 else await context.new_page()"""
content = content.replace(old_check_sub_context, new_check_sub_context)

# 4. Modify perform_action page fetching for persistent context
content = content.replace('page = await context.new_page()', 'pages = context.pages\n        page = pages[0] if len(pages) > 0 else await context.new_page()')

# 5. Add Ghost Activity Function
ghost_activity_code = """
async def perform_ghost_activity(p, acc, cursor, db):
    debug_log(f"Starting Ghost Activity for {acc['username']}")
    ua = get_user_agent(acc, cursor)
    db.commit()
    is_mobile = "Mobile" in ua or "iPhone" in ua or "Android" in ua
    context_args = {"proxy": PROXY_SETTINGS, "user_agent": ua, "locale": "en-US"}
    if is_mobile:
        context_args["viewport"] = {"width": 390, "height": 844}
        context_args["is_mobile"] = True
        context_args["has_touch"] = True

    profile_dir = os.path.join(APP_DIR, 'app', 'modules', 'cron', 'controllers', 'python_worker', 'profiles', acc['username'])
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
            args=['--no-sandbox', '--disable-dev-shm-usage', '--disable-blink-features=AutomationControlled'],
            **context_args
        )
        if acc.get('cookies'):
            try: await context.add_cookies(json.loads(acc['cookies']))
            except: pass
        pages = context.pages
        page = pages[0] if len(pages) > 0 else await context.new_page()
        await page.route("**/*", lambda route: route.abort() if route.request.resource_type in ["image", "media", "font"] else route.continue_())
        await Stealth().apply_stealth_async(page)
        
        await page.goto("https://www.instagram.com/", wait_until="domcontentloaded", timeout=60000)
        await asyncio.sleep(random.uniform(5, 10))
        await handle_popups(page)
        
        for _ in range(random.randint(3, 7)):
            await human_scroll(page, random.randint(300, 800))
        
        if random.random() > 0.5:
            explore = page.locator('a[href="/explore/"]').first
            if await explore.is_visible(timeout=3000):
                await explore.click(timeout=5000, force=True)
                await asyncio.sleep(random.uniform(3, 7))
                for _ in range(random.randint(2, 5)):
                    await human_scroll(page, random.randint(300, 600))
                    
        await context.close()
        debug_log(f"Ghost Activity completed for {acc['username']}")
    except Exception as e:
        debug_log(f"Ghost Activity error for {acc['username']}: {e}")
        try: await context.close()
        except: pass

"""
content = content.replace('async def handle_popups(page):', ghost_activity_code + 'async def handle_popups(page):')

# 6. Idle task loop handling (call ghost activity occasionally)
idle_loop_code = """
                else:
                    cursor.execute("UPDATE automation_tasks SET status = 0, run_after = NOW() + INTERVAL 10 MINUTE, error_message = 'No accounts available', changed = NOW() WHERE id = %s", (task['id'],))
                    db.commit()
            else:
                # No tasks found. 10% chance to do ghost activity
                if random.random() < 0.1:
                    cursor.execute("SELECT * FROM ig_accounts WHERE status = 1 ORDER BY RAND() LIMIT 1")
                    ghost_acc = cursor.fetchone()
                    if ghost_acc:
                        await asyncio.wait_for(perform_ghost_activity(p, ghost_acc, cursor, db), timeout=300)
"""
content = content.replace("""
                else:
                    cursor.execute("UPDATE automation_tasks SET status = 0, run_after = NOW() + INTERVAL 10 MINUTE, error_message = 'No accounts available', changed = NOW() WHERE id = %s", (task['id'],))
                    db.commit()
            
            cursor.close()""", idle_loop_code + "            cursor.close()")

# 7. Human Typing Dynamics
human_type_code = """
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
"""
content = content.replace('async def human_scroll(page, distance):', human_type_code + 'async def human_scroll(page, distance):')
content = content.replace('await comment_input.type(comment_text, delay=random.uniform(50, 150))', 'await human_type(comment_input, comment_text)')

# 8. Bezier Curve / Touch Simulation for human_scroll
new_human_scroll = """
async def human_scroll(page, distance):
    steps = random.randint(5, 12)
    step_distance = distance / steps
    
    # Simulate touch drag instead of wheel if possible, or bezier wheel
    for _ in range(steps):
        # Adding slight horizontal variation
        await page.mouse.wheel(random.randint(-10, 10), step_distance + random.randint(-15, 30))
        await asyncio.sleep(random.uniform(0.05, 0.2))
    await asyncio.sleep(random.uniform(1.0, 2.5))
"""
content = re.sub(r'async def human_scroll.*?await asyncio.sleep\(random.uniform\(1\.0, 2\.5\)\)', new_human_scroll.strip(), content, flags=re.DOTALL)


# 9. Chronobiology (Sleep Mode) & Progressive Warm-up
chronobiology_code = """
    # Progressive Warm-up Limits
    days_alive = 30 # default
    if acc.get('created'):
        days_alive = (today - acc['created'].date()).days
    
    # Max 40, Min 5, increases by 5 per day
    dynamic_limit = min(40, max(5, days_alive * 5))
        
    if daily_actions >= dynamic_limit:
        return False, "DAILY_LIMIT_REACHED"
        
    # Chronobiology Sleep Hours (11 PM to 7 AM local time)
    fingerprint = acc.get('device_fingerprint')
    if fingerprint:
        if isinstance(fingerprint, str): fingerprint = json.loads(fingerprint)
        tz = fingerprint.get("timezone", "America/New_York")
        import pytz
        try:
            local_time = datetime.datetime.now(pytz.timezone(tz))
            if local_time.hour >= 23 or local_time.hour < 7:
                debug_log(f"Account {acc['username']} is sleeping ({local_time.hour}:00 in {tz})")
                return False, "SLEEP_MODE_ACTIVE"
        except: pass
"""
content = content.replace("""
    if daily_actions >= 40:
        return False, "DAILY_LIMIT_REACHED"
""", chronobiology_code)

# 10. Action Ratio (Like before follow)
action_ratio_code = """
        if action == 'follow':
            # ACTION RATIO: 50% chance to like 1-2 home feed posts before following
            if random.random() > 0.5:
                debug_log("Action Ratio: Liking home feed posts before follow.")
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
                await page.goto(target, wait_until="domcontentloaded", timeout=60000)
                await asyncio.sleep(10)
"""
content = content.replace("        if action == 'follow':", action_ratio_code)

# Import pytz at top
if 'import pytz' not in content:
    content = content.replace('import json\n', 'import json\nimport pytz\n')

with open('/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/remote_worker_updated.py', 'w') as f:
    f.write(content)
print("Updated file written to remote_worker_updated.py")
