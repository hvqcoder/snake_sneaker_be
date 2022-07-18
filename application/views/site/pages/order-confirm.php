<?php 
    $this->load->view('site/layout/breadcrumb'); 
    $ci =& get_instance();
?>
<style type="text/css">
h1 {
    font-size: 28px;
    font-weight: 600;    
}
.align-items-center {
    -ms-flex-align: center !important;
    align-items: center !important;
}
.justify-content-center {
    -ms-flex-pack: center !important;
    justify-content: center !important;
}
.d-flex {
    display: -ms-flexbox !important;
    display: flex !important;
}
.confirm-item-block{
	background-image: url(<?=base_url($this->img_dir.'order-confirm-bg.png')?>);
	background-size: contain;
	background-repeat: no-repeat;
	background-position: center;
	box-shadow: 0px 1px 20px 1px rgba(0, 0, 0, 0.1);
	border-radius: 5px;
	padding:20px;
	margin:25px 0;
}	
</style>

<div class="py-5 d-flex justify-content-center align-items-center">
    <div class="container">	
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="confirm-item-block">
					<div class="text-center" style="margin-bottom: 2rem;">
						<img src="<?=base_url($this->img_dir.'order-confirm.gif')?>" style="width: 150px;margin-bottom:10px;">
						<h1><?=$this->lang->line('thank_you_ord_lbl')?></h1>
						<p style="font-size: 16px;line-height:26px;margin-top: 10px;"><?=$this->lang->line('ord_confirm_lbl')?></p>
					</div>
					<div class="text-center" style="margin-bottom: 2rem;">
						<button class="btn form-button grow-btn" onclick="location.href='<?=base_url('my-orders/'.$_GET['order'])?>'"><?=$this->lang->line('track_ord_btn')?></button>
						<button class="btn form-button grow-btn" onclick="location.href='<?=base_url('my-orders')?>'"><?=$this->lang->line('my_ord_btn')?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>