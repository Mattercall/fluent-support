<?php

/**
 * Enable Query Log
 */
if (!function_exists('fluent_support_enable_query_log')) {
    function fluent_support_enable_query_log()
    {
        defined('SAVEQUERIES') || define('SAVEQUERIES', true);
    }
}

/**
 * Get Query Log
 */
if (!function_exists('fluent_support_get_query_log')) {
    function fluent_support_get_query_log()
    {
        $result = [];
        foreach ((array)$GLOBALS['wpdb']->queries as $key => $query) {
            $result[++$key] = array_combine([
                'query', 'execution_time'
            ], array_slice($query, 0, 2));
        }
        return $result;
    }
}
