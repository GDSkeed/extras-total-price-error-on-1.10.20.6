<?php
if (!defined('WPINC')) die;

$d1 = date_create($reserveInfo->start_date);
$d2 = date_create($reserveInfo->end_date);

?>

<?php if (!empty(HFY_PAGE_BOOKINGS_LIST_URL)): ?>
	<a href="<?= HFY_PAGE_BOOKINGS_LIST_URL ?>">
		&larr; <?= __('Back to list', 'hostifybooking')  ?>
	</a>
<?php endif; ?>

<div class="hfy-wrap data-block">
    <div class="row mtop-5">

        <div class="col-md-6 payment-content">

            <div id="roomInfoSection">
                <a href="<?= UrlHelper::listing(['id' => $listingInfo->id]) ?>" class="noprint">
                    <div class="cover-bg fluid" style="background-image:url(<?= $listingInfo->thumbnail_file ?>)"></div>
                </a>
                <div class="booking-block">
                    <div class="booking-title"><?= $listingInfo->name ?></div>
                    <div><span><?= $listingInfo->city ?>, <?= $listingInfo->country ?></span></div>
                </div>
                <div class="booking-block">
                    <div class="row" style="padding:0">
                        <div class="col-xs-5 col-5">
                            <?= __('Check In', 'hostifybooking') ?>
                            <div class="booking-row"><?= date_format($d1, "M d, Y") ?></div>
                        </div>
                        <div class="col-xs-2 col-2"><img src="<?= HOSTIFYBOOKING_URL ?>public/res/images/next.png" style="height:16px;margin-top:16px"></div>
                        <div class="col-xs-5 col-5 text-right">
                            <?= __('Check Out', 'hostifybooking') ?>
                            <div class="booking-row"><?= date_format($d2, "M d, Y") ?></div>
                        </div>
                    </div>
                </div>
                <div class="booking-block">
                    <div class="booking-row small">
                        <?= $reserveInfo->guests ?>
                        <?= __('Guests', 'hostifybooking') ?>
                    </div>
                    <div class="booking-row small">
                        <div style="float:right"><?= ListingHelper::withSymbol($price, $reservation) ?></div>
                        <div>
                            <?php // ListingHelper::withSymbol($listingPricePerNight, $reservation) ?>
                            <?= __('Price for', 'hostifybooking') ?> <?= $listingInfo->nights; ?> <?= __('nights', 'hostifybooking') ?>
                        </div>
                    </div>

                    <?php if (!empty($reservation->fees)) : ?>
                        <?php foreach ($reservation->fees as $fee) : ?>
                            <div class="booking-row small">
                                <div style="float:right"><?= ListingHelper::withSymbol($fee->total, $reservation) ?></div>
                                <?= __($fee->fee_name, 'hostifybooking') ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php
                    if (!empty($reservation->extra)):

                        ob_start();
                        foreach ($reservation->extra as $fee):
                            if ($fee->checked):
                                ?>
                                <div class="booking-row small">
                                    <div style="float:right">
                                        <?= ListingHelper::withSymbol($fee->total, $reservation) ?>
                                    </div>
                                    <span>
                                        <?= __($fee->fee_name, 'hostifybooking') ?>
                                        <?php if ($fee->quantity > 1): ?>
                                            &times; <?= $fee->quantity ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <?php
                            endif;
                        endforeach;
                        $extras = ob_get_clean();

                        if (!empty($extras)):
                            ?>

                            <div class="booking-block">
                                <div class="booking-row">
                                    <div style="float:right"><?= ListingHelper::withSymbol($reservation->total - $reservation->extras_price, $reservation) ?></div>
                                    <?= __('Subtotal', 'hostifybooking') ?>
                                </div>
                            </div>

                            <br/>
                            <div class="booking-row mb-2">
                                <?= __('Extras', 'hostifybooking') ?>
                            </div>
                            <?= $extras ?>
                            <?php
                        endif;
                    endif;
                    ?>

                </div>

                <div class="booking-block">
                    <div class="booking-row-total">
                        <div style="float:right"><?= ListingHelper::withSymbol($total, $reservation) ?></div>
                        <?= __('Total', 'hostifybooking') ?>
                    </div>
                </div>

                <?php if (isset($alt)): ?>
                    <br/>
                    <div class="booking-row">
                        <div style="float:right">
                            <?= ListingHelper::withSymbol($alt->guest_total_price ?? $alt->payout_price, $reservation) ?>
                        </div>
                        <?= __('Alteration request', 'hostifybooking') ?>
                    </div>
                    <div style='text-decoration:underline; padding-top: 10px; cursor: pointer;' class="alt-details-btn">View details</div>
                    <hr/>
                <?php endif; ?>

            </div>

        </div>
        <?php /* ?>
        <div class="col-md-1 vert-line"></div>
        <?php */ ?>
        <div class="col-md-1 hide-to-1200"></div>
        <div class="col-md-5 payment-info">

            <div class="widget hfy-payment-success">

                <h3 class="text-center text-reservation-confirmed">
                    <?= __('Your reservation', 'hostifybooking') ?>
                    &nbsp;
                    <b class="label label-primary"><?= $reservation->status_description ?></b>
                </h3>

                <hr />

                <div class="row result-row">
                    <div class="col-lg-4 title">
                        <?= __('Reservation code', 'hostifybooking') ?>
                    </div>
                    <div class="col-lg-8 copy-text copytocb">
                        <input type='text' readonly value="<?= esc_attr($reservation->confirmation_code) ?>" />
                        <span class="icon-copy"></span>
                    </div>
                </div>

                <hr />

                <div class="row result-row">
                    <div class="col-md-4 title ico-address">
                        <?= __('Address', 'hostifybooking') ?>
                    </div>
                    <div class="col-md-8">
                        <?= $listingInfo->address ?? '' ?>
                    </div>
                </div>

                <hr />

                <div class="row result-row">
                    <div class="col-md-4 title ico-houserul">
                        <?= __('House rules', 'hostifybooking') ?>
                    </div>
                    <div class="col-md-8">
                        <?= __('Check in', 'hostifybooking') ?>:
                        <?php
                        $c1 = fix_check_time($reservation->checkin_start ?? '');
                        $c2 = fix_check_time($reservation->checkin_end ?? '');
                        $c3 = fix_check_time($reservation->checkout ?? '');
                        ?>
                        <?php if ($c1 == $c2) : ?>
                            <?= $c1 ?>
                        <?php else : ?>
                            <?php if ($c1 == __('Any time', 'hostifybooking')) : ?>
                                <?= $c1 ?>
                            <?php else : ?>
                                <?= __('start from', 'hostifybooking') ?> <?= $c1 ?>
                                <?php if ($c2 != __('Any time', 'hostifybooking')) : ?>
                                    <?= __('till', 'hostifybooking') ?> <?= $c2 ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div>
                            <?= __('Check out', 'hostifybooking') ?>: <?= $c3 ?>
                        </div>

                        <div>
                            <?= __('Smoking allowed:', 'hostifybooking') ?>
                            <?= $reservation->smoking_allowed ? __('Yes', 'hostifybooking') : __('No', 'hostifybooking') ?>
                        </div>
                        <div>
                            <?= __('Children allowed:', 'hostifybooking') ?>
                            <?= $reservation->children_allowed ? __('Yes', 'hostifybooking') : __('No', 'hostifybooking') ?>
                        </div>
                        <div>
                            <?= __('Pets allowed:', 'hostifybooking') ?>
                            <?= $reservation->pets_allowed ? __('Yes', 'hostifybooking') : __('No', 'hostifybooking') ?>
                        </div>
                        <div>
                            <?= __('Party allowed:', 'hostifybooking') ?>
                            <?= $reservation->events_allowed ? __('Yes', 'hostifybooking') : __('No', 'hostifybooking') ?>
                        </div>
                    </div>
                </div>

                <?php if ($showCancel): ?>
                    <br />
                    <div class="mt-4 text-center noprint">
                        <span id="btn-cancel-booking">
                            <div class="btn btn-outline-primary" style="margin-left: auto; margin-right: auto; padding: 10px 20px;">
                                <?= __('Cancel booking', 'hostifybooking') ?>
                            </div>
                        </span>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>
