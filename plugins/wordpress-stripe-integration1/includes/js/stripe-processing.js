( function( $ ) {
    Stripe.setPublishableKey(stripe_vars.publishable_key);

    function stripeResponseHandler(status, response) {
        if (response.error) {
            // show errors returned by Stripe
            jQuery(".payment-errors").html(response.error.message);

            // re-enable the submit button
            jQuery('#stripe-submit').attr("disabled", false);
        } else {
            var form$ = jQuery("#stripe-cashout-form");

            // token contains id, last4, and card type
            var token = response['id'];

            // insert the token into the form so it gets submitted to the server
            form$.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");

            // and submit
            form$.get(0).submit();
        }
    }
    function reportError(msg) {

        // Show the error in the form:
        $('.payment-errors').text(msg).addClass('error');

        // Re-enable the submit button:
        $('#stripe-submit').prop('disabled', false);

        return false;

    }

    $( document ).ready( function() {
        $('#payout_destination').change(function(){
            var $this = $(this), $val = $(this).val();

            $('.payout_destination').hide();
            $('.payout_destination.'+$val).show();

        });

        $("#stripe-cashout-form").submit(function(event) {

            event.preventDefault();

            // Disable the submit button to prevent repeated clicks
            $('#stripe-submit').attr("disabled", "disabled");

            var error = false;

            var payout_type = $('#payout_destination').val();

            switch (payout_type) {
                case 'stripe':

                    break;
                case 'credit':

                    // Get the values:
                    var ccNum = $('.card-number').val(),
                        cvcNum = $('.card-cvc').val(),
                        expMonth = $('select.card-expiry-month').val(),
                        expYear = $('select.card-expiry-year').val(),
                        name = $('.card-name-first').val()+" "+$('.card-name-last').val();

                    // Validate the number:
                    if (!Stripe.card.validateCardNumber(ccNum)) {
                        error = true;
                        reportError('The credit card number appears to be invalid.');
                    }

                    // Validate the CVC:
                    if (!Stripe.card.validateCVC(cvcNum)) {
                        error = true;
                        reportError('The CVC number appears to be invalid.');
                    }

                    // Validate the expiration:
                    if (!Stripe.card.validateExpiry(expMonth, expYear)) {
                        error = true;
                        reportError('The expiration date appears to be invalid.');
                    }

                    // Validate the Name:
                    if (name == '') {
                        error = true;
                        reportError('Name must be filled out.');
                    }else if ( !/^[A-Za-z\s]+$/.test(name)){
                        error = true;
                        reportError("The Name appears to be invalid.");
                    }

                    // Send the card details to Stripe
                    if ( !error ) {

                        Stripe.card.createToken({
                            number: ccNum,
                            cvc: cvcNum,
                            exp_month: expMonth,
                            exp_year: expYear,
                            name: name,
                            currency: 'usd'
                        }, stripeResponseHandler);
                    }

                    break;

                case 'bank':
                    // Get the values:
                    var routNum = $('.br_nm').val(),
                        accNum = $('.ba_nm').val(),
                        accName = $('.acc_hold_nm').val();

                    // Validate the number:
                    if (!Stripe.bankAccount.validateRoutingNumber(routNum)) {
                        error = true;
                        reportError('Invalid bank routing number.');
                    }

                    // Validate the CVC:
                    if (!Stripe.bankAccount.validateAccountNumber(accNum, 'US')) {
                        error = true;
                        reportError('Invalid bank account number.');
                    }

                    // Validate the Name:
                    if (name == '') {
                        error = true;
                        reportError('Name must be filled out.');
                    }else if ( !/^[A-Za-z\s]+$/.test(name)){
                        error = true;
                        reportError("The Name appears to be invalid.");
                    }

                    Stripe.bankAccount.createToken({
                        country: 'us',
                        currency: 'usd',
                        routing_number: routNum,
                        account_number: accNum,
                        account_holder_name: accName,
                        account_holder_type: 'individual'
                    }, stripeResponseHandler);

                    break;

            }

            // prevent the form from submitting with the default action
            return false;
        });
    });
} )( jQuery );