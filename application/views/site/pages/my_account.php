<?php 
$this->load->view('site/layout/breadcrumb'); 
$ci =& get_instance();

define('IMG_PATH', base_url('assets/images/users/'));

if($user_data->user_image!='' && file_exists('assets/images/users/'.$user_data->user_image)){
	$user_img=IMG_PATH.$user_data->user_image;
}
else{
	$user_img=base_url('assets/images/photo.jpg');
}

?>

<style type="text/css">
	.file-custom:after {
		content: "<?=$this->lang->line('choose_file_place_lbl')?>";
	}
	.file-custom:before {
		content: "<?=$this->lang->line('browse_file_place_lbl')?>";
	}
	.fileupload_img{
		width:50px;
		height:50px;
		margin-top:0px;
		border:2px solid #e5e5e5;
		border-radius:4px;
	}
</style>

<div class="product-list-grid-view-area mt-20">
	<div class="container">
		<div class="row"> 
			<div class="col-lg-3 col-md-3 mb_40"> 
				<?php $this->load->view('site/layout/sidebar_my_account'); ?>
			</div>
			<div class="col-lg-9 col-md-9">
				<div class="my_profile_area_detail">
					<div class="checkout-title">
						<h3><?=$this->lang->line('my_profile_lbl')?></h3>
					</div>
					<form action="<?=site_url('user/update_profile')?>" id="profile_form" method="post" enctype="multipart/form-data">

						<div class="row">
							<div class="col-md-6">
								<div class="wizard-form-field">
									<div class="wizard-form-input has-float-label">
										<input type="text" name="user_name" value="<?=$user_data->user_name?>" <?=($user_data->user_type!='Normal') ? 'readonly=""' : ''?> placeholder="<?=$this->lang->line('name_place_lbl')?>">
										<label><?=$this->lang->line('name_place_lbl')?></label>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="wizard-form-field">
									<div class="wizard-form-input has-float-label">
										<input type="text" name="user_email" value="<?=$user_data->user_email?>" <?=($user_data->user_type=='Normal') ? 'required=""' : ''?> <?=($user_data->user_email!='') ? 'readonly=""' : ''?> placeholder="<?=$this->lang->line('email_place_lbl')?>">
										<label><?=$this->lang->line('email_place_lbl')?></label>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="wizard-form-field">
									<div class="wizard-form-input has-float-label">
										<input type="text" name="user_phone" value="<?=$user_data->user_phone?>" placeholder="<?=$this->lang->line('phone_no_place_lbl')?>" onkeypress="return isNumberKey(event)" maxlength="15">
										<label><?=$this->lang->line('phone_no_place_lbl')?></label>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<label class="file">
									<input type="file" id="file" name="file_name" aria-label="Profile picture browse" accept=".jpg, .png, jpeg, .PNG, .JPG, .JPEG">
									<span class="file-custom"></span>
								</label>
							</div>
							<div class="col-md-2">
								<img class="fileupload_img" alt="profile" title="profile" src="<?=$user_img?>" style="">
								<a href="javascript:void(0)"class="_tooltip remove_profile" data-toggle="tooltip" title="<?=$this->lang->line('remove_profile_lbl')?>" data-original-title="<?=$this->lang->line('remove_profile_lbl')?>"><i class="fa fa-close"></i></a>
							</div>
							<div class="clearfix"></div>
							<div class="login-submit col-md-12">
								<button type="submit" class="form-button"><?=$this->lang->line('save_btn')?></button>  
							</div>
						</div>			
					</form>
				</div>
			</div>
		</div>
	</div>
</div>