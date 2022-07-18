<?php 
$this->load->view('site/layout/breadcrumb'); 
$ci =& get_instance();
?>

<style type="text/css">
.msg-container{
	display: none;
	margin-bottom: 1rem;
}
</style>

<div class="product-list-grid-view-area mt-20">
	<div class="container">
		<div class="row"> 
			<div class="col-lg-3 col-md-3 mb_40"> 
				<?php $this->load->view('site/layout/sidebar_my_account'); ?>
			</div>
			<div class="col-lg-9 col-md-9">
				<?php 
				if(strcmp($this->session->userdata('user_type'), 'Normal')==0){
					?>
					<div class="my_profile_area_detail">
						<div class="checkout-title">
							<h3><?=$this->lang->line('change_password_lbl')?></h3>
						</div>
						<div class="msg-container"></div>
						<form action="" id="change_password_form" method="post">
							<div class="row">
								<div class="col-md-4">
									<div class="wizard-form-field">
										<div class="wizard-form-input has-float-label">
											<input type="password" name="old_password" value="" placeholder="<?=$this->lang->line('old_password_place_lbl')?>"  autocomplete="off">
											<label><?=$this->lang->line('old_password_place_lbl')?></label>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="wizard-form-field">
										<div class="wizard-form-input has-float-label">
											<input type="password" name="new_password" value="" placeholder="<?=$this->lang->line('new_password_place_lbl')?>"  autocomplete="off">
											<label><?=$this->lang->line('new_password_place_lbl')?></label>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="wizard-form-field">
										<div class="wizard-form-input has-float-label">
											<input type="password" name="confirm_password" value="" placeholder="<?=$this->lang->line('c_new_password_place_lbl')?>"  autocomplete="off">
											<label><?=$this->lang->line('c_new_password_place_lbl')?></label>
										</div>
									</div>
								</div>
								<div class="login-submit col-md-12">
									<button type="submit" class="form-button"><?=$this->lang->line('save_btn')?></button>			  
								</div>
							</div>			
						</form>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	$("#change_password_form").submit(function(e){

		var inputs = $("#change_password_form :input[type='password']");
		var flag=false;
		e.preventDefault();

		inputs.each(function(){
			if($(this).val()=='')
			{
				flag=true;
				
			}
		});

		if(!flag)
		{
			if($("input[name='confirm_password']").val()!=$("input[name='new_password']").val())
			{
				myAlert('<?=$this->lang->line("password_cpass_match_lbl")?>','myalert-danger');
				return false;
			}

			var href = '<?=base_url("user/change_password")?>';

			$.ajax({
				url: href,
				type: 'POST',
				data: $(this).serialize(),
				dataType: 'json',
				success: function(data){

					if(data.status==1){
						myAlert(data.msg,'myalert-success');
						inputs.val('')
					}
					else{
						myAlert(data.msg,'myalert-danger');
					}
				}
			});

		}
		else{
			myAlert('<?=$this->lang->line("all_required_field_err")?>','myalert-danger');
		}
	});

</script>