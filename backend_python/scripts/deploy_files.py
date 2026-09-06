import paramiko
import os

files_to_upload = [
    {
        'local': '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/api_worker_server.py',
        'remote': '/var/www/html/app/modules/cron/controllers/python_worker/api_worker_server.py'
    },
    {
        'local': '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/worker_server.py',
        'remote': '/var/www/html/app/modules/cron/controllers/python_worker/worker_server.py'
    },
    {
        'local': '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/checkpoint_solver.py',
        'remote': '/var/www/html/app/modules/cron/controllers/python_worker/account_recovery.py'
    }
]

transport = paramiko.Transport((os.getenv('SERVER_IP', '127.0.0.1'), 22))
transport.connect(username='root', password=os.getenv('DB_PASS', 'secret'))
sftp = paramiko.SFTPClient.from_transport(transport)

for f in files_to_upload:
    print(f"Uploading {f['local']} to {f['remote']}...")
    sftp.put(f['local'], f['remote'])

sftp.close()
transport.close()
print("All uploads complete.")
