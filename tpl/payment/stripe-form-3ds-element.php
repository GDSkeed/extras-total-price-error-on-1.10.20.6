<?php
if (!defined('WPINC')) die;

$fill_name1 = HfyHelper::getUserMeta('first_name');
$fill_name2 = HfyHelper::getUserMeta('last_name');
$fill_name = empty($reserveInfo->name) ? trim("$fill_name1 $fill_name2") : $reserveInfo->name;
$fill_email = empty($reserveInfo->email) ? HfyHelper::getUserMeta('user_email') : $reserveInfo->email;
$fill_phone = empty($reserveInfo->phone) ? HfyHelper::getUserMeta('phone_number') : $reserveInfo->phone;

?>

<main>
    <section class="container-lg">
		<div class="cell hfy-payment">

			<?php if (empty($secret_error)): ?>

				<form id="payment-form" role="form" action="<?= HFY_PAGE_CHARGE_URL ?>" method="post">

					<div class="tab-content hfy-payment-steps">
						<div role="tabpanel" class="tab-pane active hfy-payment-step-first">
							<?php include hfy_tpl('payment/stripe-form-3ds-element-step-1'); ?>
						</div>

						<?php if ($sliderStepsCount > 2): ?>
							<div role="tabpanel" class="tab-pane">
								<?php include hfy_tpl('payment/stripe-form-3ds-element-step-2'); ?>
							</div>
						<?php endif; ?>

						<div role="tabpanel" class="tab-pane hfy-payment-step-last">
							<?php include hfy_tpl('payment/stripe-form-3ds-element-step-3'); ?>
						</div>

					</div>

					<?php include hfy_tpl('payment/additional-info'); ?>

				</form>

				<div class="success" style='display:none'>
					<div class="icon">
						<svg width="84px" height="84px" viewBox="0 0 84 84" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><circle class="border" cx="42" cy="42" r="40" stroke-linecap="round" stroke-width="4" stroke="#000" fill="none"></circle><path class="checkmark" stroke-linecap="round" stroke-linejoin="round" d="M23.375 42.5488281 36.8840688 56.0578969 64.891932 28.0500338" stroke-width="4" stroke="#000" fill="none"></path></svg>
					</div>
				</div>

<?php
function get_client_ip() {
	if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
		return  $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
		return $_SERVER['REMOTE_ADDR'];
	} else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
		return $_SERVER['HTTP_CLIENT_IP'];
	}
	return '';
}
?>

<script>
var hfystripedata = {
key: '<?= $publishable_key ?>',
connectedAccount: '<?= $paymentSettings->services->account_id ?? '' ?>',
start_date: '<?= $reserveInfo->start_date ?>',
end_date: '<?= $reserveInfo->end_date ?>',
guests: '<?= $reserveInfo->guests ?>',
adults: '<?= $reserveInfo->adults ?>',
children: '<?= $reserveInfo->children ?>',
infants: '<?= $reserveInfo->infants ?>',
pets: '<?= $reserveInfo->pets ?>',
listing_id: '<?= $reserveInfo->listing_id ?>',
pname: '<?= $fill_name ?>',
pemail: '<?= $fill_email ?>',
pphone: '<?= $fill_phone ?>',
note: '<?= $reserveInfo->note ?>',
total: '<?= $totalPrice ?>',
amount: <?= intval($totalPrice * 100) ?>,
curr: '<?= strtolower($reserveInfo->prices->iso_code ?? $listing->currency_data->iso_code ?? '') ?>',
processFlow: 'reservation-payment',
pageUrlComplete: '<?= HFY_PAGE_CHARGE_URL ?>',
listingName: '<?= esc_attr($listingInfo->name ?? '') ?>',
userIp: '<?= get_client_ip() ?>',
userAgent: '<?= $_SERVER['HTTP_USER_AGENT'] ?>',
return_url: '<?= HFY_PAGE_CHARGE_URL ?>',
paymentMethodTypes: <?= $paymentSettings->payment_method_types ? json_encode($paymentSettings->payment_method_types) : '[]' ?>,
paymentMethodConfiguration: <?= $paymentSettings->payment_method_configuration ? '"'.$paymentSettings->payment_method_configuration.'"' : 'false' ?>
}
</script>

			<?php else: ?>

				<div class="error">
					<?= __('Error', 'hostifybooking') ?>: <?= $secret_error ?>
				</div>

			<?php endif; ?>

		</div>
    </section>
</main>
