<?php
/**
 * Stripe Donation Module for WebEngine CMS 1.2.7
 * Place this file in: modules/donation/stripe.php
 *
 * Requires the "Stripe" plugin to be installed and enabled
 * (includes/plugins/stripe/).
 *
 * The Stripe Checkout Session is created on-demand when the user
 * clicks "Donar ahora" (see modules/donation/stripe_checkout.php),
 * so this page does not call the Stripe API at all.
 */

try {

	if(!isLoggedIn()) redirect(1, 'login');

	loadModuleConfigs('stripe');
	if(!mconfig('active')) throw new Exception('[Stripe] This payment method is currently disabled.');

	$packsCoins = loadConfig('sp_packs_coin');
	$packsVip   = loadConfig('sp_packs_vip');

	$currency    = mconfig('currency');
	$description = mconfig('item_description');

	echo '<div class="page-title"><span><i class="fas fa-credit-card me-2"></i>Stripe</span></div>';

	// ===== COIN PACKS =====
	if(mconfig('coins_status') == 1 && is_array($packsCoins)) {

		echo '<div class="info-section-title"><i class="fas fa-coins"></i> Paquetes de Monedas</div>';
		echo '<div class="alert alert-success text-center">'.($description ? $description : 'Las monedas se acreditan automáticamente al confirmarse el pago.').'</div>';

		echo '<div class="row g-3 mb-4">';

		$creditSystem = new CreditSystem();

		foreach($packsCoins as $packId => $pack) {
			if(!$pack['active']) continue;

			$price  = $pack['price'];
			$amount = $pack['amount'];
			$typeM  = $pack['type_M'];

			$creditSystem->setConfigId($typeM);
			$currencyInfo = $creditSystem->showConfigs(true);
			$currencyName = is_array($currencyInfo) ? $currencyInfo['config_title'] : 'Coins';

			$checkoutUrl = __BASE_URL__.'donation/stripe_checkout?type=coin&id='.$packId;

			echo '<div class="col-md-4 col-sm-6">';
				echo '<div class="info-stat-card">';
					echo '<i class="fas fa-coins"></i>';
					echo '<span class="info-stat-label">'.number_format($amount, 0, ',', '.').' '.$currencyName.'</span>';
					echo '<span class="info-stat-value">'.strtoupper($currency).' '.number_format($price, 2, '.', ',').'</span>';
					echo '<a href="'.$checkoutUrl.'" class="btn-premium-gold mt-3 d-block">Donar ahora</a>';
				echo '</div>';
			echo '</div>';
		}

		echo '</div>';
	}

	// ===== VIP PACKS =====
	if(mconfig('vip_status') == 1 && is_array($packsVip)) {

		echo '<div class="info-section-title"><i class="fas fa-star"></i> Paquetes VIP</div>';
		echo '<div class="alert alert-warning text-center">El VIP se activa automáticamente al confirmarse el pago.</div>';

		echo '<div class="row g-3 mb-4">';

		$vipNames = array(1 => 'Bronce', 2 => 'Plata', 3 => 'Oro');

		foreach($packsVip as $packId => $pack) {
			if(!$pack['active']) continue;

			$price = $pack['price'];
			$days  = $pack['amount'];
			$typeV = $pack['type_V'];
			$vipName = $vipNames[$typeV] ?? 'VIP';

			$checkoutUrl = __BASE_URL__.'donation/stripe_checkout?type=vip&id='.$packId;

			echo '<div class="col-md-4 col-sm-6">';
				echo '<div class="info-stat-card">';
					echo '<i class="fas fa-star"></i>';
					echo '<span class="info-stat-label">'.number_format($days, 0, ',', '.').' días VIP '.$vipName.'</span>';
					echo '<span class="info-stat-value">'.strtoupper($currency).' '.number_format($price, 2, '.', ',').'</span>';
					echo '<a href="'.$checkoutUrl.'" class="btn-premium-gold mt-3 d-block">Donar ahora</a>';
				echo '</div>';
			echo '</div>';
		}

		echo '</div>';
	}

	echo '<div class="alert alert-warning"><i class="fas fa-exclamation-circle me-2"></i>La acreditación es automática e inmediata mediante webhook de Stripe.</div>';

} catch(Exception $ex) {
	message('error', $ex->getMessage());
}
