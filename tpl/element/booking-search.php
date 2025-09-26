<?php 
if (!defined('WPINC')) die; 

// Ensure keyword search constants are defined
if (!defined('HFY_ENABLE_KEYWORD_SEARCH')) {
    $options = get_option('hostifybooking-plugin');
    define('HFY_ENABLE_KEYWORD_SEARCH', ($options['enable_keyword_search'] ?? 'no') == 'yes');
}

if (!defined('HFY_KEYWORD_SEARCH_PLACEHOLDER')) {
    $options = get_option('hostifybooking-plugin');
    $xx = trim($options['keyword_search_placeholder'] ?? '');
    define('HFY_KEYWORD_SEARCH_PLACEHOLDER', empty($xx) ? __('Search by keyword...', 'hostifybooking') : $xx);
}

$hideMap = $_GET['hidemap'] ?? '';
?>

<div class="hfy-search-form-wrap">

    <form action="<?= UrlHelper::listings([], $samepage ?? null) ?>" method="get" id="formBooking" target="_top">

        <?php if (HFY_LONG_SHORT_SELECTOR): ?>

            <div class="btn-group btn-group-toggle ltm-selector" data-toggle="buttons">
                <label class="btn btn-secondary <?= $longTermMode == 1 ? 'active' : '' ?>">
                    <input type="radio" name="long_term_mode" value="1" id="ltm1" <?= $longTermMode == 1 ? 'checked' : '' ?> />
                    <?= __('Long-term bookings', 'hostifybooking') ?>
                </label>
                <label class="btn btn-secondary <?= $longTermMode == 2 ? 'active' : '' ?>">
                    <input type="radio" name="long_term_mode" value="2" id="ltm2" <?= $longTermMode == 2 ? 'checked' : '' ?> />
                    <?= __('Short-term bookings', 'hostifybooking') ?>
                </label>
            </div>

        <?php else: ?>

            <input type='hidden' name='long_term_mode' value="<?= esc_attr($longTermMode) ?>" />

        <?php endif; ?>

        <div class="hfy-search-form-row">

            <?php if (!empty($tagsmenu)): ?>

                <div class="_col col-location">
                    <div class="form-group">
                        <div class="input-group booking-search-input-container">
                            <select name="tag" class="input-theme1 form-control <?= HFY_RICH_SELECT_LOC ? 'search-place' : '' ?>">
                                <option value="" selected><?= HFY_TEXT_SELECT_LOCATION ?></option>
                                <?php foreach ($tagsmenu as $item): ?>
                                    <option value="<?= esc_attr($item) ?>" <?= $prm->tag == $item ? 'selected' : '' ?>><?= $item ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

            <?php elseif (!empty(HFY_TAGS_MENU)): ?>

                <div class="_col col-location">
                    <div class="form-group">
                        <div class="input-group booking-search-input-container">
                            <select name="tag" class="input-theme1 form-control <?= HFY_RICH_SELECT_LOC ? 'search-place' : '' ?>">
                                <option value="" selected><?= HFY_TEXT_SELECT_LOCATION ?></option>
                                <?php foreach (HFY_TAGS_MENU as $item): ?>
                                    <option value="<?= esc_attr($item['title']) ?>" <?= $prm->tag == $item['title'] ? 'selected' : '' ?>><?= $item['title'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>


            <?php else: ?>

                <?php 
                // Debug the template condition
                echo '<!-- TEMPLATE DEBUG: noLocationFilter = ' . ($noLocationFilter ? 'TRUE' : 'FALSE') . ' -->';
                echo '<!-- TEMPLATE DEBUG: !noLocationFilter = ' . (!$noLocationFilter ? 'TRUE' : 'FALSE') . ' -->';
                echo '<!-- TEMPLATE DEBUG: isset(bookingEngine->cities) = ' . (isset($bookingEngine->cities) ? 'TRUE' : 'FALSE') . ' -->';
                echo '<!-- TEMPLATE DEBUG: Condition result = ' . ((!$noLocationFilter && isset($bookingEngine->cities)) ? 'TRUE' : 'FALSE') . ' -->';
                ?>
                <?php if (
                    !$noLocationFilter && isset($bookingEngine->cities)
                    // && count($bookingEngine->cities) > 1
                ): ?>
                    <div class="_col col-location">
                        <div class="form-group">
                            <div class="input-group booking-search-input-container">

                                <?php if (!HFY_RICH_SELECT_LOC): ?>

                                    <select name="city_id" class="input-theme1 form-control">
                                        <option value="" selected><?= HFY_TEXT_SELECT_LOCATION ?></option>
                                        <?php if ($city_id): ?>
                                            <?php foreach ($bookingEngine->cities as $city): ?>
                                                <option value="<?= (int) $city->city_id ?>" <?= $city->city_id == $city_id ? 'selected' : '' ?>><?= $city->name ?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <?php foreach ($bookingEngine->cities as $city): ?>
                                                <option value="<?= (int) $city->city_id ?>"><?= $city->name ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>

                                <?php else: ?>

                                    <input type="hidden" name="custom_search" class="custom-search" />
                                    <?php
                                    $currentText = empty($custom_search) ? HFY_TEXT_SELECT_LOCATION : $custom_search;
                                    ?>
                                    <select name="neighbourhood" class="input-theme1 form-control search-place">
                                        <?php
                                        if (!empty($settings->locations_array)): ?>
                                            <option value=""  <?= empty($city_neigh) ? 'selected' : '' ?>><?= $currentText ?></option>
                                            <?php
                                            foreach (hfyFilterLocations($settings, $locations_filter ?? '', HFY_LOCATIONS_SELECTOR) as $ival => $itext):
                                                $conly = strpos($ival, ':') === false;
                                                $issel = empty($city_neigh)
                                                    ? false
                                                    : ($city_neigh !== 0) && (($city_neigh == $ival) || (strpos($ival, ':' . $city_neigh) !== false) || ($ival === $city_neigh));
                                                ?>
                                                <option value="<?= esc_attr($ival) ?>" <?= $issel ? 'selected' : '' ?> <?= $conly ? '' : 'class="neigh"' ?>><?= $itext ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>

                                <?php endif; ?>

                                <?php if ($showIcons): ?>
                                    <i class="fa fa-building"></i>
                                <?php endif;?>

                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

            <?php if (HFY_ENABLE_KEYWORD_SEARCH): ?>
                <div class="_col col-keyword">
                    <div class="form-group">
                        <div class="input-group booking-search-input-container">
                            <input type="text" name="keyword" placeholder="<?= esc_attr(HFY_KEYWORD_SEARCH_PLACEHOLDER) ?>" class="input-theme1 form-control" value="<?= esc_attr($keyword ?? '') ?>"/>
                            <?php if ($showIcons): ?>
                                <i class="fa fa-search"></i>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (HFY_USE_V3_CALENDAR): ?>
                <div class="_col col-start-date col-dates">
                    <div class="form-group">
                        <div class="input-group booking-search-input-container">

                            <input type="hidden" name="start_date" value="<?= esc_attr($start_date) ?>"/>
                            <input type="hidden" name="end_date" value="<?= esc_attr($end_date) ?>"/>

                            <input type="text" id="hotel-datepicker" placeholder="<?= esc_attr__('Check In', 'hostifybooking') . ' – ' . esc_attr__('Check Out', 'hostifybooking') ?>" readonly class="input-theme1 form-control hotel-datepicker" value="<?= esc_attr($dates_value) ?>"/>

                            <?php if ($showIcons): ?>
                                <i class="fa fa-calendar text-primary" ></i>
                            <?php endif;?>
                        </div>
                    </div>
                </div>

            <?php elseif (HFY_USE_NEW_CALENDAR): ?>
                <div class="_col col-start-date col-dates">
                    <div class="form-group">
                        <div class="input-group booking-search-input-container">

                            <input type="hidden" name="start_date" value="<?= esc_attr($start_date) ?>"/>
                            <input type="hidden" name="end_date" value="<?= esc_attr($end_date) ?>"/>

                            <input type="text" placeholder="<?= esc_attr__('Check In', 'hostifybooking') . ' – ' . esc_attr__('Check Out', 'hostifybooking') ?>" readonly class="input-theme1 form-control calentim-dates" value="<?= esc_attr($dates_value) ?>"/>

                            <?php if ($showIcons): ?>
                                <i class="fa fa-calendar text-primary" ></i>
                            <?php endif;?>
                        </div>
                    </div>
                </div>

            <?php else: ?>

                <div class="_col col-start-date">
                    <div class="form-group">
                        <div class="input-group booking-search-input-container">
                            <input type="text" name="start_date" placeholder="<?= esc_attr__('Check In', 'hostifybooking') ?>" readonly class="input-theme1 form-control calentim-start" value="<?= esc_attr(!empty($start_date) && !empty($end_date) ? $start_date : '') ?>"/>
                            <?php if($showIcons):?>
                                <i class="fa fa-calendar text-primary" ></i>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <div class="_col col-end-date">
                    <div class="form-group">
                        <div class="input-group booking-search-input-container">
                            <input type="text" class="input-theme1 form-control calentim-end" name="end_date" readonly placeholder="<?= esc_attr__('Check Out', 'hostifybooking') ?>" value="<?= !empty($start_date) && !empty($end_date) ? $end_date : '' ?>"/>
                            <?php if($showIcons):?>
                                <i class="fa fa-calendar text-primary" ></i>
                            <?php endif;?>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

            <?php if (isset($bookingEngine->bedroom_filter) && $bookingEngine->bedroom_filter == 1):?>
                <div class="_col col-bedrooms">
                    <div class="form-group">
                        <div class="input-group booking-search-input-container">
                            <select name="bedrooms" class="input-theme1 form-control">
                                <option value="" selected <?= '' === $bedrooms ? 'disabled' : '' ?> ><?= __('Bedrooms', 'hostifybooking') ?></option>
                                <?php if (HFY_SHOW_STUDIO_OPTION): ?>
                                    <option value="0" <?= '0' === $bedrooms ? 'selected' : '' ?> ><?= __('Studio', 'hostifybooking') ?></option>
                                <?php endif; ?>
                                <?php foreach ($bookingEngine->bedrooms as $bedroom): ?>
                                    <?php if ($bedroom->bedrooms > 0): ?>
                                        <option value="<?=$bedroom->bedrooms?>" <?= $bedroom->bedrooms == $bedrooms ? 'selected' : '' ?>><?=$bedroom->bedrooms?> <?=$bedroom->bedrooms == 1 ? __('Bedroom', 'hostifybooking') : __('Bedrooms', 'hostifybooking') ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($showIcons): ?>
                                <i class="fa fa-bed" ></i>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <div class="_col col-guests">
                <div class="form-group">
                    <div class="input-group number-input booking-search-input-container ico-guest">
                        <?php if (HFY_USE_BOOKING_FORM_V2): ?>
                            <input type="text" class="input-theme1 form-control guests" placeholder="1" name="guests" readonly value="<?= $guests ? $guests : 1 ?>"/>
                            <?php include hfy_tpl('element/guests-block'); ?>
                        <?php else: ?>
                            <a class="first noselect">
                                <svg width="24" height="24" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="17" cy="16" r="16.2" stroke="#CDD1D6" stroke-width="1"/><path d="M10 16L24 16" stroke="#627080" stroke-width="1.5"/></svg>
                            </a>
                            <input type="text" class="input-theme1 form-control guests" placeholder="1" name="guests" readonly value="<?= $guests ? $guests : 1 ?>"/>
                            <a class="last noselect">
                                <svg width="24" height="24" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="17" cy="16" r="16.2" stroke="#CDD1D6" stroke-width="1"/><path d="M17 9.59998L17 22.4" stroke="#627080" stroke-width="1.5"/><path d="M10 16L24 16" stroke="#627080" stroke-width="1.5"/></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($advanced): ?>
                <div class="_col col-advanced">
                    <button class="btn advanced <?= !empty($prm->prop) || !empty($prm->am) ? 'active' : '' ?>" type="button"></button>
                </div>
            <?php endif;?>

            <div class="_col col-action">
                <button class="btn btn-primary" type="submit">
                    <span style="margin-right:10px">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.21015 16.7525C4.041 15.6353 1.56685 11.35 2.68397 7.18082C3.80109 3.01168 8.08645 0.537518 12.2556 1.65464C16.4247 2.77176 18.8989 7.05713 17.7818 11.2263C16.6647 15.3954 12.3793 17.8696 8.21015 16.7525Z" stroke="#fff" stroke-width="1.5"/><path d="M14.59 15.5782L19.9998 22.7911" stroke="#fff" stroke-width="1.5"/></svg>
                    </span>
                    <?= __( 'Search', 'hostifybooking' ) ?>
                </button>
            </div>
            <input type="hidden" id="hidemap" name="hidemap" value="<?= $hideMap ?>">
        </div>

        <?php if ($advanced) include hfy_tpl('element/booking-search-advanced'); ?>

    </form>

</div>
