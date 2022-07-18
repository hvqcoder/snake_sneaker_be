<?php 
$this->load->view('site/layout/breadcrumb'); 
$ci =& get_instance();

$currentURL = current_url();
$params   = (!empty($_SERVER['QUERY_STRING'])) ? '?'.$_SERVER['QUERY_STRING'] : '';
$fullURL = $currentURL.$params;

$cart_type=($buy_now=='true') ? 'temp_cart' : 'main_cart';

add_css(array('assets/site_assets/css/checkout.css'));
add_footer_js(array('assets/site_assets/js/checkout.js'));

?>

<div class="checkout-area mt-20">
  <div class="container">
    <div class="row"> 
      <div class="col-md-8">
        <div class="checkout-form-area mb-25">
          <div class="panel-group" id="checkout-process" role="tablist"> 
            <div class="panel panel-default active">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a data-toggle="collapse" data-parent="#checkout-process" href="#shipping-details">
                    <span class="steps step-active">1</span> <?=$this->lang->line("shipping_section_lbl")?>
                  </a>
                  <span class="detail-label"></span>
                  <span class="edit-option"><i class="fa fa-pencil" aria-hidden="true"></i> <?=$this->lang->line("edit_btn")?></span>
                </h4>
              </div>
              <div id="shipping-details" class="panel-collapse collapse in">
                <div class="panel-body">
                  <div class="address_details_block">
                    <div class="address-list">
                      <?php 
                      $order_address_id=0;
                      $order_email='';
                      foreach ($addresses as $key => $value) {
                        if($value->is_default=='true'){
                          $order_address_id=$value->id;
                        }
                        ?>
                        <div class="address_details_item">
                          <label class="container">
                            <input type="radio" name="address_id" class="address_radio" value="<?=$value->id?>" <?php echo $value->is_default=='true' ? 'checked="checked"' : ''; ?>>
                            <span class="checkmark"></span>
                          </label>                
                          <div class="address_list">
                            <span><?=$value->name?> <?=$value->mobile_no?></span>
                            <div class="address_list_edit">
                              <a href="javascript:void(0)" class="btn_edit_address" data-stuff='<?php echo htmlentities(json_encode($value)); ?>'><?=$this->lang->line('edit_btn')?></a>
                            </div>
                            <p class="address-field">
                              <?=$value->building_name.', '.$value->road_area_colony.', '.$value->city.', '.$value->state.', '.$value->country.' - '.$value->pincode;?>
                            </p>
                            <?php 
                            if($value->is_default=='true'){
                              $order_email=$value->email;
                              echo '<button class="btn-continue form-button grow-btn mt-10" data-type="address">'.$this->lang->line("delivery_here_lbl").'</button>';
                            }
                            ?>
                          </div>

                        </div>
                      <?php } ?>
                    </div>
                    <div class="address_details_item add-new-address-itme">
                      <a href="" class="btn_new_address" style="font-size: 16px">
                        <div class="address_list" style="padding: 15px 5px">
                          <i class="fa fa-plus"></i> <?=$this->lang->line('add_new_address_lbl')?>
                        </div>
                      </a>
                    </div>

                    <div class="ceckout-form add_addresss_block" style="background: #f9f9f9;padding:25px 20px 20px 20px;margin-top: 20px;<?php if(empty($addresses)){ echo 'display: block';}else{  echo 'display: none;margin-top:15px;'; } ?>" >
                      <form action="<?php echo site_url('user/addAddress'); ?>" method="post" name="address_form" class="address_form">
                        <div class="billing-fields">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="wizard-form-field">
                                <div class="wizard-form-input has-float-label">
                                  <input type="text" name="billing_name" value="" required="" placeholder="<?=$this->lang->line('name_place_lbl')?>">
                                  <label><?=$this->lang->line('name_place_lbl')?></label>
                                </div>
                              </div>
                            </div>                    
                            <div class="col-md-6">
                              <div class="wizard-form-field">
                                <div class="wizard-form-input has-float-label">
                                  <input type="email" name="billing_email" value="" required="" placeholder="<?=$this->lang->line('email_place_lbl')?>">
                                  <label><?=$this->lang->line('email_place_lbl')?></label>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="wizard-form-field">
                                <div class="wizard-form-input has-float-label">
                                  <input type="text" name="billing_mobile_no" value="" required="" placeholder="<?=$this->lang->line('phone_no_place_lbl')?>" onkeypress="return isNumberKey(event)" maxlength="15">
                                  <label><?=$this->lang->line('phone_no_place_lbl')?></label>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="wizard-form-field">
                                <div class="wizard-form-input has-float-label">
                                  <input type="text" name="alter_mobile_no" value="" placeholder="<?=$this->lang->line('alt_phone_no_place_lbl')?>" onkeypress="return isNumberKey(event)" maxlength="15">
                                  <label><?=$this->lang->line('alt_phone_no_place_lbl')?></label>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="wizard-form-field">
                                <div class="wizard-form-input has-float-label">
                                  <textarea placeholder="<?=$this->lang->line('address_place_lbl')?>" name="building_name" style="background: #fff" required=""></textarea>
                                  <label><?=$this->lang->line('address_place_lbl')?></label>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="wizard-form-field">
                                <div class="wizard-form-input has-float-label">
                                  <input type="text" name="road_area_colony" value="" required="" placeholder="<?=$this->lang->line('road_area_colony_lbl')?>">
                                  <label><?=$this->lang->line('road_area_colony_lbl')?></label>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="wizard-form-field">
                                <div class="wizard-form-input has-float-label">
                                  <input type="text" name="landmark" value="" placeholder="<?=$this->lang->line('landmark_place_lbl')?>">
                                  <label><?=$this->lang->line('landmark_place_lbl')?></label>
                                </div>
                              </div>
                            </div>                    
                            <div class="col-md-6">
                              <select name="country" id="country" style="background: rgba(255,255,255,1) url(assets/site_assets/img/arow.png) no-repeat scroll 97% center;border-radius: 4px;height: 50px;margin-bottom:20px" required="">
                                <option value="0"><?=$this->lang->line('country_place_lbl')?></option>
                                <?php 
                                $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
                                ?>
                                <?php 
                                foreach ($countries as $key => $value) {
                                  ?>
                                  <option value="<?=$value?>"><?=$value?></option>
                                  <?php
                                }
                                ?>
                              </select>
                            </div>
                            <div class="col-md-6">
                              <div class="wizard-form-field">
                                <div class="wizard-form-input has-float-label">
                                  <input type="text" name="state" value="" required="" placeholder="<?=$this->lang->line('state_place_lbl')?>">
                                  <label><?=$this->lang->line('state_place_lbl')?></label>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="wizard-form-field">
                                <div class="wizard-form-input has-float-label">
                                  <input type="text" name="district" value="" placeholder="<?=$this->lang->line('district_place_lbl')?>">
                                  <label><?=$this->lang->line('district_place_lbl')?></label>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="wizard-form-field">
                                <div class="wizard-form-input has-float-label">
                                  <input type="text" name="city" value="" required="" placeholder="<?=$this->lang->line('city_place_lbl')?>">
                                  <label><?=$this->lang->line('city_place_lbl')?></label>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="wizard-form-field">
                                <div class="wizard-form-input has-float-label">
                                  <input type="text" name="pincode" value="" required="" placeholder="<?=$this->lang->line('zipcode_place_lbl')?>" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" maxlength="7">
                                  <label><?=$this->lang->line('zipcode_place_lbl')?></label>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <p>
                                <label><?=$this->lang->line('address_type_lbl')?><span class="required">*</span></label>
                              </p>
                              <div class="clearfix"></div>
                              <label class="radio-inline">
                                <input type="radio" name="address_type" value="1" readonly="" style="width: 20px;height: 15px" checked><?=$this->lang->line('home_address_lbl')?>
                              </label>
                              <label class="radio-inline">
                                <input type="radio" name="address_type" readonly="" value="2" style="width: 20px;height: 15px"><?=$this->lang->line('office_address_lbl')?>
                              </label>
                            </div>
                          </div>
                          <br/>

                          <div class="form-fild">
                            <div class="add-to-link">
                              <button class="form-button grow-btn" type="button" name="submit" data-text="save"><?=$this->lang->line('save_btn')?></button>
                              <button class="form-button grow-btn close_form" type="button"><?=$this->lang->line('close_btn')?></button>
                            </div>
                          </div>
                        </div>               
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a class="collapsed" data-toggle="collapse" data-parent="#checkout-process" href="javascript:void(0)"><span class="steps">2</span> <?=$this->lang->line("ord_summary_lbl")?></a>
                  <span class="detail-label"></span>
                  <span class="edit-option"><i class="fa fa-pencil" aria-hidden="true"></i> <?=$this->lang->line("edit_btn")?></span>
                </h4>
              </div>
              <div id="order-summary" class="panel-collapse collapse">
                <div class="panel-body">
                  <input type="hidden" name="chkout_ref" value="<?=(isset($_GET['chkout_ref'])) ? $_GET['chkout_ref'] : ''?>">
                  <?php 
                  $total_cart_amt=$you_save=$delivery_charge=0;
                  $cart_ids='';
                  foreach ($my_cart as $key => $value) {
                    $cart_ids.=$value->id.',';
                    $is_avail=true;
                    if($value->status==0){
                      $is_avail=false;
                    }
                    $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value->featured_image);
                    $img_file=base_url($ci->_generate_thumbnail('assets/images/products/',$thumb_img_nm,$value->featured_image,80,80));
                    ?>
                    <div class="product-img img-full product-holder" style="margin-bottom: 1rem;border-bottom: 2px solid #eee;">
                      <input type="hidden" name="cart_ids[]" value="<?=$value->id?>">
                      <div class="col-md-3 col-sm-3 text-center"> 
                        <a href="<?=base_url('single-product'.$value->product_slug)?>" title="<?=$value->product_title?>" target="_blank"> <img class="first-img" src="<?=$img_file?>" alt="" style="height: 80px;width: 80px;border: 2px solid rgba(0, 0, 0, 0.07);border-radius: 6px;margin-bottom: 8px;object-fit: cover;"></a>
                        <div class="quantity" style="display: inline-block;margin: 5px 0px 10px;">
                          <div class="buttons_added">
                            <input type="button" data-product="<?=encrypt_url($value->product_id)?>" data-perform="minus" value="-" class="minus">
                            <input class="input-text product_qty" name="product_qty" value="<?=$value->product_qty?>" type="text" min="1" max="<?=$value->max_unit_buy?>" onkeypress="return isNumberKey(event)" readonly="">
                            <input type="button" data-product="<?=encrypt_url($value->product_id)?>" data-perform="plus" value="+" class="plus">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-9 col-sm-9 product-item-dtl">
                        <div class="product-content-2">
                          <h4>
                            <a href="<?=base_url('product/'.$value->product_slug)?>" title="<?=$value->product_title?>" target="_blank">
                              <?=$value->product_title?>
                            </a>
                          </h4>
                          <div class="product-price">
                            <?php 
                            if($value->you_save_amt!='0'){
                              echo '<span class="new-price">'.CURRENCY_CODE.' '.number_format(($value->selling_price * $value->product_qty), 2);
                              echo '</span>';
                              echo '<span class="old-price">';
                              echo CURRENCY_CODE.' '.number_format(($value->product_mrp * $value->product_qty), 2);
                              echo '</span>';
                            }
                            else{
                              echo '<span class="new-price">'.CURRENCY_CODE.' '.number_format(($value->selling_price * $value->product_qty), 2).'</span>';
                            }
                            ?>
                          </div>
                        </div>
                        <a href="javascript:void(0)" class="btn btn-default btn-xs btn-remove-cart checkout_remove_item" data-id="<?=encrypt_url($value->id)?>">Remove</a>
                      </div>
                    </div>
                    <?php
                    if($is_avail){
                      $total_cart_amt+=$value->selling_price*$value->product_qty;
                      $delivery_charge+=$value->delivery_charge;
                      $you_save+=$value->you_save_amt * $value->product_qty;
                    }
                  }
                  $cart_ids=rtrim($cart_ids,',');
                  $total_cart_amt+=$delivery_charge;
                  ?>
                  <div class="order-mail-instruction">
                    <h4 style="font-size: 1.5rem;padding: 10px 0px;display: inline-block;">All order related details will be sent to <span class="order-email" style="font-weight: 600;"><?=$order_email?></span>
                    </h4>
                    <button class="btn-continue form-button grow-btn pull-right" data-type="order-summary">Continue</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a class="collapsed" data-toggle="collapse" data-parent="#checkout-process" href="javascript:void(0)"><span class="steps">3</span> <?=$this->lang->line("payment_lbl")?></a>
                </h4>
              </div>
              <div id="payment" class="panel-collapse collapse">
                <div class="panel-body">
                  <div class="checkout-payment">
                    <input type="hidden" name="buy_now" value="<?=$buy_now?>">
                    <input type="hidden" name="coupon_id" value="<?=$coupon_id?>">
                    <input type="hidden" name="current_page" value="<?=$fullURL?>">
                    <ul>
                      <?php 
                      if($this->settings->cod_status!='false')
                      {
                        ?>
                        <li class="payment_method">
                          <input id="payment_method_cod" class="input-radio" name="payment_method" checked="checked" value="cod" type="radio">
                          <label for="payment_method_cod">
                            <img src="<?=base_url($this->img_dir.'payment-icon/cash-on-delivery.png')?>">
                            <?=$this->lang->line('cod_lbl')?>
                          </label>
                          <div class="pay-box payment_method_cod">
                            <div class="col-md-12">
                              <label class="cash-on-del-title"><span class="_lblnum1"><?=rand(0,10)?></span> + <span class="_lblnum2"><?=rand(5,10)?></span> = </label>
                              <input type="text" name="" class="form-control input_txt">
                              <button class="order-btn btn_place_order grow-btn" style="width: max-content;"><?=$this->lang->line('place_ord_btn')?></button>
                            </div>            
                          </div>
                        </li>
                        <?php
                      }

                      if($this->settings->paypal_status!='false' AND $this->settings->paypal_client_id!='' AND $this->settings->paypal_secret_key!='')
                      {
                        ?>
                        <li class="payment_method">
                          <input id="payment_method_paypal" class="input-radio" name="payment_method" value="paypal" type="radio">
                          <label for="payment_method_paypal">
                            <img src="<?=base_url($this->img_dir.'payment-icon/paypal.png')?>">
                            <?=$this->lang->line('paypal_lbl')?>
                          </label>
                          <div class="pay-box payment_method_paypal">
                            <div class="col-md-12" style="margin-top: 10px;">
                              <button class="order-btn btn_place_order grow-btn" style="width: max-content;"><?=$this->lang->line('place_ord_btn')?></button>
                            </div>            
                          </div>
                        </li>
                        <?php
                      }

                      if($this->settings->stripe_status!='false' AND $this->settings->stripe_key!='' AND $this->settings->stripe_secret!='')
                      {
                        ?>
                        <li class="payment_method">
                          <input id="payment_method_stripe" class="input-radio" name="payment_method" value="stripe" type="radio">
                          <label for="payment_method_stripe">
                            <img src="<?=base_url($this->img_dir.'payment-icon/stripe.png')?>">
                            <?=$this->lang->line('stripe_lbl')?>
                          </label>
                          <div class="pay-box payment_method_stripe">
                            <div class="col-md-12" style="margin-top: 10px;">

                              <input type="hidden" name="stripe_token_id" readonly="" value="">

                              <fieldset>
                                <div class="row">
                                  <div class="wizard-form-field">
                                    <div class="wizard-form-input has-float-label">
                                      <input type="text" name="card_name" value="" placeholder="<?=$this->lang->line('card_name_lbl')?>" required="" autocomplete="name">
                                      <label><?=$this->lang->line('card_name_lbl')?></label>
                                    </div>
                                  </div>
                                </div>
                              </fieldset>
                              <fieldset>
                                <div class="row enter_card_item">
                                  <div id="stripe-elements"></div>
                                </div>
                              </fieldset>
                              <fieldset>
                                <div class="row" style="margin-top: 2rem">
                                  <button class="order-btn btn_place_order grow-btn" style="width: max-content;"><?=$this->lang->line('place_ord_btn')?></button>
                                </div>
                              </fieldset>
                            </div>            
                          </div>
                        </li>
                        <?php
                      }

                      if($this->settings->razorpay_status!='false' AND $this->settings->razorpay_key!='' AND $this->settings->razorpay_secret!='' && ($this->settings->app_currency_code == 'INR' || $this->settings->app_currency_code == 'inr'))
                      {
                        ?>

                        <form action="<?=base_url('razorpay/pay')?>" method="post" id="razorpayForm" style="display: none;">
                        </form>

                        <li class="payment_method">
                          <input id="payment_method_razorpay" class="input-radio" name="payment_method" value="razorpay" type="radio">
                          <label for="payment_method_razorpay">
                            <img src="<?=base_url($this->img_dir.'payment-icon/razorpay.png')?>">
                            <?=$this->lang->line('razorpay_lbl')?>
                          </label>
                          <div class="pay-box payment_method_razorpay">
                            <div class="col-md-12" style="margin-top: 10px;">
                              <button class="order-btn btn_place_order grow-btn" style="width: max-content;"><?=$this->lang->line('place_ord_btn')?></button>
                            </div>            
                          </div>
                        </li>
                        <?php
                      }

                      if($this->settings->paystack_status!='false' AND $this->settings->paystack_pubic_key!='' AND $this->settings->paystack_secret_key!='')
                      {
                        ?>
                        <li class="payment_method">
                          <input id="payment_method_paystack" class="input-radio" name="payment_method" value="paystack" type="radio">
                          <label for="payment_method_paystack">
                            <img src="<?=base_url($this->img_dir.'payment-icon/paystack.png')?>">
                            <?=$this->lang->line('paystack_lbl')?>
                          </label>
                          <div class="pay-box payment_method_paystack">
                            <div class="col-md-12" style="margin-top: 10px;">
                              <button class="order-btn btn_place_order grow-btn" style="width: max-content;"><?=$this->lang->line('place_ord_btn')?></button>
                            </div>            
                          </div>
                        </li>
                        <?php
                      }
                      ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="your-order-fields">
          <div class="your-order-title">
            <h3><?=$this->lang->line('order_section_lbl')?></h3>
          </div>
          <div class="your-order-table table-responsive">
            <table>
              <tfoot>
                <tr class="cart-subtotal">
                  <th><?=$this->lang->line('sub_total_lbl')?></th>
                  <td nowrap=""><span class="amount"><?=CURRENCY_CODE.' '.number_format(($total_cart_amt-$delivery_charge), 2);?></span></td>
                </tr>
                <tr class="shipping">
                  <th><?=$this->lang->line('delivery_charge_lbl')?></th>
                  <td nowrap=""><span class="delivery_charge"><?=($delivery_charge!=0)?'+ '.CURRENCY_CODE.' '.number_format($delivery_charge, 2) : $this->lang->line('free_lbl');?></span></td>
                </tr>
                <tr class="order-total">
                  <th><?=$this->lang->line('total_lbl')?></th>
                  <td nowrap=""><strong><span class="total-amount"><?=CURRENCY_CODE.' '.number_format($total_cart_amt, 2);?></span></strong></td>
                </tr>
                <tr class="apply_msg">
                  <td colspan="2">
                    <h4 class="text-center msg_2" style="font-weight: 500;color: green;margin-bottom: 15px;">
                      <?=($you_save > 0) ? str_replace('###', CURRENCY_CODE.' '.number_format($you_save, 2), $this->lang->line('coupon_save_msg_lbl')) : ''?>
                    </h4>
                  </td>
                </tr>
                <tr class="apply_button">
                  <td colspan="2">
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#coupons_detail">
                      <img src="<?=base_url('assets/images/coupon-icon.png')?>" style="width: 30px;height: 30px">
                      <?=$this->lang->line('apply_coupan_lbl')?>
                    </a>
                  </td>
                </tr>
                <tr class="remove_coupon" style="display: none;">
                  <td colspan="2">
                    <a href="javascript:void(0)" data-coupon="<?=$coupon_id?>" data-cart_ids="<?=$cart_ids?>" style="color: red">
                      &times; <?=$this->lang->line('remove_coupan_lbl')?>
                    </a>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="edit_address" class="modal fade" role="dialog" style="z-index: 99999">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="modal-details">
          <div style="background: none;border:none;">
            <form action="<?=site_url('user/edit_address')?>" method="post" id="edit_address_form">
              <input type="hidden" name="address_id">
              <div class="billing-fields">
                <div class="row">
                  <div class="col-md-6">
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="billing_name" value="" required="" placeholder="<?=$this->lang->line('name_place_lbl')?>">
                        <label><?=$this->lang->line('name_place_lbl')?></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="email" name="billing_email" value="" required="" placeholder="<?=$this->lang->line('email_place_lbl')?>">
                        <label><?=$this->lang->line('email_place_lbl')?></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="billing_mobile_no" value="" required="" placeholder="<?=$this->lang->line('phone_no_place_lbl')?>" onkeypress="return isNumberKey(event)" maxlength="15">
                        <label><?=$this->lang->line('phone_no_place_lbl')?></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="alter_mobile_no" value="" placeholder="<?=$this->lang->line('alt_phone_no_place_lbl')?>" onkeypress="return isNumberKey(event)" maxlength="15">
                        <label><?=$this->lang->line('alt_phone_no_place_lbl')?></label>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <textarea placeholder="<?=$this->lang->line('address_place_lbl')?>" name="building_name" required=""></textarea>
                        <label><?=$this->lang->line('address_place_lbl')?></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="road_area_colony" value="" required="" placeholder="<?=$this->lang->line('road_area_colony_place_lbl')?>">
                        <label><?=$this->lang->line('road_area_colony_place_lbl')?></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="landmark" value="" placeholder="<?=$this->lang->line('landmark_place_lbl')?>">
                        <label><?=$this->lang->line('landmark_place_lbl')?></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <select name="country" id="country" data-placeholder="Choose country...." tabindex="-1" style="background: rgba(255,255,255,1) url(assets/site_assets/img/arow.png) no-repeat scroll 97% center;border-radius: 4px;height: 50px;margin-bottom:20px" required="">
                      <option value="0"><?=$this->lang->line('country_place_lbl')?></option>
                      <?php 
                      $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
                      ?>
                      <?php 
                      foreach ($countries as $key => $value) {
                        ?>
                        <option value="<?=$value?>"><?=$value?></option>
                        <?php
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="state" value="" required="" placeholder="<?=$this->lang->line('state_place_lbl')?>">
                        <label><?=$this->lang->line('state_place_lbl')?></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="district" value="" placeholder="<?=$this->lang->line('district_place_lbl')?>">
                        <label><?=$this->lang->line('district_place_lbl')?></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="city" value="" required="" placeholder="<?=$this->lang->line('city_place_lbl')?>">
                        <label><?=$this->lang->line('city_place_lbl')?></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="wizard-form-field">
                      <div class="wizard-form-input has-float-label">
                        <input type="text" name="pincode" value="" required="" placeholder="<?=$this->lang->line('zipcode_place_lbl')?>" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" maxlength="7">
                        <label><?=$this->lang->line('zipcode_place_lbl')?></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <p>
                      <label><?=$this->lang->line('address_type_lbl')?><span class="required">*</span></label>
                    </p>
                    <div class="clearfix"></div>
                    <label class="radio-inline">
                      <input type="radio" name="address_type" value="1" readonly="" style="width: 20px;height: 15px" checked><?=$this->lang->line('home_address_lbl')?>
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="address_type" readonly="" value="2" style="width: 20px;height: 15px"><?=$this->lang->line('office_address_lbl')?>
                    </label>
                  </div>
                </div>
                <br/>                  
                <div class="form-fild">
                  <div class="add-to-link">
                    <button class="form-button" type="submit" data-text="save"><?=$this->lang->line('save_btn')?></button>
                    <button class="form-button" type="button" data-dismiss="modal"><?=$this->lang->line('close_btn')?></button>
                  </div>
                </div>
              </div>               
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="coupons_detail" class="modal fade" role="dialog" style="z-index: 9999999;background: rgba(0,0,0,0.8);">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="modal-details">
          <div class="row"> 
            <div class="col-md-12 col-sm-12">
              <div class="product-info">
                <h3><?=$this->lang->line('avail_coupan_lbl')?></h3>
                <br/>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <td class="text-center"><b><?=$this->lang->line('avail_coupan_code_lbl')?></b></td>
                      <td class="text-center"><b><?=$this->lang->line('avail_coupan_max_lbl')?></b></td>
                      <td class="text-center"><?=$this->lang->line('avail_coupan_apply_lbl')?></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $total_amt=$total_cart_amt-$delivery_charge;
                    foreach ($coupon_list as $key => $value) {

                      if($value->cart_status=='true'){
                        if($value->coupon_cart_min > $total_amt){
                          continue;
                        }
                      }

                      if($value->coupon_per==0 && ($total_amt < $value->coupon_amt)){
                        continue;
                      }
                      ?>
                      <tr>
                        <td class="text-center">
                          <?=$value->coupon_code?>
                        </td>
                        <td class="text-center">
                          <?php 
                          if($value->coupon_per!=0 AND $value->coupon_max_amt!=0){
                            echo CURRENCY_CODE.' '.$value->coupon_max_amt;
                          }
                          else{
                            echo CURRENCY_CODE.' '.$value->coupon_amt;
                          }
                          ?>
                        </td>
                        <td class="text-center">
                          <a href="javascript:void(0)" data-coupon="<?=$value->id?>" data-type="<?=$cart_type?>" data-cart="<?=$cart_ids?>" class="btn btn-success btn-sm btn_apply_coupon" style="border-radius: 3px">Apply</a>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  <?php 
  if($coupon_id!=0){
    ?>
    var href = "<?=base_url('checkout/apply_coupon')?>";

    var coupon_id="<?=$coupon_id?>";
    var cart_ids="<?=$cart_ids?>";

    var cart_type="<?=$cart_type?>";

    $.ajax({
      url: href,
      type: 'post',
      data: {'coupon_id' : coupon_id, 'cart_ids' : cart_ids, 'cart_type' : cart_type},
      dataType: 'json',
      success: function(res) {

        if(res.status == '1') {
          $(".order-total").find("span").html("<?=CURRENCY_CODE?>" + ' ' + res.payable_amt);
          $(".msg_2").html(res.you_save_msg);
          $(".apply_msg").show();
          $("input[name='coupon_id']").val(res.coupon_id);
          $(".apply_button").hide();
          $(".remove_coupon").show();
        }
        else if(res.success=='-1'){
          myAlert(res.msg,'myalert-error');
        }
        else {
          window.location.href = "<?=base_url('login-register')?>";
        }
      },
      error: function(res) {
        myAlert(Settings.err_something_went_wrong,'myalert-error');
      }

    });
    <?php
  }
  ?>

  $(document).on("click", ".edit-option", function (e) {
    e.preventDefault();

    $(this).hide();

    $("input[name='payment_method']").filter("[value='cod']").prop("checked",true);
    $(".pay-box").hide();
    $(".payment_method_cod").show();

    var _current_panel=$(this).parents(".panel");

    var _collapse_link=$("#checkout-process").find("[data-toggle='collapse']").attr("href","javascript:void(0)");

    $("#checkout-process").find(".active").removeClass("active");
    $("#checkout-process").find(".in").removeClass("in");
    $("#checkout-process").find(".steps").removeClass("step-active");
    
    _current_panel.find(".panel-title a").attr("href","#"+_current_panel.find(".collapse").attr("id"));
    _current_panel.find(".steps").addClass("step-active");
    _current_panel.find(".collapse").addClass("in");

    var _next_panel=_current_panel.nextAll(".panel");

    _next_panel.find(".edit-option").hide();

    _current_panel.find(".detail-label").hide();
    _next_panel.find(".detail-label").hide();
    
  });

  $(document).on("click", ".btn-continue", function (e) {
    e.preventDefault();

    var _current_panel=$(this).parents(".panel");

    var _next_panel=_current_panel.next(".panel");

    _next_panel.find(".detail-label").hide();

    var _collapse_link=$("#checkout-process").find("[data-toggle='collapse']").attr("href","javascript:void(0)");

    $("#checkout-process").find(".active").removeClass("active");
    $("#checkout-process").find(".in").removeClass("in");
    _current_panel.find(".steps").removeClass("step-active");

    _next_panel.find(".panel-title a").attr("href","#"+_next_panel.find(".collapse").attr("id"));
    _next_panel.find(".steps").addClass("step-active");
    _next_panel.find(".collapse").addClass("in");

    var _prev_panel=_next_panel.prevAll(".panel");

    _prev_panel.find(".edit-option").show();

    var _type=$(this).data("type");

    if(_type=='address'){
     var _field=$(this).prev(".address-field").html();
   }
   else if(_type=='order-summary'){
    var _field=$('.product-holder').length+' product(s) you have to checkout.';
  }
  _current_panel.find(".detail-label").text(_field).show();
});
</script>