// Global datepicker instance will be stored in window.hotelDatepicker

// Removed redundant getRequiredMinStayForDate function. Library handles this internally now.

document.addEventListener('DOMContentLoaded', function() {
    // Check if HotelDatepicker is available
    if (typeof HotelDatepicker !== 'function') {
        console.warn('HotelDatepicker not found. Skipping datepicker initialization.');
        return;
    }

    // --- DEBUG: Log global variables ---
    // console.log('DEBUG: Initial value of calendarCustomStay:', typeof calendarCustomStay !== 'undefined' ? JSON.stringify(calendarCustomStay) : 'undefined');
    // console.log('DEBUG: Initial value of minNights:', typeof minNights !== 'undefined' ? minNights : 'undefined');
    // console.log('DEBUG: Initial value of maxNights:', typeof maxNights !== 'undefined' ? maxNights : 'undefined');
    // console.log('DEBUG: Initial value of hfyArgs:', typeof hfyArgs !== 'undefined' ? JSON.stringify(hfyArgs) : 'undefined');
    // console.log('DEBUG: hfyx.use_v3_calendar:', typeof hfyx !== 'undefined' ? hfyx.use_v3_calendar : 'undefined');
    // --- END DEBUG ---

    var currentSelectionStartDate = null; // Variable to hold the start date during selection

    // Process disabled dates from global variable
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
                    var rangeStartDate = fecha.parse(dateItem.start, 'YYYY-MM-DD'); // Renamed to avoid conflict
                    var rangeEndDate = fecha.parse(dateItem.end, 'YYYY-MM-DD'); // Renamed to avoid conflict
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
    } else if (typeof calendarDisabledDates !== 'undefined') {
        console.warn('calendarDisabledDates is defined but not an array:', calendarDisabledDates);
    }

    // Get default minNights from global variable
    var defaultMinNights = (typeof minNights !== 'undefined' && Number.isInteger(parseInt(minNights)) && parseInt(minNights) > 0) ? parseInt(minNights) : 1;

    // Process disabled days of week from global variable (hfyArgs)
    var disabledDaysOfWeek = (typeof hfyArgs !== 'undefined' && Array.isArray(hfyArgs.disabledWeekDays)) ? hfyArgs.disabledWeekDays : [];

    // Process no check-in/check-out days of week from global variable (hfyArgs)
    // Note: The old code used calendarInDays/calendarOutDays bitmasks. We'll use hfyArgs.daysOfWeekDisabled for both now.
    var noCheckInDaysOfWeek = (typeof hfyArgs !== 'undefined' && Array.isArray(hfyArgs.daysOfWeekDisabled)) ? hfyArgs.daysOfWeekDisabled : [];
    var noCheckOutDaysOfWeek = (typeof hfyArgs !== 'undefined' && Array.isArray(hfyArgs.daysOfWeekDisabled)) ? hfyArgs.daysOfWeekDisabled : []; // Assuming same as check-in for now

    // Process specific no check-in/check-out dates from global variables
    var noCheckInDates = (typeof calendarOverInDays !== 'undefined' && Array.isArray(calendarOverInDays)) ? calendarOverInDays.map(d => typeof d === 'string' ? d : '') : [];
    var noCheckOutDates = (typeof calendarOverOutDays !== 'undefined' && Array.isArray(calendarOverOutDays)) ? calendarOverOutDays.map(d => typeof d === 'string' ? d : '') : [];
    // Filter out any empty strings resulting from non-string items
    noCheckInDates = noCheckInDates.filter(d => d !== '');
    noCheckOutDates = noCheckOutDates.filter(d => d !== '');

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
        minNights: 1, // Set base minNights to 1, rely on calendarCustomStay for specifics
        maxNights: defaultMaxNights,
        selectForward: true, // Changed based on review
        autoClose: true,   // Changed based on review
        moveBothMonths: true, // Changed based on review
        disabledDates: formattedDisabledDates,
        enableCheckout: true,
        disabledDaysOfWeek: disabledDaysOfWeek,
        noCheckInDaysOfWeek: noCheckInDaysOfWeek,
        noCheckOutDaysOfWeek: noCheckOutDaysOfWeek,
        noCheckInDates: noCheckInDates, // Added
        noCheckOutDates: noCheckOutDates, // Added
        animationSpeed: '.3s',
        // hoveringTooltip: true, // Use library's default tooltip which now respects dynamic minNights
        calendarCustomStay: typeof calendarCustomStay !== 'undefined' ? calendarCustomStay : [], // Pass custom stay rules
        
        // Mobile-specific options
        mobileMonthsToShow: 6, // Show 6 months initially on mobile
        mobileMonthsToLoad: 24, // Pre-load 12 months total
        onDayClick: function(event, date) {
            var datepicker = window.hotelDatepicker;
            if (datepicker && !datepicker.getValue()) {
                 currentSelectionStartDate = date;
            } else {
                 currentSelectionStartDate = null;
            }
        },
        onSelectRange: function() {
            currentSelectionStartDate = null; // Reset on range selection complete
            updateFormAndPrices();
        }
        // getMinStayFunction: getRequiredMinStayForDate // We are not modifying the library anymore
    };

    // --- Prepare initial value for the datepicker input if dates are pre-filled ---
    var mainDateInput = document.getElementById('hotel-datepicker');
    
    // Exit if datepicker input doesn't exist (e.g. on payment page)
    if (!mainDateInput) {
        // console.log('Datepicker input #hotel-datepicker not found on this page. Skipping initialization.');
        return;
    }
    
    var initialStartDateInputPrep = document.querySelector('input[name="start_date"]'); // Use different var name to avoid conflict later
    var initialEndDateInputPrep = document.querySelector('input[name="end_date"]');

    if (mainDateInput && initialStartDateInputPrep && initialStartDateInputPrep.value && initialEndDateInputPrep && initialEndDateInputPrep.value) {
        var startDateStr = initialStartDateInputPrep.value;
        var endDateStr = initialEndDateInputPrep.value;
        var formattedStartDate = null;
        var formattedEndDate = null;

        // --- Roo: Try parsing YYYY-MM-DD first ---
        if (/^\d{4}-\d{2}-\d{2}$/.test(startDateStr) && /^\d{4}-\d{2}-\d{2}$/.test(endDateStr)) {
            try {
                // Use fecha to be sure they are valid dates, even if format matches regex
                var d1 = fecha.parse(startDateStr, 'YYYY-MM-DD');
                var d2 = fecha.parse(endDateStr, 'YYYY-MM-DD');
                if (d1 && d2 && d1 < d2) {
                    formattedStartDate = startDateStr;
                    formattedEndDate = endDateStr;
                }
            } catch (e) { /* Ignore parsing errors here */ }
        }

        // --- Roo: If YYYY-MM-DD failed or was invalid, try DD-MM-YYYY ---
        if (!formattedStartDate && /^\d{2}-\d{2}-\d{4}$/.test(startDateStr) && /^\d{2}-\d{2}-\d{4}$/.test(endDateStr)) {
             try {
                var d1 = fecha.parse(startDateStr, 'DD-MM-YYYY');
                var d2 = fecha.parse(endDateStr, 'DD-MM-YYYY');
                if (d1 && d2 && d1 < d2) {
                    // Reformat to the expected YYYY-MM-DD for the input and datepicker library
                    formattedStartDate = fecha.format(d1, 'YYYY-MM-DD');
                    formattedEndDate = fecha.format(d2, 'YYYY-MM-DD');
                }
            } catch (e) { /* Ignore parsing errors here */ }
        }

        // --- Roo: Set the input value if we successfully parsed and formatted dates ---
        if (formattedStartDate && formattedEndDate) {
            mainDateInput.value = formattedStartDate + ' - ' + formattedEndDate;
            // console.log('DEBUG: Pre-setting #hotel-datepicker value to:', mainDateInput.value);
        } else {
            // console.warn('Could not parse or validate initial start/end dates from hidden inputs. Not pre-setting value.', { start: startDateStr, end: endDateStr });
        }
    }
    // --- End preparation ---

    // Initialize the datepicker (use the mainDateInput variable which might have updated value)
    var datepicker = new HotelDatepicker(mainDateInput, options);
    
    // Override the i18n settings with translated month and day names if available
    if (typeof hfyTranslations !== 'undefined') {
        // Override month names
        if (hfyTranslations.monthNames && hfyTranslations.monthNames.length === 12) {
            datepicker.i18n['month-names'] = hfyTranslations.monthNames;
        }
        if (hfyTranslations.monthNamesShort && hfyTranslations.monthNamesShort.length === 12) {
            datepicker.i18n['month-names-short'] = hfyTranslations.monthNamesShort;
        }
        // Override day names
        if (hfyTranslations.dayNames && hfyTranslations.dayNames.length === 7) {
            datepicker.i18n['day-names'] = hfyTranslations.dayNames;
        }
        if (hfyTranslations.dayNamesShort && hfyTranslations.dayNamesShort.length === 7) {
            datepicker.i18n['day-names-short'] = hfyTranslations.dayNamesShort;
        }
        // Update the fecha library with new translations
        datepicker.setFechaI18n();
    }

    // Store the datepicker instance globally
    window.hotelDatepicker = datepicker;

    // Handle clear dates button
    var resetDateButton = document.querySelector('.reset-date');
    if (resetDateButton && datepicker) {
        resetDateButton.addEventListener('click', function() {
            datepicker.clear();
            clearFormDates();
        });
    }

    // Listen for the afterClose event (redundant if onSelectRange works, but safe)
    if (mainDateInput) {
        mainDateInput.addEventListener('afterClose', function() {
            // Check if dates are actually selected before updating
            if (datepicker && datepicker.getValue()) {
                 updateFormAndPrices();
            }
        });
    }

    // Trigger initial update if dates are pre-filled on load
    var initialStartDateInput = document.querySelector('input[name="start_date"]');
    var initialEndDateInput = document.querySelector('input[name="end_date"]');
    if (initialStartDateInput && initialStartDateInput.value && initialEndDateInput && initialEndDateInput.value) {
        setTimeout(function() {
            if (typeof window.doUpdatePriceBlock === 'function') {
                window.doUpdatePriceBlock();
            } else if (typeof submitForm === 'function') {
                submitForm();
            }
        }, 0);
    }

    // Initialize V3 calendar in hfy-listing-availability div if V3 is enabled
    if (typeof hfyx !== 'undefined' && hfyx.use_v3_calendar) {
        var availabilityDiv = document.querySelector('.hfy-listing-availability');
        if (availabilityDiv) {
            var v3Container = document.getElementById('hotel-datepicker-v3');
            if (v3Container && typeof HotelDatepicker === 'function') {
                // Initialize V3 datepicker with same options but modified for direct display
                var v3Options = Object.assign({}, options, {
                    inline: true,  // Make the calendar always visible
                    showTopbar: false,
                    clearButton: false,
                    format: 'YYYY-MM-DD',
                    startOfWeek: 'monday',
                    minNights: 1, // Set base minNights to 1, rely on calendarCustomStay for specifics
                    maxNights: defaultMaxNights,
                    selectForward: true, // Changed based on review
                    autoClose: false,  // Don't auto-close since it's always visible
                    moveBothMonths: true, // Changed based on review
                    disabledDates: formattedDisabledDates,
                    enableCheckout: true,
                    disabledDaysOfWeek: disabledDaysOfWeek,
                    noCheckInDaysOfWeek: noCheckInDaysOfWeek,
                    noCheckOutDaysOfWeek: noCheckOutDaysOfWeek,
                    noCheckInDates: noCheckInDates, // Added
                    noCheckOutDates: noCheckOutDates, // Added
                    animationSpeed: '.3s',
                    // hoveringTooltip: true, // Use library's default tooltip which now respects dynamic minNights
                    calendarCustomStay: typeof calendarCustomStay !== 'undefined' ? calendarCustomStay : [], // Pass custom stay rules
                    
                    // Force desktop mode
                    isMobile: false,
                    isTouchDevice: false,
                    
                    // Mobile-specific options
                    // mobileMonthsToShow: 1, // Show only 1 month on mobile - is not working
                    mobileMonthsToLoad: 2, // Pre-load 2 months total
                });
                
                var v3Datepicker = new HotelDatepicker(v3Container, v3Options);
                
                // Override the i18n settings with translated month and day names if available
                if (typeof hfyTranslations !== 'undefined') {
                    // Override month names
                    if (hfyTranslations.monthNames && hfyTranslations.monthNames.length === 12) {
                        v3Datepicker.i18n['month-names'] = hfyTranslations.monthNames;
                    }
                    if (hfyTranslations.monthNamesShort && hfyTranslations.monthNamesShort.length === 12) {
                        v3Datepicker.i18n['month-names-short'] = hfyTranslations.monthNamesShort;
                    }
                    // Override day names
                    if (hfyTranslations.dayNames && hfyTranslations.dayNames.length === 7) {
                        v3Datepicker.i18n['day-names'] = hfyTranslations.dayNames;
                    }
                    if (hfyTranslations.dayNamesShort && hfyTranslations.dayNamesShort.length === 7) {
                        v3Datepicker.i18n['day-names-short'] = hfyTranslations.dayNamesShort;
                    }
                    // Update the fecha library with new translations
                    v3Datepicker.setFechaI18n();
                }
                
                // Override the core dayClicked method to prevent selection
                v3Datepicker.dayClicked = function() { return false; };
                
                // Force desktop mode by overriding isMobile property
                Object.defineProperty(v3Datepicker, 'isMobile', {
                    get: function() {
                        return false; // Always return false to force desktop mode
                    },
                    set: function() {
                        // Prevent setting isMobile
                    }
                });
                
                // Override the checkIsMobile function
                v3Datepicker.checkIsMobile = function() {
                    return false; // Always return false to force desktop mode
                };
                
                // Override the isTouchDevice function
                v3Datepicker.isTouchDevice = function() {
                    return false; // Always return false to force desktop mode
                };
                
                // Force initial check and render
                v3Datepicker.onResizeDatepicker();
            }
        }
    }
});

