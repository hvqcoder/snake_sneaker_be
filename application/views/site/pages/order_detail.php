<?php 
$this->load->view('site/layout/breadcrumb'); 

$ci =& get_instance();

$is_order_claim=$ci->is_order_claim($order_details->id);

$dataClaimStuff=array('bank_err' => $this->lang->line('cancel_ord_bank_err'));
add_footer_js(array('assets/site_assets/js/order.js'));
?>

<style type="text/css">
.price_details_tbl td{
	padding: 3px 0px;
}
.price_details_tbl td:first-child { 
	font-weight: 500;
}

.payment-mode{
	font-weight: 600;
	font-size: 16px;
	background: rgba(231, 76, 60, 0.2);
	padding: 0px 10px;
	border-radius: 14px;
}

</style>

<div class="wishlist-table-area mt-20 mb-50">
	<div class="container">
		<div class="slingle_product_block row">
			<div class="col-md-4 col-sm-4 col-xs-12 slingle_item_address_part single_bdr_right bdr_top0">
				<div class="single_address_list">
					<span class="delivery_address_title" style="font-weight: 600;"><?=$this->lang->line('ord_details_section_lbl')?> :-</span>
					<div class="address_detail_product_item">
						<span><?=$this->lang->line('ord_id_lbl')?>: <?=$order_details->order_unique_id?> </span>
						<p><?=$this->lang->line('ord_on_lbl')?>: <?=date("M d, Y",$order_details->order_date)?> </p>
						<?php
						$_lbl_class='label-primary';
						$_lbl_title=$ci->get_status_title($order_details->order_status);

						switch ($order_details->order_status) {
							case '1':
							$_lbl_class='label-default';
							break;
							case '2':
							$_lbl_class='label-primary';
							break;
							case '3':
							$_lbl_class='label-warning';
							break;

							case '4':
							$_lbl_class='label-success';
							break;

							default:
							$_lbl_class='label-danger';
							break;
						}

						?>

						<p><?=$this->lang->line('ord_status_lbl')?>: <label class="label <?=$_lbl_class?>"><?=$_lbl_title?></label></p>
						<?php 
						echo $order_details->order_status==4 ? '<p>'.$this->lang->line('delivery_on_lbl').': '.date("M jS, y",$order_details->delivery_date).' </p><button type="button" class="form-button btn_download" data-id="'.$order_details->order_unique_id.'">'.$this->lang->line('download_invoice_btn').'</button>' : '<p>'.$this->lang->line('expected_delivery_lbl').': '.date("M jS, y",$order_details->delivery_date).' </p>';
						?>
						<?php
						if($is_order_claim){
							echo '<a href="javascript:void(0)" class="form-button cancle_order_btn btn_claim" style="margin-top:10px" data-order="'.$order_details->id.'" data-product="0">
							'.$this->lang->line('claim_refund_btn').'
							</a>';
						}
						if($order_details->order_status < 4){
							echo '<a href="javascript:void(0)" class="form-button cancle_order_btn product_cancel" style="margin-top:10px" data-order="'.$order_details->id.'" data-product="0" data-unique="'.$order_details->order_unique_id.'" data-gateway="'.$ci->get_single_info(array('order_id' => $order_details->id),'gateway','tbl_transaction').'">
							'.$this->lang->line('cancel_ord_btn').'
							</a>';
						}
						?>

					</div>
				</div>
			</div>
			<div class="col-md-5 col-sm-8 col-xs-12 slingle_item_address_part single_bdr_right bdr_top0">
				<div class="single_address_list" style="width: 100%;">
					<span class="delivery_address_title" style="font-weight: 600;"><?=$this->lang->line('ord_payment_section_lbl')?> :-</span>
					<div class="address_detail_product_item">
						<table class="price_details_tbl" cellpadding="50" cellspacing="20" width="100%">
							<tr>
								<td><?=$this->lang->line('total_amt_lbl')?>:</td>
								<td class="text-right">
									<?=CURRENCY_CODE.' '.number_format($order_details->total_amt, 2)?>
								</td>
							</tr>
							<?php 
							if(!empty($refund_data))
							{
								$cancel_ord_amt=array_sum(array_column($refund_data,'refund_pay_amt'));
								?>
								<tr>
									<td><?=$this->lang->line('cancel_ord_amt_lbl')?>:</td>
									<td class="text-right">- <?=CURRENCY_CODE.' '.number_format($cancel_ord_amt, 2)?></td>
								</tr>
								<?php
							}
							?>
							<tr>
								<td><?=$this->lang->line('discount_lbl')?>:</td>
								<td class="text-right">- <?=CURRENCY_CODE.' '.number_format($order_details->discount_amt, 2)?></td>
							</tr>
							<tr>
								<td><?=$this->lang->line('delivery_charge_lbl')?>:</td>
								<td class="text-right">+ <?=CURRENCY_CODE.' '.number_format($order_details->delivery_charge, 2)?></td>
							</tr>
							<tr>
								<td><?=$this->lang->line('payable_amt_lbl')?>:</td>
								<td class="text-right"><?=CURRENCY_CODE.' '.number_format($order_details->new_payable_amt, 2)?></td>
							</tr>
						</table>
						<p style="font-size: 16px;margin-top: 5px; font-weight: 600;"><?=$this->lang->line('payment_mode_lbl')?>:<br/><strong class="payment-mode"><?=strtoupper($order_details->gateway)?></strong>
							<span class="payment-mode" style="float: right;text-transform: inherit;"><?=$order_details->payment_id?></span>
						</p>
						
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-12 col-xs-12 slingle_item_address_part bdr_top0">
				<div class="single_address_list">
					<span class="delivery_address_title" style="font-weight: 600;"><?=$this->lang->line('ord_address_section_lbl')?> :-</span>
					<div class="address_detail_product_item">
						<span><?=$order_details->name?> </span>
						<p><?=$order_details->email?></p>
						<div class="product_address">
							<?=$order_details->building_name.', '.$order_details->road_area_colony.', '.$order_details->city.', '.$order_details->district.', '.$order_details->state.' - '.$order_details->pincode;?>
						</div>
						<span class="user_contact"><?=$this->lang->line('phone_no_lbl')?> : <?=$order_details->mobile_no?></span>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php

			foreach ($order_items as $key => $value) {

				$thumb_img = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value->featured_image);

				$img_file=$ci->_generate_thumbnail('assets/images/products/',$thumb_img,$value->featured_image,300,300);

				?>
				<div class="col-md-12 details_part_product_img slingle_item_address_part">
					<div class="row">
						<div class="col-md-1 col-sm-2 col-xs-4">
							<div class="product_img_part">
								<a href="<?php echo site_url('product/'.$value->product_slug); ?>" target="_blank" title="<?=$value->product_title?>">
									<img src="<?=base_url($img_file)?>" alt="<?=$value->product_title?>" title="<?=$value->product_title?>">
								</a>	
							</div>					
						</div>

						<div class="col-md-5 col-sm-5 col-xs-8" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">
							<a href="<?php echo site_url('product/'.$ci->get_single_info(array('id' => $value->product_id),'product_slug','tbl_product')); ?>" title="<?=$value->product_title?>" title="<?=$value->product_title?>" target="_blank">
								<?=$value->product_title;?>
							</a>				  
							<div><strong style="font-weight: 500;"><?=$this->lang->line('price_lbl')?>:</strong> <?=CURRENCY_CODE.' '.number_format($value->product_price, 2)?></div>
							<div><strong style="font-weight: 500;"><?=$this->lang->line('qty_lbl')?>:</strong> <?=$value->product_qty?></div>
							<?php 
							if($value->product_size!='' AND $value->product_size!='0')
							{
								echo '<div><strong style="font-weight: 500;">'.$this->lang->line('size_lbl').':</strong> '.$value->product_size.'</div>';
							}
							?>
						</div>

						<?php
						if($value->pro_order_status!='4' && $value->pro_order_status!='5'){
							?>
							<div class="col-md-5 col-sm-5 col-xs-12 col-md-offset-1 text-right">				 							
								<a href="javascript:void(0)" class="form-button pull-right btn-danger product_cancel" data-order="<?=$value->order_id?>" data-product="<?=$value->product_id?>" data-unique="<?=$order_details->order_unique_id?>" data-gateway="<?=$order_details->gateway?>"><?=$this->lang->line('cancel_btn')?></a>
							</div>
							<?php
						}
						else if($value->pro_order_status=='5'){
							$cancelled_on=$ci->get_single_info(array('order_id' => $value->order_id, 'product_id' => $value->product_id),'created_at','tbl_refund');
							?>
							<div class="col-md-6 col-sm-5 col-xs-12">
								<span style="color: red;"><?=$this->lang->line('product_cancelled_on_lbl')?> <?=date('d-m-Y h:i A',$cancelled_on)?></span>
								<br>
								<strong><?=$this->lang->line('reason_lbl')?>:</strong>
								<?php echo '<label style="">'.$ci->get_single_info(array('order_id' => $value->order_id, 'product_id' => $value->product_id),'refund_reason','tbl_refund').'</label>';?>
								<?php 
								if($ci->get_single_info(array('order_id' => $value->order_id, 'product_id' => $value->product_id),'gateway','tbl_refund')!='cod')
								{
									switch ($ci->get_single_info(array('order_id' => $value->order_id, 'product_id' => $value->product_id),'request_status','tbl_refund')) {
										case '0':
										$_lbl_title=$this->lang->line('refund_pending_lbl');
										$_lbl_class='label-warning';
										break;
										case '2':
										$_lbl_title=$this->lang->line('refund_process_lbl');
										$_lbl_class='label-primary';
										break;
										case '1':
										$_lbl_title=$this->lang->line('refund_complete_lbl');
										$_lbl_class='label-success';
										break;
										case '-1':
										$_lbl_title=$this->lang->line('refund_wait_lbl');
										$_lbl_class='btn-danger';
									}
									?>
									<br/>
									<?=$this->lang->line('refund_status_lbl')?>: <label class="label <?=$_lbl_class?>"><?=$_lbl_title?></label>
									<?php 
									if(!$is_order_claim)
									{
										if($ci->get_single_info(array('order_id' => $value->order_id, 'product_id' => $value->product_id),'request_status','tbl_refund')=='-1')
										{
											echo '<a href="javascript:void(0)" class="form-button pull-right btn-danger btn_claim" data-order="'.$value->order_id.'" data-product="'.$value->product_id.'">'.$this->lang->line('claim_refund_btn').'</a>';
										}
									}
								}
								?>
							</div>
							<?php
						}
						?>

					</div>
					<hr style="margin: 10px 0px">
					<div class="row">
						<div class="product_timeline_block">
							<section class="cd-horizontal-timeline">
								<?php 
								if($value->pro_order_status!='5')
								{
									?>
									<div class="timeline">
										<?php

										foreach ($status_titles as $value1) {
											if($value1->id=='5')
												break;
											?>
											<div class="dot <?php if($value1->id<=$value->pro_order_status){ echo 'active_dot';}else{ echo 'deactive_dot'; } ?>" id="<?=$value1->id?>" style="<?php if($value->pro_order_status < $value1->id){ echo 'pointer-events: none;cursor: default;';}?>">
												<span></span>
												<date style="width: max-content">
													<?=$value1->title?>
												</date>
											</div>
										<?php } ?>
										<?php 
										if($value->pro_order_status=='4'){
											?>
											<div class="inside" style="width: 100% !important"></div>
											<?php
										}
										else{
											?>
											<div class="inside" style="width: <?=(20*$value->pro_order_status+2)?>% !important"></div>
											<?php
										}
										?>

									</div>

									<?php 
									$display_first=true;
									foreach ($status_titles as $value1) {

										$where=array('order_id' => $order_details->id,'status_title' => $value1->id);

										$this->db->where($where);
										$row_status = $this->db->get('tbl_order_status')->row();

										if(empty($row_status))
											continue;

										if($row_status->status_desc!='')
										{
											?>
											<article class="modal <?=$value1->id?>" style="<?php if($value1->id==$value->pro_order_status){ echo 'display: block';}?>">
												<date><?=date("M jS, y",$row_status->created_at)?></date>
												<h2><?=$value1->title?></h2>
												<p><?=$row_status->status_desc?></p>
											</article>
											<?php
										}
										else{
											?>
											<article class="modal <?=$value1->id?>" style="<?php echo $display_first ? 'display: block' : '';  ?>">
												<h2><?=$this->lang->line('no_ord_status_lbl')?></h2>
											</article>
											<?php
										}
										$display_first=false; 
									}
								}
								else{
									?>
									<div class="timeline">
										<?php

										foreach ($status_titles as $key2 => $value2) {

											if($value2->id!='5' && $value2->id!='1')
												continue;
											?>
											<div class="dot <?php if($value2->id<=$value->pro_order_status){ echo 'active_dot';}else{ echo 'deactive_dot'; } ?>" id="<?=$value2->id?>">
												<span></span>
												<date style="width: max-content"><?=$value2->title?></date>
											</div>
										<?php } ?>
										<div class="inside" style="width: <?=(20*($value->pro_order_status-3))+2?>% !important"></div>
									</div>

									<?php 
									$display_first=true;
									foreach ($status_titles as $key2 => $value2) {

										$where=array('order_id' => $order_details->id,'status_title' => $value2->id);

										$this->db->where($where);
										$row_status = $this->db->get('tbl_order_status')->row();

										if(empty($row_status))
											continue;

										if($row_status->status_desc!='')
										{
											?>
											<article class="modal <?=$value2->id?>" style="<?php if($value2->id==$value->pro_order_status){ echo 'display: block';}?>">
												<date><?=date("M jS, y",$row_status->created_at)?></date>
												<h2><?=$value2->title?></h2>
												<p><?=$row_status->status_desc?></p>
											</article>
											<?php
										}
										else{
											?>
											<article class="modal <?=$value2->id?>" style="<?php echo $display_first ? 'display: block' : '';  ?>">
												<h2><?=$this->lang->line('no_ord_status_lbl')?></h2>
											</article>
											<?php
										}
										$display_first=false; 
									}
								}
								?>
							</section>
						</div>
					</div>
				</div>
			<?php } ?>		  
		</div>

	</div>

	<div id="orderCancel" class="modal">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<img src="<?= base_url('assets/images/shopping-cancel-512.png') ?>" style="width: 70px">
					<h4 class="modal-title cancelTitle"><?= $this->lang->line('product_cancel_confirm_lbl') ?></h4>
					<h5><?= $this->lang->line('ord_id_lbl') ?>: <span class="order_unique_id"></span></h5>
				</div>
				<div class="modal-body" style="padding:0px;padding-top:20px;">
					<form id="">
						<input type="hidden" name="order_id" value="">
						<input type="hidden" name="product_id" value="">
						<input type="hidden" name="gateway" value="">
						<div class="row">
							<div class="col-md-12">
								<div class="wizard-form-field">
									<div class="wizard-form-input has-float-label">
										<textarea class="form-control" name="reason" rows="4" placeholder="<?= $this->lang->line('reason_place_lbl') ?> *"></textarea>
										<label><?= $this->lang->line('reason_place_lbl') ?> *</label>
									</div>
								</div>
							</div>
							<div class="col-md-12 bank_details" style="display: none">
								<div class="address_details_block">
									<?php
									if(!empty($bank_details) > 0){
										foreach ($bank_details as $key => $row_bank) {
											?>
											<div class="address_details_item">
												<label class="container">
													<input type="radio" name="bank_acc_id" class="address_radio" value="<?= $row_bank->id ?>" <?php if ($row_bank->is_default == '1') {
														echo 'checked="checked"';
													} ?>>
													<span class="checkmark"></span>
												</label>

												<div class="address_list">
													<label class="badge badge-success" style="position: absolute;right: 0;font-weight: 500;"><?=ucfirst($row_bank->account_type)?></label>
													<span style="margin-bottom: 0px"><?=$row_bank->bank_name ?></span>
													<p style="margin-bottom: 0px"><?= $this->lang->line('bank_acc_no_lbl') ?>: <?=$row_bank->account_no ?></p>
													<p style="margin-bottom: 0px"><?=$row_bank->bank_holder_name ?></p>
												</div>
											</div>
											<?php
										}
									}
									else{
										echo '<div class="col-md-12 text-center no-content">
										<h3><i class="fa fa-info-circle"></i>'.$this->lang->line('no_saved_bank_lbl').'	
										</div><div class="clearfix"></div>';
									}
									?>
									<div class="address_details_item">
										<a href="javascript:void(0)" class="btn_new_account">
											<div class="address_list">
												<i class="fa fa-plus"></i> <?= $this->lang->line('add_new_bank_lbl') ?>
											</div>
										</a>
									</div>
								</div>
							</div>
						</div>
					</form>

					<form method="post" accept-charset="utf-8" action="<?php echo site_url('bank/add_new_bank'); ?>" class="bank_form" style="display: none;">
						<div class="row">
							<div class="col-md-12">
								<div class="wizard-form-field">
									<div class="wizard-form-input has-float-label">
										<input type="text" name="bank_name" value="" required="" placeholder="<?= $this->lang->line('bank_name_place_lbl') ?>">
										<label><?= $this->lang->line('bank_name_place_lbl') ?></label>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="wizard-form-field">
									<div class="wizard-form-input has-float-label">
										<input type="text" name="account_no" value="" required="" placeholder="<?= $this->lang->line('bank_acc_no_place_lbl') ?>" onkeypress="return isNumberKey(event)">
										<label><?= $this->lang->line('bank_acc_no_place_lbl') ?></label>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<select class="form-control" required="required" name="account_type">
										<option value="saving"><?= $this->lang->line('saving_type_lbl') ?></option>
										<option value="current"><?= $this->lang->line('current_type_lbl') ?></option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="wizard-form-field">
									<div class="wizard-form-input has-float-label">
										<input type="text" name="bank_ifsc" value="" required="" placeholder="<?= $this->lang->line('bank_ifsc_place_lbl') ?>">
										<label><?= $this->lang->line('bank_ifsc_place_lbl') ?></label>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="wizard-form-field">
									<div class="wizard-form-input has-float-label" style="margin-bottom: 0px">
										<input type="text" name="holder_name" value="" required="" placeholder="<?= $this->lang->line('holder_name_place_lbl') ?>">
										<label><?= $this->lang->line('holder_name_place_lbl') ?></label>
									</div>
									<p class="hint_lbl" style="margin-bottom: 20px">(<?= $this->lang->line('holder_name_note_lbl') ?>)</p>
								</div>
							</div>
							<div class="col-md-12">
								<div class="wizard-form-field">
									<div class="wizard-form-input has-float-label" style="margin-bottom: 0px">
										<input type="text" name="holder_mobile" value="" required="" placeholder="<?= $this->lang->line('holder_mobile_place_lbl') ?>" onkeypress="return isNumberKey(event)" maxlength="15">
										<label><?= $this->lang->line('holder_mobile_place_lbl') ?></label>
									</div>
									<p class="hint_lbl" style="margin-bottom: 20px">(<?= $this->lang->line('holder_mobile_note_lbl') ?>)</p>
								</div>
							</div>
							<div class="col-md-12">
								<div class="wizard-form-field">
									<div class="wizard-form-input has-float-label" style="margin-bottom: 0px">
										<input type="text" name="holder_email" value="" required="" placeholder="<?= $this->lang->line('holder_email_place_lbl') ?>">
										<label><?= $this->lang->line('holder_email_place_lbl') ?></label>
									</div>
									<p class="hint_lbl" style="margin-bottom: 20px">(<?= $this->lang->line('holder_email_note_lbl') ?>)</p>
								</div>
							</div>
							<div class="col-md-12">
								<label class="container_checkbox"><?= $this->lang->line('default_refund_acc_lbl') ?>
								<input type="checkbox" checked="checked" name="is_default">
								<span class="checkmark"></span>
							</label>
						</div>
						<div class="col-md-12">
							<br />
							<div class="form-group">
								<button type="submit" class="btn grow-btn form-button"><?= $this->lang->line('save_btn') ?></button>
								<button type="button" class="btn grow-btn form-button btn_cancel_form"><?= $this->lang->line('cancel_btn') ?></button>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger grow-btn" data-dismiss="modal" aria-label="Close"><?= $this->lang->line('close_btn') ?></button>
				<?php

				$dataStuff = array('cancel_ord_reason_err' => $this->lang->line('cancel_ord_reason_err'), 'cancel_ord_bank_err' => $this->lang->line('cancel_ord_bank_err'), 'cancel_ord_btn' => $this->lang->line('cancel_ord_btn'), 'please_wait_lbl' => $this->lang->line('please_wait_lbl'), 'cancelled_lbl' => $this->lang->line('cancelled_lbl'));

				?>
				<button class="btn btn-success grow-btn cancel_order" data-stuff="<?= htmlentities(json_encode($dataStuff)) ?>"><?= $this->lang->line('cancel_ord_btn') ?></button>
			</div>
		</div>
	</div>
