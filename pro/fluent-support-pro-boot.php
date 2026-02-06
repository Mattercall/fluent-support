<?php

!defined('WPINC') && die;

define('FLUENTSUPPORTPRO', 'fluent-support-pro');
define('FLUENTSUPPORTPRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FLUENTSUPPORTPRO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('FLUENTSUPPORTPRO_PLUGIN_VERSION', '2.0.0');
define('FLUENTSUPPORT_MIN_CORE_VERSION', '2.0.0');

require_once __DIR__ . '/vendor/autoload.php';


class FluentSupportPro_Dependency
{
    public function init()
    {
        $this->injectDependency();
    }

    /**
     * Notify the user about the FluentSupport dependency and instructs to install it.
     */
    protected function injectDependency()
    {
        add_action('admin_notices', function () {
            $pluginInfo = $this->getInstallationDetails();

            $class = 'notice notice-error';

            $install_url_text = 'Click Here to Install the Plugin';

            if ($pluginInfo->action == 'activate') {
                $install_url_text = 'Click Here to Activate the Plugin';
            }

            $message = 'Fluent Support Pro  Requires Fluent Support Base Plugin, <b><a href="' . $pluginInfo->url
                . '">' . $install_url_text . '</a></b>';

            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
        });
    }

    /**
     * Get the FluentSupport plugin installation information e.g. the URL to install.
     *
     * @return \stdClass $activation
     */
    protected function getInstallationDetails()
    {
        $activation = (object)[
            'action' => 'install',
            'url'    => ''
        ];

        $allPlugins = get_plugins();

        if (isset($allPlugins['fluent-support/fluent-support.php'])) {
            $url = wp_nonce_url(
                self_admin_url('plugins.php?action=activate&plugin=fluent-support/fluent-support.php'),
                'activate-plugin_fluent-support/fluent-support.php'
            );

            $activation->action = 'activate';
        } else {
//            $url = wp_nonce_url(
//                admin_url('admin-post.php?action=install_fluent_support'),
//                'install_fluent_support'
//            );

            $api = (object)[
                'slug' => 'fluent-support'
            ];

            $url = wp_nonce_url(
                self_admin_url('update.php?action=install-plugin&plugin=' . $api->slug),
                'install-plugin_' . $api->slug
            );
        }

        $activation->url = $url;

        return $activation;
    }

}

add_action('init', function () {
    if (!defined('FLUENT_SUPPORT_VERSION')) {
        (new FluentSupportPro_Dependency())->init();
    }
});


add_action('plugins_loaded', function () {
    $updaterVersion = defined('FLUENT_SUPPORT_VERSION') ? FLUENT_SUPPORT_VERSION : FLUENTSUPPORTPRO_PLUGIN_VERSION;

    $licenseManager = (new \FluentSupportPro\App\Services\PluginManager\FluentLicensing())->register([
        'version'           => $updaterVersion, // Current version of your plugin
        'item_id'           => 7560869, // Product ID from FluentCart
        'settings_key'      => '__fluentsupport_pro_license',
        'plugin_title'      => 'FluentSupport Pro',
        'basename'          => 'fluent-support/fluent-support.php', // Plugin basename (e.g., 'your-plugin/your-plugin.php')
        'api_url'           => 'https://fluentapi.wpmanageninja.com/', // The API URL for license verification. Normally your store URL
        'store_url'         => 'https://wpmanageninja.com/', // Your store URL
        'purchase_url'      => 'https://fluentsupport.com/', // Purchase URL
        'activate_url'      => admin_url('admin.php?page=fluent-support#/settings/license-management'),
        'show_check_update' => true
    ]);

    $licenseMessage = $licenseManager->getLicenseMessages();
    $activateUrl = $licenseManager->getConfig('activate_url');

    if ($licenseMessage) {
        add_action('admin_notices', function () use ($licenseMessage) {
            if (defined('FLUENT_SUPPORT_VERSION') && !empty($licenseMessage['message'])) {
                $class = 'notice notice-error fc_message';
                $message = $licenseMessage['message'];
                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
            }
        });

        add_filter('fluent_support/dashboard_notice', function ($messages) use ($licenseMessage, $activateUrl) {
            if ($licenseMessage && !empty($licenseMessage['message'])) {
                $html = '<div class="fs_alert_notification fs_alert_warning" style="border-radius: 8px; margin-bottom: 24px; max-width: 1360px; margin-left: auto; margin-right: auto;">
            <div style="display: flex; gap: 8px; align-items: center; padding: 8px 8px 8px 16px;">
                <span style="font-size: 15px; line-height: 18px; flex-shrink: 0;">⚠️</span>
                <p style="flex: 1; margin: 0; font-size: 14px; line-height: 20px; color: #0e121b; letter-spacing: -0.084px;">' . $licenseMessage['message'] . '</p>
                <a href="' . $activateUrl . '" style="color: var(--fs-text-primary, #0E121B);font-size: 14px;font-style: normal;font-weight: 500;line-height: 20px;letter-spacing: -0.084px;text-decoration-line: underline;text-decoration-style: solid;">Activate License</a>
            </div>
        </div>';
                $messages .= $html;
            }
            return $messages;
        }, 100);
    }
}, 0);
