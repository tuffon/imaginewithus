<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WC_Name_Your_Price_Frontend' ) ){
    /**
     * implement free frontend features
     * Class YITH_WC_Name_Your_Price_Frontend
     */
    class YITH_WC_Name_Your_Price_Frontend{

        /**
         * @var YITH_WC_Name_Your_Price_Frontend , single instance
         */
        protected static $instance;

        /**
         * __construct function
         * @author YIThemes
         * @since 1.0.0
         */
        public function __construct(){



            //print form for nameyourprice
            add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'ywcnp_include_form_price' ) );

            //include frontend style and script
            add_action( 'wp_enqueue_scripts', array( $this, 'include_free_frontend_script') );

            //cart filters
            add_filter( 'woocommerce_add_cart_item_data', array( $this, 'yith_wc_name_your_price_add_cart_item_data' ), 5, 3 );
            add_filter( 'woocommerce_add_cart_item', array( $this, 'yith_wc_name_your_price_add_cart_item' ), 5, 1 );
            add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'yith_wc_name_your_price_add_cart_validation' ), 5, 4 );
            add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 11, 2 );
            add_filter( 'ywcnp_add_cart_item', array( $this, 'ywcnp_add_cart_item' ), 5,2 );
            add_filter( 'ywcnp_add_cart_validation', array( $this, 'ywcnp_add_cart_validation' ), 5 ,4 );

            //Add button Name Your Price in loop
            add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'add_name_your_price_in_shop_loop' ) , 10 , 2 );
            add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_url_name_your_price_in_shop_loop' ), 10, 2 );
            add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'disable_ajax_add_to_cart_in_loop' ), 20, 2 );

        }


        /**
         * include free frontend script
         * @author YIThemes
         * @since 1.0.0
         */
        public function include_free_frontend_script() {

            $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

            wp_register_script( 'yit_name_your_price_frontend', YWCNP_ASSETS_URL.'js/ywcnp_free_frontend'.$suffix.'.js', array('jquery'), YWCNP_VERSION, true );

            $yith_name_your_price = array(
                'ajax_url' => admin_url('admin-ajax.php', is_ssl() ? 'https' : 'http'),
                'mon_decimal_point' => wc_get_price_decimal_separator(),
                'mon_decimal_error' => ywcnp_get_error_message('invalid_price'),
                'mon_negative_error' => ywcnp_get_error_message('negative_price')
                );

            wp_enqueue_script( 'yit_name_your_price_frontend' );

            wp_localize_script( 'yit_name_your_price_frontend', 'yith_name_your_price', $yith_name_your_price );
        }



        /**
         * print form choose your price in single product
         * @author YIThemes
         * @since 1.0.0
         */
        public function ywcnp_include_form_price(){

            global $product;

            $supported_types = ywcnp_get_product_type_allowed();

            $args = array('product_id'=>$product->id );
            $args['args'] = $args;

            if(  $product->is_type( $supported_types ) && ywcnp_product_is_name_your_price( $product->id ) ){

                wc_get_template('single-product/nameyourprice-price-form.php', $args, '', YWCNP_TEMPLATE_PATH );

            }
        }

        /**
         * @param $cart_item
         * @param $values
         * @return mixed|void
         */
        public function get_cart_item_from_session( $cart_item, $values ) {

            if ( isset( $values['ywcnp_amount'] ) ) {
                $cart_item['ywcnp_amount'] = $values['ywcnp_amount'];

                $cart_item = $this->yith_wc_name_your_price_add_cart_item( $cart_item );
            }



            return $cart_item;
        }

        /**
         * @param $cart_item_data
         * @param $product_id
         * @param $variation_id
         */
        public function yith_wc_name_your_price_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {

            if( isset( $_REQUEST['ywcnp_amount'] ) ){

                if( $variation_id )
                    $product_id = $variation_id;

                $cart_item_data['ywcnp_amount'] = floatval( ywcnp_format_number( $_REQUEST['ywcnp_amount'] ) );

                $cart_item_data = apply_filters( 'ywcnp_add_cart_item_data', $cart_item_data, $product_id );

            }


            return $cart_item_data;
        }




        /** set cart item data
         * @author YIThemes
         * @since 1.0.0
         * @param $cart_item_data
         */
        public function yith_wc_name_your_price_add_cart_item( $cart_item_data ){

            $product_id = $cart_item_data['variation_id'] ? $cart_item_data['variation_id'] : $cart_item_data['product_id'];

            $product = wc_get_product( $cart_item_data['product_id'] );

            $supported_types = ywcnp_get_product_type_allowed();

            if(  $product->is_type( $supported_types ) && ywcnp_product_is_name_your_price( $product_id ) ){


                $cart_item_data = apply_filters( 'ywcnp_add_cart_item', $cart_item_data, $product );
            }


            return $cart_item_data;

        }


        /**
         * @param $passed
         * @param $product_id
         * @param $quantity
         * @param string $variation_id
         * @return mixed
         */
        public function yith_wc_name_your_price_add_cart_validation( $passed, $product_id, $quantity, $variation_id='' ){

            if( $variation_id )
                $product_id = $variation_id;


            if( !ywcnp_product_is_name_your_price( $product_id ) )
                return $passed;

            if( !isset( $_REQUEST['ywcnp_amount'] ) || empty( $_REQUEST['ywcnp_amount'] ) )
                $amount = 0;
            else
                $amount = $_REQUEST['ywcnp_amount'];

            return apply_filters( 'ywcnp_add_cart_validation', $passed, $amount, $product_id );

        }

        /**
         * set cart item for simple product
         * @author YIThemes
         * @since 1.0.0
         * @param $cart_item_data
         * @param $product
         * @return mixed
         */
        public function ywcnp_add_cart_item( $cart_item_data, $product ){


            if( isset( $cart_item_data['ywcnp_amount'] ) ) {
                $cart_item_data['data']->price = $cart_item_data['ywcnp_amount'];
                $cart_item_data['data']->subscription_price = $cart_item_data['ywcnp_amount'];
                $cart_item_data['data']->sale_price = $cart_item_data['ywcnp_amount'];
                $cart_item_data['data']->regular_price = $cart_item_data['ywcnp_amount'];
            }
            return $cart_item_data;
        }

        /**
         * validation simple product
         * @author YIThemes
         * @since 1.0.0
         * @param $passed
         * @param $amount
         * @param $product
         * @return bool
         */
        public function ywcnp_add_cart_validation( $passed, $amount, $product ){

            $error_message = '';

            $amount = floatval( ywcnp_format_number( $amount ) );

            if( !is_numeric( $amount ) ) {
                $error_message = ywcnp_get_error_message( 'invalid_price' );
                $passed= false;
            }
            if( $amount<0 ) {
                $error_message = ywcnp_get_error_message( 'negative_price' );
                $passed= false;
                }

            if( $error_message )
                wc_add_notice( $error_message, 'error');

            return $passed;

        }

        /**
         * get add to cart text for name your price prodcut
         * @author YIThemes
         * @since 1.0.0
         */
        public function add_name_your_price_in_shop_loop( $text, $product ){

            $is_nameyourprice = get_post_meta( $product->id, '_is_nameyourprice' , true );

            if( $is_nameyourprice )
                return get_option( 'ywcnp_button_loop_label', __( 'Choose Price','yith-woocommerce-name-your-price' ) );
            else
                return $text;


        }

        /**
         * @param $url
         * @param $product
         * @return false|string
         */
        public function add_url_name_your_price_in_shop_loop( $url, $product ){

            $is_nameyourprice = get_post_meta( $product->id, '_is_nameyourprice' , true );
            if( $is_nameyourprice )
                return get_permalink( $product->id );
            else
                return $url;
        }

        /**
         * @param $button_html
         * @param $product
         * @return mixed
         */
        public function disable_ajax_add_to_cart_in_loop( $button_html, $product ){

            $is_nameyourprice = get_post_meta( $product->id, '_is_nameyourprice' , true );
            if( $is_nameyourprice ){

                return str_replace( 'product_type_simple','proudct_type_name_your_price', $button_html );
            }
            else
                return $button_html;

        }


        /**
         * return single instance
         * @author YIThemes
         * @since 1.0.0
         * @return YITH_WC_Name_Your_Price_Frontend
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }


    }
}
/**
 * @return YITH_WC_Name_Your_Price_Frontend | YITH_WC_Name_Your_Price_Premium_Frontend
 */
function YITH_Name_Your_Price_Frontend(){

    if(  defined( 'YWCNP_PREMIUM' ) && YWCNP_PREMIUM  )
        return YITH_WC_Name_Your_Price_Premium_Frontend::get_instance();

    return YITH_WC_Name_Your_Price_Frontend::get_instance();
}