
var calentimDates,
    inquiryCalentimStart,
    inquiryCalentimEnd,
    startSelected = null,
    endSelected = null;

function fixDateFormat(key, value)
{
    if (key == 'start_date') {
        if (startSelected) {
            return startSelected.format(hfyDFdef);
        } else if ((typeof calentimDates !== 'undefined') && calentimDates.config.startDate) {
            return calentimDates.config.startDate.format(hfyDFdef);
        }
    }
    if (key == 'end_date') {
        if (endSelected) {
            return endSelected.format(hfyDFdef);
        } else if ((typeof calentimDates !== 'undefined') && calentimDates.config.endDate) {
            return calentimDates.config.endDate.format(hfyDFdef);
        }
    }
    return value;
}


jQuery(document).ready(function($) {

    var calDisDates = typeof calendarDisabledDates === 'undefined' ? [] : calendarDisabledDates;

    jQuery.ajaxSetup({ headers: { 'X-HFY-CSRF-TOKEN': jQuery('meta[name="hfy-csrf-token"]').attr('content') }});


    if (typeof minNights === 'undefined') {
        var minNights = 1;
    }

    var minNightsDefault = minNights;

    var filterCustomStay = function (date) {
        let result = false;
        if (typeof calendarCustomStay !== 'undefined') {
            $.each(calendarCustomStay, function(key, value) {
                if (date.isBetween(value.date_start, value.date_end, "day", "[]")) {
                    result = value.min_stay;
                    return false;
                }
            });
        }
        return result;
    };

    var setMinNights = function (date) {
        let customMinDate = filterCustomStay(date);
        if (customMinDate) {
            minNights = customMinDate;
        } else {
            minNights = minNightsDefault;
        }
        let addDays = getMinstay(date);
        if (addDays > minNights) minNights = addDays;
    };


    $formWrap = jQuery('.hfy-listing-booking-form');
    $searchWrap = jQuery('.hfy-search-form-wrap');

	var ondrawEvent = function (instance) {
        // console.log('=== ONDRAW EVENT CALLED ===');
        // console.log('Init complete:', instance.globals.initComplete);
        // console.log('Current calentim input value:', instance.$elem.val());
        
        if (instance.globals.initComplete) {
            // console.log('Calling updateInputs from ondrawEvent');
            updateInputs();
            // console.log('Calentim input value after updateInputs:', instance.$elem.val());
            // instance.updateHeader();
        }
    };



    jQuery(".hfy-listing-booking-form").each(function(){
        var $dc = jQuery(".hfy-listing-booking-form .discount-code-wrap");
        var $dcCheck = $dc.find('.discount_code_cb');
        var $dcText = $dc.find('input[name=discount_code]');
        var $dcApply = $dc.find('.discount_code_check');

        function dcApplyShow() {
            // if ($.trim($dcText.val()) !== '') $dcApply.show(); else $dcApply.hide();
            if ($.trim($dcText.val()) !== '') $dcApply.removeClass('btn-light').addClass('btn-success');
        }

        $dcText.on('keyup blur change', function(){
            dcApplyShow();
        });

        $dcApply.on('click', function(){
            submitForm();
            // jQuery(this).hide();
        });

        $dcCheck.on('change', function(){
            let $t = jQuery(this);
            let $i = $dc.find('.input_wrap');
            $dcText.val('');
            if ($t.prop('checked')) {
                $i.show();
                dcApplyShow();
            } else {
                $i.hide();
                submitForm();
            }
        });

        if ($dcCheck.prop('checked')) {
            $dc.find('.input_wrap').show();
            submitForm();
        }
    });

    var $formListingPrice = $formWrap.find('.listing-price');
    var $prices = $formListingPrice.find('.prices');
    var delaysubmitform;

    function submitForm(timeouted = false) {

        if ($formListingPrice.length <= 0) {
            return;
        }

        $formListingPrice.attr('method', 'get');

        var query = '?' + $formListingPrice.serialize();
        var queryObj = $.query.parseNew(query);

        if (
            (queryObj && queryObj.queryObject && queryObj.queryObject.keys) && (
                ((queryObj.queryObject.keys.start_date || '') != '')
                || ((queryObj.queryObject.keys.end_date || '') != '')
            )
        ) {
            $('.booking-price-block').removeClass('dates-is-selected');
        } else {
            $('.booking-price-block').addClass('dates-is-selected');
        }

        // get form values & fix queryObj values
        let vars = {};
        $.each(queryObj.keys, function(key, value) {
            let v = fixDateFormat(key, value);
            queryObj.keys[key] = v;
            if (value !== true) vars[key] = v;
        });

        // add missed items from current url parameters
        let href = new URL(window.location.href);
        let ent = Object.fromEntries(href.searchParams.entries());
        $.each(ent, function(key, value) {
            if (typeof vars[key] === 'undefined') vars[key] = value;
        });

        // fix dates
        $.each(vars, function(key, v) {
            let value = $.trim(v);
            if (key == 'start_date') {
                if (startSelected) {
                    value = startSelected.format(hfyDFdef);
                } else if ((typeof calentimDates !== 'undefined') && calentimDates.config.startDate) {
                    value = calentimDates.config.startDate.format(hfyDFdef);
                }
            }
            if (key == 'end_date') {
                if (endSelected) {
                    value = endSelected.format(hfyDFdef);
                } else if ((typeof calentimDates !== 'undefined') && calentimDates.config.endDate) {
                    value = calentimDates.config.endDate.format(hfyDFdef);
                }
            }
            vars[key] = value;
        });

        // update url params
        window.history.replaceState('', '', queryObj);

        let fres = []; // different array format for sending
        $.each(vars, function(name, value) {
            fres.push({ name, value });
        });

        if (
               typeof vars['start_date'] === 'undefined'
            || vars['start_date'] === false
            || vars['start_date'] === ''
            || typeof vars['end_date'] === 'undefined'
            || vars['end_date'] === false
            || vars['end_date'] === ''
        ) {
            return;
        }

        var do_ = function() {
            jQuery('body').addClass('loading-price');
            $.ajax({
                url: hfyx.url,
                data: {
                    action: 'listing_price',
                    data: jQuery.param(fres),
                },
                type: 'POST',
                success: function(data) {
                    jQuery('body').removeClass('loading-price');
                    if (data['success'] == true) {
                        $prices.html(data['data']);
                        $prices.html(data['title']);
                        let chListId = $prices.data('channel-listing-id');
                        let bookHref = jQuery('.book-on-airbnb').attr('href');
                        if (chListId && bookHref) {
                            jQuery('.book-on-airbnb').attr('href', bookHref.replace('channel_listing_id', chListId));
                        } else {
                            jQuery('.book-on-airbnb').removeAttr('href');
                        }
                        // jQuery('.booking-price-block .direct-inquiry-modal-open').show();
                        if (jQuery('.direct-inquiry-modal .direct-inquiry-nights').length) {
                            jQuery('.direct-inquiry-modal .direct-inquiry-nights').val(data['nights']);
                        }
                    } else {
                        $prices.html(data['data']);
                        $formListingPrice.find('.price-per-night').html($prices.data('price'));
                    }
                    window.dispatchEvent(new Event('resize'));
                    document.dispatchEvent(new Event('hfy-price-loaded'));
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    jQuery('body').removeClass('loading-price');
                    $prices.html('<div class="calendar-error">Please try again</div>');
                    $formListingPrice.find('.price-per-night').html($prices.data('price'));
                    window.dispatchEvent(new Event('resize'));
                }
            });
        }

        clearTimeout(delaysubmitform);
        delaysubmitform = setTimeout(function(){ do_() }, timeouted ? 500 : 1);
    }

    $formWrap.on("click", ".number-input .last, .number-input .first", function () {
        changeGuests(jQuery(this), $formWrap.find('.guests'));
        submitForm(true);
    });

    $formWrap.on("submit", "form", function (e) {
        let $form = $(this);
        let $submitBtn = $form.find('input[type="submit"]');
        let formAction = $submitBtn.attr('formaction');
        
        // Get current language from URL
        let currentUrl = window.location.href;
        let langMatch = currentUrl.match(/\/([a-z]{2})\//);
        if (langMatch && langMatch[1]) {
            // If formAction doesn't already have language prefix, add it
            if (!formAction.includes('/' + langMatch[1] + '/')) {
                let urlParts = formAction.split('://');
                if (urlParts.length > 1) {
                    let domain = urlParts[0] + '://' + urlParts[1].split('/')[0];
                    let path = '/' + urlParts[1].split('/').slice(1).join('/');
                    formAction = domain + '/' + langMatch[1] + path;
                    $submitBtn.attr('formaction', formAction);
                }
            }
        }
        
        // console.log('Form submission:', {
        //     originalAction: formAction,
        //     currentUrl: window.location.href,
        //     formData: $form.serialize()
        // });
    });



    function handleGuestsSelector()
    {
        $formWrap.on("click", ".guests-count-num-wrap, .btn-close-guests-box", function () {
            let $dd = $formWrap.find('.select-guests-dropdown');
            if ($dd.length) {
                if ($dd.is(':hidden')) {
                    $dd.show();
                } else {
                    $dd.hide();
                }
            }
        });

        $searchWrap.on("click", ".col-guests label, .col-guests .guests-input-label, .col-guests .guests-input-label > *",
        function (e) {
            let $dd = $searchWrap.find('.select-guests-dropdown');
            if ($dd.length) {
                if ($dd.is(':hidden')) {
                    $dd.show();
                } else {
                    $dd.hide();
                }
            }
        });

		$searchWrap.on("click", ".btn-close-guests-box", function (e) {
            $searchWrap.find('.select-guests-dropdown').hide();
            e.stopPropagation();
        });

        $(document).mouseup(function(e) {
            let g = $('.col-guests .select-guests-dropdown');
            if (!g.is(e.target) && g.has(e.target).length === 0) g.hide();
        });

        let $guests_wrap = $('.hfy-listing-booking-form .select-guests-wrap');
        // if ($guests_wrap.length <= 0) {
        //     $guests_wrap = $('.hfy-search-form-wrap .select-guests-wrap');
        // }
        if ($guests_wrap.length <= 0) return;

        $(".guests-box, .btn-close-guests-box .btn").on('click', $guests_wrap, function(){
            $('.select-guests').toggleClass('active-menu');
        });

        let $guests = $('.hfy-listing-booking-form .guests-count-num');
        let $guests_input = $('.hfy-listing-booking-form input[name=guests]');

        let $adults = $('#adults', $guests_wrap);
        let $children = $('#children', $guests_wrap);
        let $infants = $('#infants', $guests_wrap);
        let $pets = $('#pets', $guests_wrap);

        let max = parseInt($adults.attr('max'), 10);

        let iAdults = Math.abs(parseInt($adults.data('val'), 10));
        let iChildren = Math.abs(parseInt($children.data('val'), 10));

        let x = fixGuests(max, iAdults, iChildren);

        $adults.val(x[0]);
        $children.val(x[1]);
        $guests.text(x[2]);
        $guests_input.val(x[2]);

        $('.number-guest-input .ctrl-dec, .number-guest-input .ctrl-inc', $guests_wrap).on('click', function() {

            let $t = $(this);
            let $ctrl = $t.siblings('input').first();
            let iname = $ctrl.attr('name') || false;
            let ctrlMax = $ctrl.attr('max') || 10;

            if (iname === false) return;

            if ($t.hasClass('disabled')) return;

            let change = $t.hasClass('ctrl-inc') ? 1 : -1;

            let x = fixGuests(max, Math.abs(parseInt($adults.val(), 10)), Math.abs(parseInt($children.val(), 10)));
            let adults = x[0];
            let children = x[1];

            if (iname == 'adults') {
                if (change > 0) {
                    if (adults + children + 1 <= max) adults++;
                } else {
                    if (adults > 1) adults--;
                }
                x = fixGuests(max, adults, children);
                $adults.val(x[0]);
                $children.val(x[1]);
                $guests.text(x[2]);
                $guests_input.val(x[2]);

            } else if (iname == 'children') {
                if (change > 0) {
                    if (adults + children + 1 <= max) children++;
                } else {
                    if (children > 0) children--;
                }
                x = fixGuests(max, adults, children);
                $adults.val(x[0]);
                $children.val(x[1]);
                $guests.text(x[2]);
                $guests_input.val(x[2]);

            } else {
                let val = Math.abs(parseInt($ctrl.val(), 10)) + change;
                if (val > ctrlMax) val = ctrlMax;
                if (val < 0) val = 0;
                $ctrl.val(val);
            }

            submitForm(true);
        });

        // $formWrap.on("click", ".select-guests-dropdown", function () {
        // });
    }

    function handleGuestsSelectorSearch()
    {
        let $guests_wrap = $('.hfy-search-form-wrap .booking-search-input-container');
        if ($guests_wrap.length <= 0) return;

        $(".guests-box, .btn-close-guests-box .btn", $guests_wrap).on('click', $guests_wrap, function(){
            $('.select-guests').toggleClass('active-menu');
        });

        let $swrap = $('.hfy-search-form-wrap .booking-search-input-container');
        let $guests = $('.guests-count-num', $swrap);
        let $guests_input = $('input[name=guests]', $swrap);

        let $adults = $('#adults', $guests_wrap);
        let $children = $('#children', $guests_wrap);
        let $infants = $('#infants', $guests_wrap);
        let $pets = $('#pets', $guests_wrap);

        let max = parseInt($adults.attr('max'), 10);

        let iAdults = Math.abs(parseInt($adults.data('val'), 10));
        let iChildren = Math.abs(parseInt($children.data('val'), 10));

        let all = parseInt($adults.val(), 10) + parseInt($children.val(), 10);
        if (!all) all = $guests_input.val() || 1;
        $guests.text(all);
        $guests_input.val(all);

        $('.number-guest-input .ctrl-dec, .number-guest-input .ctrl-inc', $guests_wrap).on('click', function() {
            let $t = $(this);
            let $ctrl = $t.siblings('input').first();
            let iname = $ctrl.attr('name') || false;
            let ctrlMax = max || 10;
            let $guests = $t.parents('.booking-search-input-container').first().find('.guests-count-num');
            let $guests_input = $t.parents('.booking-search-input-container').first().find('input.guests');

            if (iname === false) return;

            let adults = Math.abs(parseInt($adults.val(), 10));
            let children = Math.abs(parseInt($children.val(), 10));

            let change = $t.hasClass('ctrl-inc') ? 1 : -1;

            let val = Math.abs(parseInt($ctrl.val(), 10)) + change;
            if (val > ctrlMax) val = ctrlMax;
            if (val < 0) val = 0;
            $ctrl.val(val);

            let all = parseInt($adults.val(), 10) + parseInt($children.val(), 10);
            $guests.text(all);
            $guests_input.val(all);
        });

        $formWrap.on("click", ".guests-count-num-wrap, .btn-close-guests-box .btn", function () {
            let $dd = $formWrap.find('.select-guests-dropdown');
            if ($dd.length) {
                if ($dd.is(':hidden')) {
                    $dd.show();
                } else {
                    $dd.hide();
                }
            }
        });

        $searchWrap.on("click", ".guests-count-num-wrap, .btn-close-guests-box .btn", function () {
            let $dd = $searchWrap.find('.select-guests-dropdown');
            if ($dd.length) {
                if ($dd.is(':hidden')) {
                    $dd.show();
                } else {
                    $dd.hide();
                }
            }
        });

        // $formWrap.on("click", ".select-guests-dropdown", function () {
        // });
    }

    handleGuestsSelector();
    handleGuestsSelectorSearch();

    //

    $searchWrap.on("click", ".number-input .last, .number-input .first", function () {
        changeGuests(jQuery(this), $searchWrap.find('.guests'));
    });

    $searchWrap.on("click", "button.advanced", function () {
        jQuery(this).toggleClass('active');
        $searchWrap.find('.hfy-search-form-row-advanced').toggle();
        return false;
    });

    $searchWrap.on("click", ".toggle-more-btn", function () {
        jQuery(this).parent().find('.toggle-more-container').toggleClass('expanded');
        jQuery(this).hide();
        return false;
    });

    $searchWrap.on("click", ".btn-reset", function () {
        let $x = jQuery(this).parents('.hfy-search-form-row-advanced').first();
        $x.find('input').val('').removeAttr('checked');
        $x.find('select option').removeAttr('selected');
        $x.find('select').val('');
    });

    $searchWrap.on("submit", "form", function () {
        $('.col-action button', this).attr('disabled', true);
        $searchWrap.addClass('processing');
        // reFormatSearchFormDateCal($('.hfy-search-form-row input[name=start_date]', this), startSelected);
        // reFormatSearchFormDateCal($('.hfy-search-form-row input[name=end_date]', this), endSelected);
    });





    let updateInputs = function()
    {
        // console.log('=== UPDATE INPUTS CALLED ===');
        // console.log('Current state:', {
        //     startSelected: startSelected ? startSelected.format('YYYY-MM-DD') : 'null',
        //     endSelected: endSelected ? endSelected.format('YYYY-MM-DD') : 'null',
        //     calentimDates: typeof calentimDates !== 'undefined' ? 'defined' : 'undefined'
        // });
        
        if (startSelected) {
            setMinNights(startSelected);
        }
        let addDays = getMinstay(startSelected);
        
        if (startSelected && endSelected && startSelected.hasOwnProperty("_isAMomentObject") && endSelected.hasOwnProperty("_isAMomentObject")) {
            // console.log('Both dates selected, updating calentim input...');
            
            if (startSelected.isAfter(endSelected, "day")) {
                endSelected = startSelected.clone().add(addDays, "days");
                // console.log('End date adjusted:', endSelected.format('YYYY-MM-DD'));
            }
            
            const displayValue = [
                startSelected.format(hfyDFopt),
                endSelected.format(hfyDFopt)
            ].join(calentimDates.config.dateSeparator);
            
            // console.log('Setting calentim input in updateInputs:', {
            //     displayValue: displayValue,
            //     hfyDFopt: hfyDFopt,
            //     dateSeparator: calentimDates.config.dateSeparator
            // });
            
            calentimDates.$elem.val(displayValue);
            
            // console.log('Calentim input value after updateInputs:', calentimDates.$elem.val());
        } else {
            // console.log('Not updating calentim input - missing dates or calentimDates');
        }
        if (startSelected && startSelected.hasOwnProperty("_isAMomentObject")) {
            if (inquiryCalentimStart && inquiryCalentimEnd) {
                inquiryCalentimStart.config.startEmpty = false;
                inquiryCalentimStart.config.startDate = startSelected;
                inquiryCalentimEnd.config.startDate = startSelected;
                inquiryCalentimEnd.config.minDate = startSelected.clone().add(addDays, 'days');
                inquiryCalentimStart.$elem.val(startSelected.format(inquiryCalentimStart.config.format));
            }

            calentimDates.config.startEmpty = false;
            calentimDates.config.startDate = startSelected;
            calentimDates.config.endDate = startSelected.clone().add(addDays, 'days');
            // calentimDates.config.minDate = startSelected;

            // calentimStart.$elem.val(startSelected.format(calentimStart.config.format));
        }
        if (endSelected && endSelected.hasOwnProperty("_isAMomentObject")) {
            if (inquiryCalentimStart && inquiryCalentimEnd) {
                inquiryCalentimStart.config.endDate = endSelected;
                inquiryCalentimEnd.config.endDate = endSelected;
                inquiryCalentimEnd.$elem.val(endSelected.format(inquiryCalentimEnd.config.format));
            }
            calentimDates.config.endDate = endSelected;
        }
        
        // Update the hidden input fields for form submission
        if (startSelected && startSelected.hasOwnProperty("_isAMomentObject")) {
            jQuery('input[name=start_date]').val(startSelected.format(hfyDFdef));
        }
        if (endSelected && endSelected.hasOwnProperty("_isAMomentObject")) {
            jQuery('input[name=end_date]').val(endSelected.format(hfyDFdef));
        }
        
        // Also update the inquiry form inputs directly
        if (jQuery('#inquiry_checkin').length && startSelected && startSelected.hasOwnProperty("_isAMomentObject")) {
            jQuery('#inquiry_checkin').val(startSelected.format(hfyDFopt));
        }
        if (jQuery('#inquiry_checkout').length && endSelected && endSelected.hasOwnProperty("_isAMomentObject")) {
            jQuery('#inquiry_checkout').val(endSelected.format(hfyDFopt));
        }
    };

    var filterDays = function(disabledRanges, date, maxNights) {
        let result = false;
        let dateMax = false;

        dateMin = date.clone().add(minNights, 'days');
        if (maxNights) {
            dateMax = moment(date.clone().add(maxNights, 'days'));
        }

        $.each(disabledRanges, function(key, value) {
            if (dateMin.isBefore(value.start, 'day') || dateMin.isSame(value.start, 'day')) {
                result = value.start;
                return false;
            } else if (dateMin.isBetween(value.start, value.end, "day", "[]")) {
                result = dateMin;
                return false;
            }
        });

        if ((!result && dateMax) || (result && dateMax && dateMax.isBefore(result, 'day'))) {
            result = dateMax;
        }

        return result;
    };

    var filterDisabled = function(disabledRanges, date) {
        let result = false;
        $.each(disabledRanges, function(key, value) {
            if (date.isBetween(value.start, value.end, "day", "(]")) {
                result = value.start;
                return false;
            }
        });
        return result;
    };

    // console.log('=== CALENTIM INITIALIZATION START ===');
    // console.log('Initial HTML value of calentim input:', $formWrap.find(".calentim-dates").val());
    // console.log('Hidden start_date input value:', $formWrap.find('input[name=start_date]').val());
    // console.log('Hidden end_date input value:', $formWrap.find('input[name=end_date]').val());
    
    const startDateFromInput = $formWrap.find('input[name=start_date]').val();
    const endDateFromInput = $formWrap.find('input[name=end_date]').val();
    
    // console.log('Date parsing for calentim:', {
    //     startDateFromInput: startDateFromInput,
    //     endDateFromInput: endDateFromInput,
    //     hfyDFdef: hfyDFdef,
    //     parsedStartDate: moment(startDateFromInput, hfyDFdef).format('YYYY-MM-DD'),
    //     parsedEndDate: moment(endDateFromInput, hfyDFdef).format('YYYY-MM-DD')
    // });
    
    // Clear the calentim input value before initialization to prevent calentim from reading the wrong dates
    $formWrap.find(".calentim-dates").val('');
    
    // console.log('Cleared calentim input value before initialization');
    
    $formWrap.find(".calentim-dates").calentim({
        startDate: moment(startDateFromInput, hfyDFdef),
        endDate: moment(endDateFromInput, hfyDFdef),
        format: hfyDFopt,
        startOnMonday: hfyStartOnMonday,
        locale: hfyCurrentLang,
        enableMonthSwitcher: false,
        enableYearSwitcher: false,
        showHeader: isMob,
        showFooter: false,
        startEmpty: false, // Force calentim to use our provided dates
        showTimePickers: false,
        calendarCount: isMob ? 24 : 2,
        isMobile: isMob,
        isIOS: isIOS,
        showOn: 'bottom',
        arrowOn: 'center',
        autoAlign: true,
        oneCalendarWidth: 280,
        continuous: true,
        autoCloseOnSelect: !isMob,
        minDate: moment().startOf('day'),
        maxDate: moment().add(1, 'years'),
        disabledRanges: calDisDates,

        onaftershow: updatePopupTipsIn,
        onaftermonthchange: updatePopupTipsIn,
        onafteryearchange: updatePopupTipsIn,

        onCustomStyles: function(cell, cellMoment, cellDateUnix, cellStyle, minDateUnix, maxDateUnix, currentMonth) {
            if (isDayDisabledForCheckIn(cellMoment)) {
                cellStyle += " calentim-disabled-day";
            }
            return cellStyle;
        },

        onafterhide: function() {
            if (calentimDates) {
                submitForm();
            }
        },

        oninit: function(instance) {
            // console.log('=== CALENTIM INITIALIZATION START ===');
            // console.log('Current configuration:', {
            //     use_v3_calendar: typeof hfyx !== 'undefined' ? hfyx.use_v3_calendar : 'undefined',
            //     hfyDFopt: typeof hfyDFopt !== 'undefined' ? hfyDFopt : 'undefined',
            //     hfyDFdef: typeof hfyDFdef !== 'undefined' ? hfyDFdef : 'undefined'
            // });
            // console.log('Calentim input value at oninit:', instance.$elem.val());
            // console.log('Calentim input HTML value attribute:', instance.$elem[0].getAttribute('value'));
            
            moment.locale(hfyCurrentLang);
            instance.globals.delayInputUpdate = true;
            calentimDates = instance;

            // console.log('Initial calentim config:', {
            //     startDate: instance.config.startDate ? instance.config.startDate.format('YYYY-MM-DD') : 'null',
            //     endDate: instance.config.endDate ? instance.config.endDate.format('YYYY-MM-DD') : 'null',
            //     format: instance.config.format,
            //     dateSeparator: instance.config.dateSeparator
            // });

            // Initialize with pre-selected dates if they exist
            let displayValue = null; // Declare in broader scope
            
            if (instance.config.startDate && instance.config.endDate && 
                instance.config.startDate.isValid() && instance.config.endDate.isValid()) {
                // console.log('Pre-selected dates found, initializing...');
                
                startSelected = instance.config.startDate.clone();
                endSelected = instance.config.endDate.clone();
                
                // console.log('Selected dates set:', {
                //     startSelected: startSelected.format('YYYY-MM-DD'),
                //     endSelected: endSelected.format('YYYY-MM-DD')
                // });
                
                // Update the calentim input with proper display format immediately
                // Since we're in main4.js, we know this is the NEW_CALENDAR context
                displayValue = [
                    startSelected.format(hfyDFopt),
                    endSelected.format(hfyDFopt)
                ].join(instance.config.dateSeparator);
                
                // console.log('Setting calentim input display value:', {
                //     displayValue: displayValue,
                //     hfyDFopt: hfyDFopt,
                //     dateSeparator: instance.config.dateSeparator
                // });
                
                instance.$elem.val(displayValue);
                
                // console.log('Calentim input value after setting:', instance.$elem.val());
                
                // Update hidden inputs
                const startDateValue = startSelected.format(hfyDFdef);
                const endDateValue = endSelected.format(hfyDFdef);
                
                // console.log('Updating hidden inputs:', {
                //     start_date: startDateValue,
                //     end_date: endDateValue,
                //     hfyDFdef: hfyDFdef
                // });
                
                jQuery('input[name=start_date]').val(startDateValue);
                jQuery('input[name=end_date]').val(endDateValue);
                
                // console.log('Hidden inputs after update:', {
                //     start_date: jQuery('input[name=start_date]').val(),
                //     end_date: jQuery('input[name=end_date]').val()
                // });
            } else {
                // console.log('No pre-selected dates found');
            }

            if (calentimDates) {
                // console.log('Calling submitForm() from oninit');
                submitForm();
            }
            
            // console.log('=== CALENTIM INITIALIZATION END ===');
            
            // Add a delayed check to see if the value gets overridden
            setTimeout(function() {
                // console.log('=== DELAYED CHECK ===');
                // console.log('Calentim input value after 1 second:', instance.$elem.val());
                // console.log('Hidden inputs after 1 second:', {
                //     start_date: jQuery('input[name=start_date]').val(),
                //     end_date: jQuery('input[name=end_date]').val()
                // });
                
                // Force update the display value if it's still wrong
                if (startSelected && endSelected && instance.$elem.val() !== displayValue) {
                    // console.log('Forcing display value update after delay');
                    instance.$elem.val(displayValue);
                    // console.log('Calentim input value after forced update:', instance.$elem.val());
                }
            }, 1000);
        },

        ondraw: ondrawEvent,

        onbeforeselect: function(instance, start, end) {
            startSelected = start.clone();
            endSelected = end.clone();
            updateInputs();
            return true;
        },

        // onbeforeshow: function(instance) {
        //     calentimDates.config.disabledRanges = calDisDates;
        //     calentimDates.config.minDate = moment().startOf('day');
        //     calentimDates.config.maxDate = null;
        // },

        onfirstselect: function(instance, start) {
            var min;
            instance.config.startEmpty = false;

            startSelected = start.clone();
//            instance.globals.startSelected = false;

            // BEGIN open disabled date for end select (calentim.js changes)
            var maxDate = filterDays(instance.config.disabledRanges, startSelected, maxNights);
            instance.config.maxDate = maxDate ? moment(maxDate) : null;
            // END open disabled date for end select

            if (endSelected && endSelected.isAfter(instance.config.maxDate, 'day')) {
                min = startSelected.clone().add(minNights, 'days');
                if (!filterDisabled(instance.config.disabledRanges, min)) {
                    endSelected = min;
                    instance.config.endDate = min;
                    // instance.$elem.val(min.format(instance.config.format));
                } else {
                    endSelected = null;
                    instance.config.endDate = null;
                    // instance.$elem.val("");
//                    window.history.replaceState('', '', location.pathname);
                    $formListingPrice.find('.prices').html('');
                }
            }

            min = startSelected.clone().add(minNights, 'days');

//             if (endSelected && endSelected.isSameOrBefore(min, 'days')) {
//                 endSelected = null;
//                 instance.config.endDate = null;
//                 // instance.$elem.val("");
// //                window.history.replaceState('', '', location.pathname);
//                 $formListingPrice.find('.prices').html('');
//             }

            // if (!endSelected) {
            //     // min = startSelected.clone().add(minNights, 'days');
            //     // if (!filterDisabled(instance.config.disabledRanges, min)) {
            //     //     endSelected = min;
            //     //     instance.config.endDate = min;
            //     //     // instance.$elem.val(min.format(instance.config.format));
            //     //     // instance.config.endDate = min;
            //     // } else {
            //     //     instance.config.endDate = startSelected;
            //     // }
            // }
            instance.config.minDate = min;
            instance.config.isHotelBooking = true;
        },

        onlastselect: function(instance, startDate, endDate) {
            // endSelected = endDate.clone();

            $formWrap.find('input[name=start_date]').val(startDate.format(hfyDFdef));
            $formWrap.find('input[name=end_date]').val(endDate.format(hfyDFdef));

            if (startSelected) {
                setMinNights(startSelected);
            }

            endSelected = endDate.clone().set({
                hours: instance.config.endDate.hours(),
                minutes: instance.config.endDate.minutes(),
                seconds: instance.config.endDate.seconds()
            });

            updateInputs();

            instance.config.disabledRanges = calDisDates;
            instance.config.minDate = moment().startOf('day');
            instance.config.maxDate = null;
            instance.config.isHotelBooking = false;
        },
    });

    // search - new - one field

    var $d1 = $searchWrap.find('.hfy-search-form-row input[name=start_date]');
    var $d2 = $searchWrap.find('.hfy-search-form-row input[name=end_date]');

    $searchWrap.find(".calentim-dates").calentim({
        startDate: $d1.length ? moment($d1.val(), hfyDFdef) : null,
        endDate: $d2.length ? moment($d2.val(), hfyDFdef) : null,
        startEmpty: true,
        format: hfyDFdef, // Use actual date format instead of display format
        startOnMonday: hfyStartOnMonday,
        locale: hfyCurrentLang,
        enableMonthSwitcher: false,
        enableYearSwitcher: false,
        showHeader: isMob,
        showFooter: false,
        showTimePickers: false,
        calendarCount: isMob ? 24 : 2,
        isMobile: isMob,
        isIOS: isIOS,
        showOn: "bottom",
        arrowOn: 'left',
        autoAlign: true,
        oneCalendarWidth: 280,
        continuous: true,
        autoCloseOnSelect: !isMob,
        minDate: moment().startOf('day'),
        oninit: function(instance) {
            calentimDates = instance;
            moment.locale(hfyCurrentLang);
            // let v = instance.$elem[0].getAttribute('value');
            // if (v.length) {
            //     startSelected = instance.config.startDate;
            //     endSelected = instance.config.endDate;
            // } else {
            //     instance.$elem.val('');
            // }
        },

        // onbeforeselect: function(instance, start, end) {
        //     // startSelected = start.clone();
        //     // endSelected = end.clone();
        //     // updateInputs();
        // },

        onbeforeshow: function(instance) {
            instance.config.minDate = moment().startOf('day');
        },

        onfirstselect: function(instance, start) {
            instance.config.minDate = start.add(1, 'days');

            // startSelected = start.clone();
            // instance.globals.startSelected = false;
            // updateInputs();
            // instance.hideDropdown(instance);
            // calentimEnd.showDropdown(calentimEnd);

            // if (startSelected) {
            //     setMinNights(startSelected);

            //     var addDays = getMinstay(startSelected);
            //     // var addDays = minNights;
            //     if (startSelected.hasOwnProperty("_isAMomentObject") && endSelected && endSelected.hasOwnProperty("_isAMomentObject")) {
            //         if (startSelected.isAfter(endSelected, "day")) {
            //             endSelected = startSelected.clone().add(addDays, "days");
            //         }
            //     }
            //     if (startSelected.hasOwnProperty("_isAMomentObject")) {

            //         // if(inquiryCalentimStart && inquiryCalentimEnd){
            //         //     inquiryCalentimStart.config.startEmpty = false;
            //         //     inquiryCalentimStart.config.startDate = startSelected;
            //         //     inquiryCalentimEnd.config.startDate = startSelected;
            //         //     inquiryCalentimEnd.config.minDate = startSelected.clone().add(addDays, 'days');
            //         //     inquiryCalentimStart.$elem.val(startSelected.format(inquiryCalentimStart.config.format));
            //         // }

            //         // calentimDates.config.startEmpty = false;
            //         // calentimDates.config.startDate = startSelected;

            //         calentimDates.config.startDate = startSelected;
            //         calentimDates.config.minDate = startSelected.clone().add(addDays, 'days');

            //         // calentimStart.$elem.val(startSelected.format(calentimStart.config.format));
            //     }
            //     if (endSelected && endSelected.hasOwnProperty("_isAMomentObject")) {
            //         // if(inquiryCalentimStart && inquiryCalentimEnd){
            //         //     inquiryCalentimStart.config.endDate = endSelected;
            //         //     inquiryCalentimEnd.config.endDate = endSelected;
            //         //     inquiryCalentimEnd.$elem.val(endSelected.format(inquiryCalentimEnd.config.format));
            //         // }
            //         calentimDates.config.endDate = endSelected;
            //         // calentimDates.config.endDate = endSelected;
            //         // calentimEnd.$elem.val(endSelected.format(calentimEnd.config.format));
            //     }
            // }
        },
        onlastselect: function(instance, startDate, endDate) {
            $searchWrap.find('.hfy-search-form-row input[name=start_date]').val(startDate.format(hfyDFdef));
            $searchWrap.find('.hfy-search-form-row input[name=end_date]').val(endDate.format(hfyDFdef));
            // calentim.config.startDate = startDate;
            // calentim.config.endDate = endDate;
        },
    });

    function initInquiry()
    {
        let $m = jQuery('.direct-inquiry-modal').last();

        jQuery(document).on('click', '.direct-inquiry-modal-open', function(){
            $m.find('.thx').hide();
            $m.find('.direct-inquiry-modal-content').show();
            $m.hfymodal('show');
        });

        $m.on($.hfymodal.OPEN, function(event, modal) {
            setTimeout(function(){
                let $mc = jQuery('.direct-inquiry-modal-content');
                $mc.find('#inquiry_adults').val(jQuery('.listing-price .guests').val());
                $mc.find('input[name=discount_code]').val(jQuery('.listing-price input[name=discount_code]').val());
                
                // Ensure we have the latest start and end dates from the booking form
                if (jQuery('input[name=start_date]').val()) {
                    startSelected = moment(jQuery('input[name=start_date]').val(), hfyDFdef);
                }
                if (jQuery('input[name=end_date]').val()) {
                    endSelected = moment(jQuery('input[name=end_date]').val(), hfyDFdef);
                }
                
                handleInquiryDatepickers();
            }, 200);
            document.dispatchEvent(new Event('direct-inquiry-modal-opened'), modal);
        });

        jQuery(document).on('change paste keyup', '.direct-inquiry-form :input', function() {
            let errorContainer = jQuery(this).siblings('.error');
            if (errorContainer.length > 0) errorContainer.html('');
            else {
                errorContainer = jQuery(this).parent().siblings('.error');
                if (errorContainer) errorContainer.html('');
            }
        });

        jQuery(document).on('submit', '.direct-inquiry-form', function(e) {
            e.preventDefault();

            let $submitButton = jQuery('.direct-inquiry-modal-submit-button');
            $submitButton.attr('disabled', 'disabled');
            $submitButton.addClass('btn-in-progress');

            let form = jQuery(this);

            let fa = form.serializeArray(), fres = [];
            fa.forEach(function(el){
                if (el.name == 'check_in') {
                    fres.push({ name: el.name, value: fixDateFormat('start_date', el.value) });
                } else if (el.name == 'check_out') {
                    fres.push({ name: el.name, value: fixDateFormat('end_date', el.value) });
                } else {
                    fres.push(el);
                }
            });
            let data = jQuery.param(fres);

            let recaptchaResponse = grecaptcha.getResponse()
            if (recaptchaResponse.length == 0){
                jQuery('#g-recaptcha-error').html('<span style="color:red;"> Required field.</span>');
                $submitButton.removeClass('btn-in-progress');
                $submitButton.removeAttr('disabled');
                return false;
            }

            $.ajax({
                url: hfyx.url,
                data: {
                    action: 'inquiry',
                    data: data
                },
                type: 'POST',
                success: function(result) {
                    if (!!result.success) {
                        jQuery('.direct-inquiry-modal .direct-inquiry-modal-content').hide();
                        jQuery('.direct-inquiry-modal .thx').show();
                    } else {
                        let error = result.msg;
                        if (error) {
                            let errorContainer = jQuery('.direct-inquiry-modal #message_error');
                            if (errorContainer) {
                                errorContainer.html(error);
                                errorContainer.show();
                            }
                            // if (index == 'verifyCode') {
                            //     jQuery('.direct-inquiry-refresh-captcha').addClass('fa-spin');
                            //     jQuery('.direct-inquiry-captcha > img').click();
                            // }
                        }
                    }
                },
                complete: function(result) {
                    $submitButton.removeClass('btn-in-progress');
                    $submitButton.removeAttr('disabled');
                }
            });
        });
    }

    function handleInquiryDatepickers()
    {
        // Ensure we have the latest start and end dates from the booking form
        if (!startSelected && jQuery('input[name=start_date]').val()) {
            startSelected = moment(jQuery('input[name=start_date]').val(), hfyDFdef);
        }
        if (!endSelected && jQuery('input[name=end_date]').val()) {
            endSelected = moment(jQuery('input[name=end_date]').val(), hfyDFdef);
        }
        
        // Initialize the inquiry datepickers
        handleInquiryStart();
        handleInquiryEnd();
        
        // Ensure the values are displayed in the input fields
        if (startSelected && inquiryCalentimStart) {
            inquiryCalentimStart.$elem.val(startSelected.format(inquiryCalentimStart.config.format));
        }
        if (endSelected && inquiryCalentimEnd) {
            inquiryCalentimEnd.$elem.val(endSelected.format(inquiryCalentimEnd.config.format));
        }
    }

    function handleInquiryStart()
    {
        let inq1 = jQuery(".direct-inquiry-modal:last #inquiry_checkin");
        if (inq1.length) {
            let inst1 = inq1.data("calentim");

            if (typeof inst1 !== 'undefined') {
                if (startSelected) {
                    inquiryCalentimStart.config.startDate = startSelected;
                    inquiryCalentimStart.$elem.val(startSelected.format(inquiryCalentimStart.config.format));
                }
                return;
            }

            inq1.calentim({
                startOnMonday: hfyStartOnMonday,
                locale: hfyCurrentLang,
                enableMonthSwitcher: false,
                enableYearSwitcher: false,
                showHeader: false, // isMob,
                showFooter: false,
                showTimePickers: false,
                calendarCount: isMob ? 24 : 2,
                isMobile: isMob,
                isIOS: isIOS,
                format: hfyDFopt,
                showOn:"bottom",
                arrowOn: 'left',
                autoAlign: true,
                oneCalendarWidth: 280,
                continuous: true,
                minDate: moment().startOf('day'),
                onCustomStyles: function(cell, cellMoment, cellDateUnix, cellStyle, minDateUnix, maxDateUnix, currentMonth) {
                    if (isDayDisabledForCheckIn(cellMoment)) {
                        cellStyle += " calentim-disabled-day";
                    }
                    return cellStyle;
                },
                disabledRanges: calDisDates,
                autoCloseOnSelect: !isMob,
                onaftershow: updatePopupTipsIn,
                onaftermonthchange: updatePopupTipsIn,
                onafteryearchange: updatePopupTipsIn,
                oninit: function(instance) {
                    moment.locale(hfyCurrentLang);
                    instance.globals.delayInputUpdate = true;
                    inquiryCalentimStart = instance;
                    // let v = instance.$elem.val();
                    // let d = moment(v, hfyDFdef);
                    // if (d.isSameOrAfter(inquiryCalentimStart.config.minDate, 'day')) {
                    if (startSelected) {
                        inquiryCalentimStart.config.startDate = startSelected;
                        inquiryCalentimStart.$elem.val(startSelected.format(inquiryCalentimStart.config.format));
                    } else {
                        inquiryCalentimStart.$elem.val('');
                    }
                    instance.input.find(".calentim-apply").attr("disabled",false);
                },
                ondraw: ondrawEvent,
                onfirstselect: function(instance, start) {
                    // endSelected = null;
                    startSelected = start.clone();
                    instance.globals.startSelected = false;
                    updateInputs();
                    instance.hideDropdown(instance);

                    // BEGIN open disabled date for end select (calentim.js changes)
                    let filterDays = function(date, maxNights) {
                        let result = false;
                        let dateMax = false;

                        dateMin = date.clone().add(minNights, 'days');
                        if (maxNights)
                            dateMax = moment(date.clone().add(maxNights, 'days'));

                        $.each(instance.config.disabledRanges, function(key, value) {
                            if (dateMin.isBefore(value.start, 'day') || dateMin.isSame(value.start, 'day')) {
                                result = value.start;
                                return false;
                            } else if (dateMin.isBetween(value.start, value.end, "day", "[]")) {
                                result = dateMin;
                                return false;
                            }
                        });

                        if ((!result && dateMax) || (result && dateMax && dateMax.isBefore(result, 'day'))) {
                            result = dateMax;
                        }

                        return result;
                    };
                    let maxDate = typeof maxNights !== 'undefined' ? filterDays(startSelected, maxNights) : null;
                    if (maxDate) {
                        inquiryCalentimEnd.config.maxDate = moment(maxDate);
                    } else {
                        inquiryCalentimEnd.config.maxDate = null;
                    }
                    // END open disabled date for end select

                    let filterDisabled = function(date) {
                        let result = false;
                        $.each(instance.config.disabledRanges, function(key, value) {
                            if (date.isBetween(value.start, value.end, "day", "(]")) {
                                result = value.start;
                                return false;
                            }
                        });
                        return result;
                    };

                    if (endSelected && endSelected.isAfter(inquiryCalentimEnd.config.maxDate, 'day')) {
                        min = startSelected.clone().add(minNights, 'days');
                        if (!filterDisabled(min)) {
                            endSelected = min;
                            inquiryCalentimEnd.config.endDate = min;
                            inquiryCalentimEnd.$elem.val(min.format(inquiryCalentimEnd.config.format));
                            instance.config.endDate = min;
                        } else {
                            endSelected = null;
                            inquiryCalentimEnd.config.endDate = null;
                            inquiryCalentimEnd.$elem.val("");
                            window.history.replaceState('', '', location.pathname);
                            $formListingPrice.find('.prices').html('');
                        }
                    }

                    min = startSelected.clone().add(minNights, 'days');
                    if (endSelected && endSelected.isSameOrBefore(min, 'days')) {
                        endSelected = null;
                        inquiryCalentimEnd.config.endDate = null;
                        inquiryCalentimEnd.$elem.val("");
                        window.history.replaceState('', '', location.pathname);
                        $formListingPrice.find('.prices').html('');
                    }

                    if (!endSelected) {
                        min = startSelected.clone().add(minNights, 'days');
                        if (!filterDisabled(min)) {
                            endSelected = min;
                            inquiryCalentimEnd.config.endDate = min;
                            inquiryCalentimEnd.$elem.val(min.format(inquiryCalentimEnd.config.format));
                            instance.config.endDate = min;
                        } else {
                            instance.config.endDate = startSelected;
                        }
                    }

                    if (!isMob && startSelected && endSelected) {
                        submitForm();
                    }

                    instance.input.find(".calentim-apply").attr("disabled",true);
                    instance.hideDropdown(instance);
                    inquiryCalentimEnd.showDropdown(inquiryCalentimEnd);
                },
                onbeforeselect: function(instance, start, end) {
                    startSelected = start.clone();
                    endSelected = end.clone();
                    updateInputs();
                },
                onbeforeshow: function(instance) {
                    if (startSelected) {
                        instance.config.startEmpty = false;
                        instance.setStart(startSelected);
                        instance.config.startDate = startSelected;
                        instance.$elem.val(startSelected.format(instance.config.format));
                        instance.setDisplayDate(startSelected);
                        if (!endSelected) {
                            instance.setEnd(startSelected)
                            instance.config.endDate = startSelected;
                        }
                    }
                }
            });
        }
    }

    function handleInquiryEnd()
    {
        let inq2 = jQuery(".direct-inquiry-modal:last #inquiry_checkout");
        if (inq2.length) {
            let inst2 = inq2.data("calentim");

            if (typeof inst2 !== 'undefined') {
                if (endSelected) {
                    inquiryCalentimEnd.config.endDate = endSelected;
                    inquiryCalentimEnd.$elem.val(endSelected.format(inquiryCalentimEnd.config.format));
                }
                return;
            }

            inq2.calentim({
                startOnMonday: hfyStartOnMonday,
                locale: hfyCurrentLang,
                enableMonthSwitcher: false,
                enableYearSwitcher: false,
                showHeader: false, // isMob,
                showFooter: false,
                showTimePickers: false,
                calendarCount: isMob ? 24 : 2,
                isMobile: isMob,
                isIOS: isIOS,
                format: hfyDFopt,
                showOn: "bottom",
                arrowOn: 'right',
                autoAlign: true,
                oneCalendarWidth: 280,
                continuous: true,
                autoCloseOnSelect: !isMob,
                onCustomStyles: function(cell, cellMoment, cellDateUnix, cellStyle, minDateUnix, maxDateUnix, currentMonth) {
                    if (isDayDisabledForCheckOut(cellMoment)) {
                        cellStyle += " calentim-disabled-day";
                    }
                    return cellStyle;
                },
                disabledRanges: calDisDates,
                isHotelBooking: true,
                onaftershow: updatePopupTipsOut,
                onaftermonthchange: updatePopupTipsOut,
                onafteryearchange: updatePopupTipsOut,
                oninit: function(instance) {
                    moment.locale(hfyCurrentLang);
                    let filterInitDisabled = function(dateStart, dateEnd) {
                        $.each(instance.config.disabledRanges, function(key, value) {
                            if (dateStart.isBetween(value.start, value.end, "day", "[]") ||
                                dateEnd.isBetween(value.start, value.end, "day", "(]") ||
                                dateStart.isSameOrBefore(value.start, "days") && dateEnd.isSameOrAfter(value.end, "days")) {
                                return true;
                            }
                        });
                        return false;
                    };
                    instance.globals.delayInputUpdate = true;
                    inquiryCalentimEnd = instance;

                    if (startSelected) {
                        setMinNights(startSelected);
                    }

                    if (moment(startSelected).isSameOrAfter(inquiryCalentimStart.config.minDate, 'day') &&
                        moment(startSelected.clone().add(minNights, 'days')).isSameOrBefore(moment(instance.$elem.attr('value'), hfyDFopt), 'days') &&
                        !filterInitDisabled(inquiryCalentimStart.config.startDate, moment(instance.$elem.attr('value'), hfyDFopt))) {
                        endSelected = moment(instance.$elem.attr('value'), hfyDFopt);
                        inquiryCalentimEnd.config.startDate = startSelected;
                        inquiryCalentimEnd.config.endDate = endSelected;
                        inquiryCalentimEnd.$elem.val(endSelected.format(inquiryCalentimEnd.config.format));
                        inquiryCalentimStart.config.endDate = endSelected;

                        let filterDays = function(date, maxNights) {
                            let result = false;
                            let dateMax = false;

                            dateMin = date.clone().add(minNights, 'days');
                            if (maxNights)
                                dateMax = moment(date.clone().add(maxNights, 'days'));

                            $.each(instance.config.disabledRanges, function(key, value) {
                                if (dateMin.isBefore(value.start, 'day') || dateMin.isSame(value.start, 'day')) {
                                    result = value.start;
                                    return false;
                                } else if (dateMin.isBetween(value.start, value.end, "day", "[]")) {
                                    result = dateMin;
                                    return false;
                                }
                            });

                            if ((!result && dateMax) || (result && dateMax && dateMax.isBefore(result, 'day'))) {
                                result = dateMax;
                            }

                            return result;
                        };

                        filterDays(startSelected, typeof maxNights !== 'undefined' ? maxNights : null);
                        if (maxDate)
                            inquiryCalentimEnd.config.maxDate = moment(maxDate);
                    } else {
                        // if (instance.$elem.attr('value').length > 0 && inquiryCalentimStart.$elem.attr('value').length > 0) {
                        //     $formListingPrice.find('.prices').html('<div class="calendar-error">The listing is not available for your selected dates.</div>');
                        // }
                        inquiryCalentimEnd.$elem.val("");
                        inquiryCalentimEnd.config.startDate = null;
                        inquiryCalentimEnd.config.endDate = null;
                        inquiryCalentimEnd.config.startEmpty = true;
                        // endSelected = '';
                        // startSelected = '';
                        inquiryCalentimStart.config.startEmpty = true;
                        inquiryCalentimStart.config.startDate = null;
                        inquiryCalentimStart.config.endDate = null;
                        inquiryCalentimStart.$elem.val("");
                    }
                    inquiryCalentimEnd.config.minDate = inquiryCalentimStart.config.minDate.clone().add(minNights, 'days');
                    updateInputs();
                },
                ondraw: ondrawEvent,
                onfirstselect: function(instance, start) {
                    if (calentimDates.config.endDate)
                        endSelected = start.clone().set({
                            hours: inquiryCalentimStart.config.endDate.hours(),
                            minutes: inquiryCalentimStart.config.endDate.minutes(),
                            seconds: inquiryCalentimStart.config.endDate.seconds()
                        });
                    else
                        endSelected = start.clone();
                    instance.globals.startSelected = false;
                    updateInputs();
                    instance.hideDropdown(null);
                    if (!isMob) submitForm();
                    instance.input.find(".calentim-apply").attr("disabled",false);
                    if (isMob) inquiryCalentimStart.showDropdown(inquiryCalentimStart);
                },
                onbeforeselect: function(instance, start, end) {
                    startSelected = start.clone();
                    endSelected = end.clone();
                    updateInputs();
                },
                onbeforeshow: function(instance) {
                    if (startSelected && endSelected) {
                        instance.config.startEmpty = false;
                        instance.setStart(startSelected);
                        instance.config.startDate = startSelected;
                    }
                    if (startSelected && !endSelected) {
                        instance.setDisplayDate(startSelected);
                    } else {
                        instance.setDisplayDate(endSelected);
                    }
                }
            });
        }
    }

    $('.hfy-listing-availability > div').calentim({
        format: hfyDFopt,
        startOnMonday: hfyStartOnMonday,
        locale: hfyCurrentLang,
        enableMonthSwitcher: false,
        enableYearSwitcher: false,
        showHeader: false,
        showFooter: false,
        showButtons: false,
        startEmpty: true,
        showTimePickers: false,
        calendarCount: isMob ? 1 : 2,
        isMobile: isMob,
        isIOS: isIOS,
        showOn: 'bottom',
        arrowOn: 'center',
        autoAlign: true,
        oneCalendarWidth: 280,
        continuous: true,
        autoCloseOnSelect: !isMob,
        minDate: moment().startOf('day'),
        maxDate: moment().add(1, 'years'),
        disabledRanges: calDisDates,
        inline: true,
        enableKeyboard: false,
        onlastselect: function(instance, startDate, endDate) {
            instance.config.startDate = null;
            instance.globals.firstValueSelected = true;
            instance.globals.endSelected = true;
            instance.updateHeader();
            instance.reDrawCells();
        },
    });

    var resetDate = function(e) {
        e && e.preventDefault();
        startSelected = null;
        if (calentimDates && calentimDates.config) {
            calentimDates.config.startEmpty = true;
            calentimDates.config.startDate = moment().startOf('day');
            calentimDates.config.endDate = moment().startOf('day');
            // calentimEnd.config.startEmpty = true;
            // calentimEnd.config.minDate = moment().startOf('day');
            calentimDates.setStart(moment().startOf('day'));
            calentimDates.$elem.val("");
            calentimDates.setEnd(null);
            // calentimEnd.$elem.val("");
        }
        endSelected = null;
        // window.history.replaceState('', '', location.pathname);
        var href = new URL(window.location.href);
        href.searchParams.set('start_date', '');
        href.searchParams.set('end_date', '');
        window.history.replaceState('', '', href.toString());

        $formListingPrice.find('.prices').html('');
        // jQuery('.booking-price-block .direct-inquiry-modal-open').hide();
    }

    $formWrap.on('click', '.reset-date', resetDate);

    // var $pagination = jQuery('.pagin');

    // if (typeof totalPages === 'undefined') {
    //     var totalPages = 1;
    // }

    // if (totalPages > 1) {
    //     $pagination.twbsPagination(pagiDefaultOpts);
    // }

    function submitForm_() {
        var form = jQuery('.listing-form'),
            formS = form.serialize(),
            loadingScreen = jQuery('.loading-screen');

        loadingScreen.addClass('active');
        var query = '?' + formS;
        var queryObj = $.query.parseNew(query);

        var city_id = queryObj.keys['city_id'];
        delete queryObj.keys['city_id'];
        $.each(queryObj.keys, function(key, value) {
            if (value === true) delete queryObj.keys[key];
        });
        // window.history.replaceState('', '', '/index/' + city_id + queryObj);
        $.ajax({
            url: '/site/filter-listing',
            type: "POST",
            data: formS,
            dataType: 'json',
            success: function(data) {
                if (data['success'] == true) {
                    deleteMarkers();
                    jQuery('.listing-block').html(data['data']);
                    if (data['totalPages'] > 0 && !$.isEmptyObject(data['mapMarkers'])) {
                        mapMarkers = data['mapMarkers'];
                        if (window.map) {
                            checkZoom();
                            addMarkers();
                            showMarkers();
                        }
                    }
                    // if (data['totalPages'] > 1) {
                    //     var $pagination = jQuery('.pagin');
                    //     $pagination.twbsPagination('destroy');
                    //     $pagination.twbsPagination($.extend({}, pagiDefaultOpts, {
                    //         startPage: 1,
                    //         totalPages: data['totalPages']
                    //     }));
                    // }
                }
                // if (data['success'] == true) {
                //     location.reload();
                // } else {
                //     jQuery('.alert-msg').html('<div class="alert alert-danger text-center">'+data['error']+'</div>');
                // }
            },
            error: function() {

            },
            complete: function() {
                loadingScreen.removeClass('active');
            }
        });
    }

    var $filterBar = jQuery('.hfy-search-form-wrap');
    var filterBarTop = $filterBar.offset() ? $filterBar.offset().top : 0;

    if (jQuery(window).scrollTop() + 57 > filterBarTop) {
        $filterBar.addClass('floating');
    }

    jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() + 57 > filterBarTop) {
            $filterBar.addClass('floating');
        } else {
            $filterBar.removeClass('floating');
        }
    });

    // resetDate();





    //

    initInquiry();

    window.doUpdatePriceBlock = submitForm;



    // submitForm();

    // Reviews: View More functionality
    try {
        var reviewsList = document.getElementById('reviews-comments-list');
        var viewMoreBtn = document.getElementById('view-more-reviews');
        if (reviewsList && viewMoreBtn && window.listingReviewsData) {
            var currentCount = parseInt(viewMoreBtn.getAttribute('data-current-count')) || 10;
            var reviewsPerLoad = 5;
            var allReviews = window.listingReviewsData;
            var totalReviews = allReviews.length;
            
            function renderReviews(reviews) {
                reviewsList.innerHTML = '';
                reviews.forEach(function(review) {
                    var guestName = review.name || 'Anonymous';
                    var reviewDate = '';
                    if (review.created) {
                        var d = new Date(review.created);
                        if (!isNaN(d)) {
                            var month = d.toLocaleString('default', { month: 'long' });
                            var year = d.getFullYear();
                            reviewDate = month + ' ' + year;
                        }
                    }
                    var sourceHtml = '';
                    if (window.hfyShowReviewSource && review.source && !window.hfyUseOldApi) {
                        if (review.source === 'airbnb') {
                            sourceHtml = '<span class="review-source"><span style="color: #FF5A5F; font-weight: bold;">• Airbnb</span></span>';
                        } else if (review.source === 'bcom') {
                            sourceHtml = '<span class="review-source"><span style="color: #003580; font-weight: bold;">• Booking.com</span></span>';
                        } else if (review.source === 'vrbo') {
                            sourceHtml = '<span class="review-source"><span style="color: #00A699; font-weight: bold;">• VRBO</span></span>';
                        } else if (review.source === 'internal') {
                            sourceHtml = '<span class="review-source"><span style="color: #767676; font-weight: bold;">• Internal</span></span>';
                        }
                    }
                    
                    // Create guest picture HTML
                    var guestPictureHtml = '';
                    if (review.guest_picture) {
                        guestPictureHtml = '<img src="' + review.guest_picture + '" alt="Guest" class="comment-author">';
                    }
                    
                    // Create rating HTML for inline display
                    var ratingHtml = '';
                    var reviewRating = review.rating || 0;
                    
                    // Determine display rating and max stars (same as PHP template)
                    var displayRating = reviewRating;
                    var maxStars = 5; // Always use 5-star scale for display
                    
                    if (review.source === 'bcom') {
                        displayRating = reviewRating / 2; // Convert 10-star to 5-star
                    }
                    
                    if (reviewRating > 0) {
                        // Calculate star width to match PHP template output (120px scale)
                        var reviewStarRating = Math.round(displayRating * (120 / maxStars)); // Convert to 120px scale to match PHP
                        var starSvg = '<svg width="120" height="20" viewBox="0 0 100 16"><g stroke="none" fill="#E4E5E6" stroke-width="0"><g><polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon></g><g transform="translate(20)"><polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon></g><g transform="translate(40)"><polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon></g><g transform="translate(60)"><polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon></g><g transform="translate(80)"><polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon></g></g></svg>';
                        var filledStarSvg = '<svg width="120" height="20" viewBox="0 0 100 16"><g stroke="none" fill="' + (window.hfyCustomColor || '#FF5A5F') + '" stroke-width="0"><g><polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon></g><g transform="translate(20)"><polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon></g><g transform="translate(40)"><polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon></g><g transform="translate(60)"><polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon></g><g transform="translate(80)"><polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon></g></g></svg>';
                        
                        ratingHtml = '<span class="review-rating-inline">' +
                            '<span class="review-stars">' +
                            starSvg +
                            '<span style="height:20px;width:' + reviewStarRating + 'px">' +
                            filledStarSvg +
                            '</span>' +
                            '</span>' +
                            '<span class="review-rating-number">' + displayRating.toFixed(1) + '</span>' +
                            '</span>';
                    }
                    
                    var reviewHtml = '<div class="reviews-comments-item">' +
                        guestPictureHtml +
                        '<div class="comment-body">' +
                        '<h5>' +
                        '<span class="review-header-left">' + guestName + (reviewDate ? ', <span>' + reviewDate + '</span>' : '') + '</span>' +
                        '<span class="review-header-right">' + ratingHtml + sourceHtml + '</span>' +
                        '</h5>' +
                        '<p class="comment-content">' + (review.comments || '') + '</p>' +
                        '</div>' +
                        '</div>';
                    reviewsList.appendChild(document.createElement('div')).innerHTML = reviewHtml;
                });
            }
            
            viewMoreBtn.addEventListener('click', function() {
                currentCount += reviewsPerLoad;
                var reviewsToShow = allReviews.slice(0, currentCount);
                renderReviews(reviewsToShow);
                viewMoreBtn.setAttribute('data-current-count', currentCount);
                
                if (currentCount >= totalReviews) {
                    viewMoreBtn.style.display = 'none';
                }
            });
        }
    } catch (error) {
        console.error('Reviews JS - Error:', error);
    }

});

