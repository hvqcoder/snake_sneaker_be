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

  <link rel="stylesheet" href="<?=base_url($this->vendor_dir.'/duDialog-master/duDialog.min.css')?>">
  <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/font-awesome.min.css') ?>">

  <link rel="stylesheet" href="<?=base_url($this->vendor_dir.'/myalert/css/myalert.min.css')?>">
  <link rel="stylesheet" href="<?=base_url($this->vendor_dir.'/myalert/css/myalert-theme.min.css')?>">

  <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/login-register.css') ?>">

</head>
<body class="login-signup-block">
  <div id="main-wrapper" class="h-100">
    <div class="container-fluid px-0 h-100">
      <div class="row no-gutters h-100">
        <div class="col-md-6 register-welcome-item">
          <div class="hero-wrap d-flex align-items-center h-100">
            <div class="hero-mask opacity-9"></div>
            <div class="hero-bg hero-bg-scroll" style="background-image:url('<?= base_url("assets/img/login-signup-bg-img.jpg")?>');background-attachment: fixed;"></div>
            <div class="hero-content mx-auto w-100 h-100 d-flex flex-column">
              <div class="row no-gutters">
                <div class="col-10 col-lg-9 mx-auto">
                  <div class="logo mt-50 mb-0 mb-md-0"> <a class="d-flex" href="<?= base_url('/') ?>" title="<?=$this->settings->app_name?>"><img class="login-signup-logo" src="<?= base_url('assets/images/'.$this->web_settings->web_logo_2)?>" title="<?=$this->settings->app_name?>" alt="<?=$this->settings->app_name?>"></a> </div>
                </div>
              </div>
              <div class="row no-gutters my-auto">
                <div class="col-10 col-lg-9 mx-auto">
                  <h1 class="mb-4"><?=$this->lang->line("register_heading1")?></h1>
                  <p class="lead mb-5"><?=$this->lang->line("register_heading2")?></p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6 d-flex align-items-center register-item-block">
          <div class="container my-4">
            <div class="row">
              <div class="col-11 col-lg-9 col-xl-8 mx-auto">
                <h3 class="form-title-item mb-40"><?=$this->lang->line('register_lbl')?></h3>
                <div class="err"></div>
                <form id="registerForm" action="<?=site_url('frontend/register')?>" class="login-register-form" method="post">
                  <input type="hidden" readonly="readonly" name="preview_url" value="<?php if(isset($_SERVER['HTTP_REFERER'])){ echo $_SERVER['HTTP_REFERER']; }?>">
                  <div class="step1-container">
                    <div class="wizard-form-field">
                     <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="user_name" value="" autocomplete="off" placeholder="<?=$this->lang->line('name_place_lbl')?>">
                        <label><?=$this->lang->line('name_place_lbl')?></label>						  
                      </div>
                    </div>
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="user_email" value="" autocomplete="off" placeholder="<?=$this->lang->line('email_place_lbl')?>">
                        <label><?=$this->lang->line('email_place_lbl')?></label>
                      </div>
                    </div>
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="password" name="user_password" value="" autocomplete="off" placeholder="<?=$this->lang->line('password_lbl')?>">
                        <label><?=$this->lang->line('password_lbl')?></label>
                      </div>
                    </div>
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="password" name="c_password" value="" autocomplete="off" placeholder="<?=$this->lang->line('cpassword_lbl')?>">
                        <label><?=$this->lang->line('cpassword_lbl')?></label>
                      </div>
                    </div>
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="user_phone" value="" autocomplete="off" placeholder="<?=$this->lang->line('phone_no_lbl')?>" onkeypress="return isNumberKey(event)" maxlength="15">
                        <label><?=$this->lang->line('phone_no_lbl')?></label>
                      </div>
                    </div>
                  </div>
                  <?php 
                  if($this->settings->email_otp_op_status){
                    ?>
                    <button class="btn form-button btn-block my-2 mb-3 btn-submit grow-btn" type="button"><?=$this->lang->line('submit_btn')?></button>
                    <?php
                  }
                  else{
                    ?>
                    <button class="btn form-button btn-block my-2 mb-3 btn-register grow-btn" type="button"><?=$this->lang->line('register_btn')?></button>
                    <?php
                  }
                  ?>
                </div>

                <div class="step2-container">
                  <p class="text-center sent_otp_success">
                    <img src="<?=base_url('assets/img/successful-icon.png')?>" title="successful-icon" alt="successful-icon">
                    <?=$this->lang->line('sent_otp_lbl')?>
                  </p>
                  <div class="wizard-form-field">
                    <div class="wizard-form-input has-float-label">
                      <input type="text" name="email_sent_code" value="" autocomplete="off" placeholder="<?=$this->lang->line('enter_code_lbl')?>">
                      <label><?=$this->lang->line('enter_code_lbl')?></label>
                    </div>
                  </div>
                  <div class="register-submit">
                    <button type="button" class="form-button btn_resend grow-btn" disabled="true" style="background-color: #bbb"><?=$this->lang->line('resend_btn')?></button><span>&nbsp;&nbsp;&nbsp;&nbsp;Wait <span id="countdown">60</span> Seconds</span>
                    <div class="clearfix"></div>
                    <br/>
                    <button type="button" class="form-button btn_back grow-btn"><?=$this->lang->line('back_btn')?></button>
                    <button type="submit" class="form-button btn-final-register grow-btn"><?=$this->lang->line('register_btn')?></button>
                  </div>
                </div>
              </form>
              <p class="text-3 text-center text-muted">Already have an account? <a class="btn-link" href="<?=base_url('login-register')?>" title="<?=$this->lang->line('login_lbl')?>"><?=$this->lang->line('login_lbl')?></a></p>                
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
    </div>
  </div>
  <div id="content" data-myalert data-myalert-max="1"></div>
