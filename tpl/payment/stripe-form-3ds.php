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
			<form id="payment-form" role="form" action="<?= HFY_PAGE_CHARGE_URL ?>" method="post">

				<div class="tab-content hfy-payment-steps">
					<div role="tabpanel" class="tab-pane active hfy-payment-step-first">
						<?php include hfy_tpl('payment/stripe-form-3ds-step-1'); ?>
					</div>

					<?php if ($sliderStepsCount > 2): ?>
						<div role="tabpanel" class="tab-pane">
							<?php include hfy_tpl('payment/stripe-form-3ds-step-2'); ?>
						</div>
					<?php endif; ?>

					<div role="tabpanel" class="tab-pane hfy-payment-step-last">
						<?php include hfy_tpl('payment/stripe-form-3ds-step-3'); ?>
					</div>

				</div>

				<?php include hfy_tpl('payment/additional-info'); ?>

			</form>

			<div class="success" style='display:none'>
				<div class="icon">
					<svg width="84px" height="84px" viewBox="0 0 84 84" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><circle class="border" cx="42" cy="42" r="40" stroke-linecap="round" stroke-width="4" stroke="#000" fill="none"></circle><path class="checkmark" stroke-linecap="round" stroke-linejoin="round" d="M23.375 42.5488281 36.8840688 56.0578969 64.891932 28.0500338" stroke-width="4" stroke="#000" fill="none"></path></svg>
				</div>
			</div>

		</div>

    </section>
</main>

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
processFlow: 'reservation-payment',
listingName: '<?= esc_attr($listingInfo->name ?? '') ?>',
return_url: '<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>'
}
</script>