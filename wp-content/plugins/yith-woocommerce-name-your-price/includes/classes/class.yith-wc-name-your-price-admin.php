<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WC_Name_Your_Price_Admin' ) ){
    /**
     * implement free admin features
     * Class YITH_WC_Name_Your_Price_Admin
     */
    class YITH_WC_Name_Your_Price_Admin{

        /**
         * @var YITH_WC_Name_Your_Price_Admin , single instance
         */
        protected static $instance;
        /**
         * __construct function
         * @author YIThemes
         * @since 1.0.0
         */
        public function __construct(){

            //add metaboxes in edit product
            add_action( 'woocommerce_product_options_pricing', array( $this, 'add_option_general_product_data' ) );
            add_filter( 'product_type_options', array( $this, 'add_product_name_your_price_option' ) );
            add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_nameyourprice_meta' ), 20, 2 );
            add_action( 'save_simple_nameyourprice_meta', array( $this, 'save_simple_nameyourprice_meta' ) );



            // include admin script
            add_action( 'admin_enqueue_scripts', array( $this, 'include_admin_scripts' ) );
        }


        /**
         * save nameyourprice product meta
         * @author YIThemes
         * @since 1.0.0
         * @param $post_id
         * @param $post
         */
        public function save_product_nameyourprice_meta( $post_id, $post ){

            $product_type_support = ywcnp_get_product_type_allowed();

            $product = wc_get_product( $post_id );
            $product_type = $product->get_type();

          if( in_array( $product_type, $product_type_support ) )
                do_action( 'save_'.$product_type.'_nameyourprice_meta', $post_id );


        }

        /**
         * save product simple meta
         * @author YIThemes
         * @since 1.0.0
         * @param $product
         */
        public function save_simple_nameyourprice_meta( $product_id ){

            $product_meta   = apply_filters( 'ywcnp_add_premium_single_meta', array(
                    '_ywcnp_enabled_product'    => isset( $_REQUEST['_ywcnp_enabled_product'] ) ? 'yes' : 'no',
                    ) );


            foreach( $product_meta as $key => $value )
                update_post_meta( $product_id, $key, $value );

            $is_nameyourprice   = $product_meta['_ywcnp_enabled_product'] == 'yes';

            update_post_meta( $product_id, '_is_nameyourprice', $is_nameyourprice );
        }


        /** add checkbox in product data header
         * @author YIThemes
         * @since 1.0.0
         * @param $type_options
         * @return array
         */
        public function add_product_name_your_price_option( $type_options ){


            $wrapper_class = apply_filters( 'ywcnp_wrapper_class', array( 'show_if_simple' ) );

            $nameyourprice_option = array(
                'ywcnp_enabled_product'  => array(
                    'id' => esc_attr( '_ywcnp_enabled_product' ),
                    'wrapper_class' => esc_attr( implode( " ", $wrapper_class ) ),
                    'label' => esc_attr( __('Name Your Price','yith-woocommerce-name-your-price') ),
                    'description' =>esc_attr( __('Enable name your price for this product','yith-woocommerce-name-your-price') ),
                    'default'   =>esc_attr( 'no' )
                )
            );

            return array_merge( $type_options, $nameyourprice_option );
        }

        /**
         * print custom template in general product data
         * @author YIThemes
         * @since 1.0.0
         */
        public function add_option_general_product_data(){

            ob_start();

            wc_get_template('metaboxes/general_product_data_name_your_price_enabled.php', array(), '', YWCNP_TEMPLATE_PATH );
            $template = ob_get_contents();

            ob_end_clean();
            echo $template;
        }

        /**
         * include admin script
         *@author YIThemes
         *@since 1.0.0
         *
         */
        public function include_admin_scripts(){

            $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

            wp_enqueue_script( 'ywcnp_admin_script', YWCNP_ASSETS_URL.'js/ywcnp_free_admin'.$suffix.'.js', array('jquery'), YWCNP_VERSION, true );


        }

        /**
         * return single instance
         * @author YIThemes
         * @since 1.0.0
         * @return YITH_WC_Name_Your_Price_Admin
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
 * @return YITH_WC_Name_Your_Price_Admin|YITH_WC_Name_Your_Price_Premium_Admin
 */
function YITH_Name_Your_Price_Admin(){

    if(  defined( 'YWCNP_PREMIUM' ) && YWCNP_PREMIUM  )
        return YITH_WC_Name_Your_Price_Premium_Admin::get_instance();

    return YITH_WC_Name_Your_Price_Admin::get_instance();
}