  <footer>
    <div class="footer-container white-bg"> 
      <div class="footer-top-area ptb-50">
        <div class="container">
          <div class="row"> 
            <div class="col-md-4 col-sm-6">
              <div class="single-footer"> 
                <div class="footer-logo"> <a href="<?=base_url('/')?>" title="<?=$this->settings->app_name?>"><img src="<?=base_url('assets/images/').APP_LOGO_2?>" title="<?=$this->settings->app_name?>" alt="<?=$this->settings->app_name?>"></a> </div>
                <div class="footer-content">
                  <p>
                    <?php

                    $about_content=strip_tags($this->web_settings->about_content);
                    if(strlen($about_content) > 120){
                      echo substr($about_content,0,120).'...';
                    }
                    else{
                      echo $about_content;
                    }
                    ?>
                  </p>
                  <div class="contact">
                    <p>
                      <label><?=$this->lang->line('address_sort_lbl')?>:</label>
                      <?php echo $this->web_settings->address;?>
                    </p>
                    <p>
                      <label><?=$this->lang->line('phone_sort_lbl')?>:</label>
                      <a href="tel:<?=str_replace(' ', '', $this->web_settings->contact_number)?>" title="<?=$this->lang->line('phone_sort_lbl')?>"><?=$this->web_settings->contact_number?></a>
                    </p>
                    <p>
                      <label><?=$this->lang->line('email_sort_lbl')?>:</label>
                      <a href="mailto:<?=$this->web_settings->contact_email?>" title="<?=$this->lang->line('email_sort_lbl')?>"><?=$this->web_settings->contact_email?></a></p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-2 col-sm-6">
                <div class="single-footer mt-10">
                  <div class="footer-title">
                    <h3><?=$this->lang->line('about_section_lbl')?></h3>
                  </div>
                  <ul class="footer-info">
                    <?php 
                    if($this->web_settings->about_status=='true')
                    {
                      ?>
                      <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('about-us'); ?>" title="<?=$this->web_settings->about_page_title?>"><?=$this->web_settings->about_page_title?></a></li>
                    <?php } ?>
                    
                    <?php 
                    if($this->web_settings->terms_of_use_page_status=='true')
                    {
                      ?>
                      <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('terms-of-use'); ?>" title="<?=$this->web_settings->terms_of_use_page_title?>"><?=$this->web_settings->terms_of_use_page_title?></a></li>
                    <?php } ?>
                    <?php 
                    if($this->web_settings->privacy_page_status=='true')
                    {
                      ?>
                      <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('privacy'); ?>" title="<?=$this->web_settings->privacy_page_title?>"><?=$this->web_settings->privacy_page_title?></a></li>
                    <?php } ?>
                    <?php 
                    if($this->web_settings->refund_return_policy_status=='true')
                    {
                      ?>
                      <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('refund-return-policy'); ?>" title="<?=$this->web_settings->refund_return_policy_page_title?>"><?=$this->web_settings->refund_return_policy_page_title?></a></li>
                    <?php } ?>
                    <?php 
                    if($this->web_settings->cancellation_page_status=='true')
                    {
                      ?>
                      <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('cancellation'); ?>" title="<?=$this->web_settings->cancellation_page_title?>"><?=$this->web_settings->cancellation_page_title?></a></li>
                    <?php } ?>

                    <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('payments'); ?>" title="<?=$this->lang->line('payment_lbl')?>"><?=$this->lang->line('payment_lbl')?></a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md-2 col-sm-6">
                <div class="single-footer mt-10">
                  <div class="footer-title">
                    <h3><?=$this->lang->line('myaccount_section_lbl')?></h3>
                  </div>
                  <ul class="footer-info">
                    <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('my-account'); ?>" title="<?=$this->lang->line('myaccount_lbl')?>"><?=$this->lang->line('myaccount_lbl')?></a></li>

                    <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('my-orders'); ?>" title="<?=$this->lang->line('myorders_lbl')?>"><?=$this->lang->line('myorders_lbl')?></a></li>

                    <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('my-cart'); ?>" title="<?=$this->lang->line('shoppingcart_lbl')?>"><?=$this->lang->line('shoppingcart_lbl')?></a></li>

                    <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('wishlist'); ?>" title="<?=$this->lang->line('mywishlist_lbl')?>"><?=$this->lang->line('mywishlist_lbl')?></a></li>

                    <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('my-reviews'); ?>" title="<?=$this->lang->line('myreviewrating_lbl')?>"><?=$this->lang->line('myreviewrating_lbl')?></a></li>

                    <li><i class="fa fa-angle-double-right"></i><a href="<?php echo site_url('faq'); ?>" title="<?=$this->lang->line('faq_lbl')?>"><?=$this->lang->line('faq_lbl')?></a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md-4 col-sm-6">
                <div class="single-footer mt-10">
                  <?php 
                  if($this->settings->facebook_url!='' || $this->settings->twitter_url!='' || $this->settings->instagram_url!='' || $this->settings->youtube_url!='')
                  {
                    ?>
                    <div class="footer-title">
                      <h3><?=$this->lang->line('followus_section_lbl')?></h3>
                    </div>
                    <ul class="socil-icon mb-40">
                      <?php 
                      if($this->settings->facebook_url!='')
                      {
                        ?>
                        <li><a href="<?=$this->settings->facebook_url?>" title="Facebook" target="_blank"><i class="ion-social-facebook"></i></a></li>
                      <?php } ?>
                      <?php 
                      if($this->settings->twitter_url!='')
                      {
                        ?>
                        <li><a href="<?=$this->settings->twitter_url?>" title="Twitter" target="_blank"><i class="ion-social-twitter"></i></a></li>
                      <?php } ?>

                      <?php 
                      if($this->settings->instagram_url!='')
                      {
                        ?>
                        <li><a href="<?=$this->settings->instagram_url?>" title="Instagram" target="_blank"><i class="ion-social-instagram"></i></a></li>
                      <?php } ?>

                      <?php 
                      if($this->settings->youtube_url!='')
                      {
                        ?>
                        <li><a href="<?=$this->settings->youtube_url?>" title="Youtube" target="_blank"><i class="ion-social-youtube"></i></a></li>
                      <?php } ?>
                      
                    </ul>
                  <?php } ?>
                  <div class="footer-title">
                    <h3><?=$this->lang->line('downloadapps_section_lbl')?></h3>
                  </div>
                  <div class="footer-content"> 
                    <?php 
                    if($this->web_settings->android_app_url!='')
                    {
                      ?>
                      <a href="<?=$this->web_settings->android_app_url?>" target="_blank" title="google-play"><img src="<?=base_url('assets/images/google-play.png')?>" title="google-play" alt="google-play"></a> 
                    <?php } ?>
                    <?php 
                    if($this->web_settings->ios_app_url!='')
                    {
                      ?>
                      <a href="<?=$this->web_settings->ios_app_url?>" target="_blank" title="app-store"><img src="<?=base_url('assets/images/app-store.png')?>" title="app-store" alt="app-store"></a> </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="footer-bottom-area">
          <div class="container">
            <div class="row"> 
              <div class="col-md-6 col-sm-6">
                <div class="copyright-text">
                  <p><?=$this->web_settings->copyright_text?></p>
                </div>
              </div>
              <div class="col-md-6 col-sm-6">
                <div class="payment-img text-right"> <a href="javascript:void(0)" title="payment-methods"><img src="<?=base_url('assets/site_assets/img/payment/payment.png')?>" title="payment-methods" alt="payment-methods"></a> </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Product Quick Preview -->
      <div id="productQuickView" class="modal fade" role="dialog">
        <div class="modal-dialog"> 
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body"></div>
          </div>
        </div>
      </div>

      <div id="size_chart" class="modal">
        <div class="modal-dialog modal-confirm">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" style="font-weight: 600"><?=$this->lang->line('size_chart')?></h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding:0px;padding-top:15px;">
              <img src="" alt="no_data" title="no_data" class="size_chart_img">
              <h3 class="no_data"><?=$this->lang->line('no_data')?></h3>
            </div>
          </div>
        </div>
      </div>

    </footer>

    <div id="content" data-myalert data-myalert-max="1"></div>
    
    <style type="text/css">
      .radio-group{
        position: relative;
      }
      .radio_btn{
        display: inline-block;
        width: auto;
        height: auto;
        background-color: #eee;
        border: 2px solid #ddd;
        cursor: pointer;
        margin: 2px 1px;
        text-align: center;
        padding: 5px 15px;
        border-radius: 5px;
      }
      .radio_btn.selected{
        border-color: #ff5252;
      }
    </style>

    <div id="cartModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body"></div>
        </div>
      </div>
    </div>
    <?php
    if($this->session->flashdata('cart_msg')) {
      $message = $this->session->flashdata('cart_msg');
      unset($_SESSION['cart_msg']);
      ?>
      <script type="text/javascript">
        var _msg="<?=$message['message']?>";
        _msg=_msg.replace(/(<([^>]+)>)/ig,"");
        myAlert(_msg);
      </script>
        <?php
      }
      ?>

      <?php

      if($this->session->flashdata('response_msg')) {
        $message = $this->session->flashdata('response_msg');
        unset($_SESSION['response_msg']);
        ?>
        <script type="text/javascript">
          var _msg="<?=$message['message']?>";
          var _class='<?=($message['class']) ? $message['class'] : 'success'?>';

          if(_class=='error'){
            _class='danger';
          }
          _msg=_msg.replace(/(<([^>]+)>)/ig,"");
          myAlert(_msg,'myalert-'+_class);
        </script>
        <?php
      }
      ?>

      <?php 
      if($this->web_settings->libraries_load_from=='local'){
        ?>

        <script defer src="<?=base_url('assets/site_assets/js/bootstrap.min.js')?>"></script>

        <script type="text/javascript" src="<?=base_url('assets/site_assets/js/jquery.scrollUp.min.js')?>"></script>

        <script type="text/javascript" src="<?=base_url('assets/site_assets/js/jquery.meanmenu.min.js')?>"></script>

        <script defer type="text/javascript" src="<?=base_url('assets/site_assets/js/owl.carousel.min.js')?>"></script>

      <?php }else if($this->web_settings->libraries_load_from=='cdn'){ ?>

        <script defer type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/scrollup/2.4.1/jquery.scrollUp.min.js"></script>

        <script defer type="text/javascript" src="<?=base_url('assets/site_assets/js/jquery.meanmenu.min.js')?>"></script>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js"></script>
      <?php } ?>

      <?php
      echo put_cdn_footers();
      echo put_footers();
      ?>

      <script defer type="text/javascript" src="<?=base_url('assets/site_assets/js/jquery-ui.min.js')?>"></script>

      <script defer type="text/javascript" src="<?=base_url('assets/sweetalert/sweetalert.min.js')?>"></script>

      <script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

      <script defer type="text/javascript" src="<?=base_url('assets/site_assets/js/plugins.js')?>"></script>

      <script defer type="text/javascript" src="<?=base_url('assets/site_assets/js/custom_jquery.js')?>"></script>

      <script defer src="<?=base_url($this->vendor_dir.'duDialog-master/duDialog.min.js')?>"></script>

      <script defer src="<?=base_url('assets/site_assets/js/custom.js')?>"></script>

      <?=html_entity_decode($this->web_settings->footer_code)?>

    </body>
    </html>