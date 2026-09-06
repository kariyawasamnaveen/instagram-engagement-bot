import os

filepath = '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/remote_worker_updated.py'
with open(filepath, 'r') as f:
    content = f.read()

# 1. Fix cookie loading
old_cookie = """        if not storage_state:
            return False, "COOKIES_EXPIRED" """

new_cookie = """        if not storage_state and acc.get('cookies'):
            try:
                import json
                db_cookies = json.loads(acc['cookies'])
                if isinstance(db_cookies, list):
                    storage_state = {"cookies": db_cookies, "origins": []}
            except:
                pass

        if not storage_state:
            return False, "COOKIES_EXPIRED" """

if old_cookie in content:
    content = content.replace(old_cookie, new_cookie)

# 2. Add repost and share logic
old_actions = """        elif action == 'comment':
            await human_scroll(page, 200)
            await handle_popups(page)"""

new_actions = """        elif action in ['repost', 'share']:
            await human_scroll(page, 200)
            await handle_popups(page)
            
            # Click the Share (Paper Airplane) button
            share_btn = page.locator('svg[aria-label="Share Post"], svg[aria-label="Share"]').first
            if await share_btn.is_visible(timeout=5000):
                await share_btn.click()
                await asyncio.sleep(random.uniform(2.0, 3.0))
                
                # Click "Copy link"
                copy_link_btn = page.locator('div[role="button"]:has-text("Copy link"), div[role="button"]:has-text("Copy Link")').first
                if await copy_link_btn.is_visible(timeout=5000):
                    await copy_link_btn.click()
                    debug_log(f"SUCCESS: {action.capitalize()} placed successfully for target: {target} (Copied link)")
                    await context.close()
                    return True, f"{action.capitalize()}ed successfully"
                else:
                    return False, "Copy link button not found in Share menu"
            else:
                return False, "Share button not found"

        elif action == 'comment':
            await human_scroll(page, 200)
            await handle_popups(page)"""

if old_actions in content:
    content = content.replace(old_actions, new_actions)

with open(filepath, 'w') as f:
    f.write(content)

print("Modified locally!")
