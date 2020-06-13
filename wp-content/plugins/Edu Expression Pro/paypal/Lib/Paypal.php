<?php
include_once("Hash.php");
/**
 * Paypal class
 */
class Paypal {

/**
 * Target version for "Classic" Paypal API
 */
	protected $paypalClassicApiVersion = '104.0';

/**
 * Live or sandbox
 */
	protected $sandboxMode = true;

/**
 * API credentials - nvp username
 */
	protected $nvpUsername = null;

/**
 * API credentials - nvp password
 */
	protected $nvpPassword = null;

/**
 * API credentials - nvp signature
 */
	protected $nvpSignature = null;

/**
 * API credentials - nvp token
 */
    protected $nvpToken = null;

/**
 * API credentials - Adaptive App ID
 */
    protected $adaptiveAppID = null;

/**
 * API credentials - Adaptive User ID
 */
    protected $adaptiveUserID = null;

/**
 * API credentials - Application id
 */
	protected $oAuthAppId = null;

/**
 * API credentials - oAuth client id
 */
	protected $oAuthClientId = null;

/**
 * API credentials - oAuth secret
 */
	protected $oAuthSecret = null;

/**
 * API credentials - oAuth access token
 */
	protected $oAuthAccessToken = null;

/**
 * Live endpoint for REST API
 */
	protected $liveRestEndpoint = 'https://api.paypal.com';

/**
 * Sandbox endpoint for REST API
 */
	protected $sandboxRestEndpoint = 'https://api.sandbox.paypal.com';

/**
 * Live endpoint for Classic API
 */
	protected $liveClassicEndpoint = 'https://api-3t.paypal.com/nvp';

/**
 * Sandbox endpoint for Classic API
 */
	protected $sandboxClassicEndpoint = 'https://api-3t.sandbox.paypal.com/nvp';

/**
 * Live endpoint for Adaptive Accounts API
 */
    protected $liveAdaptiveAccountsEndpoint = 'https://svcs.paypal.com/AdaptiveAccounts/';

/**
 * Sandbox endpoint for Adaptive Accounts API
 */
    protected $sandboxAdaptiveAccountsEndpoint = 'https://svcs.sandbox.paypal.com/AdaptiveAccounts/';

/**
 * Live endpoint for Paypal web login (used in classic paypal payments)
 */
	protected $livePaypalLoginUri = 'https://www.paypal.com/webscr';

/**
 * Sandbox endpoint for Paypal web login (used in classic paypal payments)
 */
	protected $sandboxPaypalLoginUri = 'https://www.sandbox.paypal.com/webscr';

/**
 * More descriptive API error messages. Error code and message.
 *
 * @var array
 */
	protected $errorMessages = array();

/**
 * Redirect error codes
 *
 * @var array
 */
	protected $redirectErrors = array(10411, 10412, 10422, 10445, 10486);

/**
 * HttpSocket utility class
 */
	public $HttpSocket = null;

/**
 * CakeRequest
 */
	public $CakeRequest = null;

/**
 * Constructor. Takes API credentials, and other properties to set (e.g sandbox mode)
 *
 * @param array $config An array of properties to overide (e.g the API signature)
 * @return void
 **/
	public function __construct($config = array()) {
            $this->ajaxUrl=admin_url('admin-ajax.php').'?action=examapp_Payment';
		$this->url=admin_url('admin.php').'?page=examapp_Payment';
		if (!empty($config)) {
			foreach ($config as $property => $value) {
				if (property_exists($this, $property)) {
					$this->{$property} = $value;
				}
			}
                        
		}
                // Sets errorMessages instance var with localization
		$this->errorMessages = array(
			10411 => __('The Express Checkout transaction has expired and the transaction needs to be restarted'),
			10412 => __('You may have made a second call for the same payment or you may have used the same invoice ID for seperate transactions.'),
			10422 => __('Please use a different funcing source.'),
			10445 => __('An error occured, please retry the transaction.'),
			10486 => __('This transaction couldn\'t be completed. Redirecting to payment gateway'),
			10500 => __('You have not agreed to the billing agreement.'),
			10501 => __('The billing agreement is disabled or inactive.'),
			10502 => __('The credit card used is expired.'),
			10505 => __('The transaction was refused because the AVS response returned the value of N, and the merchant account is not able to accept such transactions.'),
			10507 => __('The payment gateway account is restricted.'),
			10509 => __('You must submit an IP address of the buyer with each API call.'),
			10511 => __('The merchant selected a value for the PaymentAction field that is not supported.'),
			10519 => __('The credit card field was blank.'),
			10520 => __('The total amount and item amounts do not match.'),
			10534 => __('The credit card entered is currently restricted by the payment gateway.'),
			10536 => __('The merchant entered an invoice ID that is already associated with a transaction by the same merchant. Attempt with a new invoice ID'),
			10537 => __('The transaction was declined by the country filter managed by the merchant.'),
			10538 => __('The transaction was declined by the maximum amount filter managed by the merchant.'),
			10539 => __('The transaction was declined by the payment gateway.'),
			10541 => __('The credit card entered is currently restricted by the payment gateway.'),
			10544 => __('The transaction was declined by the payment gateway.'),
			10545 => __('The transaction was declined by payment gateway because of possible fraudulent activity.'),
			10546 => __('The transaction was declined by payment gateway because of possible fraudulent activity on the IP address.'),
			10548 => __('The merchant account attempting the transaction is not a business account.'),
			10549 => __('The merchant account attempting the transaction is not able to process Direct Payment transactions. '),
			10550 => __('Access to Direct Payment was disabled for your account.'),
			10552 => __('The merchant account attempting the transaction does not have a confirmed email address with the payment gateway.'),
			10553 => __('The merchant attempted a transaction where the amount exceeded the upper limit for that merchant.'),
			10554 => __('The transaction was declined because of a merchant risk filter for AVS. Specifically, the merchant has set to decline transaction when the AVS returned a no match (AVS = N).'),
			10555 => __('The transaction was declined because of a merchant risk filter for AVS. Specifically, the merchant has set the filter to decline transactions when the AVS returns a partial match.'),
			10556 => __('The transaction was declined because of a merchant risk filter for AVS. Specifically, the merchant has set the filter to decline transactions when the AVS is unsupported.'),
			10747 => __('The merchant entered an IP address that was in an invalid format. The IP address must be in a format such as 123.456.123.456.'),
			10748 => __('The merchant\'s configuration requires a CVV to be entered, but no CVV was provided with this transaction.'),
			10751 => __('The merchant provided an address either in the United States or Canada, but the state provided is not a valid state in either country.'),
			10752 => __('The transaction was declined by the issuing bank, not the payment gateway. The merchant should attempt another card.'),
			10754 => __('The transaction was declined by the payment gateway.'),
			10760 => __('The merchant\'s country of residence is not currently supported to allow Direct Payment transactions.'),
			10761 => __('The transaction was declined because the payment gateway is currently processing a transaction by the same buyer for the same amount. Can occur when a buyer submits multiple, identical transactions in quick succession.'),
			10762 => __('The CVV provided is invalid. The CVV is between 3-4 digits long.'),
			10764 => __('Please try again later. Ensure you have passed the correct CVV and address info for the buyer. If creating a recurring profile, please try again by passing a init amount of 0.'),
			12000 => __('Transaction is not compliant due to missing or invalid 3-D Secure authentication values. Check ECI, ECI3DS, CAVV, XID fields.'),
			12001 => __('Transaction is not compliant due to missing or invalid 3-D Secure authentication values. Check ECI, ECI3DS, CAVV, XID fields.'),
			15001 => __('The transaction was rejected by the payment gateway because of excessive failures over a short period of time for this credit card.'),
			15002 => __('The transaction was declined by payment gateway.'),
			15003 => __('The transaction was declined because the merchant does not have a valid commercial entity agreement on file with the payment gateway.'),
			15004 => __('The transaction was declined because the CVV entered does not match the credit card.'),
			15005 => __('The transaction was declined by the issuing bank, not the payment gateway. The merchant should attempt another card.'),
			15006 => __('The transaction was declined by the issuing bank, not the payment gateway. The merchant should attempt another card.'),
			15007 => __('The transaction was declined by the issuing bank because of an expired credit card. The merchant should attempt another card.'),
		);
	}


/**
 * Returns custom error message if there are any set for the error code passed in with the parsed response.
 * Returns the long message in the response otherwise.
 *
 * @param  array $parsed  Parsed response
 * @return string         The error message
 */
	public function getErrorMessage($parsed) {
		if (array_key_exists($parsed['L_ERRORCODE0'], $this->errorMessages)) {
			return $this->errorMessages[$parsed['L_ERRORCODE0']];
		}
		return $parsed['L_LONGMESSAGE0'];
	}

/**
 * SetExpressCheckout
 * The SetExpressCheckout API operation initiates an Express Checkout transaction.
 *
 * @param array $order Takes an array order (See tests for supported fields).
 * @return string Will return the full URL to redirect the user to.
 **/
    public function setExpressCheckout($order) {
        try {
            // Build the NVPs
            $nvps = $this->buildExpressCheckoutNvp($order);

        
            // Classic API endpoint
            $endPoint = $this->getClassicEndpoint();

            // Make a Http request for a new token
            $response = $this->post($endPoint , $nvps);

            // Parse the results
            $parsed = $this->parseClassicApiResponse($response);

            // Handle the resposne
            if (isset($parsed['TOKEN']) && $parsed['ACK'] == "Success")  {
                return $this->expressCheckoutUrl($parsed['TOKEN']);
            }
            elseif ($parsed['ACK'] == "Failure" && isset($parsed['L_LONGMESSAGE0']))  {
                $redirectUrl=$this->url;
                $redirectUrl=add_query_arg('info','index',$redirectUrl);
                $redirectUrl=add_query_arg('fmsg',$this->getErrorMessage($parsed),$redirectUrl);
                wp_redirect($redirectUrl);
                exit;
            }
            else {
                $redirectUrl=$this->url;
                $redirectUrl=add_query_arg('info','index',$redirectUrl);
                $redirectUrl=add_query_arg('fmsg','There+was+an+error+while+connecting+to+Paypal',$redirectUrl);
                wp_redirect($redirectUrl);
                exit;
            }
        } catch (Exception $e) {
                $redirectUrl=$this->url;
                $redirectUrl=add_query_arg('info','index',$redirectUrl);
                $redirectUrl=add_query_arg('fmsg','There+was+a+problem+initiating+the+transaction, please try again.',$redirectUrl);
                wp_redirect($redirectUrl);
                exit;
        }
	}

/**
 * GetExpressCheckoutDetails
 * Call GetExpressCheckoutDetails to obtain customer information
 * e.g. for customer review before payment
 *
 * @param string $token The token for this purchase (from Paypal, see SetExpressCheckout)
 * @return array $parsed Returns an array containing details of the transaction/buyer
 **/
	public function getExpressCheckoutDetails($token) {
        try {
            // Build the NVPs (Named value pairs)
            $nvps = array(
                'METHOD' => 'GetExpressCheckoutDetails' ,
                'VERSION' => $this->paypalClassicApiVersion,
                'TOKEN' => $token,
                'USER' => $this->nvpUsername,
                'PWD' => $this->nvpPassword,
                'SIGNATURE' => $this->nvpSignature,
            );
            
            // Classic API endpoint
            $endPoint = $this->getClassicEndpoint();

            // Make a Http request for a new token
            $response = $this->post($endPoint , $nvps);

            // Parse the results
            $parsed = $this->parseClassicApiResponse($response);

            // Handle the resposne
            if (isset($parsed['TOKEN']) && $parsed['ACK'] == "Success")  {
                return $parsed;
            }
            elseif ($parsed['ACK'] == "Failure" && isset($parsed['L_LONGMESSAGE0']))  {
                $redirectUrl=$this->url;
                $redirectUrl=add_query_arg('info','index',$redirectUrl);
                $redirectUrl=add_query_arg('fmsg',$this->getErrorMessage($parsed),$redirectUrl);
                wp_redirect($redirectUrl);
                exit;
            }
            else {
                    $redirectUrl=$this->url;
                    $redirectUrl=add_query_arg('info','index',$redirectUrl);
                    $redirectUrl=add_query_arg('fmsg',__('There+was+an+error+while+connecting+to+Paypal'),$redirectUrl);
                    wp_redirect($redirectUrl);
                    exit;
            }
        } catch (Exception $e) {
                $redirectUrl=$this->url;
                $redirectUrl=add_query_arg('info','index',$redirectUrl);
                $redirectUrl=add_query_arg('fmsg',__('There+was+a+problem+getting+your+details,+please+try+again.'),$redirectUrl);
                wp_redirect($redirectUrl);
                exit;
        }
	}

/**
 * DoExpressCheckoutPayment
 * The DoExpressCheckoutPayment API operation completes an Express Checkout transaction
 *
 * @param array $order Takes an array order (See tests for supported fields).
 * @param string $token The token for this purchase (from Paypal, see SetExpressCheckout)
 * @param string $payerId The ID of the Paypal user making the purchase
 * @return array Details of the completed transaction
 **/
	public function doExpressCheckoutPayment($order, $token , $payerId) {
        try {
            // Build the NVPs
            $nvps = $this->buildExpressCheckoutNvp($order);

            // When we call DoExpressCheckoutPayment, there are 3 NVPs that are different;
            $keysToAdd = array(
                'METHOD' => 'DoExpressCheckoutPayment',
                'TOKEN' => $token,
                'PAYERID' => $payerId,
            );

            // Add/overite, we now habe our final NVPs
            $finalNvps = array_merge($nvps, $keysToAdd);

            // Classic API endpoint
            $endPoint = $this->getClassicEndpoint();

            // Make a Http request for a new token
            $response = $this->post($endPoint , $finalNvps);

            // Parse the results
            $parsed = $this->parseClassicApiResponse($response);

            // Handle the resposne
            if (isset($parsed['TOKEN']) && $parsed['ACK'] == "Success")  {
                return $parsed;
            }
            elseif ($parsed['ACK'] == "Failure" && isset($parsed['L_LONGMESSAGE0']))  {
                if (in_array($parsed['L_ERRORCODE0'], $this->redirectErrors) && isset($parsed['TOKEN'])) {
					// We can catch an exception that requires a redirect back to paypal
                    $redirectUrl=$this->url;
                    $redirectUrl=add_query_arg('info','index',$redirectUrl);
                    $redirectUrl=add_query_arg('fmsg',$this->expressCheckoutUrl($token),$redirectUrl);
                    wp_redirect($redirectUrl);
                    exit;
                }
                    $redirectUrl=$this->url;
                    $redirectUrl=add_query_arg('info','index',$redirectUrl);
                    $redirectUrl=add_query_arg('fmsg',$this->getErrorMessage($parsed),$redirectUrl);
                    wp_redirect($redirectUrl);
                    exit;
            }
            else {
                    $redirectUrl=$this->url;
                    $redirectUrl=add_query_arg('info','index',$redirectUrl);
                    $redirectUrl=add_query_arg('fmsg',__('There+was+an+error+completing+the+payment'),$redirectUrl);
                    wp_redirect($redirectUrl);
                    exit;
            }
        } catch (Exception $e) {
            $redirectUrl=$this->url;
                    $redirectUrl=add_query_arg('info','index',$redirectUrl);
                    $redirectUrl=add_query_arg('fmsg',__('There+was+a+problem+processing+the+transaction,+please+try+again.'),$redirectUrl);
                    wp_redirect($redirectUrl);
                    exit;
        }
	}


/**
 * Formats the order array to Paypal nvps
 *
 * @param array $order Takes an array order (See tests for supported fields).
 * @return array Formatted array of Paypal NVPs for setExpressCheckout
 **/
	public function buildExpressCheckoutNvp($order) {
		if (empty($order) || !is_array($order)) {
			$redirectUrl=$this->url;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalidorder',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
		}
		if (!isset($order['return']) || !isset($order['cancel'])) {
			$redirectUrl=$this->url;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalidcancel',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
		}
		if (!isset($order['currency']))  {
			$redirectUrl=$this->url;
			$redirectUrl=add_query_arg('info','index',$redirectUrl);
			$redirectUrl=add_query_arg('msg','invalidcurrency',$redirectUrl);
			wp_redirect($redirectUrl);
			exit;
		}
		$nvps = array(
			'METHOD' => 'SetExpressCheckout',
			'VERSION' => $this->paypalClassicApiVersion,
			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			'USER' => $this->nvpUsername,
			'PWD' => $this->nvpPassword,
			'SIGNATURE' => $this->nvpSignature,
			'RETURNURL' => $order['return'],
			'CANCELURL' => $order['cancel'],
			'PAYMENTREQUEST_0_CURRENCYCODE' => $order['currency'],
			'PAYMENTREQUEST_0_DESC' => $order['description'],
		);
		// Custom field?
		if (isset($order['custom'])) {
			$nvps['PAYMENTREQUEST_0_CUSTOM'] = $order['custom'];
		}
		// Add up each item and calculate totals
		if (isset($order['items']) && is_array($order['items'])) {
			$items_subtotal = array_sum(Hash::extract($order , 'items.{n}.subtotal'));
			$items_shipping = array_sum(Hash::extract($order , 'items.{n}.shipping'));
			$items_tax = array_sum(Hash::extract($order , 'items.{n}.tax'));
			$items_total = array_sum(array($items_subtotal , $items_tax, $items_shipping));
			$nvps['PAYMENTREQUEST_0_ITEMAMT'] = $items_subtotal;
			$nvps['PAYMENTREQUEST_0_SHIPPINGAMT'] = $items_shipping;
			$nvps['PAYMENTREQUEST_0_TAXAMT'] = $items_tax;
			$nvps['PAYMENTREQUEST_0_AMT'] = $items_total;
			// Paypal only supports 10 items in express checkout
			if (count($order['items']) > 10) {
				return $nvps;
			}
			foreach ($order['items'] as $m => $item) {
				$nvps["L_PAYMENTREQUEST_0_NAME$m"] = $item['name'];
				$nvps["L_PAYMENTREQUEST_0_DESC$m"] = $item['description'];
				$nvps["L_PAYMENTREQUEST_0_TAXAMT$m"] = $item['tax'];
				$nvps["L_PAYMENTREQUEST_0_AMT$m"] = $item['subtotal'];
				$nvps["L_PAYMENTREQUEST_0_QTY$m"] = 1;
			}
		}
		return $nvps;
	}
	

/**
 * Returns the Paypal REST API endpoint
 *
 * @return string
 **/
	public function getRestEndpoint() {
		if ($this->sandboxMode) {
			return $this->sandboxRestEndpoint;
		}
		return $this->liveRestEndpoint;
	}

/**
 * Returns the Paypal Classic API endpoint
 *
 * @return string
 **/
	public function getClassicEndpoint() {
		if ($this->sandboxMode) {
			return $this->sandboxClassicEndpoint;
		}
		return $this->liveClassicEndpoint;
	}

/**
 * oAuthTokenUrl
 *
 * @return void
 **/
	public function oAuthTokenUrl() {
		return $this->getRestEndpoint() . '/v1/oauth2/token';
	}
	
/**
 * chargeStoredCardUrl
 *
 * @return void
 **/
	public function chargeStoredCardUrl() {
		return $this->getRestEndpoint() . '/v1/payments/payment';
	}
	
/**
 * storeCreditCardUrl
 *
 * @return void
  **/
	public function storeCreditCardUrl() {
		return $this->getRestEndpoint() . '/v1/vault/credit-card';
	}	

/**
 * Returns Paypal Adaptive Accounts API endpoint
 *
 * @return string
 **/
	public function getAdaptiveAccountsEndpoint() {
		if ($this->sandboxMode) {
			return $this->sandboxAdaptiveAccountsEndpoint;
		}
		return $this->liveAdaptiveAccountsEndpoint;
	}


/**
 * Returns the Paypal login URL for express checkout

 **/
	public function getPaypalLoginUri() {
		if ($this->sandboxMode) {
			return $this->sandboxPaypalLoginUri;
		}
		return $this->livePaypalLoginUri;
	}

/**
 * Build the login url for an express checkout payment, user is redirected to this
 *
 * @param string $token
 * @return string
 **/
	public function expressCheckoutUrl($token) {
		$endpoint = $this->getPaypalLoginUri();
		return "$endpoint?cmd=_express-checkout&token=$token";
	}

	public function parseClassicApiResponse($response)
        {
		parse_str($response , $parsed);
		return $parsed;
	}
        public function post($endPoint,$nvps)
        {
            $postVars = http_build_query($nvps);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endPoint);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);            
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'cURL/PHP');
            $response = curl_exec($ch);print_r($response);
            curl_close($ch);
            return$response;
        }

}
