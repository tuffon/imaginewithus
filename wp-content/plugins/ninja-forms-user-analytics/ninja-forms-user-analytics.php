<?php
/*
Plugin Name: Ninja Forms - User Analytics
Plugin URI: https://ninjaforms.com/extensions/user-analytics/
Description: Add user analytics to Ninja Forms.
Version: 1.2.5
Author: Never5
Author URI: http://www.never5.com
*/

/*
	Copyright 2015 - Never5

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'NINJA_FORMS_EDD_SL_STORE_URL' ) ) {
	define( 'NINJA_FORMS_EDD_SL_STORE_URL', 'http://wpninjas.com/' );
}
define( 'NINJA_FORMS_EDD_UA_PRODUCT_NAME', 'User Analytics' );
define( 'NINJA_FORMS_UA_VERSION', '1.2.5' );
define( 'NINJA_FORMS_UA_AUTHOR', 'Never5' );


/**
 *    Setup the updater & license page
 */
function ninja_forms_ua_setup_license() {
	if ( class_exists( 'NF_Extension_Updater' ) ) {
		$NF_Extension_Updater = new NF_Extension_Updater( NINJA_FORMS_EDD_UA_PRODUCT_NAME, NINJA_FORMS_UA_VERSION, NINJA_FORMS_UA_AUTHOR, __FILE__, 'ua' );
	}
}

add_action( 'admin_init', 'ninja_forms_ua_setup_license' );


class NF_User_Analytics {

	private $fields;

	/**
	 * Initialize the plugin
	 */
	public function __construct() {
		// set up custom fields
		$this->fields['ip_address']      = array(
			'name'              => 'IP Address',
			'display_function'  => 'ip_address_display',
			'sub_edit_function' => 'ip_address_sub_edit_display'
		);
		$this->fields['browser']         = array(
			'name'              => 'Browser',
			'display_function'  => 'browser_display',
			'sub_edit_function' => 'browser_sub_edit_display'
		);
		$this->fields['browser_version'] = array(
			'name'              => 'Browser Version',
			'display_function'  => 'browser_version_display',
			'sub_edit_function' => 'browser_version_sub_edit_display'
		);
		$this->fields['os']              = array(
			'name'              => 'Operating System',
			'display_function'  => 'os_display',
			'sub_edit_function' => 'os_sub_edit_display'
		);
		$this->fields['country']         = array(
			'name'              => 'Country',
			'display_function'  => 'country_display',
			'sub_edit_function' => 'country_sub_edit_display'
		);
		$this->fields['region']          = array(
			'name'              => 'Region (State)',
			'display_function'  => 'region_display',
			'sub_edit_function' => 'region_sub_edit_display'
		);
		$this->fields['city']            = array(
			'name'              => 'City',
			'display_function'  => 'city_display',
			'sub_edit_function' => 'city_sub_edit_display'
		);
		$this->fields['latitude']        = array(
			'name'              => 'Latitude',
			'display_function'  => 'latitude_display',
			'sub_edit_function' => 'latitude_sub_edit_display'
		);
		$this->fields['longitude']       = array(
			'name'              => 'Longitude',
			'display_function'  => 'longitude_display',
			'sub_edit_function' => 'longitude_sub_edit_display'
		);
		$this->fields['utm_campaign']    = array(
			'name'              => 'UTM Campaign',
			'display_function'  => 'utm_campaign_display',
			'sub_edit_function' => 'utm_campaign_sub_edit_display'
		);
		$this->fields['utm_source']      = array(
			'name'              => 'UTM Source',
			'display_function'  => 'utm_source_display',
			'sub_edit_function' => 'utm_source_sub_edit_display'
		);
		$this->fields['utm_medium']      = array(
			'name'              => 'UTM Medium',
			'display_function'  => 'utm_medium_display',
			'sub_edit_function' => 'utm_medium_sub_edit_display'
		);
		$this->fields['utm_content']     = array(
			'name'              => 'UTM Content',
			'display_function'  => 'utm_content_display',
			'sub_edit_function' => 'utm_content_sub_edit_display'
		);
		$this->fields['utm_term']        = array(
			'name'              => 'UTM Term',
			'display_function'  => 'utm_term_display',
			'sub_edit_function' => 'utm_term_sub_edit_display'
		);
		$this->fields['referer']         = array(
			'name'              => 'URL Referer',
			'display_function'  => 'referer_display',
			'sub_edit_function' => 'referer_edit_display'
		);

		// load scripts
		add_action( 'ninja_forms_display_js', array( $this, "load_scripts" ) );

		// ajax
		add_action( 'wp_ajax_nfua_data', array( $this, 'ajax_host_info' ) );
		add_action( 'wp_ajax_nopriv_nfua_data', array( $this, 'ajax_host_info' ) );

		// load custom fields and such
		$this->load_field_settings_tab();
		$this->load_fields();
	}

