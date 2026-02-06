<?php

namespace FluentSupport\App\Hooks\Handlers;

class DeactivationHandler
{
    public function handle()
    {
        if ($this->shouldHandleNetworkWide()) {
            $this->clearSchedulesForAllSites();
            return;
        }

        $this->clearSchedules();
    }

    protected function shouldHandleNetworkWide()
    {
        if (!is_multisite()) {
            return false;
        }

        if (!function_exists('is_plugin_active_for_network')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        return is_plugin_active_for_network('fluent-support/fluent-support.php');
    }

    protected function clearSchedulesForAllSites()
    {
        foreach ($this->getSiteIds() as $siteId) {
            switch_to_blog($siteId);
            $this->clearSchedules();
            restore_current_blog();
        }
    }

    protected function getSiteIds()
    {
        if (function_exists('get_sites')) {
            return get_sites(['fields' => 'ids']);
        }

        global $wpdb;

        return $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    }

    protected function clearSchedules()
    {
        wp_clear_scheduled_hook('fluent_support_hourly_tasks');
        wp_clear_scheduled_hook('fluent_support_daily_tasks');
        wp_clear_scheduled_hook('fluent_support_weekly_tasks');
    }
}
