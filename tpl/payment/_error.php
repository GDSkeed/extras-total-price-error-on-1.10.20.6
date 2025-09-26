<?php if (!defined('WPINC')) die; ?>

<?php
if ($paymentSuccess == false) { ?>
    <p><?= __('There was a problem with your payment. Please try again.', 'hostifybooking') ?></p>

    <form role="form" action="<?= HFY_PAGE_PAYMENT_URL ?>" id="payment-response" method="post">

        <input type="hidden" name="start_date" value="<?= esc_attr($reserveInfo->start_date) ?>" />
        <input type="hidden" name="end_date" value="<?= esc_attr($reserveInfo->end_date) ?>" />
        <input type="hidden" name="guests" value="<?= (int) $reserveInfo->guests ?>" />
        <input type="hidden" name="listing_id" value="<?= (int) $reserveInfo->listing_id ?>" />
        <input type="hidden" name="pname" value="<?= esc_attr($reserveInfo->name) ?>" />
        <input type="hidden" name="pemail" value="<?= esc_attr($reserveInfo->email) ?>" />
        <input type="hidden" name="pphone" value="<?= esc_attr($reserveInfo->phone) ?>" />
        <input type="hidden" name="zip" value="<?= esc_attr($reserveInfo->zip) ?>" />
        <input type="hidden" name="discount_code" value="<?= esc_attr($reserveInfo->discount_code) ?>" />
        <textarea style="display:none" name="note"><?= esc_attr($reserveInfo->note) ?></textarea>

        <input type="submit" formaction="<?= HFY_PAGE_PAYMENT_URL ?>" value="<?= esc_attr__('Back to payment', 'hostifybooking') ?>" class="back-top-payment-btn" />
    </form>

<?php
} else {
    // todo
    // if (in_array($message,  //params['unavailableDatesMessages'])) {
    //     echo '<p>The dates you have chosen are not available.</p>';
    //     echo '<p>Please contact us.</p>';
    // } else {
    //     echo '<p>There was a problem with your booking.</p>';
    //     echo '<p>Please contact us.</p>';
    // }
}
