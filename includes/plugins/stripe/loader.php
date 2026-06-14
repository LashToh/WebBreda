<?php
/**
 * Stripe Plugin for WebEngine CMS 1.2.7
 * https://webenginecms.org/
 *
 * This file is loaded automatically by WebEngine on every request
 * (via includes/plugins -> plugins.cache) once the plugin is
 * installed and enabled from AdminCP -> Plugins.
 *
 * This plugin talks to the Stripe REST API directly via cURL
 * (no composer SDK required), using Checkout Sessions for payments
 * and Webhooks for automatic credit/VIP activation.
 */

if(!defined('access') or !access) die();

if(!defined('WEBENGINE_STRIPE_TRANSACTIONS')) {
	define('WEBENGINE_STRIPE_TRANSACTIONS', WE_PREFIX.'WEBENGINE_STRIPE_TRANSACTIONS');
}

class Stripe {

	protected $dbWebengine;
	protected $dbMuOnline;
	protected $secretKey;

	function __construct() {
		$this->dbWebengine = Connection::Database('Me_MuOnline');
		$this->dbMuOnline  = Connection::Database('MuOnline');

		loadModuleConfigs('stripe');
		$this->secretKey = mconfig('secret_key');
	}

	/**
	 * Performs a request against the Stripe REST API using cURL.
	 *
	 * @param string $method   GET, POST, etc
	 * @param string $endpoint e.g. "checkout/sessions"
	 * @param array  $data     form-encoded params (supports nested arrays)
	 */
	public function request($method, $endpoint, $data = array()) {
		if(!check_value($this->secretKey)) throw new Exception('[Stripe] Missing Secret Key, please configure the plugin from AdminCP.');

		$ch = curl_init();
		$url = 'https://api.stripe.com/v1/'.$endpoint;

		if($method == 'GET' && !empty($data)) {
			$url .= '?'.http_build_query($data);
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERPWD, $this->secretKey.':');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);

		if($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		}

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlError = curl_error($ch);
		curl_close($ch);

		if($response === false) {
			throw new Exception('[Stripe] cURL error: '.$curlError);
		}

		$decoded = json_decode($response, true);

		if($httpCode >= 400) {
			$msg = $decoded['error']['message'] ?? 'Unknown Stripe API error.';
			throw new Exception('[Stripe] '.$msg);
		}

