<?php
    $id = 'sub-checkout-' . $plan->_id . '-' . (time() * rand(1, 1000000));
?>

<div class="content-wrap zype-form-center" id="<?php echo $id; ?>">
    <?php if (!empty($error)): ?>
        <div id="choose-wrapper">
            <div class="main-heading inner-heading">
                <h1 class="title text-uppercase zype-title"><?php echo $error; ?></h1>
            </div>
            <div class="user-wrap">
                <div class="holder-main">
                    <div class="row">
                        <div class="">
                            <button type="button" class="zype_auth_markup zype-button" data-type="plans">Go back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div id="payment-wrapper">
            <div class="main-heading inner-heading">
                <h1 class="title text-uppercase zype-title">Enter your billing info</h1>
            </div>
            <div class="user-wrap">
                <div class="holder-main">
                    <div class="row payment-row">
                        <div class="">
                            <input type="hidden" name="action" value="zype_plans">

                            <div class="holder">
                                <input type="hidden" name="action" value="zype_checkout">
                                <form id="payment-form">
                                    <input name="plan_id" type="hidden" value="<?php echo $plan->_id; ?>">
                                    <input name="email" type="hidden" value="<?php zype_current_consumer(); ?>">
                                    <input name="stripe_card_token" type="hidden">
                                    <input name="braintree_payment_nonce" type="hidden">

                                    <p class="checkout_error" style='color: red'></p>
                                    <?php if (!empty($braintree_token)): ?>
                                        <input name="type" type="hidden" value="braintree">
                                        <div id="braintree-form"></div>
                                    <?php elseif (!empty($plan->stripe_id)): ?>
                                        <input name="type" type="hidden" value="stripe">
                                        <div id="stripe-form">
                                            <p class="form-group required-row zype-input-wrap">
                                                <input type="text"
                                                       placeholder="Card number"
                                                       class="zype-input-text zype-card-number">
                                            </p>
                                            <p class="form-group required-row zype-input-wrap">
                                                <input maxlength="4"
                                                       oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');"
                                                       type="text" placeholder="CVC"
                                                       class="zype-input-text zype-card-cvc">
                                                <input type="text" placeholder="MM/YY"
                                                       class="zype-input-text zype-card-date">
                                            </p>
                                            <p class="form-group zype-input-wrap">
                                                <input type="text"
                                                        name="stripe_coupon_code"
                                                        placeholder="Coupon code"
                                                        class="zype-input-text zype-stripe-coupon">
                                            </p>
                                        </div>
                                    <?php endif ?>

                                    <div class="zype-buttons-row">
                                        <div class="zype-buttons-column">
                                            <button type="button" class="zype_auth_markup zype-button"
                                                    data-type="plans"
                                                    data-root-parent="<?php echo $root_parent; ?>">Go back
                                            </button>
                                        </div>

                                        <div class="zype-buttons-column">
                                            <button type="submit" class="zype-checkout-button zype-button"
                                                    data-description="<?php echo $plan->name; ?>"
                                                    data-interval="<?php echo $plan->interval; ?>"
                                                    data-amount="<?php echo $plan->amount; ?>" disabled>Continue
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<script>
    var rootParent = "#<?php echo $root_parent ?>";
    var id = "#<?php echo $id; ?>"
    var cardDateSelector = [rootParent, id, '.zype-card-date'].join(' ');
    var cardNumberSelector = [rootParent, id, '.zype-card-number'].join(' ');
    var checkoutButtonSelector = [rootParent, id, '.zype-checkout-button'].join(' ');
    var paymentFormSelector = [rootParent, id, '#payment-form'].join(' ');
    var checkoutErrorSelector = [rootParent, id, '.checkout_error'].join(' ');
    var spinnerSelector = [rootParent, id, '.zype-spinner'].join(' ');
    var paymentWrapperSelector = [rootParent, id, '#payment-wrapper'].join(' ');
    var titleSelector = [paymentWrapperSelector, '.main-heading', '.title'].join(' ');
    var paymentRowSelector = [paymentWrapperSelector, '.payment-row'].join(' ');

    function mask() {
        return [
            // american express
            {
                mask: '0000 000000 00000',
                regex: '^3[47]\\d{0,13}',
                lazy: false
            },
            // discover
            {
                mask: '0000 0000 0000 0000',
                regex: '^(?:6011|65\\d{0,2}|64[4-9]\\d?)\\d{0,12}',
                lazy: false
            },
            // diners
            {
                mask: '0000 000000 0000',
                regex: '^3(?:0([0-5]|9)|[689]\\d?)\\d{0,11}',
                lazy: false
            },
            // mastercard
            {
                mask: '0000 0000 0000 0000',
                regex: '^(5[1-5]\\d{0,2}|22[2-9]\\d{0,1}|2[3-7]\\d{0,2})\\d{0,12}',
                lazy: false
            },
            // jcb15
            {
                mask: '0000 000000 00000',
                regex: '^(?:2131|1800)\\d{0,11}',
                lazy: false
            },
            // jcb
            {
                mask: '0000 0000 0000 0000',
                regex: '^(?:35\\d{0,2})\\d{0,12}',
                lazy: false
            },
            // maestro
            {
                mask: '0000 0000 0000 0000',
                regex: '^(?:5[0678]\\d{0,2}|6304|67\\d{0,2})\\d{0,12}',
                lazy: false
            },
            // visa
            {
                mask: '0000 0000 0000 0000',
                regex: '^4\\d{0,15}',
                lazy: false
            },
            // unionpay
            {
                mask: '0000 0000 0000 0000',
                regex: '^62\\d{0,14}',
                lazy: false
                // Unknown
            },
            {
                mask: '0000 0000 0000 0000',
                lazy: false
            }
        ];
    };

    jQuery(document).ready(function ($) {
        <?php if (!empty($braintree_token)): ?>
            var ifFastPay = true;
            var payloadNonce = false;
            braintree.dropin.create({
                authorization: '<?php echo $braintree_token ?>',
                container: '#braintree-form',
                paypal: {
                    flow: 'vault'
                }
            }, function (createErr, instance) {
                if (createErr) {
                    console.error(createErr);
                    return;
                }

                $(checkoutButtonSelector).prop('disabled', false);

                instance.on('noPaymentMethodRequestable', function (event) {
                });

                instance.on('paymentOptionSelected', function (event) {
                    if (event.paymentOption) {
                        payloadNonce = false;
                        ifFastPay = false;
                    }
                });

                instance.on('paymentMethodRequestable', function (event) {
                    if (!event.paymentMethodIsSelected) {
                        payloadNonce = false;
                    }
                });

                $(checkoutButtonSelector).click(function (e) {
                    e.preventDefault();

                    $(this).append('<i class="zype-spinner"></i>');
                    $(checkoutButtonSelector).prop('disabled', true);

                    if (ifFastPay && instance.isPaymentMethodRequestable() && !payloadNonce) {
                        ifFastPay = false;
                        instance.requestPaymentMethod(function (requestPaymentMethodErr, payload) {
                            if (payload && typeof payload.nonce != 'undefined') {
                                payloadNonce = payload.nonce;
                                $(paymentFormSelector).find('input[name="braintree_payment_nonce"]').val(payload.nonce);
                                sendPaymentRequest();
                            } else {
                                $(checkoutButtonSelector).prop('disabled', false);
                                $(spinnerSelector).remove();
                            }
                        });

                        return;
                    }

                    ifFastPay = false;

                    if (payloadNonce) {
                        sendPaymentRequest();
                    } else {
                        instance.requestPaymentMethod(function (requestPaymentMethodErr, payload) {
                            if (payload && typeof payload.nonce != 'undefined') {
                                payloadNonce = payload.nonce;
                                $(paymentFormSelector).find('input[name="braintree_payment_nonce"]').val(payload.nonce);
                            }

                            $(checkoutButtonSelector).prop('disabled', false);
                            $(spinnerSelector).remove();
                        });
                    }
                });
            });

        <?php elseif (!empty($plan->stripe_id)): ?>
            var cardnumberMask = new IMask($(cardDateSelector)[0], {
                mask: 'MM/YY',
                blocks: {
                    MM: {
                        mask: IMask.MaskedRange,
                        from: 1,
                        to: 12
                    },
                    YY: {
                        mask: '00',
                    }
                }
            });
            var cardnumberMask = new IMask($(cardNumberSelector)[0], {
                mask: mask(),
                dispatch: function (appended, dynamicMasked) {
                    var number = (dynamicMasked.value + appended).replace(/\D/g, '');

                    return dynamicMasked.compiledMasks.find(function (m) {
                        var re = new RegExp(m.regex);
                        return number.match(re) !== null;
                    });
                }
            });

            Stripe.setPublishableKey('<?php echo $stripe_pk ?>');
            $(checkoutButtonSelector).prop('disabled', false);

            $(checkoutButtonSelector).click(function (e) {
                e.preventDefault();
                var stripeForm = $(this).closest('#payment-form').children('#stripe-form');

                $(this).prop('disabled', true).append('<i class="zype-spinner"></i>');

                var cardDate = stripeForm.find('.zype-card-date').val();
                var cardNumber = stripeForm.find('.zype-card-number').val();
                var cardCVC = stripeForm.find('.zype-card-cvc').val();

                Stripe.card.createToken({
                    number: cardNumber,
                    cvc: cardCVC,
                    exp_month: cardDate.split('/')[0],
                    exp_year: cardDate.split('/')[1],
                }, stripeTokenHandler);

                return false;
            });

            function stripeTokenHandler(status, response) {
                if (response.error) {
                    $(checkoutErrorSelector).text(response.error.message);
                    $(checkoutButtonSelector).prop('disabled', false);
                    $(spinnerSelector).remove();
                } else {
                    $(paymentFormSelector).find('input[name="stripe_card_token"]').val(response.id);
                    sendPaymentRequest();
                }
            }
        <?php endif ?>

        function sendPaymentRequest() {
            $(checkoutErrorSelector).text('');

            $.ajax({
                url: "<?php zype_url('subscribe');?>/submit",
                type: 'post',
                data: $(paymentFormSelector).serialize(),
                dataType: 'json',
                encode: true
            }).done(function (data) {
                if (typeof data.errors != 'undefined') {
                    $.each(data.errors, function (index, value) {
                        $(checkoutErrorSelector).append(value + "<br/>");
                    });
                    $(checkoutButtonSelector).prop('disabled', false);
                    $(spinnerSelector).remove();
                    return;
                }
                if (data.success) {
                    $(titleSelector).text('Thanks for your payment!');
                    $(paymentRowSelector).html('<p class="to-sign-up">You\'ve successfully unlocked your content. Enjoy!</p><button class="zype-button" id="zype_modal_close">Let\'s starting watching</button><input type="hidden" class="close_reload" value="reload">');
                }
                $(checkoutButtonSelector).prop('disabled', false);
                $(spinnerSelector).remove();
             }).fail(function (data) {
                $(checkoutButtonSelector).prop('disabled', false);
                $(spinnerSelector).remove();
                console.log(data.errors);
            });
        }

        $(document).on('click', '#zype_modal_close', function(e) {
            e.preventDefault();
            var url = '<?php echo $redirect_url ?>';
            if (url.length > 0) {
                window.location.replace(url);
            } else {
                window.location.reload();
            }
        });

        $(window).on('popstate', function () {
            handler.close();
        });
    });
</script>
