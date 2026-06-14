<?php
/**
 * Stripe Plugin for WebEngine CMS 1.2.7
 * Webhook endpoint
 *
 * Configure this URL in your Stripe Dashboard -> Developers -> Webhooks:
 *   https://yourdomain.com/includes/plugins/stripe/api.php
 *
 * Listen for the event: checkout.session.completed
 */

define('access', 'api');

if(!@include_once(rtrim(str_replace('\\','/', dirname(__FILE__)), '/').'/../../webengine.php')) {
	throw new Exception('Could not load WebEngine.');
}

loadModuleConfigs('stripe');

if(!mconfig('active')) die();

$stripe = new Stripe();

$rawPostData = file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$webhookSecret = mconfig('webhook_secret');

// Validate the request actually comes from Stripe
if(!$stripe->verifyWebhookSignature($rawPostData, $sigHeader, $webhookSecret)) {
	http_response_code(400);
	die('Invalid signature.');
}

$event = json_decode($rawPostData, true);

if($event['type'] == 'checkout.session.completed') {

	$session = $event['data']['object'];

	$sessionId   = $session['id'] ?? '';
	$paymentStatus = $session['payment_status'] ?? '';
	$amountTotal = ($session['amount_total'] ?? 0) / 100;
	$currency    = $session['currency'] ?? '';
	$created     = $session['created'] ?? time();
	$paymentIntent = $session['payment_intent'] ?? '';

	$metadata = $session['metadata'] ?? array();
	$username = $metadata['username'] ?? '';
	$amount   = $metadata['amount'] ?? 0;
	$type     = $metadata['type'] ?? ''; // COINS or VIP
	$configId = $metadata['configId'] ?? 0;

	$userId = $stripe->getUserId($username);

	$logData = array(
		'ip_payed'      => $_SERVER['REMOTE_ADDR'] ?? '',
		'userID'        => $userId,
		'buy_id'        => $sessionId,
		'username'      => $username,
		'credits'       => $amount,
		'description'   => $type,
		'method'        => 'stripe',
		'method_payed'  => $session['payment_method_types'][0] ?? 'card',
		'date_create'   => date('Y-m-d H:i:s', $created),
		'amount'        => $amountTotal,
		'type_money'    => $currency,
		'buy_status'    => $paymentStatus,
		'buy_detail'    => $paymentIntent,
		'approved_date' => date('Y-m-d H:i:s'),
	);

	$alreadyProcessed = $stripe->paymentExists($sessionId);

	if($paymentStatus === 'paid') {
		if(!$alreadyProcessed) {
			if($type == 'COINS' && $userId !== false) {
				$stripe->addCoins($userId, $amount, $configId);
			} elseif($type == 'VIP') {
				$stripe->addVip($username, $amount, $configId);
			}
			$stripe->logTransaction($logData);
		}
	} else {
		if(!$alreadyProcessed) {
			$stripe->logTransaction($logData);
		}
	}
}

http_response_code(200);
echo 'OK';
