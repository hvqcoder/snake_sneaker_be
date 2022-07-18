<?php 
$this->load->view('site/layout/breadcrumb'); 
?>
<section class="contact-form-area mt-20 mb-30">
	<div class="container">
		<div class="row"> 
			<?php 
			$i=0;
			$ci =& get_instance();
			foreach ($offers_list as $key => $row) 
			{
				$thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $row->offer_image);

				$img_offer=base_url($ci->_generate_thumbnail('assets/images/offers/',$thumb_img_nm,$row->offer_image,370,210));

				?>
				<div class="col-md-2 col-sm-2 mb-30">
					<div class="single-offer">
						<div class="offer-img img-full">
							<a href="<?=base_url('offers/'.$row->offer_slug)?>" title="<?=$row->offer_slug?>"> <img src="<?=$img_offer?>" title="<?=$row->offer_slug?>" alt="<?=$row->offer_slug?>" style="height: auto"> </a>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</section>