<?php
$this->load->view('site/layout/breadcrumb');
$ci = &get_instance();

$dataClaimStuff = array('bank_err' => $this->lang->line('cancel_ord_bank_err'));

add_footer_js(array('assets/site_assets/js/order.js'));

?>

<div class="wishlist-table-area mt-20 mb-50">
	<div class="container">
		<div class="row">

			<?php
			if (!empty($my_orders)) {
				?>
				<div class="col-md-12">
					<?php
					foreach ($my_orders as $key => $value) {

						$is_order_claim = $ci->is_order_claim($value->id);

						?>
						<div class="product_oreder_part">
							<div class="oreder_part_block">
								<div class="order_detail_track">
									<div class="row">
										<div class="col-md-4 col-sm-4 col-xs-12">
											<div class="order_track_btn">
												<a href="<?php echo site_url('my-orders/' . $value->order_unique_id); ?>" title="<?= $value->order_unique_id ?>" target="_blank">
													<div class="order_btn grow-btn" style="text-transform: none;"><?= $value->order_unique_id ?></div>
												</a>
											</div>
										</div>
										<div class="col-md-4 col-sm-4 col-xs-12">
											<?php
											$status_arr = $ci->order_status($value->id, $value->order_status)[0];

											if ($value->order_status != '4' && $value->order_status != '5') {
												?>
												<?= $this->lang->line('expected_delivery_lbl') ?> <?= date("M d", $value->delivery_date) ?>
												<br>
												<?php
											}
											?>
										</div>

										<div class="col-md-4 col-sm-4 col-xs-12 order_track_item">
											<?php
											if ($is_order_claim) {
												?>
												<div class="order_cancle_btn_item">
													<a href="javascript:void(0)">
														<div class="cancle_order_btn btn_claim grow-btn" data-order="<?= $value->id; ?>" data-product="0"><?= $this->lang->line('claim_refund_btn') ?></div>
													</a>
												</div>
											<?php } ?>
											<div class="order_cancle_btn_item">
												<a href="<?php echo site_url('my-orders/'. $value->order_unique_id); ?>" title="<?= $value->order_unique_id ?>" target="_blank">
													<div class="cancle_order_btn grow-btn" style="display: block;"><i class="fa fa-map-marker"></i> <?= $this->lang->line('track_btn') ?></div>
												</a>
											</div>
										</div>
									</div>
								</div>
								<div class="track_order_details_part">
									<?php
										$where = array('order_id' => $value->id);
										$row_items = $this->General_model->selectByids($where, 'tbl_order_items');
										foreach ($row_items as $key2 => $value2) {

											$featured_image=$ci->get_single_info(array('id' => $value2->product_id), 'featured_image', 'tbl_product');

											$thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $featured_image);

											$img_file = $ci->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $featured_image, 100, 100);
											?>
											<div class="col-md-12 details_part_product_img slingle_item_address_part">
												<div class="row">
													<div class="col-md-1 col-sm-2 col-xs-4">
														<div class="product_img_part">
															<a href="<?php echo site_url('product/' . $ci->get_single_info(array('id' => $value2->product_id), 'product_slug', 'tbl_product')); ?>" title="<?=$value2->product_title;?>" target="_blank">
																<img src="<?= base_url($img_file)?>" alt="<?=$value2->product_title;?>" title="<?=$value2->product_title;?>">
															</a>
														</div>
													</div>
													<div class="col-md-4 col-sm-6 col-xs-8" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">
														<a href="<?php echo site_url('product/' . $ci->get_single_info(array('id' => $value2->product_id), 'product_slug', 'tbl_product')); ?>" target="_blank" title="<?= $value2->product_title ?>">
															<?php echo $value2->product_title;?>
														</a>
														<div><?= $this->lang->line('price_lbl') ?>: <?= CURRENCY_CODE . ' ' . number_format($value2->product_price, 2) ?></div>
														<div><?= $this->lang->line('qty_lbl') ?>: <?= $value2->product_qty ?></div>
														<?php
														if ($value2->product_size != '' and $value2->product_size != '0') {
															echo '<div>' . $this->lang->line('size_lbl') . ': ' . $value2->product_size . '</div>';
														}
														?>
													</div>

													<?php
													if ($value2->pro_order_status != '4' && $value2->pro_order_status != '5') {
														?>
														<div class="col-md-2 col-sm-4 col-xs-12 col-md-offset-5 text-right">
															<a href="javascript:void(0)" class="form-button grow-btn pull-right btn-danger product_cancel pull-right" data-order="<?= $value2->order_id ?>" data-product="<?= $value2->product_id ?>" data-unique="<?= $value->order_unique_id ?>" data-gateway="<?= $ci->get_single_info(array('order_id' => $value2->order_id), 'gateway', 'tbl_transaction') ?>"><?= $this->lang->line('cancel_btn') ?></a>
														</div>
														<?php
													} else if ($value2->pro_order_status == '5') {

														$cancelled_on = $ci->get_single_info(array('order_id' => $value2->order_id, 'product_id' => $value2->product_id), 'created_at', 'tbl_refund');

														?>
														<div class="col-md-7 col-sm-4 col-xs-12">
															<span style="color: red;"><?= $this->lang->line('product_cancelled_on_lbl') ?> <?= date('d-m-Y h:i A', $cancelled_on) ?></span>
															<br>
															<strong><?= $this->lang->line('reason_lbl') ?>:</strong>
															<?php echo '<label style="">' . $ci->get_single_info(array('order_id' => $value2->order_id, 'product_id' => $value2->product_id), 'refund_reason', 'tbl_refund') . '</label>'; ?>
															<?php
															if ($ci->get_single_info(array('order_id' => $value2->order_id, 'product_id' => $value2->product_id), 'gateway', 'tbl_refund') != 'cod') {
																switch ($ci->get_single_info(array('order_id' => $value2->order_id, 'product_id' => $value2->product_id), 'request_status', 'tbl_refund')) {
																	case '0':
																	$_lbl_title = $this->lang->line('refund_pending_lbl');
																	$_lbl_class = 'label-warning';
																	break;
																	case '2':
																	$_lbl_title = $this->lang->line('refund_process_lbl');
																	$_lbl_class = 'label-primary';
																	break;
																	case '1':
																	$_lbl_title = $this->lang->line('refund_complete_lbl');
																	$_lbl_class = 'label-success';
																	break;
																	case '-1':
																	$_lbl_title = $this->lang->line('refund_wait_lbl');
																	$_lbl_class = 'btn-danger';
																}
																?>
																<br />
																<?= $this->lang->line('refund_status_lbl') ?>: <label class="label <?= $_lbl_class ?>"><?= $_lbl_title ?></label>
																<?php

																if (!$is_order_claim) {

																	if ($ci->get_single_info(array('order_id' => $value2->order_id, 'product_id' => $value2->product_id), 'request_status', 'tbl_refund') == '-1') {
																		echo '<a href="javascript:void(0)" class="form-button grow-btn pull-right btn-danger btn_claim" data-order="' . $value2->order_id . '" data-product="' . $value2->product_id . '">' . $this->lang->line('claim_refund_btn') . '</a>';
																	}
																}
															}
															?>
														</div>
														<?php
													}
													?>

												</div>
											</div>
											<?php
										}
									?>

									<div class="row product_img_part_bottom">
										<div class="col-md-6 col-sm-6 col-xs-12 product_item_date_item">

											<?php
											$_lbl_class='label-primary';
											$_lbl_title=$ci->get_status_title($value->order_status);

											switch ($value->order_status) {
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

											<p style="font-weight: 550;font-size: 15px;margin-bottom: 5px;"><?=$this->lang->line('ord_status_lbl')?>: <label class="label <?=$_lbl_class?>" style="font-weight: 500;"><?=$_lbl_title?></label></p>

											<span><?= $this->lang->line('ord_on_lbl') ?> </span> <?= date("D, M jS 'y", $value->order_date) ?>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12 price_item_right"><span><?= $this->lang->line('ord_total_lbl') ?> </span><span class="product_item_price_item"><?= CURRENCY_CODE . ' ' . number_format($value->payable_amt, 2) ?></span></div>
									</div>

								</div>
							</div>
						</div>
					<?php } ?>

					<?php 
					if(!empty($links)){
						?>
						<div class="pagination pb-10">
							<?php 
							echo $links;  
							?>
						</div>
					<?php } ?>
				</div>
				<?php
			}
			else{
				?>
				<center style="margin-bottom: 50px;">
					<img src="<?= base_url('assets/img/my_order.png') ?>" title="my-order" alt="my-order">
					<h2 style="font-size: 18px;font-weight: 500;color: #888;"><?= $this->lang->line('my_order_empty') ?></h2>
					<br />
					<a href="<?= base_url('/') ?>" title="continue-shopping">
						<img src="<?= base_url('assets/images/continue-shopping-button.png') ?>" alt="continue-shopping" title="continue-shopping">
					</a>
				</center>
				<?php
			}
			?>
		</div>
	</div>
</div>

<div id="orderCancel" class="modal">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header">
				<img src="<?= base_url('assets/images/shopping-cancel-512.png') ?>" alt="cancel" title="cancel" style="width: 70px">
				<h4 class="modal-title cancelTitle">
					<?= $this->lang->line('product_cancel_confirm_lbl') ?>
				</h4>
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