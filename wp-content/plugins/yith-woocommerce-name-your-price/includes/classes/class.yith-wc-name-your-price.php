<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WooCommerce_Name_Your_Price' ) ){
    /**
     * Class YITH_WooCommerce_Name_Your_Price
     */
    class YITH_WooCommerce_Name_Your_Price{

        /**
         * @var YITH_WooCommerce_Name_Your_Price, single instance
         */
        protected static $instance;

        /**
         * @var YIT_Plugin_Panel_Woocommerce instance
         */
        protected $_panel;

        /**
         * @var YIT_Plugin_Panel_Woocommerce instance
         */
        protected $_panel_page = 'yith_wcnp_panel';

        /**
         * @var string Official plugin documentation
         */
        protected $_official_documentation = 'http://yithemes.com/docs-plugins/yith-woocommerce-name-your-price/' ;

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_landing_url = 'http://yithemes.com/themes/plugins/yith-woocommerce-name-your-price/' ;

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_live_demo = 'http://plugins.yithemes.com/yith-woocommerce-name-your-price/' ;

        /**
         * @var string Premium page
         */
        protected $_premium = 'premium.php';



        /**
         * __construct function
         * @author YIThemes
         * @since 1.0.0
         */
        public function __construct(){

            /* Plugin Informations */
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader') ,15 );
            add_filter( 'plugin_action_links_' . plugin_basename( YWCNP_DIR . '/' . basename( YWCNP_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
            add_action( 'yith_wc_name_your_price_premium', array( $this, 'show_premium_tab' ) );

            //Replace default price with minimum name your price
            add_filter( 'woocommerce_get_price_html', array( $this, 'get_nameyourprice_price_html' ), 20, 2 );


            /*Add Name Your Price in YITH PLUGIN*/
            add_action( 'admin_menu', array( $this, 'add_name_your_price_menu' ), 5 );


              //Load Admin Class
                if (is_admin()) {

                    YITH_Name_Your_Price_Admin();
                } //Load FrontEnd Class
                else {

                    YITH_Name_Your_Price_Frontend();
                }

            //Set product as purchasable
            add_filter( 'woocommerce_is_purchasable', array( $this, 'ywcnp_is_purchasable' ), 20, 2 );
            add_filter( 'woocommerce_product_is_on_sale', array( $this, 'ywcnp_is_on_sale' ), 20, 2 );
        }

        /**
         * @author YIThemes
         * @since 1.0.0
         * @param $purchasable
         * @param $product
         * @return bool
         */
        public function ywcnp_is_purchasable( $purchasable, $product ){

            $product_id = isset( $product->variation_id ) ? $product->variation_id : $product->id;

            $product_type_supported = ywcnp_get_product_type_allowed();

            if( $product->is_type( $product_type_supported ) && ywcnp_product_is_name_your_price( $product_id ) )
                return true;

            return $purchasable;


        }

        /**
         * @author YIThemes
         * @since 1.0.3
         * @param $on_sale
         * @param $product
         * @return bool
         */
        public function  ywcnp_is_on_sale( $on_sale, $product ){

            $product_id = isset( $product->variation_id ) ? $product->variation_id : $product->id;

            $product_type_supported = ywcnp_get_product_type_allowed();


            if( $product->is_type( $product_type_supported ) && ywcnp_product_is_name_your_price( $product_id ) )
                return false;

            return $on_sale;
        }

        /**
         * return single instance
         * @author YIThemes
         * @since 1.0.0
         * @return YITH_WooCommerce_Name_Your_Price
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * load plugin framework 2.0
         * @author YIThemes
         * @since 1.0.0
         */
        public function plugin_fw_loader() {
            if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if( ! empty( $plugin_fw_data ) ){
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }

        /**
         * Action Links
         *
         * add the action links to plugin admin page
         *
         * @param $links | links plugin array
         *
         * @return   mixed Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return mixed
         * @use plugin_action_links_{$plugin_file_name}
         */
        public function action_links($links)
        {

            $links[] = '<a href="' . admin_url("admin.php?page={$this->_panel_page}") . '">' . __('Settings', 'yith-woocommerce-name-your-price') . '</a>';

            $premium_live_text = defined( 'YWCNP_FREE_INIT' ) ?  __( 'Premium live demo', 'yith-woocommerce-name-your-price' ) : __( 'Live demo', 'yith-woocommerce-name-your-price' );

            $links[] = '<a href="'.$this->_premium_live_demo.'" target="_blank">'.$premium_live_text.'</a>';

            if (defined('YWCNP_FREE_INIT')) {
                $links[] = '<a href="' . $this->get_premium_landing_uri() . '" target="_blank">' . __('Premium Version', 'yith-woocommerce-name-your-price') . '</a>';
            }

            return $links;
        }

        /**
         * plugin_row_meta
         *
         * add the action links to plugin admin page
         *
         * @param $plugin_meta
         * @param $plugin_file
         * @param $plugin_data
         * @param $status
         *
         * @return   Array
         * @since    1.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use plugin_row_meta
         */
        public function plugin_row_meta($plugin_meta, $plugin_file, $plugin_data, $status)
        {
            if ((defined('YWCNP_INIT') && (YWCNP_INIT == $plugin_file)) ||
                (defined('YWCNP_FREE_INIT') && (YWCNP_FREE_INIT == $plugin_file))
            ) {

                $plugin_meta[] = '<a href="' . $this->_official_documentation . '" target="_blank">' . __('Plugin Documentation', 'yith-woocommerce-name-your-price') . '</a>';
            }

            return $plugin_meta;
        }

        /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri()
        {
            return defined('YITH_REFER_ID') ? $this->_premium_landing_url . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing_url .'?refer_id=1030585';
        }

        /**
         * Premium Tab Template
         *
         * Load the premium tab template on admin page
         *
         * @since   1.0.0
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  void
         */
        public function show_premium_tab()
        {
            $premium_tab_template = YWCNP_TEMPLATE_PATH . '/admin/' . $this->_premium;
            if (file_exists($premium_tab_template)) {
                include_once($premium_tab_template);
            }
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use     /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function add_name_your_price_menu()
        {
            if (!empty($this->_panel)) {
                return;
            }


            $admin_tabs['general-settings'] = __( 'General Settings', 'yith-woocommerce-name-your-price' );

            if (!defined('YWCNP_PREMIUM'))
                $admin_tabs['premium-landing'] = __('Premium Version', 'yith-woocommerce-name-your-price');


            $args = array(
                'create_menu_page' => true,
                'parent_slug' => '',
                'page_title' => __('Name Your Price', 'yith-woocommerce-name-your-price'),
                'menu_title' => __('Name Your Price', 'yith-woocommerce-name-your-price'),
                'capability' => 'manage_options',
                'parent' => '',
                'parent_page' => 'yit_plugin_panel',
                'page' => $this->_panel_page,
                'admin-tabs' => apply_filters( 'ywcnp_add_premium_tab', $admin_tabs ),
                'options-path' => YWCNP_DIR . '/plugin-options'
            );

            $this->_panel = new YIT_Plugin_Panel_WooCommerce($args);
        }

        /**
         * print the minimum price html
         * @author YIThemes
         * @since 1.0.0
         * @param $price
         * @param $product
         * @return mixed|string|void
         */
        public function get_nameyourprice_price_html( $price, $product ){


                if ( ywcnp_product_is_name_your_price( $product->id ) ) {

                    $price = '';

                    return apply_filters('ywcnp_get_product_price_html', $price, $product);
                }
                else
                    return  $price ;

        }


    }
}