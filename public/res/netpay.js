jQuery(document).ready(function() {
    jQuery("#payment-name").on("keyup change", function(e) {
        let inputValue = jQuery(this).val();
        if (inputValue.length > 0) {
            jQuery("#payment-name-label").text("");
        } else {
            jQuery("#payment-name-label").text("Name");
        }
    });

    jQuery("#payment-email").on("keyup change", function(e) {
        let inputValue = jQuery(this).val();
        if (inputValue.length > 0) {
            jQuery("#payment-email-label").text("");
        } else {
            jQuery("#payment-email-label").text("Email");
        }
    });

    jQuery("#payment-phone").on("keyup change", function(e) {
        let inputValue = jQuery(this).val();
        if (inputValue.length > 0) {
            jQuery("#payment-phone-label").text("");
        } else {
            jQuery("#payment-phone-label").text("Phone");
        }
    });

    jQuery("#payment-address").on("keyup change", function(e) {
        let inputValue = jQuery(this).val();
        if (inputValue.length > 0) {
            jQuery("#payment-address-label").text("");
        } else {
            jQuery("#payment-address-label").text("Address");
        }
    });

    jQuery("#payment-country").on("change", function(e) {
        let inputValue = jQuery(this).val();
        if (inputValue.length > 0 ) {
            if (inputValue === "MX") {
                jQuery("#payment-state-field").css('display', 'none').attr("required", false);
                jQuery("#payment-state-us-field").css('display', 'none').attr("required", false);
                jQuery("#payment-state-mx-field").css('display', 'flex').attr("required", false);
            } else if (inputValue === "US") {
                jQuery("#payment-state-field").css('display', 'none').attr("required", false);
                jQuery("#payment-state-us-field").css('display', 'flex').attr("required", true);
                jQuery("#payment-state-mx-field").css('display', 'none').attr("required", false);
            } else {
                jQuery("#payment-state-field").css('display', 'flex').attr("required", false);
                jQuery("#payment-state-us-field").css('display', 'none').attr("required", false);
                jQuery("#payment-state-mx-field").css('display', 'none').attr("required", false);
            }
        }
    });

    jQuery("#payment-city").on("keyup change", function(e) {
        let inputValue = jQuery(this).val();
        if (inputValue.length > 0) {
            jQuery("#payment-city-label").text("");
        } else {
            jQuery("#payment-city-label").text("City");
        }
    });

    jQuery("#payment-state").on("input", function() {
        let inputValue = jQuery(this).val().toUpperCase();
        if (inputValue.length > 0) {
            let newText = inputValue.replace(/[^A-Z]/g, ''); // Remove non-alphabetic characters
            jQuery("#payment-state-label").text("");
            jQuery(this).val(newText.slice(0, 2));
        } else {
            jQuery("#payment-state-label").text("State");
        }
    });

    jQuery("#payment-zip").on("keyup change", function(e) {
        let inputValue = jQuery(this).val();
        if (inputValue.length > 0) {
            jQuery("#payment-zip-label").text("");
        } else {
            jQuery("#payment-zip-label").text("ZIP");
        }
    });
});
