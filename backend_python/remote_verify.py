import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('YOUR_SERVER_IP', username='root', password='YOUR_DB_PASSWORD')

def run_cmd(cmd):
    print(f"--- Running: {cmd} ---")
    stdin, stdout, stderr = client.exec_command(cmd)
    out = stdout.read().decode('utf-8')
    err = stderr.read().decode('utf-8')
    if out: print(out.strip())
    if err: print("ERR:", err.strip())

print("\n--- 1. Checking Remote Code (First 20 lines) ---")
run_cmd("head -n 20 /var/www/html/app/modules/cron/controllers/python_worker/api_worker_server.py")

print("\n--- 2. Checking PM2 Status ---")
run_cmd("pm2 info api_worker_server | grep -E 'status|uptime|restarts'")

print("\n--- 3. Checking Recent PM2 Logs ---")
run_cmd("pm2 logs api_worker_server --lines 30 --nostream")

client.close()
