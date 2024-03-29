<?php

class WePayLib {

	/**
	 * Version number - sent in user agent string
	 */
	const VERSION = '0.0.9';

	/**
	 * Scope fields
	 * Passed into Wepay::getAuthorizationUri as array
	 */
	const SCOPE_MANAGE_ACCOUNTS  = 'manage_accounts';   // Open and interact with accounts
	const SCOPE_VIEW_BALANCE     = 'view_balance';      // View account balances
	const SCOPE_COLLECT_PAYMENTS = 'collect_payments';  // Create and interact with checkouts
	const SCOPE_REFUND_PAYMENTS  = 'refund_payments';   // Refund checkouts
	const SCOPE_VIEW_USER        = 'view_user';         // Get details about authenticated user
	const SCOPE_PREAPPROVE_PAYMENTS = 'preapprove_payments';         // Gets Preappovals
	/**
	 * Application's client ID
	 */
	private static $client_id;

	/**
	 * Application's client secret
	 */
	private static $client_secret;

	/**
	 * Pass Wepay::$all_scopes into getAuthorizationUri if your application desires full access
	 */
	public static $all_scopes = array(
		self::SCOPE_MANAGE_ACCOUNTS,
		self::SCOPE_VIEW_BALANCE,
		self::SCOPE_COLLECT_PAYMENTS,
		self::SCOPE_REFUND_PAYMENTS,
		self::SCOPE_VIEW_USER,
		self::SCOPE_PREAPPROVE_PAYMENTS
	);

	/**
	 * Determines whether to use WePay's staing or production servers
	 */
	private static $production = null;

	/**
	 * cURL handle
	 */
	private $ch;

	/**
	 * Authenticated user's access token
	 */
	private $token;

	/**
	 * Generate URI used during oAuth authorization
	 * Redirect your user to this URI where they can grant your application
	 * permission to make API calls
	 * @link https://www.wepay.com/developer/reference/oauth2
	 * @param array  $scope             List of scope fields for which your appliation wants access
	 * @param string $redirect_uri      Where user goes after logging in at WePay (domain must match application settings)
	 * @param array  $options optional  user_name,user_email which will be pre-fileld on login form, state to be returned in querystring of redirect_uri
	 * @return string URI to which you must redirect your user to grant access to your application
	 */
	public static function getAuthorizationUri(array $scope, $redirect_uri, array $options = array()) {
		// This does not use WePay::getDomain() because the user authentication
		// domain is different than the API call domain
		if (self::$production === null) {
			throw new RuntimeException('You must initialize the WePay SDK with WePay::useStaging() or WePay::useProduction()');
		}
		$domain = self::$production ? 'https://www.wepay.com' : 'https://stage.wepay.com';
		$uri = $domain . '/v2/oauth2/authorize?';
		$uri .= http_build_query(array(
			'client_id'    => self::$client_id,
			'redirect_uri' => $redirect_uri,
			'scope'        => implode(',', $scope),
			'state'        => empty($options['state'])      ? '' : $options['state'],
			'user_name'    => empty($options['user_name'])  ? '' : $options['user_name'],
			'user_email'   => empty($options['user_email']) ? '' : $options['user_email'],
		));
		return $uri;
	}

	private static function getDomain() {
		if (self::$production === true) {
			return 'https://wepayapi.com/v2/';
		}
		elseif (self::$production === false) {
			return 'https://stage.wepayapi.com/v2/';
		}
		else {
			throw new RuntimeException('You must initialize the WePay SDK with WePay::useStaging() or WePay::useProduction()');
		}
	}