		return $decoded;
	}

	/**
	 * Creates a Checkout Session for a single line item and returns it
	 * (use $session['url'] to redirect the user, $session['id'] as reference).
	 *
	 * @param string $itemName    name shown on the Stripe checkout page
	 * @param float  $unitAmount  price in the major currency unit (e.g. 10.50)
	 * @param string $currency    3-letter ISO currency code (e.g. usd)
	 * @param array  $metadata    arbitrary key/value pairs (username, type, configId, amount)
	 * @param string $successUrl
	 * @param string $cancelUrl
	 */
	public function createCheckoutSession($itemName, $unitAmount, $currency, $metadata, $successUrl, $cancelUrl) {
		$data = array(
			'mode' => 'payment',
			'success_url' => $successUrl,
			'cancel_url' => $cancelUrl,
			'line_items' => array(
				array(
					'quantity' => 1,
					'price_data' => array(
						'currency' => $currency,
						'unit_amount' => (int) round($unitAmount * 100), // Stripe expects the smallest currency unit
						'product_data' => array(
							'name' => $itemName,
						),
					),
				),
			),
			'metadata' => $metadata,
		);

		return $this->request('POST', 'checkout/sessions', $data);
	}

	/**
	 * Retrieves a Checkout Session by id.
	 */
	public function getCheckoutSession($sessionId) {
		return $this->request('GET', 'checkout/sessions/'.$sessionId);
	}

	/**
	 * Verifies the "Stripe-Signature" header of an incoming webhook
	 * without requiring the official SDK.
	 *
	 * @param string $payload   raw POST body
	 * @param string $sigHeader value of the Stripe-Signature header
	 * @param string $secret    webhook signing secret (whsec_...)
	 * @param int    $tolerance allowed clock drift in seconds
	 */
	public function verifyWebhookSignature($payload, $sigHeader, $secret, $tolerance = 300) {
		if(!check_value($sigHeader) || !check_value($secret)) return false;

		$timestamp = null;
		$signatures = array();

		foreach(explode(',', $sigHeader) as $part) {
			$pair = explode('=', $part, 2);
			if(count($pair) != 2) continue;
			if($pair[0] == 't') $timestamp = $pair[1];
			if($pair[0] == 'v1') $signatures[] = $pair[1];
		}

		if(!check_value($timestamp) || empty($signatures)) return false;

		// Prevent replay attacks
		if(abs(time() - (int)$timestamp) > $tolerance) return false;

		$signedPayload = $timestamp.'.'.$payload;
		$expectedSignature = hash_hmac('sha256', $signedPayload, $secret);

		foreach($signatures as $signature) {
			if(hash_equals($expectedSignature, $signature)) return true;
		}

		return false;
	}

	/**
	 * Returns true if this Stripe Checkout Session id was already processed.
	 */
	public function paymentExists($sessionId) {
		$result = $this->dbWebengine->query_fetch("SELECT buy_id FROM ".WEBENGINE_STRIPE_TRANSACTIONS." WHERE buy_id = ?", array($sessionId));
		return is_array($result) && count($result) > 0;
	}

	/**
	 * Returns the memb_guid for a given username.
	 */
	public function getUserId($username) {
		$result = $this->dbMuOnline->query_fetch("SELECT memb_guid FROM MEMB_INFO WHERE memb___id = ?", array($username));
		if(is_array($result) && isset($result[0]['memb_guid'])) {
			return $result[0]['memb_guid'];
		}
		return false;
	}

	/**
	 * Adds credits/coins to a user account using WebEngine's native CreditSystem.
	 *
	 * @param int $userId    memb_guid of the account
	 * @param int $credits   amount to add
	 * @param int $configId  CreditSystem config id (currency type)
	 */
	public function addCoins($userId, $credits, $configId) {
		if(!Validator::UnsignedNumber($userId)) throw new Exception('[Stripe] Invalid user id.');

		$common = new common();
		$accountInfo = $common->accountInformation($userId);
		if(!is_array($accountInfo)) throw new Exception('[Stripe] Invalid account.');

		$creditSystem = new CreditSystem();
		$creditSystem->setConfigId($configId);
		$configSettings = $creditSystem->showConfigs(true);
		if(!is_array($configSettings)) throw new Exception('[Stripe] Invalid credit configuration.');

		switch($configSettings['config_user_col_id']) {
			case 'userid':
				$creditSystem->setIdentifier($accountInfo[_CLMN_MEMBID_]);
				break;
			case 'username':
				$creditSystem->setIdentifier($accountInfo[_CLMN_USERNM_]);
				break;
			case 'email':
				$creditSystem->setIdentifier($accountInfo[_CLMN_EMAIL_]);
				break;
			default:
				throw new Exception('[Stripe] Invalid identifier.');
		}

		$creditSystem->addCredits($credits);
	}

	/**
	 * Activates VIP for a given username.
	 *
	 * @param string $username
	 * @param int    $days     amount of days to add
	 * @param int    $vipLevel AccountLevel value to set
	 */
	public function addVip($username, $days, $vipLevel) {
		$this->dbMuOnline->query(
			"UPDATE MEMB_INFO SET AccountLevel = ?, AccountExpireDate = DATEADD(DAY, ?, GETDATE()) WHERE memb___id = ?",
			array($vipLevel, (int)$days, $username)
		);
	}

	/**
	 * Logs a transaction in WEBENGINE_STRIPE_TRANSACTIONS.
	 */
	public function logTransaction($data) {
		$this->dbWebengine->query(
			"INSERT INTO ".WEBENGINE_STRIPE_TRANSACTIONS." 
			(ip_payed, userID, buy_id, username, credits, description, method, method_payed, date_create, amount, type_money, buy_status, buy_detail, approved_date) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
			array(
				$data['ip_payed'], $data['userID'], $data['buy_id'], $data['username'],
				$data['credits'], $data['description'], $data['method'], $data['method_payed'],
				$data['date_create'], $data['amount'], $data['type_money'], $data['buy_status'],
				$data['buy_detail'], $data['approved_date']
			)
		);
	}
}
