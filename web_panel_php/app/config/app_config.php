<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['controller'] = [
    'user' => [
        'statistics'       => ['name' => 'Statistics',      'icon' => 'fa fa-bar-chart',     'area_title' => false, 'route-name' => 'statistics', 'menu-level' => 0],
        'new_order'        => ['name' => 'New_order',       'icon' => 'fe fe-shopping-cart', 'area_title' => false, 'route-name' => 'new_order', 'menu-level' => 0],
        'order'            => ['name' => 'order_logs',       'icon' => 'fe fe-calendar', 'area_title' => false, 'route-name' => 'order', 'menu-level' => 0],
        'dripfeed'         => ['name' => 'dripfeed',        'icon' => 'fe fe-droplet',       'area_title' => false, 'route-name' => 'dripfeed', 'menu-level' => 1],
        'subscriptions'    => ['name' => 'Subscriptions',   'icon' => 'fe fe-thumbs-up',     'area_title' => false, 'route-name' => 'subscriptions', 'menu-level' => 1],
        'refill'           => ['name' => 'refill',          'icon' => 'fe fe-refresh-cw',     'area_title' => false, 'route-name' => 'refill', 'menu-level' => 1],
        'services'         => ['name' => 'Services',        'icon' => 'fa fa-list-ul',       'area_title' => false, 'route-name' => 'services', 'menu-level' => 1],
        'add_funds'        => ['name' => 'Add_funds',       'icon' => 'fa fa-file-text-o',   'area_title' => false, 'route-name' => 'add_funds', 'menu-level' => 0],
        'transactions'     => ['name' => 'Transaction_logs','icon' => 'fa fa-file-text-o',   'area_title' => false, 'route-name' => 'transactions', 'menu-level' => 0],
        'membership'       => ['name' => 'Membership',      'icon' => 'fe fe-star',          'area_title' => false, 'route-name' => 'membership', 'menu-level' => 0],
        'tickets'          => ['name' => 'Tickets',          'icon' => 'fa fa-comments-o',    'area_title' => false, 'route-name' => 'tickets', 'menu-level' => 1],
        'faq'              => ['name' => 'FAQs',            'icon' => 'fe fe-help-circle',   'area_title' => false, 'route-name' => 'faq', 'menu-level' => 1],
        'api'              => ['name' => 'API', 'icon' => 'fe fe-share-2',   'area_title' => false, 'route-name' => 'api/docs', 'menu-level' => 0],
        'affiliates'       => ['name' => 'Affiliates', 'icon' => 'fe fe-percent',   'area_title' => false, 'route-name' => 'affiliates', 'menu-level' => 0],
        'terms'            => ['name' => 'terms__conditions', 'icon' => 'fe fe-link',   'area_title' => false, 'route-name' => 'terms', 'menu-level' => 0],
    ],
    'admin' => [
        'general_area'     => ['name' => 'General',         'icon' => '',                    'area_title' => true,  'route-name' => '#'],
        'statistics'       => ['name' => 'Statistics',      'icon' => 'fa fa-bar-chart',     'area_title' => false, 'route-name' => 'statistics'],
        'reports'          => ['name' => 'Reports',         'icon' => 'fe fe-file-text',     'area_title' => false, 'route-name' => 'reports', 'menu-level' => 0],
        'service_area'     => ['name' => 'Service',         'icon' => '',                    'area_title' => true,  'route-name' => '#'],
        'order'            => ['name' => 'Orders',          'icon' => 'fe fe-shopping-cart', 'area_title' => false, 'route-name' => 'order'],
        'dripfeed'         => ['name' => 'Dripfeed',        'icon' => 'fe fe-droplet',       'area_title' => false, 'route-name' => 'dripfeed'],
        'subscriptions'    => ['name' => 'Subscriptions',   'icon' => 'fe fe-thumbs-up',     'area_title' => false, 'route-name' => 'subscriptions'],
        'refill'           => ['name' => 'Refill',          'icon' => 'fe fe-refresh-cw',    'area_title' => false, 'route-name' => 'refill'],
        'cancel'           => ['name' => 'Cancel',          'icon' => 'fe fe-trash-2',    'area_title' => false, 'route-name' => 'cancel'],
        'services'         => ['name' => 'Services',        'icon' => 'fa fa-list-ul',       'area_title' => false, 'route-name' => 'services'],
        'transactions'     => ['name' => 'Transaction logs','icon' => 'fa fa-file-text-o',   'area_title' => false, 'route-name' => 'transactions'],
        'category'         => ['name' => 'Category',        'icon' => 'fa fa-th-large',      'area_title' => false, 'route-name' => 'category'],
        'support_area'     => ['name' => 'Support Area',    'icon' => '',                    'area_title' => true,  'route-name' => '#'],
        'tickets'          => ['name' => 'Ticket',          'icon' => 'fa fa-comments-o',    'area_title' => false, 'route-name' => 'tickets'],
        'users_area'       => ['name' => 'Manage Users',    'icon' => '',                    'area_title' => true, 'route-name' => '#'],
        'users'            => ['name' => 'Users',           'icon' => 'fe fe-users',         'area_title' => false, 'route-name' => 'users'],
        'subscribers'      => ['name' => 'Subscribers',     'icon' => 'fe fe-user-plus',     'area_title' => false, 'route-name' => 'subscribers'],
        'users_activity'   => ['name' => 'User activity log', 'icon' => 'icon-fa fa fa-history', 'area_title' => false, 'route-name' => 'users_activity'],
        'blacklist'        => ['name' => 'Blacklist',       'icon' => 'icon-fa fa fa-user-times', 'area_title' => true, 'route-name' => '#',  'level' => 1],
        'blacklist_ip'     => ['name' => 'Blacklist IP address',       'icon' => 'fe fe-user-x', 'area_title' => false, 'route-name' => 'blacklist/ip', 'level' => 2],
        'blacklist_link'   => ['name' => 'Blacklist Link',       'icon' => 'fe fe-link', 'area_title' => false, 'route-name' => 'blacklist/link', 'level' => 2],
        'blacklist_email'  => ['name' => 'Blacklist Email',       'icon' => 'fe fe-mail', 'area_title' => false, 'route-name' => 'blacklist/email', 'level' => 2],
        'blog_area'        => ['name' => 'Blog',        'icon' => '',                    'area_title' => true,  'route-name' => '#'],
        'blog_category'    => ['name' => 'Blog Category',        'icon' => 'fa fa-th-large',      'area_title' => false, 'route-name' => 'blog_category'],
        'blog_posts'       => ['name' => 'Blog Posts',        'icon' => 'fe fe-edit',      'area_title' => false, 'route-name' => 'blog_posts'],
        'setting_area'     => ['name' => 'Settings',        'icon' => '',                    'area_title' => true,  'route-name' => '#'],
        
        'staffs'           => ['name' => 'Staffs', 'icon' => 'fe fe-users', 'area_title' => false, 'route-name' => 'staffs'],
        'staffs_activity' => ['name' => 'Staff Activity', 'icon' => 'icon-fa fa fa-history', 'area_title' => false, 'route-name' => 'staffs_activity'],
        'role_permission'  => ['name' => 'Role Permissions', 'icon' => 'fe fe-user-check', 'area_title' => false, 'route-name' => 'role_permission'],
        
        'settings'         => ['name' => 'Settings',        'icon' => 'fe fe-settings',      'area_title' => false, 'route-name' => 'settings'],
        'provider'         => ['name' => 'Providers',    'icon' => 'fe fe-git-pull-request',      'area_title' => false, 'route-name' => 'provider'],
        'payments'         => ['name' => 'Payments',        'icon' => 'fe fe-dollar-sign',   'area_title' => false, 'route-name' => 'payments'],
        'payments_bonuses' => ['name' => 'Payment Bonuses', 'icon' => 'fe fe-award',         'area_title' => false, 'route-name' => 'payments_bonuses'],
        'affiliates'       => ['name' => 'Affiliates',      'icon' => 'fe fe-percent',   'area_title' => false, 'route-name' => 'affiliates'],
        'plugins'          => ['name' => 'Modules',         'icon' => 'fe fe-database',         'area_title' => false, 'route-name' => 'plugins'],
        'news'             => ['name' => 'News',            'icon' => 'fe fe-bell',          'area_title' => false, 'route-name' => 'news'],
        'language'         => ['name' => 'Languages',       'icon' => 'fa fa-language',      'area_title' => false, 'route-name' => 'language'],
        'faqs'             => ['name' => 'FAQs',            'icon' => 'fe fe-help-circle',   'area_title' => false, 'route-name' => 'faqs'],
        'proxies'          => ['name' => 'Proxies',         'icon' => 'fe fe-shield',        'area_title' => false, 'route-name' => 'proxies'],
        'ig_accounts'      => ['name' => 'Instagram Accounts', 'icon' => 'fe fe-instagram', 'area_title' => false, 'route-name' => 'ig_accounts'],
        'cronjobs'         => ['name' => 'Cronjobs link',   'icon' => 'fe fe-rotate-cw',   'area_title' => false, 'route-name' => 'cronjobs'],
    ]
];

