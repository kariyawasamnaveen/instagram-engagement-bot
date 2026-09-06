import os
import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'), timeout=10)
    
    query = """
    mysql -u root -pRoot@123 smm_db -e "
    SELECT '--- TASK STATUS (automation_tasks) ---' as '';
    SELECT 
        CASE 
            WHEN status = 0 THEN 'Pending (0)'
            WHEN status = 1 THEN 'Processing (1)'
            WHEN status = 2 THEN 'Completed (2)'
            WHEN status = 3 THEN 'Failed (3)'
            ELSE CONCAT('Other: ', status)
        END as Task_Status,
        COUNT(*) as Count
    FROM automation_tasks 
    GROUP BY status;
    
    SELECT '--- ORDER STATUS (orders) ---' as '';
    SELECT id, link, quantity, remains, status 
    FROM orders 
    WHERE status IN ('pending', 'processing', 'inprogress') OR remains > 0
    ORDER BY id DESC LIMIT 10;
    "
    """
    
    stdin, stdout, stderr = client.exec_command(query, timeout=10)
    
    out = stdout.read().decode()
    
    print(out)
        
finally:
    client.close()
