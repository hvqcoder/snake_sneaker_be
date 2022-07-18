<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="author" content="">
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title> <?php if (isset($current_page)) { echo $current_page . ' | ';} ?><?php echo $this->settings->app_name; ?></title>

<meta name="description" content="<?=$this->web_settings->site_description ?>">
<meta name="keywords" content="<?=$this->web_settings->site_keywords ?>">
<meta name="theme-color" content="#ff5252">

<link rel="shortcut icon" type="image/png" href="<?=base_url('assets/images/'.$this->web_settings->web_favicon)?>" />

<meta name="description" content="<?=$this->web_settings->site_description?>">
<meta name="keywords" content="<?=$this->web_settings->site_keywords?>">

<link rel="stylesheet" href="<?= base_url('assets/site_assets/css/bootstrap.min.4.5.3.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/site_assets/css/style.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/site_assets/css/style.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/site_assets/css/responsive.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/site_assets/css/font-awesome.min.css') ?>">

<link rel="stylesheet" href="<?=base_url($this->vendor_dir.'/myalert/css/myalert.min.css')?>">
<link rel="stylesheet" href="<?=base_url($this->vendor_dir.'/myalert/css/myalert-theme.min.css')?>">

<link rel="stylesheet" href="<?= base_url('assets/site_assets/css/login-register.css') ?>">


