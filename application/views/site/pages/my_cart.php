<?php 

$this->load->view('site/layout/breadcrumb'); 

$ci =& get_instance();

$total_cart_amt=$delivery_charge=0;

?>
<div class="shopping-cart-area mt-20">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php 
        $is_items=false;
        if(!empty($my_cart))
        {
          $is_items=true;
          ?>
          <div class="wishlist-table table-responsive" style="margin-bottom:30px;">
            <table>
              <thead>
                <tr>
                  <th class="product-remove"></th>
                  <th class="product-cart-img"> <span class="nobr"> <?=$this->lang->line('item_img_lbl')?> </span> </th>
                  <th class="product-name"> <span class="nobr"> <?=$this->lang->line('item_title_lbl')?> </span> </th>
                  <th class="product-quantity" style="text-align: center;"> <span class="nobr"><?=$this->lang->line('item_qty_lbl')?> </span> </th>
                  <th class="product-price" nowrap=""> <span class="nobr"> <?=$this->lang->line('item_price_lbl')?> </span> </th>
                  <th class="product-total-price" nowrap=""> <span class="nobr"> <?=$this->lang->line('total_price_lbl')?> </span> </th>
                </tr>
              </thead>
              <tbody>
                <?php 

                foreach ($my_cart as $value) {

                  $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value->featured_image);

                  $img_file=$ci->_generate_thumbnail('assets/images/products/',$thumb_img_nm,$value->featured_image,50,50);

                  $is_avail=true;

                  if($value->status==0){
                    $is_avail=false;
                  }
                  ?>
                  <tr>
                    <td class="product-remove">
                      <a href="<?php echo site_url('remove-to-cart/'.$value->id); ?>" title="remove" class="btn_remove_cart grow-btn">×</a>
                    </td>

                    <td <?=(!$is_avail) ? 'style="opacity: 0.5;"' : ''?> class="product-cart-img">
                      <img src="<?=base_url($img_file)?>" alt="<?=$value->product_slug?>" title="<?=$value->product_slug?>" style="width: 50px;height: 50px">
                    </td>
                    <td class="product-name">
                      <a href="<?=(!$is_avail) ? 'javascript:void(0)' : site_url('product/'.$value->product_slug);?>" <?=(!$is_avail) ? 'style="opacity: 0.5;"' : ''?>>                      
                        <?php 
                        if(strlen($value->product_title) > 30){
                          echo substr(stripslashes($value->product_title), 0, 30).'...';  
                        }else{
                          echo $value->product_title;
                        }
                        ?>
                      </a>
                      <?php 
                      if(!$is_avail){
                        echo '<p style="color: red;background: #FFF;display: inline-block;box-shadow: 0px 5px 10px #ccc;padding: 5px 10px;line-height: initial">'.$this->lang->line('unavailable_lbl').'</p>';
                      }
                      ?>
                    </td>
                    <td <?=(!$is_avail) ? 'style="opacity: 0.5"' : ''?> class="product-quantity">
                      <select class="update_product_qty" data-cart="<?=encrypt_url($value->id)?>" <?=(!$is_avail) ? 'disabled="disabled"' : ''?>>
                        <?php 
                        for ($i=1; $i <= $value->max_unit_buy; $i++) { 
                          ?>
                          <option value="<?=$i?>" <?php if($i==$value->product_qty){ echo 'selected';} ?>><?=$i?></option>
                        <?php } ?>
                      </select>
                    </td>
                    <td <?=(!$is_avail) ? 'style="opacity: 0.5"' : ''?> class="product-price">
                      <span>

                        <?php 

                        $actual_price='';

                        if($value->you_save_amt!='0'){
                          ?>
                          <ins><?=CURRENCY_CODE.' '.amount_format($actual_price=($value->selling_price * $value->product_qty))?></ins>
                          &nbsp;
                          <del><?=CURRENCY_CODE.' '.amount_format(($value->product_mrp * $value->product_qty));?></del>
                          <?php
                        }
                        else{
                          ?>
                          <ins><?=CURRENCY_CODE.' '.amount_format($actual_price=($value->product_mrp * $value->product_qty));?></ins>
                          <?php
                          
                        }
                        ?>
                      </span>
                    </td>
                    <td <?=(!$is_avail) ? 'style="opacity: 0.5"' : ''?>><span class="product-total-price"><?=CURRENCY_CODE.' '.amount_format($actual_price)?></span></td>
                  </tr>
                  <?php
                  $total_cart_amt+=$actual_price;
                  $delivery_charge+=$value->delivery_charge;
                }
                $total_cart_amt+=$delivery_charge;
                ?>
              </tbody>
            </table>
          </div>
          <?php 
        }else{
          ?>
          <center style="margin-bottom: 50px;">
            <img src="<?=base_url('assets/img/empty_cart.png')?>" title="continue-shopping" alt="empty-cart" style="width: 200px;">
            <h2 style="font-size: 18px;font-weight: 500;color: #888;text-transform: capitalize;"><?=$this->lang->line('empty_cart_lbl')?></h2>
            <br/>
            <a href="<?=base_url('/')?>" title="continue-shopping">
              <img src="<?=base_url('assets/images/continue-shopping-button.png')?>" alt="continue-shopping" title="continue-shopping" style="width: 200px;height: 100%;">
            </a>
          </center>
          <?php
        } ?>
      </div>
    </div>
  </div>
  <?php 
  if($is_items)
  {
    ?>
    <div class="container">
      <div class="row">
        <div class="col-md-6 col-sm-6">
        </div>
        <div class="col-md-6 col-sm-6">
          <div class="shopping-cart-total">
            <h2><?=$this->lang->line('total_price_lbl')?></h2>
            <div class="shop-table table-responsive">
              <table>
                <tbody>
                  <tr class="cart-subtotal">
                    <td data-title="<?=$this->lang->line('sub_total_lbl')?>"><span class="sub-total"><?=CURRENCY_CODE.' '.amount_format(($total_cart_amt-$delivery_charge))?></span></td>
                  </tr>
                  <tr class="shipping">
                    <td data-title="<?=$this->lang->line('delivery_charge_lbl')?>"><span class="delivery-charge"><?=($delivery_charge!=0)?'+ '.CURRENCY_CODE.' '.amount_format($delivery_charge):$this->lang->line('free_lbl');?></span></td>
                  </tr>
                  <tr class="order-total">
                    <td data-title="<?=$this->lang->line('payable_amt_lbl')?>"><span class="total-amount" style="font-weight: 900;font-size: 18px;"><?=CURRENCY_CODE.' '.amount_format($total_cart_amt)?></span></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <h4 class="text-center msg_2" style="font-weight: 500;color: green;margin-bottom: 15px;"></h4>
            <div class="proceed-to-checkout">

              <?php
              echo form_open('checkout', ['id' => 'frmUsers','method' => 'GET']);
              ?>
              <button type="submit" class="checkout-button grow-btn"><?=$this->lang->line('proceed_checkout_btn')?></button>
              <?php
              echo form_close();
              ?>  
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>