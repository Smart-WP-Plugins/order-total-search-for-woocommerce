<?php
/**
 * Plugin Name: Order Total Search for WooCommerce
 * Description: Adds a dropdown filter to search WooCommerce orders by total value in admin. Requires WooCommerce HPOS to be enabled.
 * Version: 1.0.0
 * Author: Smart WP Plugins
 * Author URI: http://smartwpplugins.com/
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: swp-order-total-search
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * WC requires at least: 7.0
 * WC tested up to: 9.0
 * Last Updated: 2025-08-22 20:03:52
 *
 * @package SWP_Order_Total_Search
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Declare HPOS compatibility
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * Main plugin class
 */
class SWP_Order_Total_Search {

	/**
	 * Plugin version
	 */
	const VERSION = '1.0.0';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Initialize plugin
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Initialize plugin
	 */
	public function init() {
		// Check if WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );

			return;
		}

		// Check if HPOS is enabled
		if ( ! $this->is_hpos_enabled() ) {
			add_action( 'admin_notices', array( $this, 'hpos_disabled_notice' ) );

			return;
		}

		// Add filters
		add_filter( 'woocommerce_hpos_admin_search_filters', array( $this, 'add_total_search_filter' ) );
		add_filter( 'woocommerce_hpos_generate_where_for_search_filter', array( $this, 'handle_total_search' ), 10, 4 );
	}

	/**
	 * Check if HPOS is enabled
	 *
	 * @return bool
	 */
	private function is_hpos_enabled() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\OrderUtil::class ) ) {
			return \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
		}

		return false;
	}

	/**
	 * Add total search option to the search filters dropdown
	 * Places the option at the beginning of the dropdown list
	 *
	 * @param array $options Existing filter options
	 *
	 * @return array Modified filter options
	 */
	public function add_total_search_filter( $options ) {
		$label = apply_filters(
			'swp_order_total_search_filter_label',
			__( 'Order Total', 'swp-order-total-search' )
		);

		return array( 'order_total' => $label ) + $options;
	}

	/**
	 * Handle the order total search
	 *
	 * @param string $where The WHERE clause
	 * @param string $search_term The search term
	 * @param string $search_filter The selected filter
	 * @param object $query The WP_Query object
	 *
	 * @return string Modified WHERE clause
	 */
	public function handle_total_search( $where, $search_term, $search_filter, $query ) {
		global $wpdb;

		if ( 'order_total' === $search_filter ) {
			// Make sure search term is numeric
			$search_term = floatval( preg_replace( '/[^0-9.]/', '', $search_term ) );

			// Allow modification of the search term
			$search_term = apply_filters( 'swp_order_total_search_term', $search_term );

			if ( $search_term > 0 ) {
				$orders = $wpdb->prepare(
					"{$wpdb->prefix}wc_orders.id IN (
                        SELECT id 
                        FROM {$wpdb->prefix}wc_orders 
                        WHERE total_amount = %f
                    )",
					$search_term
				);

				// Allow modification of the final WHERE clause
				return apply_filters( 'swp_order_total_search_where', $orders, $search_term );
			}
		}

		return $where;
	}

	/**
	 * Admin notice for missing WooCommerce
	 */
	public function woocommerce_missing_notice() {
		?>
		<div class="notice notice-error">
			<p><?php _e( 'Order Total Search for WooCommerce requires WooCommerce to be installed and active.', 'swp-order-total-search' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Admin notice for disabled HPOS
	 */
	public function hpos_disabled_notice() {
		?>
		<div class="notice notice-error">
			<p><?php _e( 'Order Total Search for WooCommerce requires WooCommerce HPOS (High-Performance Order Storage) to be enabled. Please enable it in WooCommerce > Settings > Advanced > Features.', 'swp-order-total-search' ); ?></p>
		</div>
		<?php
	}
}

// Initialize plugin
new SWP_Order_Total_Search();