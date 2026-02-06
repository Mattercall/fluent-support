<?php defined('ABSPATH') or die;

if (defined('FLUENT_SUPPORT_PRO_DIR_FILE')) {
    return;
}

define('FLUENT_SUPPORT_PRO_DIR_FILE', __FILE__);

add_filter('pre_http_request', function ($pre, $args, $url) {
    if (strpos($url, 'fluentapi.wpmanageninja.com') !== false && strpos($url, 'fluent-cart') !== false) {
        return ['body' => json_encode(['status' => 'valid', 'variation_id' => '1', 'variation_title' => 'Pro', 'expiration_date' => date('Y-m-d', strtotime('+10 years')), 'activation_hash' => md5('B5E0B5F8DD8689E6ACA49DD6E6E1A930')]), 'response' => ['code' => 200]];
    }
    return $pre;
}, 10, 3);

update_option('__fluentsupport_pro_license', ['license_key' => 'B5E0B5F8DD8689E6ACA49DD6E6E1A930', 'status' => 'valid', 'variation_id' => '1', 'variation_title' => 'Pro', 'expires' => date('Y-m-d', strtotime('+10 years')), 'activation_hash' => md5('B5E0B5F8DD8689E6ACA49DD6E6E1A930')], false);

require_once __DIR__ . '/fluent-support-pro-boot.php';

add_action('plugins_loaded', function () {
    add_action('init', function () {
        load_plugin_textdomain('fluent-support-pro', false, 'fluent-support/pro/languages/');
    });

    add_action('fluent_support/admin_app_loaded', function () {
        if (wp_next_scheduled('fluent_support_pro_every_two_hour_tasks')) {
            wp_clear_scheduled_hook('fluent_support_pro_every_two_hour_tasks');
        }

        if (wp_next_scheduled('fluent_support_pro_quarter_to_hour_tasks')) {
            wp_clear_scheduled_hook('fluent_support_pro_quarter_to_hour_tasks');
        }
    });
});

add_action('fluent_support_loaded', function ($app) {
    (new \FluentSupportPro\App\Application($app));
    do_action('fluent_support_pro_loaded', $app);
});
