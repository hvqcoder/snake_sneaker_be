<?php
define('APP_NAME', $this->web_settings->site_name);
define('APP_FAVICON', $this->web_settings->web_favicon);
define('APP_LOGO', $this->web_settings->web_logo_2);
define('APP_LOGO_2', $this->web_settings->web_logo_1);

if (isset($sharing_img) and $sharing_img != '') {
  $sharing_img = $sharing_img;
  $sharing_wp_img = $sharing_img;
} else {
  $sharing_img = base_url('assets/images/facebook_share_banner.png');
  $sharing_wp_img = base_url('assets/images/wp_share_banner.png');
}

$ci = &get_instance();

if (empty($product)) {
  $array_items = array('single_pre_url');
  $this->session->unset_userdata($array_items);
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="author" content="">
  <title> <?=(isset($current_page)) ? $current_page . ' | ' : '';?><?php echo APP_NAME; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="canonical" href="<?=base_url()?>" />

  <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/images/') . APP_FAVICON ?>" />

  <meta name="description" content="<?= (empty($product) or $product->seo_meta_description == '') ? $this->web_settings->site_description : $product->seo_meta_description ?>">

  <meta name="keywords" content="<?= (empty($product) or $product->seo_keywords == '') ? $this->web_settings->site_keywords : $product->seo_keywords ?>">

  <meta property="og:type" content="article" />

  <meta property="og:title" content="<?=(isset($current_page)) ? $current_page.' | ' : ''?><?php echo APP_NAME; ?>" />

  <meta property="og:description" content="<?= (empty($product) or $product->seo_meta_description == '') ? $this->web_settings->site_description : $product->seo_meta_description ?>" />

  <meta property="og:image" itemprop="image" content="<?= $sharing_wp_img ?>" />
  <meta property="og:url" content="<?= current_url() ?>" />
  <meta property="og:image:width" content="1024" />
  <meta property="og:image:height" content="1024" />
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:image" content="<?= $sharing_img ?>">
  <link rel="image_src" href="<?= $sharing_wp_img ?>">

  <meta name="theme-color" content="#ff5252">

  <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/ionicons.min.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/font-awesome.min.css') ?>">

  <?php
  echo put_headers();
  echo put_cdn_headers();
  ?>

  <?php
  if ($this->web_settings->libraries_load_from == 'local') {
    ?>
    <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/normalize.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/jquery-ui.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/owl.carousel.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/bootstrap.min.css') ?>">

    <script src="<?= base_url('assets/site_assets/js/vendor/jquery-3.4.1.min.js') ?>"></script>

  <?php } else if ($this->web_settings->libraries_load_from == 'cdn') { ?>
    <!-- Include CDN Files -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- End CDN Files -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <?php
  }
  ?>

  <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/meanmenu.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/default.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/style.min.css') ?>">

  <link rel="stylesheet" href="<?=base_url($this->vendor_dir.'/duDialog-master/duDialog.min.css')?>">

  <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/cust_style.css') ?>">

  <link rel="stylesheet" href="<?= base_url('assets/site_assets/css/responsive.css') ?>">
  
  <!-- Sweetalert popup -->
  <link rel="stylesheet" type="text/css" href="<?= base_url('assets/sweetalert/sweetalert.css') ?>">

  <script src="<?= base_url('assets/site_assets/js/notify.min.js') ?>"></script>

  <link rel="stylesheet" href="<?=base_url($this->vendor_dir.'myalert/css/myalert.min.css')?>">
  <link rel="stylesheet" href="<?=base_url($this->vendor_dir.'myalert/css/myalert-theme.min.css')?>">

  <script src="<?=base_url($this->vendor_dir.'myalert/js/myalert.min.js')?>"></script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

  <style type="text/css">
  
    .social_img {
      width: 20px !important;
      height: 20px !important;
      position: absolute;
      top: 5px;
      left: 25px;
      z-index: 1;
      margin: 5px;
    }
    #cartForm .radio_btn {
      margin: 3px 4px 3px 0;
      text-align: center;
      padding: 5px 10px !important;
      min-width: 40px;
      border-radius: 4px;
    }

  </style>


  <?= html_entity_decode($this->web_settings->header_code) ?>

  <script type="text/javascript">
    var Settings = {
      base_url: '<?= base_url() ?>',
      currency_code: '<?=$this->settings->app_currency_html_code?>',
      stripe_pk: '<?=$this->settings->stripe_key?>',
      confirm_msg: '<?=$this->lang->line('are_you_sure_msg')?>',
      ord_cancel_confirm: '<?= $this->lang->line('ord_cancel_confirm_lbl') ?>',
      product_cancel_confirm: '<?= $this->lang->line('product_cancel_confirm_lbl') ?>',
      err_cart_item_buy: '<?= $this->lang->line('err_cart_item_buy_lbl') ?>',
      err_shipping_address: '<?= $this->lang->line('no_shipping_address_err') ?>',
      err_something_went_wrong: '<?= $this->lang->line('something_went_wrong_err') ?>',
      please_wait: '<?=$this->lang->line('please_wait_lbl')?>',
      all_required_field_err: '<?= $this->lang->line('all_required_field_err') ?>',
      password_confirm_pass_err: '<?= $this->lang->line('password_confirm_pass_err') ?>'
    }
  </script>

