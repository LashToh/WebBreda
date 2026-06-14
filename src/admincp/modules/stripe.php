<?php
/**
 * Stripe Plugin for WebEngine CMS 1.2.7
 * Place this file in: admincp/modules/stripe.php
 */

echo '<h1 class="page-header">Stripe Settings</h1>';

function stripe_saveChanges() {
	global $_POST;
	foreach($_POST as $key => $setting) {
		if($key == 'submit_changes') continue;
		if(!check_value($setting) && $setting !== '0') {
			message('error', '[Stripe] Missing fields, please fill out all the settings.');
			return;
		}
	}

	$xmlPath = __PATH_MODULE_CONFIGS__.'stripe.xml';
	$xml = simplexml_load_file($xmlPath);
	$xml->active = $_POST['setting_1'];
	$xml->coins_status = $_POST['setting_2'];
	$xml->vip_status = $_POST['setting_3'];
	$xml->item_description = $_POST['setting_4'];
	$xml->secret_key = $_POST['setting_5'];
	$xml->publishable_key = $_POST['setting_6'];
	$xml->webhook_secret = $_POST['setting_7'];
	$xml->currency = strtolower($_POST['setting_8']);
	$xml->success_url = $_POST['setting_9'];
	$xml->cancel_url = $_POST['setting_10'];

	$save = $xml->asXML($xmlPath);

	if($save) {
		message('success', '[Stripe] Settings successfully saved.');
	} else {
		message('error', '[Stripe] There has been an error while saving changes.');
	}
}

if(check_value($_POST['submit_changes'])) {
	stripe_saveChanges();
}

loadModuleConfigs('stripe');
?>

<form action="" method="post">
	<table class="table table-striped table-bordered table-hover module_config_tables">
		<tr>
			<th><b>[ Status ]</b><br/><span>Enable or disable the Stripe donation module.</span></th>
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
			<th><b>[ Item Description ]</b><br/><span>Text shown above the coin packs on the donation page.</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_4" value="<?php echo mconfig('item_description', true); ?>"/>
			</td>
		</tr>
		<tr>
			<th><b>[ Secret Key ]</b><br/><span>Your Stripe Secret Key (sk_live_... or sk_test_...). <a href="https://dashboard.stripe.com/apikeys" target="_blank">(Get API keys)</a></span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_5" value="<?php echo mconfig('secret_key', true); ?>"/>
			</td>
		</tr>
		<tr>
			<th><b>[ Publishable Key ]</b><br/><span>Your Stripe Publishable Key (pk_live_... or pk_test_...). Not required for Checkout redirects, but kept for future use.</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_6" value="<?php echo mconfig('publishable_key', true); ?>"/>
			</td>
		</tr>
		<tr>
			<th><b>[ Webhook Signing Secret ]</b><br/><span>Found in Stripe Dashboard -> Developers -> Webhooks -> (your endpoint) -> Signing secret (whsec_...).</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_7" value="<?php echo mconfig('webhook_secret', true); ?>"/>
			</td>
		</tr>
		<tr>
			<th><b>[ Currency ]</b><br/><span>3-letter ISO currency code used for all packs (e.g. usd, eur).</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_8" value="<?php echo mconfig('currency', true); ?>"/>
			</td>
		</tr>
		<tr>
			<th><b>[ Success URL ]</b><br/><span>Where the user is redirected after a successful payment.</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_9" value="<?php echo mconfig('success_url', true); ?>"/>
			</td>
		</tr>
		<tr>
			<th><b>[ Cancel URL ]</b><br/><span>Where the user is redirected if they cancel the checkout.</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" name="setting_10" value="<?php echo mconfig('cancel_url', true); ?>"/>
			</td>
		</tr>
		<tr>
			<th><b>[ Webhook URL ]</b><br/><span>Set this as the endpoint URL in your Stripe Webhook configuration (read-only, informational).</span></th>
			<td>
				<input style="width: 100%;" class="input-xxlarge" type="text" value="<?php echo __BASE_URL__; ?>includes/plugins/stripe/api.php" readonly/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="submit_changes" value="Save Changes" class="btn btn-success"/>
				<a href="<?php echo admincp_base('stripe_packs'); ?>" class="btn btn-primary">Manage Coin / VIP Packs</a>
			</td>
		</tr>
	</table>
</form>
