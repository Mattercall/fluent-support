<?php

namespace FluentSupport\App\Hooks\Handlers;

class AdminStylingHandler
{
    public function init()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue()
    {
        if (!$this->isFluentSupportScreen()) {
            return;
        }

        $styleHandle = 'fluent_support_pro_ui_skin';
        $scriptHandle = 'fluent_support_pro_ui_skin_runtime';

        wp_register_style($styleHandle, false, [], FLUENT_SUPPORT_VERSION);
        wp_enqueue_style($styleHandle);
        wp_add_inline_style($styleHandle, $this->styles());

        wp_register_script($scriptHandle, '', [], FLUENT_SUPPORT_VERSION, true);
        wp_enqueue_script($scriptHandle);
        wp_add_inline_script($scriptHandle, $this->script());
    }

    protected function isFluentSupportScreen()
    {
        if (!is_admin()) {
            return false;
        }

        $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';

        return $page === 'fluent-support';
    }

    protected function styles()
    {
        return <<<'CSS'
#fluent_support_app.fs-pro-skin {
    --fs-pro-primary: #2563eb;
    --fs-pro-primary-dark: #1d4ed8;
    --fs-pro-primary-soft: rgba(37, 99, 235, 0.14);
    --fs-pro-surface: #ffffff;
    --fs-pro-surface-alt: #f8fafc;
    --fs-pro-border: #d7e0ef;
    --fs-pro-border-strong: #b9c8e3;
    --fs-pro-text: #0f172a;
    --fs-pro-text-muted: #475569;
    --fs-pro-success: #16a34a;
    --fs-pro-warning: #f59e0b;
    --fs-pro-danger: #dc2626;
    --fs-pro-radius: 12px;
    --fs-pro-radius-sm: 8px;
    --fs-pro-shadow-soft: 0 18px 35px -25px rgba(15, 23, 42, 0.45);
    --fs-pro-shadow-strong: 0 28px 45px -30px rgba(15, 23, 42, 0.6);
    font-family: "Inter", "SF Pro Text", "Segoe UI", sans-serif;
    color: var(--fs-pro-text);
    background: linear-gradient(180deg, #f3f6fd 0%, #f9fbff 100%);
    min-height: 100%;
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
}

#fluent_support_app.fs-pro-skin *,
#fluent_support_app.fs-pro-skin *::before,
#fluent_support_app.fs-pro-skin *::after {
    box-sizing: border-box;
}

#fluent_support_app.fs-pro-skin h1,
#fluent_support_app.fs-pro-skin h2,
#fluent_support_app.fs-pro-skin h3,
#fluent_support_app.fs-pro-skin h4,
#fluent_support_app.fs-pro-skin h5,
#fluent_support_app.fs-pro-skin h6 {
    font-weight: 600;
    color: var(--fs-pro-text);
    letter-spacing: -0.01em;
}

#fluent_support_app.fs-pro-skin p,
#fluent_support_app.fs-pro-skin span,
#fluent_support_app.fs-pro-skin li,
#fluent_support_app.fs-pro-skin .fs-text,
#fluent_support_app.fs-pro-skin .fs-description {
    color: var(--fs-pro-text-muted);
    line-height: 1.6;
}

#fluent_support_app.fs-pro-skin a {
    color: var(--fs-pro-primary);
    text-decoration: none;
    transition: color 0.2s ease, opacity 0.2s ease;
}

#fluent_support_app.fs-pro-skin a:hover,
#fluent_support_app.fs-pro-skin a:focus {
    color: var(--fs-pro-primary-dark);
    text-decoration: none;
}

#fluent_support_app.fs-pro-skin .button,
#fluent_support_app.fs-pro-skin .components-button,
#fluent_support_app.fs-pro-skin .fs-btn,
#fluent_support_app.fs-pro-skin .fs_button,
#fluent_support_app.fs-pro-skin .wp-core-ui .button {
    font-family: inherit;
    font-weight: 600;
    border-radius: var(--fs-pro-radius-sm);
    border-width: 1px;
    border-style: solid;
    border-color: transparent;
    box-shadow: none;
    transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    line-height: 1.4;
    padding-top: 10px;
    padding-bottom: 10px;
}

