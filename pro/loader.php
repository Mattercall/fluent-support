<?php
/*
 * Bootstrap the Fluent Support Pro module when bundled with the core plugin.
 */

if (defined('FLUENTSUPPORTPRO_PLUGIN_PATH')) {
    return;
}

if (!defined('FLUENT_SUPPORT_VERSION')) {
    return;
}

define('FLUENTSUPPORTPRO', 'fluent-support-pro');
define('FLUENTSUPPORTPRO_PLUGIN_VERSION', FLUENT_SUPPORT_VERSION);

$proPath = rtrim(FLUENT_SUPPORT_PLUGIN_PATH, '/\\') . '/pro/';
$proUrl  = rtrim(FLUENT_SUPPORT_PLUGIN_URL, '/\\') . '/pro/';

define('FLUENTSUPPORTPRO_PLUGIN_PATH', $proPath);
define('FLUENTSUPPORTPRO_PLUGIN_URL', $proUrl);
define('FLUENT_SUPPORT_PRO_DIR_FILE', FLUENT_SUPPORT_PLUGIN_PATH . 'fluent-support.php');

spl_autoload_register(function ($class) {
    if (strpos($class, 'FluentSupportPro\\') !== 0) {
        return;
    }

    $file = str_replace(
        ['FluentSupportPro', '\\', '/App/', 'Database'],
        ['', DIRECTORY_SEPARATOR, 'app/', 'database'],
        $class
    );

    $path = FLUENTSUPPORTPRO_PLUGIN_PATH . trim($file, '/');
    $filepath = $path . '.php';
    if (is_readable($filepath)) {
        require $filepath;
    }
});

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
