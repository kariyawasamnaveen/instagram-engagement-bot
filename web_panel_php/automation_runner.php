<?php
declare(strict_types=1);

require_once __DIR__ . '/app/config.php';

date_default_timezone_set(TIMEZONE);

function runner_log(string $message): void
{
    @file_put_contents('/tmp/automation_runner.log', date('c') . ' ' . $message . PHP_EOL, FILE_APPEND);
}

function runner_output(string $message): void
{
    echo $message . PHP_EOL;
    runner_log($message);
}

function parse_db_host(string $dbHost): array
{
    $parts = explode(':', $dbHost, 2);
    $host = $parts[0];
    $port = isset($parts[1]) ? (int) $parts[1] : 3306;
    return [$host, $port];
}

function request_value(string $key, $default = null)
{
    if (PHP_SAPI === 'cli') {
        global $argv;
        foreach (($argv ?? []) as $arg) {
            if (strpos($arg, $key . '=') === 0) {
                return substr($arg, strlen($key) + 1);
            }
            if ($arg === '--' . str_replace('_', '-', $key)) {
                return '1';
            }
        }
    }

    return $_GET[$key] ?? $default;
}

function is_dry_run(): bool
{
    return (string) request_value('dry_run', '0') === '1';
}

function is_valid_instagram_target(string $action, string $target): bool
{
    if (!filter_var($target, FILTER_VALIDATE_URL)) {
        return false;
    }

    $host = strtolower((string) parse_url($target, PHP_URL_HOST));
    $path = (string) parse_url($target, PHP_URL_PATH);

    if (strpos($host, 'instagram.com') === false) {
        return false;
    }

    if ($action === 'follow') {
        return (bool) preg_match('~^/[^/]+/?$~', $path) && !preg_match('~^/(p|reel|tv)/~', $path);
    }

    return (bool) preg_match('~^/(p|reel|tv)/[^/]+/?$~', $path);
}

function refresh_order_status(mysqli $db, int $orderId): void
{
    $counts = [
        0 => 0,
        1 => 0,
        2 => 0,
        3 => 0,
    ];

    $sql = "SELECT status, COUNT(id) AS total FROM automation_tasks WHERE order_id = {$orderId} GROUP BY status";
    $result = $db->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $counts[(int) $row['status']] = (int) $row['total'];
        }
        $result->free();
    }

    if (($counts[0] + $counts[1]) > 0) {
        $status = 'processing';
    } elseif ($counts[2] > 0 && $counts[3] > 0) {
        $status = 'partial';
    } elseif ($counts[2] > 0) {
        $status = 'completed';
    } elseif ($counts[3] > 0) {
        $status = 'fail';
    } else {
        $status = 'awaiting';
    }

    $stmt = $db->prepare("UPDATE orders SET status = ?, changed = NOW() WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('si', $status, $orderId);
        $stmt->execute();
        $stmt->close();
    }
}

function find_available_account(mysqli $db): ?array
{
    $sql = "SELECT * FROM ig_accounts
            WHERE status = 1
              AND (last_action_at IS NULL OR last_action_at <= DATE_SUB(NOW(), INTERVAL 30 MINUTE))
            ORDER BY last_action_at IS NULL DESC, last_action_at ASC, id ASC
            LIMIT 1";
    $result = $db->query($sql);
    if (!$result) {
        return null;
    }

    $row = $result->fetch_assoc();
    $result->free();
    return $row ?: null;
}

if (PHP_SAPI !== 'cli') {
    $key = (string) request_value('key', '');
    $expected = substr(ENCRYPTION_KEY, 0, 10);
    if ($key !== $expected) {
        http_response_code(403);
        exit('Invalid token');
    }
}

[$dbHost, $dbPort] = parse_db_host(DB_HOST);
$db = @new mysqli($dbHost, DB_USER, DB_PASS, DB_NAME, $dbPort);
if ($db->connect_errno) {
    runner_output('DB connection failed: ' . $db->connect_error);
    exit(1);
}