$config['client'] = [
    'header_nav' => [
        'home' => ['name' => 'Home', 'icon' => 'fa fa-bar-chart', 'route-name' => '', 'menu-level' => 0],
        'services' => ['name' => 'Services', 'icon' => 'fa fa-bar-chart', 'route-name' => 'services', 'menu-level' => 0],
        'blog' => ['name' => 'Blog', 'icon' => 'fa fa-bar-chart', 'route-name' => 'blog', 'menu-level' => 0],
        'faq' => ['name' => 'FAQs', 'icon' => 'fa fa-bar-chart', 'route-name' => 'faq', 'menu-level' => 0],
        'api' => ['name' => 'api', 'icon' => 'fa fa-bar-chart', 'route-name' => 'api/docs', 'menu-level' => 0],
    ],
];

$config['social_media'] = [
    'everything' => [
        'name'      => 'All',
        'icon_class'   => '<i class="fa fa-home fs-16" aria-hidden="true"></i>',
    ],
    'favorite' => [
        'name'      => 'My favorite',
        'icon_class'   => '<i class="fa fa-heart fs-16" aria-hidden="true"></i>',
    ],
    'instagram' => [
        'name'      => 'Instagram',
        'icon_class'   => '<i class="fa fa-instagram fs-16" aria-hidden="true"></i>',
    ],
    'tiktok' => [
        'name'      => 'Tiktok',
        'icon_class'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16">
                            <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"/>
                            </svg>',
    ],
    'facebook' => [
        'name'      => 'Facebook',
        'icon_class'   => '<i class="fa fa-facebook-official fs-16" aria-hidden="true"></i>',
    ],
    'twitter' => [
        'name'      => 'Twitter',
        'icon_class'   => '<i class="fa fa-twitter-square fs-16" aria-hidden="true"></i>',
    ],
    'youtube' => [
        'name'      => 'Youtube',
        'icon_class'   => '<i class="fa fa-youtube-play fs-16" aria-hidden="true"></i>',
    ],
    'pinterest' => [
        'name'      => 'Pinterest',
        'icon_class'   => '<i class="fa fa-pinterest fs-16" aria-hidden="true"></i>',
    ],
    'twitch' => [
        'name'      => 'Twitch',
        'icon_class'   => '<i class="fa fa-twitch fs-16" aria-hidden="true"></i>',
    ],
    'telegram' => [
        'name'      => 'Telegram',
        'icon_class'   => '<i class="fa fa-telegram fs-16" aria-hidden="true"></i>',
    ],
    'spotify' => [
        'name'      => 'Spotify',
        'icon_class'   => '<i class="fa fa-spotify fs-16" aria-hidden="true"></i>',
    ],
    'soundcloud' => [
        'name'      => 'Soundcloud',
        'icon_class'   => '<i class="fa fa-soundcloud fs-16" aria-hidden="true"></i>',
    ],
    'snapchat' => [
        'name'      => 'Snapchat',
        'icon_class'   => '<i class="fa fa-snapchat fs-16" aria-hidden="true"></i>',
    ],
    'vk' => [
        'name'      => 'VK',
        'icon_class'   => '<i class="fa fa-vk fs-16" aria-hidden="true"></i>',
    ],
    'vimeo' => [
        'name'      => 'Vimeo',
        'icon_class'   => '<i class="fa fa-vimeo fs-16" aria-hidden="true"></i>',
    ],
    'tumblr' => [
        'name'      => 'Tumblr',
        'icon_class'   => '<i class="fa fa-tumblr fs-16" aria-hidden="true"></i>',
    ],
    'linkedin' => [
        'name'      => 'Linkedin',
        'icon_class'   => '<i class="fa fa-linkedin fs-16" aria-hidden="true"></i>',
    ],
    'tripadvisor' => [
        'name'      => 'Tripadvisor',
        'icon_class'   => '<i class="fa fa-tripadvisor fs-16" aria-hidden="true"></i>',
    ],
    'discord' => [
        'name'      => 'Discord',
        'icon_class'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-discord" viewBox="0 0 16 16">
                            <path d="M13.545 2.907a13.2 13.2 0 0 0-3.257-1.011.05.05 0 0 0-.052.025c-.141.25-.297.577-.406.833a12.2 12.2 0 0 0-3.658 0 8 8 0 0 0-.412-.833.05.05 0 0 0-.052-.025c-1.125.194-2.22.534-3.257 1.011a.04.04 0 0 0-.021.018C.356 6.024-.213 9.047.066 12.032q.003.022.021.037a13.3 13.3 0 0 0 3.995 2.02.05.05 0 0 0 .056-.019q.463-.63.818-1.329a.05.05 0 0 0-.01-.059l-.018-.011a9 9 0 0 1-1.248-.595.05.05 0 0 1-.02-.066l.015-.019q.127-.095.248-.195a.05.05 0 0 1 .051-.007c2.619 1.196 5.454 1.196 8.041 0a.05.05 0 0 1 .053.007q.121.1.248.195a.05.05 0 0 1-.004.085 8 8 0 0 1-1.249.594.05.05 0 0 0-.03.03.05.05 0 0 0 .003.041c.24.465.515.909.817 1.329a.05.05 0 0 0 .056.019 13.2 13.2 0 0 0 4.001-2.02.05.05 0 0 0 .021-.037c.334-3.451-.559-6.449-2.366-9.106a.03.03 0 0 0-.02-.019m-8.198 7.307c-.789 0-1.438-.724-1.438-1.612s.637-1.613 1.438-1.613c.807 0 1.45.73 1.438 1.613 0 .888-.637 1.612-1.438 1.612m5.316 0c-.788 0-1.438-.724-1.438-1.612s.637-1.613 1.438-1.613c.807 0 1.451.73 1.438 1.613 0 .888-.631 1.612-1.438 1.612"/>
                        </svg>',
    ],
    'reddit' => [
        'name'      => 'Reddit',
        'icon_class'   => '<i class="fa fa-reddit fs-16" aria-hidden="true"></i>',
    ],
    'google' => [
        'name'      => 'Google',
        'icon_class'   => '<i class="fa fa-google fs-16" aria-hidden="true"></i>',
    ],
    'traffic' => [
        'name'      => 'Web Traffic',
        'icon_class'   => '<i class="fa fa-globe fs-16" aria-hidden="true"></i>',
    ],
    'quora' => [
        'name'      => 'Quora',
        'icon_class'   => '<i class="fa fa-quora fs-16" aria-hidden="true"></i>',
    ],
    'other' => [
        'name'      => 'Others',
        'icon_class'   => '<i class="fa fa-plus fs-16" aria-hidden="true"></i>',
    ],
];

