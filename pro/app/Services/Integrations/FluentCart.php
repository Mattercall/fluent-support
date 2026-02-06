<?php

namespace FluentSupportPro\App\Services\Integrations;

/**
 * FluentCart Integration for FluentSupport Pro
 *
 * Provides custom field and workflow integration for FluentCart products and orders.
 */
class FluentCart
{
    /**
     * Initialize FluentCart custom fields integration
     */
    public function boot()
    {
        // Register custom field types for FluentCart
        $this->registerCustomFieldTypes();

        // Setup field option handlers
        $this->setupFieldOptionHandlers();

        // Setup field display handlers
        $this->setupFieldDisplayHandlers();
    }

    /**
     * Register FluentCart custom field types
     */
    private function registerCustomFieldTypes()
    {
        add_filter('fluent_support/custom_field_types', function ($fieldTypes) {
            $fieldTypes['fct_products'] = [
                'is_custom'   => true,
                'is_remote'   => true,
                'custom_text' => __('FluentCart products will be shown at the ticket form', 'fluent-support-pro'),
                'type'        => 'fct_products',
                'label'       => __('FluentCart Products', 'fluent-support-pro'),
                'value_type'  => 'number'
            ];

            $fieldTypes['fct_orders'] = [
                'is_custom'   => true,
                'is_remote'   => true,
                'custom_text' => __('FluentCart orders will be shown at the ticket form', 'fluent-support-pro'),
                'type'        => 'fct_orders',
                'label'       => __('FluentCart Orders', 'fluent-support-pro'),
                'value_type'  => 'number'
            ];

            return $fieldTypes;
        });
    }

    /**
     * Setup field option handlers for dropdowns
     */
    private function setupFieldOptionHandlers()
    {
        // Handle FluentCart Orders dropdown options
        add_filter('fluent_support/render_custom_field_options_fct_orders', [$this, 'renderOrderOptions'], 10, 2);

        // Handle FluentCart Products dropdown options
        add_filter('fluent_support/render_custom_field_options_fct_products', [$this, 'renderProductOptions'], 10, 2);
    }

    /**
     * Setup field display handlers for showing values
     */
    private function setupFieldDisplayHandlers()
    {
        // Handle how order values are displayed
        add_filter('fluent_support/custom_field_render_fct_orders', [$this, 'renderOrderValue'], 10, 2);

        // Handle how product values are displayed
        add_filter('fluent_support/custom_field_render_fct_products', [$this, 'renderProductValue'], 10, 2);

    }

    /**
     * Render order dropdown options
     */
    public function renderOrderOptions($field, $customer)
    {
        $orders = $this->getCustomerOrders($customer);

        if (!$orders || $orders->isEmpty()) {
            return $field;
        }

        $options = [];
        foreach ($orders as $order) {
            $options[] = [
                'id'    => strval($order->id),
                'title' => sprintf(__('Order #%d - %s', 'fluent-support-pro'), $order->id, $order->created_at->format('M d, Y'))
            ];
        }

        $field['type'] = 'select';
        $field['filterable'] = true;
        $field['rendered'] = true;
        $field['options'] = $options;

        return $field;
    }

    /**
     * Render product dropdown options
     */
    public function renderProductOptions($field, $customer)
    {
        $products = $this->getFluentCartProducts();

        if (!$products) {
            return false;
        }

        $options = [];
        foreach ($products as $product) {
            $options[] = [
                'id'    => strval($product->ID),
                'title' => $product->post_title
            ];
        }

        $field['type'] = 'select';
        $field['rendered'] = true;
        $field['filterable'] = true;
        $field['options'] = $options;

        return $field;
    }

    /**
     * Render order value display
     */
    public function renderOrderValue($value, $scope = 'admin')
    {
        if (!is_numeric($value)) {
            return $value; // Non-numeric values are returned as-is
        }

        $orderId    = absint($value);
        $orderTitle = sprintf(__('Order #%d', 'fluent-support-pro'), $orderId);
        $order      = null;

        // Try to fetch the order model if available
        if (class_exists('\FluentCart\App\Models\Order')) {
            try {
                $order = \FluentCart\App\Models\Order::find($orderId);

                if ($order) {
                    $formattedDate = $order->created_at
                        ? $order->created_at->format('M d, Y')
                        : '';

                    $orderTitle = sprintf(
                        __('Order #%d%s', 'fluent-support-pro'),
                        $orderId,
                        $formattedDate ? ' - ' . $formattedDate : ''
                    );
                }
            } catch (\Throwable $e) {
                // Log if needed, but don't break rendering
            }
        }

        // Determine link based on scope and order availability
        if ($scope === 'admin') {
            $adminUrl = admin_url('admin.php?page=fluent-cart#/orders/' . $orderId . '/view');

            return sprintf(
                '<a target="_blank" rel="nofollow" href="%s">%s</a>',
                esc_url($adminUrl),
                esc_html($orderTitle)
            );
        }

        if ($order && method_exists($order, 'getViewUrl')) {
            $customerUrl = $order->getViewUrl('customer');

            return sprintf(
                '<a target="_blank" rel="nofollow" href="%s">%s</a>',
                esc_url($customerUrl),
                esc_html($orderTitle)
            );
        }

        return esc_html($orderTitle);
    }


