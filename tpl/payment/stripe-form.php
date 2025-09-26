<?php

// DEPRECATED

if (!defined('WPINC')) die;
?>

<main>
    <section class="container-lg">
		<div class="cell hfy-payment">
			<form id="payment-form" role="form" action="<?= HFY_PAGE_CHARGE_URL ?>" method="post">
				<div data-locale-reversible>
					<div class="row">
						<div class="field">
							<input id="hfy-payment-name" name="pname" class="input <?= $reserveInfo->name ? '' : 'empty' ?>" type="text" placeholder="<?= esc_attr__('Name', 'hostifybooking') ?>" required="" <?= $reserveInfo->name ? 'value="' . $reserveInfo->name . '"' : ''; ?>>
							<label for="hfy-payment-name"><?= __('Name', 'hostifybooking') ?></label>
							<div class="baseline"></div>
						</div>
					</div>
					<div class="row" data-locale-reversible>
						<div class="field">
							<input id="hfy-payment-email" name="pemail" class="input <?= $reserveInfo->email ? '' : 'empty' ?>" type="email" placeholder="<?= esc_attr__('Email', 'hostifybooking') ?>" required="" <?= $reserveInfo->email ? 'value="' . $reserveInfo->email . '"' : ''; ?>>
							<label for="hfy-payment-email"><?= __('Email', 'hostifybooking') ?></label>
							<div class="baseline"></div>
						</div>
						<div class="field">
							<input id="hfy-payment-phone" name="pphone" class="input <?= $reserveInfo->phone ? '' : 'empty' ?>" type="tel" placeholder="<?= esc_attr__('Phone', 'hostifybooking') ?>" required="" <?= $reserveInfo->phone ? 'value="' . $reserveInfo->phone . '"' : ''; ?>>
							<label for="hfy-payment-phone"><?= __('Phone', 'hostifybooking') ?></label>
							<div class="baseline"></div>
						</div>
					</div>
					<div class="row">
						<div class="field textarea-field">
							<textarea id="hfy-payment-note" name="note" class="input <?= $reserveInfo->note ? '' : 'empty' ?>" placeholder="<?= esc_attr__('Feel free to leave any additional question...', 'hostifybooking') ?>"><?= $reserveInfo->note ? $reserveInfo->note : ''; ?></textarea>
							<label for="hfy-payment-note"><?= __('Note', 'hostifybooking') ?></label>
							<div class="baseline"></div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="field">
						<div id="hfy-payment-card-number" class="input empty"></div>
						<label for="hfy-payment-card-number"><?= __('Card number', 'hostifybooking') ?></label>
						<div class="baseline"></div>
					</div>
				</div>
				<div class="row">
					<div class="field third-width">
						<div id="hfy-payment-card-expiry" class="input empty"></div>
						<label for="hfy-payment-card-expiry"><?= __('Expiration', 'hostifybooking') ?></label>
						<div class="baseline"></div>
					</div>
					<div class="field third-width">
						<div id="hfy-payment-card-cvc" class="input empty"></div>
						<label for="hfy-payment-card-cvc"><?= __('CVC', 'hostifybooking') ?></label>
						<div class="baseline"></div>
					</div>
					<div class="field third-width">
						<input id="hfy-payment-zip" name="zip" class="input <?= $reserveInfo->zip ? '' : 'empty' ?>" type="text" placeholder="" <?= $reserveInfo->zip ? 'value="' . $reserveInfo->zip . '"' : ''; ?>>
						<label for="hfy-payment-zip"><?= __('ZIP', 'hostifybooking') ?></label>
						<div class="baseline"></div>
					</div>
				</div>

				<input name="discount_code" type="hidden" value="<?= $reserveInfo->discount_code ?>" />
				<input name="dcid" type="hidden" value="<?= $reserveInfo->dcid ?>" />

				<div class="row">
					<div class="col">
						<div class="error" role="alert" style='display:none'>
							<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17"><path class="base" fill="#000" d="M8.5,17 C3.80557963,17 0,13.1944204 0,8.5 C0,3.80557963 3.80557963,0 8.5,0 C13.1944204,0 17,3.80557963 17,8.5 C17,13.1944204 13.1944204,17 8.5,17 Z"></path><path class="glyph" fill="#FFF" d="M8.5,7.29791847 L6.12604076,4.92395924 C5.79409512,4.59201359 5.25590488,4.59201359 4.92395924,4.92395924 C4.59201359,5.25590488 4.59201359,5.79409512 4.92395924,6.12604076 L7.29791847,8.5 L4.92395924,10.8739592 C4.59201359,11.2059049 4.59201359,11.7440951 4.92395924,12.0760408 C5.25590488,12.4079864 5.79409512,12.4079864 6.12604076,12.0760408 L8.5,9.70208153 L10.8739592,12.0760408 C11.2059049,12.4079864 11.7440951,12.4079864 12.0760408,12.0760408 C12.4079864,11.7440951 12.4079864,11.2059049 12.0760408,10.8739592 L9.70208153,8.5 L12.0760408,6.12604076 C12.4079864,5.79409512 12.4079864,5.25590488 12.0760408,4.92395924 C11.7440951,4.59201359 11.2059049,4.59201359 10.8739592,4.92395924 L8.5,7.29791847 L8.5,7.29791847 Z"></path></svg>
							<span id="card-errors" class="message"></span>
						</div>
					</div>
				</div>

				<?php if (!empty($terms)): ?>
					<div class="row terms-row">
						<div class="col">
							<div class="terms-checkbox">
								<input type="checkbox" name="terms" required="required" />
								<span></span>
							</div>
							<?= $terms ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="row">
					<div class="col">
						<button class='pay-btn' type="submit"><?= __('Pay', 'hostifybooking') ?> <?= ListingHelper::withSymbol($totalPrice, $reserveInfo->prices, $listingInfo->currency_symbol) ?></button>
					</div>
				</div>

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
  start_date: '<?= $reserveInfo->start_date ?>',
  end_date: '<?= $reserveInfo->end_date ?>',
  guests: '<?= $reserveInfo->guests ?>',
  listing_id: '<?= $reserveInfo->listing_id ?>',
  pname: '<?= $reserveInfo->name ?>',
  pemail: '<?= $reserveInfo->email ?>',
  pphone: '<?= $reserveInfo->phone ?>',
  note: '<?= $reserveInfo->note ?>',
  total: '<?= $totalPrice ?>',
}
</script>