</div>

<div id="claimRefund" class="modal">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line('ord_refund_account_lbl') ?></h4>
			</div>
			<div class="modal-body" style="padding: 10px 15px 20px 15px;">
				<form id="claim-form" action="<?=site_url('refund/claim_refund')?>">
					<input type="hidden" name="order_id" value="">
					<input type="hidden" name="product_id" value="">
					<div class="row">
						<div class="col-md-12 bank_details" style="display: none">
							<div class="address_details_block">
								<?php
								if(!empty($bank_details) > 0){
									foreach ($bank_details as $key => $row_bank) {
										?>
										<div class="address_details_item">
											<label class="container">
												<input type="radio" name="bank_acc_id" class="address_radio" value="<?= $row_bank->id ?>" <?php if ($row_bank->is_default == '1') {
													echo 'checked="checked"';
												} ?>>
												<span class="checkmark"></span>
											</label>

											<div class="address_list">
												<label class="badge badge-success" style="position: absolute;right: 0;font-weight: 500;"><?=ucfirst($row_bank->account_type)?></label>
												<span style="margin-bottom: 0px"><?=$row_bank->bank_name ?></span>
												<p style="margin-bottom: 0px"><?= $this->lang->line('bank_acc_no_lbl') ?>: <?=$row_bank->account_no ?></p>
												<p style="margin-bottom: 0px"><?=$row_bank->bank_holder_name ?></p>
											</div>
										</div>
										<?php
									}
								}
								else{
									echo '<div class="col-md-12 text-center no-content">
									<h3><i class="fa fa-info-circle"></i>'.$this->lang->line('no_saved_bank_lbl').'	
									</div><div class="clearfix"></div>';
								}
								?>
								<div class="address_details_item">
									<a href="" class="btn_new_account" style="font-size: 16px">
										<div class="address_list" style="padding: 15px 5px">
											<i class="fa fa-plus"></i> <?= $this->lang->line('add_new_bank_lbl') ?>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>
				</form>

				<form method="post" accept-charset="utf-8" action="<?php echo site_url('bank/add_new_bank'); ?>" class="bank_form" style="display: none">
					<div class="row">
						<div class="col-md-12">
							<div class="wizard-form-field">
								<div class="wizard-form-input has-float-label">
									<input type="text" name="bank_name" value="" required="" placeholder="<?= $this->lang->line('bank_name_place_lbl') ?>">
									<label><?= $this->lang->line('bank_name_place_lbl') ?></label>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="wizard-form-field">
								<div class="wizard-form-input has-float-label">
									<input type="text" name="account_no" value="" required="" placeholder="<?= $this->lang->line('bank_acc_no_place_lbl') ?>" onkeypress="return isNumberKey(event)">
									<label><?= $this->lang->line('bank_acc_no_place_lbl') ?></label>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<select class="form-control" required="required" name="account_type">
									<option value="saving"><?= $this->lang->line('saving_type_lbl') ?></option>
									<option value="current"><?= $this->lang->line('current_type_lbl') ?></option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="wizard-form-field">
								<div class="wizard-form-input has-float-label">
									<input type="text" name="bank_ifsc" value="" required="" placeholder="<?= $this->lang->line('bank_ifsc_place_lbl') ?>">
									<label><?= $this->lang->line('bank_ifsc_place_lbl') ?></label>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="wizard-form-field">
								<div class="wizard-form-input has-float-label" style="margin-bottom: 0px">
									<input type="text" name="holder_name" value="" required="" placeholder="<?= $this->lang->line('holder_name_place_lbl') ?>">
									<label><?= $this->lang->line('holder_name_place_lbl') ?></label>
								</div>
								<p class="hint_lbl" style="margin-bottom: 20px">(<?= $this->lang->line('holder_name_note_lbl') ?>)</p>
							</div>
						</div>
						<div class="col-md-12">
							<div class="wizard-form-field">
								<div class="wizard-form-input has-float-label" style="margin-bottom: 0px">
									<input type="text" name="holder_mobile" value="" required="" placeholder="<?= $this->lang->line('holder_mobile_place_lbl') ?>" onkeypress="return isNumberKey(event)" maxlength="15">
									<label><?= $this->lang->line('holder_mobile_place_lbl') ?></label>
								</div>
								<p class="hint_lbl" style="margin-bottom: 20px">(<?= $this->lang->line('holder_mobile_note_lbl') ?>)</p>
							</div>
						</div>
						<div class="col-md-12">
							<div class="wizard-form-field">
								<div class="wizard-form-input has-float-label" style="margin-bottom: 0px">
									<input type="text" name="holder_email" value="" required="" placeholder="<?= $this->lang->line('holder_email_place_lbl') ?>">
									<label><?= $this->lang->line('holder_email_place_lbl') ?></label>
								</div>
								<p class="hint_lbl" style="margin-bottom: 20px">(<?= $this->lang->line('holder_email_note_lbl') ?>)</p>
							</div>
						</div>
						<div class="col-md-12">
							<label class="container_checkbox">
								<?= $this->lang->line('default_refund_acc_lbl') ?>
								<input type="checkbox" checked="checked" name="is_default" />
								<span class="checkmark"></span>
							</label>
						</div>
						<div class="col-md-12">
							<br />
							<div class="form-group">
								<button type="submit" class="btn form-button"><?= $this->lang->line('save_btn') ?></button>
								<button type="button" class="btn form-button btn_cancel_form"><?= $this->lang->line('cancel_btn') ?></button>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal" aria-label="<?= $this->lang->line('close_btn') ?>"><?= $this->lang->line('close_btn') ?></button>
				<button class="btn btn-success claim_refund" data-stuff="<?= htmlentities(json_encode($dataClaimStuff)) ?>"><?= $this->lang->line('claim_refund_btn') ?></button>
			</div>
		</div>
	</div>
</div>
</div>

<script src="<?=base_url('assets/site_assets/js/timeline.js')?>"></script>