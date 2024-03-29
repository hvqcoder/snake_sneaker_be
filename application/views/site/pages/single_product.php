<?php

if ($this->db->get_where('tbl_web_settings', array('id' => '1'))->row()->libraries_load_from == 'local') {
	add_css(array('assets/site_assets/css/nivo-slider.css', 'assets/site_assets/css/slick.min.css'));

	add_footer_js(array('assets/site_assets/js/jquery.nivo.slider.js', 'assets/site_assets/js/jquery.countdown.min.js', 'assets/site_assets/js/slick.min.js'));
} else if ($this->db->get_where('tbl_web_settings', array('id' => '1'))->row()->libraries_load_from == 'cdn') {
	add_cdn_css(array('https://cdnjs.cloudflare.com/ajax/libs/jquery-nivoslider/3.2/nivo-slider.min.css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css'));

	add_footer_cdn_js(array('https://cdnjs.cloudflare.com/ajax/libs/jquery-nivoslider/3.2/jquery.nivo.slider.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js'));
}

add_footer_js(array('assets/site_assets/js/nivo.slider.init.js', 'assets/site_assets/js/slick.init.js', 'assets/site_assets/js/product.js'));

$this->load->view('site/layout/breadcrumb');

$ci = &get_instance();

$single_pre_url = current_url();

$this->session->set_userdata(array('single_pre_url' => $single_pre_url));

$user_id = $this->session->userdata('user_id') ? $this->session->userdata('user_id') : '0';

$thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $product->featured_image);

$img_file = $ci->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $product->featured_image, 600, 600);

$img_file_sm = $ci->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $product->featured_image, 200, 200);

$full_img = '<div id="' . $product->product_slug . '" class="tab-pane fade in active">
<div> <a href="'.base_url($img_file).'" class="lightbox" title="'.$product->product_slug.'"> <img src="'.base_url($img_file).'" alt="' . $product->product_slug . '" title="'.$product->product_slug.'"> </a> </div>
</div>';

$thumb_img = '<a data-toggle="tab" href="#' . $product->product_slug . '" title="'.$product->product_slug.'"><img src="'.base_url($img_file_sm).'" alt="'.$product->product_slug.'" title="'.$product->product_slug.'"></a> ';

$where = array('parent_id' => $product->id, 'type' => 'product');

$row_img = $ci->General_model->selectByids($where, 'tbl_product_images');

foreach ($row_img as $value) {

	$thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value->image_file);

	$img_big = $ci->_generate_thumbnail('assets/images/products/gallery/', $thumb_img_nm, $value->image_file, 600, 600);

	$img_small = $ci->_generate_thumbnail('assets/images/products/gallery/', $thumb_img_nm, $value->image_file, 200, 200);

	$full_img .= '<div id="'. $value->id.'" class="tab-pane fade">
	<div> <a href="'.base_url($img_big).'" class="lightbox" title="'.$value->id.'"> <img src="' . base_url($img_big). '" alt="'.$value->id.'" title="'.$value->id.'"> </a> </div>
	</div>';

	$thumb_img .= '<a data-toggle="tab" href="#'.$value->id.'" title="'.$value->id.'"><img src="'.base_url($img_small).'" alt="'.$value->id.'" title="'.$value->id.'"></a> ';
}

$size = $selected_size = $size_view = '';
if ($product->product_size != '') {

	$i = 1;
	foreach (explode(',', $product->product_size) as $key => $value) {

		$class = 'radio_btn';

		if ($ci->check_cart($product->id, $this->session->userdata('user_id'))) {

			$cart_size = $ci->get_single_info(array('product_id' => $product->id, 'user_id' => $this->session->userdata('user_id')), 'product_size', 'tbl_cart');


			if ($cart_size == $value) {
				$class = 'radio_btn selected';
			} else {
				$class = 'radio_btn';
			}
		} else {
			if ($i == 1) {
				$class = 'radio_btn selected';
			} else {
				$class = 'radio_btn';
			}
		}

		if ($i == 1) {
			$selected_size = $value;
			$size .= '<div class="' . $class . '" data-value="' . $value . '">' . $value . '</div>';
			$i = 0;
		} else {
			$size .= '<div class="' . $class . '" data-value="' . $value . '">' . $value . '</div>';
		}
	}

	$size_chart = (file_exists('assets/images/products/' . $product->size_chart) and $product->size_chart != '') ? base_url('assets/images/products/' . $product->size_chart) : "";

	if ($size_chart != '') {
		$size_view .= '<p style="font-weight: 600;margin:5px 0px">' . $this->lang->line('size_lbl') . ': </p>
		<div class="radio-group" style="margin-bottom:10px">
		' . $size . '
		<input type="hidden" id="radio-value" name="product_size" value="' . $selected_size . '" />

		</div><a href="" class="size_chart" data-img="' . $size_chart . '"><img src="' . base_url('assets/images/size_chart.png') . '" style="width:20px;height:20px;margin-right:4px;"> ' . $this->lang->line('size_chart_lbl') . '</a><br/><br/>';
	} else {

		$size_view .= '
		<div class="clearfix"></div>
		<p style="font-weight: 600;margin:5px 0px">' . $this->lang->line('size_lbl') . '</p>
		<div class="radio-group">
		' . $size . '
		<input type="hidden" id="radio-value" name="product_size" value="' . $selected_size . '" />
		</div><br/>';
	}
}

