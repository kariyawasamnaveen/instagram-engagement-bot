import re

def refactor():
    with open('remote_worker_playwright.py', 'r') as f:
        code = f.read()

    # 1. Signature
    code = code.replace(
        "async def perform_action(p, acc, task, cursor, db):",
        "async def perform_action(p, acc, bundle_tasks, cursor, db):\n    task = bundle_tasks[0]"
    )

    # 2. Target extraction
    code = code.replace(
        "action = task['action']\n        target = task['target']",
        "target = bundle_tasks[0]['target']\n        bundle_results = []"
    )

    # We will indent everything inside the action blocks.
    parts = code.split("if action == 'follow':", 1)
    before_loop = parts[0]
    rest = "if action == 'follow':" + parts[1]
    
    # Where does the loop content end?
    after_loop_parts = rest.split('await context.close()\n        return False, "Unknown action"')
    loop_content = after_loop_parts[0]
    rest_of_func = after_loop_parts[1]
    
    # Strip any trailing whitespace/newlines from before_loop
    before_loop = before_loop.rstrip() + '\n'
    
    # 3. Process loop content
    # Replace returns
    loop_content = re.sub(r'await context\.close\(\)', 'pass # context closed later', loop_content)
    loop_content = re.sub(r'return True,\s*(.+)', r'bundle_results.append((task, True, \1)); continue', loop_content)
    loop_content = re.sub(r'return False,\s*(.+)', r'bundle_results.append((task, False, \1)); continue', loop_content)
    
    # Remove trailing newlines from loop_content
    loop_content = loop_content.rstrip() + '\n'
    
    # Indent the loop content by 4 spaces
    indented_loop_content = '\n'.join(('    ' + line) if line.strip() else line for line in loop_content.split('\n'))
    
    # Combine
    loop_header = """
        for task in bundle_tasks:
            action = task['action']
            
"""
    code = before_loop + loop_header + indented_loop_content + """
            bundle_results.append((task, False, "Unknown action"))
            
        pass # context closed later
        return bundle_results
""" + rest_of_func

    # 5. Fix worker_loop
    old_fetch = """cursor.execute("SELECT * FROM automation_tasks WHERE status = 0 AND (run_after IS NULL OR run_after <= NOW()) ORDER BY id ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
            task = cursor.fetchone()
            
            if task:
                debug_log(f"Picked up task {task['id']} for Order {task['order_id']} (Action: {task.get('action')})")
                cursor.execute("UPDATE automation_tasks SET status = 1, changed = NOW() WHERE id = %s", (task['id'],))
                cursor.execute("UPDATE orders SET status = 'processing' WHERE id = %s AND status IN ('pending', 'inprogress')", (task['order_id'],))
                db.commit()"""
                
    new_fetch = """cursor.execute("SELECT target FROM automation_tasks WHERE status = 0 AND (run_after IS NULL OR run_after <= NOW()) ORDER BY id ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
            target_row = cursor.fetchone()
            
            bundle_tasks = []
            if target_row:
                target = target_row['target']
                cursor.execute("SELECT * FROM automation_tasks WHERE status = 0 AND target = %s ORDER BY FIELD(action, 'like', 'comment', 'share', 'follow') LIMIT 3 FOR UPDATE SKIP LOCKED", (target,))
                bundle_tasks = cursor.fetchall()
                
                if bundle_tasks:
                    for bt in bundle_tasks:
                        debug_log(f"Picked up task {bt['id']} for Order {bt['order_id']} (Action: {bt.get('action')})")
                        cursor.execute("UPDATE automation_tasks SET status = 1, changed = NOW() WHERE id = %s", (bt['id'],))
                        cursor.execute("UPDATE orders SET status = 'processing' WHERE id = %s AND status IN ('pending', 'inprogress')", (bt['order_id'],))
                    db.commit()"""
    
    code = code.replace(old_fetch, new_fetch)
    
    # 6. Change `perform_action` call
    code = code.replace(
        "success, msg = await asyncio.wait_for(perform_action(p, acc, task, cursor, db), timeout=300)",
        "results = await asyncio.wait_for(perform_action(p, acc, bundle_tasks, cursor, db), timeout=300)"
    )
    
    # 7. Replace result handling
    # The original result handling
    old_result_handling = """if msg == "ACCOUNT_BLOCKED":
                            debug_log(f"ACCOUNT ERROR: Account {acc['username']} HARD BLOCKED ({msg}). Switching account.")
                            try:
                                debug_log("Triggering immediate AirProxy rotation due to Hard Block to discard toxic IP subnet!")
                                requests.get("https://airproxy.io/api/proxy/change_ip/?format=json&id=3301&key=YOUR_AIRPROXY_API_KEY", timeout=15)
                                time.sleep(45)
                            except: pass
                            # Block account permanently
                            cursor.execute("UPDATE ig_accounts SET status = 3 WHERE id = %s", (acc['id'],))
                            cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                            logging.warning(f"Account {acc['username']} HARD BLOCKED. Task reset.")
                        elif msg in ("ACTION_BLOCKED_SILENT", "ACTION_BLOCKED_API"):
                            debug_log(f"ACCOUNT ERROR: Account {acc['username']} SOFT BLOCKED ({msg}). Going into 48h Cooldown.")
                            try:
                                debug_log("Triggering immediate AirProxy rotation due to Soft Block to protect other accounts!")
                                requests.get("https://airproxy.io/api/proxy/change_ip/?format=json&id=3301&key=YOUR_AIRPROXY_API_KEY", timeout=15)
                                time.sleep(45)
                            except: pass
                            cursor.execute("UPDATE ig_accounts SET cooldown_until = NOW() + INTERVAL 48 HOUR WHERE id = %s", (acc['id'],))
                            cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                            logging.warning(f"Account {acc['username']} SOFT BLOCKED. Resting for 48h.")
                        elif msg == "SLEEP_MODE_ACTIVE":
                            cursor.execute("UPDATE automation_tasks SET status = 0, run_after = NOW() + INTERVAL 30 MINUTE, changed = NOW() WHERE id = %s", (task['id'],))
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
                        
                        db.commit()"""

    new_result_handling = """if isinstance(results, tuple):
                            success, msg = results
                            for task in bundle_tasks:
                                if msg == "ACCOUNT_BLOCKED":
                                    debug_log(f"ACCOUNT ERROR: Account {acc['username']} HARD BLOCKED ({msg}). Switching account.")
                                    try:
                                        requests.get("https://airproxy.io/api/proxy/change_ip/?format=json&id=3301&key=YOUR_AIRPROXY_API_KEY", timeout=15)
                                    except: pass
                                    cursor.execute("UPDATE ig_accounts SET status = 3 WHERE id = %s", (acc['id'],))
                                    cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                                elif msg in ("ACTION_BLOCKED_SILENT", "ACTION_BLOCKED_API"):
                                    cursor.execute("UPDATE ig_accounts SET cooldown_until = NOW() + INTERVAL 48 HOUR WHERE id = %s", (acc['id'],))
                                    cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                                elif msg == "SLEEP_MODE_ACTIVE":
                                    cursor.execute("UPDATE automation_tasks SET status = 0, run_after = NOW() + INTERVAL 30 MINUTE, changed = NOW() WHERE id = %s", (task['id'],))
                                elif msg == "COOKIES_EXPIRED":
                                    cursor.execute("UPDATE ig_accounts SET status = 3 WHERE id = %s", (acc['id'],))
                                    cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                                elif msg == "ERR_TUNNEL_CONNECTION_FAILED":
                                    cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                                elif msg == "INVALID_URL":
                                    cursor.execute("UPDATE automation_tasks SET status = 3, error_message = %s, changed = NOW() WHERE id = %s", (msg, task['id']))
                                else:
                                    retries = task.get('retries', 0)
                                    if retries < 3:
                                        cursor.execute("UPDATE automation_tasks SET status = 0, retries = %s, run_after = NOW() + INTERVAL 5 MINUTE, error_message = %s, changed = NOW() WHERE id = %s", (retries + 1, msg, task['id']))
                                    else:
                                        cursor.execute("UPDATE automation_tasks SET status = 3, error_message = %s, changed = NOW() WHERE id = %s", (msg, task['id']))
                            db.commit()
                        else:
                            for task, success, msg in results:
                                if success:
                                    debug_log(f"Task {task['id']} SUCCESSFUL. Updating Order {task['order_id']} remains.")
                                    cursor.execute("UPDATE automation_tasks SET status = 2, error_message = %s, changed = NOW() WHERE id = %s", (msg, task['id']))
                                    cursor.execute("UPDATE ig_accounts SET last_action_at = NOW(), daily_actions = daily_actions + 1 WHERE id = %s", (acc['id'],))
                                    
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
                                        delay_seconds = random.randint(180, 300) if delivery_speed == 'organic' else random.randint(60, 120)
                                    else:
                                        delay_seconds = random.randint(60, 120) if delivery_speed == 'organic' else random.randint(15, 30)
                                        
                                    cursor.execute("UPDATE automation_tasks SET run_after = NOW() + INTERVAL %s SECOND WHERE order_id = %s AND status = 0 AND (run_after IS NULL OR run_after < NOW() + INTERVAL %s SECOND)", (delay_seconds, task['order_id'], delay_seconds))
                                else:
                                    if msg == "ACCOUNT_BLOCKED":
                                        cursor.execute("UPDATE ig_accounts SET status = 3 WHERE id = %s", (acc['id'],))
                                        cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                                    elif msg in ("ACTION_BLOCKED_SILENT", "ACTION_BLOCKED_API"):
                                        cursor.execute("UPDATE ig_accounts SET cooldown_until = NOW() + INTERVAL 48 HOUR WHERE id = %s", (acc['id'],))
                                        cursor.execute("UPDATE automation_tasks SET status = 0, changed = NOW() WHERE id = %s", (task['id'],))
                                    elif msg == "INVALID_URL":
                                        cursor.execute("UPDATE automation_tasks SET status = 3, error_message = %s, changed = NOW() WHERE id = %s", (msg, task['id']))
                                    else:
                                        retries = task.get('retries', 0)
                                        if retries < 3:
                                            cursor.execute("UPDATE automation_tasks SET status = 0, retries = %s, run_after = NOW() + INTERVAL 5 MINUTE, error_message = %s, changed = NOW() WHERE id = %s", (retries + 1, msg, task['id']))
                                        else:
                                            cursor.execute("UPDATE automation_tasks SET status = 3, error_message = %s, changed = NOW() WHERE id = %s", (msg, task['id']))
                            db.commit()"""

    code = code.replace(old_result_handling, new_result_handling)
    
    # 8. Timeouts
    old_timeout = """retries = task.get('retries', 0)
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
                    cursor.execute("UPDATE automation_tasks SET status = 0, run_after = NOW() + INTERVAL 10 MINUTE, error_message = 'No accounts available', changed = NOW() WHERE id = %s", (task['id'],))"""
                    
    new_timeout = """for task in bundle_tasks:
                            retries = task.get('retries', 0)
                            if retries < 3:
                                debug_log(f"TASK ERROR: Task {task['id']} TIMEOUT. Retrying ({retries+1}/3).")
                                cursor.execute("UPDATE automation_tasks SET status = 0, retries = %s, run_after = NOW() + INTERVAL 5 MINUTE, error_message = 'Timeout', changed = NOW() WHERE id = %s", (retries + 1, task['id']))
                            else:
                                cursor.execute("UPDATE automation_tasks SET status = 3, error_message = 'Timeout', changed = NOW() WHERE id = %s", (task['id'],))
                        db.commit()
                else:
                    for task in bundle_tasks:
                        cursor.execute("UPDATE automation_tasks SET status = 0, run_after = NOW() + INTERVAL 10 MINUTE, error_message = 'No accounts available', changed = NOW() WHERE id = %s", (task['id'],))"""

    code = code.replace(old_timeout, new_timeout)
    
    with open('remote_worker_bundled.py', 'w') as f:
        f.write(code)

refactor()
