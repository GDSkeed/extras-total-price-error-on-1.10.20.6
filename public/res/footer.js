!(function ($) {

    $(document).ready(function () {
        init();
    });

    // wishlist

	function sendWish(action, id, t) {
		if (id) {
			$(t).addClass('loading');
			$.ajax({
				url: hfyx.url,
				data: {
					action: 'wish',
					data: { action, id }
				},
				type: 'POST',
				success: function(res) {
					$(t).hide();
					if (action == 'remove') {
						$(t).removeClass('added-to-wish').addClass('add-to-wish');
					} else {
						$(t).removeClass('add-to-wish').addClass('added-to-wish');
					}
				},
				error: function(e) {
					console.log('err',e);
				},
				complete: function(res) {
					$(t).removeClass('loading').fadeIn();
				}
			});
		}
	}

	function handleWish(e) {
		e.preventDefault();
		sendWish(
			$(e.target).hasClass('added-to-wish') ? 'remove' : 'add',
			$(e.target).data('id'),
			e.target
		);
		return false;
	}

    function priceSlider()
    {
        $(".price-slider").ionRangeSlider({
            min: 130,
            max: 575,
            type: 'double',
            prefix: "$",
            prettify: false,
            hasGrid: true
        });
    }

    function iCheck()
    {
        $('.i-check, .i-radio').iCheck({
            checkboxClass: 'icheckbox_square-aero',
            radioClass: 'iradio_square-aero',
        });
    }

    function countdown()
    {
        let element = $('.countdown');
        element.countdown(element.attr('data-date'), function(event) {
            $(this).html(
                event.strftime("<div class='v'>%D <span>days</span></div><div class='s'>:</div><div class='v'>%H <span>hours</span></div><div class='s'>:</div><div class='v'>%M <span>minit</span></div><div class='s'>:</div><div class='v'>%S <span>Second</span></div>")
            );
        });
    }

    function addOrUpdateField($frm, fname, val)
	{
		if ($frm.length) {
			let $f = $frm.find('input[name='+fname+']');
			if ($f.length) {
				$f.val(''+val)
			} else {
				$frm.append($('<input type="hidden" />').attr('name', fname).val(''+val));
			}
		}
	}

    function init()
    {
        // if ($('.i-check').length || $('.i-radio').length) {
        //     iCheck();
        // }

        if ($('.countdown').length) {
            countdown();
        };

        // gallery single listing

        var $hfygal = $('.hfy-listing-gallery.hfy-lg');
        $hfygal.lightGallery({
            dynamic: false,
            mode: "lg-fade",
            download: false,
            zoom: false,
            share: false,
            selector: '.img-wrap'
        });

        if ($('.open-gallery-by-click').length) {
            $('.open-gallery-by-click').on('click', function(e){
                e.preventDefault();
                //$hfygal.data('lightGallery').show();
                $hfygal.data('lightGallery').$el.find(':first').click();
            });
        }

        // ltm selector

        $('.ltm-selector input').on('click', function(){
            $(this).parents('.ltm-selector').first().find('.btn.active').removeClass('active');
            $(this).parent().addClass('active');
        });

        // show more amenities

        $('.hfy-listing-amenities .hfy-am--more.do-action').on('click', function(){
            $(this).hide();
            $(this).siblings().removeClass('hidden');
        });

        $('.add-to-wish, .added-to-wish').on('click', handleWish);

        //

        $('.sort-controls-wrap select').on('change', function(e){
            $(this).attr('disabled', 'disabled');
            var href = new URL(window.location.href);
            href.searchParams.set('sort', e.target.value || '');
            window.location.href = href.toString();
        });

        // user booking manage

        $(document).on('click', '#btn-cancel-booking', function(){
            var $m = $('.cancel-booking-modal:first');
            $m.hfymodal('show');
            return false;
        });

        $(document).on('click', '.cancel-booking-modal input.btn', function(e){
            e.preventDefault();
            var $f = $(this).parents('form').first();
            var t1 = $f.find('select.reason option:selected').text();
            var t2 = $f.find('textarea').val();
            var s = ('' + t1 + "\n" + t2).trim();
            if (s.length > 2) {
                $f.find('.error').hide();
                $f.addClass('loading');
                $.ajax({
                    url: hfyx.url,
                    data: {
                        action: 'res_cancel',
                        data: {
                            rid: $f.find('[name=reservation_id]').val(),
                            msg: s
                        }
                    },
                    type: 'POST',
                    success: function(res) {
                        if (res == 1) {
                            $f.find('.error1').show();
                            $f.removeClass('loading');
                            return;
                        } else if (res == 'ok') {
                            // document.location.href = '/my-bookings/';
                            $f.find('.ok').show();
                            $f.find('.um-button').hide();
                            return;
                        }
                        // console.info(res);
                        $f.find('.error2').show();
                        $f.removeClass('loading');
                    },
                    error: function(e) {
                        console.error(e);
                        $f.find('.error2').show();
                        $f.removeClass('loading');
                    }
                });
            } else {
                $f.find('.error1').show();
            }
            return false;
        });

        // extras

        $(document).on('click', '.hfy-wrap.payment-extras-set', function(e){
            e.preventDefault();
            $('.hfy-wrap.payment-extras-set').not(this).removeClass('selected');
            let x = $(this).toggleClass('selected');
            let ids = x.hasClass('selected') ? $(this).data('ids') : '';
            addOrUpdateField($('.hfy-listing-booking-form form'), 'extrasSet', ids);
        });

        $(document).on('click', '.hfy-wrap.payment-extras-optional .payment-extras-optional-item', function(e){
            let x = $(this).toggleClass('selected');
            let ids = [];
            $(this).parent().find('.payment-extras-optional-item').each(function(){
                ids.push([
                    $(this).data('id'),
                    $(this).hasClass('selected') ? '1' : '0'
                ].join(':'));
            });
            addOrUpdateField($('.hfy-listing-booking-form form'), 'extrasOptional', ids.join());
        });

        // $('.hfy-wrap.payment-extras-optional .payment-extras-optional-item').click();

        var eodelay;
        $('.hfy-wrap.payment-wrapper .extras-optional-wrap input[type=checkbox]').click(function(){
            let $els = $(this).parents('.extras-optional-wrap').first().find('input[type=checkbox]');
            $newPrm = [];
            $newFees = [];
            $els.each(function($x){
                let ch = $(this).prop('checked');
                $newPrm.push([$(this).val(), ch ? '1' : '0'].join(':'));
                if (ch) $newFees.push($(this).val());
            });
            setUrlParam('extrasOptional', $newPrm);
            $('.hfy-wrap input#fees').val($newFees.join(','));

            clearTimeout(eodelay);
            eodelay = setTimeout(function(){
                $('.hfy-wrap #roomInfoSection, .hfy-payment-steps').addClass('submitting');
                // window.location.reload();
                jQuery.ajax({
                    type: 'POST',
                    data: { action: 'payment_preview', data: getUrlParameters(window.location.href) },
                    url: hfyx.url,
                    success: function(result) {
                        $('.hfy-wrap #roomInfoSection').parent().html(result);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log('error', { xhr, ajaxOptions, thrownError });
                    },
                    complete: function(result) {
                        $('.hfy-wrap #roomInfoSection, .hfy-payment-steps').removeClass('submitting');
                    }
                });

            }, 600);
        });

        $('.hfy-wrap .extras-optional input[type=checkbox]').each(function(){
            $(this).wrap( "<span class='styled-checkbox'></span>" );
            if ($(this).is(':checked')) $(this).parent().addClass("selected");
            $(this).on('click', function(){
                $(this).parent().toggleClass("selected");
            });
        });

        initListingsSliders($);

        // $('.nav.nav-tabs .nav-item > *').on('click', function(e){
        //     e.preventDefault();
        //     let id = $(this).data('target');
        //     let $x = $('.tab-content').find(id);
        //     $x.parent().find('.tab-pane').removeClass('active').hide();
        //     $x.addClass('active').show();
        //     return false;
        // });

        if (typeof $.fn.SumoSelect !== 'undefined' && typeof entloctxt !== 'undefined') {
            let searchText = entloctxt || 'Enter location...';
            let $sf = $('.hfy-search-form-wrap .search-place');
            $sf.SumoSelect({
                showTitle: false,
                isFloating: false,
                forceCustomRendering: true,
                search: true,
                searchText: searchText,
                placeholder: searchText,
                searchFn: function(str, needle) {
                    return str.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase().indexOf(needle.toLowerCase()) < 0;
                },
            });
            
            // Handle selection change to populate custom_search field
            $sf.on('sumo:closed', function(sumo) {
                let customSearch = $('input.custom-search');
                if (customSearch.length) {
                    let selectedValue = sumo.target.value;
                    let selectedText = sumo.target.sumo.placeholder.trim();
                    
                    // Update the custom_search field with the selected text
                    customSearch.val(selectedText);
                    
                    // Don't set the neighbourhood field - we want keyword search behavior
                    // This allows the map to work with custom_search instead of conflicting neighbourhood
                }
            });
        }

        $('body').on('click', '.hfy-ctrl-hide-map', function(){ showHideMap(0) });
        $('body').on('click', '.hfy-ctrl-show-map', function(){ showHideMap(1) });

        if (isMob) {
            $('.hfy-listings-map-toggle-mobile').show();
        }

    	if (isTablet) {
            $('.hfy-listings-map-toggle-tablet').show();
        }

        if (window.listingsNoResult && window.listingsNoResult == true) {
            // showHideMap(0)
        } else {
            if (isMob || isTablet) {
                showHideMap(0, true)
            } else {
                if (!$('body').hasClass('listings-map-hidden')) {
                    showHideMap(1, false);
                }
            }
        }

        $('.reviews-show-more').hideMaxListItems({ 'max': 10, 'itemSelector': '.review-item' });

        $(document).on("click", ".more-btn", function (e) {
            e.stopPropagation();
            let parent = $(this).parent();
            let text = $(parent).siblings('div');
            parent.remove();
            text.removeAttr("style");
        });

        //

    }

})(jQuery);
