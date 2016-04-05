<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}
?>
<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
	<td class="product-name">
		<?php
			$is_visible = $product && $product->is_visible();

			echo apply_filters( 'woocommerce_order_item_name', $is_visible ? sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), $item['name'] ) : $item['name'], $item, $is_visible );
			echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item );

			do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

			$order->display_item_meta( $item );
			displayDownloads( $item );

			do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
		?>
	</td>
	<td class="product-total">
		<?php echo $order->get_formatted_line_subtotal( $item ); ?>
	</td>
</tr>
<?php if ( $show_purchase_note && $purchase_note ) : ?>
<tr class="product-purchase-note">
	<td colspan="3"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>
</tr>
<?php endif; ?>

<?php

function displayDownloads( $item ){
	$product = getProductFromItem( $item );

	if ( $product && $product->exists() && $product->is_downloadable() && $this->is_download_permitted() ) {
		$download_files = getItemDownloads( $item );
		$i              = 0;
		$links          = array();

		foreach ( $download_files as $download_id => $file ) {
			$i++;
			$prefix  = count( $download_files ) > 1 ? sprintf( __( 'Download %d', 'woocommerce' ), $i ) : __( 'Download', 'woocommerce' );
			$links[] = '<span class="download-url">' . $prefix . ': <a href="' . esc_url( $file['download_url'] ) . '" target="_blank">' . esc_html( $file['name'] ) . '</a></span>' . "\n";
		}

		echo '<br/>' . implode( '<br/>', $links );
	}
}

function getProductFromItem( $item ){
	if ( ! empty( $item['variation_id'] ) && 'product_variation' === get_post_type( $item['variation_id'] ) ) {
		$_product = wc_get_product( $item['variation_id'] );
	} elseif ( ! empty( $item['product_id']  ) ) {
		$_product = wc_get_product( $item['product_id'] );
	} else {
		$_product = false;
	}

	return apply_filters( 'woocommerce_get_product_from_item', $_product, $item, $this );
}

/**
 * Get the downloadable files for an item in this order.
 *
 * @param  array $item
 * @return array
 */
function getItemDownloads( $item ) {
	global $wpdb;

	$product_id   = $item['variation_id'] > 0 ? $item['variation_id'] : $item['product_id'];
	$product      = wc_get_product( $product_id );
	if ( ! $product ) {
		/**
		 * $product can be `false`. Example: checking an old order, when a product or variation has been deleted.
		 * @see \WC_Product_Factory::get_product
		 */
		return array();
	}
	$download_ids = $wpdb->get_col( $wpdb->prepare("
			SELECT download_id
			FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
			WHERE user_email = %s
			AND order_key = %s
			AND product_id = %s
			ORDER BY permission_id
		", $this->billing_email, $this->order_key, $product_id ) );

	$files = array();

	foreach ( $download_ids as $download_id ) {

		if ( $product->has_file( $download_id ) ) {
			$files[ $download_id ]                 = $product->get_file( $download_id );
			$files[ $download_id ]['download_url'] = $this->get_download_url( $product_id, $download_id );
		}
	}

	return apply_filters( 'woocommerce_get_item_downloads', $files, $item, $this );
}