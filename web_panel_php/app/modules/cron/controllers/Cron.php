<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');
        $this->cron_token();
        $this->provider = new Smm_api();
    }

    public function index()
    {
        redirect(cn());
    }

    public function status()
    {
        $lock = fopen('_lock_file_status.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Order already running');
        $params = [
            'limit' => 15,
            'start' => 0,
        ];
        $items = $this->main_model->list_items($params, ['task' => 'list-items-status']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }
        foreach ($items as $key => $item) {
            $api = $this->main_model->get_item(['id' => $item['api_provider_id']], ['task' => 'get-item-provider']);
            if (!$api) {
                $response = ['error' => "API Provider does not exists"];
                $this->main_model->save_item(['item' => $item, 'response' => $response], ['task' => 'item-status']);
                continue;
            }
            $response = $this->provider->status($api, $item['api_order_id']);
            $this->main_model->save_item(['item' => $item, 'response' => $response], ['task' => 'item-status']);
        }
        echo "Successfully";
    }

    public function dripfeed()
    {
        $lock = fopen('_lock_file_dripfeed.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Dripfeed already running');
        $params = [
            'limit' => 15,
            'start' => 0,
        ];
        $items = $this->main_model->list_items($params, ['task' => 'list-items-dripfeed-new-order']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }
        foreach ($items as $key => $item) {
            $this->main_model->save_item_by_dripfeed(['item' => $item]);
        }
        echo "Successfully";
    }

    public function dripfeed_old()
    {
        $lock = fopen('_lock_file_dripfeed.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Dripfeed already running');
        $params = [
            'limit' => 15,
            'start' => 0,
        ];
        $items = $this->main_model->list_items($params, ['task' => 'list-items-dripfeed-status']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }
        foreach ($items as $key => $item) {
            $api = $this->main_model->get_item(['id' => $item['api_provider_id']], ['task' => 'get-item-provider']);
            if (!$api) {
                $response = ['error' => "API Provider does not exists"];
                $this->main_model->save_item(['order_id' => $item['id'], 'response' => $response], ['task' => 'item-dripfeed-status']);
                continue;
            }
            $response = $this->provider->status($api, $item['api_order_id']);
            $this->main_model->save_item(['item' => $item, 'item_api' => $api, 'response' => $response], ['task' => 'item-dripfeed-status']);
        }
        echo "Successfully";
    }

    public function subscriptions()
    {
        $lock = fopen('_lock_file_subscriptions.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Subscriptions already running');
        $params = [
            'limit' => 15,
            'start' => 0,
        ];
        $items = $this->main_model->list_items($params, ['task' => 'list-items-subscriptions-status']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }
        foreach ($items as $key => $item) {
            $api = $this->main_model->get_item(['id' => $item['api_provider_id']], ['task' => 'get-item-provider']);
            if (!$api) {
                $response = ['error' => "API Provider does not exists"];
                $this->main_model->save_item(['order_id' => $item['id'], 'response' => $response], ['task' => 'item-subscriptions-status']);
                continue;
            }
            $response = $this->provider->status($api, $item['api_order_id']);
            $this->main_model->save_item(['item' => $item, 'item_api' => $api, 'response' => $response], ['task' => 'item-subscriptions-status']);
        }
        echo "Successfully";
    }

    /**
     * MONITOR: Detect new posts for Local Subscriptions
     * Usage: /cron/local_subscriptions?key=YOUR_KEY
     */
    public function local_subscriptions()
    {
        $lock = fopen('_lock_file_local_subscriptions.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Local Subscriptions already running');

        $this->load->library('Ig_Automation_Lib');
        $this->load->model('admin/automation_tasks_model', 'automation_model');

        $active_subs = $this->main_model->get_active_local_subscriptions();
        if (!$active_subs) {
            echo "No active local subscriptions found.<br>";
            return;
        }

        foreach ($active_subs as $sub) {
            // Check for monthly expiry in real-time
            if ($sub['is_monthly'] == 1 && !empty($sub['expiry_at']) && strtotime($sub['expiry_at']) < time()) {
                echo "Subscription {$sub['id']} for {$sub['username']} has expired. Updating status...<br>";
                $this->db->update(ORDER, ['sub_status' => 'Expired', 'status' => 'completed'], ['id' => $sub['id']]);
                continue;
            }

            echo "Checking [@{$sub['username']}] for new posts...<br>";
            
            // USE PYTHON PLAYWRIGHT FOR RELIABLE DETECTION
            $python_path = APPPATH . 'modules/cron/controllers/python_worker/venv/bin/python3';
            // RUN PYTHON VIA SSH@LOCALHOST TO BYPASS WWW-DATA BROWSER LIMITS
            $script_cmd = "/var/www/html/app/modules/cron/controllers/python_worker/venv/bin/python3 /var/www/html/app/modules/cron/controllers/python_worker/worker.py --check-subscription " . escapeshellarg($sub['username']);
            $command = "sshpass -p 'YOUR_DB_PASSWORD' ssh -o StrictHostKeyChecking=no root@localhost \"{$script_cmd}\" 2>&1";
            $output = shell_exec($command);
            
            // Extract only the JSON part from potential logs
            if (preg_match('/({.*})/', $output, $matches)) {
                $latest_media = json_decode($matches[1], true);
            } else {
                $latest_media = null;
            }

            if (!$latest_media || !isset($latest_media['shortcode']) || empty($latest_media['shortcode'])) {
                echo "Failed to detect latest post for [{$sub['username']}]. Response: {$output}<br>";
                continue;
            }

            $history = json_decode($sub['sub_response_orders'], true);
            if (isset($history['media_ids']) && in_array($latest_media['shortcode'], $history['media_ids'])) {
                echo "Latest post [{$latest_media['shortcode']}] already processed. Skipping.<br>";
                continue;
            }

            echo "NEW POST DETECTED! [{$latest_media['shortcode']}]. Generating Auto-Order...<br>";

            // 1. Create Sub-Order in Orders table
            $charge_per_post = ($sub['is_monthly'] == 1) ? 0 : ($sub['charge'] / $sub['sub_posts']);
            $order_data = [
                'ids' => ids(),
                'uid' => $sub['uid'],
                'cate_id' => $sub['cate_id'],
                'service_id' => $sub['service_id'],
                'main_order_id' => $sub['id'],
                'service_type' => 'default',
                'order_source_type' => ORDER_SOURCE_SUBSCRIPTIONS,
                'link' => $latest_media['url'],
                'quantity' => rand($sub['sub_min'], $sub['sub_max']),
                'remains' => 0, // Initially 0, will be updated by loop
                'charge' => $charge_per_post,
                'status' => ORDER_STATUS_PENDING,
                'created' => NOW,
                'changed' => NOW
            ];
            $order_data['remains'] = $order_data['quantity'];
            $this->db->insert(ORDER, $order_data);
            $new_order_id = $this->db->insert_id();

            // 2. Insert into Automation Queue (Fanning out for full quantity)
            $profile_url = "https://www.instagram.com/{$sub['username']}/";
            $task_meta = [
                'shortcode' => $latest_media['shortcode'],
                'target_url' => $latest_media['url'],
                'username' => $sub['username']
            ];
            
            $delivery_speed = (!empty($sub['delivery_speed'])) ? $sub['delivery_speed'] : 'organic';
            $this->automation_model->create_tasks($new_order_id, $sub['service_id'], 'like', $profile_url, $order_data['quantity'], $task_meta, ['delivery_speed' => $delivery_speed]);

            // 3. Update Subscription History & Counter
            $this->main_model->update_subscription_post_history($sub['id'], $latest_media['media_id'], $new_order_id);
            
            echo "SUCCESS: Auto-Order {$new_order_id} created for {$sub['username']}<br>";
        }

        echo "Local subscription monitoring finished.<br>";
    }

    /**
     * RENEWAL: Handle Auto-Renewal for Monthly VIP Subscriptions
     * Usage: /cron/subscriptions_renewal?key=YOUR_KEY
     */
    public function subscriptions_renewal()
    {
        $lock = fopen('_lock_file_subscriptions_renewal.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Renewal process already running');

        // 1. Find Expiring Monthly Subscriptions
        $this->db->select("o.*, u.balance");
        $this->db->from(ORDER . ' o');
        $this->db->join('general_users u', 'o.uid = u.id', 'left');
        $this->db->where('o.service_type', 'subscriptions');
        $this->db->where('o.is_monthly', 1);
        $this->db->where('o.auto_renew', 1);
        $this->db->where('o.expiry_at <', date('Y-m-d H:i:s', time() + 86400)); // Ending in 24h
        $this->db->where('o.sub_status !=', 'Canceled');
        $query = $this->db->get();
        $items = $query->result_array();

        if (!$items) {
            echo "No subscriptions due for renewal.<br>";
            return;
        }

        foreach ($items as $item) {
            $renewal_fee = 59.00; // Fixed VIP price for now
            echo "Processing renewal for {$item['username']} (UID: {$item['uid']})...<br>";

            if ($item['balance'] >= $renewal_fee) {
                // SUCCESS: Deduct balance and extend
                $new_balance = $item['balance'] - $renewal_fee;
                $new_expiry = date('Y-m-d H:i:s', strtotime($item['expiry_at'] . ' +30 days'));

                $this->db->trans_start();
                
                // Deduct from User
                $this->db->update('general_users', ['balance' => $new_balance], ['id' => $item['uid']]);
                
                // Update Subscription
                $this->db->update(ORDER, [
                    'expiry_at' => $new_expiry,
                    'sub_status' => 'Active',
                    'changed' => NOW
                ], ['id' => $item['id']]);

                // Log Transaction
                $transaction_data = [
                    'ids' => ids(),
                    'uid' => $item['uid'],
                    'type' => 'Direct',
                    'transaction_id' => 'RENEWAL-' . $item['id'] . '-' . time(),
                    'amount' => $renewal_fee,
                    'status' => 1,
                    'created' => NOW
                ];
                $this->db->insert('general_transaction_logs', $transaction_data);

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    echo "FAILED: Database error during renewal for {$item['id']}.<br>";
                } else {
                    echo "SUCCESS: Renewed until {$new_expiry}<br>";
                }
            } else {
                // FAILED: Insufficient Funds
                echo "FAILED: Insufficient funds for UID {$item['uid']}. Expiring subscription...<br>";
                $this->db->update(ORDER, [
                    'sub_status' => 'Expired',
                    'auto_renew' => 0,
                    'changed' => NOW
                ], ['id' => $item['id']]);
            }
        }

        echo "Subscription renewal cycle finished.<br>";
    }

    public function multiple_status()
    {
        $lock = fopen('_lock_file_multiple_status.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Orders already running');
        $params = [
            'limit' => 100,
            'start' => 0,
        ];
        $items = $this->main_model->list_items($params, ['task' => 'list-items-multiple-status']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }

        $items_group_by_api = group_by_criteria($items, 'api_provider_id');
        foreach ($items_group_by_api as $api_id => $items_group) {
            $api = $this->main_model->get_item(['id' => $api_id], ['task' => 'get-item-provider']);
            if (!$api) {
                $response = ['error' => "API Provider does not exists"];
                $params = [
                    'order_ids' => array_column($items_group, 'id'),
                    'response' => $response,
                ];
                $this->main_model->save_item($params, ['task' => 'item-multiple_status']);
                continue;
            } else {

                $response = $this->provider->multiStatus($api, array_column($items_group, 'api_order_id'));
                if ($response) {
                    $exist_items = [];
                    foreach ($items_group as $key => $item) {
                        if (isset($response[$item['api_order_id']]) && !in_array($item['api_order_id'], $exist_items)) {
                            $this->main_model->save_item(['item' => $item, 'response' => $response[$item['api_order_id']]], ['task' => 'item-status']);
                            $exist_items[] = $item['api_order_id'];
                        }
                    }
                } else {
                    $response = [
                        'connect' => false,
                        'message' => "API Request Error: Unable to connect to the external service!"
                    ];
                    $params = [
                        'order_ids' => array_column($items_group, 'id'),
                        'response' => $response,
                    ];
                    $this->main_model->save_item($params, ['task' => 'item-multiple_status']);
                    continue;
                }
            }
        }
        echo "Successfully";
    }

    //Send
    public function order()
    {
        $lock = fopen('_lock_file_multiple_order.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Order already running');
        $items = $this->main_model->list_items('', ['task' => 'list-items-new-order']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }
        foreach ($items as $key => $row) {

            $this->db->trans_start();

            $api = $this->main_model->get_item(['id' => $row->api_provider_id], ['task' => 'get-item-provider']);
            if (!$api) {
                $response = ['error' => "API Provider does not exists"];
                $this->main_model->save_item(['order_id' => $row->id, 'response' => $response], ['task' => 'item-new-update']);
                continue;
            }
            $data_post = [
                'action' => 'add',
                'service' => $row->api_service_id,
            ];
            switch ($row->service_type) {
                case 'subscriptions':
                    $data_post["username"] = $row->username;
                    $data_post["min"] = $row->sub_min;
                    $data_post["max"] = $row->sub_max;
                    $data_post["posts"] = ($row->sub_posts == -1) ? 0 : $row->sub_posts;
                    $data_post["delay"] = $row->sub_delay;
                    $data_post["expiry"] = (!empty($row->sub_expiry)) ? date("d/m/Y", strtotime($row->sub_expiry)) : ""; //change date format dd/mm/YYYY
                    break;

                case 'custom_comments':
                    $data_post["link"] = $row->link;
                    $data_post["comments"] = json_decode($row->comments);
                    break;

                case 'mentions_with_hashtags':
                    $data_post["link"] = $row->link;
                    $data_post["quantity"] = $row->quantity;
                    $data_post["usernames"] = $row->usernames;
                    $data_post["hashtags"] = $row->hashtags;
                    break;

                case 'mentions_custom_list':
                    $data_post["link"] = $row->link;
                    $data_post["usernames"] = json_decode($row->usernames);
                    break;

                case 'mentions_hashtag':
                    $data_post["link"] = $row->link;
                    $data_post["quantity"] = $row->quantity;
                    $data_post["hashtag"] = $row->hashtag;
                    break;

                case 'mentions_user_followers':
                    $data_post["link"] = $row->link;
                    $data_post["quantity"] = $row->quantity;
                    $data_post["username"] = $row->username;
                    break;

                case 'mentions_media_likers':
                    $data_post["link"] = $row->link;
                    $data_post["quantity"] = $row->quantity;
                    $data_post["media"] = $row->media;
                    break;

                case 'package':
                    $data_post["link"] = $row->link;
                    break;

                case 'custom_comments_package':
                    $data_post["link"] = $row->link;
                    $data_post["comments"] = json_decode($row->comments);
                    break;

                case 'comment_likes':
                    $data_post["link"] = $row->link;
                    $data_post["quantity"] = $row->quantity;
                    $data_post["username"] = $row->username;
                    break;

                default:
                    $data_post["link"] = $row->link;
                    if (isset($row->is_drip_feed) && $row->is_drip_feed == 1) {
                        $data_post["runs"] = $row->runs;
                        $data_post["interval"] = $row->interval;
                        $data_post["quantity"] = $row->dripfeed_quantity;
                    } else {
                        if (isset($row->service_overflow) && $row->service_overflow > 0) {
                            $data_post["quantity"] = $row->quantity * (1 + $row->service_overflow / 100);
                        } else {
                            $data_post["quantity"] = $row->quantity;
                        }
                    }
                    break;
            }
            $response = $this->provider->order($api, $data_post);
            $this->main_model->save_item(['order_id' => $row->id, 'response' => $response], ['task' => 'item-new-update']);

            $this->db->trans_complete();
        }
        echo "Successfully";
    }

    public function automation()
    {
        // DISABLED: We now use the Python Playwright worker exclusively for all automation tasks
        // This prevents race conditions and fake successes from the deprecated IG API
        die('Automation worker disabled. Use Python worker instead.');


        $this->load->model('admin/automation_tasks_model', 'automation_model');
        $this->load->model('admin/ig_accounts_model', 'ig_model');
        $this->load->library('Ig_Automation_Lib');

        file_put_contents('/tmp/automation.log', date('c') . " automation controller entered\n", FILE_APPEND);
        $is_dry_run = $this->is_dry_run_mode();

        // 1. Get Pending Tasks (Micro-batching: Max 3 tasks per run to avoid spam detection)
        $tasks = $this->automation_model->get_pending_tasks(3);
        if (!$tasks) {
            echo $is_dry_run ? "No pending dry-run automation tasks.\n" : "No pending automation tasks.\n";
            exit();
        }
        file_put_contents(
            '/tmp/automation.log',
            sprintf(
                "Automation%s: found %d tasks\n",
                $is_dry_run ? ' [DRY RUN]' : '',
                count($tasks)
            ),
            FILE_APPEND
        );

        foreach ($tasks as $task) {
            // 2. Get an Available Account
            $accounts = $this->ig_model->get_available_accounts(1);
            if (!$accounts) {
                $this->automation_model->update_task($task->id, ['status' => 0, 'error_message' => 'No accounts available']);
                continue;
            }
            $account = (object) $accounts[0];

            $this->automation_model->update_order_status($task->order_id, ORDER_STATUS_PROCESSING);
            $this->automation_model->update_task($task->id, [
                'status' => 1,
                'account_id' => $account->id,
            ]);

            if ($is_dry_run) {
                $result = $this->simulate_automation_result($task);
            } else {
                $target_id = $this->resolve_automation_target($task);

                if (!$target_id) {
                    $error_message = 'Unable to resolve target ID';
                    $this->automation_model->update_task($task->id, [
                        'status' => 3,
                        'error_message' => $error_message,
                    ]);
                    $this->automation_model->log_result(
                        $task->id,
                        $account->id,
                        0,
                        $task->action,
                        0,
                        ['status' => false, 'message' => $error_message]
                    );
                    $this->ig_model->update_action_time($account->id, $error_message);
                    $this->automation_model->refresh_order_status($task->order_id);
                    continue;
                }

                // 3. Perform Action
                $result = $this->ig_automation_lib->perform_action(
                    $task->action,
                    $account->username,
                    $account->password,
                    $target_id,
                    $account->proxy
                );
            }

            // 4. Log and Update Task
            $status = $result['status'] ? 2 : 3; // 2: success, 3: failed
            $this->automation_model->update_task($task->id, [
                'status' => $status,
                'error_message' => isset($result['message']) ? $result['message'] : 'Failed'
            ]);

            if ($result['status']) {
                $current_remains = $this->automation_model->get_order_remains($task->order_id);
                if ($current_remains > 0) {
                    $this->automation_model->update_order_remains($task->order_id, $current_remains - 1);
                }
            }

            $this->automation_model->log_result(
                $task->id,
                $account->id,
                0, // proxy_id if we had a table for it
                $task->action,
                $result['status'] ? 1 : 0,
                $result
            );

            // Anti-ban: Check for Checkpoint/Action Block
            if (!$is_dry_run && isset($result['action_block']) && $result['action_block']) {
                $this->ig_model->update_action_time($account->id, 'BANNED/CHECKPOINT');
                // Set account status to 3 (Disabled/Action Required)
                $this->db->update('ig_accounts', ['status' => 3], ['id' => $account->id]);
                file_put_contents('/tmp/automation_bans.log', "Account {$account->username} blocked on task {$task->id}\n", FILE_APPEND);
            } else {
                // Regular update account cooldown
                $account_message = $is_dry_run
                    ? 'DRY RUN: ' . $result['message']
                    : $result['message'];
                $this->ig_model->update_action_time($account->id, $account_message);
            }

            $this->automation_model->refresh_order_status($task->order_id);
        }

        echo $is_dry_run
            ? "Dry-run automation processing finished.\n"
            : "Automation processing finished.\n";
    }

    private function resolve_automation_target($task)
    {
        if ($task->action == 'follow') {
            $target = $this->ig_automation_lib->resolve_user_id($task->target);
        } else {
            $target = $this->ig_automation_lib->resolve_media_id($task->target);
        }

        return preg_match('/^\d+$/', (string) $target) ? (string) $target : null;
    }

    private function is_dry_run_mode()
    {
        if ((string) get('dry_run') === '1') {
            return true;
        }

        if (!is_cli()) {
            return false;
        }

        $argv = $_SERVER['argv'] ?? [];
        return in_array('dry_run=1', $argv, true) || in_array('--dry-run', $argv, true);
    }

    private function simulate_automation_result($task)
    {
        if (!$this->is_valid_dry_run_target($task)) {
            return [
                'status'   => false,
                'message'  => 'Dry run validation failed for ' . $task->action . ' target',
                'dry_run'  => true,
                'response' => json_encode([
                    'task_id' => $task->id,
                    'action'  => $task->action,
                    'target'  => $task->target,
                    'mode'    => 'dry_run',
                    'result'  => 'invalid_target',
                ]),
            ];
        }

        return [
            'status'   => true,
            'message'  => 'Dry run simulated ' . $task->action . ' action',
            'dry_run'  => true,
            'response' => json_encode([
                'task_id'      => $task->id,
                'action'       => $task->action,
                'target'       => $task->target,
                'mode'         => 'dry_run',
                'result'       => 'simulated_success',
                'processed_at' => date('Y-m-d H:i:s'),
            ]),
        ];
    }

    private function is_valid_dry_run_target($task)
    {
        $target = trim((string) $task->target);
        if (!preg_match('#instagram\.com/#i', $target)) {
            return false;
        }

        $path = trim((string) parse_url($target, PHP_URL_PATH), '/');
        if ($path === '') {
            return false;
        }

        $segments = array_values(array_filter(explode('/', $path)));
        if ($task->action == 'follow') {
            if (count($segments) !== 1) {
                return false;
            }

            return !in_array(strtolower($segments[0]), ['p', 'reel', 'tv', 'explore', 'stories', 'accounts', 'direct']);
        }

        return preg_match('#instagram\.com/(p|reel|tv)/#i', $target) === 1;
    }

    protected function cron_token()
    {
        if (is_cli()) {
            return true;
        }
        $cron_key = get_cron_key();
        if ($cron_key != get('key')) {
            echo "Cron Key mismatch. Expected: $cron_key, Received: " . get('key') . "\n";
            exit('Invalid token');
        }
        return true;
    }

}
