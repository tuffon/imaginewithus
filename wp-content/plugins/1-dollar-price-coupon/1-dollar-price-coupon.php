<?php
/*
Plugin Name: 1 Dollar Coupon Price
Plugin URI: http://www.easy-development.com
Description: Plugin Crafted for Grace Duong - Task #22216
Version: 1.0.0
Author: Andrei-Robert Rusu
Author URI: http://www.easy-development.com
*/

class OneDollarCoupon {

  public $discount = 999;
  public $did_discount = 0;

  public function __construct() {

    add_filter( 'woocommerce_coupon_discount_types', array($this, 'price_register'));
    add_filter( 'woocommerce_coupon_get_discount_amount', array($this, 'price_get_discount_amount'), 10, 5);
    add_filter( 'woocommerce_coupon_is_valid_for_cart', array($this, 'is_valid_for_cart'), 10, 2);

    add_action( 'init', array( $this, 'init') );
  }

  public function init() {
    global $woocommerce;

    if( isset($woocommerce->cart) && is_object( $woocommerce->cart ) ) {
      $cart_content = @$woocommerce->cart->get_cart();

      foreach($cart_content as $cart_item)
        $this->discount = ( $cart_item['line_subtotal'] < $this->discount ? $cart_item['line_subtotal'] : $this->discount );
    }
  }

  public function price_register( $coupon_types ) {
    $coupon_types['one_dollar'] = __("One Dollar Coupon Type");

    return $coupon_types;
  }

  public function price_get_discount_amount($discount, $discounting_amount, $cart_item, $single, $coupon_instance) {
    if( $coupon_instance->discount_type == "one_dollar"
        && ($cart_item['line_subtotal'] == $this->discount || $this->discount == 999)
        && $this->did_discount == 0 ) {

      $this->did_discount = 1;

      return ( $cart_item['line_subtotal'] - 1 );
    }

    return $discount;
  }

  public function is_valid_for_cart( $response, $coupon_instance) {
    if( $coupon_instance->discount_type == "one_dollar" )
      return true;

    return $response;
  }

}

$instance = new OneDollarCoupon();