#fluent_support_app.fs-pro-skin .button.button-primary,
#fluent_support_app.fs-pro-skin .components-button.is-primary,
#fluent_support_app.fs-pro-skin .fs-btn--primary,
#fluent_support_app.fs-pro-skin .fs_button.button-primary,
#fluent_support_app.fs-pro-skin .wp-core-ui .button-primary {
    background: linear-gradient(135deg, var(--fs-pro-primary) 0%, var(--fs-pro-primary-dark) 100%);
    color: #ffffff;
    border-color: transparent;
    box-shadow: 0 18px 28px -18px rgba(37, 99, 235, 0.55);
}

#fluent_support_app.fs-pro-skin .button.button-primary:hover,
#fluent_support_app.fs-pro-skin .components-button.is-primary:hover,
#fluent_support_app.fs-pro-skin .fs-btn--primary:hover,
#fluent_support_app.fs-pro-skin .wp-core-ui .button-primary:hover {
    box-shadow: 0 22px 36px -20px rgba(37, 99, 235, 0.6);
    transform: translateY(-1px);
}

#fluent_support_app.fs-pro-skin .button.button-primary:focus,
#fluent_support_app.fs-pro-skin .components-button.is-primary:focus,
#fluent_support_app.fs-pro-skin .fs-btn--primary:focus,
#fluent_support_app.fs-pro-skin .wp-core-ui .button-primary:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.24);
}

#fluent_support_app.fs-pro-skin .button:not(.button-primary),
#fluent_support_app.fs-pro-skin .components-button:not(.is-primary),
#fluent_support_app.fs-pro-skin .fs-btn--secondary,
#fluent_support_app.fs-pro-skin .fs_button.is-secondary,
#fluent_support_app.fs-pro-skin .wp-core-ui .button-secondary {
    background: #ffffff;
    color: var(--fs-pro-primary-dark);
    border-color: var(--fs-pro-border-strong);
    box-shadow: 0 1px 1px rgba(15, 23, 42, 0.08);
}

#fluent_support_app.fs-pro-skin .button:not(.button-primary):hover,
#fluent_support_app.fs-pro-skin .components-button:not(.is-primary):hover,
#fluent_support_app.fs-pro-skin .fs-btn--secondary:hover,
#fluent_support_app.fs-pro-skin .wp-core-ui .button-secondary:hover {
    border-color: var(--fs-pro-primary);
    background: var(--fs-pro-primary-soft);
    color: var(--fs-pro-primary-dark);
}

#fluent_support_app.fs-pro-skin .fs_dashboard_box,
#fluent_support_app.fs-pro-skin .fs_box,
#fluent_support_app.fs-pro-skin .fs-card,
#fluent_support_app.fs-pro-skin .fs_panel,
#fluent_support_app.fs-pro-skin .ticket-card,
#fluent_support_app.fs-pro-skin .dashboard-card,
#fluent_support_app.fs-pro-skin .report-card,
#fluent_support_app.fs-pro-skin .components-card,
#fluent_support_app.fs-pro-skin .metabox-holder .postbox {
    background-color: var(--fs-pro-surface);
    border: 1px solid var(--fs-pro-border);
    border-radius: var(--fs-pro-radius);
    box-shadow: var(--fs-pro-shadow-soft);
    overflow: hidden;
}

#fluent_support_app.fs-pro-skin .fs_box_header,
#fluent_support_app.fs-pro-skin .fs-card__header,
#fluent_support_app.fs-pro-skin .card-header,
#fluent_support_app.fs-pro-skin .components-card__header,
#fluent_support_app.fs-pro-skin .metabox-holder .postbox h2.hndle {
    border-bottom: 1px solid var(--fs-pro-border);
    border-top-left-radius: var(--fs-pro-radius);
    border-top-right-radius: var(--fs-pro-radius);
    padding: 18px 24px;
    background: rgba(255, 255, 255, 0.82);
    color: var(--fs-pro-text);
    font-size: 16px;
}

