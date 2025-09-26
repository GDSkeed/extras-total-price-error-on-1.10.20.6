
var stripe = typeof hfystripedata !== 'undefined' ? Stripe(hfystripedata.key) : null;

function formAppendHidden(form, name, val) {
    var h = document.createElement("input");
    h.setAttribute("type", "hidden");
    h.setAttribute("name", name);
    h.setAttribute("value", val);
    form.appendChild(h);
}

function stripeTokenHandler(token) {
    var form = document.getElementById("payment-form");
    formAppendHidden(form, "stripeToken", token.id);
    formAppendHidden(form, "start_date", hfystripedata.start_date);
    formAppendHidden(form, "end_date", hfystripedata.end_date);
    formAppendHidden(form, "guests", hfystripedata.guests);
    formAppendHidden(form, "adults", hfystripedata.adults);
    formAppendHidden(form, "children", hfystripedata.children);
    formAppendHidden(form, "infants", hfystripedata.infants);
    formAppendHidden(form, "pets", hfystripedata.pets);
    formAppendHidden(form, "listing_id", hfystripedata.listing_id);
    formAppendHidden(form, "total", hfystripedata.total);
    form.submit();
    return true;
}

function registerElements(ele, formName) {
    var formClass = "." + formName;
    var example = document.querySelector(formClass);

    var form = example.querySelector("form#payment-form");
    var resetButton = example.querySelector(formClass+" a.reset");
    var error = form.querySelector(formClass+" .error");
    var errorMessage = error.querySelector(formClass+" .message");

    function enableInputs() {
        Array.prototype.forEach.call(
            form.querySelectorAll(
                "input[type='text'], input[type='email'], input[type='tel'], textarea"
            ),
            function (input) {
                input.removeAttribute("disabled");
            }
        );
    }

    function disableInputs() {
        Array.prototype.forEach.call(
            form.querySelectorAll(
                "input[type='text'], input[type='email'], input[type='tel'], textarea"
            ),
            function (input) {
                input.setAttribute("disabled", "true");
            }
        );
    }

    // Listen for errors from each Element, and show error messages in the UI.
    ele.forEach(function (element) {
        element.on("change", function (event) {
            if (event.error) {
                error.classList.add("visible");
                errorMessage.innerText = event.error.message;
            } else {
                error.classList.remove("visible");
            }
        });
    });

    // Listen on the form's 'submit' handler...
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Show a loading screen...
        example.classList.add("submitting");
        jQuery('.hfy-payment .pay-btn').attr('disabled', 'disabled');

        // disableInputs();

        // Gather additional customer data we may have collected in our form.
        var name = form.querySelector("#" + formName + "-name");
        var email = form.querySelector("#" + formName + "-email");
        var note = form.querySelector("#" + formName + "-note");
        var zip = form.querySelector("#" + formName + "-zip");
        var additionalData = {
            name: name ? name.value : undefined,
            email: email ? email.value : undefined,
            note: note ? note.value : undefined,
            address_zip: zip ? zip.value : undefined,
        };

        // Use Stripe.js to create a token. We only need to pass in one Element
        // from the Element group in order to create a token. We can also pass
        // in the additional customer data we collected in our form.
        if (stripe) {
            stripe.createToken(ele[0], additionalData).then(function (result) {
                if (result.error) {
                    example.classList.remove("submitting");
                    jQuery('.hfy-payment .pay-btn').removeAttr('disabled');
                    // Inform the customer that there was an error.
                    var errorElement = document.getElementById("card-errors");
                    errorElement.textContent = result.error.message;
                    error.classList.add("visible");
                    enableInputs();
                } else {
                    error.classList.remove("visible");
                    stripeTokenHandler(result.token);
                }
            });
        }
        return true;
    });
}

(function () {
    "use strict";

    if (stripe) {

        var elements = stripe.elements({
            // fonts: [{cssSrc: '//fonts.googleapis.com/css?family=Source+Code+Pro'}],
            // Stripe's examples are localized to specific languages, but if
            // you wish to have Elements automatically detect your user's locale,
            // use `locale: 'auto'` instead.
            locale: window.__exampleLocale,
        });

        // Floating labels
        var inputs = document.querySelectorAll(".hfy-payment .input");
        Array.prototype.forEach.call(inputs, function (input) {
            input.addEventListener("focus", function () {
                input.classList.add("focused");
            });
            input.addEventListener("blur", function () {
                input.classList.remove("focused");
            });
            input.addEventListener("keyup", function () {
                if (input.value.length === 0) {
                    input.classList.add("empty");
                } else {
                    input.classList.remove("empty");
                }
            });
        });

        var elementStyles = {
            base: {
                color: "#32325D",
                fontWeight: 500,
                fontFamily: "Consolas, Menlo, monospace",
                fontSize: "16px",
                fontSmoothing: "antialiased",
                "::placeholder": { color: "#CFD7DF" },
                ":-webkit-autofill": { color: "#e39f48" },
            },
            invalid: {
                color: "#E25950",
                "::placeholder": { color: "#FFCCA5" },
            },
        };

        var elementClasses = {
            focus: "focused",
            empty: "empty",
            invalid: "invalid",
        };

        var cardCvc = elements.create("cardCvc", {
            style: elementStyles,
            classes: elementClasses,
        });
        cardCvc.mount("#hfy-payment-card-cvc");

        var cardExpiry = elements.create("cardExpiry", {
            style: elementStyles,
            classes: elementClasses,
        });
        cardExpiry.mount("#hfy-payment-card-expiry");

        var cardNumber = elements.create("cardNumber", {
            style: elementStyles,
            classes: elementClasses,
        });
        cardNumber.mount("#hfy-payment-card-number");

        registerElements([cardNumber, cardExpiry, cardCvc], "hfy-payment");
    }

})();

//
var prform = document.getElementById('payment-response');
if (prform && stripe) {
    prform.addEventListener('submit', function(e) {
        e.preventDefault();

        formAppendHidden(this, 'start_date', hfystripedata.start_date);
        formAppendHidden(this, 'end_date', hfystripedata.end_date);
        formAppendHidden(this, 'guests', hfystripedata.guests);
        formAppendHidden(this, 'adults', hfystripedata.adults);
        formAppendHidden(this, 'children', hfystripedata.children);
        formAppendHidden(this, 'infants', hfystripedata.infants);
        formAppendHidden(this, 'pets', hfystripedata.pets);
        formAppendHidden(this, 'listing_id', hfystripedata.listing_id);
        formAppendHidden(this, 'pname', hfystripedata.name);
        formAppendHidden(this, 'pemail', hfystripedata.email);
        formAppendHidden(this, 'pphone', hfystripedata.phone);
        formAppendHidden(this, 'zip', hfystripedata.zip);

        var x = document.createElement('textarea');
        x.setAttribute('style', 'visibility:hidden');
        x.setAttribute('name', 'note');
        x.textContent = hfystripedata.note;
        this.appendChild(x);

        this.submit();
        return true;
    });
}
