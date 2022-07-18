<?php 
$this->load->view('site/layout/breadcrumb');
?>
<section class="my-account-area mt-20 mb-20">
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3">
        <div class="customer-login-register register-pt-0">
          <?php 
          if($link_err!=''){
            ?>
            <div>
              <div class="form-register-title">
                <h2><?=$this->lang->line('something_went_wrong_err')?></h2>
              </div>
              <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?=$link_err?>
              </div>
            </div>
          <?php }else if($link_err==''){ ?>
            <div class="form-register-title">
              <h2><?=$this->lang->line('reset_password_lbl')?></h2>
            </div>
            <div class="register-form">

              <?php 
              echo form_open('frontend/reset_password_form', ['id' => 'resetPassword']);
              ?>
              <input type="hidden" name="requestToken" value="<?=$this->input->get('requestToken')?>">
              <div class="wizard-form-field">
                <div class="wizard-form-input has-float-label">
                  <input type="password" name="new_password" value="" autocomplete="off" placeholder="<?=$this->lang->line('new_password_lbl')?>">
                  <label><?=$this->lang->line('new_password_lbl')?></label>
                </div>
              </div>

              <div class="wizard-form-field">
                <div class="wizard-form-input has-float-label">
                  <input type="password" name="c_password" value="" autocomplete="off" placeholder="<?=$this->lang->line('c_new_password_lbl')?>">
                  <label><?=$this->lang->line('c_new_password_lbl')?></label>
                </div>
              </div>
              <div class="register-submit">
                <button type="submit" class="form-button" name="btn_reset_password"><?=$this->lang->line('reset_btn')?></button>
              </div>
              <?php echo form_close(); ?>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>