#fluent_support_app.fs-pro-skin .fs_box_body,
#fluent_support_app.fs-pro-skin .fs-card__body,
#fluent_support_app.fs-pro-skin .card-body,
#fluent_support_app.fs-pro-skin .components-card__body,
#fluent_support_app.fs-pro-skin .metabox-holder .inside {
    border-bottom-left-radius: var(--fs-pro-radius);
    border-bottom-right-radius: var(--fs-pro-radius);
    padding: 20px 24px;
    background: var(--fs-pro-surface);
}

#fluent_support_app.fs-pro-skin .fs-section-title,
#fluent_support_app.fs-pro-skin .fs_section_title,
#fluent_support_app.fs-pro-skin .fs-page-header,
#fluent_support_app.fs-pro-skin .fs_header {
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--fs-pro-border);
}

#fluent_support_app.fs-pro-skin table.wp-list-table,
#fluent_support_app.fs-pro-skin table.fs-table,
#fluent_support_app.fs-pro-skin table.fs_data_table {
    background: var(--fs-pro-surface);
    border: 1px solid var(--fs-pro-border);
    border-radius: var(--fs-pro-radius);
    overflow: hidden;
    box-shadow: var(--fs-pro-shadow-soft);
}

#fluent_support_app.fs-pro-skin table.wp-list-table thead,
#fluent_support_app.fs-pro-skin table.fs-table thead,
#fluent_support_app.fs-pro-skin table.fs_data_table thead {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(37, 99, 235, 0.04));
    color: var(--fs-pro-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 12px;
}

#fluent_support_app.fs-pro-skin table.wp-list-table thead th,
#fluent_support_app.fs-pro-skin table.fs-table thead th,
#fluent_support_app.fs-pro-skin table.fs_data_table thead th {
    border-bottom: 1px solid var(--fs-pro-border);
    font-weight: 600;
    padding: 14px 18px;
}

#fluent_support_app.fs-pro-skin table.wp-list-table tbody tr,
#fluent_support_app.fs-pro-skin table.fs-table tbody tr,
#fluent_support_app.fs-pro-skin table.fs_data_table tbody tr {
    border-bottom: 1px solid var(--fs-pro-border);
    transition: background-color 0.15s ease, box-shadow 0.15s ease;
}

#fluent_support_app.fs-pro-skin table.wp-list-table tbody tr:hover,
#fluent_support_app.fs-pro-skin table.fs-table tbody tr:hover,
#fluent_support_app.fs-pro-skin table.fs_data_table tbody tr:hover {
    background-color: rgba(37, 99, 235, 0.05);
}

#fluent_support_app.fs-pro-skin table.wp-list-table tbody td,
#fluent_support_app.fs-pro-skin table.fs-table tbody td,
#fluent_support_app.fs-pro-skin table.fs_data_table tbody td {
    padding: 14px 18px;
    color: var(--fs-pro-text);
}

#fluent_support_app.fs-pro-skin input[type="text"],
#fluent_support_app.fs-pro-skin input[type="email"],
#fluent_support_app.fs-pro-skin input[type="search"],
#fluent_support_app.fs-pro-skin input[type="password"],
#fluent_support_app.fs-pro-skin select,
#fluent_support_app.fs-pro-skin textarea,
#fluent_support_app.fs-pro-skin .fs-input,
#fluent_support_app.fs-pro-skin .components-text-control__input,
#fluent_support_app.fs-pro-skin .components-select-control__input,
#fluent_support_app.fs-pro-skin .components-combobox-control__input {
    background: #ffffff;
    border: 1px solid var(--fs-pro-border);
    border-radius: var(--fs-pro-radius-sm);
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.06);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    padding: 10px 12px;
    color: var(--fs-pro-text);
}

