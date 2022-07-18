(function ($) {
	"use strict";

	var base_url = Settings.base_url;

	$(document).on("submit","#review-form", function(e){

		e.preventDefault();

		var _submit_btn=$(this).find("button[type='submit']");

		_submit_btn.prop('disabled', true);

		var _btn_text=_submit_btn.text();

		_submit_btn.html("<i class='fa fa-spinner fa-spin'></i>");

		var _action=$(this).attr("action");
		var _formData = new FormData($(this)[0]);

		$.ajax({
			url: _action,
			data: _formData,
			processData: false,
			contentType: false,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				_submit_btn.text(_btn_text);
				_submit_btn.prop('disabled', false);

				if(data.status==1){
					location.reload();
				}
				else if(data.status==2){
					window.location.href=base_url+'login-register';
				}
				else{
					myAlert(data.msg,'myalert-danger');
				}
			}
		});
	});

	$(document).on("click", ".applied_offer_lbl", function(e) {
		e.preventDefault();
		var content = $(this).find(".offer_details").html();
		$("#offer_details .modal-body").html(content)
		$("#offer_details").modal("show");
	});

	$(document).on("click", ".size_chart", function(e) {
		e.preventDefault();

		$(".size_chart_img").hide();
		$(".no_data").hide();
		if ($(this).data("img") == '') {
			$(".no_data").show();
		} else {
			$(".size_chart_img").show();
			$(".size_chart_img").attr("src", $(this).data("img"));
		}
		$("#size_chart").modal("show")
	});

	if($("#buy_now_form").length > 0){

		$("#buy_now_form").find("input[name='size']").val($("input[name='product_size']").val());
		$("#buy_now_form").find("input[name='qty']").val($("input[name='product_qty']").val());
		

		$(document).on("submit", "#buy_now_form", function() {
			$(this).children(':input[value=""]').attr("disabled", "disabled");
			return true;
		});
	}

	var _max_unit_buy=$("input[name='max_unit_buy']").val();

	$(document).on("click", "#cart-form .plus", function(event) {

		var _current_element=$(this);
		_current_element.attr("disabled",false);

		var qty = parseInt($(this).parents().parents(".quantity").find(".product_qty").val());

		$(".minus").attr("disabled",false);

		if(qty <= 0){
			$(this).parents().parents(".quantity").find(".product_qty").val(1);
		}
		else{
			qty=qty+1;

			if(qty > _max_unit_buy){

				_current_element.attr("disabled",true);

				var limit_items=Settings.err_cart_item_buy;
				myAlert(limit_items.replace("###", _max_unit_buy),'myalert-danger');

				$(this).parents().parents(".quantity").find(".product_qty").val(_max_unit_buy);  
			}
			else{
				$("#buy_now_form").find("input[name='qty']").val(qty);
				$(this).parents().parents(".quantity").find(".product_qty").val(qty);   
			}
		}
	});

	$(document).on("click", "#cart-form .minus", function(event) {

		$(".plus").attr("disabled",false);

		var _current_element=$(this);
		_current_element.attr("disabled",false);

		var qty = parseInt($(this).parents().parents(".quantity").find(".product_qty").val());
		qty=qty-1;

		if(qty <= 0){

			_current_element.attr("disabled",true);

			$("#buy_now_form").find("input[name='qty']").val(1);
			$(this).parents().parents(".quantity").find(".product_qty").val(1);
		}
		else{
			$("#buy_now_form").find("input[name='qty']").val(qty);
			$(this).parents().parents(".quantity").find(".product_qty").val(qty);
		}
	});

	$(document).on("submit", "form#cart-form", function (e) {
		e.preventDefault();

		var _submit_btn=$(this).find("button[type='submit']");

		var _id=$(this).find("input[name='product_id']").val();

		_submit_btn.prop('disabled', true);

		var _btn_text=_submit_btn.text();

		_submit_btn.html("<i class='fa fa-spinner fa-spin'></i>");

		$.ajax({
			type: 'POST',
			url: $(this).attr("action"),
			data: $(this).serialize(),
			dataType: 'json',
		})
		.done(function(res) {
			_submit_btn.prop('disabled', false);
			if(res.status == '1') {
				_submit_btn.text(res.update_lbl);
				$(".cart-item-count").text(res.cart_items);
				$(".cart-items").html(res.cart_view);
				myAlert(res.msg,'myalert-success');
			}
			else if(res.login_require){
				window.location.href=base_url+'login-register';
			}
			else{
				_submit_btn.text(_btn_text);
				myAlert(res.msg,'myalert-danger');
			}
		})
		.fail(function(response) {
			myAlert(res.msg,'myalert-danger');
		});

	});

	$('.discription-tab-menu a[data-toggle="tab"]').on('show.bs.tab', function(e) {
		localStorage.setItem('activeTabDiscription', $(e.target).attr('href'));
	});

	var activeTabDiscription = localStorage.getItem('activeTabDiscription');

	if (activeTabDiscription) {
		$('.discription-tab-menu a[href="' + activeTabDiscription + '"]').tab('show');
		$(".discription-tab-content").find("div.tab-pane").removeClass("active");
		$(".discription-tab-content").find(activeTabDiscription).addClass('active');
	}

})(jQuery);