$is_avail = true;

if ($product->status == 0) {
	$is_avail = false;
}

?>

<link rel="stylesheet" type="text/css" href="<?= base_url('assets/site_assets/js/baguettebox/baguetteBox.min.css') ?>">

<style type="text/css">
.morecontent span {
	display: none;
}

.morelink {
	display: block;
}

#baguetteBox-overlay {
	background: rgba(0, 0, 0, 0.9) !important;
}
</style>

<section class="single-product-area mt-20">
	<div class="container">
		<div class="row">
			<div class="single-product-info mb-50">
				<div class="col-md-5 col-sm-5 text-center tz-gallery">
					<?php
					if (!$is_avail) {
						?>
						<div class="unavailable_override">
							<p><?= $this->lang->line('unavailable_lbl') ?></p>
						</div>
					<?php } ?>
					<div class="single-product-tab-content tab-content" style="overflow: hidden;">
						<?= $full_img ?>
					</div>
					<div class="single-product-tab">
						<div class="single-product-tab-menu owl-carousel">
							<?= $thumb_img ?>
						</div>
					</div>
				</div>
				<div class="col-md-7 col-sm-7">
					<div class="single-product-content">
						<h1 class="product-title"><?= $product->product_title ?></h1>

						<div class="single-product-price" style="margin-bottom: 0px;">
							<?php
							if ($product->you_save_amt != '0') {
								?>
								<span class="new-price"><?= CURRENCY_CODE . ' ' . number_format($product->selling_price, 2) ?></span>
								<span class="old-price"><?= CURRENCY_CODE . ' ' . number_format($product->product_mrp, 2); ?></span>
								<?php
							} else {
								?>
								<span class="new-price"><?= CURRENCY_CODE . ' ' . number_format($product->product_mrp, 2); ?></span>
								<?php
							}
							?>
							<?php
							if ($product->offer_id != 0) {
								?>
								<br />
								<a href="javascript:void(0)" class="applied_offer_lbl" data-offer="<?= $product->offer_id ?>" style="font-weight: 500;color: green;font-size: 15px">
									<i class="fa fa-gift"></i> <?= $this->lang->line('applied_offer_lbl') ?>

									<div class="offer_details" style="display: none">
										<div class="row">
											<div class="col-md-12">
												<h4 style="text-align: left">
													<strong><?= $ci->get_single_info(array('id' => $product->offer_id), 'offer_title', 'tbl_offers') ?></strong>
												</h4>
												<p style="font-weight: normal !important;">
													<?= $ci->get_single_info(array('id' => $product->offer_id), 'offer_desc', 'tbl_offers') ?>
												</p>
											</div>
										</div>
										<hr style="margin: 10px auto" />
										<div class="row">
											<div class="col-md-4">
												<p style="font-weight: normal !important;">
													<strong>Discount: </strong><?= $ci->get_single_info(array('id' => $product->offer_id), 'offer_percentage', 'tbl_offers') ?>%
												</p>
											</div>

										</div>
									</div>

								</a>
							<?php } ?>
						</div>
						<div class="product-rating" style="margin-top:5px;margin-bottom: 10px">
							<?php
							for ($x = 0; $x < 5; $x++) {
								if ($x < $product->rate_avg) {
									?>
									<i class="fa fa-star"></i>
									<?php
								} else {
									?>
									<i class="fa fa-star" style="color: #7a7a7a"></i>
									<?php
								}
							}
							?>
							<?php
							if (count($product_rating) > 0) {
								echo '<a class="review-link" href="#review">(' . count($product_rating) . ' ' . $this->lang->line('cust_review_lbl') . ')</a> ';
							}
							?>
						</div>
						<div class="product-description">
							<span class="more">
								<?= $product->product_desc ?>
							</span>
						</div>
						<br />

						<?php
						if ($is_avail) {
							?>

							<div class="single-product-quantity">
								<form action="<?=base_url('user/add_to_cart') ?>" method="post" id="cart-form">
									<?php
									echo $size_view;
									?>
									<div class="quantity">
										<input type="hidden" name="max_unit_buy" value="<?=$product->max_unit_buy ? $product->max_unit_buy : '1' ?>" class="max_unit_buy">
										<input type="hidden" name="preview_url" value="<?= current_url() ?>">
										<input type="hidden" name="product_id" value="<?=encrypt_url($product->id)?>" />

										<div class="buttons_added">
											<input type="button" value="-" class="minus">
											<input class="input-text product_qty" name="product_qty" value="<?php if ($ci->check_cart($product->id, $this->session->userdata('user_id'))) {
												echo $ci->get_single_info(array('product_id' => $product->id, 'user_id' => $this->session->userdata('user_id')), 'product_qty', 'tbl_cart');
												} else {
													echo '1';
												} ?>" type="text" min="1" max="<?= $product->max_unit_buy ? $product->max_unit_buy : '1' ?>" onkeypress="return isNumberKey(event)" readonly="">
												<input type="button" value="+" class="plus">
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="col-md-3">
											<button class="quantity-button cart-action-btn" style="margin-bottom: 10px" type="submit"><?php if ($ci->check_cart($product->id, $this->session->userdata('user_id'))) {
												echo $this->lang->line('update_cart_btn');
											} else {
												echo $this->lang->line('add_cart_btn');
											} ?></button>
										</div>
									</form>
									<div class="row">
										<div class="col-md-3">
											<form action="<?= site_url('buy-now') ?>" id="buy_now_form" action="post">
												<input type="hidden" name="product" value="<?= $product->product_slug ?>">
												<input type="hidden" name="size" value="">
												<input type="hidden" name="qty" value="">
												<input type="hidden" name="chkout_ref" value="<?= uniqid('chkref_') ?>">
												<button class="quantity-button" type="submit" id="buy_now_btn"><?= $this->lang->line('buy_now_btn'); ?></button>
											</form>
										</div>
									</div>
								</div>

							<?php } else { ?>
								<div class="single-product-quantity">
									<p style="color: red;font-weight: 500"><?= $this->lang->line('unavailable_lbl') ?></p>
								</div>
							<?php } ?>

							<div class="single-product-quantity">
								<?php
								if ($product->color != '') {
									$color_arr = explode('/', $product->color);
									$color_name = $color_arr[0];
									$color_code = $color_arr[1];

									echo '<h4><strong>' . $this->lang->line('colour_lbl') . ':</strong> ' . $color_name . '</h4>';
								}

								if ($product->other_color_product != '') {

									$thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $product->featured_image);

									$img_color_sm = $ci->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $product->featured_image, 80, 80);

									?>
									<a href="<?php echo site_url('product/' . $product->product_slug); ?>" title="<?= $color_name ?>" style="float:left;margin-right:10px;">
										<div class="text-center" style="width: 50px;margin-top: 10px">
											<div class="container-fluid" style="border: 2px solid #ff5252;padding: 0px;border-radius:6px;">
												<img src="<?= base_url($img_color_sm)?>" title="<?=$product->product_slug?>" alt="<?=$product->product_slug?>" style="border-radius:4px;">
											</div>
										</div>
									</a>
									<?php

									$ids = explode(',', $product->other_color_product);

									foreach ($ids as $key => $value) {

										$product_slug = $ci->get_single_info(array('id' => $value), 'product_slug', 'tbl_product');

										$featured_image = $ci->get_single_info(array('id' => $value), 'featured_image', 'tbl_product');

										$thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $featured_image);

										$img_color_sm = $ci->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $featured_image, 80, 80);

										$color_arr = explode('/', $ci->get_single_info(array('id' => $value), 'color', 'tbl_product'));
										$color_name = $color_arr[0];

										?>
										<a href="<?php echo site_url('product/' . $product_slug); ?>" title="<?= $color_name ?>" style="float:left">
											<div class="text-center" style="width: 50px;margin-top: 10px">
												<div class="container-fluid" style="border: 2px solid #eee;padding: 0px;border-radius:6px;">
													<img src="<?= base_url($img_color_sm)?>" title="<?=$product->product_slug?>" alt="<?=$product->product_slug?>" style="border-radius:4px;">
												</div>
											</div>
										</a>
										<?php
									}
								}
								?>
								<div class="clearfix"></div>
							</div>

							<div class="single-product-sharing">
								<ul>
									<li>
										<h4><strong><?= $this->lang->line('share_lbl') ?>:</strong></h4>
									</li>
									<li>
										<a href="https://www.facebook.com/sharer/sharer.php?u=<?= current_url() ?>" target="_blank" title="facebook"><i class="fa fa-facebook"></i></a></li>
									<li>
										<a href="https://twitter.com/intent/tweet?text=<?= $page_title ?>&amp;url=<?= current_url() ?>" target="_blank" title="twitter"><i class="fa fa-twitter"></i></a>
									</li>
									<li>
										<a href="http://pinterest.com/pin/create/button/?url=<?= current_url() ?>&media=<?= base_url($img_file)?>&description=<?= $page_title ?>" target="_blank" title="pinterest"><i class="fa fa-pinterest"></i></a>
									</li>

									<li>
										<a href="whatsapp://send?text=<?= current_url() ?>" target="_blank" data-action="share/whatsapp/share" title="whatsapp"><i class="fa fa-whatsapp"></i></a>
									</li>
								</ul>

							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="discription-tab">
					<div class="col-md-12">
						<div class="discription-review-contnet mb-20">
							<div class="discription-tab-menu">
								<ul>
									<li class="active">
										<a data-toggle="tab" href="#description" title="description"><?= $this->lang->line('features_lbl') ?></a>
									</li>
									<li>
										<a data-toggle="tab" href="#review" title="review"><?= $this->lang->line('reviews_lbl') ?> (<?= $ci->number_format_short(count($product_rating)) ?>)</a>
									</li>
								</ul>
							</div>
							<div class="discription-tab-content tab-content">
								<div id="description" class="tab-pane fade in active">
									<div class="row">
										<div class="col-md-12">
											<div class="description-content">
												<?= $product->product_features ?>
											</div>
										</div>
									</div>
								</div>
								<div id="review" class="tab-pane fade">
									<div class="row">
										<div class="col-md-12">
											<div class="review-page-comment">
												<div class="review-comment">
													<?php
													if (!empty($product_rating)) {
														?>
														<?php
														if (count($product_rating) > 1) {
															?>
															<div class="category_view_all">
																<a href="<?= base_url('product-reviews/' . $product->product_slug) ?>"><?= $this->lang->line('view_all_lbl') ?> (<?= count($product_rating) ?>)</a>
															</div>
														<?php } ?>
														<div class="clearfix"></div>
														<br />
														<ul>
															<?php
															define('IMG_PATH', base_url('assets/images/users/'));

															$user_img = '';

															$i = 1;
															foreach ($product_rating as $key => $value) {

																if ($i > 3) {
																	break;
																}

																$i++;

																if ($this->General_model->selectByidParam($value->user_id, 'tbl_users', 'user_image') != '' && file_exists('assets/images/users/' . $this->General_model->selectByidParam($value->user_id, 'tbl_users', 'user_image'))) {

																	$user_img = IMG_PATH . $this->General_model->selectByidParam($value->user_id, 'tbl_users', 'user_image');
																} else {
																	$user_img = base_url('assets/images/2.png');
																}

																$row_review_img = $this->General_model->selectByids(array('parent_id' => $value->id, 'type' => 'review'), 'tbl_product_images');
																?>
																<li>
																	<div class="product-comment">
																		<img src="<?= $user_img ?>" alt="user-profile" title="user-profile" style="width: 60px;height: 60px;" />
																		<div class="product-comment-content">
																			<p>
																				<strong><?= ($value->user_id == $this->session->userdata('user_id')) ? 'You' : $value->user_name; ?></strong> - <span><?= date('M jS, Y', $value->created_at) ?></span>
																				<span class="pro-comments-rating">
																					<?php
																					for ($x = 0; $x < 5; $x++) {
																						if ($x < $value->rating) {
																							?>
																							<i class="fa fa-star" style="color: #F9BA48"></i>
																							<?php
																						} else {
																							?>
																							<i class="fa fa-star"></i>
																							<?php
																						}
																					}
																					?>
																				</span>
																			</p>
																			<div class="description">
																				<span class="more">
																					<p><?= nl2br(stripslashes($value->rating_desc)) ?></p>
																				</span>
																				<div class="tz-gallery">
																					<div class="row upload_img_part">
																						<?php
																						foreach ($row_review_img as $key => $review_img) {

																							?>
																							<div class="user_upload_preview">
																								<a class="lightbox" href="<?= base_url('assets/images/review_images/') . $review_img->image_file ?>">
																									<img src="<?= base_url('assets/images/review_images/') . $review_img->image_file ?>" alt="Review image">
																								</a>
																							</div>
																						<?php } ?>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</li>
															<?php } ?>

														</ul>
														<?php
													}

													if (check_user_login() == '1' && $is_purchased) {
														?>
														<div class="review-form-wrapper">
															<div class="review-form"> <span class="comment-reply-title" style="font-size: 18px;font-weight: 500">Add a Review </span>
																<form action="<?= base_url('user/product_review') ?>" id="review-form">
																	<p class="comment-notes"> <span id="email-notes"><?= $this->lang->line('email_note_lbl') ?></span> <?= $this->lang->line('required_field_mark_lbl') ?> <span class="required">*</span> </p>
																	<div class="comment-form-rating">
																		<label><?= $this->lang->line('your_rating_lbl') ?></label>
																		<div class="rating">
																			<div class='rating-stars'>
																				<ul id='stars'>
																					<li class='star selected' title='<?= $this->lang->line('rate_poor_lbl') ?>' data-value='1'>
																						<i class='fa fa-star fa-fw'></i>
																					</li>
																					<li class='star <?php if (!empty($my_review) && $my_review[0]->rating >= 2) {
																						echo 'selected';
																					} ?>' title='<?= $this->lang->line('rate_fair_lbl') ?>' data-value='2'>
																					<i class='fa fa-star fa-fw'></i>
																				</li>
																				<li class='star <?php if (!empty($my_review) && $my_review[0]->rating >= 3) {
																					echo 'selected';
																				} ?>' title='<?= $this->lang->line('rate_good_lbl') ?>' data-value='3'>
																				<i class='fa fa-star fa-fw'></i>
																			</li>
																			<li class='star <?php if (!empty($my_review) && $my_review[0]->rating >= 4) {
																				echo 'selected';
																			} ?>' title='<?= $this->lang->line('rate_excellent_lbl') ?>' data-value='4'>
																			<i class='fa fa-star fa-fw'></i>
																		</li>
																		<li class='star <?php if (!empty($my_review) && $my_review[0]->rating >= 5) {
																			echo 'selected';
																		} ?>' title='<?= $this->lang->line('rate_wow_lbl') ?>' data-value='5'>
																		<i class='fa fa-star fa-fw'></i>
																	</li>
																</ul>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="input-element">
															<input type="hidden" class="inp_rating" name="rating" value="<?php echo (!empty($my_review)) ? $my_review[0]->rating : '1'; ?>">
															<input type="hidden" name="product_id" value="<?=encrypt_url($product->id)?>">
															<div class="review-comment-form-author col-md-6">
																<label><?= $this->lang->line('name_lbl') ?> </label>
																<input placeholder="name" type="text" readonly="" value="<?= $this->session->userdata('user_name') ?>">
															</div>
															<div class="review-comment-form-email col-md-6">
																<label><?= $this->lang->line('email_lbl') ?> </label>
																<input placeholder="email" readonly="" type="text" value="<?= $this->session->userdata('user_email') ?>">
															</div>
															<div class="comment-form-comment col-md-12">
																<label><?= $this->lang->line('reviews_lbl') ?></label>
																<textarea placeholder="<?= $this->lang->line('reviews_place_lbl') ?>" name="message" cols="40" rows="8"><?php echo (!empty($my_review)) ? $my_review[0]->rating_desc : ''; ?></textarea>
															</div>
															<div class="comment-form-comment col-md-12 upload_img_part" style="margin-bottom: 10px">
																<label><?= $this->lang->line('product_review_img_lbl') ?></label>
																<input type="file" name="product_images[]" multiple="" style="outline: none !important;padding-left:0;">
																<?php
																if (!empty($my_review)) {
																	?>
																	<div class="row">
																		<?php
																		foreach ($my_review[1] as $key => $value) {
																			?>
																			<div class="text-center review_img_holder">
																				<img src="<?= base_url('assets/images/review_images/') . $value->image_file ?>" alt="<?=$value->image_file?>" title="$value->image_file">
																				<br />
																				<a href="javascript:void(0)" title="remove" class="btn_remove_img" data-id="<?= $value->id ?>" style="color: #F00">&times;</a>
																			</div>
																		<?php } ?>
																	</div>
																	<?php
																}
																?>

															</div>
															<div class="comment-submit col-md-12 text-left" style="text-align: left">
																<button type="submit" class="form-button"><?= $this->lang->line('submit_btn') ?></button>
															</div>
														</div>
													</div>
												</form>
											</div>
										</div>
									<?php } else if (check_user_login() == '1' && !$is_purchased) {
										?>
										<div class="review-form-wrapper">
											<button class="quantity-button" onclick="showReviewNotAllow()"><?= $this->lang->line('add_review_btn') ?></button>
										</div>
										<?php
									} else { ?>
										<div class="review-form-wrapper">
											<button class="quantity-button" onclick="location.href='<?php echo site_url('login-register'); ?>'"><?= $this->lang->line('add_review_btn') ?></button>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</section>

<!-- Related products -->
<section class="related-products-area mb-85">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="section-title1-border">
					<div class="section-title1">
						<h3><?= $this->lang->line('related_product_lbl') ?></h3>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="related-products owl-carousel">
				<?php

				foreach ($related_products as $key => $product_row) {

					$thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $product_row->featured_image);

					$img_file = $ci->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $product_row->featured_image, 180, 180);

					$img_file2 = $ci->_generate_thumbnail('assets/images/products/', $product_row->id, $product_row->featured_image2, 180, 180);

					$is_avail = true;

					if ($product_row->status == 0) {
						$is_avail = false;
					}

					?>
					<div class="col-md-12">
						<div class="single-product">
							<?php
							if (!$is_avail) {
								?>
								<div class="unavailable_override">
									<p><?= $this->lang->line('unavailable_lbl') ?></p>
								</div>
							<?php } ?>
							<div class="product-img">
								<a href="<?php echo site_url('product/' . $product_row->product_slug); ?>" title="<?= $product_row->product_title ?>" alt="<?= $product_row->product_title ?>"> 
									<img class="first-img" src="<?= base_url($img_file)?>" title="<?= $product_row->product_title ?>" alt="<?= $product_row->product_title ?>"> 
									<img class="hover-img" src="<?= base_url($img_file2)?>" title="<?= $product_row->product_title ?>" alt="<?= $product_row->product_title ?>"> 
								</a>
								<?php
								if ($product_row->you_save_per != '0') {
									echo '<span class="sicker">' . $product_row->you_save_per . $this->lang->line('per_off_lbl') . '</span>';
								}
								?>
								<ul class="product-action">
									<?php
									if (check_user_login() && $ci->is_favorite($this->session->userdata('user_id'), $product_row->id)) {
										?>
										<li><a href="javascript:void(0)" class="btn_wishlist" data-id="<?= $product_row->id ?>" data-toggle="tooltip" title="<?= $this->lang->line('remove_wishlist_lbl') ?>" style="background-color: #ff5252"><i class="ion-android-favorite-outline"></i></a></li>
										<?php
									} else if ($ci->check_cart($product_row->id, $this->session->userdata('user_id'))) {
										?>
										<li><a href="javascript:void(0)" data-toggle="tooltip" title="<?= $this->lang->line('already_cart_lbl') ?>"><i class="ion-android-favorite-outline"></i></a></li>
										<?php
									} else {
										?>
										<li><a href="javascript:void(0)" class="btn_wishlist" data-id="<?= $product_row->id ?>" data-toggle="tooltip" title="<?= $this->lang->line('add_wishlist_lbl') ?>"><i class="ion-android-favorite-outline"></i></a></li>
										<?php
									}
									?>

									<li><a href="javascript:void(0)" class="btn_quick_view" data-id="<?= $product_row->id ?>" title="<?= $this->lang->line('quick_view_lbl') ?>"><i class="ion-android-expand"></i></a></li>

								</ul>
							</div>
							<div class="product-content">
								<h2>
									<a href="<?php echo site_url('product/' . $product_row->product_slug); ?>">
										<?=$product_row->product_title;?>
									</a>
								</h2>

								<div class="product-price">
									<?php
									if ($product_row->you_save_amt != '0') {
										?>
										<span class="new-price"><?= CURRENCY_CODE . ' ' . number_format($product_row->selling_price, 2) ?></span>
										<span class="old-price"><?= CURRENCY_CODE . ' ' . number_format($product_row->product_mrp, 2); ?></span>
										<?php
									} else {
										?>
										<span class="new-price"><?= CURRENCY_CODE . ' ' . number_format($product_row->product_mrp, 2); ?></span>
										<?php
									}
									?>

									<div class="rating">
										<?php
										for ($x = 0; $x < 5; $x++) {
											if ($x < $product_row->rate_avg) {
												?>
												<i class="fa fa-star" style="color: #F9BA48"></i>
												<?php
											} else {
												?>
												<i class="fa fa-star"></i>
												<?php
											}
										}
										?>
									</div>

									<?php
									if (!$ci->check_cart($product_row->id, $user_id)) {
										?>
										<a href="javascript:void(0)" class="button add-btn btn_cart <?= (!$is_avail) ? 'disabled' : '' ?>" data-id="<?= $product_row->id ?>" data-maxunit="<?= $product_row->max_unit_buy ?>" data-toggle="tooltip" title="<?= $this->lang->line('add_cart_lbl') ?>"><?= $this->lang->line('add_cart_btn') ?></a>
										<?php
									} else {

										$cart_id = $ci->get_single_info(array('product_id' => $product_row->id, 'user_id' => $user_id), 'id', 'tbl_cart');

										?>
										<a href="<?php echo site_url('remove-to-cart/' . $cart_id); ?>" class="button add-btn btn_remove_cart" data-toggle="tooltip" title="<?= $this->lang->line('remove_cart_lbl') ?>"><?= $this->lang->line('remove_cart_btn') ?></a>
										<?php
									}
									?>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
