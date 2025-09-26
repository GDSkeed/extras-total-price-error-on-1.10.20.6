<?php if (!defined('WPINC')) die; ?>

<div class="hfy-listing-booking-form">

    <div class="booking-price-block body">
        <div class="price-bar calendar-fields-wrapper">

            <span class="price-title">
                <?= __('Price on request', 'hostifybooking') ?>
            </span>

            <form class="listing-price" method="get">
                <div class="row">
                    <?php if (HFY_USE_NEW_CALENDAR): ?>
                        <input type="hidden" name="start_date" value="<?= esc_attr($start_date) ?>"/>
                        <input type="hidden" name="end_date" value="<?= esc_attr($end_date) ?>"/>
                        <div class="col">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" placeholder="<?= esc_attr__('Check In', 'hostifybooking') . ' – ' . esc_attr__('Check Out', 'hostifybooking') ?>" readonly class="form-control calentim-dates" value="<?= esc_attr($dates_value) ?>"/>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-6">
                            <div class="form-group">
                                <div class="input-group">
                                <?php $startDate ?>
                                    <input type="text" name="start_date" readonly placeholder="<?= esc_attr__('Check In', 'hostifybooking') ?>" class="input-theme1 form-control calentim-start" value="<?= esc_attr($startDate) ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="input-theme1 form-control calentim-end" name="end_date" readonly placeholder="<?= esc_attr__('Check Out', 'hostifybooking') ?>" value="<?= esc_attr($endDate) ?>" />
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php // todo: update with guests-block ?>
                    <div class="col-12">
                        <div class="input-group number-input guests-input">
                            <span class='guests-input-label'><?= __( 'Guests:', 'hostifybooking' ) ?></span>
                            <a class="first noselect">
                                <svg width="24" height="24" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="17" cy="16" r="16.2" stroke="#CDD1D6" stroke-width="1"/><path d="M10 16L24 16" stroke="#627080" stroke-width="1.5"/></svg>
                            </a>
                            <input type="text" class="guests" placeholder="1" min="1" max="<?= $listingDetails->person_capacity; ?>" name="guests" readonly value="<?= (int) $guests ?>" />
                            <a class="last noselect">
                                <svg width="24" height="24" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="17" cy="16" r="16.2" stroke="#CDD1D6" stroke-width="1"/><path d="M17 9.59998L17 22.4" stroke="#627080" stroke-width="1.5"/><path d="M10 16L24 16" stroke="#627080" stroke-width="1.5"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (HFY_SHOW_DISCOUNT): ?>
                    <div class='row'>
                        <div class='col discount-code-wrap'>
                            <label><input autocomplete="off" type="checkbox" class='form-control discount_code_cb' /> <?= __( 'Have a discount code?', 'hostifybooking' ) ?> </label>
                            <div class="input_wrap" style='display:none'>
                                <input autocomplete="off" type="text" name="discount_code" class='form-control' placeholder="<?= __( 'Your discount code...', 'hostifybooking' ) ?>" />
                                <input type="button" class='btn btn-small btn-light discount_code_check' value="<?= esc_attr__('Apply', 'hostifybooking') ?>" />
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="discount_code" />
                <?php endif; ?>

                <div class="prices" data-channel-listing-id="<?= $listingData->channel_listing_id ?? 0 ?>">
                    <?php
                    if ($listingPrice && is_object($listingPrice)) {
                        $prices = $listingPrice;
                        $channelListingId = $listingData->channel_listing_id ?? 0;
                        $currencySymbol = $listingCurrencyData->symbol;
                        $is_listed = $listingData->is_listed;
                        include hfy_tpl('element/price-block-on-request');
                    }
                    ?>
                </div>

                <input type="hidden" name="id" value="<?= (int) $listingData->id ?>" />
                <input type="hidden" name="listing_id" value="<?= (int) $listingData->id ?>" />

            </form>
        </div>

        <div class="reset-date-wrap">
            <a class="reset-date"><?= __( 'Clear dates', 'hostifybooking' ) ?></a>
        </div>

        <?php if ($settings->direct_inquiry_email): ?>
            <div class="direct-inquiry-modal-open">
                <span><?= __( 'Reservation Inquiry', 'hostifybooking' ) ?></span>
            </div>
        <?php endif; ?>

    </div>

</div>

<?php
if (isset($settings->direct_inquiry_email)) {
    include hfy_tpl('element/direct-inquiry-modal');
}