#fluent_support_app.fs-pro-skin input[type="text"]:focus,
#fluent_support_app.fs-pro-skin input[type="email"]:focus,
#fluent_support_app.fs-pro-skin input[type="search"]:focus,
#fluent_support_app.fs-pro-skin input[type="password"]:focus,
#fluent_support_app.fs-pro-skin select:focus,
#fluent_support_app.fs-pro-skin textarea:focus,
#fluent_support_app.fs-pro-skin .fs-input:focus,
#fluent_support_app.fs-pro-skin .components-text-control__input:focus,
#fluent_support_app.fs-pro-skin .components-select-control__input:focus {
    border-color: var(--fs-pro-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
    outline: none;
}

#fluent_support_app.fs-pro-skin label,
#fluent_support_app.fs-pro-skin .form-label,
#fluent_support_app.fs-pro-skin .components-base-control__label {
    font-weight: 600;
    color: var(--fs-pro-text-muted);
}

#fluent_support_app.fs-pro-skin .notice,
#fluent_support_app.fs-pro-skin .fs-alert,
#fluent_support_app.fs-pro-skin .fs_notice {
    border-radius: var(--fs-pro-radius-sm);
    border: 1px solid var(--fs-pro-border);
    color: var(--fs-pro-text);
    box-shadow: 0 12px 25px -20px rgba(15, 23, 42, 0.35);
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(248, 250, 252, 0.96) 100%);
    padding: 16px 20px;
}

#fluent_support_app.fs-pro-skin .notice.notice-success,
#fluent_support_app.fs-pro-skin .fs-alert.fs-alert-success {
    border-color: rgba(22, 163, 74, 0.2);
    background: rgba(22, 163, 74, 0.08);
    color: var(--fs-pro-success);
}

#fluent_support_app.fs-pro-skin .notice.notice-warning,
#fluent_support_app.fs-pro-skin .fs-alert.fs-alert-warning {
    border-color: rgba(245, 158, 11, 0.2);
    background: rgba(245, 158, 11, 0.08);
    color: var(--fs-pro-warning);
}

#fluent_support_app.fs-pro-skin .notice.notice-error,
#fluent_support_app.fs-pro-skin .fs-alert.fs-alert-danger {
    border-color: rgba(220, 38, 38, 0.2);
    background: rgba(220, 38, 38, 0.08);
    color: var(--fs-pro-danger);
}

#fluent_support_app.fs-pro-skin .fs-badge,
#fluent_support_app.fs-pro-skin .fs_status,
#fluent_support_app.fs-pro-skin .status-badge,
#fluent_support_app.fs-pro-skin .fs_tag,
#fluent_support_app.fs-pro-skin .fs-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    background: var(--fs-pro-primary-soft);
    color: var(--fs-pro-primary-dark);
}

#fluent_support_app.fs-pro-skin .fs-badge.fs-success,
#fluent_support_app.fs-pro-skin .fs_status.success,
#fluent_support_app.fs-pro-skin .status-badge.success {
    background: rgba(22, 163, 74, 0.12);
    color: var(--fs-pro-success);
}

#fluent_support_app.fs-pro-skin .fs-badge.fs-warning,
#fluent_support_app.fs-pro-skin .fs_status.warning,
#fluent_support_app.fs-pro-skin .status-badge.warning {
    background: rgba(245, 158, 11, 0.14);
    color: var(--fs-pro-warning);
}

#fluent_support_app.fs-pro-skin .fs-badge.fs-danger,
#fluent_support_app.fs-pro-skin .fs_status.danger,
#fluent_support_app.fs-pro-skin .status-badge.danger {
    background: rgba(220, 38, 38, 0.14);
    color: var(--fs-pro-danger);
}

#fluent_support_app.fs-pro-skin .fs-toolbar,
#fluent_support_app.fs-pro-skin .fs_filter_bar,
#fluent_support_app.fs-pro-skin .fs_filters,
#fluent_support_app.fs-pro-skin .fs-sub-nav {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--fs-pro-border);
    border-radius: var(--fs-pro-radius);
    padding: 14px 18px;
    box-shadow: var(--fs-pro-shadow-soft);
    backdrop-filter: blur(6px);
}