	/**
	 * WP AJAX 'nfua_data' callback
	 */
	public function ajax_host_info() {
		$response = wp_remote_get( 'http://api.hostip.info/get_json.php?position=true&ip=' . $_SERVER['REMOTE_ADDR'] );
		if ( ! is_wp_error( $response ) ) {
			echo $response['body'];
		}

		exit;
	}

	/**
	 * Load our scripts
	 */
	function load_scripts( $form_id ) {
		// first let's get all of the fields on the page
		$all_fields = ninja_forms_get_fields_by_form_id( $form_id );

		// determine if one this plugin's fields are present
		$nfuaFields       = array(
			"ip_address",
			"browser",
			"browser_version",
			"os",
			"country",
			"region",
			"city",
			"latitude",
			"longitude"
		);
		$nfuaFieldPresent = false;
		foreach ( $all_fields as $key => $value ) {
			if ( isset( $value['type'] ) && in_array( $value['type'], $nfuaFields ) ) {
				$nfuaFieldPresent = true;
			}
		}

		// if one of our fields is present in the form then print out the scripts
		if ( $nfuaFieldPresent ) {
			// load main script
			wp_enqueue_script( 'nf-user-analytics', plugins_url( 'assets/scripts/script.js', __FILE__ ), array( 'jquery' ), NINJA_FORMS_UA_VERSION, true );

			wp_localize_script( 'nf-user-analytics', 'nfua', array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			) );
		}
	}


	/**
	 * Load our custom field settings tab to hold all of our fields
	 */
	function load_field_settings_tab() {
		// check to make sure that the function exists (aka plugin is loaded)
		if ( function_exists( 'ninja_forms_register_sidebar' ) ) {
			// now register the new sidebar
			$args = array(
				'name'             => __( 'User Analytics Fields', 'ninja-forms-ua' ),
				'page'             => 'ninja-forms',
				'tab'              => 'builder',
				'display_function' => 'ninja_forms_sidebar_display_fields'
			);
			ninja_forms_register_sidebar( 'user_analytics_fields', $args );
		}
	}


	/**
	 * Load our custom form fields
	 */
	function load_fields() {
		// register the custom fields
		if ( function_exists( 'ninja_forms_register_field' ) ) {
			foreach ( $this->fields as $key => $value ) {
				$temp_args = array(
					'name'              => __( $value['name'], 'ninja-forms-ua' ),
					'display_function'  => array( $this, $value['display_function'] ),
					'sub_edit_function' => array( $this, $value['sub_edit_function'] ),
					'edit_label'        => false,
					'edit_label_pos'    => false,
					'edit_req'          => false,
					'edit_custom_class' => false,
					'edit_help'         => false,
					'sidebar'           => 'user_analytics_fields',
					'display_label'     => false,
					'display_wrap'      => false
				);
				ninja_forms_register_field( $key, $temp_args );
			}
		}
	}


	/**
	 * Populate Fields from URL parameters. Set a default if the parameter isn't present.
	 */
	function populate_url_parameter_fields( $param ) {
		return ( isset( $_GET[ $param ] ) ? $_GET[ $param ] : "n/a" );
	}


	/**
	 *    Display the IP Address field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function ip_address_display( $field_id, $data ) {
		// Get our user's IP address.
		$ip = $this->get_ip_address();
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-ip-address"
		       value="<?php echo $ip; ?>">
		<?php
	}


	/**
	 *    Display the IP Address field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function ip_address_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">IP
				Address</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-ip-address"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the Browser field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function browser_display( $field_id, $data ) {
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-browser" value="n/a">
		<?php
	}


	/**
	 *    Display the Browser field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function browser_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">Browser</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-browser"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the Browser Version field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function browser_version_display( $field_id, $data ) {
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-browser-version" value="n/a">
		<?php
	}


	/**
	 *    Display the Browser Version field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function browser_version_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">Browser
				Version</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-browser-version"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the Operating System field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function os_display( $field_id, $data ) {
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-os" value="n/a">
		<?php
	}


	/**
	 *    Display the OS field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function os_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">Operating
				System</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-os"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the Country field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function country_display( $field_id, $data ) {
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-country" value="n/a">
		<?php
	}


	/**
	 *    Display the Country field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function country_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">Country</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-country"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the Region (State) field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function region_display( $field_id, $data ) {
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-region" value="n/a">
		<?php
	}


	/**
	 *    Display the Region (State) field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function region_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">Region
				(State)</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-region"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the City field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function city_display( $field_id, $data ) {
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-city" value="n/a">
		<?php
	}


	/**
	 *    Display the City field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function city_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>"
			       id="ninja_forms_field_<?php echo $field_id; ?>">City</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-city"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the Latitude field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function latitude_display( $field_id, $data ) {
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-latitude" value="n/a">
		<?php
	}


	/**
	 *    Display the Latitude field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function latitude_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">Latitude</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-latitude"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the Longitude field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function longitude_display( $field_id, $data ) {
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-longitude" value="n/a">
		<?php
	}


	/**
	 *    Display the OS field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function longitude_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">Longitude</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-longitude"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the UTM Campaign field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function utm_campaign_display( $field_id, $data ) {
		$value = $this->populate_url_parameter_fields( 'utm_campaign' );
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-utm-campaign"
		       value="<?php echo $value; ?>">
		<?php
	}


	/**
	 *    Display the UTM Campaign field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function utm_campaign_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">UTM
				Campaign</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-utm-campaign"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the UTM Source field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function utm_source_display( $field_id, $data ) {
		$value = $this->populate_url_parameter_fields( 'utm_source' );
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-utm-source"
		       value="<?php echo $value; ?>">
		<?php
	}


	/**
	 *    Display the UTM Source field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function utm_source_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">UTM
				Source</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-utm-source"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the UTM Medium field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function utm_medium_display( $field_id, $data ) {
		$value = $this->populate_url_parameter_fields( 'utm_medium' );
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-utm-medium"
		       value="<?php echo $value; ?>">
		<?php
	}


	/**
	 *    Display the UTM Medium field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function utm_medium_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">UTM
				Medium</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-utm-medium"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the UTM Content field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function utm_content_display( $field_id, $data ) {
		$value = $this->populate_url_parameter_fields( 'utm_content' );
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-utm-content"
		       value="<?php echo $value; ?>">
		<?php
	}


	/**
	 *    Display the UTM Content field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function utm_content_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">UTM
				Content</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-utm-content"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the UTM Term field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function utm_term_display( $field_id, $data ) {
		$value = $this->populate_url_parameter_fields( 'utm_term' );
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-utm-term"
		       value="<?php echo $value; ?>">
		<?php
	}


	/**
	 *    Display the UTM Term field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function utm_term_sub_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">UTM
				Term</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-utm-term"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 *    Display the Referer field
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function referer_display( $field_id, $data ) {
		$value = $this->get_url_referer();
		?>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-referer"
		       value="<?php echo $value; ?>">
		<?php
	}


	/**
	 *    Display the UTM Term field on the Edit Submission page
	 *
	 *    $field_id is the id of the field currently being displayed.
	 *    $data is an array the possibly modified field data for the current field.
	 */
	function referer_edit_display( $field_id, $data ) {
		?>
		<div class="field-wrap text-wrap label-left">
			<label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>">URL
				Referer</label>
			<input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-referer"
			       value="<?php echo $data['default_value'] ?>">
		</div>
		<?php
	}


	/**
	 * Get the users IP address
	 *
	 * @return string
	 * @since  1.2.1
	 */
	function get_ip_address() {
		// if HTTP_X_FORWARDED_FOR key is present we should use it
		if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			if ( strpos( $_SERVER['HTTP_X_FORWARDED_FOR'], ',' ) > 0 ) {
				$addr = explode( ",", $_SERVER['HTTP_X_FORWARDED_FOR'] );
				$ip   = trim( $addr[0] );
			} else {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		} else {
			// as a backup use the standard REMOTE_ADDR
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}


	/**
	 *  Get all User Analytics fields.
	 *  This allows 3rd party plugins to get UA fields.
	 */
	function get_ua_fields() {
		return $this->fields;
	}


	/**
	 * Get the URL Referer
	 *
	 * @return string
	 * @since
	 */
	function get_url_referer() {
		return sanitize_text_field( $_SERVER["HTTP_REFERER"] );
	}

}


/**
 *    Create singleton instance of this plugin
 */
function ninja_forms_ua_initiate() {
	global $NF_User_Analytics;
	$NF_User_Analytics = new NF_User_Analytics();
}

add_action( 'init', 'ninja_forms_ua_initiate' );


if ( ! isset( $NF_User_Analytics ) ) {
	$NF_User_Analytics = null;
}
