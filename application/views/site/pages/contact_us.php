<?php 
$this->load->view('site/layout/breadcrumb'); 
$ci =& get_instance();
?>
<?php 
if($this->web_settings->g_captcha=='true'){
	?>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php } ?>

<style type="text/css">
	.chosen-container-single .chosen-single div b {
		background: url(<?php echo base_url('assets/site_assets/img/chosen-sprite.png'); ?>) no-repeat 0px 10px;
	}
</style>

<section class="contact-form-area mt-20 mb-30">
	<div class="container">
		<div class="row"> 
			<div class="col-md-8">
				<div class="contact-form-title">
					<h2><?=$this->lang->line('form_section_lbl')?></h2>
				</div>
				<div class="contact-form mb-30">
					<form id="contact_form" action="<?php echo site_url('frontend/contact_form'); ?>" method="post">
						<div class="row">	
							<div class="contact-input wizard-form-field">
								<div class="col-md-6">
									<div class="wizard-form-input has-float-label">
										<input type="text" name="name" required="" placeholder="<?=$this->lang->line('name_place_lbl')?> *">
										<label><?=$this->lang->line('name_place_lbl')?></label>
									</div>	
								</div>
								<div class="col-md-6">
									<div class="wizard-form-input has-float-label">
										<input type="email" name="email" required="" placeholder="<?=$this->lang->line('email_lbl')?> *">
										<label><?=$this->lang->line('email_lbl')?></label>
									</div>
								</div>
								<div class="col-md-12">
									<div class="wizard-form-input has-float-label">	
										<select name="subject_id" class="chosen-select" required="">
											<option value=""><?=$this->lang->line('subject_lbl')?></option>
											<?php 
											foreach ($contact_subjects as $key => $value) {
												echo '<option value="'.$value->id.'">'.$value->title.'</option>';
											}
											?>
										</select>
									</div>	  
								</div>
								<div class="col-md-12">
									<div class="wizard-form-input has-float-label">
										<textarea name="message" required="" cols="40" rows="10" placeholder="<?=$this->lang->line('message_lbl')?> *"></textarea>
										<label><?=$this->lang->line('message_lbl')?></label>
									</div>	
								</div>
								
								<?php 
								if($this->web_settings->g_captcha=='true'){
									?>
									<div class="col-md-12">
										<div class="wizard-form-input has-float-label">
											<div class="g-recaptcha" data-sitekey="<?=$this->web_settings->g_captcha_site_key?>"></div>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
						<div class="contact-submit">
							<button type="submit" class="form-button btn_send grow-btn"><?=$this->lang->line('send_msg_btn')?></button>
						</div>						
					</form>
					<p class="form-messege"></p>
				</div>
			</div>
			<div class="col-md-4">
				<div class="contact-address-info">
					<div class="contact-form-title">
						<h2><?=$this->web_settings->contact_page_title?></h2>
					</div>
					<div class="contact-address mb-30">
						<ul>
							<li><i class="fa fa-map"></i> <?=$this->web_settings->address?></li>
							<li><i class="fa fa-phone"></i> <?=$this->web_settings->contact_number?></li>
							<li><i class="fa fa-envelope-o"></i> <?=$this->web_settings->contact_email?></li>
						</ul>
					</div>            
				</div>
			</div>
		</div>
	</div>
</section>