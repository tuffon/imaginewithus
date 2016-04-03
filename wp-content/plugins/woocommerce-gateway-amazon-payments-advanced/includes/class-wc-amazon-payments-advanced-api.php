<?php
/**
 * Amazon Payments Advanced API class
 */
class WC_Amazon_Payments_Advanced_API {

	/**
	 * API Endpoints
	 *
	 * @var array
	 */
	protected static $endpoints = array(
		'sandbox' => array(
			'US' => 'https://mws.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01/',
			'GB' => 'https://mws-eu.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01/',
			'DE' => 'https://mws-eu.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01/',
		),
		'production' => array(
			'US' => 'https://mws.amazonservices.com/OffAmazonPayments/2013-01-01/',
			'GB' => 'https://mws-eu.amazonservices.com/OffAmazonPayments/2013-01-01/',
			'DE' => 'https://mws-eu.amazonservices.com/OffAmazonPayments/2013-01-01/',
		)
	);

	/**
	 * Register URLs
	 *
	 * @var array
	 */
	protected static $register_urls = array(
		'US' => 'https://sellercentral.amazon.com/hz/me/sp/signup?solutionProviderOptions=mws-acc%3B&marketplaceId=AGWSWK15IEJJ7&solutionProviderToken=AAAAAQAAAAEAAAAQ1XU19m0BwtKDkfLZx%2B03RwAAAHBZVsoAgz2yhE7DemKr0y26Mce%2F9Q64kptY6CRih871XhB7neN0zoPX6c1wsW3QThdY6g1Re7CwxJkhvczwVfvZ9BvjG1V%2F%2FHrRgbIf47cTrdo5nNT8jmYSIEJvFbSm85nWxpvHjSC4CMsVL9s%2FPsZt&solutionProviderId=A1BVJDFFHQ7US4',
		'GB' => 'https://sellercentral-europe.amazon.com/gp/on-board/workflow/Registration/login.html?passthrough%2Fsource=internal-landing-select&passthrough%2F*entries*=0&passthrough%2FmarketplaceID=A2WQPBGJ59HSXT&passthrough%2FsuperSource=OAR&passthrough%2F*Version*=1&passthrough%2Fld=APRPWOOCOMMERCE&passthrough%2Faccount=cba&passthrough%2FwaiveFee=1',
		'DE' => 'https://sellercentral-europe.amazon.com/gp/on-board/workflow/Registration/login.html?passthrough%2Fsource=internal-landing-select&passthrough%2F*entries*=0&passthrough%2FmarketplaceID=A1OCY9REWJOCW5&passthrough%2FsuperSource=OAR&passthrough%2F*Version*=1&passthrough%2Fld=APRPWOOCOMMERCE&passthrough%2Faccount=cba&passthrough%2FwaiveFee=1'
	);

	/**
	 * Widgets URLs
	 *
	 * @var array
	 */
	protected static $widgets_urls = array(
		'US' => 'https://static-na.payments-amazon.com/OffAmazonPayments/us/%sjs/Widgets.js?sellerId=%s',
		'GB' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/%sjs/Widgets.js?sellerId=%s',
		'DE' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/%sjs/Widgets.js?sellerId=%s'
	);

	/**
	 * Get settings
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = get_option( 'woocommerce_amazon_payments_advanced_settings', array() );
		$default  = array(
			'enabled'                       => 'no',
			'title'                         => __( 'Amazon', 'woocommerce-gateway-amazon-payments-advanced' ),
			'seller_id'                     => '',
			'mws_access_key'                => '',
			'secret_key'                    => '',
			'sandbox'                       => 'yes',
			'payment_capture'               => 'no',
			'cart_button_display_mode'      => 'button',
			'hide_standard_checkout_button' => 'no'
		);

		return array_merge( $default, $settings );
	}

	/**
	 * Get reference ID
	 *
	 * @return string
	 */
	public static function get_reference_id() {
		$reference_id = ! empty( $_REQUEST['amazon_reference_id'] ) ? $_REQUEST['amazon_reference_id'] : '';

		if ( isset( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $post_data );

			if ( isset( $post_data['amazon_reference_id'] ) ) {
				$reference_id = $post_data['amazon_reference_id'];
			}
		}

		return $reference_id;
	}

