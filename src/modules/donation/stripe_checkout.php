<?php
/**
 * Stripe Checkout Redirect Handler for WebEngine CMS 1.2.7
 * Place this file in: modules/donation/stripe_checkout.php
 *
 * Creates a Stripe Checkout Session on demand (when the user clicks
 * "Donar ahora") and redirects them to Stripe, instead of generating
 * one session per pack on every page load.
 *
 * URL: donation/stripe_checkout?type=coin&id=0
 *      donation/stripe_checkout?type=vip&id=0
 */

try {

	if(!isLoggedIn()) redirect(1, 'login');

	loadModuleConfigs('stripe');
	if(!mconfig('active')) throw new Exception('[Stripe] This payment method is currently disabled.');

	$type = $_REQUEST['type'] ?? '';
	$id   = $_REQUEST['id'] ?? '';

	if(!in_array($type, array('coin', 'vip'))) throw new Exception('[Stripe] Invalid pack type.');
	if(!check_value($id) || !is_numeric($id)) throw new Exception('[Stripe] Invalid pack id.');

	$common = new common();
	$accountInfo = $common->accountInformation($_SESSION['userid']);
	$username = $accountInfo[_CLMN_USERNM_];

	$stripe = new Stripe();
	$currency   = mconfig('currency');
	$successUrl = mconfig('success_url');
	$cancelUrl  = mconfig('cancel_url');

	if($type == 'coin') {
		$packs = loadConfig('sp_packs_coin');
		if(!is_array($packs) || !isset($packs[$id]) || !$packs[$id]['active']) throw new Exception('[Stripe] Pack not found.');

		$pack = $packs[$id];
		$price  = $pack['price'];
		$amount = $pack['amount'];
		$typeM  = $pack['type_M'];

		$creditSystem = new CreditSystem();
		$creditSystem->setConfigId($typeM);
		$currencyInfo = $creditSystem->showConfigs(true);
		$currencyName = is_array($currencyInfo) ? $currencyInfo['config_title'] : 'Coins';

		$session = $stripe->createCheckoutSession(
			number_format($amount, 0, ',', '.').' '.$currencyName,
			$price,
			$currency,
			array(
				'username' => $username,
				'amount'   => $amount,
				'type'     => 'COINS',
				'configId' => $typeM,
			),
			$successUrl,
			$cancelUrl
		);

	} else {
		$packs = loadConfig('sp_packs_vip');
		if(!is_array($packs) || !isset($packs[$id]) || !$packs[$id]['active']) throw new Exception('[Stripe] Pack not found.');

		$pack = $packs[$id];
		$price = $pack['price'];
		$days  = $pack['amount'];
		$typeV = $pack['type_V'];
		$vipNames = array(1 => 'Bronce', 2 => 'Plata', 3 => 'Oro');
		$vipName = $vipNames[$typeV] ?? 'VIP';

		$session = $stripe->createCheckoutSession(
			$days.' días VIP '.$vipName,
			$price,
			$currency,
			array(
				'username' => $username,
				'amount'   => $days,
				'type'     => 'VIP',
				'configId' => $typeV,
			),
			$successUrl,
			$cancelUrl
		);
	}

	header('Location: '.$session['url']);
	exit;

} catch(Exception $ex) {
	message('error', $ex->getMessage());
}