$config['template'] = [
    'table' => [
        'class'  => 'table table-hover table-borderless table-vcenter card-table',
    ],
    'form' => [
        'class_element'            => 'form-control',
        'class_element_checkbox'   => 'form-check-input',
        'class_element_editor'     => 'form-control plugin_editor',
        'class_element_datepicker' => 'form-control datepicker',
        'class_element_text_emoji' => 'form-control text-emoji',
    ],
    'datetime' => [
        'long'  => 'Y-m-d H:i:s',
        'short' => 'Y-m-d',
        'blog'  => 'M j, Y',
    ],
    'news' => [
        'new_services'      => ['name' => 'New services',      'class' => 'btn-info'],
        'disabled_services' => ['name' => 'Disabled services', 'class' => 'btn-orange'],
        'updated_services'  => ['name' => 'Updated services',  'class' => 'btn-lime'],
        'announcement'      => ['name' => 'Announcement',      'class' => 'btn-primary'],
    ],
    'status' => [
        '1'      => ['name' => 'Active',   'class' => 'bg-indigo', 'class-badge' => 'bg-indigo-lt'],
        '0'      => ['name' => 'Deactive', 'class' => 'bg-orange', 'class-badge' => 'bg-orange-lt'],
        '2'      => ['name' => 'Unknow',   'class' => 'bg-orange', 'class-badge' => 'bg-orange-lt'],
        '3'      => ['name' => 'All',      'class' => 'bg-azure'],
    ],
    'tickets_status' => [
        'pending'     => ['name' => 'Pending',  'class' => 'bg-purple', 'class-badge' => 'bg-purple-lt'],
        'answered'    => ['name' => 'Answered', 'class' => 'bg-green',  'class-badge' => 'bg-secondary'],
        'closed'      => ['name' => 'Closed',   'class' => 'bg-indigo', 'class-badge' => 'bg-indigo-lt'],
    ], 
    'order_status' => [
        'all'             => ['name' => 'All',       'class' => 'bg-purple', 'class-badge' => 'bg-purple'],
        'active'          => ['name' => 'Active',       'class' => 'bg-purple', 'class-badge' => 'bg-green'],
        'completed'       => ['name' => 'Completed',    'class' => 'bg-green',  'class-badge' => 'bg-indigo'],
        'processing'      => ['name' => 'Processing',   'class' => 'bg-indigo', 'class-badge' => 'bg-blue'],
        'inprogress'      => ['name' => 'In progress',   'class' => 'bg-indigo', 'class-badge' => 'bg-cyan'],
        'pending'         => ['name' => 'Pending',      'class' => 'bg-indigo', 'class-badge' => 'bg-green'],
        'partial'         => ['name' => 'Partial',      'class' => 'bg-indigo', 'class-badge' => 'bg-pink'],
        'canceled'        => ['name' => 'Canceled',     'class' => 'bg-secondary', 'class-badge' => 'bg-secondary'],
        'error'           => ['name' => 'Error',        'class' => 'bg-red', 'class-badge' => 'bg-red'],
        'fail'            => ['name' => 'Fail',         'class' => 'bg-red', 'class-badge' => 'bg-red'],
        'paused'          => ['name' => 'Paused',     'class' => 'bg-red', 'class-badge' => 'bg-red'],
        'expired'         => ['name' => 'Expired',     'class' => 'bg-red', 'class-badge' => 'bg-red'],
        'success'         => ['name' => 'Success',     'class' => 'bg-green', 'class-badge' => 'bg-indigo'],
        'awaiting'        => ['name' => 'Awaiting',     'class' => 'bg-lime', 'class-badge' => 'bg-yellow-lt'],
        'rejected'        => ['name' => 'Rejected',     'class' => 'bg-secondary', 'class-badge' => 'bg-secondary'],
        'refunded'        => ['name' => 'Refunded',     'class' => 'bg-indigo', 'class-badge' => 'bg-indigo'],
        'approved'        => ['name' => 'Approved',     'class' => 'bg-indigo', 'class-badge' => 'bg-indigo'],
    ], 
    'transactions_status' => [
        '-1'     => ['name' => 'Cancelled', 'class' => 'bg-red',    'class-badge' => 'bg-red'],
        '1'      => ['name' => 'Paid',      'class' => 'bg-indigo', 'class-badge' => 'bg-indigo-lt'],
        '0'      => ['name' => 'Waiting',   'class' => 'bg-orange', 'class-badge' => 'bg-orange-lt'],
        '2'      => ['name' => 'Unknow',    'class' => 'bg-orange', 'class-badge' => 'bg-orange-lt'],
        '3'      => ['name' => 'All',      'class' => 'bg-azure'],
    ],
    'button' => [
        'edit'         => ['name' => 'Edit',   'class' => 'ajaxModal',      'icon' => 'fe fe-edit', 'route-name' => '/update/'],
        'edit2'         => ['name' => 'Edit',   'class' => '',      'icon' => 'fe fe-edit', 'route-name' => '/update/'],
        'sign_in_history' => ['name' => 'Sign in history IP',   'class' => '', 'icon' => 'fe fe-calendar', 'route-name' => '/sign_in_history/'],
        'delete'       => ['name' => 'Delete', 'class' => 'ajaxDeleteItem', 'icon' => 'fe fe-trash-2', 'route-name' => '/delete/'],
        'delete_custom_rate' => ['name' => 'Delete custom rates', 'class' => 'ajaxDeleteItem', 'icon' => 'fe fe-trash', 'route-name' => '/delete_custom_rate/'],
        'view_user'    => ['name' => 'View User', 'class' => 'ajaxViewUser', 'icon' => 'fe fe-eye', 'route-name' => '/view_user/'],
        'view'         => ['name' => 'View', 'class' => 'ajaxModal', 'icon' => 'fe fe-eye', 'route-name' => '/view/'],
        'more_detail'  => ['name' => 'More detail', 'class' => 'ajaxModal', 'icon' => 'fe fe-help-circle', 'route-name' => '/info/'],
        'set_password'  => ['name' => 'Set Password', 'class' => 'ajaxModal', 'icon' => 'fe fe-lock', 'route-name' => '/set_password/'],
        'add_funds'     => ['name' => 'Add Funds', 'class' => 'ajaxModal', 'icon' => 'fe fe-dollar-sign', 'route-name' => '/add_funds/'],
        // 'set_balance'   => ['name' => 'Set balance', 'class' => 'ajaxModal', 'icon' => 'fe fe-credit-card', 'route-name' => '/edit_funds/'],
        'send_mail'     => ['name' => 'Send Mail', 'class' => 'ajaxModal', 'icon' => 'fe fe-mail', 'route-name' => '/mail/'],
        'balance'       => ['name' => 'Update balance', 'class' => 'ajaxUpdateApiProvider', 'icon' => 'fe fe-dollar-sign', 'route-name' => '/balance/'],
        'sync_services' => ['name' => 'Sync services', 'class' => 'ajaxModal', 'icon' => 'fe fe-refresh-cw', 'route-name' => '/sync_services/'],
        'services'      => ['name' => 'Services Lists', 'class' => '', 'icon' => 'fe fe-list', 'route-name' => '/services/'],
        'resend'        => ['name'  => 'Resend Order', 'class' => '', 'icon' => 'fe fe-refresh-cw', 'route-name' => '/resend/'],
        'update_from_provider' => ['name'  => 'Update From Provider', 'class' => 'ajaxGeneralAction', 'icon' => 'fe fe-loader', 'route-name' => '/update_from_provider/'],
    ],
    'bulk_action' => [
        'delete'   => ['name' => 'Delete', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-trash-2',      'route-name' => '/bulk_action/'],
        'empty'    => ['name' => 'Empty All', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-trash',      'route-name' => '/bulk_action/'],
        'delete_custom_rates'   => ['name' => 'Delete custom rates', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-trash',      'route-name' => '/bulk_action/'],
        'deactive' => ['name' => 'Deactive All', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-x-square',     'route-name' => '/bulk_action/'],
        'active'   => ['name' => 'Active', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-check-square', 'route-name' => '/bulk_action/'],
        'pending'  => ['name' => 'Mark as Pending',  'class' => 'ajaxActionOptions', 'icon' => 'fe fe-navigation',  'route-name' => '/bulk_action/'],
        'closed'   => ['name' => 'Mark as Closed',   'class' => 'ajaxActionOptions', 'icon' => 'fe fe-lock',        'route-name' => '/bulk_action/'],
        'answered' => ['name' => 'Mark as Answered', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-check',       'route-name' => '/bulk_action/'],
        'unread'   => ['name' => 'Mark as Unread', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-mail',       'route-name' => '/bulk_action/'],
        'csv'      => ['name' => 'Export CSV',   'class' => '', 'icon' => 'fe fe-download',  'route-name' => '/export/'],
        'excel'    => ['name' => 'Export excel', 'class' => '', 'icon' => 'fe fe-download',  'route-name' => '/export/'],
        'pending'      => ['name' => 'Pending', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-clock', 'route-name' => '/bulk_action/'],
        'inprogress'   => ['name' => 'In progress', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-loader', 'route-name' => '/bulk_action/'],
        'completed'    => ['name' => 'Completed', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-check-square', 'route-name' => '/bulk_action/'],
        'resend'       => ['name' => 'Resend Order', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-refresh-cw', 'route-name' => '/bulk_action/'],
       
        'cancel'       => ['name' => 'Cancel and Refund', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-x', 'route-name' => '/bulk_action/'],
        'rejected'     => ['name' => 'Rejected', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-x', 'route-name' => '/bulk_action/'],
        'paused'       => ['name' => 'Paused', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-alert-triangle', 'route-name' => '/bulk_action/'],
        'copy_id'      => ['name' => 'Copy to Clipboard (ID)', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-copy', 'route-name' => '/bulk_action/'],
        'copy_order_id' => ['name' => 'Copy to Clipboard (Order ID)', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-copy', 'route-name' => '/bulk_action/'],
        'copy_api_order_id' => ['name' => 'Copy to Clipboard (API Order ID)', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-copy', 'route-name' => '/bulk_action/'],
        'copy_api_refill_id' => ['name' => 'Copy to Clipboard (API Refill ID)', 'class' => 'ajaxActionOptions', 'icon' => 'fe fe-copy', 'route-name' => '/bulk_action/'],
    ],
    'search_field' => [
        'all'           => ['name' => 'All'],
        'id'            => ['name' => 'ID'],
        'email'         => ['name' => 'Email'],
        'name'          => ['name' => 'Name'],
        'order_id'      => ['name' => 'Order ID'],
        'sort'          => ['name' => 'Sort'],
        'question'      => ['name' => 'Question'],
        'answer'        => ['name' => 'Answer'],
        'description'    => ['name' => 'Description'],
        'ip'             => ['name' => 'IP address'],
        'history_ip'     => ['name' => 'History ip'],
        'country'        => ['name' => 'Location'],
        'first_name'     => ['name' => 'First name'],
        'last_name'      => ['name' => 'Last name'],
        'username'      => ['name'  => 'Username'],
        'transaction_id' => ['name' => 'Transaction id'],
        'note'           => ['name' => 'Note'],
        'subject'        => ['name' => 'Subject'],
        'api_service_id' => ['name' => 'API service id'],
        'api_order_id'   => ['name' => 'API Order id'],
        'service_id'     => ['name' => 'Service id'],
        'link'           => ['name' => 'Link'],
        'event'          => ['name' => 'Event'],
    ],
    'service_type' => [
        'default'                 => 'Default',
        'subscriptions'           => 'Subscriptions',
        'custom_comments'         => 'Custom comments',
        'custom_comments_package' => 'Custom comments package',
        'mentions_with_hashtags'  => 'Mentions with hashtags',
        'mentions_custom_list'    => 'Mentions custom list',
        'mentions_hashtag'        => 'Mentions hashtag',
        'mentions_user_followers' => 'Mentions user followers',
        'mentions_media_likers'   => 'Mentions media likers',
        'package'                 => 'Package',
        'comment_likes'           => 'Comment likes',
    ],
    'reports' => [
        'payments' => ['name' => 'Payments'],
        'orders'   => ['name' => 'Orders'],
        'tickets'  => ['name' => 'Tickets'],
        'profits'  => ['name' => 'Profits'],
    ]
];

$config['config'] = [
    'default_customizer_settings' => [
        "colorScheme"   => "light",
        "sidebarColor"  => "dark",
        "sidebarSize"   => "default",
    ],
    'service_avg_time' => [
        'orders_for_avg_time'   => 3,
        'max_service_requests'  => 10
    ],
    'button' => [
        'default'          => ['edit', 'delete'],
        'transactions'     => ['edit', 'delete'],
        'faqs'             => ['edit', 'delete'],
        'language'         => ['edit2', 'delete'],
        'blog_posts'       => ['edit2', 'delete'],
        'order'            => ['edit', 'resend', 'update_from_provider'],
        'news'             => ['edit', 'view' ,'delete'],
        'dripfeed'         => ['edit', 'resend' ,'delete'],
        'subscriptions'    => ['edit', 'resend' ,'delete'],
        'tickets'          => ['delete'],
        'category'         => ['edit', 'delete'],
        'services'         => ['edit', 'delete_custom_rate', 'delete'],
        'payments'         => ['edit'],
        'payments_bonuses' => ['edit', 'delete'],
        'provider'         => ['edit', 'balance', 'sync_services', 'services','delete'],
        'subscribers'      => ['delete', 'send_mail'],
        'users'            => ['edit', 'view_user', 'add_funds', 'sign_in_history', 'set_password', 'send_mail', 'more_detail', 'delete',], //, 'set_balance'
        'staffs'           => ['edit', 'set_password', 'delete',],
        'role_permission'  => ['edit', 'delete'],
        'users_banned_ip'  => ['edit', 'delete'],
        'refill'           => ['view'],
        'cancel'           => ['view'],
    ],
    'status' => [
        'default'  => [
            '0' => 'inactive', 
            '1' => 'active'
        ],
        'transactions'  => [
            '-1' => 'waiting', 
            '0' => 'pending', 
            '1' => 'paid'
        ],
        'tickets'  => [
            'pending'   => 'pending', 
            'answered'  => 'answered', 
            'closed'    => 'closed'
        ],
        'order'          => ['all', 'awaiting','pending', 'processing', 'inprogress', 'completed', 'partial', 'canceled', 'error', 'fail'],
        'dripfeed'       => ['all', 'active', 'completed', 'error', 'canceled'],
        'subscriptions'  => ['all', 'active', 'paused', 'expired', 'completed', 'error'],
        'refill'         => ['all', 'pending', 'awaiting', 'inprogress', 'rejected', 'completed'],
        'cancel'         => ['all', 'pending', 'awaiting', 'success', 'rejected', 'completed'],
    ],

    'search' => [
        'default'         => ['all', 'name'],
        'blacklist_ip'    => ['ip'],
        'blacklist_email' => ['email'],
        'blacklist_link'  => ['link'],
        'category'        => ['name'],
        'blog_category'   => ['name'],
        'blog_posts'      => ['name'],
        'transactions'    => ['transaction_id', 'email', 'note'],
        'payments'        => ['name'],
        'faqs'            => ['question', 'answer'],
        'news'            => ['description'],
        'subscribers'     => ['email', 'ip', 'country'],
        'users'           => ['email', 'first_name', 'last_name', 'history_ip'],
        'staffs'          => ['email', 'first_name', 'last_name', 'history_ip'],
        'staffs_activity' => ['event', 'ip'],
        'role_permission' => ['all', 'name'],
        'users_activity'  => ['email', 'ip'],
        'users_banned_ip' => ['ip', 'description'],
        'tickets'         => ['id', 'email', 'subject'],
        'services'        => [ 'all', 'id', 'name', 'api_service_id'],
        'provider'        => [ 'name'],
        'order'           => [ 'id', 'api_order_id', 'link', 'email', 'service_id'],
        'dripfeed'        => [ 'id', 'api_order_id', 'link', 'email'],
        'subscriptions'   => [ 'id', 'api_order_id', 'username', 'email'],
        'refill'          => [ 'id', 'order_id', 'link', 'email'],
        'cancel'          => [ 'id', 'order_id', 'link', 'email'],
        'affiliates'      => [ 'email'],
    ],
    'bulk_action' => [
        'default'          => ['active', 'deactive', 'delete'],
        'services'         => ['active', 'deactive', 'delete_custom_rates', 'delete'],
        'tickets'          => ['pending', 'answered', 'unread', 'closed', 'delete'],
        'category'         => ['active', 'deactive', 'delete'],
        'blog_category'    => ['active', 'deactive', 'delete'],
        'blog_posts'       => ['active', 'deactive', 'delete'],
        'payments'         => ['active', 'deactive'],
        'payments_bonuses' => ['active', 'deactive'],
        'users'            => ['active', 'deactive', 'csv', 'excel', 'delete'],
        'users_activity'   => ['delete', 'empty'],
        'staffs_activity'  => ['delete', 'empty'],
        'users_banned_ip'  => ['delete', 'empty'],
        'subscribers'      => ['csv', 'excel', 'delete'],
        'transactions'     => [],
        'order'            => ['resend', 'pending', 'inprogress', 'completed', 'cancel', 'copy_id', 'copy_api_order_id'],
        'dripfeed'         => ['completed', 'cancel'],
        'refill'           => ['completed', 'rejected', 'copy_order_id', 'copy_api_order_id', 'copy_api_refill_id'],
        'cancel'           => ['completed', 'rejected', 'copy_order_id'],
        'subscriptions'    => ['active', 'completed', 'paused', 'cancel'],
    ],
    
    'default_role_permissions' => [
        'users'            => [
            'index' => 1, 
            'rules' => [
                'add'               => ['status' => 1, 'alias' => 'Add User'],  
                'edit'              => ['status' => 1, 'alias' => 'Edit User'],  
                'delete'            => ['status' => 1, 'alias' => 'Delete User'], 
                'view_user'         => ['status' => 1, 'alias' => 'View user'], 
                'change_status'     => ['status' => 1, 'alias' => 'Change status'], 
                'add_funds'         => ['status' => 1, 'alias' => 'Add funds'], 
                'custom_rate'       => ['status' => 1, 'alias' => 'Set custom rate'], 
                'sign_in_history'   => ['status' => 1, 'alias' => 'Sign in history'], 
                // 'set_balance'       => ['status' => 1, 'alias' => 'Set balance'], 
                'set_password'      => ['status' => 1, 'alias' => 'Set password'],
                'send_mail'         => ['status' => 1, 'alias' => 'Send mail'], 
                'export'            => ['status' => 1, 'alias' => 'Export User']
            ]
        ],
        'tickets'          => [
            'index' => 1, 
            'rules' => [
                'add'               => ['status' => 1, 'alias' => 'Add Ticket'],
                'view'              => ['status' => 1, 'alias' => 'View Ticket'], 
                'delete'            => ['status' => 1, 'alias' => 'Delete Ticket'], 
                'closed'            => ['status' => 1, 'alias' => 'Close Ticket'], 
                'unread'            => ['status' => 1, 'alias' => 'Mark as unread'], 
                'submit_message'    => ['status' => 1, 'alias' => 'Submit Message'], 
                'edit_message'      => ['status' => 1, 'alias' => 'Edit Message'], 
                'delete_message'    => ['status' => 1, 'alias' => 'Delete Message'], 
            ]
        ],
        'order'            => [
            'index' => 1, 
            'rules' => [
                'edit'                  => ['status' => 1, 'alias' => 'Edit order'], 
                'delete'                => ['status' => 1, 'alias' => 'Delete order'], 
                'copy_api_order_id'     => ['status' => 1, 'alias' => 'See API Order ID'], 
                'see_charge'            => ['status' => 1, 'alias' => 'See Charge'], 
                'see_cost'              => ['status' => 1, 'alias' => 'See Cost'],
                'change_status'         => ['status' => 1, 'alias' => 'Change status'],  
                'cancel'                => ['status' => 1, 'alias' => 'Cancel and Refund'],
                'partial'               => ['status' => 1, 'alias' => 'Set Partial'],
                'resend'                => ['status' => 1, 'alias' => 'Resend order'],
                'update_from_provider'  => ['status' => 1, 'alias' => 'Update from provider']
            ]
        ],
        'dripfeed'         => [
            'index' => 1, 
            'rules' => [
                'edit'              => ['status' => 1, 'alias' => 'Edit order'],
                'delete'            => ['status' => 1, 'alias' => 'Delete order'], 
                'copy_api_order_id'        => ['status' => 1, 'alias' => 'See API Order ID'], 
                'see_charge'        => ['status' => 1, 'alias' => 'See Charge'],  
                'resend'            => ['status' => 1, 'alias' => 'Resend order'],
                'cancel'            => ['status' => 1, 'alias' => 'Cancel and Refund'],
            ]
        ],
        'subscriptions'    => [
            'index' => 1, 
            'rules' => [
                'edit'              => ['status' => 1, 'alias' => 'Edit order'],
                'delete'            => ['status' => 1, 'alias' => 'Delete order'], 
                'copy_api_order_id'        => ['status' => 1, 'alias' => 'See API Order ID'], 
                'see_charge'        => ['status' => 1, 'alias' => 'See Charge'],  
                'resend'            => ['status' => 1, 'alias' => 'Resend order'],
                'cancel'            => ['status' => 1, 'alias' => 'Cancel and Refund'],
            ]
        ],
        'refill'           => [
            'index' => 1, 
            'rules' => [
                'copy_api_order_id'   => ['status' => 1, 'alias' => 'See API order ID'], 
                'copy_api_refill_id'  => ['status' => 1, 'alias' => 'See API refill ID'], 
            ]
        ],
        'cancel'           => [
            'index' => 1, 
            'rules' => [
                'copy_api_order_id'   => ['status' => 1, 'alias' => 'See API order ID'], 
            ]
        ],
        'services'         => [
            'index' => 1, 
            'rules' => [
                'see_provider'  => ['status' => 1, 'alias' => 'See Provider Column'],  
                'add'           => ['status' => 1, 'alias' => 'Add Service'], 
                'import'        => ['status' => 1, 'alias' => 'Import Service'], 
                'edit'          => ['status' => 1, 'alias' => 'Edit Service'], 
                'delete'        => ['status' => 1, 'alias' => 'Delete Service'],
                'change_status'         => ['status' => 1, 'alias' => 'Change status'],  
                'delete_custom_rates' => ['status' => 1, 'alias' => 'Delete custom rates']
            ]
        ],
        'transactions'     => [
            'index' => 1, 
            'rules' => [
                'edit'   => ['status' => 1, 'alias' => 'Edit'], 
                'delete' => ['status' => 1, 'alias' => 'Delete'], 
            ]
        ],
        'category'         => [
            'index' => 1, 
            'rules' => [
                'add'    => ['status' => 1, 'alias' => 'Add category'], 
                'edit'   => ['status' => 1, 'alias' => 'Edit category'],
                'change_status'  => ['status' => 1, 'alias' => 'Change status'],  
                'delete' => ['status' => 1, 'alias' => 'Delete'], 
            ]
        ],
        'subscribers'      => [
            'index' => 1, 
            'rules' => [
                'export' => ['status' => 1, 'alias' => 'Export Subscribers'],
                'delete' => ['status' => 1, 'alias' => 'Delete'], 
            ]
        ],
        'users_activity'   => [
            'index' => 1, 
            'rules' => [
                'delete'  => ['status' => 1, 'alias' => 'Delete'], 
                'empty'   => ['status' => 1, 'alias' => 'Empty all'], 
            ]
        ],
        'blacklist'     => [
            'index' => 1, 
            'rules' => [
                'add'    => ['status' => 1, 'alias' => 'Add New'],
                'edit'   => ['status' => 1, 'alias' => 'Edit'],
                'delete' => ['status' => 1, 'alias' => 'Delete'], 
            ]
        ],

        'blog_category'    => [
            'index' => 1, 
            'rules' => [
                'add'    => ['status' => 1, 'alias' => 'Add New'],
                'edit'   => ['status' => 1, 'alias' => 'Edit'], 
                'delete' => ['status' => 1, 'alias' => 'Delete'], 
            ]
        ],
        'blog_posts'       => [
            'index' => 1, 
            'rules' => [
                'add'    => ['status' => 1, 'alias' => 'Add New'],
                'edit'   => ['status' => 1, 'alias' => 'Edit'], 
                'delete' => ['status' => 1, 'alias' => 'Delete'], 
            ]
        ],
        
        'news'             => [
            'index' => 1, 
            'rules' => [
                'add'    => ['status' => 1, 'alias' => 'Add New'],
                'edit'   => ['status' => 1, 'alias' => 'Edit'], 
                'delete' => ['status' => 1, 'alias' => 'Delete'], 
            ]
        ],
        'language'         => [
            'index' => 1, 
            'rules' => [
                'add'    => ['status' => 1, 'alias' => 'Add New'],
                'edit'   => ['status' => 1, 'alias' => 'Edit'], 
                'delete' => ['status' => 1, 'alias' => 'Delete'], 
            ]
        ],
        'faqs'             => [
            'index' => 1, 
            'rules' => [
                'add'    => ['status' => 1, 'alias' => 'Add New'],
                'edit'   => ['status' => 1, 'alias' => 'Edit'], 
                'delete' => ['status' => 1, 'alias' => 'Delete'], 
            ]
        ],
        'settings'         => ['index' => 1],
        'statistics'       => ['index' => 1],
        'reports'          => ['index' => 1],
        'provider'         => ['index' => 1],
        'subscribers'      => ['index' => 1],
        'payments'         => ['index' => 1],
        'payments_bonuses' => ['index' => 1],
        'affiliates'       => ['index' => 1],
        'cronjobs'         => ['index' => 1],
    ]
];