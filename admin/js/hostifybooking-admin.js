"use strict";

(function ($) {
    $(window).load(function () {

		$('.exopite-sof-content .ctcc').on('click', function(){
            var t = $(this);
            copyToClipboard(t.data('copy') ? t.data('copy') : t.text());
		})

        $('.hfy-admin-toolbar .hfy-clear-cache-btn, .hostifybooking-plugin-options .hfy-clear-cache-btn').on('click', function(){
            $(this).html('<span class="dashicons dashicons-update animate-icon"></span> Wait please...');
            $('.hfy-clear-cache-btn').addClass('disabled');
            $('.button-primary.exopite-sof-submit-button-js').addClass('disabled');
            $('#wpbody header .exopite-sof-ajax-message, #message, .notice.notice-error, .notice.notice-warning, .hfy-notice-connected').hide();
            $('.hostifybooking-plugin-options .check-connection-hfy').addClass('disabled').html('<span class="dashicons dashicons-update animate-icon"></span> Checking connection...');
        })

        $('.hostifybooking-plugin-options #hostifybooking-plugin-save').on('click', function(){
            let b = $('.hfy-admin-toolbar .hostifybooking-plugin-save-atb');
            b.addClass('disabled');
            b.val('Saving...');
            $('.hfy-admin-toolbar .hfy-clear-cache-btn').attr('disabled', true);
            $('.hostifybooking-plugin-options .hfy-notice-connected').hide();
            // setTimeout(function(){
            //     $('.hostifybooking-plugin-options .check-connection-hfy').show();
            //     b.removeClass('disabled');
            //     b.val('Save settings');
            // }, 1000);
        });

        $(document).on('hfy-admin-options-saved', function(res) {
            window.location.reload();
        });

        $('.hfy-admin-toolbar .hostifybooking-plugin-save-atb').on('click', function(){
            $('.hostifybooking-plugin-options #hostifybooking-plugin-save').click();
            return false;
        });

        let href = new URL(window.location.href);
        if (href.hash && href.hash.length) {
            let $section = $('.settings_page_hostifybooking-plugin .exopite-sof-nav-list #section_'+(href.hash.replace('#','')));
            if ($section.length) {
                $section.click();
            }
        }

    });
})(jQuery);

const copyToClipboard = (str) => {
    const el = document.createElement("textarea");
    el.value = str;
    el.setAttribute("readonly", "");
    el.style.position = "absolute";
    el.style.left = "-9999px";
    document.body.appendChild(el);
    const selected = document.getSelection().rangeCount > 0 ? document.getSelection().getRangeAt(0) : false;
    el.select();
    document.execCommand("copy");
    document.body.removeChild(el);
    if (selected) {
        document.getSelection().removeAllRanges();
        document.getSelection().addRange(selected);
    }
};