	/**
	 * Get location
	 *
	 * @return string
	 */
	public static function get_location() {
		return in_array( WC()->countries->get_base_country(), array( 'US', 'GB', 'DE' ) ) ? WC()->countries->get_base_country() : 'US';
	}

	/**
	 * Get Amazon Register URL
	 *
	 * @return string
	 */
	public static function get_register_url() {
		$location = self::get_location();

		return $register_urls[ $location ];
	}

	/**
	 * Get widget URL
	 *
	 * @return string
	 */
	public static function get_widgets_url() {
		$location = self::get_location();
		$settings = self::get_settings();

		return sprintf( self::$widgets_urls[ $location ], $settings['sandbox'] == 'yes' ? 'sandbox/' : '', $settings['seller_id'] );
	}

	/**
	 * Get API endpoint
	 *
	 * @return string
	 */
	protected static function get_endpoint( $is_sandbox = false ) {
		$location = self::get_location();

		return $is_sandbox ? self::$endpoints['sandbox'][ $location ] : self::$endpoints['production'][ $location ];
	}

	/**
	 * Make an api request
	 *
	 * @param  args $args
	 * @return wp_error or parsed response array
	 */
	public static function request( $args ) {
		$settings = self::get_settings();
		$defaults = array(
			'AWSAccessKeyId' => $settings['mws_access_key'],
			'SellerId'       => $settings['seller_id']
		);
		$args     = wp_parse_args( $args, $defaults );
		$endpoint = self::get_endpoint( 'yes' === $settings['sandbox'] );
		$url      = self::get_signed_amazon_url( $endpoint . '?' . http_build_query( $args, '', '&' ), $settings['secret_key'] );
		$response = wp_remote_get( $url, array(
			'timeout' => 12
		) );

		if ( ! is_wp_error( $response ) ) {
			$response = new SimpleXmlElement( $response['body'], LIBXML_NOCDATA );
		}

		return $response;
	}

	/**
	 * Sign a url for amazon
	 *
	 * @param  string $url
	 *
	 * @return string
	 */
	protected static function get_signed_amazon_url( $url, $secret_key ) {
		$urlparts = parse_url( $url );

		// Build $params with each name/value pair
		foreach ( explode( '&', $urlparts['query'] ) as $part ) {
			if ( strpos( $part, '=' ) ) {
				list( $name, $value ) = explode( '=', $part, 2 );
			} else {
				$name  = $part;
				$value = '';
			}
			$params[ $name ] = $value;
		}

		// Include a timestamp if none was provided
		if ( empty( $params['Timestamp'] ) ) {
			$params['Timestamp'] = gmdate( 'Y-m-d\TH:i:s\Z' );
		}

		$params['SignatureVersion'] = '2';
		$params['SignatureMethod']  = 'HmacSHA256';

		// Sort the array by key
		ksort( $params );

		// Build the canonical query string
		$canonical = '';

		// Don't encode here - http_build_query already did it.
		foreach ( $params as $key => $val ) {
			$canonical  .= $key . "=" . rawurlencode( utf8_decode( urldecode( $val ) ) ) . '&';
		}

		// Remove the trailing ampersand
		$canonical = preg_replace( '/&$/', '', $canonical );

		// Some common replacements and ones that Amazon specifically mentions
		$canonical = str_replace( array( ' ', '+', ',', ';' ), array( '%20', '%20', urlencode( ',' ), urlencode( ':' ) ), $canonical );

		// Build the sign
		$string_to_sign = "GET\n{$urlparts['host']}\n{$urlparts['path']}\n$canonical";

		// Calculate our actual signature and base64 encode it
		$signature = base64_encode( hash_hmac( 'sha256', $string_to_sign, $secret_key, true ) );

		// Finally re-build the URL with the proper string and include the Signature
		$url = "{$urlparts['scheme']}://{$urlparts['host']}{$urlparts['path']}?$canonical&Signature=" . rawurlencode( $signature );

		return $url;
	}

