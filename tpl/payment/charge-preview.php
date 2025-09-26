<?php
if (!defined('WPINC')) die;

$sym = '&nbsp;'.$listingInfo->currency_symbol.'&nbsp;';
?>
<div id="roomInfoSection">
    <a href="<?= UrlHelper::listing([ 'id' => $listingInfo->id ]) ?>">
        <div class="cover-bg fluid" style="background-image:url(<?=$listingInfo->thumbnail_file;?>)"></div>
    </a>
    <div class="booking-block">
        <div class="booking-title"><?=$listingInfo->name;?></div>
        <div><span><?=$listingInfo->city;?>, <?=$listingInfo->country;?></span></div>
    </div>
    <div class="booking-block">
        <div class="row" style="padding:0">
            <div class="col-xs-5 col-5"><?= __('Check In', 'hostifybooking') ?>
                <div class="booking-title sub"><?=date_format(date_create($reserveInfo->start_date),"M d, Y");?></div>
            </div>
            <div class="col-xs-2 col-2"><img src="<?= HOSTIFYBOOKING_URL ?>public/res/images/next.png" style="height:16px;margin-top:16px"></div>
            <div class="col-xs-5 col-5"><?= __('Check Out', 'hostifybooking') ?>
                <div class="booking-title sub"><?=date_format(date_create($reserveInfo->end_date),"M d, Y");?></div>
            </div>
        </div>
    </div>
    <div class="booking-block">
        <div class="booking-title sub">
            <div style="float:right"><?=$reserveInfo->guests;?></div>
            <?= __('Guests', 'hostifybooking') ?>
        </div>
        <?php if (HFY_USE_BOOKING_FORM_V2): ?>
            <div class="booking-title sub">
                <div style="float:right;text-align:right">
                    <?= $reserveInfo->adults ?>
                    <br/>
                    <?= $reserveInfo->children ?>
                    <br/>
                    <?= $reserveInfo->infants ?>
                    <br/>
                    <?= $reserveInfo->pets ?>
                </div>
                &rsaquo; <?= __('Adults', 'hostifybooking') ?>
                <br/>
                &rsaquo; <?= __('Children', 'hostifybooking') ?>
                <br/>
                &rsaquo; <?= __('Infants', 'hostifybooking') ?>
                <br/>
                &rsaquo; <?= __('Pets', 'hostifybooking') ?>
                <div class='clearfix'></div>
            </div>
        <?php endif; ?>

    </div>

    <div class="booking-block">
        <div class="booking-title">
            <div style="float:right"><?= ListingHelper::withSymbol($reservation->total_price, $reservation->prices, $sym) ?></div>
            <?= __('Total', 'hostifybooking') ?>
        </div>

        <?php
        $isSecDep = $settings->security_deposit && (intval($reserveInfo->prices->security_deposit) > 0);

        ?>
    </div>
</div>
