<?php
if ( !defined( 'ABSPATH' ) )
    exit;

global $product;

$product_id = $product->id;
$sugg_price = ywcnp_get_suggest_price( $product_id );
$min_price = ywcnp_get_min_price( $product_id );
$max_price = ywcnp_get_max_price( $product_id );

$span_subscription = '';

$show_form = ( $product instanceof WC_Product_Variable ) ? 'display:none;' : 'display:block;';

if ( ywcnp_product_has_subscription( $product_id ) ) {

    $price_is_per = get_post_meta( $product_id, '_ywsbs_price_is_per', true );
    $price_time_option = get_post_meta( $product_id, '_ywsbs_price_time_option', true );

    $price = ' / ' . $price_is_per . ' ' . $price_time_option;

    $span_subscription = '<span class="ywcnp_subscription_period">' . $price . '</span>';
}

$price_format = get_woocommerce_price_format();
$currency = get_woocommerce_currency_symbol();

$input_number = sprintf('<input type="text" name="ywcnp_amount" class="ywcnp_sugg_price short wc_input_price" value="%s">', esc_attr( $sugg_price ) );

?>

<div id="ywcnp_form_name_your_price" style="margin:10px 0px;<?php echo $show_form; ?>">
    <?php do_action( 'ywcnp_before_suggest_price_single' ); ?>
    <p class="ywcnp_suggest_price_single">
        <?php
        $sugg_label_text = get_option( 'ywcnp_name_your_price_label' ); ?>
        <label for="ywcnp_suggest_price_single"><?php echo $sugg_label_text; ?></label>
        <?php echo sprintf($price_format, '<span class="ywcnp_currency">'.$currency.'</span>', $input_number );?>
        <?php echo $span_subscription; ?>
        <input type="hidden" name="ywcnp_min" value="<?php echo esc_attr( $min_price ); ?>"/>
        <input type="hidden" name="ywcnp_max" value="<?php echo esc_attr( $max_price ); ?>"/>
    </p>

    <p class="ywcnp_min_label" style="display:<?php echo empty( $min_price ) ? 'none' : 'block'; ?>;">
        <?php echo ywcnp_get_min_price_html( $product_id ); ?>
    </p>

    <p class="ywcnp_max_label" style="display:<?php echo empty( $max_price ) ? 'none' : 'block'; ?>;">
        <?php echo ywcnp_get_max_price_html( $product_id ); ?>
    </p>
    <?php do_action( 'ywcnp_after_suggest_price_single' ); ?>
</div>