	/**
	 * VAT registered sellers - Obtaining the Billing Address
	 * http://docs.developer.amazonservices.com/en_UK/apa_guide/APAGuide_GetAuthorizationStatus.html
	 *
	 * @param int $order_id
	 *
	 * @param array $result
	 */
	public static function maybe_update_billing_details( $order_id, $result ) {
		if ( ! empty( $result->AuthorizationBillingAddress ) ) {
			$address = (array) $result->AuthorizationBillingAddress;

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
				update_post_meta( $order_id, '_billing_company', $address_lines[0] );
				update_post_meta( $order_id, '_billing_address_1', $address_lines[1] );
				update_post_meta( $order_id, '_billing_address_2', $address_lines[2] );
			} elseif ( 2 === sizeof( $address_lines ) ) {
				update_post_meta( $order_id, '_billing_address_1', $address_lines[0] );
				update_post_meta( $order_id, '_billing_address_2', $address_lines[1] );
			} elseif ( sizeof( $address_lines ) ) {
				update_post_meta( $order_id, '_billing_address_1', $address_lines[0] );
			}

			if ( isset( $address['City'] ) ) {
				update_post_meta( $order_id, '_billing_city', $address['City'] );
			}

			if ( isset( $address['PostalCode'] ) ) {
				update_post_meta( $order_id, '_billing_postcode', $address['PostalCode'] );
			}

			if ( isset( $address['StateOrRegion'] ) ) {
				update_post_meta( $order_id, '_billing_state', $address['StateOrRegion'] );
			}

			if ( isset( $address['CountryCode'] ) ) {
				update_post_meta( $order_id, '_billing_country', $address['CountryCode'] );
			}
		}
	}

	/**
	 * Get auth state from amazon API
	 *
	 * @param  string $id
	 *
	 * @return string or false on failure
	 */
	public static function get_reference_state( $order_id, $id ) {
		if ( $state = get_post_meta( $order_id, 'amazon_reference_state', true ) ) {
			return $state;
		}

		$response = self::request( array(
			'Action'                 => 'GetOrderReferenceDetails',
			'AmazonOrderReferenceId' => $id,
		) );

		if ( is_wp_error( $response ) || isset( $response['Error']['Message'] ) ) {
			return false;
		}

		$state = (string) $response->GetOrderReferenceDetailsResult->OrderReferenceDetails->OrderReferenceStatus->State;

		update_post_meta( $order_id, 'amazon_reference_state', $state );

		return $state;
	}

	/**
	 * Get auth state from amazon API
	 *
	 * @param  string $id
	 *
	 * @return string or false on failure
	 */
	public static function get_authorization_state( $order_id, $id ) {
		if ( $state = get_post_meta( $order_id, 'amazon_authorization_state', true ) ) {
			return $state;
		}

		$response = self::request( array(
			'Action'                => 'GetAuthorizationDetails',
			'AmazonAuthorizationId' => $id,
		) );

		if ( is_wp_error( $response ) || isset( $response['Error']['Message'] ) ) {
			return false;
		}

		$state = (string) $response->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationStatus->State;

		update_post_meta( $order_id, 'amazon_authorization_state', $state );

		self::maybe_update_billing_details( $order_id, $response->GetAuthorizationDetailsResult->AuthorizationDetails );

		return $state;
	}

	/**
	 * Get capture state from amazon API
	 *
	 * @param  string $id
	 *
	 * @return string or false on failure
	 */
	public static function get_capture_state( $order_id, $id ) {
		if ( $state = get_post_meta( $order_id, 'amazon_capture_state', true ) ) {
			return $state;
		}

		$response = self::request( array(
			'Action'          => 'GetCaptureDetails',
			'AmazonCaptureId' => $id,
		) );

		if ( is_wp_error( $response ) || isset( $response['Error']['Message'] ) ) {
			return false;
		}

		$state = (string) $response->GetCaptureDetailsResult->CaptureDetails->CaptureStatus->State;

		update_post_meta( $order_id, 'amazon_capture_state', $state );

		return $state;
	}

	/**
	 * Authorize payment
	 */
	public static function authorize_payment( $order_id, $amazon_reference_id, $capture_now = false ) {
		$order = new WC_Order( $order_id );

		if ( 'amazon_payments_advanced' == $order->payment_method ) {
			$response = self::request( array(
				'Action'                           => 'Authorize',
				'AmazonOrderReferenceId'           => $amazon_reference_id,
				'AuthorizationReferenceId'         => $order->id . '-' . current_time( 'timestamp', true ),
				'AuthorizationAmount.Amount'       => $order->get_total(),
				'AuthorizationAmount.CurrencyCode' => strtoupper( get_woocommerce_currency() ),
				'CaptureNow'                       => $capture_now,
				'TransactionTimeout'               => 0,
				// 'SellerAuthorizationNote'          => '{"SandboxSimulation": {"State":"Declined", "ReasonCode":"AmazonRejected"}}'
			) );

			if ( is_wp_error( $response ) ) {

				$order->add_order_note( __( 'Unable to authorize funds with amazon:', 'woocommerce-gateway-amazon-payments-advanced' ) . ' ' . $response->get_error_message() );

				return false;

			} elseif ( isset( $response->Error->Message ) ) {

				$order->add_order_note( (string) $response->Error->Message );

				return false;

			} else {

				if ( isset( $response->AuthorizeResult->AuthorizationDetails->AmazonAuthorizationId ) ) {
					$auth_id = (string) $response->AuthorizeResult->AuthorizationDetails->AmazonAuthorizationId;
				} else {
					return false;
				}

				if ( isset( $response->AuthorizeResult->AuthorizationDetails->AuthorizationStatus->State ) ) {
					$state = strtolower( (string) $response->AuthorizeResult->AuthorizationDetails->AuthorizationStatus->State );
				} else {
					$state = 'pending';
				}

				update_post_meta( $order_id, 'amazon_authorization_id', $auth_id );

				self::maybe_update_billing_details( $order_id, $response->AuthorizeResult->AuthorizationDetails );

				if ( 'declined' == $state ) {
					$order->add_order_note( sprintf( __( 'Order Declined with reason code: %s', 'woocommerce-gateway-amazon-payments-advanced' ), (string) $response->AuthorizeResult->AuthorizationDetails->AuthorizationStatus->ReasonCode ) );
					// Payment was not authorized
					return false;
				}

				if ( $capture_now ) {
					update_post_meta( $order_id, 'amazon_capture_id', str_replace( '-A', '-C', $auth_id ) );

					$order->add_order_note( sprintf( __( 'Captured (Auth ID: %s)', 'woocommerce-gateway-amazon-payments-advanced' ), str_replace( '-A', '-C', $auth_id ) ) );
				} else {
					$order->add_order_note( sprintf( __( 'Authorized (Auth ID: %s)', 'woocommerce-gateway-amazon-payments-advanced' ), $auth_id ) );
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Close auth
	 *
	 * @param int $order_id
	 *
	 * @param string $amazon_authorization_id
	 */
	public static function close_authorization( $order_id, $amazon_authorization_id ) {
		$order = new WC_Order( $order_id );

		if ( 'amazon_payments_advanced' == $order->payment_method ) {
			$response = self::request( array(
				'Action'                => 'CloseAuthorization',
				'AmazonAuthorizationId' => $amazon_authorization_id
			) );

			if ( is_wp_error( $response ) ) {

				// Don't add a note
			} elseif ( isset( $response->Error->Message ) ) {

				$order->add_order_note( (string) $response->Error->Message );

			} else {

				delete_post_meta( $order_id, 'amazon_authorization_id' );

				$order->add_order_note( sprintf( __( 'Authorization closed (Auth ID: %s)', 'woocommerce-gateway-amazon-payments-advanced' ), $amazon_authorization_id ) );

			}
		}
	}

	/**
	 * Capture payment
	 *
	 * @param int $order_id
	 */
	public static function capture_payment( $order_id, $amazon_authorization_id ) {
		$order = new WC_Order( $order_id );

		if ( $order->payment_method == 'amazon_payments_advanced' ) {
			$response = self::request( array(
				'Action'                     => 'Capture',
				'AmazonAuthorizationId'      => $amazon_authorization_id,
				'CaptureReferenceId'         => $order->id . '-' . current_time( 'timestamp', true ),
				'CaptureAmount.Amount'       => $order->get_total(),
				'CaptureAmount.CurrencyCode' => strtoupper( get_woocommerce_currency() )
			) );

			if ( is_wp_error( $response ) ) {

				$order->add_order_note( __( 'Unable to authorize funds with amazon:', 'woocommerce-gateway-amazon-payments-advanced' ) . ' ' . $response->get_error_message() );

			} elseif ( isset( $response->Error->Message ) ) {

				$order->add_order_note( (string) $response->Error->Message );

			} else {
				$capture_id = (string) $response->CaptureResult->CaptureDetails->AmazonCaptureId;

				$order->add_order_note( sprintf( __( 'Capture Attempted (Capture ID: %s)', 'woocommerce-gateway-amazon-payments-advanced' ), $capture_id ) );

				update_post_meta( $order_id, 'amazon_capture_id', $capture_id );
			}
		}
	}

	/**
	 * Refund a payment
	 *
	 * @param int    $order_id
	 * @param string $capture_id
	 * @param float  $amount
	 * @param stirng $note
	 */
	public static function refund_payment( $order_id, $capture_id, $amount, $note ) {
		$order = new WC_Order( $order_id );

		if ( $order->payment_method == 'amazon_payments_advanced' ) {

			if ( 'US' == WC()->countries->get_base_country() && $amount > $order->get_total() ) {
				$order->add_order_note( __( 'Unable to refund funds via amazon:', 'woocommerce-gateway-amazon-payments-advanced' ) . ' ' . __( 'Refund amount is greater than order total.', 'woocommerce-gateway-amazon-payments-advanced' ) );

				return;
			} elseif ( $amount > min( ( $order->get_total() * 1.15 ), ( $order->get_total() + 75 ) ) ) {
				$order->add_order_note( __( 'Unable to refund funds via amazon:', 'woocommerce-gateway-amazon-payments-advanced' ) . ' ' . __( 'Refund amount is greater than the max refund amount.', 'woocommerce-gateway-amazon-payments-advanced' ) );

				return;
			}

			$response = self::request( array(
				'Action'                    => 'Refund',
				'AmazonCaptureId'           => $capture_id,
				'RefundReferenceId'         => $order->id . '-' . current_time( 'timestamp', true ),
				'RefundAmount.Amount'       => $amount,
				'RefundAmount.CurrencyCode' => strtoupper( get_woocommerce_currency() ),
				'SellerRefundNote'          => $note
			) );

			if ( is_wp_error( $response ) ) {

				$order->add_order_note( __( 'Unable to refund funds via amazon:', 'woocommerce-gateway-amazon-payments-advanced' ) . ' ' . $response->get_error_message() );

			} elseif ( isset( $response->Error->Message ) ) {

				$order->add_order_note( (string) $response->Error->Message );

			} else {
				$refund_id = (string) $response->RefundResult->RefundDetails->AmazonRefundId;

				$order->add_order_note( sprintf( __( 'Refunded %s (%s)', 'woocommerce-gateway-amazon-payments-advanced' ), woocommerce_price( $amount ), $note ) );

				add_post_meta( $order_id, 'amazon_refund_id', $refund_id );
			}
		}
	}
}
