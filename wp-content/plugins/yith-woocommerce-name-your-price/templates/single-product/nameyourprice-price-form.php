<?php
if( !defined( 'ABSPATH' ) )
    exit;

global $product;
?>

<div id="ywcnp_form_name_your_price" style="margin:10px 0px;">
    <?php do_action( 'ywcnp_before_suggest_price_single');?>
    <?php
          $sugg_label_text = get_option( 'ywcnp_suggest_price_label', __( 'Choose the amount','yith-woocommerce-name-your-price' ) ); ?>
    <label for="ywcnp_suggest_price_single"><?php echo $sugg_label_text;?></label>
    <input type="text" class="ywcnp_sugg_price short wc_input_price" name="ywcnp_amount"/>
    <?php do_action( 'ywcnp_after_suggest_price_single' );?>
</div>