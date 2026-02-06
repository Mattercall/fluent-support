<?php defined('ABSPATH') or die;
/**
 * Plugin Name:  Fluent Support Pro
 * Plugin URI:   https://fluentsupport.com
 * Description:  Customer Support and Ticketing System for WordPress
 * Version:      2.0.0
 * Author:       WPManageNinja LLC
 * Author URI:   https://fluentsupport.com
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  fluent-support-pro
 * Domain Path:  /languages
 */

if (defined('FLUENT_SUPPORT_PRO_DIR_FILE')) {
    return;
}

define('FLUENT_SUPPORT_PRO_DIR_FILE', __FILE__);

if (defined('FLUENT_SUPPORT_UNIFIED_BOOTSTRAP')) {
    // Unified runtime is responsible for loading Pro module.
    return;
}

require_once 'fluent-support-pro-boot.php';

add_action('plugins_loaded', function () {
    add_action('init', function () {
        load_plugin_textdomain('fluent-support-pro', false, 'fluent-support-pro/languages/');
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

register_activation_hook(
    __FILE__, array('FluentSupportPro\\Database\\DBMigrator', 'run')
);

add_action('wp_insert_site', function ($newSite) {
    if (is_plugin_active_for_network('fluent-support-pro/fluent-support-pro.php')) {
        switch_to_blog($newSite->blog_id);
        \FluentSupportPro\Database\DBMigrator::run(false);
        restore_current_blog();
    }
});
