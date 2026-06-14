<?php
/**
 * MercadoPago Donation Module for WebEngine CMS 1.2.7
 * Place this file in: modules/donation/mercadopago.php
 *
 * Requires the "MercadoPago" plugin to be installed and enabled
 * (includes/plugins/mercadopago/).
 */

try {

	if(!isLoggedIn()) redirect(1, 'login');

	loadModuleConfigs('mercadopago');
	if(!mconfig('active')) throw new Exception('[MercadoPago] This payment method is currently disabled.');

	$packsCoins = loadConfig('mp_packs_coin');
	$packsVip   = loadConfig('mp_packs_vip');

	$common = new common();
	$accountInfo = $common->accountInformation($_SESSION['userid']);
	$username = $accountInfo[_CLMN_USERNM_];

	$accessToken = mconfig('access_token');
	$description = mconfig('mercadopago_desc');
	$returnUrl   = mconfig('mercadopago_return_url');
	$apiReturnUrl = mconfig('mercadopago_api_return_url');

	MercadoPago\SDK::setAccessToken($accessToken);

	echo '<div class="page-title"><span><i class="fas fa-credit-card me-2"></i>MercadoPago</span></div>';

	// ===== COIN PACKS =====
	if(mconfig('coins_status') == 1 && is_array($packsCoins)) {

		echo '<div class="info-section-title"><i class="fas fa-coins"></i> Paquetes de Monedas</div>';
		echo '<div class="alert alert-success text-center">'.($description ? $description : 'Las monedas se acreditan automáticamente al confirmarse el pago.').'</div>';

		echo '<div class="row g-3 mb-4">';

		$creditSystem = new CreditSystem();

		foreach($packsCoins as $pack) {
			if(!$pack['active']) continue;

			$price  = $pack['price'];
			$amount = $pack['amount'];
			$typeM  = $pack['type_M'];

			$creditSystem->setConfigId($typeM);
			$currencyInfo = $creditSystem->showConfigs(true);
			$currencyName = is_array($currencyInfo) ? $currencyInfo['config_title'] : 'Coins';

			// build a MercadoPago preference for this pack
			$preference = new MercadoPago\Preference();
			$item = new MercadoPago\Item();
			$item->id = 'pack-coin-'.$typeM.'-'.$amount;
			$item->title = $username.'|'.$amount.'|COINS|'.$typeM;
			$item->description = $description;
			$item->category_id = 'home';
			$item->quantity = 1;
			$item->unit_price = (float) $price;
			$preference->items = array($item);
			$preference->back_urls = array(
				'success' => $returnUrl,
				'failure' => $returnUrl,
				'pending' => $returnUrl,
			);
			$preference->auto_return = 'approved';
			$preference->notification_url = $apiReturnUrl;
			$preference->save();

			echo '<div class="col-md-4 col-sm-6">';
				echo '<div class="info-stat-card">';
					echo '<i class="fas fa-coins"></i>';
					echo '<span class="info-stat-label">'.number_format($amount, 0, ',', '.').' '.$currencyName.'</span>';
					echo '<span class="info-stat-value">$'.number_format($price, 0, ',', '.').' ARS</span>';
					echo '<a href="'.$preference->init_point.'" target="_blank" class="btn-premium-gold mt-3 d-block">'.'Donar ahora'.'</a>';
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

		foreach($packsVip as $pack) {
			if(!$pack['active']) continue;

			$price = $pack['price'];
			$days  = $pack['amount'];
			$typeV = $pack['type_V'];
			$vipName = $vipNames[$typeV] ?? 'VIP';

			$preference = new MercadoPago\Preference();
			$item = new MercadoPago\Item();
			$item->id = 'pack-vip-'.$typeV.'-'.$days;
			$item->title = $username.'|'.$days.'|VIP|'.$typeV;
			$item->description = $description;
			$item->category_id = 'home';
			$item->quantity = 1;
			$item->unit_price = (float) $price;
			$preference->items = array($item);
			$preference->back_urls = array(
				'success' => $returnUrl,
				'failure' => $returnUrl,
				'pending' => $returnUrl,
			);
			$preference->auto_return = 'approved';
			$preference->notification_url = $apiReturnUrl;
			$preference->save();

			echo '<div class="col-md-4 col-sm-6">';
				echo '<div class="info-stat-card">';
					echo '<i class="fas fa-star"></i>';
					echo '<span class="info-stat-label">'.number_format($days, 0, ',', '.').' días VIP '.$vipName.'</span>';
					echo '<span class="info-stat-value">$'.number_format($price, 0, ',', '.').' ARS</span>';
					echo '<a href="'.$preference->init_point.'" target="_blank" class="btn-premium-gold mt-3 d-block">'.'Donar ahora'.'</a>';
				echo '</div>';
			echo '</div>';
		}

		echo '</div>';
	}

	echo '<div class="alert alert-warning"><i class="fas fa-exclamation-circle me-2"></i>La acreditación es automática, pero en medios de pago como Pago Fácil / Rapipago puede demorar hasta 24hs hábiles.</div>';

} catch(Exception $ex) {
	message('error', $ex->getMessage());
}
