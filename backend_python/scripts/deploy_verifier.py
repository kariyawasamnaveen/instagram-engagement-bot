import paramiko
import os

files_to_upload = [
    {
        'local': '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/account_verifier.py',
        'remote': '/var/www/html/app/modules/cron/controllers/python_worker/account_verifier.py'
    },
    {
        'local': '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/goodAAAL.txt',
        'remote': '/var/www/html/app/modules/cron/controllers/python_worker/goodAAAL.txt'
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
