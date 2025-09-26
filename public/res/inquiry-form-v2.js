/**
 * JavaScript for handling the v2 inquiry form modal
 * Implementing the v3 calendar (datepicker) functionality
 */
jQuery(document).ready(function($) {
    // console.log('inquiry-form-v2.js loaded (debug)');
    
    // Check if new inquiry form is enabled
    if (typeof window.HFY_USE_NEW_INQUIRY_FORM === 'undefined' || !window.HFY_USE_NEW_INQUIRY_FORM) {
        // console.log('New inquiry form is disabled, skipping V2 initialization');
        return;
    }
    
    // console.log('New inquiry form is enabled, initializing V2 modal');

    // Initialize the modal
    function initInquiryModalV2() {
        // Modal elements
        const $modal = $('#hfy-direct-inquiry-modal-v2');
        const $form = $('#hfy-direct-inquiry-form-v2');
        const $modalClose = $modal.find('.hfy-modal-close, .hfy-modal-cancel');
        const $responseArea = $modal.find('.hfy-inquiry-response');
        
        // Calendar elements - check which type is being used
        const isV3Calendar = typeof window.HFY_USE_V3_CALENDAR !== 'undefined' && window.HFY_USE_V3_CALENDAR;
        
        let $datepickerInput, $startDateInput, $endDateInput;
        
        if (isV3Calendar) {
            // V3 Calendar (Datepicker) - Single input
            $datepickerInput = $modal.find('#inquiry-hotel-datepicker');
            $startDateInput = $modal.find('input[name="start_date"]');
            $endDateInput = $modal.find('input[name="end_date"]');
        } else {
            // V2 Calendar (Old Calendar) - Two separate inputs
            $startDateInput = $modal.find('input[name="start_date"]');
            $endDateInput = $modal.find('input[name="end_date"]');
            // No datepicker input for V2 calendar
        }
        
        const $resetDateLink = $modal.find('.reset-date');

        // Guest elements
        const $guestButtons = $modal.find('.hfy-guest-btn');
        const $guestCounts = $modal.find('.hfy-guest-count');
        const $guestInputs = $modal.find('input[name="adults"], input[name="children"], input[name="infants"]');
        const $maxGuests = $('#hfy-max-guests-v2');
        const $guestSummary = $('#hfy-guest-summary-v2');
        const $guestWarning = $('#hfy-guest-warning-v2');
        
        // Variable to store the inquiry datepicker instance
        let inquiryDatepicker = null;
        
        // Override the default click handler for the inquiry button
        // Remove default handler by intercepting the event
        $(document).off('click', '.direct-inquiry-modal-open');
        
        // Add our custom handler
        $(document).on('click', '.direct-inquiry-modal-open', function(e){
            // console.log('V2 Inquiry modal open button clicked');
            e.preventDefault();
            e.stopPropagation();
            
            $modal.find('.thx').hide();
            $form.show(); // Keep showing form content if hidden on success
            // Use the hfymodal function to show, assuming it handles blocker and body class
            if ($.isFunction($.fn.hfymodal)) {
                 $modal.hfymodal('show');
            } else {
                 console.error('hfymodal function not found. Falling back to basic show.');
                 $modal.show();
                 $('body').addClass('hfy-modal-open'); // Fallback body class
            }

            // Delay calendar initialization to ensure modal and inputs are in DOM
            setTimeout(function() {
                // console.log('Calendar initialization check:', {
                //     isV3Calendar: isV3Calendar,
                //     datepickerInputExists: isV3Calendar ? ($datepickerInput && $datepickerInput.length > 0) : false,
                //     shouldInitDatepicker: isV3Calendar && $datepickerInput && $datepickerInput.length && $datepickerInput[0],
                //     shouldInitOldCalendar: !isV3Calendar
                // });
                
                // Initialize datepicker only if V3 calendar is enabled
                if (isV3Calendar && $datepickerInput && $datepickerInput.length && $datepickerInput[0]) {
                    initInquiryDatepicker();
                } else if (!isV3Calendar) {
                    // Initialize old calendar (V2) if V3 calendar is disabled
                    initInquiryOldCalendar();
                }

                // Transfer dates from main booking form if they exist
                const mainStartDate = $('.hfy-listing-booking-form input[name="start_date"]').val();
                const mainEndDate = $('.hfy-listing-booking-form input[name="end_date"]').val();

                // Also check URL parameters directly
                const urlParams = new URLSearchParams(window.location.search);
                const urlStartDate = urlParams.get('start_date');
                const urlEndDate = urlParams.get('end_date');

                // console.log('Checking for pre-selected dates from main booking form:', {
                //     mainStartDate: mainStartDate,
                //     mainEndDate: mainEndDate,
                //     urlStartDate: urlStartDate,
                //     urlEndDate: urlEndDate,
                //     isV3Calendar: isV3Calendar,
                //     currentUrl: window.location.href
                // });

                // Use URL parameters if main booking form doesn't have dates
                const effectiveStartDate = mainStartDate || urlStartDate;
                const effectiveEndDate = mainEndDate || urlEndDate;

                if (effectiveStartDate && effectiveEndDate) {
                    // console.log('Using effective dates for inquiry modal:', {
                    //     effectiveStartDate: effectiveStartDate,
                    //     effectiveEndDate: effectiveEndDate
                    // });

                    // Set the hidden inputs in the modal
                    $startDateInput.val(effectiveStartDate);
                    $endDateInput.val(effectiveEndDate);

                    if (isV3Calendar) {
                        // Update the visible datepicker input in the modal
                        const dateValue = `${effectiveStartDate} - ${effectiveEndDate}`;
                        $datepickerInput.val(dateValue);

                        // Update the datepicker instance if initialized
                        if (inquiryDatepicker) {
                            inquiryDatepicker.close(); // Close if open
                            // Set the range in the datepicker instance
                            try {
                                inquiryDatepicker.setRange(effectiveStartDate, effectiveEndDate);
                            } catch (e) {
                                console.warn('Error setting range on inquiry datepicker:', e);
                            }
                        }
                    } else {
                        // For V2 calendar, update the visible calentim inputs
                        const $modalCalentimStart = $modal.find('input.calentim-start');
                        const $modalCalentimEnd = $modal.find('input.calentim-end');
                        
                        // console.log('Updating V2 calendar inputs in modal:', {
                        //     modalCalentimStartFound: $modalCalentimStart.length,
                        //     modalCalentimEndFound: $modalCalentimEnd.length,
                        //     startDate: effectiveStartDate,
                        //     endDate: effectiveEndDate
                        // });
                        
                        if ($modalCalentimStart.length) {
                            $modalCalentimStart.val(effectiveStartDate);
                            // console.log('Updated modal calentim-start input:', effectiveStartDate);
                        }
                        if ($modalCalentimEnd.length) {
                            $modalCalentimEnd.val(effectiveEndDate);
                            // console.log('Updated modal calentim-end input:', effectiveEndDate);
                        }
                    }

                    // Calculate and update nights
                    updateNightsInput(effectiveStartDate, effectiveEndDate);
                } else {
                     // Clear dates if main form has none selected
                    $startDateInput.val('');
                    $endDateInput.val('');
                    if (isV3Calendar) {
                        $datepickerInput.val('');
                        if (inquiryDatepicker) {
                            inquiryDatepicker.clear();
                        }
                    } else {
                        // For V2 calendar, clear the visible inputs
                        $modal.find('input[name="start_date"]').val('');
                        $modal.find('input[name="end_date"]').val('');
                    }
                    updateNightsInput(null, null); // Clear nights input
                }

                // Transfer guest counts if they exist
                const adults = $('.listing-price .guests').val() || 1;
                $('#hfy-inquiry-adults-v2').val(adults);
                $('.hfy-guest-count[data-type="adults"]').text(adults);
                updateGuestSummary();
                
                // Log for debugging
                // console.log('Inquiry form modal opened, datepicker initialized:', inquiryDatepicker);
            }, 100); // 100ms delay to ensure modal/input is in DOM
        });
        
        // Close modal - Reverted to manual hide, but hide blocker instead of removing
        $modalClose.on('click', function() {
            // console.log('Close button clicked.'); // Add log
            
            // Close datepicker if open
            if (inquiryDatepicker) {
                try {
                    inquiryDatepicker.close();
                } catch (e) {
                    console.warn('Error closing datepicker:', e);
                }
            }
            
            $modal.hide(); // Hide the modal div itself
            // Hide the blocker instead of removing it
            $('.hfy-modal-blocker.current').hide();
            // It's possible hfymodal('show') adds/removes the 'current' class,
            // or maybe we need to remove 'current' here? Let's try just hiding first.

            $('body').removeClass('hfy-modal-open'); // Remove body class

            // Reset visual state
            $responseArea.hide().text(''); // Clear response area
            $modal.removeClass('hfy-inquiry-success'); // Remove success class
            $modal.find('.hfy-modal-header').show(); // Ensure header is visible
            $form.show(); // Ensure form is visible for next open
            $modal.find('.thx').hide(); // Hide thank you message
        });
        
        // Initialize the v3 datepicker specifically for the inquiry form
        function initInquiryDatepicker() {
            // console.log('Initializing inquiry datepicker for V2 modal');
            // console.log('Datepicker input element:', $datepickerInput[0]);
            // console.log('Datepicker input length:', $datepickerInput.length);
            
            // Check if HotelDatepicker exists
            if (typeof HotelDatepicker !== 'function') {
                console.error('HotelDatepicker not found! Make sure datepicker.js is loaded.');
                return;
            }

            // Destroy existing instance if it exists
            if (inquiryDatepicker) {
                try {
                    inquiryDatepicker.destroy();
                } catch (e) {
                    console.warn('Error destroying existing datepicker:', e);
                }
                inquiryDatepicker = null;
            }

            // Process disabled dates from global variable - reusing from datepicker.js
            var formattedDisabledDates = [];
            if (typeof calendarDisabledDates !== 'undefined' && Array.isArray(calendarDisabledDates)) {
                calendarDisabledDates.forEach(function(dateItem) {
                    if (typeof dateItem === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(dateItem)) {
                        formattedDisabledDates.push(dateItem);
                    } else if (dateItem instanceof Date) {
                        try {
                            formattedDisabledDates.push(fecha.format(dateItem, 'YYYY-MM-DD'));
                        } catch (e) { console.warn('Could not format Date object from calendarDisabledDates:', dateItem, e); }
                    } else if (typeof dateItem === 'object' && dateItem !== null && dateItem.start && dateItem.end && typeof dateItem.start === 'string' && typeof dateItem.end === 'string') {
                        try {
                            var rangeStartDate = fecha.parse(dateItem.start, 'YYYY-MM-DD');
                            var rangeEndDate = fecha.parse(dateItem.end, 'YYYY-MM-DD');
                            if (rangeStartDate && rangeEndDate && rangeStartDate <= rangeEndDate) {
                                var currentDate = new Date(rangeStartDate);
                                while (currentDate <= rangeEndDate) {
                                    formattedDisabledDates.push(fecha.format(currentDate, 'YYYY-MM-DD'));
                                    currentDate.setDate(currentDate.getDate() + 1);
                                }
                            } else { console.warn('Invalid date range object in calendarDisabledDates:', dateItem); }
                        } catch (e) { console.warn('Could not parse date range object from calendarDisabledDates:', dateItem, e); }
                    } else if (typeof dateItem === 'object' && dateItem !== null && typeof dateItem.format === 'function') {
                        try {
                            formattedDisabledDates.push(dateItem.format('YYYY-MM-DD'));
                        } catch (e) { console.warn('Could not format moment-like object from calendarDisabledDates:', dateItem, e); }
                    } else { console.warn('Ignoring invalid item in calendarDisabledDates:', dateItem); }
                });
            }

            // Get default minNights from global variable
            var defaultMinNights = (typeof minNights !== 'undefined' && Number.isInteger(parseInt(minNights)) && parseInt(minNights) > 0) ? parseInt(minNights) : 1;

            // Process disabled days of week from global variable (hfyArgs)
            var disabledDaysOfWeek = (typeof hfyArgs !== 'undefined' && Array.isArray(hfyArgs.disabledWeekDays)) ? hfyArgs.disabledWeekDays : [];

            // Process no check-in/check-out days of week from global variable (hfyArgs)
            var noCheckInDaysOfWeek = (typeof hfyArgs !== 'undefined' && Array.isArray(hfyArgs.daysOfWeekDisabled)) ? hfyArgs.daysOfWeekDisabled : [];
            var noCheckOutDaysOfWeek = (typeof hfyArgs !== 'undefined' && Array.isArray(hfyArgs.daysOfWeekDisabled)) ? hfyArgs.daysOfWeekDisabled : [];

            // Process specific no check-in/check-out dates from global variables
            var noCheckInDates = (typeof calendarOverInDays !== 'undefined' && Array.isArray(calendarOverInDays)) ? calendarOverInDays.filter(d => typeof d === 'string' && d !== '') : [];
            var noCheckOutDates = (typeof calendarOverOutDays !== 'undefined' && Array.isArray(calendarOverOutDays)) ? calendarOverOutDays.filter(d => typeof d === 'string' && d !== '') : [];

            // Get maxNights from global variable
            var defaultMaxNights = (typeof maxNights !== 'undefined' && Number.isInteger(parseInt(maxNights)) && parseInt(maxNights) >= 0) ? parseInt(maxNights) : 0;

            // Basic mobile check
            var isMob = window.innerWidth <= 768;

            // Define options for the datepicker
            var options = {
                showTopbar: false,
                clearButton: false,
                format: 'YYYY-MM-DD',
                startOfWeek: 'monday',
                minNights: defaultMinNights,
                maxNights: defaultMaxNights,
                selectForward: true,
                autoClose: true,
                moveBothMonths: true,
                disabledDates: formattedDisabledDates,
                enableCheckout: true,
                disabledDaysOfWeek: disabledDaysOfWeek,
                noCheckInDaysOfWeek: noCheckInDaysOfWeek,
                noCheckOutDaysOfWeek: noCheckOutDaysOfWeek,
                noCheckInDates: noCheckInDates,
                noCheckOutDates: noCheckOutDates,
                animationSpeed: '.3s',
                calendarCustomStay: typeof calendarCustomStay !== 'undefined' ? calendarCustomStay : [],
                
                // Mobile-specific options
                mobileMonthsToShow: 6,
                mobileMonthsToLoad: 24,
                
                onDayClick: function(event, date) {
                    // This will handle the first date click
                },
                onSelectRange: function() {
                    // This will handle when a range is selected
                    var dateValue = this.getValue(); // 'this' refers to the datepicker instance
                    if (!dateValue) return;

                    var dates = dateValue.split(' - ');
                    var startDateStr = dates[0];
                    var endDateStr = dates.length > 1 ? dates[1] : null;

                    // Update hidden inputs for inquiry form
                    if (startDateStr) {
                        $startDateInput.val(startDateStr);
                    }
                    if (endDateStr) {
                        $endDateInput.val(endDateStr);
                    }

                    // Calculate and update nights input
                    updateNightsInput(startDateStr, endDateStr);

                    // Also update the main booking form fields to keep them in sync
                    var $mainStartDateInput = $('.hfy-listing-booking-form input[name="start_date"]');
                    var $mainEndDateInput = $('.hfy-listing-booking-form input[name="end_date"]');
                    var $mainDatepickerInput = $('.hfy-listing-booking-form #hotel-datepicker'); // Assuming main form also uses #hotel-datepicker

                    if ($mainStartDateInput.length) {
                        $mainStartDateInput.val(startDateStr);
                    }
                    if ($mainEndDateInput.length) {
                        $mainEndDateInput.val(endDateStr);
                    }
                    // Update the visible input in the main form as well
                    if ($mainDatepickerInput.length && startDateStr && endDateStr) {
                         $mainDatepickerInput.val(`${startDateStr} - ${endDateStr}`);
                         // Trigger change event if needed for other scripts listening on the main form
                         $mainDatepickerInput.trigger('change');
                    }

                    // Update prices if the function exists
                    if (typeof window.doUpdatePriceBlock === 'function') {
                        window.doUpdatePriceBlock();
                    }
                }
            };

            // Initialize the datepicker on the modal's input
            // console.log('Creating new HotelDatepicker instance for V2 modal');
            inquiryDatepicker = new HotelDatepicker($datepickerInput[0], options);
            // console.log('HotelDatepicker instance created:', inquiryDatepicker);
            
            // Add click and focus handlers to ensure datepicker opens
            $datepickerInput.off('click.inquiryDatepicker focus.inquiryDatepicker').on('click.inquiryDatepicker focus.inquiryDatepicker', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // console.log('Inquiry datepicker input ' + e.type + 'ed, opening datepicker');
                // console.log('Datepicker instance available:', !!inquiryDatepicker);
                if (inquiryDatepicker) {
                    // console.log('Attempting to open datepicker...');
                    inquiryDatepicker.open();
                } else {
                    console.error('Datepicker instance is null!');
                }
            });
            
            // console.log('Inquiry datepicker initialized successfully for V2 modal');
        }

        // Initialize the old calendar (V2) for the inquiry form
        function initInquiryOldCalendar() {
            // console.log('Initializing old calendar (V2) for inquiry modal');
            
            // Check if calentim plugin is available
            if (typeof $.fn.calentim !== 'function') {
                console.error('Calentim plugin not found! Make sure calentim.js is loaded.');
                return;
            }
            
            // Check if moment.js is available
            if (typeof moment !== 'function') {
                console.error('Moment.js not found! Make sure moment.js is loaded.');
                return;
            }

            // Get the start and end date inputs by their calentim classes
            const $startInput = $modal.find('input.calentim-start');
            const $endInput = $modal.find('input.calentim-end');
            
            // console.log('Found old calendar inputs:', {
            //     startInput: $startInput.length,
            //     endInput: $endInput.length,
            //     startInputElement: $startInput[0],
            //     endInputElement: $endInput[0]
            // });

            if (!$startInput.length || !$endInput.length) {
                console.error('Old calendar inputs not found in inquiry modal');
                return;
            }
            
            // Check if inputs have the correct classes
            // console.log('Input classes:', {
            //     startClasses: $startInput.attr('class'),
            //     endClasses: $endInput.attr('class')
            // });

            // Basic mobile check
            const isMob = window.innerWidth <= 768;
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);

            // Get disabled dates from global variables
            const calDisDates = (typeof calendarDisabledDates !== 'undefined' && Array.isArray(calendarDisabledDates)) ? calendarDisabledDates : [];
            const minNightsValue = (typeof minNights !== 'undefined' && Number.isInteger(parseInt(minNights)) && parseInt(minNights) > 0) ? parseInt(minNights) : 1;
            
            // Check global variables
            // console.log('Global variables check:', {
            //     hfyStartOfWeek: typeof hfyStartOfWeek !== 'undefined' ? hfyStartOfWeek : 'undefined',
            //     hfySelectedLang: typeof hfySelectedLang !== 'undefined' ? hfySelectedLang : 'undefined',
            //     hfyDF: typeof hfyDF !== 'undefined' ? hfyDF : 'undefined',
            //     calDisDates: calDisDates.length,
            //     isMob: isMob,
            //     isIOS: isIOS
            // });

            // Initialize start date calendar
            // console.log('Initializing start date calendar with calentim...');
            $startInput.calentim({
                startOnMonday: typeof hfyStartOfWeek !== 'undefined' ? hfyStartOfWeek === 1 : false,
                locale: typeof hfySelectedLang !== 'undefined' ? hfySelectedLang : 'en',
                enableMonthSwitcher: false,
                enableYearSwitcher: false,
                showHeader: false,
                showFooter: false,
                showTimePickers: false,
                calendarCount: isMob ? 24 : 2,
                isMobile: isMob,
                isIOS: isIOS,
                format: typeof hfyDF !== 'undefined' ? hfyDF : 'L',
                showOn: 'bottom',
                arrowOn: 'center',
                autoAlign: true,
                oneCalendarWidth: 280,
                continuous: true,
                minDate: moment().startOf('day'),
                disabledRanges: calDisDates,
                autoCloseOnSelect: true, // Always auto-close on select
                onaftershow: function(instance) {
                    // console.log('Inquiry start calendar shown');
                },
                onafterhide: function() {
                    // console.log('Inquiry start calendar hidden');
                },
                oninit: function(instance) {
                    // console.log('Inquiry start calendar initialized successfully');
                    // console.log('Start calendar instance:', instance);
                    // Store reference for later use
                    window.inquiryCalentimStart = instance;
                },
                onfirstselect: function(instance, start) {
                    // console.log('Inquiry start date selected:', start.format('YYYY-MM-DD'));
                    // Update hidden inputs
                    $startDateInput.val(start.format('YYYY-MM-DD'));
                    
                    // Update main booking form start date
                    const $mainStartDateInput = $('.hfy-listing-booking-form input[name="start_date"]');
                    if ($mainStartDateInput.length) {
                        $mainStartDateInput.val(start.format('YYYY-MM-DD'));
                    }
                    
                    // Close this calendar
                    instance.hideDropdown();
                    // Show end date calendar after a short delay
                    setTimeout(function() {
                        if (window.inquiryCalentimEnd) {
                            window.inquiryCalentimEnd.showDropdown();
                        }
                    }, 100);
                }
            });

            // Initialize end date calendar
            // console.log('Initializing end date calendar with calentim...');
            $endInput.calentim({
                startOnMonday: typeof hfyStartOfWeek !== 'undefined' ? hfyStartOfWeek === 1 : false,
                locale: typeof hfySelectedLang !== 'undefined' ? hfySelectedLang : 'en',
                enableMonthSwitcher: false,
                enableYearSwitcher: false,
                showHeader: false,
                showFooter: false,
                showTimePickers: false,
                calendarCount: isMob ? 24 : 2,
                isMobile: isMob,
                isIOS: isIOS,
                format: typeof hfyDF !== 'undefined' ? hfyDF : 'L',
                showOn: 'bottom',
                arrowOn: 'center',
                autoAlign: true,
                oneCalendarWidth: 280,
                continuous: true,
                minDate: moment().startOf('day'),
                disabledRanges: calDisDates,
                autoCloseOnSelect: true, // Always auto-close on select
                onaftershow: function(instance) {
                    // console.log('Inquiry end calendar shown');
                },
                onafterhide: function() {
                    // console.log('Inquiry end calendar hidden');
                },
                oninit: function(instance) {
                    // console.log('Inquiry end calendar initialized successfully');
                    // console.log('End calendar instance:', instance);
                    // Store reference for later use
                    window.inquiryCalentimEnd = instance;
                },
                onfirstselect: function(instance, end) {
                    // console.log('Inquiry end date selected:', end.format('YYYY-MM-DD'));
                    // Update hidden inputs
                    $endDateInput.val(end.format('YYYY-MM-DD'));
                    
                    // Close this calendar
                    instance.hideDropdown();
                    
                    // Calculate and update nights
                    const startDate = $startDateInput.val();
                    if (startDate) {
                        updateNightsInput(startDate, end.format('YYYY-MM-DD'));
                    }
                    
                    // Update main booking form
                    const $mainStartDateInput = $('.hfy-listing-booking-form input[name="start_date"]');
                    const $mainEndDateInput = $('.hfy-listing-booking-form input[name="end_date"]');
                    
                    // For old calendar (V2), update the visible calentim inputs
                    const $mainCalentimStart = $('.hfy-listing-booking-form input.calentim-start');
                    const $mainCalentimEnd = $('.hfy-listing-booking-form input.calentim-end');
                    const $mainCalentimDates = $('.hfy-listing-booking-form input.calentim-dates');
                    
                    // For new calendar (V3), update the datepicker input
                    let $mainDatepickerInput = $('#hotel-datepicker'); // Try global selector first
                    if (!$mainDatepickerInput.length) {
                        $mainDatepickerInput = $('.hfy-listing-booking-form #hotel-datepicker');
                    }
                    
                    // console.log('Main booking form elements found:', {
                    //     mainStartDateInput: $mainStartDateInput.length,
                    //     mainEndDateInput: $mainEndDateInput.length,
                    //     mainCalentimStart: $mainCalentimStart.length,
                    //     mainCalentimEnd: $mainCalentimEnd.length,
                    //     mainCalentimDates: $mainCalentimDates.length,
                    //     mainDatepickerInput: $mainDatepickerInput.length,
                    //     startDate: startDate,
                    //     endDate: end.format('YYYY-MM-DD')
                    // });
                    
                    // Only update main form inputs if we're actually in the inquiry modal context
                    if ($mainStartDateInput.length && $('.direct-inquiry-modal').is(':visible')) {
                        $mainStartDateInput.val(startDate);
                        // console.log('Updated main start date input:', startDate);
                    } else if (!$('.direct-inquiry-modal').is(':visible')) {
                        // console.log('Skipping main form update - inquiry modal not visible');
                    } else {
                        console.warn('Main start date input not found');
                    }
                    
                    if ($mainEndDateInput.length && $('.direct-inquiry-modal').is(':visible')) {
                        $mainEndDateInput.val(end.format('YYYY-MM-DD'));
                        // console.log('Updated main end date input:', end.format('YYYY-MM-DD'));
                    } else if (!$('.direct-inquiry-modal').is(':visible')) {
                        // console.log('Skipping main form update - inquiry modal not visible');
                    } else {
                        console.warn('Main end date input not found');
                    }
                    
                    // Update the visible calentim inputs in main form (for old calendar)
                    // Only update if we're actually in the inquiry modal context
                    if ($mainCalentimStart.length && startDate && $('.direct-inquiry-modal').is(':visible')) {
                        $mainCalentimStart.val(startDate);
                        // console.log('Updated main calentim start input:', startDate);
                    }
                    
                    if ($mainCalentimEnd.length && end.format('YYYY-MM-DD') && $('.direct-inquiry-modal').is(':visible')) {
                        $mainCalentimEnd.val(end.format('YYYY-MM-DD'));
                        // console.log('Updated main calentim end input:', end.format('YYYY-MM-DD'));
                    }
                    
                    // Update the combined calentim-dates input
                    // Only update if we're actually in the inquiry modal context
                    // console.log('Inquiry form checking main calentim dates:', {
                    //     hasMainCalentimDates: $mainCalentimDates.length > 0,
                    //     hasStartDate: !!startDate,
                    //     hasEndDate: !!end.format('YYYY-MM-DD'),
                    //     modalVisible: $('.direct-inquiry-modal').is(':visible'),
                    //     currentValue: $mainCalentimDates.val()
                    // });
                    
                    if ($mainCalentimDates.length && startDate && end.format('YYYY-MM-DD') && $('.direct-inquiry-modal').is(':visible')) {
                        const displayFormat = typeof hfyDF !== 'undefined' ? hfyDF : 'YYYY-MM-DD';
                        const startMoment = moment(startDate, 'YYYY-MM-DD');
                        const dateRange = `${startMoment.format(displayFormat)} - ${end.format(displayFormat)}`;
                        $mainCalentimDates.val(dateRange);
                        // console.log('Updated main calentim-dates input:', dateRange);
                    }
                    
                    // Update the visible datepicker input in main form (for new calendar)
                    // Only update if we're actually in the inquiry modal context
                    if ($mainDatepickerInput.length && startDate && end.format('YYYY-MM-DD') && $('.direct-inquiry-modal').is(':visible')) {
                        const dateRange = `${startDate} - ${end.format('YYYY-MM-DD')}`;
                        $mainDatepickerInput.val(dateRange);
                        $mainDatepickerInput.trigger('change');
                        // console.log('Updated main datepicker input:', dateRange);
                    }
                    
                    // Update URL and prices by calling available functions
                    // console.log('Checking for submitForm function:', typeof submitForm);
                    // console.log('Checking for doUpdatePriceBlock function:', typeof window.doUpdatePriceBlock);
                    
                    // Try to update URL manually first
                    try {
                        const startDateVal = $startDateInput.val();
                        const endDateVal = end.format('YYYY-MM-DD');
                        
                        if (startDateVal && endDateVal) {
                            const href = new URL(window.location.href);
                            href.searchParams.set('start_date', startDateVal);
                            href.searchParams.set('end_date', endDateVal);
                            window.history.replaceState('', '', href.toString());
                            // console.log('Updated URL manually:', href.toString());
                            
                            // Force browser to show the updated URL
                            setTimeout(function() {
                                window.history.replaceState('', '', href.toString());
                            }, 100);
                        }
                    } catch (e) {
                        console.warn('Could not update URL manually:', e);
                    }
                    
                    // Try to call available functions
                    if (typeof submitForm === 'function') {
                        // console.log('Calling submitForm()');
                        submitForm();
                    } else if (typeof window.doUpdatePriceBlock === 'function') {
                        // console.log('Calling doUpdatePriceBlock()');
                        window.doUpdatePriceBlock();
                    } else {
                        console.warn('No form submission method available');
                    }
                }
            });

            // console.log('Old calendar (V2) initialized successfully for inquiry modal');
        }

        // Reset Dates functionality
        $resetDateLink.on('click', function(e) {
            e.preventDefault();
            
            if (isV3Calendar) {
                $datepickerInput.val('');
                if (inquiryDatepicker) {
                    inquiryDatepicker.clear();
                }
            } else {
                // For V2 calendar, clear the visible inputs and reset calentim instances
                $modal.find('input[name="start_date"]').val('');
                $modal.find('input[name="end_date"]').val('');
                
                // Reset calentim instances if they exist
                if (window.inquiryCalentimStart) {
                    window.inquiryCalentimStart.config.startDate = null;
                    window.inquiryCalentimStart.$elem.val('');
                }
                if (window.inquiryCalentimEnd) {
                    window.inquiryCalentimEnd.config.endDate = null;
                    window.inquiryCalentimEnd.$elem.val('');
                }
            }
            
            $startDateInput.val('');
            $endDateInput.val('');
            updateNightsInput(null, null); // Clear nights input

            // Optionally clear main form dates as well
            var $mainStartDateInput = $('.hfy-listing-booking-form input[name="start_date"]');
            var $mainEndDateInput = $('.hfy-listing-booking-form input[name="end_date"]');
            var $mainDatepickerInput = $('.hfy-listing-booking-form #hotel-datepicker');
            $mainStartDateInput.val('');
            $mainEndDateInput.val('');
            if ($mainDatepickerInput.length) {
                $mainDatepickerInput.val('');
            }
             if (typeof window.doUpdatePriceBlock === 'function') {
                window.doUpdatePriceBlock(); // Update price block after clearing dates
            }
        });

        // Function to calculate and update nights (Corrected)
        function updateNightsInput(startDateStr, endDateStr) {
            const $nightsInput = $('#hfy-inquiry-nights-v2');
            if (!$nightsInput.length) {
                // console.log('Nights input (#hfy-inquiry-nights-v2) not found.');
                return;
            }

            // console.log('Attempting to update nights input. Start:', startDateStr, 'End:', endDateStr);

            if (startDateStr && endDateStr && typeof moment === 'function') { // Ensure moment.js is available
                try {
                    const startDate = moment(startDateStr, 'YYYY-MM-DD');
                    const endDate = moment(endDateStr, 'YYYY-MM-DD');

                    // Check if dates are valid moment objects and end date is strictly after start date
                    if (startDate.isValid() && endDate.isValid() && endDate.isAfter(startDate)) {
                        const nights = endDate.diff(startDate, 'days'); // Use moment's diff function
                        // console.log('Calculated nights:', nights);
                        $nightsInput.val(nights > 0 ? nights : 1); // Ensure nights is at least 1
                    } else {
                        if (!startDate.isValid()) console.warn('Start date is invalid:', startDateStr);
                        if (!endDate.isValid()) console.warn('End date is invalid:', endDateStr);
                        if (startDate.isValid() && endDate.isValid() && !endDate.isAfter(startDate)) console.warn('End date is not after start date.');
                        // console.log('Invalid date range. Defaulting nights to 1.');
                        $nightsInput.val(1); // Default to 1 if dates are invalid or same day
                    }
                } catch (e) {
                    console.error('Error calculating nights with moment.js:', e);
                    $nightsInput.val(1); // Default on error
                }
            } else {
                 if (typeof moment !== 'function') {
                    console.warn('moment.js is not available for night calculation.');
                 }
                 if (!startDateStr || !endDateStr) {
                    // console.log('Start or end date string is missing. Defaulting nights to 1.');
                 }
                $nightsInput.val(1); // Default to 1 if dates are cleared or moment is missing
            }
        }

        // Guest counter functionality
$guestButtons.on('click', function(e) {
    e.preventDefault();
    
    const $btn     = $(this);
    const type     = $btn.data('type');                          // "adults" or "children"
    const isPlus   = $btn.hasClass('hfy-guest-btn-plus');
    const max      = parseInt($maxGuests.val(), 10);

    // Read current totals BEFORE we change anything:
    const adults   = parseInt($modal.find('.hfy-guest-count[data-type="adults"]').text(), 10);
    const children = parseInt($modal.find('.hfy-guest-count[data-type="children"]').text(), 10);
    const total    = adults + children;

    // **1) Prevent overshoot: bail out if we’re already at or above the max**
    if (isPlus && total >= max) {
      return;
    }

    // 2) Grab the single counter you actually want to inc/dec:
    const $count = $modal.find(`.hfy-guest-count[data-type="${type}"]`);
    const $input = $modal.find(`#hfy-inquiry-${type}-v2`);
    let cnt     = parseInt($count.text(), 10);

    // 3) Increment or decrement (never let adults drop below 1, kids below 0):
    if (isPlus) {
      cnt++;
    } else if (cnt > (type === 'adults' ? 1 : 0)) {
      cnt--;
    }

    // 4) Write it back:
    $count.text(cnt);
    $input.val(cnt);

    // 5) Disable the “–” if you’re already at the minimum:
    $modal
      .find(`.hfy-guest-btn-minus[data-type="${type}"]`)
      .prop('disabled', (type === 'adults' && cnt === 1) || (type !== 'adults' && cnt === 0));

    // 6) Refresh summary & plus/minus state:
    updateGuestSummary();
});
        
       // Summary + disabling of the “+” buttons
function updateGuestSummary() {
    const adults       = parseInt($modal.find('.hfy-guest-count[data-type="adults"]').text(), 10);
    const children     = parseInt($modal.find('.hfy-guest-count[data-type="children"]').text(), 10);
    const maxGuestsVal = parseInt($maxGuests.val(), 10);
    const totalGuests  = adults + children;

    // Update the display
    $guestSummary.text(`${totalGuests}/${maxGuestsVal} guests`);

    if (totalGuests >= maxGuestsVal) {
        // at max or over: show warning & disable all “+”
        $guestWarning.show();
        $modal
         .find('.hfy-guest-btn-plus[data-type="adults"], .hfy-guest-btn-plus[data-type="children"]')
         .prop('disabled', true);
    } else {
        // under max: hide warning & re-enable relevant “+”
        $guestWarning.hide();
        // Always enable adults '+' if under max
        $modal.find('.hfy-guest-btn-plus[data-type="adults"]').prop('disabled', false);

        // Only enable children '+' if under max AND children are allowed
        const childrenAllowed = $modal.find('.space-y-3.border.rounded-md').data('children-allowed') === true;
        if (childrenAllowed) {
            $modal.find('.hfy-guest-btn-plus[data-type="children"]').prop('disabled', false);
        } else {
             $modal.find('.hfy-guest-btn-plus[data-type="children"]').prop('disabled', true); // Ensure it stays disabled
        }
    }
}
        
        // Form submission
        $form.on('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const $submitButton = $form.find('button[type="submit"]');
            $submitButton.prop('disabled', true).addClass('loading');
            
            // Check reCAPTCHA if it exists
            const recaptchaVersion = window.hfyRecaptchaVersion || 'v2';
            
            if (recaptchaVersion === 'v2' && typeof grecaptcha !== 'undefined' && $('#g-recaptcha-error-v2').length && $('#g-recaptcha-error-v2').is(':visible')) {
                const recaptchaResponse = grecaptcha.getResponse();
                if (recaptchaResponse.length === 0) {
                    $('#g-recaptcha-error-v2').html('<span style="color:red; display: block; margin-top: 10px;">Please verify you are not a robot.</span>');
                    $submitButton.prop('disabled', false).removeClass('loading');
                    return false;
                } else {
                    $('#g-recaptcha-error-v2').html('');
                }
            } else if (recaptchaVersion === 'v3' && typeof grecaptcha !== 'undefined') {
                // For v3, we need to execute and get token
                try {
                    grecaptcha.execute(window.hfyRecaptchaSiteKey, {action: 'inquiry_submit'}).then(function(token) {
                        if (!token) {
                            $('#g-recaptcha-error-v2').html('<span style="color:red; display: block; margin-top: 10px;">reCAPTCHA verification failed.</span>');
                            $submitButton.prop('disabled', false).removeClass('loading');
                            return false;
                        }
                        // Store token for AJAX request
                        window.hfyRecaptchaV3Token = token;
                    }).catch(function(error) {
                        console.error('reCAPTCHA v3 error:', error);
                        $('#g-recaptcha-error-v2').html('<span style="color:red; display: block; margin-top: 10px;">reCAPTCHA verification failed.</span>');
                        $submitButton.prop('disabled', false).removeClass('loading');
                        return false;
                    });
                } catch (error) {
                    console.error('reCAPTCHA v3 error:', error);
                    $('#g-recaptcha-error-v2').html('<span style="color:red; display: block; margin-top: 10px;">reCAPTCHA verification failed.</span>');
                    $submitButton.prop('disabled', false).removeClass('loading');
                    return false;
                }
            }
            
            // Get form data
            const formData = new FormData(this);
            const formObject = {};
            
            // Map the form field names to what the AJAX processor expects
            // Map form field names if necessary (adjust based on hfy_ajax_inquiry expectations)
            // NOTE: Since we added start_date and end_date inputs directly, mapping might not be needed for them.
            // Verify what 'hfy_ajax_inquiry' expects. Based on old form, it expects check_in/check_out.
            const fieldMap = {
                'firstName': 'first_name',
                'lastName': 'last_name',
                'start_date': 'check_in', // Map start_date to check_in
                'end_date': 'check_out',  // Map end_date to check_out
                'comment': 'message',
                'discountCode': 'discount_code'
                // listingId, listingName, listingNickname, nights should be picked up directly by FormData
                // Ensure 'action' and 'hfy_nonce' are included by FormData
            };

            formData.forEach(function(value, key) {
                const mappedKey = fieldMap[key] || key;
                formObject[mappedKey] = value;
            });

            // Ensure essential fields like listing_id are present
             if (!formObject.listing_id) {
                 formObject.listing_id = $modal.find('input[name="listing_id"]').val();
             }

            // Add listing name and nickname if not present (adjust selector if needed)
            if (!formObject.listingName) {
                formObject.listingName = $('.hfy-modal-title').text() || '';
            }
            if (!formObject.listingNickname) {
                formObject.listingNickname = $('.hfy-modal-title').text() || '';
            }
            
            // Log the data being sent
            // console.log('Submitting inquiry with data object:', formObject);
            // console.log('Submitting inquiry with serialized data:', $.param(formObject));

            // Handle submission
            $.ajax({
                url: hfyx.url, // Assuming hfyx.url is defined globally
                type: 'POST',
                data: {
                    action: 'inquiry', // Matches the old action
                    data: $.param(formObject), // Wrap the mapped data object, matching old structure
                    'g-recaptcha-response': (function() {
                        const recaptchaVersion = window.hfyRecaptchaVersion || 'v2';
                        if (recaptchaVersion === 'v2' && typeof grecaptcha !== 'undefined' && $('#g-recaptcha-error-v2').is(':visible')) {
                            return grecaptcha.getResponse();
                        } else if (recaptchaVersion === 'v3' && window.hfyRecaptchaV3Token) {
                            return window.hfyRecaptchaV3Token;
                        }
                        return '';
                    })()
                },
                dataType: 'json', // Expect JSON response from the server
                success: function(response) {
                    // console.log('Inquiry AJAX Success Response:', response); // Log the full success response
                    $submitButton.prop('disabled', false).removeClass('loading');

                    // Check the response structure carefully
                    if (response && response.success === true) { // Explicitly check for true
                        // console.log('Server indicated success. Applying success state.');
                        // Add success class, hide form & header, show thank you message
                        $modal.addClass('hfy-inquiry-success'); // Add class to main modal
                        $form.hide();
                        $modal.find('.hfy-modal-header').hide(); // Hide the header
                        $modal.find('.thx').show(); // Show the thank you container
                        $responseArea.hide().text('').removeClass('error success'); // Clear any previous messages
                    } else {
                        // Handle cases where success might be false, missing, or response is unexpected
                        $modal.removeClass('hfy-inquiry-success'); // Ensure success class is removed on failure
                        $modal.find('.hfy-modal-header').show(); // Ensure header is visible on failure
                        const errorMsg = (response && response.msg) ? response.msg : 'Inquiry submission failed. Please check your details and try again.';
                        console.error('Inquiry failed (Server Response):', errorMsg, 'Full Response:', response);
                        $responseArea.text(errorMsg).removeClass('success').addClass('error').show();
                    }
                },
                error: function(xhr, status, error) {
                    // Log detailed error information
                    console.error('Inquiry AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText, // Log server's raw response if available
                        xhr: xhr
                    });
                    $submitButton.prop('disabled', false).removeClass('loading');
                    $responseArea.text('A connection error occurred. Please check your internet connection and try again.').removeClass('success').addClass('error').show();
                }
            });
        });
        
        // Removed outside click handler for old container
        // Removed custom event listener 'direct-inquiry-modal-opened'
    }
    
    // Initialize the modal when document is ready
    initInquiryModalV2();
});

