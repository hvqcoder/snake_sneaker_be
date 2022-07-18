<div id="invoice">
  <div class="invoice overflow-auto">
    <div style="min-width: 600px">
      <header>
        <div class="row">
          <div class="col-md-6 logo">
            <img src="<?=base_url('assets/images/'.$this->web_settings->web_logo_1)?>" data-holder-rendered="true" />
          </div>
          <div class="col-md-6 company-details">
            <h3 class="name"><?=$this->settings->app_name?></h3>
            <div class="date"><?=$this->settings->app_contact?></div>
          </div>
        </div>
      </header>
      <main>
        <div class="row contacts">
          <div class="col-md-6 invoice-to">
            <div class="text-gray-light"><?=$this->lang->line('billing_shipping_section_lbl')?>:</div>
            <h3 class="to"><?php echo $this->General_model->selectByidParam($order_details->user_id, 'tbl_users','user_name');?></h3>
            <div class="address">
              <?=$order_details->building_name.', '.$order_details->road_area_colony.',<br/>'.$order_details->city.', '.$order_details->state.', '.$order_details->country.' - '.$order_details->pincode;?>
            </div>
            <div class="email"><?php echo $order_details->email?></div>
          </div>
          <div class="col-md-6 invoice-details">
            <h3 class="invoice-id"><?=$this->lang->line('ord_id_lbl')?>: <?=$order_details->order_unique_id?></h3>
            <div class="date"><?=$this->lang->line('ord_on_lbl')?>: <?php echo date('M d, Y',$order_details->order_date);?></div>
            <h3 class="invoice-no"><?=$this->lang->line('invoice_no_lbl')?>: # <?=$invoice_no?></h3>
            <div class="invoice-date"><?=$this->lang->line('invoice_date_lbl')?>: <?=date('d-m-Y')?></div>
            <h4 class="payment" style="margin-top: 20px;"><?=$this->lang->line('payment_mode_lbl')?>: <span style="font-weight: bold;"><?=ucfirst($order_details->gateway)?></span></h4>
            <?php 
            if($order_details->payment_id > 0)
            {
              ?>
              <h4 class="payment"><?=$this->lang->line('payment_id_lbl')?>: <span style="font-weight: bold;"><?=$order_details->payment_id?></span></h4>
            <?php } ?>
          </div>
        </div>
        <div class="row">
          <table border="0" cellspacing="0" cellpadding="0">
            <thead>
              <tr>
                <th class="text-left"><?=$this->lang->line('product_lbl')?></th>
                <th align="center"><?=$this->lang->line('price_lbl')?></th>
                <th align="center"><?=$this->lang->line('saving_lbl')?></th>
                <th align="center"><?=$this->lang->line('qty_lbl')?></th>
                <th align="center"><?=$this->lang->line('total_price_lbl')?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $_total_price=$_total_price=$_total_qty=0;

              $max_key=max(array_keys($order_items));

              foreach ($order_items as $key => $val) {

                $_total_price+=$val->total_price;

                $apply_css='';

                if($max_key==$key){
                  $apply_css='style="border-bottom: 1px solid #ddd;"';
                }

                ?>
                <tr>
                  <td class="text-left" <?=$apply_css?>>
                    <?=wordwrap($val->product_title,35,"<br>\n")?>
                  </td>
                  <td class="qty" align="center" <?=$apply_css?>>
                    <?php echo CURRENCY_CODE.' '.$this->General_model->selectByidParam($val->product_id, 'tbl_product','product_mrp');?>
                  </td>
                  <td class="qty" align="center" <?=$apply_css?>>
                    <?=$this->General_model->selectByidParam($val->product_id, 'tbl_product','product_mrp')-$val->product_price?>
                  </td>
                  <td class="qty" align="center">
                    <?=$val->product_qty?>
                  </td>
                  <td class="total" align="center">
                    <?=CURRENCY_CODE.' '.$val->total_price?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2"></td>
                <td colspan="2" style="border-top: 1px solid #ddd;font-size: 14px;font-weight: bold;"><?=$this->lang->line('sub_total_lbl')?></td>
                <td align="center" style="border-top: 1px solid #ddd;font-size: 14px;font-weight: bold;"><?=CURRENCY_CODE.' '.$_total_price?></td>
              </tr>
              <tr>
                <td colspan="2"></td>
                <td colspan="2" style="border-top: 1px solid #ddd;font-size: 14px;font-weight: bold;"><?=$this->lang->line('delivery_charge_lbl')?></td>
                <td align="center" style="border-top: 1px solid #ddd;font-size: 14px;font-weight: bold;"><?=$order_details->delivery_charge ? '+ '.$order_details->delivery_charge : $this->lang->line('free_lbl')?></td>
              </tr>
              <tr>
                <td colspan="2"></td>
                <td colspan="2" style="border-top: 1px solid #ddd;font-size: 14px;font-weight: bold;"><?=$this->lang->line('discount_lbl')?></td>
                <td align="center" style="border-top: 1px solid #ddd;font-size: 14px;font-weight: bold;">- <?=CURRENCY_CODE.' '.($order_details->discount_amt)?></td>
              </tr>
              <tr>
                <td colspan="2"></td>
                <td colspan="2" style="border-top: 1px solid #ddd;font-size: 14px;font-weight: bold;"><?=$this->lang->line('payable_amt_lbl')?></td>
                <td align="center" style="border-top: 1px solid #ddd;font-size: 14px;font-weight: bold;"><?=CURRENCY_CODE.' '.$order_details->new_payable_amt?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </main>
    </div>
  </div>
</div>