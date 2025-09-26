<?php
if (!defined('WPINC')) die;

$sym = '&nbsp;'.$listingInfo->currency_symbol.'&nbsp;';
?>
<div id="roomInfoSection">
    <a href="<?= UrlHelper::listing([ 'id' => $listingInfo->id ]) ?>" class="listing-info">
        <div class="cover-bg fluid" style="background-image:url(<?=$listingInfo->thumbnail_file;?>)"></div>
        <div>
            <div class="listing-title"><?=$listingInfo->name;?></div>
            <div class="listing-city"><?=$listingInfo->city;?>, <?=$listingInfo->country;?></div>
        </div>
    </a>

    <hr class="mob-hide" />

    <div class="booking-block booking-dates">
        <div class="row" style="padding:0">
            <div class="col-5">
                <small><?= __('Check In', 'hostifybooking') ?></small>
                <br/>
                <?= date_format(date_create($reserveInfo->start_date),"M d, Y"); ?>
            </div>
            <div class="col-2 text-center"><img src="<?= HOSTIFYBOOKING_URL ?>public/res/images/next.png" style="height:16px;margin-top:16px"></div>
            <div class="col-5 text-right">
                <small><?= __('Check Out', 'hostifybooking') ?></small>
                <br/>
                <?= date_format(date_create($reserveInfo->end_date),"M d, Y"); ?>
            </div>
        </div>
    </div>

    <hr class="mob-hide" />

    <div class="booking-block mob-hide">
        <div class="booking-title sub">
            <div style="float:right"><?=$reserveInfo->guests;?></div>
            <?= __('Guests', 'hostifybooking') ?>
        </div>
        <?php if (HFY_USE_BOOKING_FORM_V2): ?>
            <div class="booking-title sub">
                <div style="float:right;text-align:right">
                    <?= $reserveInfo->adults ?>
                    <br/>
                    <?php if ($reserveInfo->children > 0): ?>
                        <?= $reserveInfo->children ?>
                        <br/>
                    <?php endif; ?>
                    <?php if ($reserveInfo->infants > 0): ?>
                        <?= $reserveInfo->infants ?>
                        <br/>
                    <?php endif; ?>
                    <?php if ($reserveInfo->pets > 0): ?>
                        <?= $reserveInfo->pets ?>
                    <?php endif; ?>
                </div>
                <?= __('Adults', 'hostifybooking') ?>
                <br/>
                <?php if ($reserveInfo->children > 0): ?>
                    <?= __('Children', 'hostifybooking') ?>
                    <br/>
                <?php endif; ?>
                <?php if ($reserveInfo->infants > 0): ?>
                    <?= __('Infants', 'hostifybooking') ?>
                    <br/>
                <?php endif; ?>
                <?php if ($reserveInfo->pets > 0): ?>
                    <?= __('Pets', 'hostifybooking') ?>
                <?php endif; ?>
                <div class='clearfix'></div>
            </div>
        <?php endif; ?>

        <?php
        if ($accountingActive) {
            include hfy_tpl('payment/preview-accounting');
        } else {
            include hfy_tpl('payment/preview-default');
        }
        ?>

        <?php if (!empty($reserveInfo->prices->offline)) : ?>
            <span class='upon-arrival'><?= __('Due upon arrival:', 'hostifybooking') ?></span>
            <?php foreach ($reserveInfo->prices->offline as $o) : ?>
                <div class="booking-title sub">
                    <div style="float:right"><?= ListingHelper::withSymbol($o->total, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
                    <?= __($o->fee_name, 'hostifybooking') ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <div class="mob-hide">

        <?php if (!HFY_USE_API_V3 && (isset($reserveInfo->prices->includes_exclusive_fees) && ($reserveInfo->prices->includes_exclusive_fees > 0))): ?>
            <div class='booking-title sub'>
                <div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->prices->subtotal, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
                <?= __( 'Subtotal', 'hostifybooking' ) ?>
            </div>
            <div class='booking-title sub'>
                <div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->prices->totalFees, $reserveInfo->prices, $listingInfo->currency_symbol) ?></div>
                <?= __( 'Taxes', 'hostifybooking' ) ?>
            </div>
        <?php endif; ?>

        <?php include hfy_tpl('payment/preview-extras-set'); ?>

        <?php include hfy_tpl('payment/preview-extras-optional'); ?>

    </div>

    <div class="booking-block pt-0">
        <div class="booking-title">
            <div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->prices->totalAfterTax ?? $reserveInfo->prices->total, $reserveInfo->prices, $sym) ?></div>
            <?= __('Total', 'hostifybooking') ?>
        </div>

        <?php
        $isSecDep = isset($settings->security_deposit) && (intval($reserveInfo->prices->security_deposit) > 0);
        $isTotalPart = $totalPartial > 0;
        if ($isSecDep || $isTotalPart) :
        ?>
            <div class="booking-title">
                <div style="float:right"><?= ListingHelper::withSymbol(
                    $totalPartial > 0 ? $totalPartial : $total,
                    $reserveInfo->prices, $sym) ?></div>
                <?= __('Due now', 'hostifybooking') ?>
            </div>

            <?php if ($isSecDep) : ?>
                <hr class="mob-hide" />
                <div class="booking-title sub">
                    <div style="float:right"><?= ListingHelper::withSymbol($reserveInfo->prices->security_deposit, $reserveInfo->prices, $sym) ?></div>
                    <?= __( 'Security deposit', 'hostifybooking' ) ?>
                </div>
                <?php /* ?>
                <div class="booking-title sub">
                    <div style="float:right"><?= ListingHelper::withSymbol(
                        $total + $reserveInfo->prices->security_deposit,
                        $reserveInfo->prices, $sym) ?></div>
                    <?= __( 'Price incl. security deposit', 'hostifybooking' ) ?>
                </div>
                <?php */ ?>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <hr class="mob-hide" />

    <div class="mob-hide">
        <?php include hfy_tpl('payment/additional-info'); ?>
    </div>

    <div class="mob-show pt-2">
        <div id="details-show" class="form-control btn btn-default"><?= __('Show details', 'hostifybooking') ?></div>
        <div id="details-hide" class="form-control btn btn-default"><?= __('Hide details', 'hostifybooking') ?></div>
    </div>
</div>