// Update form hidden fields and trigger price update
function updateFormAndPrices() {
    var datepicker = window.hotelDatepicker;
    if (!datepicker) return;

    var dateValue = datepicker.getValue();
    if (!dateValue) return;

    var dates = dateValue.split(' - ');
    var startDateStr = dates[0];
    var endDateStr = dates.length > 1 ? dates[1] : null;

    // --- Removed redundant Variable Minimum Stay Check ---
    // The library's internal checkSelection method now handles this validation.
    // Note: currentSelectionStartDate is reset within onSelectRange or clearFormDates

    // Update hidden input fields
    var startDateInput = document.querySelector('input[name="start_date"]');
    var endDateInput = document.querySelector('input[name="end_date"]');

    if (startDateInput && startDateStr) {
        startDateInput.value = startDateStr;
    }

    if (endDateInput && endDateStr) {
        endDateInput.value = endDateStr;
    }

    // Call submitForm function to update prices
    if (typeof window.doUpdatePriceBlock === 'function') {
        window.doUpdatePriceBlock();
    } else if (typeof submitForm === 'function') {
        submitForm();
    }
}

// Clear form dates and update UI
function clearFormDates() {
    // currentSelectionStartDate is reset within onSelectRange or here
    currentSelectionStartDate = null; // Also clear temp start date

    var startDateInput = document.querySelector('input[name="start_date"]');
    var endDateInput = document.querySelector('input[name="end_date"]');

    if (startDateInput) startDateInput.value = '';
    if (endDateInput) endDateInput.value = '';

    // Update URL and prices
    window.history.replaceState('', '', location.pathname);

    // Call submitForm function to update prices
    if (typeof window.doUpdatePriceBlock === 'function') {
        window.doUpdatePriceBlock();
    }
}