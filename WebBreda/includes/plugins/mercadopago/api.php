<?php
/**
 * MercadoPago Plugin for WebEngine CMS 1.2.7
 * IPN / Webhook endpoint
 *
 * Configure this URL in AdminCP -> MercadoPago as the "IPN Notify URL":
 *   https://yourdomain.com/includes/plugins/mercadopago/api.php
 */

define('access', 'api');

if(!@include_once(rtrim(str_replace('\\','/', dirname(__FILE__)), '/').'/../../webengine.php')) {
	throw new Exception('Could not load WebEngine.');
}

loadModuleConfigs('mercadopago');

if(!mconfig('active')) die();

$mercadoPago = new MercadoPago();

$rawPostData = file_get_contents('php://input');
$jsonData = json_decode($rawPostData, true);

$paymentId = $jsonData['data']['id'] ?? null;
$action = $jsonData['action'] ?? null;

if(($action == 'payment.created' || $action == 'payment.updated') && check_value($paymentId)) {

	$accessToken = mconfig('access_token');
	$url = 'https://api.mercadopago.com/v1/payments/'.$paymentId.'?access_token='.$accessToken;

	$response = @file_get_contents($url);

	if($response) {
		$payment = json_decode($response, true);

		$ipPayed       = $payment['additional_info']['ip_address'] ?? '';
		$idBought      = $payment['id'] ?? '';
		$description   = $payment['description'] ?? '';
		$method        = $payment['payment_method_id'] ?? '';
		$methodPayed   = $payment['payment_type_id'] ?? '';
		$dateCreate    = $payment['date_created'] ?? '';
		$totalPayed    = $payment['transaction_amount'] ?? 0;
		$currency      = $payment['currency_id'] ?? '';
		$status        = $payment['status'] ?? '';
		$statusDetail  = $payment['status_detail'] ?? '';
		$dateApproved  = $payment['date_approved'] ?? '';

		// description format: username|amount|TYPE|configId
		$info = explode('|', $description);
		$username  = $info[0] ?? '';
		$amount    = $info[1] ?? 0;
		$type      = $info[2] ?? ''; // COINS or VIP
		$configId  = $info[3] ?? 0;

		$userId = $mercadoPago->getUserId($username);

		$logData = array(
			'ip_payed'      => $ipPayed,
			'userID'        => $userId,
			'buy_id'        => $idBought,
			'username'      => $username,
			'credits'       => $amount,
			'description'   => $type,
			'method'        => $method,
			'method_payed'  => $methodPayed,
			'date_create'   => $dateCreate,
			'amount'        => $totalPayed,
			'type_money'    => $currency,
			'buy_status'    => $status,
			'buy_detail'    => $statusDetail,
			'approved_date' => $dateApproved,
		);

		$alreadyProcessed = $mercadoPago->paymentExists($idBought);

		if($status === 'approved' && $statusDetail === 'accredited') {
			if(!$alreadyProcessed) {
				if($type == 'COINS' && $userId !== false) {
					$mercadoPago->addCoins($userId, $amount, $configId);
				} elseif($type == 'VIP') {
					$mercadoPago->addVip($username, $amount, $configId);
				}
				$mercadoPago->logTransaction($logData);
			}
		} else {
			if(!$alreadyProcessed) {
				$mercadoPago->logTransaction($logData);
			}
		}
	}
}
