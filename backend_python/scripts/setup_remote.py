import os
import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'))

def run_cmd(cmd):
    print(f"--- Running: {cmd} ---")
    stdin, stdout, stderr = client.exec_command(cmd)
    out = stdout.read().decode('utf-8')
    err = stderr.read().decode('utf-8')
    if out: print(out)
    if err: print("ERR:", err)

# Check if passwords exist in DB
run_cmd("mysql -u root -pRoot@123 smm_db -e 'SELECT username, password FROM ig_accounts LIMIT 5;'")

# Install instagrapi
run_cmd("pip3 install instagrapi")

client.close()
