<?php
/**
 * MercadoPago Plugin for WebEngine CMS 1.2.7
 * Place this file in: admincp/modules/mercadopago.php
 */

echo '<h1 class="page-header">MercadoPago Settings</h1>';

function mercadopago_saveChanges() {
	global $_POST;
	foreach($_POST as $key => $setting) {
		if($key == 'submit_changes') continue;
		if(!check_value($setting) && $setting !== '0') {
			message('error', '[MercadoPago] Missing fields, please fill out all the settings.');
			return;
		}
	}

	$xmlPath = __PATH_MODULE_CONFIGS__.'mercadopago.xml';
	$xml = simplexml_load_file($xmlPath);
	$xml->active = $_POST['setting_1'];
	$xml->coins_status = $_POST['setting_2'];
	$xml->vip_status = $_POST['setting_3'];
	$xml->mercadopago_desc = $_POST['setting_4'];
	$xml->access_token = $_POST['setting_5'];
	$xml->mercadopago_return_url = $_POST['setting_6'];
	$xml->mercadopago_api_return_url = $_POST['setting_7'];

	$save = $xml->asXML($xmlPath);

	if($save) {
		message('success', '[MercadoPago] Settings successfully saved.');
	} else {
		message('error', '[MercadoPago] There has been an error while saving changes.');
	}
}

if(check_value($_POST['submit_changes'])) {
	mercadopago_saveChanges();
}

loadModuleConfigs('mercadopago');
?>

<form action="" method="post">
	<table class="table table-striped table-bordered table-hover module_config_tables">
		<tr>
			<th><b>[ Status ]</b><br/><span>Enable or disable the MercadoPago donation module.</span></th>
			<td>
				<?php enabledisableCheckboxes('setting_1', mconfig('active'), 'Enabled', 'Disabled'); ?>
			</td>
		</tr>
		<tr>
			<th><b>[ Coins Status ]</b><br/><span>Enable or disable Coin donations.</span></th>
			<td>
				<?php enabledisableCheckboxes('setting_2', mconfig('coins_status'), 'Enabled', 'Disabled'); ?>
			</td>
		</tr>
		<tr>
			<th><b>[ Vip Status ]</b><br/><span>Enable or disable VIP donations.</span></th>
			<td>
				<?php enabledisableCheckboxes('setting_3', mconfig('vip_status'), 'Enabled', 'Disabled'); ?>
			</td>
		</tr>
		<tr>
			<th><b>[ Item Description ]</b><br/><span>Description shown on the MercadoPago checkout.</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_4" value="<?php echo mconfig('mercadopago_desc', true); ?>"/>
			</td>
		</tr>
		<tr>
			<th><b>[ Access Token ]</b><br/><span>Your MercadoPago Access Token. <a href="https://www.mercadopago.com.ar/developers/panel/credentials" target="_blank">(Get credentials)</a></span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_5" value="<?php echo mconfig('access_token', true); ?>"/>
			</td>
		</tr>
		<tr>
			<th><b>[ Return URL ]</b><br/><span>Where the user is redirected after completing/cancelling a payment.</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_6" value="<?php echo mconfig('mercadopago_return_url', true); ?>"/>
			</td>
		</tr>
		<tr>
			<th><b>[ IPN Notify URL ]</b><br/><span>Set this in MercadoPago webhooks. Default: <b><?php echo __BASE_URL__; ?>includes/plugins/mercadopago/api.php</b></span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_7" value="<?php echo mconfig('mercadopago_api_return_url', true); ?>"/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="submit_changes" value="Save Changes" class="btn btn-success"/>
				<a href="<?php echo admincp_base('mercadopago_packs'); ?>" class="btn btn-primary">Manage Coin / VIP Packs</a>
			</td>
		</tr>
	</table>
</form>
