import os
import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'))

def run_cmd(cmd):
    stdin, stdout, stderr = client.exec_command(cmd)
    return stdout.read().decode('utf-8')

print("--- Total Accounts grouped by status ---")
print(run_cmd("mysql -u root -pRoot@123 smm_db -e 'SELECT status, COUNT(*) FROM ig_accounts GROUP BY status;'"))

print("--- Total Web Accounts (status=1) ---")
print(run_cmd("mysql -u root -pRoot@123 smm_db -e 'SELECT COUNT(*) FROM ig_accounts WHERE status=1 AND username NOT LIKE \"+91%\";'"))

print("--- Total API Accounts (status=1) ---")
print(run_cmd("mysql -u root -pRoot@123 smm_db -e 'SELECT COUNT(*) FROM ig_accounts WHERE status=1 AND username LIKE \"+91%\";'"))

client.close()