$dryRun = is_dry_run();
$limit = max(1, (int) request_value('limit', 5));

runner_output($dryRun ? 'Automation runner started in DRY RUN mode.' : 'Automation runner started.');

$taskSql = "SELECT * FROM automation_tasks
            WHERE status = 0
              AND (run_after IS NULL OR run_after <= NOW())
            ORDER BY id ASC
            LIMIT {$limit}";
$taskResult = $db->query($taskSql);
if (!$taskResult) {
    runner_output('Task query failed: ' . $db->error);
    exit(1);
}

$tasks = [];
while ($row = $taskResult->fetch_assoc()) {
    $tasks[] = $row;
}
$taskResult->free();

if (!$tasks) {
    runner_output($dryRun ? 'No pending dry-run automation tasks.' : 'No pending automation tasks.');
    exit(0);
}

foreach ($tasks as $task) {
    $taskId = (int) $task['id'];
    $orderId = (int) $task['order_id'];
    $action = (string) $task['action'];
    $target = (string) $task['target'];

    $account = find_available_account($db);
    if (!$account) {
        $stmt = $db->prepare("UPDATE automation_tasks SET error_message = ?, changed = NOW() WHERE id = ?");
        if ($stmt) {
            $message = 'No accounts available';
            $stmt->bind_param('si', $message, $taskId);
            $stmt->execute();
            $stmt->close();
        }
        runner_output("Task {$taskId}: no accounts available.");
        continue;
    }

    $accountId = (int) $account['id'];

    $stmt = $db->prepare("UPDATE orders SET status = 'processing', changed = NOW() WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $db->prepare("UPDATE automation_tasks SET status = 1, account_id = ?, changed = NOW() WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('ii', $accountId, $taskId);
        $stmt->execute();
        $stmt->close();
    }

    if (!$dryRun) {
        $message = 'Live automation is not enabled in standalone runner';
        $stmt = $db->prepare("UPDATE automation_tasks SET status = 3, error_message = ?, changed = NOW() WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('si', $message, $taskId);
            $stmt->execute();
            $stmt->close();
        }
        refresh_order_status($db, $orderId);
        runner_output("Task {$taskId}: live mode not enabled.");
        continue;
    }

    $isValidTarget = is_valid_instagram_target($action, $target);
    $success = $isValidTarget;
    $message = $success
        ? 'DRY RUN: Simulated ' . $action . ' successfully'
        : 'DRY RUN: Invalid Instagram target for ' . $action;

    $taskStatus = $success ? 2 : 3;

    $stmt = $db->prepare("UPDATE automation_tasks SET status = ?, error_message = ?, account_id = ?, changed = NOW() WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('isii', $taskStatus, $message, $accountId, $taskId);
        $stmt->execute();
        $stmt->close();
    }

    if ($success) {
        $stmt = $db->prepare("UPDATE orders SET remains = GREATEST(remains - 1, 0), changed = NOW() WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $stmt->close();
        }
    }

    $response = json_encode([
        'status' => $success,
        'message' => $message,
        'dry_run' => true,
        'task_id' => $taskId,
        'target' => $target,
        'action' => $action,
    ]);

    $stmt = $db->prepare("INSERT INTO automation_logs (task_id, account_id, proxy_id, action, result, response, created) VALUES (?, ?, 0, ?, ?, ?, NOW())");
    if ($stmt) {
        $resultValue = $success ? '1' : '0';
        $stmt->bind_param('iisss', $taskId, $accountId, $action, $resultValue, $response);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $db->prepare("UPDATE ig_accounts SET last_action_at = NOW(), status_message = ?, updated = NOW() WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('si', $message, $accountId);
        $stmt->execute();
        $stmt->close();
    }

    refresh_order_status($db, $orderId);
    runner_output("Task {$taskId}: {$message}");
}

runner_output($dryRun ? 'Dry-run automation processing finished.' : 'Automation processing finished.');