    /**
     * Render product value display
     */
    public function renderProductValue($value, $scope = 'admin')
    {
        if (!is_numeric($value)) {
            return $value; // Non-numeric values are returned as-is
        }

        $productId = absint($value);
        $productTitle = sprintf(__('Product #%d', 'fluent-support-pro'), $productId);
        $product = null;

        // Try to get product details for better display
        if (class_exists('\FluentCart\App\Models\Product')) {
            try {
                $product = \FluentCart\App\Models\Product::find($productId);
                if ($product) {
                    $productTitle = $product->post_title;
                }
            } catch (\Exception $e) {
                // Fallback to WordPress post
                $wpProduct = get_post($productId);
                if ($wpProduct && $wpProduct instanceof \WP_Post) {
                    $product = $wpProduct;
                    $productTitle = $wpProduct->post_title;
                }
            }
        }

        if ($scope == 'admin') {
            $adminUrl = admin_url('admin.php?page=fluent-cart#/products/' . $productId);
            return '<a target="_blank" rel="nofollow" href="' . $adminUrl . '">' . $productTitle . '</a>';
        }

        // For customer/non-admin scope, use frontend product URL
        if ($product) {
            $customerUrl = get_permalink($productId);
            if ($customerUrl) {
                return '<a target="_blank" rel="nofollow" href="' . $customerUrl . '">' . $productTitle . '</a>';
            }
        }

        return $productTitle;
    }

    /**
     * Get all FluentCart products
     */
    private function getFluentCartProducts()
    {
        if (!class_exists('\FluentCart\App\Models\Product')) {
            return null;
        }

        try {
            return \FluentCart\App\Models\Product::where('post_status', 'publish')
                ->orderBy('post_title', 'ASC')
                ->get();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get customer's FluentCart orders
     */
    private function getCustomerOrders($customer)
    {
        if (!class_exists('\FluentCart\App\Models\Order')) {
            return null;
        }

        try {
            // Try to find orders by user ID first
            if ($customer->user_id) {
                return $this->getOrdersByUserId($customer->user_id);
            }

            // Fallback to email if no user ID
            return $this->getOrdersByEmail($customer->email);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get orders by WordPress user ID
     */
    private function getOrdersByUserId($userId)
    {
        return \FluentCart\App\Models\Order::whereHas('customer', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->select(['id', 'status', 'created_at', 'total_amount', 'currency'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get orders by customer email
     */
    private function getOrdersByEmail($email)
    {
        return \FluentCart\App\Models\Order::whereHas('customer', function ($query) use ($email) {
            $query->where('email', $email);
        })
            ->select(['id', 'status', 'created_at', 'total_amount', 'currency'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Add FluentCart custom fields to workflow conditions
     */
    public function addToWorkflow($customField, $key)
    {
        $options = [];

        if ($key == 'fct_products') {
            $options = $this->getProductWorkflowOptions();
        } elseif ($key == 'fct_orders') {
            $options = $this->getOrderWorkflowOptions();
        }

        return [
            'title'     => $customField['label'],
            'data_type' => 'single_dropdown',
            'group'     => 'Custom Fields',
            'options'   => $options
        ];
    }

    /**
     * Get product options for workflow
     */
    private function getProductWorkflowOptions()
    {
        $options = [];
        $products = $this->getFluentCartProducts();

        if ($products) {
            foreach ($products as $product) {
                $options[$product->ID] = $product->post_title;
            }
        }

        return $options;
    }

    /**
     * Get order options for workflow
     */
    private function getOrderWorkflowOptions()
    {
        $options = [];

        if (!class_exists('\FluentCart\App\Models\Order')) {
            return $options;
        }

        $orders = \FluentCart\App\Models\Order::orderByDesc('created_at')
            ->get();

        foreach ($orders as $order) {
            $options[$order->id] = sprintf(__('Order #%d - %s', 'fluent-support-pro'), $order->id, $order->created_at->format('M d, Y'));
        }

        return $options;
    }

}
