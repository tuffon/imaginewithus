<?php
/**
 * WC_Gateway_Amazon_Payments_Advanced
 */
class WC_Gateway_Amazon_Payments_Advanced extends WC_Payment_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->method_title = 'Amazon Payments Advanced';
		$this->id           = 'amazon_payments_advanced';
		$this->icon         = apply_filters( 'woocommerce_amazon_pa_logo', plugins_url( 'assets/images/amazon-payments.gif', plugin_dir_path( __FILE__ ) ) );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables
		$this->title           = $this->get_option( 'title' );
		$this->seller_id       = $this->get_option( 'seller_id' );
		$this->mws_access_key  = $this->get_option( 'mws_access_key' );
		$this->secret_key      = $this->get_option( 'secret_key' );
		$this->sandbox         = $this->get_option( 'sandbox' );
		$this->payment_capture = $this->get_option( 'payment_capture' );

		// Get refererence ID
		$this->reference_id = WC_Amazon_Payments_Advanced_API::get_reference_id();

		// Handling for the review page of the German Market Plugin
		if ( empty( $this->reference_id ) ) {
			if ( isset( $_SESSION['first_checkout_post_array']['amazon_reference_id'] ) ) {
				$this->reference_id = $_SESSION['first_checkout_post_array']['amazon_reference_id'];
			}
		}

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Check if the gateway is available for use
	 *
	 * @return bool
	 */
	function is_available() {
		return parent::is_available() && ! empty( $this->reference_id );
	}

	/**
	 * Has fields.
	 *
	 * @return bool
	 */
	public function has_fields() {
		return is_checkout_pay_page();
	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields() {
		if ( $this->has_fields() ) {
			echo '<div id="amazon_wallet_widget"></div>';
			echo '<input type="hidden" name="amazon_reference_id" value="' . esc_attr( $this->reference_id ) . '" />';
		}
	}

	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 */
	public function admin_options() {
		?>
		<h3><?php echo $this->method_title; ?></h3>

		<?php if ( ! $this->seller_id ) : ?>
			<div class="updated woocommerce-message"><div class="squeezer">
				<h4><?php _e( 'Need an Amazon Payments Advanced account?', 'woocommerce-gateway-amazon-payments-advanced' ); ?></h4>
				<p class="submit">
					<a class="button button-primary" href="<?php echo esc_url( WC_Amazon_Payments_Advanced_API::get_register_url() ); ?>"><?php _e( 'Signup now', 'woocommerce-gateway-amazon-payments-advanced' ); ?></a>
				</p>
			</div></div>
		<?php endif; ?>

		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table><!--/.form-table-->
		<?php
	}

	/**
	 * Init payment gateway form fields
	 */
	function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-gateway-amazon-payments-advanced' ),
				'label'       => __( 'Enable Amazon Payments Advanced', 'woocommerce-gateway-amazon-payments-advanced' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-gateway-amazon-payments-advanced' ),
				'type'        => 'text',
				'description' => __( 'Payment method title that the customer will see on your website.', 'woocommerce-gateway-amazon-payments-advanced' ),
				'default'     => __( 'Amazon', 'woocommerce-gateway-amazon-payments-advanced' ),
				'desc_tip'    => true
			),
			'seller_id' => array(
				'title'       => __( 'Seller ID', 'woocommerce-gateway-amazon-payments-advanced' ),
				'type'        => 'text',
				'description' => __( 'Obtained from your Amazon account. Also known as the "Merchant ID". Usually found under Settings > Integrations after logging into your merchant account.', 'woocommerce-gateway-amazon-payments-advanced' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'mws_access_key' => array(
				'title'       => __( 'MWS Access Key', 'woocommerce-gateway-amazon-payments-advanced' ),
				'type'        => 'text',
				'description' => __( 'Obtained from your Amazon account. You can get these keys by logging into Seller Central and viewing the MWS Access Key section under the Integration tab.', 'woocommerce-gateway-amazon-payments-advanced' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'secret_key' => array(
				'title'       => __( 'Secret Key', 'woocommerce-gateway-amazon-payments-advanced' ),
				'type'        => 'text',
				'description' => __( 'Obtained from your Amazon account. You can get these keys by logging into Seller Central and viewing the MWS Access Key section under the Integration tab.', 'woocommerce-gateway-amazon-payments-advanced' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'sandbox' => array(
				'title'       => __( 'Use Sandbox', 'woocommerce-gateway-amazon-payments-advanced' ),
				'label'       => __( 'Enable sandbox mode during testing and development - live payments will not be taken if enabled.', 'woocommerce-gateway-amazon-payments-advanced' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'payment_capture' => array(
				'title'       => __( 'Payment Capture', 'woocommerce-gateway-amazon-payments-advanced' ),
				'type'        => 'select',
				'description' => '',
				'default'     => '',
				'options'     => array(
					''          => __( 'Authorize and Capture the payment when the order is placed.', 'woocommerce-gateway-amazon-payments-advanced' ),
					'authorize' => __( 'Authorize the payment when the order is placed.', 'woocommerce-gateway-amazon-payments-advanced' ),
					'manual'    => __( 'Don’t Authorize the payment when the order is placed (i.e. for pre-orders).', 'woocommerce-gateway-amazon-payments-advanced' )
				)
			),
			'cart_button_display_mode' => array(
				'title'       => __( 'Cart login button display', 'woocommerce-gateway-amazon-payments-advanced' ),
				'description' => __( 'How the login with Amazon button gets displayed on the cart page.' ),
				'type'        => 'select',
				'options'     => array(
					'button'   => __( 'Button', 'woocommerce-gateway-amazon-payments-advanced' ),
					'banner'   => __( 'Banner', 'woocommerce-gateway-amazon-payments-advanced' ),
					'disabled' => __( 'Disabled', 'woocommerce-gateway-amazon-payments-advanced' ),
				),
				'default'     => 'button',
				'desc_tip'    => true
			),
			'hide_standard_checkout_button' => array(
				'title'   => __( 'Standard checkout button', 'woocommerce-gateway-amazon-payments-advanced' ),
				'type'    => 'checkbox',
				'label'   => __( 'Hide standard checkout button on cart page', 'woocommerce-gateway-amazon-payments-advanced' ),
				'default' => 'no'
			)
	   );
	}

	/**
	 * process_payment function.
	 *
	 * @access public
	 * @param mixed $order_id
	 * @return void
	 */
	function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		$amazon_reference_id = isset( $_POST['amazon_reference_id'] ) ? wc_clean( $_POST['amazon_reference_id'] ) : '';

		try {

			if ( ! $amazon_reference_id ) {
				throw new Exception( __( 'An Amazon payment method was not chosen.', 'woocommerce-gateway-amazon-payments-advanced' ) );
			}

			// Update order reference with amounts
			$response = WC_Amazon_Payments_Advanced_API::request( array(
				'Action'                                                       => 'SetOrderReferenceDetails',
				'AmazonOrderReferenceId'                                       => $amazon_reference_id,
				'OrderReferenceAttributes.OrderTotal.Amount'                   => $order->get_total(),
				'OrderReferenceAttributes.OrderTotal.CurrencyCode'             => strtoupper( get_woocommerce_currency() ),
				'OrderReferenceAttributes.SellerNote'                          => sprintf( __( 'Order %s from %s.', 'woocommerce-gateway-amazon-payments-advanced' ), $order->get_order_number(), urlencode( remove_accents( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) ) ),
				'OrderReferenceAttributes.SellerOrderAttributes.SellerOrderId' => $order->get_order_number(),
				'OrderReferenceAttributes.SellerOrderAttributes.StoreName'     => remove_accents( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ),
				'OrderReferenceAttributes.PlatformId'                          => 'A1BVJDFFHQ7US4'
			) );

			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			if ( isset( $response->Error->Message ) ) {
				throw new Exception( (string) $response->Error->Message );
			}

			// Confirm order reference
			$response = WC_Amazon_Payments_Advanced_API::request( array(
				'Action'                 => 'ConfirmOrderReference',
				'AmazonOrderReferenceId' => $amazon_reference_id
			) );

			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			if ( isset( $response->Error->Message ) ) {
				throw new Exception( (string) $response->Error->Message );
			}

			// Get FULL address details and save them to the order
			$response = WC_Amazon_Payments_Advanced_API::request( array(
				'Action'                 => 'GetOrderReferenceDetails',
				'AmazonOrderReferenceId' => $amazon_reference_id
			) );

			if ( ! is_wp_error( $response ) && isset( $response->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination ) ) {
				$buyer          = (array) $response->GetOrderReferenceDetailsResult->OrderReferenceDetails->Buyer;
				$address        = (array) $response->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination;
				$billing_name   = explode( ' ', $buyer['Name'] );
				$shipping_name  = explode( ' ', $address['Name'] );

				// Get first and last names
				$billing_last   = array_pop( $billing_name );
				$shipping_last  = array_pop( $shipping_name );
				$billing_first  = implode( ' ', $billing_name );
				$shipping_first = implode( ' ', $shipping_name );

				update_post_meta( $order_id, '_billing_first_name', $billing_first );
				update_post_meta( $order_id, '_billing_last_name', $billing_last );
				update_post_meta( $order_id, '_billing_email', $buyer['Email'] );

				if ( isset( $buyer['Phone'] ) ) {
					update_post_meta( $order_id, '_billing_phone', $buyer['Phone'] );
				} elseif ( isset( $address['Phone'] ) ) {
					update_post_meta( $order_id, '_billing_phone', $address['Phone'] );
				}

				update_post_meta( $order_id, '_shipping_first_name', $shipping_first );
				update_post_meta( $order_id, '_shipping_last_name', $shipping_last );

				// Format address and map to WC fields
				$address_lines = array();

				if ( ! empty( $address['AddressLine1'] ) ) {
					$address_lines[] = $address['AddressLine1'];
				}
				if ( ! empty( $address['AddressLine2'] ) ) {
					$address_lines[] = $address['AddressLine2'];
				}
				if ( ! empty( $address['AddressLine3'] ) ) {
					$address_lines[] = $address['AddressLine3'];
				}

				if ( 3 === sizeof( $address_lines ) ) {
					update_post_meta( $order_id, '_shipping_company', $address_lines[0] );
					update_post_meta( $order_id, '_shipping_address_1', $address_lines[1] );
					update_post_meta( $order_id, '_shipping_address_2', $address_lines[2] );
				} elseif ( 2 === sizeof( $address_lines ) ) {
					update_post_meta( $order_id, '_shipping_address_1', $address_lines[0] );
					update_post_meta( $order_id, '_shipping_address_2', $address_lines[1] );
				} elseif ( sizeof( $address_lines ) ) {
					update_post_meta( $order_id, '_shipping_address_1', $address_lines[0] );
				}

				if ( isset( $address['City'] ) ) {
					update_post_meta( $order_id, '_shipping_city', $address['City'] );
				}

				if ( isset( $address['PostalCode'] ) ) {
					update_post_meta( $order_id, '_shipping_postcode', $address['PostalCode'] );
				}

				if ( isset( $address['StateOrRegion'] ) ) {
					update_post_meta( $order_id, '_shipping_state', $address['StateOrRegion'] );
				}

				if ( isset( $address['CountryCode'] ) ) {
					update_post_meta( $order_id, '_shipping_country', $address['CountryCode'] );
				}
			}

			// Store reference ID in the order
			update_post_meta( $order_id, 'amazon_reference_id', $amazon_reference_id );

			switch ( $this->payment_capture ) {
				case 'manual' :

					// Mark as on-hold
					$order->update_status( 'on-hold', __( 'Amazon order opened. Use the "Amazon Payments Advanced" box to authorize and/or capture payment. Authorized payments must be captured within 7 days.', 'woocommerce-gateway-amazon-payments-advanced' ) );

					// Reduce stock levels
					$order->reduce_order_stock();

				break;
				case 'authorize' :

					// Authorize only
					$result = WC_Amazon_Payments_Advanced_API::authorize_payment( $order_id, $amazon_reference_id, false );

					if ( $result ) {
						// Mark as on-hold
						$order->update_status( 'on-hold', __( 'Amazon order opened. Use the "Amazon Payments Advanced" box to authorize and/or capture payment. Authorized payments must be captured within 7 days.', 'woocommerce-gateway-amazon-payments-advanced' ) );

						// Reduce stock levels
						$order->reduce_order_stock();
					} else {
						$order->update_status( 'failed', __( 'Could not authorize Amazon payment.', 'woocommerce-gateway-amazon-payments-advanced' ) );
					}

				break;
				default :

					// Capture
					$result = WC_Amazon_Payments_Advanced_API::authorize_payment( $order_id, $amazon_reference_id, true );

					if ( $result ) {
						// Payment complete
						$order->payment_complete();
					} else {
						$order->update_status( 'failed', __( 'Could not authorize Amazon payment.', 'woocommerce-gateway-amazon-payments-advanced' ) );
					}

				break;
			}

			// Remove cart
			WC()->cart->empty_cart();

			// Return thank you page redirect
			return array(
				'result' 	=> 'success',
				'redirect'	=> $this->get_return_url( $order )
			);

		} catch( Exception $e ) {
			wc_add_notice( __( 'Error:', 'woocommerce-gateway-amazon-payments-advanced' ) . ' ' . $e->getMessage(), 'error' );
			return;
		}
	}
}
