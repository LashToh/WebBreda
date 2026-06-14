<?php
/**
 * MercadoPago Plugin for WebEngine CMS 1.2.7
 * https://webenginecms.org/
 *
 * This file is loaded automatically by WebEngine on every request
 * (via includes/plugins -> plugins.cache) once the plugin is
 * installed and enabled from AdminCP -> Plugins.
 */

if(!defined('access') or !access) die();

// Make sure the transactions table constant exists even if the
// table config file wasn't manually updated.
if(!defined('WEBENGINE_MERCADOPAGO_TRANSACTIONS')) {
	define('WEBENGINE_MERCADOPAGO_TRANSACTIONS', WE_PREFIX.'WEBENGINE_MERCADOPAGO_TRANSACTIONS');
}

// Load the MercadoPago PHP SDK (legacy v1 SDK, dx-php)
if(!class_exists('MercadoPago\\SDK')) {
	@include_once(__DIR__.'/vendor/autoload.php');
}

class MercadoPago {

	protected $dbWebengine;
	protected $dbMuOnline;

	function __construct() {
		$this->dbWebengine = Connection::Database('Me_MuOnline');
		$this->dbMuOnline  = Connection::Database('MuOnline');
	}

	/**
	 * Returns true if this MercadoPago payment id was already processed.
	 */
	public function paymentExists($buyId) {
		$result = $this->dbWebengine->query_fetch("SELECT buy_id FROM ".WEBENGINE_MERCADOPAGO_TRANSACTIONS." WHERE buy_id = ?", array($buyId));
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
	 * @param int    $userId    memb_guid of the account
	 * @param int    $credits   amount to add
	 * @param int    $configId  CreditSystem config id (currency type)
	 */
	public function addCoins($userId, $credits, $configId) {
		if(!Validator::UnsignedNumber($userId)) throw new Exception('[MercadoPago] Invalid user id.');

		$common = new common();
		$accountInfo = $common->accountInformation($userId);
		if(!is_array($accountInfo)) throw new Exception('[MercadoPago] Invalid account.');

		$creditSystem = new CreditSystem();
		$creditSystem->setConfigId($configId);
		$configSettings = $creditSystem->showConfigs(true);
		if(!is_array($configSettings)) throw new Exception('[MercadoPago] Invalid credit configuration.');

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
				throw new Exception('[MercadoPago] Invalid identifier.');
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
	 * Logs a transaction in WEBENGINE_MERCADOPAGO_TRANSACTIONS.
	 */
	public function logTransaction($data) {
		$this->dbWebengine->query(
			"INSERT INTO ".WEBENGINE_MERCADOPAGO_TRANSACTIONS." 
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
