<?php
if( !defined( 'ABSPATH' ) )
    exit;
if( !function_exists( 'ywcnp_get_product_type_allowed' ) ){

    function ywcnp_get_product_type_allowed(){

        return apply_filters( 'ywcnp_product_types', array( 'simple' ) );
    }
}

if( ! function_exists( 'ywcnp_get_error_message' ) ){

    function ywcnp_get_error_message( $message_type ){

        $messages = apply_filters('ywcnp_add_error_message', array(
            'negative_price'    => get_option( 'ywcnp_negative_price_label', __( 'Please enter a value greater or equal to 0', 'yith-woocommerce-name-your-price' ) ),
            'invalid_price'     => get_option( 'ywcnp_invalid_price_label', __( 'Please enter a valid price', 'yith-woocommerce-name-your-price' ) )
        ) );

        return $messages[$message_type];
    }
}


if( !function_exists( 'ywcnp_product_is_name_your_price' ) ){

    function ywcnp_product_is_name_your_price( $product_id ){

        return get_post_meta( $product_id, '_is_nameyourprice' , true ) || get_post_meta( $product_id, '_variation_has_nameyourprice', true );
    }
}

if( !function_exists( 'ywcnp_format_number' ) ){

    function ywcnp_format_number ( $number ){

        $number = str_replace( get_option( 'woocommerce_price_thousand_sep' ), '', $number );

        return wc_format_decimal( $number );
    }
}