#fluent_support_app.fs-pro-skin .fs-toolbar h2,
#fluent_support_app.fs-pro-skin .fs_toolbar_title,
#fluent_support_app.fs-pro-skin .fs_filter_bar .fs-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--fs-pro-text);
}

#fluent_support_app.fs-pro-skin .fs_empty_state,
#fluent_support_app.fs-pro-skin .fs-empty,
#fluent_support_app.fs-pro-skin .fs_state_empty {
    background: var(--fs-pro-surface);
    border: 1px dashed var(--fs-pro-border-strong);
    border-radius: var(--fs-pro-radius);
    padding: 40px;
    text-align: center;
    box-shadow: none;
}

#fluent_support_app.fs-pro-skin .fs_empty_state h2,
#fluent_support_app.fs-pro-skin .fs-empty h2 {
    font-size: 20px;
    margin-bottom: 8px;
}

#fluent_support_app.fs-pro-skin .fs_empty_state p,
#fluent_support_app.fs-pro-skin .fs-empty p {
    color: var(--fs-pro-text-muted);
}

#fluent_support_app.fs-pro-skin .fs-modal,
#fluent_support_app.fs-pro-skin .modal,
#fluent_support_app.fs-pro-skin .components-modal__frame {
    border-radius: var(--fs-pro-radius);
    border: 1px solid var(--fs-pro-border);
    box-shadow: var(--fs-pro-shadow-strong);
    background: var(--fs-pro-surface);
}

#fluent_support_app.fs-pro-skin .fs-modal__header,
#fluent_support_app.fs-pro-skin .modal-header,
#fluent_support_app.fs-pro-skin .components-modal__header {
    border-bottom: 1px solid var(--fs-pro-border);
    padding: 20px 24px;
    background: rgba(248, 250, 252, 0.9);
}

#fluent_support_app.fs-pro-skin .fs-modal__body,
#fluent_support_app.fs-pro-skin .modal-body,
#fluent_support_app.fs-pro-skin .components-modal__content {
    padding: 24px;
}

#fluent_support_app.fs-pro-skin .fs-modal__footer,
#fluent_support_app.fs-pro-skin .modal-footer,
#fluent_support_app.fs-pro-skin .components-modal__footer {
    border-top: 1px solid var(--fs-pro-border);
    padding: 18px 24px;
    background: rgba(248, 250, 252, 0.9);
}

#fluent_support_app.fs-pro-skin .fs-tab-nav,
#fluent_support_app.fs-pro-skin .fs_tab_nav,
#fluent_support_app.fs-pro-skin .fs-tabs,
#fluent_support_app.fs-pro-skin .nav-tab-wrapper {
    border-bottom: 1px solid var(--fs-pro-border);
    gap: 8px;
}

#fluent_support_app.fs-pro-skin .fs-tab-nav .fs-tab,
#fluent_support_app.fs-pro-skin .fs_tab_nav .nav-tab,
#fluent_support_app.fs-pro-skin .nav-tab-wrapper .nav-tab {
    background: transparent;
    border-radius: var(--fs-pro-radius-sm) var(--fs-pro-radius-sm) 0 0;
    border: 1px solid transparent;
    color: var(--fs-pro-text-muted);
    padding: 10px 16px;
    font-weight: 600;
    transition: color 0.2s ease, border-color 0.2s ease, background 0.2s ease;
}

#fluent_support_app.fs-pro-skin .fs-tab-nav .fs-tab.is-active,
#fluent_support_app.fs-pro-skin .fs_tab_nav .nav-tab.nav-tab-active,
#fluent_support_app.fs-pro-skin .nav-tab-wrapper .nav-tab.nav-tab-active {
    color: var(--fs-pro-primary-dark);
    border-color: var(--fs-pro-border) var(--fs-pro-border) #ffffff;
    background: var(--fs-pro-surface);
}

