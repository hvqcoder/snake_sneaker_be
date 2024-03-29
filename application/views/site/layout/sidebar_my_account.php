<div class="widget widget-shop-categories">
  <h3 class="widget-shop-title"><?=$this->lang->line('myaccount_lbl')?></h3>
  	<div class="widget-content">
        <ul class="product-categories my_profile_detail_widget">
        	<li>
                <i class="fa fa-angle-right"></i>
            	<a class="pjax rx-default <?php if(isset($current_page) && strcmp($current_page,$this->lang->line('my_profile_lbl'))==0){ echo 'active';} ?>" href="<?=base_url('/my-account')?>" title="<?=$this->lang->line('my_profile_lbl')?>"><?=$this->lang->line('my_profile_lbl')?></a>
            </li>
            <li>
                <i class="fa fa-angle-right"></i>
                <a class="pjax rx-default" href="<?=base_url('/my-orders')?>" title="<?=$this->lang->line('myorders_lbl')?>"><?=$this->lang->line('myorders_lbl')?></a>
            </li>
            <?php 
                if(strcmp($this->session->userdata('user_type'), 'Normal')==0)
                {
            ?>
            <li>
                <i class="fa fa-angle-right"></i>
                <a class="pjax rx-default <?php if(isset($current_page) && strcmp($current_page,$this->lang->line('change_password_lbl'))==0){ echo 'active';} ?>" href="<?=base_url('/change-password')?>" title="<?=$this->lang->line('change_password_lbl')?>"><?=$this->lang->line('change_password_lbl')?></a>
            </li>
            <?php } ?>
            <li>
                <i class="fa fa-angle-right"></i>
            	<a class="pjax rx-default <?php if(isset($current_page) && strcmp($current_page,$this->lang->line('addresses_lbl'))==0){ echo 'active';} ?>" href="<?=base_url('/my-addresses')?>" title="<?=$this->lang->line('addresses_lbl')?>"><?=$this->lang->line('addresses_lbl')?></a>
            </li>
            <li>
                <i class="fa fa-angle-right"></i>
            	<a class="pjax rx-default <?php if(isset($current_page) && strcmp($current_page,$this->lang->line('saved_bank_lbl'))==0){ echo 'active';} ?>" href="<?=base_url('/saved-bank-accounts')?>" title="<?=$this->lang->line('saved_bank_lbl')?>"><?=$this->lang->line('saved_bank_lbl')?></a>
            </li>
            <li>
                <i class="fa fa-angle-right"></i>
            	<a class="pjax rx-default <?php if(isset($current_page) && strcmp($current_page,$this->lang->line('myreviewrating_lbl'))==0){ echo 'active';} ?>" href="<?=base_url('/my-reviews')?>" title="<?=$this->lang->line('myreviewrating_lbl')?>"><?=$this->lang->line('myreviewrating_lbl')?></a>
            </li>

            <li>
                <i class="fa fa-angle-right"></i>
                <a class="rx-default btn_logout" href="<?= site_url('site/logout') ?>" title="<?=$this->lang->line('logout_lbl')?>"><?=$this->lang->line('logout_lbl')?></a>
            </li>
        </ul>
	</div>
</div>