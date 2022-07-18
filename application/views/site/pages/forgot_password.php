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
            <div class="hero-bg hero-bg-scroll" style="background-image:url('<?= base_url("assets/img/forgotten-pass-left-img-dcn-and-dentists.png")?>');"></div>
            <div class="hero-content mx-auto w-100 h-100 d-flex flex-column">
              <div class="row no-gutters">
                <div class="col-10 col-lg-9 mx-auto">
                  <div class="logo mt-50 mb-0 mb-md-0"> <a class="d-flex" href="<?= base_url('/') ?>" title="<?=$this->settings->app_name?>"><img class="login-signup-logo" src="<?= base_url('assets/images/'.$this->web_settings->web_logo_2)?>" title="<?=$this->settings->app_name?>" alt="<?=$this->settings->app_name?>"></a> </div>
                </div>
              </div>
              <div class="row no-gutters my-auto">
                <div class="col-10 col-lg-9 mx-auto">
                  <h1 class="mb-4"><?=$this->lang->line("reset_password_heading1")?></h1>
                  <p class="lead mb-5"><?=$this->lang->line("reset_password_heading2")?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Welcome Text End --> 

        <!-- Login Form -->
        <div class="col-md-6 d-flex align-items-center">
          <div class="container my-4">
            <div class="row">
              <div class="col-11 col-lg-9 col-xl-8 mx-auto">
                <h3 class="form-title-item mb-40"><?=$page_title?></h3>
                <div class="err"></div>
                <form id="forgotPasswordForm" action="<?=site_url('frontend/forgot_password')?>" class="login-register-form" method="post">
                 <div class="wizard-form-field">
                  <div class="wizard-form-input has-float-label">
                    <input type="email" name="email" autocomplete="off" placeholder="<?=$this->lang->line('registered_email_lbl')?>">
                    <label><?=$this->lang->line('registered_email_lbl')?></label>						
                  </div>
                </div>
                <button class="btn form-button btn-block my-3 mb-3" type="submit"><?=$this->lang->line('reset_password_send_btn')?></button>
              </form>
              <p class="text-3 text-center text-muted">Already have an account? <a class="btn-link" href="<?=base_url('login-register')?>" title="<?=$this->lang->line('login_lbl')?>"><?=$this->lang->line('login_lbl')?></a></p> 
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

<!--Scripts starts-->
<script type="text/javascript" src="<?= base_url('assets/site_assets/js/vendor/jquery-3.4.1.min.js') ?>"></script> 
<script type="text/javascript" src="<?=base_url('assets/site_assets/js/bootstrap.min.4.5.3.js')?>"></script>

<script src="<?=base_url($this->vendor_dir.'/myalert/js/myalert.min.js')?>"></script>

<script type="text/javascript">

  $(document).on("submit", "#forgotPasswordForm", function(e) {
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
      myAlert('<?=$this->lang->line("email_require_lbl")?>',"myalert-danger");
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

          submitBtn.attr("disabled",false);
          submitBtn.html("<?=$this->lang->line('reset_password_send_btn')?>");          

          if(data.status=='1'){
            window.location.href=data.redirectTo;
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