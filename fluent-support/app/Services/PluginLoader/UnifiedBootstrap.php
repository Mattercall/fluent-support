<?php

namespace FluentSupport\App\Services\PluginLoader;

class UnifiedBootstrap
{
    const MIGRATION_OPTION = 'fluent_support_unified_loader_migrated';

    public function boot($pluginFile)
    {
        if (!defined('FLUENT_SUPPORT_UNIFIED_BOOTSTRAP')) {
            define('FLUENT_SUPPORT_UNIFIED_BOOTSTRAP', true);
        }

        $this->loadCore($pluginFile);

        if ($this->isProPluginEnabled()) {
            $this->loadProModule($pluginFile);
        }

        $this->runMigration();
    }

    protected function loadCore($pluginFile)
    {
        call_user_func(function ($bootstrap) use ($pluginFile) {
            $bootstrap($pluginFile);
        }, require(FLUENT_SUPPORT_PLUGIN_PATH . 'boot/app.php'));

        add_action('wp_insert_site', function ($newSite) {
            if (is_plugin_active_for_network('fluent-support/fluent-support.php')) {
                switch_to_blog($newSite->blog_id);
                (new \FluentSupport\App\Hooks\Handlers\ActivationHandler())->handle(false);
                restore_current_blog();
            }
        });
    }

    protected function loadProModule($pluginFile)
    {
        $pluginsRoot = dirname(FLUENT_SUPPORT_PLUGIN_PATH);
        $proMainFile = $pluginsRoot . '/fluent-support-pro/fluent-support-pro.php';
        $proBootFile = $pluginsRoot . '/fluent-support-pro/fluent-support-pro-boot.php';

        if (!file_exists($proMainFile) || !file_exists($proBootFile)) {
            return;
        }

        if (!defined('FLUENT_SUPPORT_PRO_DIR_FILE')) {
            define('FLUENT_SUPPORT_PRO_DIR_FILE', $proMainFile);
        }

        require_once $proBootFile;

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

        register_activation_hook($pluginFile, function () {
            if (class_exists('FluentSupportPro\\Database\\DBMigrator')) {
                \FluentSupportPro\Database\DBMigrator::run();
            }
        });

        add_action('wp_insert_site', function ($newSite) {
            if (is_plugin_active_for_network('fluent-support-pro/fluent-support-pro.php') && class_exists('FluentSupportPro\\Database\\DBMigrator')) {
                switch_to_blog($newSite->blog_id);
                \FluentSupportPro\Database\DBMigrator::run(false);
                restore_current_blog();
            }
        });
    }

    protected function isProPluginEnabled()
    {
        $proBasename = 'fluent-support-pro/fluent-support-pro.php';

        if (function_exists('is_plugin_active') && is_plugin_active($proBasename)) {
            return true;
        }

        $activePlugins = (array)get_option('active_plugins', []);
        if (in_array($proBasename, $activePlugins, true)) {
            return true;
        }

        if (!is_multisite()) {
            return false;
        }

        $networkPlugins = (array)get_site_option('active_sitewide_plugins', []);

        return isset($networkPlugins[$proBasename]);
    }

    protected function runMigration()
    {
        if (get_option(self::MIGRATION_OPTION)) {
            return;
        }

        $migrationData = [
            'completed_at' => current_time('mysql'),
            'version'      => defined('FLUENT_SUPPORT_VERSION') ? FLUENT_SUPPORT_VERSION : 'unknown'
        ];

        update_option(self::MIGRATION_OPTION, $migrationData, false);

        do_action('fluent_support/unified_loader_migrated', $migrationData);
    }
}
