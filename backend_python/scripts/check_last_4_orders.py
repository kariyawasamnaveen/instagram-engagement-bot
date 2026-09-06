import os
import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'), timeout=10)
    
    query = """
    echo "=== ACCOUNT STATUS COUNTS ==="
    mysql -u root -pRoot@123 smm_db -e "SELECT status, COUNT(*) FROM ig_accounts GROUP BY status;"
    echo "=== ACTIVE ACCOUNTS DETAILS ==="
    mysql -u root -pRoot@123 smm_db -e "SELECT id, username, status, last_action_at, cooldown_until FROM ig_accounts WHERE status = 1 LIMIT 10;"
    """
    
    stdin, stdout, stderr = client.exec_command(query, timeout=10)
    
    out = stdout.read().decode()
    err = stderr.read().decode()
    
    print("--- LAST 4 ORDERS ---")
    print(out)
    if err:
        print("ERRORS:", err)
        
finally:
    client.close()
