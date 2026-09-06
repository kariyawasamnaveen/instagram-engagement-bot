with open("live_worker_patched.py", "r") as f:
    code = f.read()

old_query = """cursor.execute("SELECT * FROM automation_tasks WHERE status = 0 AND (run_after IS NULL OR run_after <= NOW()) ORDER BY id ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
                task = cursor.fetchone()
                
                if task:
                    cursor.execute("SELECT * FROM ig_accounts WHERE status = 1 ORDER BY last_action_at ASC LIMIT 1")
                    acc = cursor.fetchone()"""

new_query = """cursor.execute("SELECT * FROM automation_tasks WHERE status = 0 AND (run_after IS NULL OR run_after <= NOW()) ORDER BY id ASC LIMIT 1 FOR UPDATE SKIP LOCKED")
                task = cursor.fetchone()
                
                if task:
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
                        cursor.execute("SELECT * FROM ig_accounts WHERE status = 1 AND gender = %s ORDER BY last_action_at ASC LIMIT 1", (target_gender,))
                    else:
                        cursor.execute("SELECT * FROM ig_accounts WHERE status = 1 ORDER BY last_action_at ASC LIMIT 1")
                        
                    acc = cursor.fetchone()"""

if old_query in code:
    code = code.replace(old_query, new_query)
    with open("live_worker_patched.py", "w") as f:
        f.write(code)
    print("Patched live_worker_patched.py successfully.")
else:
    print("Could not find the target code to replace.")