// --- Support for old inquiry form with new calendar (HotelDatepicker) ---
document.addEventListener('DOMContentLoaded', function() {
    // console.log('[Old Inquiry] Checking for old inquiry form elements...');
    
    // Check if new inquiry form is enabled - if so, skip old inquiry initialization
    if (typeof window.HFY_USE_NEW_INQUIRY_FORM !== 'undefined' && window.HFY_USE_NEW_INQUIRY_FORM) {
        // console.log('[Old Inquiry] New inquiry form is enabled, skipping old inquiry initialization');
        return;
    }
    
    // Check if V3 calendar is enabled - if not, skip new datepicker initialization for old form
    if (typeof window.HFY_USE_V3_CALENDAR === 'undefined' || !window.HFY_USE_V3_CALENDAR) {
        // console.log('[Old Inquiry] V3 calendar is disabled, skipping new datepicker initialization for old form');
        return;
    }
    
    // console.log('[Old Inquiry] V3 calendar is enabled, initializing new datepicker for old inquiry form');
    
    // Check if elements exist on page load
    var inquiryCheckinOnLoad = document.getElementById('inquiry_checkin');
    var inquiryCheckoutOnLoad = document.getElementById('inquiry_checkout');
    // console.log('[Old Inquiry] Elements on page load:', {
    //     inquiryCheckin: !!inquiryCheckinOnLoad,
    //     inquiryCheckout: !!inquiryCheckoutOnLoad,
    //     inquiryCheckinParent: inquiryCheckinOnLoad ? inquiryCheckinOnLoad.parentNode : null,
    //     inquiryCheckoutParent: inquiryCheckoutOnLoad ? inquiryCheckoutOnLoad.parentNode : null
    // });
    
    // Function to initialize old inquiry form datepicker
    function initOldInquiryDatepicker() {
        if (typeof HotelDatepicker !== 'function') {
            // console.log('[Old Inquiry] HotelDatepicker not available');
            return;
        }
        
        // Check for both possible structures
        var inquiryCheckin = document.getElementById('inquiry_checkin');
        var inquiryCheckout = document.getElementById('inquiry_checkout');
        var inquiryDatepicker = document.getElementById('inquiry-hotel-datepicker');
        
        // console.log('[Old Inquiry] Checking for elements:', {
        //     inquiryCheckin: !!inquiryCheckin,
        //     inquiryCheckout: !!inquiryCheckout,
        //     inquiryDatepicker: !!inquiryDatepicker
        // });
        
        // Determine which structure is being used
        var isV3Calendar = !!inquiryDatepicker;
        var isOldStructure = !!(inquiryCheckin && inquiryCheckout);
        
        // console.log('[Old Inquiry] Structure detected:', {
        //     isV3Calendar: isV3Calendar,
        //     isOldStructure: isOldStructure
        // });
        
        if (!isV3Calendar && !isOldStructure) {
            // console.log('[Old Inquiry] No date inputs found');
            return;
        }
        
        // console.log('[Old Inquiry] Found old inquiry form elements, initializing...');
        
        if (isV3Calendar) {
            // V3 Calendar structure - single input
            // console.log('[Old Inquiry] Using V3 Calendar structure');
            
            // Check if datepicker is already initialized
            if (inquiryDatepicker._hotelDatepicker) {
                // console.log('[Old Inquiry] Datepicker already initialized on V3 input');
                return;
            }
            
            // Define options for V3 structure
            var inquiryOptions = {
                showTopbar: false,
                clearButton: false,
                format: 'YYYY-MM-DD',
                startOfWeek: 'monday',
                minNights: (typeof minNights !== 'undefined' && Number.isInteger(parseInt(minNights)) && parseInt(minNights) > 0) ? parseInt(minNights) : 1,
                maxNights: (typeof maxNights !== 'undefined' && Number.isInteger(parseInt(maxNights)) && parseInt(maxNights) >= 0) ? parseInt(maxNights) : 0,
                selectForward: true,
                autoClose: true,
                moveBothMonths: true,
                disabledDates: (typeof formattedDisabledDates !== 'undefined') ? formattedDisabledDates : [],
                enableCheckout: true,
                disabledDaysOfWeek: (typeof disabledDaysOfWeek !== 'undefined') ? disabledDaysOfWeek : [],
                noCheckInDaysOfWeek: (typeof noCheckInDaysOfWeek !== 'undefined') ? noCheckInDaysOfWeek : [],
                noCheckOutDaysOfWeek: (typeof noCheckOutDaysOfWeek !== 'undefined') ? noCheckOutDaysOfWeek : [],
                noCheckInDates: (typeof noCheckInDates !== 'undefined') ? noCheckInDates : [],
                noCheckOutDates: (typeof noCheckOutDates !== 'undefined') ? noCheckOutDates : [],
                animationSpeed: '.3s',
                calendarCustomStay: (typeof calendarCustomStay !== 'undefined') ? calendarCustomStay : [],
                onSelectRange: function() {
                    // console.log('[Old Inquiry V3] onSelectRange called!');
                    var dateValue = inquiryDatepicker.value;
                    // console.log('[Old Inquiry V3] Date value:', dateValue);
                    if (!dateValue) return;
                    
                    var dates = dateValue.split(' - ');
                    var start = dates[0] || '';
                    var end = dates[1] || '';
                    
                    // Update hidden inputs
                    var $checkInInput = jQuery('input[name="check_in"]');
                    var $checkOutInput = jQuery('input[name="check_out"]');
                    if ($checkInInput.length) $checkInInput.val(start);
                    if ($checkOutInput.length) $checkOutInput.val(end);
                    
                    // Update main booking form
                    var $mainStartDateInput = jQuery('.hfy-listing-booking-form input[name="start_date"]');
                    var $mainEndDateInput = jQuery('.hfy-listing-booking-form input[name="end_date"]');
                    var $mainDatepickerInput = jQuery('#hotel-datepicker');
                    
                    if ($mainStartDateInput.length) $mainStartDateInput.val(start);
                    if ($mainEndDateInput.length) $mainEndDateInput.val(end);
                    if ($mainDatepickerInput.length && start && end) {
                        $mainDatepickerInput.val(`${start} - ${end}`);
                        $mainDatepickerInput.trigger('change');
                    }
                    
                    if (typeof window.doUpdatePriceBlock === 'function') {
                        window.doUpdatePriceBlock();
                    }
                    
                    // console.log('[Old Inquiry V3] Updated main booking form and called doUpdatePriceBlock');
                }
            };
            
            // Initialize on the V3 input directly
            var oldInquiryDatepicker = new HotelDatepicker(inquiryDatepicker, inquiryOptions);
            inquiryDatepicker._hotelDatepicker = oldInquiryDatepicker;
            window.oldInquiryDatepicker = oldInquiryDatepicker;
            
            // console.log('[Old Inquiry V3] Datepicker initialized on inquiry-hotel-datepicker');
            
        } else {
            // Old structure - two separate inputs
            // console.log('[Old Inquiry] Using old structure with two inputs');
            
            // Create a hidden input for the range picker if not already present
            let rangeInput = document.getElementById('inquiry-hotel-datepicker-old');
            if (!rangeInput) {
                rangeInput = document.createElement('input');
                rangeInput.type = 'text';
                rangeInput.id = 'inquiry-hotel-datepicker-old';
                rangeInput.style.display = 'none';
                inquiryCheckin.parentNode.appendChild(rangeInput);
                // console.log('[Old Inquiry] Created hidden range input');
            }

            // Check if datepicker is already initialized
            if (rangeInput._hotelDatepicker) {
                // console.log('[Old Inquiry] Datepicker already initialized');
                return;
            }

            // Define options for old structure
            var inquiryOptions = {
                showTopbar: false,
                clearButton: false,
                format: 'YYYY-MM-DD',
                startOfWeek: 'monday',
                minNights: (typeof minNights !== 'undefined' && Number.isInteger(parseInt(minNights)) && parseInt(minNights) > 0) ? parseInt(minNights) : 1,
                maxNights: (typeof maxNights !== 'undefined' && Number.isInteger(parseInt(maxNights)) && parseInt(maxNights) >= 0) ? parseInt(maxNights) : 0,
                selectForward: true,
                autoClose: true,
                moveBothMonths: true,
                disabledDates: (typeof formattedDisabledDates !== 'undefined') ? formattedDisabledDates : [],
                enableCheckout: true,
                disabledDaysOfWeek: (typeof disabledDaysOfWeek !== 'undefined') ? disabledDaysOfWeek : [],
                noCheckInDaysOfWeek: (typeof noCheckInDaysOfWeek !== 'undefined') ? noCheckInDaysOfWeek : [],
                noCheckOutDaysOfWeek: (typeof noCheckOutDaysOfWeek !== 'undefined') ? noCheckOutDaysOfWeek : [],
                noCheckInDates: (typeof noCheckInDates !== 'undefined') ? noCheckInDates : [],
                noCheckOutDates: (typeof noCheckOutDates !== 'undefined') ? noCheckOutDates : [],
                animationSpeed: '.3s',
                calendarCustomStay: (typeof calendarCustomStay !== 'undefined') ? calendarCustomStay : [],
                onSelectRange: function() {
                    // console.log('[Old Inquiry] onSelectRange called!');
                    // When a range is selected, update the visible check_in and check_out fields
                    var dateValue = rangeInput.value;
                    // console.log('[Old Inquiry] Date value from range input:', dateValue);
                    if (!dateValue) {
                        // console.log('[Old Inquiry] No date value, returning');
                        return;
                    }
                    var dates = dateValue.split(' - ');
                    var start = dates[0] || '';
                    var end = dates[1] || '';
                    if (inquiryCheckin) inquiryCheckin.value = start;
                    if (inquiryCheckout) inquiryCheckout.value = end;
                    // console.log('[Old Inquiry] Picked:', start, end);

                    // --- Use the same proven logic from new form ---
                    // Update hidden inputs for inquiry form (if they exist)
                    var $startDateInput = jQuery('input[name="start_date"]');
                    var $endDateInput = jQuery('input[name="end_date"]');
                    if (start) {
                        $startDateInput.val(start);
                    }
                    if (end) {
                        $endDateInput.val(end);
                    }

                    // Also update the main booking form fields to keep them in sync
                    var $mainStartDateInput = jQuery('.hfy-listing-booking-form input[name="start_date"]');
                    var $mainEndDateInput = jQuery('.hfy-listing-booking-form input[name="end_date"]');
                    var $mainDatepickerInput = jQuery('#hotel-datepicker'); // Direct ID selector

                    // console.log('[Old Inquiry] Found main form elements:', {
                    //     mainStartDateInput: $mainStartDateInput.length,
                    //     mainEndDateInput: $mainEndDateInput.length,
                    //     mainDatepickerInput: $mainDatepickerInput.length
                    // });

                    if ($mainStartDateInput.length) {
                        $mainStartDateInput.val(start);
                    }
                    if ($mainEndDateInput.length) {
                        $mainEndDateInput.val(end);
                    }
                    // Update the visible input in the main form as well
                    if ($mainDatepickerInput.length && start && end) {
                         $mainDatepickerInput.val(`${start} - ${end}`);
                         // Trigger change event if needed for other scripts listening on the main form
                         $mainDatepickerInput.trigger('change');
                         // console.log('[Old Inquiry] Updated hotel-datepicker:', $mainDatepickerInput.val());
                    }

                    // Update prices if the function exists
                    if (typeof window.doUpdatePriceBlock === 'function') {
                        window.doUpdatePriceBlock();
                        // console.log('[Old Inquiry] Called doUpdatePriceBlock');
                    }
                    
                    // console.log('[Old Inquiry] Updated main booking form and called doUpdatePriceBlock');
                }
            };

            // Initialize the datepicker on the hidden range input
            var oldInquiryDatepicker = new HotelDatepicker(rangeInput, inquiryOptions);
            rangeInput._hotelDatepicker = oldInquiryDatepicker; // Store reference
            window.oldInquiryDatepicker = oldInquiryDatepicker; // Store globally
            // console.log('[Old Inquiry] Datepicker initialized:', oldInquiryDatepicker);

            // When user clicks either check_in or check_out, open the datepicker
            inquiryCheckin.addEventListener('focus', function() {
                // console.log('[Old Inquiry] inquiry_checkin focused, opening datepicker');
                oldInquiryDatepicker.open();
            });
            inquiryCheckout.addEventListener('focus', function() {
                // console.log('[Old Inquiry] inquiry_checkout focused, opening datepicker');
                oldInquiryDatepicker.open();
            });
        }
        
        // console.log('[Old Inquiry] Old inquiry form initialization complete');
    }
    
    // Remove immediate initialization
    // initOldInquiryDatepicker();
    
    // Listen for the hfymodal OPEN event (this is the proper way)
    jQuery(document).ready(function($) {
        $('.direct-inquiry-modal').on($.hfymodal.OPEN, function(event, modal) {
            // console.log('[Old Inquiry] hfymodal OPEN event fired, initializing...');
            
            // Check if new inquiry form is enabled - if so, skip old inquiry initialization
            if (typeof window.HFY_USE_NEW_INQUIRY_FORM !== 'undefined' && window.HFY_USE_NEW_INQUIRY_FORM) {
                // console.log('[Old Inquiry] New inquiry form is enabled, skipping old inquiry initialization');
                return;
            }
            
            // Check if this is the V2 modal - if so, skip old inquiry initialization
            if ($(this).find('#hfy-direct-inquiry-form-v2').length > 0) {
                // console.log('[Old Inquiry] V2 modal detected, skipping old inquiry initialization');
                return;
            }
            
            setTimeout(function() {
                initOldInquiryDatepicker();
            }, 200); // Same delay as the existing code
        });
    });
    
    // Also listen for the modal open event (backup)
    document.addEventListener('direct-inquiry-modal-opened', function() {
        // console.log('[Old Inquiry] Modal opened event received, checking for elements...');
        
        // Check if new inquiry form is enabled - if so, skip old inquiry initialization
        if (typeof window.HFY_USE_NEW_INQUIRY_FORM !== 'undefined' && window.HFY_USE_NEW_INQUIRY_FORM) {
            // console.log('[Old Inquiry] New inquiry form is enabled, skipping old inquiry initialization');
            return;
        }
        
        // Check if this is the V2 modal - if so, skip old inquiry initialization
        if (document.querySelector('#hfy-direct-inquiry-form-v2')) {
            // console.log('[Old Inquiry] V2 modal detected in backup event, skipping old inquiry initialization');
            return;
        }
        
        setTimeout(function() {
            initOldInquiryDatepicker();
        }, 150);
    });
});

