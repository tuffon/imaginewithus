<?php
/**
 * Simple product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $product, $sf_options;

$loading_text = __( 'Adding...', 'swiftframework' );
$added_text = __( 'Item added', 'swiftframework' );
$button_class = "add_to_cart_button";
$ajax_enabled = true;
$minimum_allowed_quantity = 1;

if ( isset($sf_options['product_addtocart_ajax']) ) {
	$ajax_enabled = $sf_options['product_addtocart_ajax'];
}

if ( !$ajax_enabled ) {
	$button_class = "single_add_to_cart_button";
}

if ( ! $product->is_purchasable() ) return;
?>

<?php
	// Availability
	$availability = $product->get_availability();

	if ( $availability['availability'] )
		echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>', $availability['availability'] );
?>

<?php
	// WooCommerce Min/Max Quanties Plugin
	if ( class_exists( 'WC_Min_Max_Quantities_Addons' ) ) {
		$custom_min_qty = sf_get_post_meta( get_the_ID(), 'minimum_allowed_quantity', true );
		if ( $custom_min_qty != "" ) {
			$minimum_allowed_quantity = $custom_min_qty;
		}
	}
?>

<?php if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" method="post" enctype='multipart/form-data'>
	 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php echo sf_wishlist_button(); ?>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php else : ?>

<?php echo sf_wishlist_button('oos'); ?>

<?php endif; ?>