</div>

<?php if ($showCancel): ?>
<div style="display:none">
    <div class="cancel-booking-modal modal-dialog modal-dialog-centered" role="dialog" tabindex="-1">

        <div class="modal-header">
            <div class="modal-title">
                <?= __('Cancel booking', 'hostifybooking') ?>
            </div>
        </div>

        <div class="modal-body">

            <form method="POST">
                <input type="hidden" name="reservation_id" value="<?= $reservation->id ?>" />
                <div class="row first_row">
                    <div class="col-sm-12 single">
                        <div class="form-group">
                            <label class="control-label"><?= __('Reason', 'hostifybooking') ?></label>
                            <select class="form-control reason" placeholder="<?= esc_attr__('Select reason', 'hostifybooking') ?>" style="width:100%">
                                <option value=""></option>
                                <option value="1"><?= __('Illness', 'hostifybooking') ?></option>
                                <option value="2"><?= __('Local competition preferred', 'hostifybooking') ?></option>
                                <option value="3"><?= __('Other destination preferred', 'hostifybooking') ?></option>
                                <option value="4"><?= __('Duplicate booking', 'hostifybooking') ?></option>
                                <option value="5"><?= __('Flight / Train cancelled', 'hostifybooking') ?></option>
                                <option value="6"><?= __('Change of business event', 'hostifybooking') ?></option>
                                <option value="7"><?= __('Decided to stay home', 'hostifybooking') ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 single">
                        <div class="form-group">
                            <label class="control-label"><?= __('Please enter your comment or feedback here (optional)', 'hostifybooking') ?></label>
                            <textarea class="form-control" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="row error error1" style="display: none">
                    <div class="col-sm-12 single">
                        <label class="control-label">
                            <?= __('Please tell us a reason or a comment', 'hostifybooking') ?>
                        </label>
                    </div>
                </div>
                <div class="row error error2" style="display: none">
                    <div class="col-sm-12 single">
                        <label class="control-label">
                            <?= __('Sorry, an error occured. Please try again later.', 'hostifybooking') ?>
                        </label>
                    </div>
                </div>
                <div class="row last_row">
                    <div class="col-sm-12 single">
                        <div class="form-group">
                            <input class="um-button btn btn-red" type="button" value="<?= esc_attr__('Send', 'hostifybooking') ?>" />
                        </div>
                    </div>
                </div>
                <div class="ok" style="display: none">
                    <div class="col-sm-12 single">
                        <label class="control-label">
                            <?= __('Request has been sent.', 'hostifybooking') ?>
                        </label>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
<?php endif; ?>
