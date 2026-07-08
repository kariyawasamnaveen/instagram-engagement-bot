import paramiko
import sys

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    print("Connecting via SSH...")
    client.connect('YOUR_SERVER_IP', username='root', password='YOUR_DB_PASSWORD', timeout=10)
    print("Connected. Running query...")
    
    query = """
    mysql -u root -pRoot@123 smm_db -e "
    SELECT '--- BEFORE UPDATE: Tasks by Status ---' as '';
    SELECT status, COUNT(*) as count FROM automation_tasks GROUP BY status;
    
    UPDATE automation_tasks t 
    JOIN orders o ON t.order_id = o.id 
    SET t.status = 0, t.retries = 0, t.error_message = NULL, t.run_after = NOW()
    WHERE t.status = 3 AND o.status IN ('processing', 'pending', 'inprogress', 'partial');
    
    SELECT '--- AFTER UPDATE: Tasks by Status ---' as '';
    SELECT status, COUNT(*) as count FROM automation_tasks GROUP BY status;
    "
    """
    
    stdin, stdout, stderr = client.exec_command(query, timeout=15)
    
    out = stdout.read().decode()
    err = stderr.read().decode()
    
    if out:
        print("Output:")
        print(out)
    if err:
        print("Errors:")
        print(err)
        
finally:
    client.close()
