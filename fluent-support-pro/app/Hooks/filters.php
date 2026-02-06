<?php
/**
 * @var $app FluentSupport\Framework\Foundation\Application
 */


add_filter('fluent_support/email_footer_credit', '__return_empty_string');

add_filter('fluent_support/ticket_custom_fields', function ($fields) {
    return \FluentSupportPro\App\Services\CustomFieldsService::getFieldLabels('admin');
});

add_filter('fluent_support/disabled_ticket_fields', function ($fields) {
    $ticketFormConfig = \FluentSupportPro\App\Services\ProHelper::getTicketFormConfig();
    return $ticketFormConfig['disabled_fields'];
});

add_filter('fluent_support/customer_portal_vars', function ($vars) {
    $customFields = \FluentSupportPro\App\Services\CustomFieldsService::getFieldLabels('public');
    $vars['custom_fields'] = $customFields;

    $vars['has_pro'] = true;

    $vars['has_doc_integration'] = \FluentSupportPro\App\Services\ProHelper::hasDocIntegration();

    if ($disabledFields = apply_filters('fluent_support/disabled_ticket_fields', [])) {
        if (in_array('product_services', $disabledFields)) {
            $vars['support_products'] = [];
        }

        if (in_array('priority', $disabledFields)) {
            $vars['customer_ticket_priorities'] = [];
        }

        if (in_array('file_upload', $disabledFields)) {
            $vars['has_file_upload'] = false;
        }
    }

    $ticketFormConfig = \FluentSupportPro\App\Services\ProHelper::getTicketFormConfig();

    if ($ticketFormConfig['disable_rich_text'] == 'yes') {
        $vars['has_rich_text_editor'] = false;
    }

    if (!empty($vars['i18n'])) {
        $vars['i18n'] = wp_parse_args($ticketFormConfig['field_labels'], $vars['i18n']);
    }

    $ajaxFields = \FluentSupportPro\App\Services\CustomFieldsService::getCustomerRenderers();
    foreach ($customFields as $customField) {
        if (in_array($customField['type'], $ajaxFields)) {
            $vars['has_custom_ajax_fields'] = true;
            return $vars;
        }
    }

    return $vars;
});

$app->addFilter('fluent_support_app_vars', function ($vars) {

    if (
        \FluentSupport\App\Modules\PermissionManager::currentUserCan('fst_run_workflows') ||
        \FluentSupport\App\Modules\PermissionManager::currentUserCan('fst_manage_workflows')
    ) {
        $workflows = \FluentSupportPro\App\Models\Workflow::select(['id', 'title'])
            ->where('trigger_type', 'manual')
            ->where('status', 'published')
            ->get();
        $vars['manual_workflows'] = $workflows;
    }

    $vars['advanced_filter_options'] = \FluentSupportPro\App\Services\ProHelper::getAdvancedFilterOptions();

    $customFields = \FluentSupportPro\App\Services\CustomFieldsService::getFieldLabels('admin');
    $ajaxFields = \FluentSupportPro\App\Services\CustomFieldsService::getCustomerRenderers();
    foreach ($customFields as $customField) {
        if (in_array($customField['type'], $ajaxFields)) {
            $vars['has_custom_ajax_fields'] = true;
            return $vars;
        }
    }
    if(defined('FLUENT_CRM_VERSION')) {
        $vars['fluentcrm_customers'] = (new \FluentCrm\App\Models\Subscriber)->get();
    }
    return $vars;
});

add_filter('fluent_support/user_portal_access_config', function ($config) {
    $ticketFormConfig = \FluentSupportPro\App\Services\ProHelper::getTicketFormConfig();
    
    if (\FluentSupport\Framework\Support\Arr::get($ticketFormConfig, 'submitter_type') == 'allowed_user_roles') {
        $acceptedRoles = \FluentSupport\Framework\Support\Arr::get($ticketFormConfig, 'allowed_user_roles', []);
        if ($acceptedRoles && get_current_user_id()) {
            $user = wp_get_current_user();
            if (!array_intersect($acceptedRoles, (array)$user->roles)) {
                $config['status'] = false;
            }
        }
    }

    return $config;
}, 10, 1);

$app->addFilter('fluent_support/countries', '\FluentSupport\App\Services\Includes\CountryNames@get');

add_filter('fluent_support/dashboard_notice', function ($messages) {
    if (version_compare(FLUENTSUPPORT_MIN_CORE_VERSION, FLUENT_SUPPORT_VERSION, '>')) {
        $updateUrl = admin_url('plugins.php?s=fluent-support&plugin_status=all');
        $html = '<div class="fs_alert_notification fs_alert_warning" style="border-radius: 8px; margin-bottom: 24px; max-width: 1360px; margin-left: auto; margin-right: auto;">
            <div style="display: flex; gap: 8px; align-items: center; padding: 8px 8px 8px 16px;">
                <span style="font-size: 15px; line-height: 18px; flex-shrink: 0;">⚠️</span>
                <p style="flex: 1; margin: 0; font-size: 14px; line-height: 20px; color: #0e121b; letter-spacing: -0.084px;">Fluent Support core plugin needs to be updated for compatibility.</p>
                <a href="'.esc_url($updateUrl).'" style="color: var(--fs-text-primary, #0E121B);font-size: 14px;font-style: normal;font-weight: 500;line-height: 20px;letter-spacing: -0.084px;text-decoration-line: underline;text-decoration-style: solid;">Update Now</a>
            </div>
        </div>';
        $messages .= $html;
    }
    return $messages;
}, 100);


/*
 * In the WP core wp-includes/functions.php file, where the filter is defined for the list of mime types and file extensions
 * In the list the JSON file type/extension is missing. So we had to add this application/JSON type to the list by the hooks
 */
add_filter('mime_types', function($mimes) {
    $mimes['json'] = 'application/json';
    return $mimes;
});

// Below filters will add custom fields to the workflow conditions
$app->addCustomFilter('workflow_ticket_created_supported_conditions', '\FluentSupportPro\App\Services\CustomFieldsService::addCustomFieldToWorkflowTrigger');
$app->addCustomFilter('workflow_response_added_by_customer_supported_conditions', '\FluentSupportPro\App\Services\CustomFieldsService::addCustomFieldToWorkflowTrigger');
$app->addCustomFilter('workflow_ticket_closed_supported_conditions', '\FluentSupportPro\App\Services\CustomFieldsService::addCustomFieldToWorkflowTrigger');
$app->addCustomFilter('workflow_conditions', '\FluentSupportPro\App\Services\CustomFieldsService::addCustomFieldToWorkflowCondition');

//Filters to remove the custom fields from the ticket form for a specific customer
$app->addCustomFilter('custom_field_required_before_ticket_create', '\FluentSupportPro\App\Services\CustomFieldsService::requiredFieldsForCustomer');
//Filters to remove the custom fields from the ticket form for a specific customer based on the conditions
$app->addCustomFilter('custom_field_required_by_conditions_before_ticket_create', '\FluentSupportPro\App\Services\CustomFieldsService::requiredFieldsForCustomerByConditions');
