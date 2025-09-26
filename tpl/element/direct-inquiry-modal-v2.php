<?php
/**
 * New Inquiry Modal Template (v2)
 * Based on Next.js design
 */

// Note: This is the basic structure. PHP variables and JS interactions will be added later.
// Icons (Mail, Phone, Tag, MessageSquare, Users, CalendarIcon, X, Minus, Plus) need to be replaced with appropriate SVGs or an icon font.
?>
<div id="hfy-direct-inquiry-modal-v2" class="hfy-modal direct-inquiry-modal hfy-direct-inquiry-modal-v2" style="display: none;" role="dialog" aria-modal="true">
    <div class="hfy-modal-content direct-inquiry-modal-content sm:max-w-[700px] max-h-[90vh] overflow-y-auto">
        <div class="hfy-modal-header">
            <h2 class="hfy-modal-title text-2xl font-semibold text-center"><?php _e('Reservation Inquiry', 'hostifybooking'); ?></h2>
            <button type="button" rel="modal:close" class="hfy-modal-close absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none data-[state=open]:bg-accent data-[state=open]:text-muted-foreground">
                <!-- X icon SVG -->
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span class="sr-only"><?php _e('Close', 'hostifybooking'); ?></span>
            </button>
        </div>

<?php if (!empty($settings->api_key_captcha)): ?>
<?php 
$recaptcha_version = defined('HFY_GOOGLE_RECAPTCHA_VERSION') ? HFY_GOOGLE_RECAPTCHA_VERSION : 'v2';
if ($recaptcha_version === 'v3') {
    echo '<script src="https://www.google.com/recaptcha/api.js?render=' . esc_attr($settings->api_key_captcha) . '"></script>';
} else {
    echo '<script src="//www.google.com/recaptcha/api.js" async defer></script>';
}
?>
<?php endif; ?>

<script>
// Pass global settings to JavaScript
window.HFY_USE_NEW_INQUIRY_FORM = <?php echo (defined('HFY_USE_NEW_INQUIRY_FORM') && HFY_USE_NEW_INQUIRY_FORM) ? 'true' : 'false'; ?>;
window.HFY_USE_V3_CALENDAR = <?php echo (defined('HFY_USE_V3_CALENDAR') && HFY_USE_V3_CALENDAR) ? 'true' : 'false'; ?>;

<?php if (!empty($settings->api_key_captcha)): ?>
// Pass reCAPTCHA settings to JavaScript
window.hfyRecaptchaVersion = '<?php echo esc_js($recaptcha_version); ?>';
window.hfyRecaptchaSiteKey = '<?php echo esc_js($settings->api_key_captcha); ?>';

<?php if ($recaptcha_version === 'v2'): ?>
function inquiryV2NotARobot() {
    document.getElementById('g-recaptcha-error-v2').innerHTML = '';
}
<?php endif; ?>
<?php endif; ?>
</script>

        <!-- Success message that will show after form submission -->
        <div class="thx" style="display:none">
            <div class="text-center p-6">
                <h3 class="text-xl font-semibold mb-4"><?php _e("Thank you for your inquiry!", 'hostifybooking'); ?></h3>
                <p><?php _e("We'll be in touch soon.", 'hostifybooking'); ?></p>
            </div>
        </div>

        <form id="hfy-direct-inquiry-form-v2" class="hfy-modal-body space-y-6">
            <input type="hidden" name="listing_id" value="<?php echo esc_attr($listingData->id ?? 0); ?>"> <!-- Use $listingData->id -->
            <input type="hidden" name="action" value="hfy_ajax_inquiry">
            <?php wp_nonce_field('hfy_ajax_inquiry_nonce', 'hfy_nonce'); ?>

