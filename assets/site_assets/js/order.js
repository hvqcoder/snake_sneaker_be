(function ($) {
	"use strict";

	var base_url = Settings.base_url;

	$(document).on("click", ".btn_claim", function(e) {
        e.preventDefault();

        if ($(this).data("gateway") != 'cod') {
            $(".bank_details").show();
        } else {
            $(".bank_details").hide();
        }

        $("#claimRefund input[name='order_id']").val($(this).data("order"));
        $("#claimRefund input[name='product_id']").val($(this).data("product"));
        $("#claimRefund").modal("show");

        $("body").css("overflow-y", "hidden");
    });

    $(document).on("click", ".claim_refund", function(e) {
        e.preventDefault();

        var _current_element = $(this);
        _current_element.prop("disabled", true);

        var dataStaff=$(this).data('stuff');

        $(".process_loader").show();

        var _form=$("#claim-form");
        var _formData=_form.serializeArray();

        if (_formData.length==2){

            $(".process_loader").hide();
            _current_element.prop("disabled", false);
            myAlert(dataStaff['bank_err'],'myalert-danger');
            return false;
        }

        $.post(_form.attr("action"), _formData, function(data){
        	
        	$(".process_loader").hide();
            _current_element.prop("disabled", false);

        	if(data.status==1){
        		location.reload();
        	}
        	else if(data.status==2){
        		window.location.href='login-register';
        	}
        	else{
        		myAlert(data.msg,'myalert-danger');
        	}
        }, "json");
    });

    $(document).on("click", ".btn_remove_bank", function(e) {
        e.preventDefault();

        var _id = $(this).data("id");

        var _action = base_url + 'bank/remove_bank_account';

        var confirmDlg = duDialog(null, Settings.confirm_msg, {
			init: true,
			dark: false, 
			buttons: duDialog.OK_CANCEL,
			okText: 'Proceed',
			callbacks: {
				okClick: function(e) {
					$(".dlg-actions").find("button").attr("disabled",true);
					$(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> '+Settings.please_wait);

					$.post(_action, { bank_id: _id}, function(data){
							confirmDlg.hide();

							if(data.status==1){
								location.reload()
							}
							else if(data.status==2){
								window.location.href='login-register';
							}
							else{
								myAlert(data.msg,'myalert-danger');
							}

					}, "json");
				}
			}
		})
		confirmDlg.show();
    });

    $(document).on("click", ".cancel_order", function(e) {

        e.preventDefault();
        var _btn = $(this);

        var dataStaff=$(this).data('stuff');

        _btn.attr("disabled", true);

        _btn.text(dataStaff['please_wait_lbl']);

        var _reason = $("textarea[name='reason']").val();

        var _bank_id = $(this).parents("#orderCancel").find("input[name='bank_acc_id']:checked").val();

        var flag = false;

        if (_reason == '') {
            _btn.attr("disabled", false);
            _btn.text(dataStaff['cancel_ord_btn']);
            myAlert(dataStaff['cancel_ord_reason_err'],'myalert-danger');
            return false;
        }

        if ((_bank_id == '' || typeof _bank_id == "undefined") && $(this).parents("#orderCancel").find("input[name='gateway']").val() != 'cod') {
            
            _btn.attr("disabled", false);
            _btn.text(dataStaff['cancel_ord_btn']);
            myAlert(dataStaff['cancel_ord_bank_err'],'myalert-danger');
            return false;
        }
        
        if(!flag) {

            $(".process_loader").show();

            var order_id = $(this).parents("#orderCancel").find("input[name='order_id']").val();
            var product_id = $(this).parents("#orderCancel").find("input[name='product_id']").val();

            var _action = base_url + 'order/cancel_product';

            $.post(_action, { 'order_id': order_id, 'product_id': product_id, 'reason': _reason, 'bank_id': _bank_id }, function(data){

                if(data.status==1){
                    location.reload()
                }
                else if(data.status==2){
                    window.location.href='login-register';
                }
                else{
                    myAlert(data.msg,'myalert-danger');
                }

            }, "json");
        }
    });

    if($(".btn_cancel_form").length > 0){
        $(document).on("click", ".btn_cancel_form", function(e) {
            e.preventDefault();
            $(".bank_form").hide();
        });
    }

    if($(".btn_download").length > 0){
        $(document).on("click", ".btn_download", function(e) {
            e.preventDefault();
            var _id = $(this).data("id");
            var href = base_url + 'download-invoice/' + _id;
            window.open(href);
        });
    }

    if($(".product_cancel").length > 0){
        $(document).on("click", ".product_cancel", function(e) {

            e.preventDefault();

            if ($(this).data("gateway") != 'cod') {
                $(".bank_details").show();
            } else {
                $(".bank_details").hide();
            }

            var _title = Settings.product_cancel_confirm;

            if ($(this).data("product") == '0') {
                _title = Settings.ord_cancel_confirm;
            }

            $("#orderCancel .cancelTitle").text(_title);
            $("#orderCancel .order_unique_id").text($(this).data("unique"));
            $("#orderCancel").modal("show");

            $("body").css("overflow-y", "hidden");

            var order_id = $(this).data("order");
            var product_id = $(this).data("product");

            $("#orderCancel input[name='order_id']").val(order_id);
            $("#orderCancel input[name='product_id']").val(product_id);

            $("#orderCancel input[name='gateway']").val($(this).data("gateway"));

        });
    }

    $(document).on("hidden.bs.modal", "#orderCancel, #claimRefund", function(e) {
        $("body").css("overflow-y", "auto");
        $(".bank_form").hide();
        $(".bank_details").hide();
        $("textarea[name='reason']").css("border-color", "#ccc");
        $("textarea").val('');
    });

})(jQuery);