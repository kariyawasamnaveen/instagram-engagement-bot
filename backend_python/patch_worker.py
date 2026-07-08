import os

filepath = '/var/www/html/app/modules/cron/controllers/python_worker/worker_server.py'
with open(filepath, 'r') as f:
    content = f.read()

old_code = """            like_btn = page.locator('svg[aria-label="Like"], svg[aria-label="Mi piace"], svg[aria-label="Me gusta"], svg[aria-label="Curtir"]').first
            if await like_btn.is_visible(timeout=5000):
                await like_btn.click(timeout=15000, force=True)"""

new_code = """            like_btn = page.locator('svg[aria-label="Like"], svg[aria-label="Mi piace"], svg[aria-label="Me gusta"], svg[aria-label="Curtir"]').first
            if await like_btn.is_visible(timeout=5000):
                await like_btn.click(timeout=15000, force=True)
            else:
                try:
                    video = page.locator('video').first
                    if await video.is_visible(timeout=2000):
                        await video.dblclick(timeout=5000)
                        debug_log("Fallback: Double clicked video to like!")
                    else:
                        role_btn = page.get_by_role("button", name=re.compile("Like|Mi piace|Me gusta|Curtir", re.IGNORECASE)).first
                        if await role_btn.is_visible(timeout=2000):
                            await role_btn.click(timeout=5000, force=True)
                            debug_log("Fallback: Clicked role button to like!")
                except Exception as e:
                    debug_log(f"Fallback like error: {e}")"""

if old_code in content:
    content = content.replace(old_code, new_code)
    with open(filepath, 'w') as f:
        f.write(content)
    print("Patched successfully!")
else:
    print("Could not find code to replace.")