#fluent_support_app.fs-pro-skin .fs-meta,
#fluent_support_app.fs-pro-skin .fs-meta-info,
#fluent_support_app.fs-pro-skin .fs-info-box {
    background: rgba(15, 23, 42, 0.02);
    border: 1px solid rgba(15, 23, 42, 0.05);
    border-radius: var(--fs-pro-radius-sm);
    padding: 14px 18px;
    color: var(--fs-pro-text-muted);
}

#fluent_support_app.fs-pro-skin .fs-meta strong,
#fluent_support_app.fs-pro-skin .fs-meta-info strong,
#fluent_support_app.fs-pro-skin .fs-info-box strong {
    color: var(--fs-pro-text);
}

#fluent_support_app.fs-pro-skin .fs-activity-log__item,
#fluent_support_app.fs-pro-skin .fs_timeline_item,
#fluent_support_app.fs-pro-skin .fs_note_item {
    border-left: 3px solid var(--fs-pro-primary-soft);
    padding: 16px 20px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: var(--fs-pro-radius-sm);
    box-shadow: 0 16px 28px -24px rgba(15, 23, 42, 0.5);
}

#fluent_support_app.fs-pro-skin .fs-activity-log__item + .fs-activity-log__item,
#fluent_support_app.fs-pro-skin .fs_timeline_item + .fs_timeline_item,
#fluent_support_app.fs-pro-skin .fs_note_item + .fs_note_item {
    margin-top: 12px;
}

#fluent_support_app.fs-pro-skin .fs-search,
#fluent_support_app.fs-pro-skin .fs_search_box,
#fluent_support_app.fs-pro-skin .fs-list-search {
    border-radius: var(--fs-pro-radius);
    border: 1px solid var(--fs-pro-border);
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.05);
}

#fluent_support_app.fs-pro-skin .fs-search input,
#fluent_support_app.fs-pro-skin .fs_search_box input,
#fluent_support_app.fs-pro-skin .fs-list-search input {
    border: none;
    box-shadow: none;
    padding: 0;
}

#fluent_support_app.fs-pro-skin .fs-search input:focus,
#fluent_support_app.fs-pro-skin .fs_search_box input:focus,
#fluent_support_app.fs-pro-skin .fs-list-search input:focus {
    outline: none;
    box-shadow: none;
}

#fluent_support_app.fs-pro-skin .fs-pagination,
#fluent_support_app.fs-pro-skin .tablenav-pages,
#fluent_support_app.fs-pro-skin .fs_pagination {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 0;
    color: var(--fs-pro-text-muted);
}

#fluent_support_app.fs-pro-skin .fs-pagination .page-numbers,
#fluent_support_app.fs-pro-skin .tablenav-pages .page-numbers,
#fluent_support_app.fs-pro-skin .fs_pagination a,
#fluent_support_app.fs-pro-skin .fs_pagination span {
    border-radius: 999px;
    padding: 6px 12px;
    border: 1px solid transparent;
    color: var(--fs-pro-text-muted);
    transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

#fluent_support_app.fs-pro-skin .fs-pagination .page-numbers.current,
#fluent_support_app.fs-pro-skin .tablenav-pages .page-numbers.current,
#fluent_support_app.fs-pro-skin .fs_pagination .current {
    background: var(--fs-pro-primary);
    border-color: var(--fs-pro-primary);
    color: #ffffff;
    box-shadow: 0 12px 24px -18px rgba(37, 99, 235, 0.6);
}

#fluent_support_app.fs-pro-skin .fs-pagination .page-numbers:hover,
#fluent_support_app.fs-pro-skin .tablenav-pages .page-numbers:hover,
#fluent_support_app.fs-pro-skin .fs_pagination a:hover {
    color: var(--fs-pro-primary-dark);
    border-color: var(--fs-pro-primary);
    background: var(--fs-pro-primary-soft);
}

#fluent_support_app.fs-pro-skin .components-notice.is-warning,
#fluent_support_app.fs-pro-skin .components-notice.is-error,
#fluent_support_app.fs-pro-skin .components-notice.is-success {
    border-radius: var(--fs-pro-radius-sm);
    border-width: 1px;
}

