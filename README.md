# Order Total Search for WooCommerce

Add the ability to search WooCommerce orders by their total value in the admin orders list.

## Description

This plugin adds a new "Order Total" option to the search filters dropdown in the WooCommerce orders admin screen. When selected, you can enter an order total amount to find orders matching that exact value.

### Important Note

This plugin requires and is fully compatible with WooCommerce's High-Performance Order Storage (HPOS) feature. HPOS is WooCommerce's new order management system that provides better performance and scalability.

### Requirements

- WordPress 5.0 or higher
- WooCommerce 7.0 or higher
- PHP 7.4 or higher
- WooCommerce HPOS must be enabled

### How to Enable HPOS

1. Go to WooCommerce > Settings
2. Click on the Advanced tab
3. Go to Features section
4. Enable "High-Performance Order Storage"
5. Save changes

## Installation

1. Upload the plugin files to the `/wp-content/plugins/swp-order-total-search` directory, or install the plugin through the WordPress plugins screen directly
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Make sure HPOS is enabled in WooCommerce settings
4. Use the Orders screen to search orders by total amount

## Usage

1. Go to WooCommerce > Orders
2. In the search area, select "Order Total" from the dropdown
3. Enter the exact order total amount you want to find
4. Click the Search Orders button

## Developer Hooks

The plugin provides several filters for customization:

```php
// Change the dropdown label
add_filter('swp_order_total_search_filter_label', function($label) {
    return 'Search by Total';
});

// Modify search term before query
add_filter('swp_order_total_search_term', function($term) {
    return round($term);
});

// Modify the WHERE clause
add_filter('swp_order_total_search_where', function($where, $search_term) {
    // Modify the WHERE clause if needed
    return $where;
}, 10, 2);