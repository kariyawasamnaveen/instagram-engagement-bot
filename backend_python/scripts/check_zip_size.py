import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('YOUR_SERVER_IP', username='root', password='YOUR_DB_PASSWORD')

stdin, stdout, stderr = client.exec_command('ls -lh /var/www/html/app/modules/cron/controllers/python_worker_backup.zip')
print(stdout.read().decode('utf-8'))
client.close()
