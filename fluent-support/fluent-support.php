<?php defined('ABSPATH') or die;
/**
 * Plugin Name: Fluent Support
 * Description: The Ultimate Support Plugin For Your WordPress.
 * Version: 2.0.1
 * Author: WPManageNinja LLC
 * Author URI: https://wpmanageninja.com
 * Plugin URI: https://fluentsupport.com
 * License: GPLv2 or later
 * Text Domain: fluent-support
 * Domain Path: /language
*/

define('FLUENT_SUPPORT_VERSION', '2.0.1');
define('FLUENT_SUPPORT_PRO_MIN_VERSION', '2.0.1');
define('FLUENT_SUPPORT_UPLOAD_DIR', 'fluent-support');
define('FLUENT_SUPPORT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FLUENT_SUPPORT_PLUGIN_PATH', plugin_dir_path(__FILE__));

require __DIR__ . '/vendor/autoload.php';

(new \FluentSupport\App\Services\PluginLoader\UnifiedBootstrap())->boot(__FILE__);
