import os

filepath = '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/remote_worker_updated.py'
with open(filepath, 'r') as f:
    content = f.read()

old_follow = """                follow_btn = page.locator('button:has-text("Follow")').first"""
new_follow = """                follow_btn = page.locator('button:has-text("Follow"), div[role="button"]:has-text("Follow")').first"""

if old_follow in content:
    content = content.replace(old_follow, new_follow)
    with open(filepath, 'w') as f:
        f.write(content)
    print("Follow button fixed locally!")
else:
    print("Follow button NOT found in local file.")