</div>

<script type="text/javascript" src="<?= base_url('assets/site_assets/js/vendor/jquery-3.4.1.min.js') ?>"></script> 
<script type="text/javascript" src="<?=base_url('assets/site_assets/js/bootstrap.min.4.5.3.js')?>"></script>
<script src="<?=base_url($this->vendor_dir.'/duDialog-master/duDialog.min.js')?>"></script>

<script src="<?=base_url($this->vendor_dir.'/myalert/js/myalert.min.js')?>"></script>

<script type="text/javascript">
  $(".btn-submit").on("click",function(e){
    var submitBtn=$(this);
    var _container=$(".step1-container");
    var $inputs = $(".step1-container").find("input");
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
      myAlert('<?=$this->lang->line("all_required_field_err")?>',"myalert-danger");
      return false;
    }
    else{
      var name=_container.find("input[name='user_name']").val();
      var email=_container.find("input[name='user_email']").val();
      var password=_container.find("input[name='user_password']").val();
      var cpassword=_container.find("input[name='c_password']").val();

      if(IsEmail(email)==false && email!=''){

        myAlert('<?=$this->lang->line("invalid_email_format")?>',"myalert-danger");
        return false;
      }

      if(password!=cpassword){
        myAlert('<?=$this->lang->line("password_confirm_pass_err")?>',"myalert-danger");
        return false;
      }

      submitBtn.attr("disabled",true);
      submitBtn.html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');

      var href = '<?=base_url('frontend/check_email')?>';

      $.ajax({
        url: href,
        data: $inputs.serialize(),
        type: 'post',
        dataType: 'json',
        success:function(res){

          submitBtn.attr("disabled",false);
          submitBtn.html("<?=$this->lang->line('submit_btn')?>");

          if(res.success=='1'){
            myAlert(res.msg,"myalert-success");
            $(".process_loader").hide();
            $(".step1-container").slideUp();
            $(".step2-container").slideDown();
            resendOTP();
          }
          else if(res.success=='0'){
            myAlert(res.msg,"myalert-danger");
          }

        }
      })

    }
  });

  $(".btn-final-register").on("click",function(e){
    e.preventDefault();

    var submitBtn=$(this);

    var email=$(".step1-container :input[name='user_email']").val();
    var code=$(".step2-container :input[name='email_sent_code']").val();

    var href = '<?=base_url('frontend/verify_code')?>';

    submitBtn.attr("disabled",true);
    submitBtn.html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');

    $.ajax({
      url:href,
      data: {email: email,code: code},
      type: 'post',
      dataType: 'json',
      success:function(res){

        submitBtn.attr("disabled",false);
        submitBtn.html("<?=$this->lang->line('register_btn')?>");

        if(res.success=='1'){
          $("#registerForm").submit();
        }
        else{
          myAlert(res.msg,"myalert-danger");
        }
      },
      error : function(res) {
        myAlert(res,"myalert-danger");
      }
    });
  });

  $(".btn_resend").on("click",function(e){
    e.preventDefault();

    var submitBtn=$(this);

    var name=$("input[name='user_name']").val();
    var email=$("input[name='user_email']").val();

    var href = '<?=base_url('frontend/sent_code')?>';

    submitBtn.attr("disabled",true);
    submitBtn.html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');

    $.ajax({
      url:href,
      data: {"email": email, "name": name},
      type: 'post',
      dataType: 'json',
      success:function(res){

        submitBtn.html("<?=$this->lang->line('resend_btn')?>");

        if(res.success==1){
          myAlert(res.msg,"myalert-success");
          $("#countdown").html("60");
          $("#countdown").parent("span").show();
          resendOTP();
        }
        else{
          myAlert(res.msg,"myalert-danger");
        }
      }

    });

  });

  $(".btn_back").on("click",function(e){

    confirmDlg = duDialog(null, "<?=$this->lang->line('are_you_sure_msg')?>", {
      init: true,
      dark: false, 
      buttons: duDialog.OK_CANCEL,
      okText: 'Proceed',
      callbacks: {
        okClick: function(e) {
          $(".dlg-actions").find("button").attr("disabled",true);
          $(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> <?=$this->lang->line('please_wait_lbl')?>');

          confirmDlg.hide();

          $(".step2-container").slideUp();
          $(".step1-container").slideDown();

          $(".btn-submit").attr("disabled", false);

        } 
      }
    });
    confirmDlg.show();
  });

  $(".btn-register").on("click",function(e){
    e.preventDefault();
    $("#registerForm").submit();
  });

  function resendOTP() {
    var count = document.getElementById('countdown');
    timeoutfn = function(){

      if(parseInt(count.innerHTML) <= 0){
        clearInterval(this);

        $('.btn_resend').removeAttr("style");
        $('.btn_resend').attr("disabled", false);
        $("#countdown").parent("span").hide();
      }
      else{
        count.innerHTML = parseInt(count.innerHTML) - 1;
        setTimeout(timeoutfn, 1000);
      }
    };

    setTimeout(timeoutfn, 1000);
  }

  function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57)){
      return false;
    }
    return true;
  }

  function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(!regex.test(email)) {
      return false;
    }else{
      return true;
    }
  }
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