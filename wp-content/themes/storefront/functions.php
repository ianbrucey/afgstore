<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */

function ace_product_quantity_through_url( $args, $product ) {
    if ( isset( $_GET['quantity'] ) && ! isset( $_POST['quantity'] ) ) {
        $args['input_value'] = absint( $_GET['quantity'] );
    }

    return $args;
}
add_filter( 'woocommerce_quantity_input_args', 'ace_product_quantity_through_url', 10, 2 );

function mysite_woocommerce_order_status_completed( $order_id ) {
    error_log( "Order complete for order $order_id", 0 );
    // send curl to
}
add_action( 'woocommerce_order_status_completed', 'mysite_woocommerce_order_status_completed', 10, 1 );

add_action( 'woocommerce_order_status_completed', 'update_frm_entry_after_wc_order_completed' );
function update_frm_entry_after_wc_order_completed( $order_id ) {
    $order = new WC_Order( $order_id );
    $items = $order->get_items();
    foreach ( $items as $item_id => $product ) {
        if ( isset( $product['formidable_form_data'] ) && is_numeric( $product['formidable_form_data'] ) ) {
            $entry_id = $product['formidable_form_data'];
            $entry = FrmEntry::getOne( $entry_id );

            if ( $entry && $entry->form_id == 25 ) {
                $field_id = 100;//Replace 100 with the field ID to update
                $new_value = 'Complete';
                FrmEntryMeta::update_entry_meta( $entry_id, $field_id, null, $new_value );
            }
        }
    }
}

add_action( 'woocommerce_order_status_completed', 'custom_wc_order_complete', 10, 2 );
function custom_wc_order_complete( $order_id, $order ) {
    // Here comes your code (doing something), optionally using the $order_id argument
}