<?php

// show recently viewed items
if (!empty($recent_viewed_products)) {
	?>
	<section class="bestseller-product mb-30">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="section-title1-border">
						<div class="section-title1">
							<h3><?= $this->lang->line('recent_view_lbl') ?></h3>
							<?php
							if (count($recent_viewed_products) > 5) {
								echo '<div class="category_view_all" style="right: 100px"><a href="' . base_url('/recently-viewed-products') . '" title="' . $this->lang->line('view_all_lbl') . '">' . $this->lang->line('view_all_lbl') . '</a></div>';
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="recently-products mb-30 owl-carousel">

					<?php

					foreach ($recent_viewed_products as $key => $product_row) {

						$thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $product_row->featured_image);

						$img_file = $ci->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $product_row->featured_image, 180, 180);

						$img_file2 = $ci->_generate_thumbnail('assets/images/products/', $product_row->product_id, $product_row->featured_image2, 180, 180);

						$is_avail = true;

						if ($product_row->status == 0) {
							$is_avail = false;
						}

						?>
						<div class="col-md-12 item-col">
							<div class="single-product3">
								<?php
								if (!$is_avail) {
									?>
									<div class="unavailable_override">
										<p><?= $this->lang->line('unavailable_lbl') ?></p>
									</div>
								<?php } ?>
								<div class="product-img">
									<a href="<?php echo site_url('product/' . $product_row->product_slug); ?>" title="<?=$product_row->product_title ?>" taget="_blank">
										<img class="first-img" src="<?=base_url($img_file)?>" alt="<?=$product_row->product_title ?>" title="<?=$product_row->product_title ?>">
										<img class="hover-img" src="<?= base_url($img_file2)?>" alt="<?=$product_row->product_title ?>" title="<?=$product_row->product_title ?>">
									</a>
									<?php
									if ($product_row->you_save_per != '0') {
										echo '<span class="sicker">' . $product_row->you_save_per . $this->lang->line('per_off_lbl') . '</span>';
									}
									?>
									<ul class="product-action">

										<?php
										if (check_user_login() && $ci->is_favorite($this->session->userdata('user_id'), $product_row->product_id)) {
											?>
											<li><a href="javascript:void(0)" class="btn_wishlist" data-id="<?= $product_row->product_id ?>" data-toggle="tooltip" title="<?= $this->lang->line('remove_wishlist_lbl') ?>" style="background-color: #ff5252"><i class="ion-android-favorite-outline"></i></a></li>
											<?php
										} else if ($ci->check_cart($product_row->product_id, $this->session->userdata('user_id'))) {
											?>
											<li><a href="javascript:void(0)" data-toggle="tooltip" title="<?= $this->lang->line('already_cart_lbl') ?>"><i class="ion-android-favorite-outline"></i></a></li>
											<?php
										} else {
											?>
											<li><a href="javascript:void(0)" class="btn_wishlist" data-id="<?= $product_row->product_id ?>" data-toggle="tooltip" title="<?= $this->lang->line('add_wishlist_lbl') ?>"><i class="ion-android-favorite-outline"></i></a></li>
											<?php
										}
										?>

										<li><a href="javascript:void(0)" class="btn_quick_view" data-id="<?= $product_row->product_id ?>" title="<?= $this->lang->line('quick_view_lbl') ?>"><i class="ion-android-expand"></i></a></li>

									</ul>
								</div>
								<div class="product-content">
									<h2>
										<a href="<?php echo site_url('product/' . $product_row->product_slug); ?>" title="<?=$product_row->product_slug?>">
											<?=$product_row->product_title?>
										</a>
									</h2>
									<div class="product-price">
										<?php
										if ($product_row->you_save_amt != '0') {
											?>
											<span class="new-price"><?= CURRENCY_CODE . ' ' . number_format($product_row->selling_price, 2) ?></span>
											<span class="old-price"><?= CURRENCY_CODE . ' ' . number_format($product_row->product_mrp, 2); ?></span>
											<?php
										} else {
											?>
											<span class="new-price"><?= CURRENCY_CODE . ' ' . number_format($product_row->product_mrp, 2); ?></span>
											<?php

										}
										?>
										<div class="rating">
											<?php
											for ($x = 0; $x < 5; $x++) {
												if ($x < $product_row->rate_avg) {
													?>
													<i class="fa fa-star" style="color: #F9BA48"></i>
													<?php
												} else {
													?>
													<i class="fa fa-star"></i>
													<?php
												}
											}
											?>
										</div>
										<?php
										$user_id = $this->session->userdata('user_id') ? $this->session->userdata('user_id') : '0';
										if (!$ci->check_cart($product_row->product_id, $user_id)) {
											?>
											<a href="javascript:void(0)" class="button add-btn btn_cart <?= (!$is_avail) ? 'disabled' : '' ?>" data-id="<?= $product_row->product_id ?>" data-maxunit="<?= $product_row->max_unit_buy ?>" data-toggle="tooltip" title="<?= $this->lang->line('add_cart_lbl') ?>"><?= $this->lang->line('add_cart_lbl') ?></a>
											<?php
										} else {
											$cart_id = $ci->get_single_info(array('product_id' => $product_row->product_id, 'user_id' => $user_id), 'id', 'tbl_cart');
											?>
											<a href="<?php echo site_url('remove-to-cart/' . $cart_id); ?>" class="button add-btn btn_remove_cart" data-toggle="tooltip" title="<?= $this->lang->line('remove_cart_lbl') ?>"><?= $this->lang->line('remove_cart_lbl') ?></a>
											<?php
										}
										?>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>
<?php } ?>

