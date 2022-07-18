function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57)){
		return false;
	}
	return true;
}

function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function(e) {
			$(".fileupload_img").attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
	}
}

document.addEventListener('DOMContentLoaded', function(){
	(function ($) {
		"use strict";

		$(window).on("load",function() {
			$(".se-pre-con").fadeOut("slow");
		});

		var base_url = Settings.base_url;

		function removeURLParameter(url, parameter) {

			var urlparts = url.split('?');
			if (urlparts.length >= 2) {

				var prefix = encodeURIComponent(parameter) + '=';
				var pars = urlparts[1].split(/[&;]/g);

				for (var i = pars.length; i-- > 0;) {
					if (pars[i].lastIndexOf(prefix, 0) !== -1) {
						pars.splice(i, 1);
					}
				}
				url = urlparts[0] + '?' + pars.join('&');
				return url;
			} else {
				return url;
			}
		}

		$("input[name='file_name']").on("change",function() { 
			readURL(this);
		});

		$(document).on("submit", "#search_form", function(e){
			if($(this).find("select").val()==''){
				$(this).find("select").attr("disabled",true);
			}
		});

		$(document).on("click",".btn_cart", function(e) {
			e.preventDefault();
			var btn = $(this);
			var _id = $(this).data("id");
			var _maxunit = $(this).data("maxunit");

			var href = base_url + 'user/cart_action';

			$.ajax({
				url: href,
				data: {"product_id": _id},
				type: 'post',
				dataType: 'json',
				success: function(res) {

					if(res.status==1){
						$("#cartModal .modal-body").html(res.html_code);
						$("#cartModal").modal("show");

						$("#cartModal .modal-body").html(res.html_code);
						$("#cartModal").modal("show");

						$('.radio-group .radio_btn').on("click", function() {
							$(this).parent().find('.radio_btn').removeClass('selected');
							$(this).addClass('selected');
							var val = $(this).attr('data-value');
							$(this).parent().find('input').val(val);
						});

						var max_unit_buy=res.max_unit_buy;

						$(".plus").on("click", function(event) {

							var qty = parseInt($(this).parents().parents(".quantity").find(".product_qty").val());

							if(qty <= 0){
								$(this).parents().parents(".quantity").find(".product_qty").val(1);
							}
							else{

								qty=qty+1;

								if(qty > max_unit_buy){

									var limit_items=Settings.err_cart_item_buy;
									myAlert(limit_items.replace("###", max_unit_buy),'myalert-danger');

									$(this).parents().parents(".quantity").find(".product_qty").val(max_unit_buy);  
								}
								else{

									$("#buy_now_form").find("input[name='qty']").val(qty);

									$(this).parents().parents(".quantity").find(".product_qty").val(qty);   
								}
							}
						});

						$(".minus").on("click", function(event) {

							var qty = parseInt($(this).parents().parents(".quantity").find(".product_qty").val());

							qty=qty-1;

							if(qty <= 0){
								$("#buy_now_form").find("input[name='qty']").val(1);
								$(this).parents().parents(".quantity").find(".product_qty").val(1);
							}
							else{
								$("#buy_now_form").find("input[name='qty']").val(qty);
								$(this).parents().parents(".quantity").find(".product_qty").val(qty);
							}
						});

						$(".size_chart").click(function(e){
							e.preventDefault();
							$(".size_chart_img").hide();
							$(".no_data").hide();

							if($(this).data("img")==''){
								$(".no_data").show();
							}
							else{
								$(".size_chart_img").show();
								$(".size_chart_img").attr("src",$(this).data("img"));
							}

							$("#size_chart").modal("show")
						});
					}
					else{
						window.location.href = base_url + "login-register";
					}
				},
				error: function(res) {
					myAlert(Settings.err_something_went_wrong,'myalert-danger');
				}
			});
		});

		$(document).on("submit","#cartForm", function(event) 
		{
			event.preventDefault();

			var _submit_btn=$(this).find("button[type='submit']");

			var _id=$(this).find("input[name='product_id']").val();

			_submit_btn.prop('disabled', true);

			var _cart_text=_submit_btn.text();

			_submit_btn.html("<i class='fa fa-spinner fa-spin'></i>");

			$.ajax({
				type: 'POST',
				url: base_url + 'user/add_to_cart',
				data: $(this).serialize(),
				dataType: 'json',
			})
			.done(function(res) {

				_submit_btn.text(_cart_text);

				_submit_btn.prop('disabled', false);

				if($('#cartModal').is(':visible')){
					$("#cartModal").modal("hide");
				}
				else{
					$("#productQuickView").modal("hide");
				}

				if(res.status == '1') {

					var _elements=$(document).find(".btn_cart[data-id='" + res.product_id + "']");

					$.each( _elements, function( key, value ) {

						if($(this).parents(".product").length > 0){

							var element=$(this).parents(".product");

							element.find(".btn_cart").text(res.btn_lbl);
							element.find(".btn_cart").attr('data-original-title', res.tooltip_lbl);
							element.find(".btn_cart").attr("href",res.removeCartUrl);
							element.find(".btn_cart").removeClass("btn_cart").addClass("btn_remove_cart");
						}
					});

					$(".cart-item-count").text(res.cart_items);
					$(".cart-items").html(res.cart_view);

					myAlert(res.msg,'myalert-success');
				}
				else{
					myAlert(res.msg,'myalert-danger');
				}
			})
			.fail(function(response) {
				myAlert(res.msg,'myalert-danger');
			});

		});

		$(document).on("click",".btn_remove_cart", function(e) {
			e.preventDefault();

			var href = $(this).attr("href");

			var _current_element=$(this);

			var path = window.location.pathname;
			var _current_page = path.split("/").pop();

			var confirmDlg = duDialog(null, Settings.confirm_msg, {
				init: true,
				dark: false, 
				buttons: duDialog.OK_CANCEL,
				okText: 'Proceed',
				callbacks: {
					okClick: function(e) {
						$(".dlg-actions").find("button").attr("disabled",true);
						$(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> '+Settings.please_wait);

						$.ajax({
							type: 'POST',
							url: href,
							dataType: 'json',
						})
						.done(function(res) {

							confirmDlg.hide();

							if (res.status == '1') {
								myAlert(res.msg,'myalert-success');

								if(_current_page=='checkout' || _current_page=='my-cart'){
									location.reload();
								}

								if($(".single-product-quantity").length > 0){
									$(".product_qty").val(1);
									$(".cart-action-btn").text(res.btn_lbl);
								}

								var _elements=$(document).find('a[href="'+href+'"]');

								$.each( _elements, function( key, value ) {

									if($(this).parents(".product").length > 0){
										var element=$(this).parents(".product");

										element.find(".btn_remove_cart").text(res.btn_lbl);
										element.find(".btn_remove_cart").attr('data-original-title', res.tooltip_lbl);
										element.find(".btn_remove_cart").attr("href","javascript:void(0)");
										element.find(".btn_remove_cart").removeClass("btn_remove_cart").addClass("btn_cart");
									}
								});

								$(".cart-item-count").text(res.cart_items);
								$(".cart-items").html(res.cart_view);

							} else {
								myAlert(res.msg,'myalert-danger');
							}

						})
						.fail(function(response) {
							myAlert(Settings.err_something_went_wrong,'myalert-danger');
						});

					}
				}
			});
			confirmDlg.show();
		});

		if($(".btn_wishlist").length > 0){
			$(document).on("click",".btn_wishlist", function(e) {
				e.preventDefault();
				var btn = $(this);
				var _id = $(this).data("id");

				var href = base_url + 'user/wishlist_action';

				$.ajax({
					url: href,
					data: {"product_id": _id},
					type: 'post',
					dataType: 'json',
					success: function(res) {

						if(res.status==1){
							myAlert(res.msg,'myalert-success');

							if (res.is_favorite) {
								btn.css("background-color", "#ff5252");
								btn.attr('data-original-title', res.icon_lbl);

							} else {
								btn.css("background-color", "#363F4D");
								btn.attr('data-original-title', res.icon_lbl);
							}
						}
						else{
							window.location.href = base_url + "login-register";
						}
					},
					error: function(res) {
						myAlert(res.msg,'myalert-danger');
					}

				});
			});
		}

		if($(".btn_remove_wishlist").length > 0){
			$(document).on("click",".btn_remove_wishlist", function(e) {
				e.preventDefault();

				var _id=$(this).data("id");
				var _current_element = $(this);

				var confirmDlg = duDialog(null, Settings.confirm_msg, {
					init: true,
					dark: false, 
					buttons: duDialog.OK_CANCEL,
					okText: 'Proceed',
					callbacks: {
						okClick: function(e) {
							$(".dlg-actions").find("button").attr("disabled",true);
							$(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> '+Settings.please_wait);

							var _url = base_url + 'user/wishlist_action';

							$.ajax({
								url: _url,
								data: {product_id: _id},
								type: 'post',
								dataType: 'json',
								success:function(res){

									confirmDlg.hide();

									if(res.status){
										myAlert(res.msg,'myalert-success');
										_current_element.parents("tr").remove();
									}
									else{
										window.location.href = base_url + "login-register";
									}
								},
								error : function(res) {
									myAlert(res,'myalert-danger');
								}
							}) 
						}
					}
				});
				confirmDlg.show();
			});
		}

		$(document).on("click", ".btn_new_address", function(e) {

			e.preventDefault();
			$(".ceckout-form").toggle();
			$(".bank_form").toggle();

			var _fa=$(this).find(".fa");

			if(_fa.hasClass("fa-plus")){
				_fa.removeClass("fa-plus");
				_fa.addClass("fa-minus");
			}
			else{
				_fa.removeClass("fa-minus");
				_fa.addClass("fa-plus");   
			}
		});

		$(document).on("click", ".btn_new_account", function(e) {
			e.preventDefault();

			var _fa=$(this).find(".fa");

			if(_fa.hasClass("fa-plus")){
				_fa.removeClass("fa-plus");
				_fa.addClass("fa-minus");
			}
			else{
				_fa.removeClass("fa-minus");
				_fa.addClass("fa-plus");   
			}

			$(".bank_form").toggle();
		});

		$(document).on("click", ".btn_edit_address", function(e){

			var data=$(this).data('stuff');

			$('#edit_address').find("input[name='address_id']").val(data['id']);
			$('#edit_address').find("input[name='billing_name']").val(data['name']);
			$('#edit_address').find("input[name='billing_mobile_no']").val(data['mobile_no']);
			$('#edit_address').find("input[name='alter_mobile_no']").val(data['alter_mobile_no']);
			$('#edit_address').find("input[name='billing_email']").val(data['email']);
			$('#edit_address').find("textarea[name='building_name']").val(data['building_name']);
			$('#edit_address').find("input[name='road_area_colony']").val(data['road_area_colony']);

			$('#edit_address').find("input[name='landmark']").val(data['landmark']);
			$('#edit_address').find("input[name='pincode']").val(data['pincode']);
			$('#edit_address').find("input[name='city']").val(data['city']);
			$('#edit_address').find("input[name='district']").val(data['district']);
			$('#edit_address').find("input[name='state']").val(data['state']);
			$('#edit_address').find('#country option[value="'+data['country']+'"]').prop('selected', true);

			$('#edit_address').find("input[name=address_type][value='"+data['address_type']+"']").prop("checked",true);

			$('#edit_address').modal({
				backdrop: 'static',
				keyboard: false
			})
		});

		$(document).on("submit", "#edit_address_form", function(e){
			e.preventDefault();

			$(".process_loader").show();

			var _action = $(this).attr("action");

			$.ajax({
				url: _action,
				type: 'POST',
				data: $(this).serialize(),
				dataType: 'json',
				success: function(res){

					$(".process_loader").hide();

					if(res.status==1){
						location.reload();
					}
					else{
						myAlert(res.msg,'myalert-danger');
					}

				}
			});

		});

		$(document).on("click", ".address_form button[name='submit']", function(e){

			e.preventDefault();

			var _current_element=$(this);
			var _action=$(this).parents("form").attr("action");
			var _formData=$(this).parents("form").serialize();
			var $inputs = $(this).parents("form").find("input, textarea, select");
			var flag=false;

			var _text=_current_element.text();
			_current_element.prop("disabled", true);

			_current_element.html("<i class='fa fa-spinner fa-spin'></i>");

			$inputs.each(function(){

				if($(this).attr('readonly')) {
					return;
				}

				if($(this).attr('required')){

					if($(this).val()=='') {
						$(this).css("border-color", "red");
						flag=true;
					}
					else{
						$(this).css("border-color", "#ebebeb");
					}
				}
			});

			if(flag){
				_current_element.html(_text);
				_current_element.prop("disabled", false);
				myAlert("Enter all required fields!",'myalert-danger');
				return false
			}
			else{

				$.post(_action, _formData,
					function(data){
						_current_element.html(_text);
						_current_element.prop("disabled", false);

						if(data.status==1){

							$('form[name="address_form"]')[0].reset();
							$(".add_addresss_block").hide();

							myAlert(data.msg,'myalert-success');
							$(".address-list").html(data.addresses);

						}
						else if(data.status==2){
							window.location.href=base_url+'login-register';
						}
						else{
							myAlert(data.msg,'myalert-danger');
						}

					}, "json");
			}
		});

		$(document).on("click",".close_form", function(e) {
			e.preventDefault();
			$('form[name="address_form"]')[0].reset();
			$(".add_addresss_block").hide();
		});

		$(document).on("click",".btn_delete_address", function(e) {

			e.preventDefault();

			var _action=base_url+'user/delete_address';

			var _id = $(this).data("id");
			var confirmDlg = duDialog(null, Settings.confirm_msg, {
				init: true,
				dark: false, 
				buttons: duDialog.OK_CANCEL,
				okText: 'Proceed',
				callbacks: {
					okClick: function(e) {
						$(".dlg-actions").find("button").attr("disabled",true);
						$(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> '+Settings.please_wait);

						$.post(_action, { address_id: _id},
							function(data){
								confirmDlg.hide();

								if(data.status==1){
									myAlert(data.msg,'myalert-success');
									$(".address-list").html(data.addresses);
								}
								else if(data.status==2){
									window.location.href=base_url+'login-register';
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

		$(document).on("click",".btn_quick_view", function(e) {

			e.preventDefault();

			var btn = $(this);
			var _id = $(this).data("id");

			var href = base_url + 'frontend/quick_view';

			$(".process_loader").show();

			$.ajax({
				url: href,
				data: {"product_id": _id},
				type: 'post',
				dataType:'json',
				success: function(obj) {

					$(".process_loader").fadeOut();

					$("#productQuickView .modal-body").html(obj.html_code);
					$("#productQuickView").modal("show");

					$('.modal-tab-menu-active').slick({
						infinite: false,
						slidesToShow: 3,
						slidesToScroll: 3,
						arrows: false,
						dots: true,
						loop: false
					});

					$('.modal').on('shown.bs.modal', function(e) {
						$('.modal-tab-menu-active').resize();
					});

					$(".img_click > a").on("click", function(e) {
						var _id = $(this).attr("href");

						$("#productQuickView").find(".tab-pane").removeClass("active");
						$("#productQuickView").find(".tab-pane").removeClass("in");

						$("#productQuickView").find(_id).addClass("active");
						$("#productQuickView").find(_id).addClass("in");

					});

					var max_unit_buy=obj.max_unit_buy;

					$(".plus").on("click", function(event) {

						var qty = parseInt($(this).parents().parents(".quantity").find(".product_qty").val());

						if(qty <= 0){
							$(this).parents().parents(".quantity").find(".product_qty").val(1);
						}
						else{
							qty=qty+1;

							if(qty > max_unit_buy){

								var limit_items=Settings.err_cart_item_buy;
								myAlert(limit_items.replace("###", max_unit_buy),'myalert-danger');

								$(this).parents().parents(".quantity").find(".product_qty").val(max_unit_buy);  
							}
							else{

								$("#buy_now_form").find("input[name='qty']").val(qty);

								$(this).parents().parents(".quantity").find(".product_qty").val(qty);   
							}
						}
					});

					$(".minus").on("click", function(event) {

						var qty = parseInt($(this).parents().parents(".quantity").find(".product_qty").val());

						qty=qty-1;

						if(qty <= 0){
							$("#buy_now_form").find("input[name='qty']").val(1);
							$(this).parents().parents(".quantity").find(".product_qty").val(1);
						}
						else{
							$("#buy_now_form").find("input[name='qty']").val(qty);
							$(this).parents().parents(".quantity").find(".product_qty").val(qty);
						}
					});

					$('.radio-group .radio_btn').click(function(){

						$(this).parent().find('.radio_btn').removeClass('selected');
						$(this).addClass('selected');
						var val = $(this).attr('data-value');
						$(this).parent().find('input').val(val);

						var size = $("input[name='product_size']").val();

						$("#buy_now_form").find("input[name='size']").val(size);
					});

					$(".size_chart").click(function(e){
						e.preventDefault();

						$(".size_chart_img").hide();
						$(".no_data").hide();

						if($(this).data("img")==''){
							$(".no_data").show();
						}
						else{
							$(".size_chart_img").show();
							$(".size_chart_img").attr("src",$(this).data("img"));
						}

						$("#size_chart").modal("show")
					});
				},
				error: function(res) {
					$(".process_loader").fadeOut();
					myAlert(Settings.err_something_went_wrong,'myalert-danger');
				}
			});

		});

		$(document).on("click", ".btn_remove_review", function(e) {
			e.preventDefault();

			var _id = $(this).data("id");
			var _action=base_url+'user/remove_review';

			var _current_element=$(this);

			var confirmDlg = duDialog(null, Settings.confirm_msg, {
				init: true,
				dark: false, 
				buttons: duDialog.OK_CANCEL,
				okText: 'Proceed',
				callbacks: {
					okClick: function(e) {
						$(".dlg-actions").find("button").attr("disabled",true);
						$(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> '+Settings.please_wait);

						$.post(_action, { review_id: _id}, function(data){
							confirmDlg.hide();

							if(data.status==1){
								_current_element.parents(".my_review_area").remove();
								myAlert(data.msg,'myalert-success');
							}
							else if(data.status==2){
								window.location.href=base_url+'login-register';
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

		$(document).on("click", ".btn_remove_img", function(e) {

			e.preventDefault();

			var _img_holder = $(this).parent(".review_img_holder");

			var _id = $(this).data("id");
			var _action=base_url+'user/remove_review_image';

			var confirmDlg = duDialog(null, Settings.confirm_msg, {
				init: true,
				dark: false, 
				buttons: duDialog.OK_CANCEL,
				okText: 'Proceed',
				callbacks: {
					okClick: function(e) {
						$(".dlg-actions").find("button").attr("disabled",true);
						$(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> '+Settings.please_wait);

						$.post(_action, { id: _id}, function(data){
							confirmDlg.hide();

							if(data.status==1){
								_img_holder.remove();
								myAlert(data.msg,'myalert-success');
							}
							else if(data.status==2){
								window.location.href=base_url+'login-register';
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

		$(document).on("submit","#edit_review_form",function(e){

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

		$(document).on("submit", ".bank_form", function(e) {

			e.preventDefault();

			var _form = $(this);

			var _current_element=$(this).find("button[type='submit']");
			_current_element.prop('disabled', true);

			var _btn_text=_current_element.text();

			_current_element.html("<i class='fa fa-spinner fa-spin'></i>");

			$.ajax({
				type: 'POST',
				url: $(this).attr("action"),
				data: $(this).serialize(),
				dataType: 'json',
				success: function(data) {

					_current_element.text(_btn_text);
					_current_element.prop('disabled', false);

					if (data.status==1) {

						myAlert(data.msg,'myalert-success');
						_form.find("input, textarea").val("");

						$(".no-content").hide();

						$(".bank_form").hide();
						$(".bank_details").find('.address_details_block .address_details_item:not(:last)').remove();
						$(".bank_details").find(".address_details_block").prepend(data.bank_list);
						$(".bank_details").show();
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

		$(document).on("submit", "#contact_form", function(e){
			e.preventDefault();

			$(".process_loader").show();

			var _action=$(this).attr('action');
			var _formData = $(this).serialize();
			var _form = $(this);

			var _current_element=$(this).find("button[type='submit']");
			_current_element.prop('disabled', true);

			$.post(_action, _formData, function(data){

				$(".process_loader").hide();
				_current_element.prop('disabled', false);

				if(data.status==1){
					_form[0].reset();
					myAlert(data.msg,'myalert-success');
				}
				else{
					myAlert(data.msg,'myalert-danger');
				}

			}, "json");

		})

		$(document).on("submit", "#resetPassword", function (e) {

			var flag=false;

			var $inputs = $(this).find("input");
			$inputs.each(function(){
				if($(this).val() == '') {
					flag=true;
				}
			});

			if(flag){
				myAlert(Settings.all_required_field_err,"myalert-danger");
				return false;
			}

			var password=$(this).find("input[name='new_password']").val();
			var c_password=$(this).find("input[name='c_password']").val();

			if(password!=c_password){
				myAlert(Settings.password_confirm_pass_err,"myalert-danger");
				return false;
			}

			if(flag){
				return false;
			}
			else{
				return true;
			}
		});

		$(document).on("submit", "#profile_form", function(e) {
			
			e.preventDefault();
			var inputs = $("#profile_form :input[type='text']");
			var flag=false;

			inputs.each(function(){
				if($(this).val()==''){
					flag=true;
				}
			});

			var _current_element=$(this).find("button[type='submit']");
			_current_element.prop('disabled', true);

			var _btn_text=_current_element.text();

			_current_element.html("<i class='fa fa-spinner fa-spin'></i>");

			if(!flag){

				var formData = new FormData($(this)[0]);

				var _action=$(this).attr('action');

				$.ajax({
					url: _action,
					processData: false,
					contentType: false,
					type: 'POST',
					data: formData,
					dataType: 'json',
					success: function(data){

						_current_element.html(_btn_text);
						_current_element.prop("disabled", false);

						if(data.status==1){
							myAlert(data.msg,'myalert-success');
							$(".profile_img").css("background-image", "url('"+data.image+"')");
						}
						else{
							myAlert(data.msg,'myalert-danger');
						}
					}
				});
			}
			else{
				_current_element.html(_btn_text);
				_current_element.prop("disabled", false);
				myAlert(Settings.all_required_field_err,'myalert-danger');
				return false
			}
		})

		$(document).on("click", ".remove_profile", function(e){
			e.preventDefault();
			var confirmDlg = duDialog(null, Settings.confirm_msg, {
				init: true,
				dark: false, 
				buttons: duDialog.OK_CANCEL,
				okText: 'Proceed',
				callbacks: {
					okClick: function(e) {
						$(".dlg-actions").find("button").attr("disabled",true);
						$(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> Please wait..');

						var href = base_url+'user/remove_profile';
						$.ajax({
							url: href,
							type: 'POST',
							dataType: 'json',
							success: function(data){

								confirmDlg.hide();

								if(data.status==1){
									myAlert(data.msg,'myalert-success');
									$(".fileupload_img").attr('src',base_url+"assets/images/photo.jpg");
									$(".profile_img").css("background-image", "url('"+base_url+"assets/images/photo.jpg')");
								}
								else{
									myAlert(data.msg,'myalert-danger');
								}
							}
						});

					} 
				}
			});
			confirmDlg.show();
		});

		$(document).on("change",".update_product_qty", function(e){

			var _current_element=$(this);

			var cart_id=$(this).data("cart");
			var qty=$(this).val();
			
			var _action = base_url+'user/update_product_qty';

			$.post(_action, { cart_id: cart_id, qty: qty}, function(data){
				if(data.status==1){
					_current_element.parents("tr").find(".product-total-price").text(data.product_amount);
					$(".shop-table").find(".sub-total").text(data.sub_total);
					$(".shop-table").find(".delivery-charge").text(data.delivery_charge);
					$(".shop-table").find(".total-amount").text(data.total);
					myAlert(data.msg,'myalert-success');
				}
				else if(data.status==2){
					window.location.href=base_url+'login-register';
				}
				else{
					myAlert(data.msg,'myalert-danger');
				}
			}, "json");
		});

		$(document).on("click", ".remove_filter", function(e) {
			e.preventDefault();

			localStorage.removeItem("products_list");

			var uri = window.location.toString();

			var action = $(this).data("action");

			var url = '';
			var id = '';

			if (action == 'sort') {
				url = removeURLParameter(uri, 'sort');
				window.location.href = url;
			} else if (action == 'price') {
				url = removeURLParameter(uri, 'price_filter');
				window.location.href = url;
			}else if (action == 'brands') {
				id = $(this).data("id");
				$('.brand_sort[value=' + id + ']').prop('checked', false);
				$("#brand_sort").submit();
			}else if (action == 'size') {
				id = $(this).data("id");
				$('.size_sort[value=' + id + ']').prop('checked', false);
				$("#size_sort").submit();
			}
		});

		$(document).on("change", ".list_order", function(e) {

			var param=$('#sort_filter_form').serialize();

			if(history.pushState) {
				var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?'+param;
				window.history.pushState({path:newurl},'',newurl);
			}

			var target = "#products_list";

			$('html, body').animate({
				scrollTop: ($(target).offset().top)
			}, 100);

			setTimeout(location.reload(), 1000);
		});

		$(document).on("hover", ".unavailable_override", function(event){
			$(this).parent(".single-product3").hover();
		});

		$(document).on('show.bs.tab', '.shop-tab a[data-toggle="tab"]', function(e) {
			localStorage.setItem('activeTabProduct', $(e.target).attr('href'));
		});

		var activeTabProduct = localStorage.getItem('activeTabProduct');

		if (activeTabProduct) {
			$('.shop-tab a[href="' + activeTabProduct + '"]').tab('show');
			$(".shop-product-area").find("div").removeClass("active");
			$(".shop-product-area").find(activeTabProduct).addClass('active');
		}

		$(document).on("mouseover", "#stars li", function() {
			var onStar = parseInt($(this).data('value'), 10);

			$(this).parent().children('li.star').each(function(e) {
				if (e < onStar) {
					$(this).addClass('hover');
				} else {
					$(this).removeClass('hover');
				}
			});

		}).on('mouseout', function() {
			$(this).parent().children('li.star').each(function(e) {
				$(this).removeClass('hover');
			});
		});

		$(document).on("click", "#stars li", function(e) {
			$(".inp_rating").val(parseInt($(this).data('value'), 10));

			var onStar = parseInt($(this).data('value'), 10);
			var stars = $(this).parents("#stars").children('li.star');

			var i;

			for (i = 0; i < stars.length; i++) {

				$(stars[i]).removeClass('selected');
			}

			for (i = 0; i < onStar; i++) {
				$(stars[i]).addClass('selected');
			}
		});

		$(document).on("mousedown",".grow-btn", function() {
			var x = event.offsetX - 10;
			var y = event.offsetY - 10;
			$(this).append('<div class="grow-circle grow" style="left:' + x + 'px;top:' + y + 'px;"></div>')
		});

	})(jQuery);
});