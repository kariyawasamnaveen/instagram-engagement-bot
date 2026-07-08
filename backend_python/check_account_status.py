import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    client.connect('YOUR_SERVER_IP', username='root', password='YOUR_DB_PASSWORD', timeout=10)
    
    query = """
    mysql -u root -pRoot@123 smm_db -e "
    SELECT 
        CASE 
            WHEN status = 1 THEN 'Active / Working (1)'
            WHEN status = 3 THEN 'Hard Blocked / Checkpoint (3)'
            WHEN status = 4 THEN 'Suspended / Disabled (4)'
            WHEN status = 5 THEN 'Soft Blocked / Resting 48h (5)'
            ELSE CONCAT('Other Status: ', status)
        END as Account_Status,
        COUNT(*) as Total_Accounts
    FROM ig_accounts 
    GROUP BY status;
    "
    """
    
    stdin, stdout, stderr = client.exec_command(query, timeout=10)
    
    out = stdout.read().decode()
    
    print("--- Current Account Status ---")
    print(out)
        
finally:
    client.close()
