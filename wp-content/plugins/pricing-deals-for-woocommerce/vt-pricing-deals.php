<?php
/*
Plugin Name: VarkTech Pricing Deals for WooCommerce
Plugin URI: http://varktech.com
Description: An e-commerce add-on for WooCommerce, supplying Pricing Deals functionality.
Version: 1.1.1.2 
Author: Vark
Author URI: http://varktech.com
*/

/*  ******************* *******************
=====================
ASK YOUR HOST TO TURN OFF magic_quotes_gpc !!!!!
=====================
******************* ******************* */


/*
** define Globals 
*/
   $vtprd_info;  //initialized in VTPRD_Parent_Definitions
   $vtprd_rules_set;
   $vtprd_rule;
   $vtprd_cart;
   $vtprd_cart_item;
   $vtprd_setup_options;
   
   $vtprd_rule_display_framework;
   $vtprd_rule_type_framework; 
   $vtprd_deal_structure_framework;
   $vtprd_deal_screen_framework;
   $vtprd_deal_edits_framework;
   $vtprd_template_structures_framework;
   
   //initial setup only, overriden later in function vtprd_debug_options
   
   
  error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR); //v1.0.7.7
  
  
  
     
class VTPRD_Controller{
	
	public function __construct(){    
 
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    } 

		define('VTPRD_VERSION',                               '1.1.1.2');
    define('VTPRD_MINIMUM_PRO_VERSION',                   '1.1.1.2');
    define('VTPRD_LAST_UPDATE_DATE',                      '2015-11-07');
    define('VTPRD_DIRNAME',                               ( dirname( __FILE__ ) ));
    define('VTPRD_URL',                                   plugins_url( '', __FILE__ ) );
    define('VTPRD_EARLIEST_ALLOWED_WP_VERSION',           '3.3');   //To pick up wp_get_object_terms fix, which is required for vtprd-parent-functions.php
    define('VTPRD_EARLIEST_ALLOWED_PHP_VERSION',          '5');
    define('VTPRD_PLUGIN_SLUG',                           plugin_basename(__FILE__));
    define('VTPRD_PRO_PLUGIN_NAME',                      'Varktech Pricing Deals Pro for WooCommerce');    //v1.0.7.1
    define('VTPRD_ADMIN_CSS_FILE_VERSION',                'v003'); //V1.1.0.8 ==> use to FORCE pickup of new CSS
    define('VTPRD_ADMIN_JS_FILE_VERSION',                 'v003'); //V1.1.0.8   ==> use to FORCE pickup of new JS
   
    require_once ( VTPRD_DIRNAME . '/woo-integration/vtprd-parent-definitions.php');
            
    // overhead stuff
    add_action('init', array( &$this, 'vtprd_controller_init' ));
    
