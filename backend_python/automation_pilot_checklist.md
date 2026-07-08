# Automation Pilot Checklist

## Pre-flight

- Confirm cron is pointing to `/cron/run?key=YOUR_CRON_KEY`.
- Run one manual dry-run first with `/cron/run?key=YOUR_CRON_KEY&dry_run=1`.
- If the web cron route stays blank or does not process tasks, use the CLI fallback runner:
  `php /var/www/html/automation_runner.php dry_run=1`
- Keep only `1-2` clean test Instagram accounts active for the pilot.
- Use very small quantities only: `1-3` actions per order.
- Keep one internal like service and one internal follow service active.
- For follow automation, ensure the service name, description, or tags include `follow`, `followers`, or `action:follow`.

## Test Order 1: Like

- Service: internal like service
- Link: Instagram post, reel, or video URL
- Quantity: `1`
- Delivery speed: `instant`
- Expected:
  - dry-run can mark the task as simulated success without calling Instagram
  - order is created successfully
  - one automation task is created
  - task moves from pending to success or fail
  - order status does not jump to `completed` unless the task succeeds

## Test Order 2: Follow

- Service: internal follow service
- Link: Instagram profile URL
- Quantity: `1`
- Delivery speed: `instant`
- Expected:
  - order is created successfully
  - one follow automation task is created
  - target resolves as a user, not a post
  - order status reflects the real task result

## Test Order 3: Organic Speed

- Service: internal like or follow service
- Quantity: `3`
- Delivery speed: `organic`
- Expected:
  - multiple tasks are created in batches
  - later tasks contain a future `run_after` value
  - order remains decrease only after successful tasks

## Stop Conditions

- `login_required`
- repeated `302` or `404`
- account status becomes blocked/checkpointed
- order marked `completed` while tasks failed

## Pass Criteria

- like and follow orders can both be created from the normal order form
- task status, order status, and remains stay in sync
- no false `completed` states appear during the pilot