#fluent_support_app.fs-pro-skin .components-notice.is-warning {
    border-color: rgba(245, 158, 11, 0.24);
    background: rgba(245, 158, 11, 0.12);
    color: var(--fs-pro-warning);
}

#fluent_support_app.fs-pro-skin .components-notice.is-error {
    border-color: rgba(220, 38, 38, 0.24);
    background: rgba(220, 38, 38, 0.12);
    color: var(--fs-pro-danger);
}

#fluent_support_app.fs-pro-skin .components-notice.is-success {
    border-color: rgba(22, 163, 74, 0.24);
    background: rgba(22, 163, 74, 0.12);
    color: var(--fs-pro-success);
}

#fluent_support_app.fs-pro-skin .components-panel__body,
#fluent_support_app.fs-pro-skin .components-panel__row {
    border: 1px solid var(--fs-pro-border);
    border-radius: var(--fs-pro-radius-sm);
    background: rgba(255, 255, 255, 0.95);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
    margin-bottom: 16px;
    padding: 16px;
}

#fluent_support_app.fs-pro-skin .components-panel__body:last-child,
#fluent_support_app.fs-pro-skin .components-panel__row:last-child {
    margin-bottom: 0;
}

#fluent_support_app.fs-pro-skin .components-menu-group,
#fluent_support_app.fs-pro-skin .components-dropdown-menu {
    border-radius: var(--fs-pro-radius-sm);
    border: 1px solid var(--fs-pro-border);
    box-shadow: var(--fs-pro-shadow-soft);
}

#fluent_support_app.fs-pro-skin .components-dropdown-menu__menu-item,
#fluent_support_app.fs-pro-skin .components-menu-item__button {
    border-radius: var(--fs-pro-radius-sm);
    transition: background 0.2s ease, color 0.2s ease;
}

#fluent_support_app.fs-pro-skin .components-dropdown-menu__menu-item:hover,
#fluent_support_app.fs-pro-skin .components-menu-item__button:hover,
#fluent_support_app.fs-pro-skin .components-dropdown-menu__menu-item:focus,
#fluent_support_app.fs-pro-skin .components-menu-item__button:focus {
    background: var(--fs-pro-primary-soft);
    color: var(--fs-pro-primary-dark);
}

#fluent_support_app.fs-pro-skin .components-base-control__help,
#fluent_support_app.fs-pro-skin .description,
#fluent_support_app.fs-pro-skin .fs_field_description {
    color: var(--fs-pro-text-muted);
    font-size: 12px;
}

#fluent_support_app.fs-pro-skin .components-checkbox-control__label,
#fluent_support_app.fs-pro-skin .components-toggle-control__label,
#fluent_support_app.fs-pro-skin .components-radio-control__label {
    color: var(--fs-pro-text);
}

#fluent_support_app.fs-pro-skin .components-checkbox-control__input:focus + span::before,
#fluent_support_app.fs-pro-skin .components-toggle-control__input:focus + span::before,
#fluent_support_app.fs-pro-skin .components-radio-control__input:focus + span::before {
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.22);
}

#fluent_support_app.fs-pro-skin .fs-breadcrumb,
#fluent_support_app.fs-pro-skin .fs-breadcrumbs {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--fs-pro-text-muted);
    margin-bottom: 12px;
}

#fluent_support_app.fs-pro-skin .fs-breadcrumb a,
#fluent_support_app.fs-pro-skin .fs-breadcrumbs a {
    color: var(--fs-pro-primary-dark);
}

#fluent_support_app.fs-pro-skin .fs-card__footer,
#fluent_support_app.fs-pro-skin .card-footer,
#fluent_support_app.fs-pro-skin .fs_panel__footer {
    border-top: 1px solid var(--fs-pro-border);
    padding: 18px 24px;
    background: rgba(248, 250, 252, 0.94);
}

