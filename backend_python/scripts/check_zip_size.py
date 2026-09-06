import os
import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'))

stdin, stdout, stderr = client.exec_command('ls -lh /var/www/html/app/modules/cron/controllers/python_worker_backup.zip')
print(stdout.read().decode('utf-8'))
client.close()