<div id="offer_details" class="modal" style="z-index: 9999999;background: rgba(0,0,0,0.5);overflow-y: auto;">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" style="font-weight: 600"><?= $this->lang->line('offer_details_lbl') ?></h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body" style="padding:0px;padding-top:15px;">
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?= base_url('assets/site_assets/js/baguettebox/baguetteBox.min.js') ?>"></script>

<script type="text/javascript">
	baguetteBox.run('.tz-gallery');
</script>

<script type="text/javascript">

	var showChar = 200;
	var ellipsestext = "...";
	var moretext = '<?= $this->lang->line("show_more_lbl") ?> <i class="fa fa-chevron-right" style="font-size: 12px"></i>';
	var lesstext = '<?= $this->lang->line("show_less_lbl") ?> <i class="fa fa-chevron-left" style="font-size: 12px"></i>';


	$('.more').each(function() {
		var content = $.trim($(this).text());

		if (content.length > showChar) {

			var c = content.substr(0, showChar);
			var h = content.substr(showChar, content.length - showChar);

			var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span><a href="javascript:void(0)" title="morelink" class="morelink">' + moretext + '</a></span>';

			$(this).html(html);
		}

	});

	$(".morelink").on("click",function() {
		if ($(this).hasClass("less")) {
			$(this).removeClass("less");
			$(this).html(moretext);
		} else {
			$(this).addClass("less");
			$(this).html(lesstext);
		}
		$(this).parent().prev().toggle();
		$(this).prev().toggle();
		return false;
	});

	$('.radio-group .radio_btn').click(function() {

		$(this).parent().find('.radio_btn').removeClass('selected');
		$(this).addClass('selected');
		var val = $(this).attr('data-value');
		$(this).parent().find('input').val(val);

		var size = $("input[name='product_size']").val();

		$("#buy_now_form").find("input[name='size']").val(size);
	});

	function showReviewNotAllow() {
		myAlert("<?= $this->lang->line('review_not_allow_lbl') ?>",'myalert-danger');
	}

</script>