<input type="hidden" name="listingName" value="<?= esc_attr($listingData->name ?? '') ?>" />
            <input type="hidden" name="listingNickname" value="<?= esc_attr($listingData->nickname ?? '') ?>" />
            <input type="hidden" name="nights" id="hfy-inquiry-nights-v2" value="<?= esc_attr($nights ?? 1) ?>" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="hfy-inquiry-firstName" class="hfy-label"><?php _e('First Name', 'hostifybooking'); ?></label>
                    <input id="hfy-inquiry-firstName" name="firstName" type="text" class="hfy-input" placeholder="<?php esc_attr_e('First Name', 'hostifybooking'); ?>" required />
                </div>

                <div class="space-y-2">
                    <label for="hfy-inquiry-lastName" class="hfy-label"><?php _e('Last Name', 'hostifybooking'); ?></label>
                    <input id="hfy-inquiry-lastName" name="lastName" type="text" class="hfy-input" placeholder="<?php esc_attr_e('Last Name', 'hostifybooking'); ?>" required />
                </div>

                <div class="space-y-2">
                    <label for="hfy-inquiry-email" class="hfy-label flex items-center gap-1">
                        <!-- Replace with Mail icon SVG -->
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <?php _e('Email', 'hostifybooking'); ?>
                    </label>
                    <input id="hfy-inquiry-email" name="email" type="email" class="hfy-input" placeholder="<?php esc_attr_e('Email', 'hostifybooking'); ?>" required />
                </div>

                <div class="space-y-2">
                    <label for="hfy-inquiry-phone" class="hfy-label flex items-center gap-1">
                        <!-- Replace with Phone icon SVG -->
                         <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <?php _e('Phone', 'hostifybooking'); ?>
                    </label>
                    <input id="hfy-inquiry-phone" name="phone" type="tel" class="hfy-input" placeholder="<?php esc_attr_e('Phone', 'hostifybooking'); ?>" required />
                </div>
            </div>

            <div class="space-y-2">
                <label class="hfy-label"><?php _e('Stay Dates', 'hostifybooking'); ?></label>
                <!-- Calendar Integration based on HFY_USE_V3_CALENDAR setting -->
                <input type="hidden" name="start_date" value=""/>
                <input type="hidden" name="end_date" value=""/>
                
                <?php if (defined('HFY_USE_V3_CALENDAR') && HFY_USE_V3_CALENDAR): ?>
                <!-- V3 Calendar (Datepicker) - Single input -->
                <div class="form-group mb-0">
                    <div class="input-group">
                        <input type="text" id="inquiry-hotel-datepicker" placeholder="<?= esc_attr__('Check In', 'hostifybooking') . ' – ' . esc_attr__('Check Out', 'hostifybooking') ?>" readonly class="form-control hotel-datepicker hfy-input" value=""/>
                    </div>
                </div>
                <?php else: ?>
                <!-- V2 Calendar (Old Calendar) - Two separate inputs -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group mb-0">
                        <div class="input-group">
                            <input type="text" name="start_date" readonly placeholder="<?= esc_attr__('Check In', 'hostifybooking') ?>" class="input-theme1 form-control calentim-start hfy-input" value="" />
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <div class="input-group">
                            <input type="text" class="input-theme1 form-control calentim-end hfy-input" name="end_date" readonly placeholder="<?= esc_attr__('Check Out', 'hostifybooking') ?>" value="" />
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="reset-date-wrap text-right mt-1">
                    <a class="reset-date text-sm text-blue-600 hover:underline cursor-pointer"><?= __( 'Clear dates', 'hostifybooking' ) ?></a>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="hfy-label text-base font-medium flex items-center gap-1">
                        <!-- Replace with Users icon SVG -->
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <?php _e('Guests', 'hostifybooking'); ?>
                    </label>
                    <span id="hfy-guest-summary-v2" class="text-sm font-medium text-muted-foreground">
                        <!-- JS will update this: e.g., 1/4 guests -->
                        <?php printf(__('%d/%d guests', 'hostifybooking'), 1, esc_attr($listingDetails->person_capacity ?? 1)); ?>
                    </span>
                     <input type="hidden" id="hfy-max-guests-v2" value="<?php echo esc_attr($listingDetails->person_capacity ?? 1); ?>">
                </div>

                <?php $children_allowed = isset($listingData) && property_exists($listingData, 'children_allowed') ? (bool) $listingData->children_allowed : false; ?>
                <div class="space-y-3 border rounded-md p-4" data-children-allowed="<?php echo $children_allowed ? 'true' : 'false'; ?>">
                    <!-- Adults -->
                    <div class="flex items-center justify-between">
                        <span><?php _e('Adults', 'hostifybooking'); ?></span>
                        <div class="flex items-center gap-3">
                            <button type="button" class="hfy-guest-btn hfy-guest-btn-minus hfy-btn hfy-btn-outline h-8 w-8 rounded-full" data-type="adults" disabled>
                                <!-- Replace with Minus icon SVG -->
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            </button>
                            <span class="hfy-guest-count w-5 text-center" data-type="adults">1</span>
                            <input type="hidden" name="adults" id="hfy-inquiry-adults-v2" value="1">
                            <button type="button" class="hfy-guest-btn hfy-guest-btn-plus hfy-btn hfy-btn-outline h-8 w-8 rounded-full" data-type="adults">
                                <!-- Replace with Plus icon SVG -->
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Children -->
                    <div class="flex items-center justify-between">
                        <span><?php _e('Children', 'hostifybooking'); ?></span>
                        <div class="flex items-center gap-3">
                            <?php $children_allowed = $listingData->children_allowed ?? false; ?>
                            <button type="button" class="hfy-guest-btn hfy-guest-btn-minus hfy-btn hfy-btn-outline h-8 w-8 rounded-full <?php echo !$children_allowed ? 'disabled' : ''; ?>" data-type="children" <?php echo !$children_allowed ? 'disabled' : ''; ?>>
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            </button>
                            <span class="hfy-guest-count w-5 text-center" data-type="children"><?php echo $children_allowed ? 0 : 0; // Always starts at 0 ?></span>
                             <input type="hidden" name="children" id="hfy-inquiry-children-v2" value="0">
                            <button type="button" class="hfy-guest-btn hfy-guest-btn-plus hfy-btn hfy-btn-outline h-8 w-8 rounded-full <?php echo !$children_allowed ? 'disabled' : ''; ?>" data-type="children" <?php echo !$children_allowed ? 'disabled' : ''; ?>>
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Infants -->
                    <div class="flex items-center justify-between">
                        <span><?php _e('Infants', 'hostifybooking'); ?></span>
                        <div class="flex items-center gap-3">
                            <?php $infants_allowed = $listingData->infants_allowed ?? ($listing->listing->infants_allowed ?? false); ?>
                            <button type="button" class="hfy-guest-btn hfy-guest-btn-minus hfy-btn hfy-btn-outline h-8 w-8 rounded-full <?php echo !$infants_allowed ? 'disabled' : ''; ?>" data-type="infants" <?php echo !$infants_allowed ? 'disabled' : ''; ?>>
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            </button>
                            <span class="hfy-guest-count w-5 text-center" data-type="infants"><?php echo $infants_allowed ? 0 : 0; // Always starts at 0 ?></span>
                             <input type="hidden" name="infants" id="hfy-inquiry-infants-v2" value="0">
                            <button type="button" class="hfy-guest-btn hfy-guest-btn-plus hfy-btn hfy-btn-outline h-8 w-8 rounded-full <?php echo !$infants_allowed ? 'disabled' : ''; ?>" data-type="infants" <?php echo !$infants_allowed ? 'disabled' : ''; ?>>
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Pets (moved inside guests container) -->
                    <div class="flex items-center justify-between">
                        <span><?php _e('Pets', 'hostifybooking'); ?></span>
                        <div class="flex items-center gap-3">
                            <?php $pets_allowed = $listingData->pets_allowed ?? ($listing->listing->pets_allowed ?? false); ?>
                            <button type="button" class="hfy-guest-btn hfy-guest-btn-minus hfy-btn hfy-btn-outline h-8 w-8 rounded-full <?php echo !$pets_allowed ? 'disabled' : ''; ?>" data-type="pets" <?php echo !$pets_allowed ? 'disabled' : ''; ?>>
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            </button>
                            <span class="hfy-guest-count w-5 text-center" data-type="pets"><?php echo $pets_allowed ? 0 : 0; // Always starts at 0 ?></span>
                            <input type="hidden" name="pets" id="hfy-inquiry-pets-v2" value="0">
                            <button type="button" class="hfy-guest-btn hfy-guest-btn-plus hfy-btn hfy-btn-outline h-8 w-8 rounded-full <?php echo !$pets_allowed ? 'disabled' : ''; ?>" data-type="pets" <?php echo !$pets_allowed ? 'disabled' : ''; ?>>
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </button>
                        </div>
                    </div>

                    <p id="hfy-guest-warning-v2" class="text-sm text-destructive" style="display: none;">
                        <?php
                        // Replicate logic from guests-block.php for detailed message
                        $max_guests_text = '';
                        if (isset($listingDetails) && isset($listingData)) { // Check if necessary data exists
                            $person_capacity = $listingDetails->person_capacity ?? 1;
                            $infants_allowed = $listingData->infants_allowed ?? ($listing->listing->infants_allowed ?? false); // Check both possible structures
                            $children_allowed = $listingData->children_allowed ?? false;
                            $children_details = $listingData->children_not_allowed_details ?? '';
                            $pets_allowed = $listingData->pets_allowed ?? ($listing->listing->pets_allowed ?? false); // Check both possible structures

                            if ($infants_allowed) {
                                $max_guests_text = sprintf(__('This place has a maximum of %d guests, not including infants.', 'hostifybooking'), $person_capacity);
                            } else {
                                $max_guests_text = sprintf(__('This place has a maximum of %d guests, infants not allowed.', 'hostifybooking'), $person_capacity);
                            }

                            if (!$children_allowed) {
                                $max_guests_text .= ' ' . __('Children aren\'t allowed', 'hostifybooking');
                                if (!empty($children_details)) {
                                    $max_guests_text .= ' (' . esc_html($children_details) . ').';
                                } else {
                                    $max_guests_text .= '.';
                                }
                            }

                            // Optionally add pets info if needed, mirroring guests-block exactly
                            // if ($pets_allowed) {
                            //     $max_guests_text .= ' ' . __('Pets allowed.', 'hostifybooking');
                            // } else {
                            //     $max_guests_text .= ' ' . __('Pets aren\'t allowed.', 'hostifybooking');
                            // }
                        } else {
                             // Fallback if data is missing
                             $max_guests_text = sprintf(__('Maximum %d guests.', 'hostifybooking'), 1);
                        }
                        echo esc_html($max_guests_text);
                        ?>
                    </p>
                </div>
            </div>

            <div class="space-y-2">
                <label for="hfy-inquiry-discountCode" class="hfy-label flex items-center gap-1">
                    <!-- Replace with Tag icon SVG -->
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.53 0 1.04.21 1.41.59l7 7a2 2 0 010 2.83l-5 5a2 2 0 01-2.83 0l-7-7A2 2 0 013 8V5a2 2 0 012-2h2z"></path></svg>
                    <?php _e('Discount Code', 'hostifybooking'); ?>
                </label>
                <input id="hfy-inquiry-discountCode" name="discountCode" type="text" class="hfy-input" placeholder="<?php esc_attr_e('Enter discount code (if available)', 'hostifybooking'); ?>" />
            </div>

            <div class="space-y-2">
                <label for="hfy-inquiry-comment" class="hfy-label flex items-center gap-1">
                    <!-- Replace with MessageSquare icon SVG -->
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    <?php _e('Question or Comment', 'hostifybooking'); ?>
                </label>
                <textarea id="hfy-inquiry-comment" name="comment" class="hfy-textarea" placeholder="<?php esc_attr_e('Any special requests or questions?', 'hostifybooking'); ?>" rows="4"></textarea>
            </div>

            <div class="space-y-4">
                <!-- reCAPTCHA -->
                <?php if (!empty($settings->api_key_captcha)): ?>
                <?php 
                $recaptcha_version = defined('HFY_GOOGLE_RECAPTCHA_VERSION') ? HFY_GOOGLE_RECAPTCHA_VERSION : 'v2';
                if ($recaptcha_version === 'v2'): 
                ?>
                <div class="flex justify-center">
                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($settings->api_key_captcha); ?>" data-callback="inquiryV2NotARobot"></div>
                    <div id="g-recaptcha-error-v2"></div>
                </div>
                <?php else: ?>
                <!-- reCAPTCHA v3 is invisible - no widget needed -->
                <div id="g-recaptcha-error-v2" style="display: none;"></div>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="hfy-modal-footer sm:justify-between">
                 <div class="hfy-inquiry-response text-sm" style="display: none;"></div>
                <button type="button" rel="modal:close" class="hfy-modal-cancel hfy-btn hfy-btn-outline">
                    <?php _e('Cancel', 'hostifybooking'); ?>
                </button>
                <button type="submit" class="hfy-btn hfy-btn-primary bg-blue-600 hover:bg-blue-700">
                    <?php _e('Send Inquiry', 'hostifybooking'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
