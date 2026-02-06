<?php

namespace FluentSupport\App\Hooks\Handlers;

use FluentSupport\Database\DBMigrator;
use FluentSupportPro\Database\DBMigrator as ProDBMigrator;

class ActivationHandler
{
    public function handle($network_wide = false)
    {
        if ($network_wide && is_multisite()) {
            $this->handleNetworkActivation();
            return;
        }

        DBMigrator::run(false);

        if (class_exists(ProDBMigrator::class)) {
            ProDBMigrator::run(false);
        }

        $this->scheduleEvents();
    }

    protected function handleNetworkActivation()
    {
        DBMigrator::run(true);

        if (class_exists(ProDBMigrator::class)) {
            ProDBMigrator::run(true);
        }

        foreach ($this->getSiteIds() as $siteId) {
            switch_to_blog($siteId);
            $this->scheduleEvents();
            restore_current_blog();
        }
    }

    protected function getSiteIds()
    {
        if (function_exists('get_sites')) {
            $args = ['fields' => 'ids'];

            if (function_exists('get_current_network_id')) {
                $args['network_id'] = get_current_network_id();
            }

            return get_sites($args);
        }

        global $wpdb;

        return $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    }

    protected function scheduleEvents()
    {
        if (!wp_next_scheduled('fluent_support_hourly_tasks')) {
            wp_schedule_event(time(), 'hourly', 'fluent_support_hourly_tasks');
        }

        if (!wp_next_scheduled('fluent_support_daily_tasks')) {
            wp_schedule_event(time(), 'daily', 'fluent_support_daily_tasks');
        }

        if (!wp_next_scheduled('fluent_support_weekly_tasks')) {
            wp_schedule_event(time(), 'weekly', 'fluent_support_weekly_tasks');
        }
    }
}