	/**
	 * Exchange a temporary access code for a (semi-)permanent access token
	 * @param string $code          'code' field from query string passed to your redirect_uri page
	 * @param string $redirect_uri  Where user went after logging in at WePay (must match value from getAuthorizationUri)
	 * @return StdClass|false
	 *  user_id
	 *  access_token
	 *  token_type
	 */
	public static function getToken($code, $redirect_uri) {
		$uri = self::getDomain() . 'oauth2/token';
		$params = (array(
			'client_id'     => self::$client_id,
			'client_secret' => self::$client_secret,
			'redirect_uri'  => $redirect_uri,
			'code'          => $code,
			'state'         => '', // do not hardcode
		));

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, 'WePay v2 PHP SDK v' . self::VERSION);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30-second timeout, adjust to taste
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$raw = curl_exec($ch);
		if ($errno = curl_errno($ch)) {
			// Set up special handling for request timeouts
			if ($errno == CURLE_OPERATION_TIMEOUTED) {
				throw new WePayServerException;
			}
			throw new Exception('cURL error while making API call to WePay: ' . curl_error($ch), $errno);
		}
		$result = json_decode($raw);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpCode >= 400) {
			if ($httpCode >= 500) {
				throw new WePayServerException($result->error_description);
			}
			switch ($result->error) {
				case 'invalid_request':
					throw new WePayRequestException($result->error_description, $httpCode);
				case 'access_denied':
				default:
					throw new WePayPermissionException($result->error_description, $httpCode);
			}
		}
		return $result;
	}

	/**
	 * Configure SDK to run against WePay's production servers
	 * @param string $client_id      Your application's client id
	 * @param string $client_secret  Your application's client secret
	 * @return void
	 * @throws RuntimeException
	 */
	public static function useProduction($client_id, $client_secret) {
		if (self::$production !== null) {
			throw new RuntimeException('API mode has already been set.');
		}
		self::$production    = true;
		self::$client_id     = $client_id;
		self::$client_secret = $client_secret;
	}

	/**
	 * Configure SDK to run against WePay's staging servers
	 * @param string $client_id      Your application's client id
	 * @param string $client_secret  Your application's client secret
	 * @return void
	 * @throws RuntimeException
	 */
	public static function useStaging($client_id, $client_secret) {
		if (self::$production !== null) {
			throw new RuntimeException('API mode has already been set.');
		}
		self::$production    = false;
		self::$client_id     = $client_id;
		self::$client_secret = $client_secret;
	}

	/**
	 * Create a new API session
	 * @param string $token - access_token returned from WePay::getToken
	 */
	public function __construct($token) {
		$this->token = $token;
	}

	/**
	 * Clean up cURL handle
	 */
	public function __destruct() {
		if ($this->ch) {
			curl_close($this->ch);
		}
	}

	/**
	 * Make API calls against authenticated user
	 * @param string $endpoint - API call to make (ex. 'user', 'account/find')
	 * @param array  $values   - Associative array of values to send in API call
	 * @return StdClass
	 * @throws WePayException on failure
	 * @throws Exception on catastrophic failure (non-WePay-specific cURL errors)
	 */
	public function request($endpoint, array $values = array()) {
		if (!$this->ch) {
			$headers = array("Content-Type: application/json"); // always pass the correct Content-Type header
			if ($this->token) { // if we have an access_token, add it to the Authorization header
				$headers[] = "Authorization: Bearer $this->token";
			}
			$this->ch = curl_init();
			curl_setopt($this->ch, CURLOPT_USERAGENT, 'WePay v2 PHP SDK v' . self::VERSION);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($this->ch, CURLOPT_TIMEOUT, 30); // 30-second timeout, adjust to taste
			curl_setopt($this->ch, CURLOPT_POST, !empty($values)); // WePay's API is not strictly RESTful, so all requests are sent as POST unless there are no request values
		}
		$uri = self::getDomain() . $endpoint;
		curl_setopt($this->ch, CURLOPT_URL, $uri);
		if (!empty($values)) {
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($values));
		}
		$raw = curl_exec($this->ch);
		if ($errno = curl_errno($this->ch)) {
			// Set up special handling for request timeouts
			if ($errno == CURLE_OPERATION_TIMEOUTED) {
				throw new WePayServerException("Timeout occurred while trying to connect to WePay");
			}
			throw new Exception('cURL error while making API call to WePay: ' . curl_error($this->ch), $errno);
		}
		$result = json_decode($raw);
		$httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		if ($httpCode >= 400) {
			if ($httpCode >= 500) {
				throw new WePayServerException($result->error_description, $httpCode, $result);
			}
			switch ($result->error) {
				case 'invalid_request':
					throw new WePayRequestException($result->error_description, $httpCode, $result);
				case 'access_denied':
				default:
					throw new WePayPermissionException($result->error_description, $httpCode, $result);
			}
		}
		return $result;
	}
}

/**
 * Different problems will have different exception types so you can
 * catch and handle them differently.
 *
 * WePayServerException indicates some sort of 500-level error code and
 * was unavoidable from your perspective. You may need to re-run the
 * call, or check whether it was received (use a "find" call with your
 * reference_id and make a decision based on the response)
 *
 * WePayRequestException indicates a development error - invalid endpoint,
 * erroneous parameter, etc.
 *
 * WePayPermissionException indicates your authorization token has expired,
 * was revoked, or is lacking in scope for the call you made
 */
class WePayException extends Exception {
	public function __construct($description = '', $http_code = FALSE, $response = FALSE, $code = 0, $previous = NULL)
	{
		$this->response = $response;
		parent::__construct($description, $code, $previous);
	}
}
class WePayRequestException extends WePayException {}
class WePayPermissionException extends WePayException {}
class WePayServerException extends WePayException {}