</head>
  <!-- <body oncontextmenu="return false"> -->
  <body>

    <div class="wrapper">
      <div class="se-pre-con"></div>
      <div class="process_loader"></div>
      <header>
        <div class="header-container">
          <div class="header-middel-area">
            <div class="container">
              <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-12">
                  <div class="logo"> <a href="<?=base_url('/')?>" title="<?=$this->settings->app_name?>"><img src="<?=base_url('assets/images/'.$this->web_settings->web_logo_2)?>" title="<?=$this->settings->app_name?>" alt="<?=$this->settings->app_name?>"></a> </div>
                </div>
                <div class="col-md-7 col-sm-6 col-xs-12">
                  <div class="search-box-area">
                    <form accept-charset="utf-8" action="<?=base_url('search-result')?>" id="search_form" method="get">
                      <div class="select-area">
                        <select name="category" data-placeholder="Choose Category" class="select" tabindex="1">
                          <option value="">All Categories</option>
                          <?php
                          foreach ($ci->get_category() as $key => $row_category) {
                            ?>
                            <option value="<?=$row_category->category_slug?>" <?=($this->input->get('category')==$row_category->category_slug) ? 'selected' : '' ?>><?= $row_category->category_name?></option>
                            <?php
                          }
                          ?>
                        </select>
                      </div>
                      <div class="search-box">
                        <input type="text" name="keyword" id="search" placeholder="<?= $this->lang->line('search_lbl') ?>" value="<?= $this->input->get('keyword') != '' ? $this->input->get('keyword') : '' ?>" required="">
                        <button type="submit"><i class="ion-ios-search-strong"></i></button>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12">
                  <div class="mini-cart-area">
                    <ul>
                      <li>
                        <?php
                        if (!check_user_login()) {
                          ?>
                          <a href="javascript:void(0)" title="person">
                            <i class="ion-android-person"></i>
                          </a>
                          <?php
                        } else {

                          $user_img = $this->db->get_where('tbl_users', array('id' => $this->session->userdata('user_id')))->row()->user_image;

                          if ($user_img == '' or !file_exists('assets/images/users/' . $user_img)) {
                            $user_img = base_url('assets/images/photo.jpg');
                          } else {

                            $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $user_img);

                            $user_img = base_url() . $ci->_generate_thumbnail('assets/images/users/', $thumb_img_nm, $user_img, 200, 200);
                          }

                          ?>
                          <?php
                          if ($this->session->userdata('user_type') == 'Google') {
                            echo '<img src="' . base_url('assets/img/google-logo.png') . '" class="social_img" title="google-logo" alt="google-logo">';
                          } else if ($this->session->userdata('user_type') == 'Facebook') {
                            echo '<img src="' . base_url('assets/img/facebook-icon.png') . '" class="social_img" title="facebook-logo" alt="facebook-logo">';
                          }
                          ?>
                          <a href="javascript:void(0)" title="profile-image" class="profile_img" style="background-image: url('<?= $user_img ?>');background-size: cover;">
                          </a>
                          <?php
                        }
                        ?>
                        <ul class="cart-dropdown user_login">
                          <?php
                          if (!check_user_login()) {
                            ?>
                            <li class="cart-button"> <a href="<?php echo site_url('login-register'); ?>" class="button2 grow-btn" title="<?= $this->lang->line('login_register_btn') ?>"><?= $this->lang->line('login_register_btn') ?></a>
                            </li>
                          <?php } else { ?>
                            <li class="cart-item"><a href="<?php echo site_url('my-account'); ?>" title="<?= $this->lang->line('myaccount_lbl') ?>"><i class="ion-android-person"></i> <?= $this->lang->line('myaccount_lbl') ?></a></li>

                            <li class="cart-item"><a href="<?php echo site_url('my-orders'); ?>" title="<?= $this->lang->line('myorders_lbl') ?>"><i class="ion-bag"></i> <?= $this->lang->line('myorders_lbl') ?></a></li>

                            <li class="cart-item"><a href="<?php echo site_url('my-cart'); ?>" title="<?= $this->lang->line('shoppingcart_lbl') ?>"><i class="ion-ios-cart-outline"></i> <?= $this->lang->line('shoppingcart_lbl') ?></a></li>
                            <li class="cart-item"><a href="<?php echo site_url('wishlist'); ?>" title="<?= $this->lang->line('mywishlist_lbl') ?>"><i class="ion-ios-list-outline"></i> <?= $this->lang->line('mywishlist_lbl') ?></a></li>

                            <li class="cart-item"><a href="<?= site_url('frontend/logout') ?>" class="btn_logout" title="<?= $this->lang->line('logout_lbl') ?>"><i class="ion-log-out"></i> <?= $this->lang->line('logout_lbl') ?></a></li>
                          <?php } ?>
                        </ul>
                      </li>
                      <li>
                        <a href="<?php echo site_url('my-cart'); ?>" title="<?= $this->lang->line('shoppingcart_lbl') ?>">
                          <i class="ion-android-cart"></i>
                          <span class="cart-add cart-item-count"><?= count($ci->get_cart()) ?></span>
                        </a>
                        <ul class="cart-dropdown cart-items">
                          <?php

                          if (check_user_login()) {

                            $row = $ci->get_cart(3);
                            if (!empty($row)) {
                              foreach ($row as $key => $value) {

                                $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value->featured_image);

                                $img_file = $ci->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $value->featured_image, 50, 50);

                                ?>
                                <li class="cart-item">
                                  <div class="cart-img" style="width: auto"> <a href="javascript:void(0)" title="<?=$value->product_title?>"><img src="<?= base_url($img_file)?>" alt="<?=$value->product_title?>" title="<?=$value->product_title?>" style="width: 68px;height: 68px"></a> </div>
                                  <div class="cart-content">
                                    <h4>
                                      <a href="<?=site_url('product/'.$value->product_slug)?>" title="<?=$value->product_title?>">
                                        <?php
                                        if (strlen($value->product_title) > 20) {
                                          echo substr(stripslashes($value->product_title), 0, 20) . '...';
                                        } else {
                                          echo $value->product_title;
                                        }
                                        ?>
                                      </a>
                                    </h4>
                                    <p class="cart-quantity"><?= $this->lang->line('qty_lbl') ?>: <?= $value->product_qty ?></p>
                                    <p class="cart-price">
                                      <?php
                                      $price = number_format($value->selling_price * $value->product_qty, 2);
                                      if (strlen($price) > 20) {
                                        echo CURRENCY_CODE . ' ' . substr(stripslashes($price), 0, 20) . '...';
                                      } else {
                                        echo CURRENCY_CODE . ' ' . $price;
                                      }
                                      ?>
                                    </p>
                                  </div>
                                  <div class="cart-close"> <a href="<?php echo site_url('remove-to-cart/' . $value->id); ?>" class="btn_remove_cart" title="Remove"><i class="ion-android-close"></i></a> </div>
                                </li>

                              <?php } ?>
                              <?php
                              if (count($ci->get_cart()) > 3) {
                                echo '<li class="cart-item text-center">
                                <h4 style="font-weight: 500">' . str_replace('###', (count($ci->get_cart()) - 3), $this->lang->line('remain_cart_items_lbl')) . '</h4>
                                </li>';
                              }
                              ?>

                              <li class="cart-button"> <a href="<?php echo site_url('my-cart'); ?>" class="button2 grow-btn" title="<?= $this->lang->line('view_cart_btn') ?>"><?= $this->lang->line('view_cart_btn') ?></a> <a href="<?php echo site_url('checkout'); ?>" class="button2 grow-btn" title="<?= $this->lang->line('checkout_btn') ?>"><?= $this->lang->line('checkout_btn') ?></a>
                              </li>
                              <?php
                            } else {
                              ?>
                              <li class="cart-item text-center" style="padding: 15px">
                                <h4 style="font-weight: 500"><i class="ion-android-cart"></i> <?= $this->lang->line('empty_cart_lbl') ?></h4>
                              </li>
                              <?php
                            }
                        }   // end of session check
                        else {
                          ?>
                          <li class="cart-item text-center" style="padding: 15px">
                            <h4 style="font-weight: 500"><?= $this->lang->line('login_status_lbl') ?></h4>
                          </li>
                          <?php
                        }
                        ?>

                      </ul>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="header-bottom-area header-inner-area header-sticky">
          <div class="container">
            <div class="row">
              <div class="col-md-12">
                <div class="logo-sticky">
                  <a href="<?= base_url('/') ?>" title="<?=$this->settings->app_name?>">
                    <img src="<?= base_url('assets/images/') . APP_LOGO ?>" title="<?=$this->settings->app_name?>" alt="<?=$this->settings->app_name?>" style="max-width: 250px !important;height: 32px;min-width: 100%;">
                  </a>
                </div>
                <div class="main-menu-area">
                  <nav>
                    <ul class="main-menu">
                      <li <?php if (isset($current_page) && $current_page == $this->lang->line('home_lbl')) {
                        echo 'class="active"';
                      } ?>><a href="<?= base_url('/') ?>" title="<?= $this->lang->line('home_lbl') ?>"><?= $this->lang->line('home_lbl') ?></a></li>

                      <li <?php if (isset($current_page) && $current_page == $this->lang->line('category_lbl')) {
                        echo 'class="active"';
                      } ?>><a href="<?= base_url('/category') ?>" title="<?= $this->lang->line('category_lbl') ?>"><?= $this->lang->line('category_lbl') ?></a>
                      <ul class="dropdown">
                        <?php
                        $n = 1;
                        foreach ($ci->get_category() as $key => $row) {

                          $counts = $ci->getCount('tbl_sub_category', array('category_id' => $row->id, 'status' => '1'));

                          if ($counts > 0) {
                            $url = base_url('category/' . $row->category_slug);
                          } else {
                            $url = base_url('category/products/' . $row->id);
                          }

                          ?>
                          <li>
                            <a href="<?= $url ?>" title="<?=$row->category_slug?>">
                              <?php
                              if (strlen($row->category_name) > 30) {
                                echo substr(stripslashes($row->category_name), 0, 30) . '...';
                              } else {
                                echo $row->category_name;
                              }
                              ?>
                              <?php if ($counts > 0) {
                                echo '<i class="fa fa-angle-right"></i>';
                              } ?></a>
                              <?php
                              if ($counts > 0) {
                                ?>
                                <ul class="dropdown">
                                  <?php
                                  $sub_category_list = $ci->get_sub_category($row->id);
                                  $i = 1;
                                  foreach ($sub_category_list as $key1 => $row1) {
                                    ?>
                                    <li>
                                      <a href="<?= site_url('category/' . $row->category_slug . '/' . $row1->sub_category_slug) ?>" title="<?=$row1->sub_category_slug?>">
                                        <?php
                                        if (strlen($row1->sub_category_name) > 30) {
                                          echo substr(stripslashes($row1->sub_category_name), 0, 30) . '...';
                                        } else {
                                          echo $row1->sub_category_name;
                                        }
                                        ?>
                                      </a>
                                    </li>
                                  <?php } ?>
                                </ul>
                              <?php } ?>
                            </li>
                          <?php } ?>
                        </ul>
                      </li>

                      <li <?php if (isset($current_page) && strcmp($current_page, $this->lang->line('offer_lbl')) == 0) {
                        echo 'class="active"';
                      } ?>><a href="<?= base_url('/offers') ?>" title="<?= $this->lang->line('offer_lbl') ?>"><?= $this->lang->line('offer_lbl') ?></a></li>
                      <li <?php if (isset($current_page) && strcmp($current_page, $this->lang->line('todays_deal_lbl')) == 0) {
                        echo 'class="active"';
                      } ?>><a href="<?= base_url('/todays-deals') ?>" title="<?= $this->lang->line('todays_deal_lbl') ?>"><?= $this->lang->line('todays_deal_lbl') ?></a></li>
                      <li <?php if (isset($current_page) && $current_page == $this->lang->line('contactus_lbl')) {
                        echo 'class="active"';
                      } ?>><a href="<?php echo site_url('contact-us'); ?>" title="<?= $this->lang->line('contactus_lbl') ?>"><?= $this->lang->line('contactus_lbl') ?></a></li>
                    </ul>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mobile-menu-area hidden-sm hidden-md hidden-lg">
          <div class="container">
            <div class="row">
              <div class="col-xs-12">
                <div class="mobile-menu">
                  <nav>
                    <ul>
                      <li <?php if (isset($current_page) && $current_page == $this->lang->line('home_lbl')) {
                        echo 'class="active"';
                      } ?>><a href="<?= base_url('/') ?>" title="<?= $this->lang->line('home_lbl') ?>"><?= $this->lang->line('home_lbl') ?></a></li>
                      <li <?php if (isset($current_page) && $current_page == $this->lang->line('category_lbl')) {
                        echo 'class="active"';
                      } ?>><a href="<?= base_url('/category') ?>" title="<?= $this->lang->line('category_lbl') ?>"><?= $this->lang->line('category_lbl') ?></a>
                      <ul>
                        <?php
                        $n = 1;
                        foreach ($ci->get_category() as $key => $row) {

                          $counts = $ci->getCount('tbl_sub_category', array('category_id' => $row->id, 'status' => '1'));

                          if ($counts > 0) {
                            $url = base_url('category/' . $row->category_slug);
                          } else {
                            $url = base_url('category/products/' . $row->id);
                          }

                          ?>
                          <li>
                            <a href="<?= $url ?>" title="<?=$row->category_slug?>">
                              <?php
                              if (strlen($row->category_name) > 30) {
                                echo substr(stripslashes($row->category_name), 0, 30) . '...';
                              } else {
                                echo $row->category_name;
                              }
                              ?>
                            </a>
                            <?php
                            if ($counts > 0) {
                              ?>
                              <ul>
                                <?php
                                $sub_category_list = $ci->get_sub_category($row->id);
                                $i = 1;
                                foreach ($sub_category_list as $key1 => $row1) {
                                  ?>
                                  <li>
                                    <a href="<?= site_url('category/' . $row->category_slug . '/' . $row1->sub_category_slug) ?>" title="<?=$row1->sub_category_slug?>">
                                      <?php
                                      if (strlen($row1->sub_category_name) > 30) {
                                        echo substr(stripslashes($row1->sub_category_name), 0, 30) . '...';
                                      } else {
                                        echo $row1->sub_category_name;
                                      }
                                      ?>
                                    </a>
                                  </li>
                                <?php } ?>
                              </ul>
                            <?php } ?>
                          </li>
                        <?php } ?>
                      </ul>
                    </li>
                    <li <?php if (isset($current_page) && strcmp($current_page, $this->lang->line('offer_lbl')) == 0) {
                      echo 'class="active"';
                    } ?>><a href="<?= base_url('/offers') ?>" title="<?= $this->lang->line('offer_lbl') ?>"><?= $this->lang->line('offer_lbl') ?></a></li>
                    <li <?php if (isset($current_page) && strcmp($current_page, $this->lang->line('todays_deal_lbl')) == 0) {
                      echo 'class="active"';
                    } ?>><a href="<?= base_url('/todays-deals') ?>" title="<?= $this->lang->line('todays_deal_lbl') ?>"><?= $this->lang->line('todays_deal_lbl') ?></a></li>
                    <li <?php if (isset($current_page) && strcmp($current_page, $this->lang->line('contactus_lbl')) == 0) {
                      echo 'class="active"';
                    } ?>><a href="<?php echo site_url('contact-us'); ?>" title="<?= $this->lang->line('contactus_lbl') ?>"><?= $this->lang->line('contactus_lbl') ?></a></li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>