    add_action( 'admin_init', array( &$this, 'vtprd_maybe_plugin_mismatch' ) ); //v1.1.0.1
                
        
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    //  these control the rules ui, add/save/trash/modify/delete
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    //  One of these will pick up the NEW post, both the Rule custom post, and the PRODUCT
    //    picks up ONLY the 1st publish, save_post works thereafter...   
    //      (could possibly conflate all the publish/save actions (4) into the publish_post action...)
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */    
    if (is_admin()) {   //v1.0.7.2   only add during is_admin
        add_action( 'draft_to_publish',       array( &$this, 'vtprd_admin_update_rule_cntl' )); 
        add_action( 'auto-draft_to_publish',  array( &$this, 'vtprd_admin_update_rule_cntl' ));
        add_action( 'new_to_publish',         array( &$this, 'vtprd_admin_update_rule_cntl' )); 			
        add_action( 'pending_to_publish',     array( &$this, 'vtprd_admin_update_rule_cntl' ));
        
        //standard mod/del/trash/untrash
        add_action('save_post',     array( &$this, 'vtprd_admin_update_rule_cntl' ));
        add_action('delete_post',   array( &$this, 'vtprd_admin_delete_rule' ));    
        add_action('trash_post',    array( &$this, 'vtprd_admin_trash_rule' ));
        add_action('untrash_post',  array( &$this, 'vtprd_admin_untrash_rule' ));
        /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
        
        //get rid of bulk actions on the edit list screen, which aren't compatible with this plugin's actions...
        add_action('bulk_actions-edit-vtprd-rule', array($this, 'vtprd_custom_bulk_actions') );
    } //v1.0.7.2  end
    
	}   //end constructor

  	                                                             
 /* ************************************************
 **   Overhead and Init
 *************************************************** */
	public function vtprd_controller_init(){
  //error_log( print_r(  'Function begin - vtprd_controller_init', true ) );
    global $vtprd_setup_options;

    //$product->get_rating_count() odd error at checkout... woocommerce/templates/single-product-reviews.php on line 20  
    //  (Fatal error: Call to a member function get_rating_count() on a non-object)
    global $product;
       
    load_plugin_textdomain( 'vtprd', null, dirname( plugin_basename( __FILE__ ) ) . '/languages' );  //v1.0.8.4  moved here above defs

    //v1.0.9.3 info not avail here
    //if ($vtprd_setup_options['discount_taken_where'] == 'discountCoupon') { //v1.0.9.0  doesn't apply if 'discountUnitPrice'
      //v1.0.8.5 begin
      // instead of translation, using filter to allow title change!!!!!!!!
      //  this propagates throughout all plugin code execution through global...
      $coupon_title  = apply_filters('vtprd_coupon_code_discount_title','' );
      if ($coupon_title) {
         global $vtprd_info; 
         $vtprd_info['coupon_code_discount_deal_title'] = $coupon_title;
      }
   // }  //v1.0.9.0
    /*
    // Sample filter execution ==>>  put into your theme's functions.php file, so it's not affected by plugin updates
          function coupon_code_discount_title() {
            return 'different coupon title';  //<<==  Change this text to be the title you want!!!
          }
          add_filter('vtprd_coupon_code_discount_title', 'coupon_code_discount_title', 10);         
    */
    //v1.0.8.5 end
    
    
    //Split off for AJAX add-to-cart, etc for Class resources.  Loads for is_Admin and true INIT loads are kept here.
    //require_once ( VTPRD_DIRNAME . '/core/vtprd-load-execution-resources.php' );

    require_once  ( VTPRD_DIRNAME . '/core/vtprd-backbone.php' );    
    require_once  ( VTPRD_DIRNAME . '/core/vtprd-rules-classes.php');
    require_once  ( VTPRD_DIRNAME . '/admin/vtprd-rules-ui-framework.php' );
    require_once  ( VTPRD_DIRNAME . '/woo-integration/vtprd-parent-functions.php');
    require_once  ( VTPRD_DIRNAME . '/woo-integration/vtprd-parent-theme-functions.php');
    require_once  ( VTPRD_DIRNAME . '/woo-integration/vtprd-parent-cart-validation.php');
//  require_once  ( VTPRD_DIRNAME . '/woo-integration/vtprd-parent-definitions.php');    //v1.0.8.4  moved above
    require_once  ( VTPRD_DIRNAME . '/core/vtprd-cart-classes.php');
    
    //************
    //changed for AJAX add-to-cart, removed the is_admin around these resources => didn't work, for whatever reason...
    if(defined('VTPRD_PRO_DIRNAME')) {
      require_once  ( VTPRD_PRO_DIRNAME . '/core/vtprd-apply-rules.php' );
      require_once  ( VTPRD_PRO_DIRNAME . '/woo-integration/vtprd-lifetime-functions.php' );          
    } else {
      require_once  ( VTPRD_DIRNAME .     '/core/vtprd-apply-rules.php' );
    }

    $vtprd_setup_options = get_option( 'vtprd_setup_options' );  //put the setup_options into the global namespace 
    
    //**************************
    //v1.0.9.0 begin  
    //**************************
    switch( true ) { 
      
      case  is_admin() : //absolutely REQUIRED!!!
        $do_nothing;
        break;
         
      case ($vtprd_setup_options['discount_taken_where'] == 'discountCoupon') :
        $do_nothing;
        break;
             
      case ($vtprd_setup_options['discount_taken_where'] == 'discountUnitPrice') :
        //turn off switches not allowed for "discountUnitPrice" ==> done on the fly, rather than at update time...
        $vtprd_setup_options['show_checkout_purchases_subtotal']     =   'none';                           
        $vtprd_setup_options['show_checkout_discount_total_line']    =   'no'; 
        $vtprd_setup_options['checkout_new_subtotal_line']           =   'no'; 
        $vtprd_setup_options['show_cartWidget_purchases_subtotal']   =   'none';                           
        $vtprd_setup_options['show_cartWidget_discount_total_line']  =   'no'; 
        $vtprd_setup_options['cartWidget_new_subtotal_line']         =   'no';         
        break;
                
      default:
        // supply default for new variables as needed for upgrade v1.0.8.9 => v1.0.9.0 as needed
        $vtprd_setup_options['discount_taken_where']        =   'discountCoupon';  
        $vtprd_setup_options['give_more_or_less_discount']  =   'more'; 
        $vtprd_setup_options['show_unit_price_cart_discount_crossout']     =   'yes'; //v1.0.9.3 ==> for help when switching to unit pricing...
        $vtprd_setup_options['show_unit_price_cart_discount_computation']  =   'no'; //v1.0.9.3 
        update_option( 'vtprd_setup_options',$vtprd_setup_options);  //v1.0.9.1
        break;
    
    }
    //v1.0.9.0 end 
    
    if (function_exists('vtprd_debug_options')) { 
      vtprd_debug_options();  //v1.0.5
    }
            
    /*  **********************************
        Set GMT time zone for Store 
    Since Web Host can be on a different
    continent, with a different *Day* and Time,
    than the actual store.  Needed for Begin/end date processing
    **********************************  */
    vtprd_set_selected_timezone();
        
    if (is_admin()){ 
        add_filter( 'plugin_action_links_' . VTPRD_PLUGIN_SLUG , array( $this, 'vtprd_custom_action_links' ) );

        require_once ( VTPRD_DIRNAME . '/admin/vtprd-setup-options.php');
        require_once ( VTPRD_DIRNAME . '/admin/vtprd-rules-ui.php' );
           
        if(defined('VTPRD_PRO_DIRNAME')) {         
          require_once ( VTPRD_PRO_DIRNAME . '/admin/vtprd-rules-update.php'); 
          require_once ( VTPRD_PRO_DIRNAME . '/woo-integration/vtprd-lifetime-functions.php' );           
        } else {
          require_once ( VTPRD_DIRNAME .     '/admin/vtprd-rules-update.php');
        }
        
        require_once ( VTPRD_DIRNAME . '/admin/vtprd-show-help-functions.php');
        require_once ( VTPRD_DIRNAME . '/admin/vtprd-checkbox-classes.php');
        require_once ( VTPRD_DIRNAME . '/admin/vtprd-rules-delete.php');
        
        $this->vtprd_admin_init();  
        
        //v1.0.7.1 begin
        /* v1.1.0.1  replaced with new notification at admin_init
        if ( (defined('VTPRD_PRO_DIRNAME')) &&
             (version_compare(VTPRD_PRO_VERSION, VTPRD_MINIMUM_PRO_VERSION) < 0) ) {    //'<0' = 1st value is lower  
          add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_version_mismatch') );            
        }
        */
        //v1.0.7.1 end 
      
      /* //v1.0.9.3 moved to functions to be run at admin-init time
        if ($vtprd_setup_options['discount_taken_where'] == 'discountCoupon') { //v1.0.9.3  doesn't apply if 'discountUnitPrice'
        //v1.0.7.4 begin  
          //****************************************
          //INSIST that coupons be enabled in woo, in order for this plugin to work!!
          //****************************************
          //always check if the manually created coupon codes are there - if not create them.
          vtprd_woo_maybe_create_coupon_types();        
          $coupons_enabled = get_option( 'woocommerce_enable_coupons' ) == 'no' ? false : true;
          if (!$coupons_enabled) {  
            add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_coupon_enable_required') );            
          } 
        }
        */
  // don't have to do this EXCEPT at install time....
  //    $this->vtprd_maybe_add_wholesale_role(); //v1.0.9.0
 
      //v1.0.7.4 end 
      
    } else {

        add_action( "wp_enqueue_scripts", array(&$this, 'vtprd_enqueue_frontend_scripts'), 1 );    //priority 1 to run 1st, so front-end-css can be overridden by another file with a dependancy

    }

      /*
    if (is_admin()){ 

      //LIFETIME logid cleanup...
      //  LogID logic from wpsc-admin/init.php
      if(defined('VTPRD_PRO_DIRNAME')) {
        switch( true ) {
          case ( isset( $_REQUEST['wpsc_admin_action2'] ) && ($_REQUEST['wpsc_admin_action2'] == 'purchlog_bulk_modify') )  :
                 vtprd_maybe_lifetime_log_bulk_modify();
             break; 
          case ( isset( $_REQUEST['wpsc_admin_action'] ) && ($_REQUEST['wpsc_admin_action'] == 'delete_purchlog') ) :
                 vtprd_maybe_lifetime_log_roll_out_cntl();
             break;                                             
        } 
          
        if (version_compare(VTPRD_PRO_VERSION, VTPRD_MINIMUM_PRO_VERSION) < 0) {    //'<0' = 1st value is lower  
          add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_version_mismatch') );            
        }          
      }
      
      //****************************************
      //INSIST that coupons be enabled in woo, in order for this plugin to work!!
      //****************************************
      $coupons_enabled = get_option( 'woocommerce_enable_coupons' ) == 'no' ? false : true;
      if (!$coupons_enabled) {  
        add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_coupon_enable_required') );            
      } 
    } 
      */   
         


    return; 
  }
  
  //v1.1.0.1  new function 
  public function vtprd_maybe_plugin_mismatch(){
  
 //error_log( print_r(  'Function begin - vtprd_maybe_plugin_mismatch', true ) );
 
      if ( (defined('VTPRD_PRO_DIRNAME')) &&
           (version_compare(VTPRD_PRO_VERSION, VTPRD_MINIMUM_PRO_VERSION) < 0) ) {    //'<0' = 1st value is lower  
        add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_version_mismatch') );            
      }

      //v1.1.1
      // Check if WooCommerce is active
      if ( ! class_exists( 'WooCommerce' ) )  {
      	add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_woocommerce_required') ); 
      }
      
      global $vtprd_setup_options;
      if ( ( class_exists( 'WC_Measurement_Price_Calculator' ) ) && 
           ( isset($vtprd_setup_options['discount_taken_where']) ) &&
           ( $vtprd_setup_options['discount_taken_where'] == 'discountUnitPrice' ) ) {
      	add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_cant_use_unit_price') ); 
      }      
      if ( ( class_exists( 'WC_Product_Addons' ) ) && 
           ( isset($vtprd_setup_options['discount_taken_where']) ) &&
           ( $vtprd_setup_options['discount_taken_where'] == 'discountUnitPrice' ) ) {
      	add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_cant_use_unit_price') ); 
      }      
            
      
      //v1.1.1
       
    return;
  
  }  
  

  public function vtprd_enqueue_frontend_scripts(){
    global $vtprd_setup_options;
  
 //error_log( print_r(  'Function begin - vtprd_enqueue_frontend_scripts', true ) );
         
    wp_enqueue_script('jquery'); //needed universally
    
    if ( $vtprd_setup_options['use_plugin_front_end_css'] == 'yes' ){
      wp_register_style( 'vtprd-front-end-style', VTPRD_URL.'/core/css/vtprd-front-end-min.css'  );   //every theme MUST have a style.css...  
      //wp_register_style( 'vtprd-front-end-style', VTPRD_URL.'/core/css/vtprd-front-end-min.css', array('style.css')  );   //every theme MUST have a style.css...      
      wp_enqueue_style('vtprd-front-end-style');
    }
    
    return;
  
  }  

         
  /* ************************************************
  **   Admin - Remove bulk actions on edit list screen, actions don't work the same way as onesies...
  ***************************************************/ 
  function vtprd_custom_bulk_actions($actions){
              //v1.0.7.2  add  ".inline.hide-if-no-js, .view" to display:none; list
    ?>         
    <style type="text/css"> #delete_all, .inline.hide-if-no-js, .view {display:none;} /*kill the 'empty trash' buttons, for the same reason*/ </style>
    <?php
    
    unset( $actions['edit'] );
    unset( $actions['trash'] );
    unset( $actions['untrash'] );
    unset( $actions['delete'] );
    return $actions;
  }

      
  /* ************************************************
  **   Admin - Show Rule UI Screen
  *************************************************** 
  *  This function is executed whenever the add/modify screen is presented
  *  WP also executes it ++right after the update function, prior to the screen being sent back to the user.   
  */  
	public function vtprd_admin_init(){
  
 //error_log( print_r(  'Function begin - vtprd_admin_init', true ) );
   
     if ( !current_user_can( 'edit_posts', 'vtprd-rule' ) )
          return;

     $vtprd_rules_ui = new VTPRD_Rules_UI;      
  }

  /* ************************************************
  **   Admin - Publish/Update Rule or Parent Plugin CPT 
  *************************************************** */
	public function vtprd_admin_update_rule_cntl(){
      global $post, $vtprd_info;    
  
 //error_log( print_r(  'Function begin - vtprd_admin_update_rule_cntl', true ) );
         
      
      // v1.0.7.3 begin
      if( !isset( $post ) ) {    
        return;
      }  
      // v1.0.7.3  end
                        
      switch( $post->post_type ) {
        case 'vtprd-rule':
            $this->vtprd_admin_update_rule();  
          break; 
        case $vtprd_info['parent_plugin_cpt']: //this is the update from the PRODUCT screen, and updates the include/exclude lists
            $this->vtprd_admin_update_product_meta_info();
          break;
      }  
      return;
  }
  
  
  /* ************************************************
  **   Admin - Publish/Update Rule 
  *************************************************** */
	public function vtprd_admin_update_rule(){
  
 //error_log( print_r(  'Function begin - vtprd_admin_update_rule', true ) );
     
    /* *****************************************************************
         The delete/trash/untrash actions *will sometimes fire save_post*
         and there is a case structure in the save_post function to handle this.
    
          the delete/trash actions are sometimes fired twice, 
               so this can be handled by checking 'did_action'
               
          'publish' action flows through to the bottom     
     ***************************************************************** */
      
      global $post, $vtprd_rules_set;
      //v1.1.0.9 begin
      if( !isset( $post ) ) {    
        return;
      }       
      //v1.1.0.9 end
      
      if ( !( 'vtprd-rule' == $post->post_type )) {
        return;
      }  
      if (( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
            return; 
      }
     if (isset($_REQUEST['vtprd_nonce']) ) {     //nonce created in vtprd-rules-ui.php  
          $nonce = $_REQUEST['vtprd_nonce'];
          if(!wp_verify_nonce($nonce, 'vtprd-rule-nonce')) { 
            return;
          }
      } 
      if ( !current_user_can( 'edit_posts', 'vtprd-rule' ) ) {
          return;
      }

      
      /* ******************************************
       The 'SAVE_POST' action is fired at odd times during updating.
       When it's fired early, there's no post data available.
       So checking for a blank post id is an effective solution.
      *************************************************** */      
      if ( !( $post->ID > ' ' ) ) { //a blank post id means no data to proces....
        return;
      } 
      //AND if we're here via an action other than a true save, do the action and exit stage left
      $action_type = $_REQUEST['action'];
      if ( in_array($action_type, array('trash', 'untrash', 'delete') ) ) {
        switch( $action_type ) {
            case 'trash':
                $this->vtprd_admin_trash_rule();  
              break; 
            case 'untrash':
                $this->vtprd_admin_untrash_rule();
              break;
            case 'delete':
                $this->vtprd_admin_delete_rule();  
              break;
        }
        return;
      }
      // lets through  $action_type == editpost                
      $vtprd_rule_update = new VTPRD_Rule_update;
  }
   
  
 /* ************************************************
 **   Admin - Delete Rule
 *************************************************** */
	public function vtprd_admin_delete_rule(){
     global $post, $vtprd_rules_set; 
  
 //error_log( print_r(  'Function begin - vtprd_admin_delete_rule', true ) );
          
      //v1.1.0.9 begin
      if( !isset( $post ) ) {    
        return;
      }       
      //v1.1.0.9 end
      
     if ( !( 'vtprd-rule' == $post->post_type ) ) {
      return;
     }        

     if ( !current_user_can( 'delete_posts', 'vtprd-rule' ) )  {
          return;
     }
    
    $vtprd_rule_delete = new VTPRD_Rule_delete;            
    $vtprd_rule_delete->vtprd_delete_rule();
        
    /* NO!! - the purchase history STAYS!
    if(defined('VTPRD_PRO_DIRNAME')) {
      vtprd_delete_lifetime_rule_info();
    }   
     */
  }
  
  
  /* ************************************************
  **   Admin - Trash Rule
  *************************************************** */   
	public function vtprd_admin_trash_rule(){
  
 //error_log( print_r(  'Function begin - vtprd_admin_trash_rule', true ) );
           
     global $post, $vtprd_rules_set; 
       //v1.1.0.9 begin
      if( !isset( $post ) ) {    
        return;
      }       
      //v1.1.0.9 end
          
     if ( !( 'vtprd-rule' == $post->post_type ) ) {
      return;
     }        
  
     if ( !current_user_can( 'delete_posts', 'vtprd-rule' ) )  {
          return;
     }  
     
     if(did_action('trash_post')) {    
         return;
    }
    
    $vtprd_rule_delete = new VTPRD_Rule_delete;            
    $vtprd_rule_delete->vtprd_trash_rule();

  }
  
  
 /* ************************************************
 **   Admin - Untrash Rule
 *************************************************** */   
	public function vtprd_admin_untrash_rule(){
  
 //error_log( print_r(  'Function begin - vtprd_admin_untrash_rule', true ) );
             
     global $post, $vtprd_rules_set; 
      //v1.1.0.9 begin
      if( !isset( $post ) ) {    
        return;
      }       
      //v1.1.0.9 end
           
     if ( !( 'vtprd-rule' == $post->post_type ) ) {
      return;
     }        

     if ( !current_user_can( 'delete_posts', 'vtprd-rule' ) )  {
          return;
     }       
    $vtprd_rule_delete = new VTPRD_Rule_delete;            
    $vtprd_rule_delete->vtprd_untrash_rule();
  }
  
  
  /* ************************************************
  **   Admin - Update PRODUCT Meta - include/exclude info
  *      from Meta box added to PRODUCT in rules-ui.php  
  *************************************************** */
	public function vtprd_admin_update_product_meta_info(){
  
 //error_log( print_r(  'Function begin - vtprd_admin_update_product_meta_info', true ) );
   
      global $post, $vtprd_rules_set, $vtprd_info;
      if ( !( $vtprd_info['parent_plugin_cpt'] == $post->post_type )) {
        return;
      }  
      if (( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
            return; 
      }

      if ( !current_user_can( 'edit_posts', $vtprd_info['parent_plugin_cpt'] ) ) {
          return;
      }
       //AND if we're here via an action other than a true save, exit stage left
      $action_type = $_REQUEST['action'];
      if ( in_array($action_type, array('trash', 'untrash', 'delete') ) ) {
        return;
      }
      
      /* ******************************************
       The 'SAVE_POST' action is fired at odd times during updating.
       When it's fired early, there's no post data available.
       So checking for a blank post id is an effective solution.
      *************************************************** */      
      if ( !( $post->ID > ' ' ) ) { //a blank post id means no data to proces....
        return;
      } 
      


      $includeOrExclude_option = $_REQUEST['includeOrExclude'];
      switch( $includeOrExclude_option ) {
        case 'includeAll':
        case 'excludeAll':   
            $includeOrExclude_checked_list = null; //initialize to null, as it's used later...
          break;
        case 'includeList':                  
        case 'excludeList':
            $includeOrExclude_checked_list = $_REQUEST['includeOrExclude-checked_list']; //contains list of checked rule post-id"s  v1.0.8.9                                               
          break;
      }

      $vtprd_includeOrExclude = array (
            'includeOrExclude_option'         => $includeOrExclude_option,
            'includeOrExclude_checked_list'   => $includeOrExclude_checked_list
             );
     
      //keep the add meta to retain the unique parameter...
      $vtprd_includeOrExclude_meta  = get_post_meta($post->ID, $vtprd_info['product_meta_key_includeOrExclude'], true);
      if ( $vtprd_includeOrExclude_meta  ) {
        update_post_meta($post->ID, $vtprd_info['product_meta_key_includeOrExclude'], $vtprd_includeOrExclude);
      } else {
        add_post_meta($post->ID, $vtprd_info['product_meta_key_includeOrExclude'], $vtprd_includeOrExclude, true);
      }
      
      //v1.1.0.7 begin
      //Update from product Publish box checkbox, labeled 'wholesale product'
      update_post_meta($post->ID, 'vtprd_wholesale_visibility', $_REQUEST['vtprd-wholesale-visibility']);
      //v1.1.0.7 end
      
  }
 

  /* ************************************************
  **   Admin - Activation Hook
  *************************************************** */  
	public function vtprd_activation_hook() {
  
 //error_log( print_r(  'Function begin - vtprd_activation_hook', true ) );
   
    global $wp_version, $vtprd_setup_options;
    //the options are added at admin_init time by the setup_options.php as soon as plugin is activated!!!
        
    $this->vtprd_create_discount_log_tables();

    $this->vtprd_maybe_add_wholesale_role(); //v1.0.9.0

    
    //v1.0.9.3 begin 
 
    //other edits moved to function vtprd_check_for_deactivation_action run at admin-init time
       
    //if plugin updated/installed, wipe out session for fresh start.
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    }    
    session_destroy(); 
    
    // Check if get_plugins() function exists. This is required on the front end of the
    // site, since it is in a file that is normally only loaded in the admin.
    if ( ! function_exists( 'get_plugins' ) ) {
    	require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    $all_plugins = get_plugins();

    foreach ($all_plugins as $key => $data) { 
      if ($key == 'pricing-deals-pro-for-woocommerce/vt-pricing-deals-pro.php') {
        $message  =  '<strong>' . __('Varktech Pricing Deals for WooCommerce has been updated / activated.' , 'vtprd') . '</strong>' ;
        $message .=  '<br><br><strong>' . __('Please Re-Activate  ** Varktech Pricing Deals PRO for WooCommerce **, if desired.' , 'vtprd') . '</strong>';
        $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        
        //activation notices must be deferred =>>  fatal test for Woo, etc in parent-functions
        $notices= get_option('vtprd_deferred_admin_notices', array());
        $notices[]= $admin_notices;
        update_option('vtprd_deferred_admin_notices', $notices);
               
        return;      
      } 
    }
    //v1.0.9.3 end

  }

   //v1.0.7.1 begin                          
   public function vtprd_admin_notice_version_mismatch() {
  
 //error_log( print_r(  'Function begin - vtprd_admin_notice_version_mismatch', true ) );
    
      $message  =  '<strong>' . __('Please also update plugin: ' , 'vtprd') . ' &nbsp;&nbsp;'  .VTPRD_PRO_PLUGIN_NAME . '</strong>' ;
      $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Your Pro Version = ' , 'vtprd') .VTPRD_PRO_VERSION. ' &nbsp;&nbsp;<strong>' . __(' The minimum required Pro Version = ' , 'vtprd') .VTPRD_MINIMUM_PRO_VERSION .'</strong>' ;      
      $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Please delete the old Pro plugin from your installation (no rules will be affected).'  , 'vtprd');
      $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Use your original download credentials, or your name and email address, and'  , 'vtprd');
      $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Go to ', 'vtprd');
      $message .=  '<a target="_blank" href="http://www.varktech.com/download-pro-plugins/">Varktech Downloads</a>';
      $message .=   __(', download and install the newest <strong>'  , 'vtprd') .VTPRD_PRO_PLUGIN_NAME. '</strong>' ;
      
      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      echo $admin_notices;
      //v1.0.9.3 begin
      /*  v1.1.0.1 removed, so that the message will stay active until sorted!
      $plugin = VTPRD_PRO_PLUGIN_SLUG;
			if( is_plugin_active($plugin) ) {
			   deactivate_plugins( $plugin );
      }
      */
      //v1.0.9.3 end
      return;    
  }   
   //v1.0.7.1 end  

   public function vtprd_admin_notice_coupon_enable_required() {
     
 //error_log( print_r(  'Function begin - vtprd_admin_notice_coupon_enable_required', true ) );
  
      $message  =  '<strong>' . __('In order for the "Pricing Deals" plugin to function successfully, the Woo Coupons Setting must be on, and it is currently off.' , 'vtprd') . '</strong>' ;
      $message .=  '<br><br>' . __('Please go to the Woocommerce/Settings page.  Under the "Checkout" tab, check the box next to "Enable the use of coupons" and click on the "Save Changes" button.'  , 'vtprd');
      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      echo $admin_notices;
      return;    
  } 

   //v1.1.1 new function
   public function vtprd_admin_notice_woocommerce_required() {
  
 //error_log( print_r(  'Function begin - vtprd_admin_notice_woocommerce_required', true ) );
     
      $message  =  '<strong>' . __('In order for the "Pricing Deals" plugin to function, the WooCommerce must be installed and active!! ' , 'vtprd') . '</strong>' ;
      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      echo $admin_notices;     
      return;    
  } 

   //v1.1.1 new function
   public function vtprd_admin_notice_cant_use_unit_price() {
  
 //error_log( print_r(  'Function begin - vtprd_admin_notice_cant_use_unit_price', true ) );
      
      $message  =  '*******************************&nbsp;&nbsp;'. '<span style="color: blue !important;">' . __('Pricing Deal Settings &nbsp; Change &nbsp; ** Required **'  , 'vtprd') .'</span><br><br>';
      $message .=  __('<strong>Pricing Deals</strong> is fully compatible with &nbsp; <em>Woocommerce Product Addons</em> &nbsp; and &nbsp; <em>Woocommerce Measurement Price Calculator</em> . ' , 'vtprd')  ;
      $message .=  '<br><br>**&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('When either of these two plugins are installed and active, <strong>**A CHANGE MUST BE MADE** on your Pricing Deals Settings page.</strong>  ' , 'vtprd') ;
      $message .=  '<br><br>**&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('Please go to the Pricing Deals/Settings page.  <em>At "Unit Price Discount or Coupon Discount" select "Coupon Discount"</em> and click on the "Save Changes" button.'  , 'vtprd');
      $message .=  '<br><br>' . __('(this is due to system limitations in the two named plugins.)'  , 'vtprd');     
      $message .=  '<br><br>*******************************';
      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      echo $admin_notices;     
      return;      
  } 
  
       
  /* ************************************************
  **   Admin - **Uninstall** Hook and cleanup
  *************************************************** */ 
	public function vtprd_uninstall_hook() {
  
 //error_log( print_r(  'Function begin - vtprd_uninstall_hook', true ) );
      
      if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
      	return;
        //exit ();
      }
  
      delete_option('vtprd_setup_options');
      $vtprd_nuke = new VTPRD_Rule_delete;            
      $vtprd_nuke->vtprd_nuke_all_rules();
      $vtprd_nuke->vtprd_nuke_all_rule_cats();
      
  }
  
   
    //Add Custom Links to PLUGIN page action links                     ///wp-admin/edit.php?post_type=vtmam-rule&page=vtmam_setup_options_page
  public function vtprd_custom_action_links( $links ) { 
     
 //error_log( print_r(  'Function begin - vtprd_custom_action_links', true ) );
  
		$plugin_links = array(
			'<a href="' . admin_url( 'edit.php?post_type=vtprd-rule&page=vtprd_setup_options_page' ) . '">' . __( 'Settings', 'vtprd' ) . '</a>',
			'<a href="http://www.varktech.com">' . __( 'Docs', 'vtprd' ) . '</a>'
		);
		return array_merge( $plugin_links, $links );
	}



	public function vtprd_create_discount_log_tables() {
     
 //error_log( print_r(  'Function begin - vtprd_create_discount_log_tables', true ) );
    
    global $wpdb;
    //Cart Audit Trail Tables
  	
    $wpdb->hide_errors();    
  	$collate = '';  
    if ( $wpdb->has_cap( 'collation' ) ) {  //mwn04142014
  		if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
  		if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
    }
     
      
  //  $is_this_purchLog = $wpdb->get_var("SHOW TABLES LIKE `".VTPRD_PURCHASE_LOG."` ");
    $table_name =  VTPRD_PURCHASE_LOG;
    $is_this_purchLog = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
    if ( $is_this_purchLog  == VTPRD_PURCHASE_LOG) {
      return;
    }

     
    $sql = "
        CREATE TABLE  `".VTPRD_PURCHASE_LOG."` (
              id bigint NOT NULL AUTO_INCREMENT,
              cart_parent_purchase_log_id bigint,
              purchaser_name VARCHAR(50), 
              purchaser_ip_address VARCHAR(50),                
              purchase_date DATE NULL,
              cart_total_discount_currency DECIMAL(11,2),      
              ruleset_object TEXT,
              cart_object TEXT,
          KEY id (id, cart_parent_purchase_log_id)
        ) $collate ;      
        ";
 
     $this->vtprd_create_table( $sql );
     
    $sql = "
        CREATE TABLE  `".VTPRD_PURCHASE_LOG_PRODUCT."` (
              id bigint NOT NULL AUTO_INCREMENT,
              purchase_log_row_id bigint,
              product_id bigint,
              product_title VARCHAR(100),
              cart_parent_purchase_log_id bigint,
              product_orig_unit_price   DECIMAL(11,2),     
              product_total_discount_units   DECIMAL(11,2),
              product_total_discount_currency DECIMAL(11,2),
              product_total_discount_percent DECIMAL(11,2),
          KEY id (id, purchase_log_row_id, product_id)
        ) $collate ;      
        ";
 
     $this->vtprd_create_table( $sql );
     
    $sql = "
        CREATE TABLE  `".VTPRD_PURCHASE_LOG_PRODUCT_RULE."` (
              id bigint NOT NULL AUTO_INCREMENT,
              purchase_log_product_row_id bigint,
              product_id bigint,
			  rule_id bigint,
              cart_parent_purchase_log_id bigint,
              product_rule_discount_units   DECIMAL(11,2),
              product_rule_discount_dollars DECIMAL(11,2),
              product_rule_discount_percent DECIMAL(11,2),
          KEY id (id, purchase_log_product_row_id, rule_id)
        ) $collate ;      
        ";
 
     $this->vtprd_create_table( $sql );



  }
  
	public function vtprd_create_table( $sql ) {
     
 //error_log( print_r(  'Function begin - vtprd_create_table', true ) );
       
      global $wpdb;
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');	        
      dbDelta($sql);
      return; 
   } 
                            
                            
 
  //****************************************
  //v1.0.7.4 new function
  //v1.0.8.8 refactored for new 'Wholesale Tax Free' role, buy_tax_free role capability
  //  adds in default 'Wholesale Buyer' + new 'Wholesale Tax Free'  role at iadmin time  
  //v1.0.9.0 moved here from functions.php, so it only executes on insall...
  //****************************************
  Public function vtprd_maybe_add_wholesale_role(){ 
     
 //error_log( print_r(  'Function begin - vtprd_maybe_add_wholesale_role', true ) );
         
		global $wp_roles;
	
		if ( class_exists( 'WP_Roles' ) ) {
      if ( !isset( $wp_roles ) ) { 
			   $wp_roles = new WP_Roles();
      }
    }

		$capabilities = array( 
			'read' => true,
			'edit_posts' => false,
			'delete_posts' => false,
		); 
     
    $wholesale_buyer_role_name    =  __('Wholesale Buyer' , 'vtprd');
    $wholesale_tax_free_role_name =  __('Wholesale Tax Free' , 'vtprd');
  

		if ( is_object( $wp_roles ) ) { 

      If ( !get_role( $wholesale_buyer_role_name ) ) {
    			add_role ('wholesale_buyer', $wholesale_buyer_role_name, $capabilities );    
    			$role = get_role( 'wholesale_buyer' );
          $role->add_cap( 'buy_wholesale' ); 
    			$role->add_cap( 'wholesale' ); //v1.1.0.7
      } else { //v1.1.0.7 begin
    			$role = get_role( 'wholesale_buyer' );
          $role->add_cap( 'wholesale' );     
      }  //v1.1.0.7 end

      If ( !get_role(  $wholesale_tax_free_role_name ) ) {
    			add_role ('wholesale_tax_free',  $wholesale_tax_free_role_name, $capabilities );    
    			$role = get_role( 'wholesale_tax_free' ); 
    			$role->add_cap( 'buy_tax_free' );
          $role->add_cap( 'wholesale' ); //v1.1.0.7
      } else { //v1.1.0.7 begin
    			$role = get_role( 'wholesale_tax_free' ); 
          $role->add_cap( 'wholesale' ); 
      }  //v1.1.0.7 end
/*
      //v1.1.0.7 begin
      $admin = __('administrator' , 'vtprd');
      If ( get_role(  $admin ) ) {
        $role = get_role( $admin );
        $role->add_cap( 'buy_wholesale' );      
  			$role->add_cap( 'buy_tax_free' );
        $role->add_cap( 'wholesale' ); 
      }
      $admin = __('admin' , 'vtprd');
      If ( get_role(  $admin ) ) {
        $role = get_role( $admin );
        $role->add_cap( 'buy_wholesale' );      
  			$role->add_cap( 'buy_tax_free' );
        $role->add_cap( 'wholesale' ); 
      }  
      */    
      //v1.1.0.7 end
		}
       
    return;
  }  


  
} //end class
$vtprd_controller = new VTPRD_Controller;
     
//has to be out here, accessing the plugin instance
if (is_admin()){
  register_activation_hook(__FILE__, array($vtprd_controller, 'vtprd_activation_hook'));
//mwn0405
//  register_uninstall_hook (__FILE__, array($vtprd_controller, 'vtprd_uninstall_hook'));
}

  