function getMinstay(ds)
{
    let ltm = typeof longTermMode !== 'undefined'
        ? longTermMode == 1
        : (typeof hfyltm !== 'undefined' ? hfyltm == 1 : false);
    // if (jQuery('.hfy-page-listing').length <= 0) {
    //     // search
    //     return ltm ? 31 : 1;
    // } else {
        // listing
        if (!ds) ds = new Date().toISOString().slice(0, 10);
        if (typeof ds !== 'string') ds = ds.format('YYYY-MM-DD');

        let minstay = (typeof calendarCustomMinStay !== 'undefined' && typeof calendarCustomMinStay[ds] !== 'undefined')
            ? calendarCustomMinStay[ds] : (typeof hfyminstay !== 'undefined' ? hfyminstay : 1);
        if (typeof calendarOverMinStay !== 'undefined' && typeof calendarOverMinStay[ds] !== 'undefined') {
            minstay = calendarCustomMinStay[ds];
        }

        if (typeof calendarCustomStay !== 'undefined') {
            let dsm = moment(ds);
            jQuery.each(calendarCustomStay, function(key, value) {
                if (dsm.isBetween(value.date_start, value.date_end, "day", "[]")) {
                    minstay = value.min_stay;
                }
            });
        }

        // if (isPropertyPage)
        minstay = (ltm && minstay < 31) ? 31 : minstay;
        return minstay;
    // }
}
