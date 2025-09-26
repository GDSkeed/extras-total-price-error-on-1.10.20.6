<?php
if (!defined('WPINC')) die;

$nights = !empty($listingPrice->nights) ? $listingPrice->nights : 1;

$fill_name1 = HfyHelper::getUserMeta('first_name');
$fill_name2 = HfyHelper::getUserMeta('last_name');
$fill_email = HfyHelper::getUserMeta('user_email');
$fill_phone = HfyHelper::getUserMeta('phone_number');

?>
<div style="display:none">

    <div class="direct-inquiry-modal" role="dialog" tabindex="-1">
        <div class="hfy-wrap">
            <div class="direct-inquiry-modal-content">
                <h4 class="modal-title"><?= __('Reservation Inquiry', 'hostifybooking') ?></h4>
                <form class="direct-inquiry-form">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6 col-xs-6 col-sm-6 inquiry_container_input container_input">
                                <input type="text" class="form-control form-icon" name="first_name" placeholder="<?= esc_attr__('First Name', 'hostifybooking') ?>" required="required" value="<?= esc_attr($fill_name1) ?>">
                                <i class="fa fa-user text-primary"></i>
                                <span class="error" id="first_name_error"></span>
                            </div>
                            <div class="col-md-6 col-xs-6 col-sm-6 inquiry_container_input container_input">
                                <input type="text" class="form-control form-icon" name="last_name" placeholder="<?= esc_attr__('Last Name', 'hostifybooking') ?>" required="required" value="<?= esc_attr($fill_name2) ?>">
                                <i class="fa fa-user text-primary"></i>
                                <span class="error" id="last_name_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6 col-xs-6 col-sm-6 inquiry_container_input container_input">
                                <input type="email" class="form-control form-icon" name="email" placeholder="<?= esc_attr__('Email', 'hostifybooking') ?>" required="required" value="<?= esc_attr($fill_email) ?>">
                                <i class="fa fa-envelope text-primary"  ></i>
                                <span class="error" id="email_error"></span>
                            </div>
                            <div class="col-md-6 col-xs-6 col-sm-6 inquiry_container_input container_input">
                                <input type="text" class="form-control form-icon" name="phone" placeholder="<?= esc_attr__('Phone', 'hostifybooking') ?>" required="required" value="<?= esc_attr($fill_phone) ?>">
                                <i class="fa fa-phone text-primary"  ></i>
                                <span class="error" id="phone_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <?php if (HFY_USE_V3_CALENDAR): ?>
                                <!--
                                    Using the same format and structure as listing-booking-form-v2.php
                                    to maintain consistency across the site
                                -->
                                <input type="hidden" name="check_in" value="<?= esc_attr($startDate) ?>"/>
                                <input type="hidden" name="check_out" value="<?= esc_attr($endDate) ?>"/>
                                <div class="col-md-12 col-xs-12 col-sm-12 inquiry_container_input">
                                    <div class="input-group">
                                        <?php
                                        // Format dates value for the calendar
                                        $dates_value = '';
                                        if (!empty($startDate) && !empty($endDate)) {
                                            $dates_value = $startDate . ' - ' . $endDate;
                                        }
                                        ?>
                                        <input type="text"
                                            id="inquiry-hotel-datepicker"
                                            placeholder="<?= esc_attr__('Check In', 'hostifybooking') . ' – ' . esc_attr__('Check Out', 'hostifybooking') ?>"
                                            readonly
                                            class="form-control hotel-datepicker form-icon"
                                            value="<?= esc_attr($dates_value) ?>"
                                        />
                                        <i class="fa fa-calendar text-primary"></i>
                                        <span class="error" id="check_in_error"></span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="col-md-6 col-xs-6 col-sm-6 inquiry_container_input">
                                    <input type="text"
                                        autocomplete="off"
                                        class="form-control datepicker2 form-icon"
                                        name="check_in"
                                        id="inquiry_checkin"
                                        placeholder="<?= esc_attr__('Check In', 'hostifybooking') ?>"
                                        required="required"
                                        value="<?= esc_attr($startDate) ?>"
                                    />
                                    <i class="fa fa-calendar text-primary"></i>
                                    <span class="error" id="check_in_error"></span>
                                </div>
                                <div class="col-md-6 col-xs-6 col-sm-6 inquiry_container_input">
                                    <input type="text"
                                        autocomplete="off"
                                        class="form-control datepicker2 form-icon"
                                        name="check_out"
                                        id="inquiry_checkout"
                                        placeholder="<?= esc_attr__('Check Out', 'hostifybooking') ?>"
                                        value="<?= esc_attr($endDate) ?>"
                                        required="required"
                                    />
                                    <i class="fa fa-calendar text-primary"></i>
                                    <span class="error" id="check_out_error"></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4 col-xs-4 col-sm-4 inquiry_container_input container_input">
                                <input type="number" min="1" class="form-control form-icon" id="inquiry_adults" name="adults" placeholder="<?= esc_attr__('Adults', 'hostifybooking') ?>" required="required">
                                <i class="fa fa-male text-primary"  ></i>
                                <span class="error" id="adults_error"></span>
                            </div>
                            <div class="col-md-4 col-xs-4 col-sm-4 inquiry_container_input container_input">
                                <input type="number" min="0" class="form-control form-icon" id="inquiry_children" name="children" placeholder="<?= esc_attr__('Children', 'hostifybooking') ?>">
                                <i class="fa fa-child text-primary"  ></i>
                                <span class="error" id="children_error"></span>
                            </div>
                            <div class="col-md-4 col-xs-4 col-sm-4 inquiry_container_input container_input">
                                <input type="number" min="0" class="form-control form-icon" id="inquiry_infants" name="infants" placeholder="<?= esc_attr__('Infants', 'hostifybooking') ?>">
                                <i class="fa fa-smile text-primary"  ></i>
                                <span class="error" id="infants_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4 col-xs-4 col-sm-4 inquiry_container_input container_input">
                                <input type="number" min="0" class="form-control form-icon" id="inquiry_pets" name="pets" placeholder="<?= esc_attr__('Pets', 'hostifybooking') ?>">
                                <i class="fa fa-paw text-primary"  ></i>
                                <span class="error" id="pets_error"></span>
                            </div>
                            <?php if (true||HFY_SHOW_DISCOUNT): ?>
                                <div class="col-md-8 col-xs-8 col-sm-8 inquiry_container_input container_input">
                                    <input type="text" class="form-control form-icon" name="discount_code" placeholder="<?= esc_attr__('Discount Code', 'hostifybooking') ?>">
                                    <i class="fa fa-tags text-primary"></i>
                                    <span class="error" id="discount_code_error"></span>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="discount_code" />
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12 direct-inquiry-col-container">
                                <textarea class="form-control form-icon" name="message"placeholder="<?= esc_attr__('Question or Comment', 'hostifybooking') ?>"  required="required"></textarea>
                                <i class="fa fa-comment text-primary"  ></i>
                                <span class="error" id="message_error"></span>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($terms)): ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 direct-inquiry-col-container">
                                    <input type="checkbox" name="terms" required="required" class="terms-checkbox" />
                                    <?= $terms ?>
                                    <span class="error" id="terms_error"></span>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12 direct-inquiry-col-container">
                                <?php if (!empty($settings->api_key_captcha)): ?>
                                <?php 
                                $recaptcha_version = defined('HFY_GOOGLE_RECAPTCHA_VERSION') ? HFY_GOOGLE_RECAPTCHA_VERSION : 'v2';
                                if ($recaptcha_version === 'v3') {
                                    echo '<script src="https://www.google.com/recaptcha/api.js?render=' . esc_attr($settings->api_key_captcha) . '"></script>';
                                } else {
                                    echo '<script src="//www.google.com/recaptcha/api.js" async defer></script>';
                                }
                                ?>
                                <script>
                                // Pass global settings to JavaScript
                                window.HFY_USE_NEW_INQUIRY_FORM = <?php echo (defined('HFY_USE_NEW_INQUIRY_FORM') && HFY_USE_NEW_INQUIRY_FORM) ? 'true' : 'false'; ?>;
                                window.HFY_USE_V3_CALENDAR = <?php echo (defined('HFY_USE_V3_CALENDAR') && HFY_USE_V3_CALENDAR) ? 'true' : 'false'; ?>;
                                
                                <?php if ($recaptcha_version === 'v2'): ?>
                                var imNotARobot = function () { document.getElementById('g-recaptcha-error').innerHTML = ''; }
                                <?php endif; ?>
                                </script>
                                <?php if ($recaptcha_version === 'v2'): ?>
                                <div class="direct-inquiry-captcha">
                                    <div class="g-recaptcha" data-sitekey="<?= $settings->api_key_captcha ?>" data-callback="imNotARobot"></div>
                                    <div id="g-recaptcha-error"></div>
                                </div>
                                <?php else: ?>
                                <!-- reCAPTCHA v3 is invisible - no widget needed -->
                                <div id="g-recaptcha-error" style="display: none;"></div>
                                <?php endif; ?>
                                <?php endif; ?>
                                <span class="error" id="verifyCode_error" style="float: left;"></span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="listingId" value="<?= (int) $listingData->id ?>" />
                    <input type="hidden" name="listingName" value="<?= esc_attr($listingData->name) ?>" />
                    <input type="hidden" name="listingNickname" value="<?= esc_attr($listingData->nickname) ?>" />
                    <input type="hidden" name="nights" id="direct-inquiry-nights" value="<?= (int) $nights ?>" />

                    <div class="row">
                        <div class="col-md-12 direct-inquiry-col-container">
                            <button class="btn btn-primary btn-lg direct-inquiry-modal-submit-button" type="submit" style="float: right; border-radius: 4px">
                                <?= __('Send', 'hostifybooking') ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class='thx' style="display:none">
                <div class="col-md-12" style="text-align:center">
                    <p><?= __("Thank you for your inquiry! We'll be in touch soon.", 'hostifybooking') ?></p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php if (HFY_USE_V3_CALENDAR): ?>
<!--
<script>
// The following code is now obsolete and replaced by robust initialization in inquiry-form-v2.js
// document.addEventListener('DOMContentLoaded', function() {
//     ...
//     initInquiryDatepicker();
//     ...
// });
</script>
-->
<?php endif; ?>
