<?php
/**
 * Loop Add to Cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;
$tooltip_text = "";
?>

<?php if ( ! $product->is_in_stock() ) : ?>

	<?php echo sf_wishlist_button(); ?>

<?php else : ?>

	<?php
		$link = array(
			'url'   => '',
			'label' => '',
			'class' => '',
			'icon' => '',
			'icon_class' => '',
		);

		$handler = apply_filters( 'woocommerce_add_to_cart_handler', $product->product_type, $product );

		switch ( $handler ) {
			case "variable" :
				$link['url'] 	= apply_filters( 'variable_add_to_cart_url', get_permalink( $product->id ) );
				$link['icon_class'] = 'sf-icon-variable-options';
				$link['label'] 	= '<i class="sf-icon-variable-options"></i><span>' . apply_filters( 'variable_add_to_cart_text', __( 'Select options', 'swiftframework' ) ) . '</span>';
				$tooltip_text = __("Select Options", "swiftframework");
			break;
			case "grouped" :
				$link['url'] 	= apply_filters( 'grouped_add_to_cart_url', get_permalink( $product->id ) );
				$link['icon_class'] = 'sf-icon-variable-options';
				$link['label'] 	= '<i class="sf-icon-variable-options"></i><span>' . apply_filters( 'grouped_add_to_cart_text', __( 'View options', 'swiftframework' ) ) . '</span>';
				$tooltip_text = __("View Options", "swiftframework");
			break;
			case "external" :
				$link['url'] 	= apply_filters( 'external_add_to_cart_url', get_permalink( $product->id ) );
				$link['icon_class'] = 'fa-info';
				$link['label'] 	= '<i class="fa-info"></i><span>' . apply_filters( 'external_add_to_cart_text', __( 'Read More', 'swiftframework' ) ) . '</span>';
				$tooltip_text = __("Read More", "swiftframework");
			break;
			default :
				if ( $product->is_purchasable() && $product->product_type != "booking" ) {
					$link['url'] 	= apply_filters( 'add_to_cart_url', esc_url( $product->add_to_cart_url() ) );
					$link['icon_class'] = 'sf-icon-add-to-cart';
					$link['label'] 	= apply_filters( 'add_to_cart_icon', '<i class="sf-icon-add-to-cart"></i>' ) . '<span>' . apply_filters( 'add_to_cart_text', __( 'Add to cart', 'swiftframework' ) ) . '</span>';
					$link['class']  = apply_filters( 'add_to_cart_class', 'add_to_cart_button ajax_add_to_cart' );
					$tooltip_text = __("Add to cart", "swiftframework");
				} else {
					$link['url'] 	= apply_filters( 'not_purchasable_url', get_permalink( $product->id ) );
					$link['icon_class'] = 'sf-icon-soldout';
					$link['label'] 	= '<i class="sf-icon-soldout"></i><span>' . apply_filters( 'not_purchasable_text', __( 'Read More', 'swiftframework' ) ) . '</span>';
					$tooltip_text = __("Read More", "swiftframework");
				}
			break;
		}

		$loading_text = __( 'Adding...', 'swiftframework' );
		$added_text = __( 'Item added', 'swiftframework' );
		$added_text_short = __( 'Added', 'swiftframework' );
		$added_tooltip_text = __( 'Added to cart', 'swiftframework' );

		// Wishlist Button
		echo sf_wishlist_button();

	?>

<?php endif; ?>
