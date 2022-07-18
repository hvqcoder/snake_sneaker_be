(function ($) {
	"use strict";

	var base_url = Settings.base_url;
	var currency_code = Settings.currency_code;

    $(document).on("click", "input[name='payment_method']", function(e) {
        var _val = $(this).val();
        $(".payment_method .pay-box").hide();
        $(this).parent(".payment_method").find(".pay-box").slideDown("100");
    });

    $(document).on("click", "input[name='address_id']", function (e) {

        $(".ceckout-form").hide();
        
        var _id=$(this).val();
        var _action = base_url + 'user/set_default_address';

        $(".process_loader").show();

        $.post(_action, { address_id: _id},
            function(data){

                $(".process_loader").hide();

                if(data.status==1){
                    $(".address-list").html(data.addresses);
                }
                else if(data.status==2){
                    window.location.href='login-register';
                }
                else{
                    myAlert(data.msg,'myalert-danger');
                }

            }, "json");

    })

    $(document).on("click",".btn_apply_coupon", function(e) {

        e.preventDefault();
        var href = base_url + 'checkout/apply_coupon';

        var coupon_id=$(this).data("coupon");
        var cart_ids=$(this).data("cart");
        var cart_type=$(this).data("type");
        
        $.ajax({
            url: href,
            type: 'post',
            data: {'coupon_id':coupon_id, 'cart_ids':cart_ids, 'cart_type':cart_type},
            dataType: 'json',
            success: function(res) {

                $("#coupons_detail").modal("hide");
                
                if(res.status == '1') {
                	myAlert(res.msg,'myalert-success');

                    $(".order-total").find("span").html(currency_code + ' ' + res.payable_amt);
                    $(".msg_2").html(res.you_save_msg);
                    $(".apply_msg").show();
                    $("input[name='coupon_id']").val(res.coupon_id);
                    $(".apply_button").hide();
                    $(".remove_coupon").show();
                }
                else if(res.success=='-2'){
                	window.location.href = base_url + "login-register";
                }
                else {
                	myAlert(res.msg,'myalert-danger');
                }
            },
            error: function(res) {
            	myAlert(Settings.err_something_went_wrong,'myalert-danger');
            }
        });
        
    });

    $(document).on("click",".remove_coupon a",function(e) {
        e.preventDefault();

        var cart_type='';
        if($("input[name='buy_now']").val()=='false'){
            cart_type='main_cart';
        }
        else{
            cart_type='temp_cart';
        }

        var confirmDlg = duDialog(null, Settings.confirm_msg, {
         init: true,
         dark: false, 
         buttons: duDialog.OK_CANCEL,
         okText: 'Proceed',
         callbacks: {
            okClick: function(e) {
               $(".dlg-actions").find("button").attr("disabled",true);
               $(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> '+Settings.please_wait);

               var coupon_id=$("input[name='coupon_id']").val();
               var cart_ids=$("input[name='cart_ids[]']").val();

               var _url = base_url + 'checkout/remove_coupon/'+cart_type;

               $.ajax({
                url: _url,
                type: 'post',
                data: {'coupon_id' : coupon_id, 'cart_ids' : cart_ids},
                dataType: 'json',
                success: function(res){

                    confirmDlg.hide();

                    if(res.status == '1'){
                        
                        myAlert(res.msg,'myalert-success');

                        $(".order-total").find("span").html(currency_code + ' ' + res.payable_amt);

                        $(".msg_2").html(res.you_save_msg);

                        $(".apply_msg").show();
                        $("input[name='coupon_id']").val(0);

                        $(".apply_button").show();
                        $(".remove_coupon").hide();
                    }
                    else if(res.success=='-1'){
                        window.location.href = base_url + "my-cart";
                    }
                    else {
                        window.location.href = base_url + "login-register";
                    }
                }
            });
           }
       }
   });
        confirmDlg.show();
    });

    $(document).on("click", "#order-summary .plus,#order-summary .minus", function(event) {

        var _current_element=$(this);

        var qty = parseInt(_current_element.parents().parents(".quantity").find(".product_qty").val());
        var _product_id=_current_element.data("product");
        var _perform=_current_element.data("perform");
        var _coupon_id=$("input[name='coupon_id']").val();
        var _buy_now=$("input[name='buy_now']").val();
        var _chkout_ref=$("input[name='chkout_ref']").val();
        var _action=base_url+'user/update_cart';

        if(_perform=='plus'){
            qty=qty+1;
        }
        else{
            qty=qty-1;
        }

        $(".plus").prop("disabled", false);
        $(".minus").prop("disabled", false);

        if(qty < 1){
            _current_element.prop("disabled", true);
            return false;
        }
        else{
            _current_element.prop("disabled", false);
        }

        $.post(_action, { product_id: _product_id, qty: qty, perform: _perform, coupon_id: _coupon_id, buy_now: _buy_now, chkout_ref: _chkout_ref},
            function(data){

                if(data.status==1){
                    myAlert(data.msg,'myalert-success');
                    $("#buy_now_form").find("input[name='qty']").val(qty);
                    _current_element.parents().parents(".quantity").find(".product_qty").val(qty);
                    $(".total-amount").text(data.total);
                    $(".amount").text(data.sub_total);
                    $(".delivery_charge").text(data.delivery_charge);
                    $(".msg_2").text(data.you_save);

                    _current_element.parents(".product-holder").find(".product-price").html(data.product_amount);

                }
                else{
                    myAlert(data.msg,'myalert-danger');
                }
            }, "json");
    });

    $(document).on("click", "#order-summary .btn-remove-cart", function(event) {

        var _current_element=$(this);

        var _cart_id=_current_element.data("id");
        var _action=base_url+'user/remove_to_cart';

        _current_element.prop("disabled", true);

        $.post(_action, { id: _cart_id},
            function(data){

                _current_element.prop("disabled", false);

                if(data.status==1){

                    myAlert(data.msg,'myalert-success');
                    $(".total-amount").text(data.total);
                    $(".amount").text(data.sub_total);
                    $(".delivery_charge").text(data.delivery_charge);
                    $(".msg_2").text(data.you_save);

                    $(".remove_coupon").find("a").data("cart_ids",data.cart_ids);

                    _current_element.parents(".product-holder").remove();

                }
                else if(data.status==2){
                    window.location.href='my-cart';
                }
                else{
                    myAlert(data.msg,'myalert-danger');
                }
            }, "json");
    });

    var loadedStripeJs = 0;
    var card, stripe, elements;

    $(document).on("change", "input[name='payment_method']", function(e) {
        var _current_element=$(this);

        var _payment_method=$(this).val();

        if(_payment_method=='stripe'){

            if (!loadedStripeJs){
                $.getScript("https://js.stripe.com/v3/").done(function( script, textStatus ) {
                    loadedStripeJs = 1;

                    stripe = Stripe(Settings.stripe_pk);

                    elements = stripe.elements({
                        locale: window.__exampleLocale
                    });

                    card = elements.create('card', {
                        iconStyle: 'solid',
                        hidePostalCode: true,
                        style: {
                            base: {
                                iconColor: '#ff5252',
                                color: '#515151',
                                fontWeight: 500,
                                fontFamily: 'Poppins, sans-serif',
                                fontSize: '16px',
                                fontSmoothing: 'antialiased',

                                ':-webkit-autofill': {
                                    color: '#fce883',
                                },
                                '::placeholder': {
                                    color: '#515151',
                                },
                            },
                            invalid: {
                                iconColor: '#f00',
                                color: '#f00',
                            },
                        },
                    });
                    card.mount('#stripe-elements');

                }).fail(function( jqxhr, settings, exception ) {
                    myAlert(Settings.err_something_went_wrong,'myalert-danger');
                });
            }
            else{
                stripe = Stripe(Settings.stripe_pk);

                elements = stripe.elements({
                    locale: window.__exampleLocale
                });

                card = elements.create('card', {
                    iconStyle: 'solid',
                    hidePostalCode: true,
                    style: {
                        base: {
                            iconColor: '#ff5252',
                            color: '#515151',
                            fontWeight: 500,
                            fontFamily: 'Poppins, sans-serif',
                            fontSize: '16px',
                            fontSmoothing: 'antialiased',

                            ':-webkit-autofill': {
                                color: '#fce883',
                            },
                            '::placeholder': {
                                color: '#515151',
                            },
                        },
                        invalid: {
                            iconColor: '#f00',
                            color: '#f00',
                        },
                    },
                });
                card.mount('#stripe-elements');
            }
        }
    });

    $(document).on("click", ".btn_place_order", function(e) {

        e.preventDefault();

        var flag=false;
        var _container=$("#checkout-process");

        var formData = $("#checkout-process input").not(".address_form input").serializeArray();

        var _current_element = $(this);
        
        var _payment_method=_container.find("input[name='payment_method']:checked").val();

        _current_element.attr("disabled",true);

        if(_payment_method=='cod'){
            if (_container.find(".input_txt").val() != '') {

                var _sum = parseInt(_container.find("._lblnum1").text()) + parseInt(_container.find("._lblnum2").text());

                if (parseInt(_container.find(".input_txt").val()) != _sum) {

                    myAlert("Enter correct value!",'myalert-danger');

                    var x = Math.floor((Math.random() * 10) + 1);
                    var y = Math.floor((Math.random() * 10) + 1);

                    _container.find("._lblnum1").text(x);
                    _container.find("._lblnum2").text(y);
                    _container.find(".input_txt").focus();
                    
                    flag=true;
                }
            }
            else{
                _container.find(".input_txt").focus();
                myAlert("Enter correct value!","myalert-danger");
                flag=true;
            }

            if(flag){
                _current_element.attr("disabled",false);
                return false;
            }
            else{
                var _url = base_url+'order/place_new_order';
                place_order(_current_element, formData, _url)
            }
        }
        else if(_payment_method=='stripe'){

            $(".process_loader").show();

            if($("input[name='card_name']").val()==''){
                $(".process_loader").hide();
                _current_element.attr("disabled",false);
                myAlert("Enter name on card!","myalert-danger");
                return false;
            }

            var additionalData = {
                name: $("input[name='card_name']").val() ? $("input[name='card_name']").val() : undefined
            };

            stripe.createToken(card, additionalData).then(function(result) {

                $(".process_loader").hide();
                _current_element.attr("disabled",false);

                if (result.token) {
                    $("input[name='stripe_token_id']").val(result.token.id);
                    formData.push({name: 'stripe_token_id', value: result.token.id});
                    var _url = base_url+'stripe/pay';
                    place_order(_current_element, formData, _url)
                }
                else{
                    myAlert(result.error.message,"myalert-danger");
                    return false;
                }
            });

        }
        else if(_payment_method=='paypal'){

            $(".process_loader").show();
            var _url = base_url+'paypal/pay';

            var _form = $(document.createElement('form'));
            $(_form).attr("action", _url);
            $(_form).attr("method", "POST");
            $(_form).append($("#checkout-process input").not(".address_form input"));
            $(_form).find("input").attr("type","hidden");
            _form.appendTo( document.body )
            $(_form).submit();
        }
        else if(_payment_method=='paystack'){

            $(".process_loader").show();

            var _url = base_url + 'paystack/transaction_initialize';

            $.ajax({
                type: 'POST',
                url: _url,
                data: formData,
                dataType: 'json',
                success: function(data) {

                    _current_element.attr("disabled",false);
                    $(".process_loader").hide();

                    if(data.status==1){
                        window.location.href=data.authorization_url;
                    }
                    else if(data.status==2){
                        window.location.href='login-register';
                    }
                    else if(data.status==-2){
                        window.location.href='my-cart';
                    }
                    else{
                        myAlert(data.msg,"myalert-danger");
                    }
                }
            });

        }
        else if(_payment_method=='razorpay'){

            $(".process_loader").show();

            var _url = base_url + 'razorpay/generate_ord';

            $.ajax({
                type: 'POST',
                url: _url,
                data: formData,
                dataType: 'json',
                success: function(data) {

                    _current_element.attr("disabled",false);
                    $(".process_loader").hide();

                    if(data.status==1){

                        $("script[src='https://checkout.razorpay.com/v1/checkout.js']").remove();

                        callRazorPayScript(data.key, data.site_name, data.description, data.logo, data.amount, data.user_name, data.user_email, data.user_phone, data.theme_color, data.razorpay_order_id)
                    }
                    else if (data.status==-1){
                        window.location.href='site';
                    }
                    else if(data.status==2){
                        window.location.href='login-register';
                    }
                    else if(data.status==-2){
                        window.location.href='my-cart';
                    }
                    else{
                        myAlert(data.msg,"myalert-danger");
                    }

                }
            })

        }
    });

function place_order(_current_element, _formData, _url) {
    $(".process_loader").show();

    $.ajax({
        type: 'POST',
        url: _url,
        data: _formData,
        dataType: 'json',
        success: function(data) {

            _current_element.attr("disabled",false);
            $(".process_loader").hide();

            if (data.status==1){
                window.location.href='order-confirm?order='+data.order_unique_id;
            }
            else if(data.status==2){
                window.location.href='login-register';
            }
            else if(data.status==3){
                duDialog('Opps!', data.msg, { init: true })
            }
            else if (data.status==-1){
                window.location.href='site';
            }
            else if(data.status==-2){
                window.location.href='my-cart';
            }
            else{
                myAlert(data.msg,"myalert-danger");
            }
        },
        error: function (jqXHR, exception) {

            _current_element.attr("disabled",false);

            $(".process_loader").hide();

            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }

            myAlert(msg,"myalert-danger");
        },
    })
}

var loadExternalScript = function(path) {
    var result = $.Deferred(),
    script = document.createElement("script");

    script.async = "async";
    script.type = "text/javascript";
    script.src = path;
    script.onload = script.onreadystatechange = function(_, isAbort) {
        if (!script.readyState || /loaded|complete/.test(script.readyState)) {
            if (isAbort)
                result.reject();
            else
                result.resolve();
        }
    };

    script.onerror = function() {
        result.reject();
    };

    $("head")[0].appendChild(script);
    return result.promise();
}

var callRazorPayScript = function(key, title, desc, logo, amount, name, email, contact, theme, order_id) {

    loadExternalScript('https://checkout.razorpay.com/v1/checkout.js').then(function() { 
        var options = {
            key: key,
            protocol: 'https',
            hostname: 'api.razorpay.com',
            amount: amount,
            order_id: order_id,
            name: title,
            description: desc,
            image: logo,
            prefill: {
                name: name,
                email: email,
                contact: contact,
            },
            theme: { color: theme },
            handler: function (transaction, response){

                $("#razorpayForm").append("<input name='razorpay_payment_id' type='hidden' value='"+transaction.razorpay_payment_id+"'>");
                
                $(".process_loader").show();

                var _formData=$("#razorpayForm").serialize();
                var _action=$("#razorpayForm").attr("action");

                $.ajax({
                    type: 'POST',
                    url: _action,
                    data: _formData,
                    dataType: 'json',
                    success: function(data) {

                        $(".process_loader").hide();

                        if (data.status==1){
                            window.location.href='order-confirm?order='+data.order_unique_id;
                        }
                        else if (data.status==-1){
                            window.location.href='site';
                        }
                        else if(data.status==2){
                            window.location.href='login-register';
                        }
                        else if(data.status==-2){
                            window.location.href='my-cart';
                        }
                        else{
                            myAlert(data.msg,"myalert-danger");
                        }
                    },
                    error: function (jqXHR, exception) {

                        $(".process_loader").hide();

                        var msg = '';
                        if (jqXHR.status === 0) {
                            msg = 'Not connect.\n Verify Network.';
                        } else if (jqXHR.status == 404) {
                            msg = 'Requested page not found. [404]';
                        } else if (jqXHR.status == 500) {
                            msg = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                            msg = 'Requested JSON parse failed.';
                        } else if (exception === 'timeout') {
                            msg = 'Time out error.';
                        } else if (exception === 'abort') {
                            msg = 'Ajax request aborted.';
                        } else {
                            msg = 'Uncaught Error.\n' + jqXHR.responseText;
                        }

                        myAlert(msg,"myalert-danger");
                    },
                });
            }
        };
        window.rzpay = new Razorpay(options);

        rzpay.open();
    });
}

})(jQuery);