// --- Global sync: Booking form to old inquiry form (outside DOMContentLoaded) ---
function syncBookingFormToOldInquiry() {
    var mainDateInput = document.getElementById('hotel-datepicker');
    if (mainDateInput && mainDateInput.value) {
        var value = mainDateInput.value;
        var dates = value.split(' - ');
        var start = dates[0] || '';
        var end = dates[1] || '';
        
        // Target the old inquiry form specifically
        var inquiryCheckin = document.querySelector('.direct-inquiry-modal #inquiry_checkin');
        var inquiryCheckout = document.querySelector('.direct-inquiry-modal #inquiry_checkout');
        var inquiryDatepicker = document.querySelector('.direct-inquiry-modal #inquiry-hotel-datepicker');
        var rangeInput = document.getElementById('inquiry-hotel-datepicker-old');
        
        // console.log('[Global Sync] Found elements:', {
        //     inquiryCheckin: !!inquiryCheckin,
        //     inquiryCheckout: !!inquiryCheckout,
        //     inquiryDatepicker: !!inquiryDatepicker,
        //     rangeInput: !!rangeInput,
        //     start: start,
        //     end: end
        // });
        
        if (inquiryCheckin) {
            inquiryCheckin.value = start;
            // console.log('[Global Sync] Updated inquiry_checkin:', start);
        }
        if (inquiryCheckout) {
            inquiryCheckout.value = end;
            // console.log('[Global Sync] Updated inquiry_checkout:', end);
        }
        if (inquiryDatepicker) {
            inquiryDatepicker.value = value;
            // console.log('[Global Sync] Updated inquiry-hotel-datepicker:', value);
        }
        if (rangeInput) {
            rangeInput.value = value;
            // Try to update the datepicker instance if it exists
            if (window.oldInquiryDatepicker) {
                window.oldInquiryDatepicker.setRange(start, end);
                // console.log('[Global Sync] Updated datepicker range:', start, end);
            }
        }
        // console.log('[Global Sync] Booking form updated old inquiry form:', start, end);
    } else {
        // console.log('[Global Sync] No main date input or value found');
    }
}