#fluent_support_app.fs-pro-skin .fs-card__footer .button + .button,
#fluent_support_app.fs-pro-skin .card-footer .button + .button,
#fluent_support_app.fs-pro-skin .fs_panel__footer .button + .button {
    margin-left: 10px;
}

#fluent_support_app.fs-pro-skin .fs-tags,
#fluent_support_app.fs-pro-skin .fs_tag_list,
#fluent_support_app.fs-pro-skin .fs-label-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

#fluent_support_app.fs-pro-skin .fs-tags .fs_tag,
#fluent_support_app.fs-pro-skin .fs_tag_list .fs_tag,
#fluent_support_app.fs-pro-skin .fs-label-group .fs_tag {
    padding: 6px 12px;
    background: rgba(37, 99, 235, 0.08);
    border-radius: 999px;
    border: 1px solid rgba(37, 99, 235, 0.14);
    color: var(--fs-pro-primary-dark);
    font-weight: 600;
}

#fluent_support_app.fs-pro-skin .fs-table-actions,
#fluent_support_app.fs-pro-skin .fs_table_actions,
#fluent_support_app.fs-pro-skin .tablenav.top {
    margin-bottom: 18px;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--fs-pro-border);
    border-radius: var(--fs-pro-radius);
    padding: 12px 16px;
    box-shadow: var(--fs-pro-shadow-soft);
}

#fluent_support_app.fs-pro-skin .tablenav.top .actions,
#fluent_support_app.fs-pro-skin .tablenav.bottom .actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

#fluent_support_app.fs-pro-skin .fs-summary,
#fluent_support_app.fs-pro-skin .fs_data_summary,
#fluent_support_app.fs-pro-skin .fs-overview {
    background: linear-gradient(180deg, rgba(37, 99, 235, 0.12) 0%, rgba(37, 99, 235, 0.02) 100%);
    border: 1px solid rgba(37, 99, 235, 0.18);
    border-radius: var(--fs-pro-radius);
    padding: 24px;
    box-shadow: var(--fs-pro-shadow-soft);
}

#fluent_support_app.fs-pro-skin .fs-summary strong,
#fluent_support_app.fs-pro-skin .fs_data_summary strong,
#fluent_support_app.fs-pro-skin .fs-overview strong {
    color: var(--fs-pro-primary-dark);
}

#fluent_support_app.fs-pro-skin .fs-chart,
#fluent_support_app.fs-pro-skin .fs_chart_wrapper {
    background: var(--fs-pro-surface);
    border: 1px solid var(--fs-pro-border);
    border-radius: var(--fs-pro-radius);
    padding: 24px;
    box-shadow: var(--fs-pro-shadow-soft);
}

#fluent_support_app.fs-pro-skin .fs-chart canvas,
#fluent_support_app.fs-pro-skin .fs_chart_wrapper canvas {
    border-radius: var(--fs-pro-radius-sm);
}

#fluent_support_app.fs-pro-skin .components-spinner,
#fluent_support_app.fs-pro-skin .spinner {
    border-color: var(--fs-pro-primary) transparent var(--fs-pro-primary) transparent;
}

#fluent_support_app.fs-pro-skin .components-spinner::before,
#fluent_support_app.fs-pro-skin .spinner::before {
    border-color: inherit;
}
CSS;
    }
    protected function script()
    {
        return <<<'JS'
(function () {
    var applySkin = function (root) {
        if (!root || !(root instanceof HTMLElement)) {
            return;
        }

        if (!root.classList.contains('fs-pro-skin')) {
            root.classList.add('fs-pro-skin');
        }
    };

    var rootId = 'fluent_support_app';
    var root = document.getElementById(rootId);

    applySkin(root);

    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (!mutation.addedNodes) {
                return;
            }
            mutation.addedNodes.forEach(function (node) {
                if (!(node instanceof HTMLElement)) {
                    return;
                }
                if (node.id === rootId) {
                    applySkin(node);
                    return;
                }
                if (node.querySelector) {
                    var potential = node.querySelector('#' + rootId);
                    if (potential) {
                        applySkin(potential);
                    }
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
})();
JS;
    }
}
