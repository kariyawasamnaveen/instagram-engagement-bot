# Automation Runner Setup

This is the temporary live-server worker setup for the current project state.

Use this runner when the normal `/cron/run` web route is not processing automation tasks correctly.

## What It Does

- Reads pending rows from `automation_tasks`
- Picks one active Instagram account from `ig_accounts`
- Updates task status, order status, and `remains`
- Writes results into `automation_logs`
- Supports `dry_run=1` for safe validation

## Current Verified Command

Dry-run command:

```bash
php /var/www/html/automation_runner.php dry_run=1
```

Verified live result on the server:

- Order `386680` moved to `completed`
- `remains` became `0`
- Task `2` moved to status `2`
- `error_message` stored `DRY RUN: Simulated like successfully`

## Temporary Manual Test Flow

1. Create a small internal order from the panel.
2. Run:

```bash
php /var/www/html/automation_runner.php dry_run=1
```

3. Check the order:
   - `completed` for instant single-task dry-run success
   - `processing` if future scheduled tasks still remain

## Recommended Temporary Server Cron

Until the broken web cron route is fixed, use server cron with CLI:

```bash
* * * * * /usr/bin/php /var/www/html/automation_runner.php dry_run=1 >> /var/log/automation_runner.log 2>&1
```

Use this only for dry-run validation.

## Important Limit

- The standalone runner is currently verified for dry-run simulation.
- The original web cron route still needs separate repair.
- Real live Instagram execution should not rely on the broken `/cron/run` route in the current server state.