// --- Listen for booking form changes globally ---
document.addEventListener('DOMContentLoaded', function() {
    // Listen for changes on the main booking form
    var mainDateInput = document.getElementById('hotel-datepicker');
    if (mainDateInput) {
        mainDateInput.addEventListener('change', syncBookingFormToOldInquiry);
        mainDateInput.addEventListener('input', syncBookingFormToOldInquiry);
    }
    
    // Also listen for the datepicker's afterClose event
    if (mainDateInput) {
        mainDateInput.addEventListener('afterClose', function() {
            setTimeout(syncBookingFormToOldInquiry, 100); // Small delay to ensure value is updated
        });
    }
    
    // Store the old inquiry datepicker globally for access
    if (typeof HotelDatepicker === 'function' && document.getElementById('inquiry-hotel-datepicker-old')) {
        var rangeInput = document.getElementById('inquiry-hotel-datepicker-old');
        if (rangeInput && rangeInput._hotelDatepicker) {
            window.oldInquiryDatepicker = rangeInput._hotelDatepicker;
        }
    }
});

// --- Ensure HotelDatepicker is initialized for old inquiry modal input when modal opens (with debug logging and event delegation) ---
// This section is now handled by the main modal initialization above

// --- Fallback: Only initialize HotelDatepicker on focus for old inquiry form, not new modal ---
document.addEventListener('focusin', function(e) {
    if (e.target && e.target.id === 'inquiry-hotel-datepicker') {
        var input = e.target;
        
        // Check if new inquiry form is enabled - if so, skip fallback initialization
        if (typeof window.HFY_USE_NEW_INQUIRY_FORM !== 'undefined' && window.HFY_USE_NEW_INQUIRY_FORM) {
            console.log('[Fallback] New inquiry form is enabled, skipping fallback initialization');
            return;
        }
        
        // Check if V3 calendar is disabled - if so, skip new datepicker initialization
        if (typeof window.HFY_USE_V3_CALENDAR === 'undefined' || !window.HFY_USE_V3_CALENDAR) {
            console.log('[Fallback] V3 calendar is disabled, skipping new datepicker initialization');
            return;
        }
        
        // Skip fallback if inside the new inquiry modal - let the new modal handle it
        if (input.closest('#hfy-direct-inquiry-form-v2') || input.closest('.hfy-modal-body')) {
            console.log('[Fallback] Skipping focusin handler for V2 modal input');
            return;
        }
        // For old inquiry forms, use the existing logic below
        var latest = window.hfyMainBookingDates || { start: '', end: '', value: '' };
        if (typeof HotelDatepicker === 'function' && !input._hotelDatepicker) {
            var inquiryOptions = {
                showTopbar: false,
                clearButton: false,
                format: 'YYYY-MM-DD',
                startOfWeek: 'monday',
                minNights: (typeof minNights !== 'undefined' && Number.isInteger(parseInt(minNights)) && parseInt(minNights) > 0) ? parseInt(minNights) : 1,
                maxNights: (typeof maxNights !== 'undefined' && Number.isInteger(parseInt(maxNights)) && parseInt(maxNights) >= 0) ? parseInt(maxNights) : 0,
                selectForward: true,
                autoClose: true,
                moveBothMonths: true,
                disabledDates: (typeof formattedDisabledDates !== 'undefined') ? formattedDisabledDates : [],
                enableCheckout: true,
                disabledDaysOfWeek: (typeof disabledDaysOfWeek !== 'undefined') ? disabledDaysOfWeek : [],
                noCheckInDaysOfWeek: (typeof noCheckInDaysOfWeek !== 'undefined') ? noCheckInDaysOfWeek : [],
                noCheckOutDaysOfWeek: (typeof noCheckOutDaysOfWeek !== 'undefined') ? noCheckOutDaysOfWeek : [],
                noCheckInDates: (typeof noCheckInDates !== 'undefined') ? noCheckInDates : [],
                noCheckOutDates: (typeof noCheckOutDates !== 'undefined') ? noCheckOutDates : [],
                animationSpeed: '.3s',
                calendarCustomStay: (typeof calendarCustomStay !== 'undefined') ? calendarCustomStay : [],
                onSelectRange: function() {}
            };
            var inquiryDatepicker = new HotelDatepicker(input, inquiryOptions);
            input._hotelDatepicker = inquiryDatepicker;
            input.removeAttribute('readonly');
            // Always set value and range to latest main booking form dates
            if (latest.value) {
                input.value = latest.value;
                inquiryDatepicker.setRange(latest.start, latest.end);
                console.log('Fallback: Set value and setRange to', latest.start, latest.end);
            }
            inquiryDatepicker.open();
            console.log('Fallback: HotelDatepicker initialized and opened on focus');
        } else if (input._hotelDatepicker && latest.value) {
            // If already initialized, always sync value and range
            input.value = latest.value;
            input._hotelDatepicker.setRange(latest.start, latest.end);
            console.log('Fallback: Already initialized, set value and setRange to', latest.start, latest.end);
        }
    }
});

// --- Store last selected main booking form dates globally ---
window.hfyMainBookingDates = { start: '', end: '', value: '' };
document.addEventListener('DOMContentLoaded', function() {
    var mainDateInput = document.getElementById('hotel-datepicker');
    if (mainDateInput) {
        mainDateInput.addEventListener('change', function() {
            var value = mainDateInput.value;
            if (!value) return;
            var dates = value.split(' - ');
            var start = dates[0] || '';
            var end = dates[1] || '';
            window.hfyMainBookingDates = { start: start, end: end, value: value };

            // Update old inquiry form (two fields) - new modal handles its own updates
            var oldCheckin = document.getElementById('inquiry_checkin');
            var oldCheckout = document.getElementById('inquiry_checkout');
            if (oldCheckin && oldCheckout) {
                oldCheckin.value = start;
                oldCheckout.value = end;
            }
            
            // console.log('Main booking form dates updated:', window.hfyMainBookingDates);
        });
    }
});

// --- When the inquiry modal is opened, sync dates from main booking form (with forced events and debug) ---
// This is now handled by the main modal initialization above