</head>
<body class="login-signup-block">
  <div id="main-wrapper" class="h-100">
    <div class="container-fluid px-0 h-100">
      <div class="row no-gutters h-100">
        <div class="col-md-6">
          <div class="hero-wrap d-flex align-items-center h-100">
            <div class="hero-mask opacity-9"></div>
            <div class="hero-bg hero-bg-scroll" style="background-image:url('<?= base_url("assets/img/login-signup-bg-img.jpg")?>');"></div>
            <div class="hero-content mx-auto w-100 h-100 d-flex flex-column">
              <div class="row no-gutters">
                <div class="col-10 col-lg-9 mx-auto">
                  <div class="logo mt-50 mb-0 mb-md-0"> <a class="d-flex" href="<?= base_url('/') ?>" title="<?=$this->settings->app_name?>"><img class="login-signup-logo" src="<?= base_url('assets/images/'.$this->web_settings->web_logo_2)?>" alt="<?=$this->settings->app_name?>" title="<?=$this->settings->app_name?>"></a> </div>
                </div>
              </div>
              <div class="row no-gutters my-auto">
                <div class="col-10 col-lg-9 mx-auto">
                  <h1 class="mb-4"><?=$this->lang->line("login_heading1")?></h1>
                  <p class="lead mb-5"><?=$this->lang->line("login_heading2")?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6 d-flex align-items-center">
          <div class="container my-4">
            <div class="row">
              <div class="col-11 col-lg-9 col-xl-8 mx-auto">
                <h3 class="form-title-item mb-40"><?=$page_title?></h3>
                <div class="err"></div>
                <form id="loginForm" action="<?=site_url('frontend/login'); ?>" class="login-register-form" method="post">
                  <input type="hidden" name="preview_url" readonly="" value="<?php if(isset($_SERVER['HTTP_REFERER']) && $this->session->userdata('single_pre_url')==''){ echo str_replace(base_url('site/register'),'',$_SERVER['HTTP_REFERER']);}else { echo $this->session->userdata('single_pre_url'); $this->session->unset_userdata('single_pre_url'); }?>">
                  <div class="wizard-form-field">
                   <div class="wizard-form-input has-float-label">
                    <input type="email" name="email" autocomplete="off" placeholder="<?=$this->lang->line('email_place_lbl')?>">
                    <label><?=$this->lang->line('email_place_lbl')?></label>						
                  </div>
                  <div class="wizard-form-input has-float-label">
                    <input type="password" name="password" autocomplete="off" placeholder="<?=$this->lang->line('password_lbl')?>">
                    <label><?=$this->lang->line('password_lbl')?></label>
                  </div>
                  <?php 
                  if($this->web_settings->g_captcha=='true'){
                    ?>
                    <div class="wizard-form-input has-float-label">
                      <div class="g-recaptcha" id="google_recaptcha" data-sitekey="<?=$this->web_settings->g_captcha_site_key?>"></div>
                    </div>
                  <?php } ?>
                </div>                  
                <div class="row">
                  <div class="col-sm text-right"><a class="btn-link" href="<?=base_url('forgot-password')?>" title="<?=$this->lang->line('forgot_password_lbl')?>"><?=$this->lang->line('forgot_password_lbl')?></a></div>
                </div>
                <button class="btn form-button btn-block my-3 mt-4 mb-3 grow-btn" type="submit"><?=$this->lang->line('login_btn')?></button>
              </form>
              <p class="text-3 text-center text-muted mb-3"><?=$this->lang->line('dont_have_account_lbl')?> <a class="btn-link" href="<?=base_url('register')?>" title="<?=$this->lang->line('register_btn')?>"><?=$this->lang->line('register_btn')?></a></p>
              <?php 
              if($this->settings->google_login_status=='true' OR $this->settings->facebook_status=='true'){
                ?>
                <div class="socail-login-item">
                  <?php 
                  if($this->settings->google_login_status=='true'){
                    ?>
                    <label>
                      <a href="<?=site_url('redirectGoogle')?>" class="btn btn-lg btn-success btn-block btn-g-plus-item" title="<?=$this->lang->line('login_with_google_btn')?>"><i class="fa fa-google"></i> <?=$this->lang->line('login_with_google_btn')?></a>     
                    </label>
                    <?php
                  }
                  ?>
                  <?php 
                  if($this->settings->facebook_status=='true'){
                    ?>			
                    <label>
                      <a href="<?=site_url('redirectFacebook')?>" class="btn btn-lg btn-success btn-block btn-facebook-item" title="<?=$this->lang->line('login_with_facebook_btn')?>"><i class="fa fa-facebook"></i> <?=$this->lang->line('login_with_facebook_btn')?></a>     
                    </label>
                  </div>
                <?php } ?>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <!-- Login Form End --> 
    </div>
  </div>
  <div id="content" data-myalert data-myalert-max="1"></div>
</div>
<!--Footer ends--> 

<script type="text/javascript" src="<?= base_url('assets/site_assets/js/vendor/jquery-3.4.1.min.js') ?>"></script> 
<script type="text/javascript" src="<?=base_url('assets/site_assets/js/bootstrap.min.4.5.3.js')?>"></script>

<script src="<?=base_url($this->vendor_dir.'/myalert/js/myalert.min.js')?>"></script>

<?php 
if($this->web_settings->g_captcha=='true'){
  ?>
  <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer>
  </script>

  <script type="text/javascript">

    <?php 
    if($this->web_settings->g_captcha=='true'){
      ?>
      var onloadCallback = function() {
        grecaptcha.render('google_recaptcha', {
          'sitekey' : "<?=$this->web_settings->g_captcha_site_key?>"
        });
      };
    <?php } ?>

  </script>

<?php } ?>

<script type="text/javascript">

  $(document).on("submit", "#loginForm", function(e) {

    e.preventDefault();

    var submitBtn=$(this).find("button");

    var $inputs = $(this).find("input");
    var flag=false;

    $inputs.each(function(){

      if($(this).attr('readonly')) {
      }
      else{
        if($(this).val()=='') {
          $(this).css("border-color", "red");
          flag=true;
        }
        else{
          $inputs.css("border-color", "#ebebeb");
        }
      }
    });

    if(flag){
      myAlert('<?=$this->lang->line("required_email_password_err")?>',"myalert-danger");
      return false;
    }
    else{
      submitBtn.attr("disabled",true);
      submitBtn.html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');

      $.ajax({
        url:$(this).attr("action"),
        data: $(this).serialize(),
        type:'post',
        dataType:'json',
        success:function(data){

          <?php 
          if($this->web_settings->g_captcha=='true'){
            ?>
            var rcres = grecaptcha.getResponse();
            if(rcres.length){
              grecaptcha.reset();
            }
          <?php } ?>

          submitBtn.attr("disabled",false);
          submitBtn.html("<?=$this->lang->line('login_btn')?>");          

          if(data.status=='1'){
            window.location.href=data.preview_url;
          }
          else{
            myAlert(data.message,"myalert-danger");
          }
        },
        error : function(data) {
          myAlert(data.message,"myalert-danger");
        }
      });
    }
  });
</script>

<script type="text/javascript">

  $(document).on("mousedown",".grow-btn",function() {
    console.log('event', event)
    var x = event.offsetX - 10;
    var y = event.offsetY - 10;
    $('.grow-btn').append('<div class="grow-circle grow" style="left:' + x + 'px;top:' + y + 'px;"></div>')
  })
</script>

<?php
if($this->session->flashdata('response_msg')) {
  $message = $this->session->flashdata('response_msg');
  unset($_SESSION['response_msg']);
  ?>
  <script type="text/javascript">
    var _msg='<?=$message['message']?>';
    var _class='<?=$message['class']?>';

    if(_class=='error'){
      _class='danger';
    }
    _msg=_msg.replace(/(<([^>]+)>)/ig,"");
    myAlert(_msg,'myalert-'+_class);
  </script>
  <?php
}
?>
</body>
</html>