import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(
            proxy={"server": "http://151.82.162.239:1834", "username": "dts", "password": "dts"},
            locale="en-US"
        )
        page = await context.new_page()
        try:
            await page.goto("https://www.instagram.com/dinesha_sewmini01/")
            await page.wait_for_timeout(5000)
            await page.screenshot(path="ig_screenshot.png")
            print("Screenshot saved to ig_screenshot.png")
        except Exception as e:
            print("Error:", e)
        await browser.close()

asyncio.run(run())
