<?php
  $ci =& get_instance();
?>
<div class="row card_item_block" style="padding-left:30px;padding-right: 30px">
  <div class="col-xs-12">
    <div class="card mrg_bottom">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
        <div class="col-md-6 col-md-offset-1 col-xs-12">
          <div class="col-sm-12">
            <div class="search_list">
              <div class="search_block">
                <form method="get" action="">
                  <input class="form-control input-sm" placeholder="<?=$this->lang->line('search_lbl')?>" aria-controls="DataTables_Table_0" type="search" name="search_value" required value="<?php if(isset($_GET['search_value'])){ echo $_GET['search_value']; }?>">

                  <?php 
                    if(isset($_GET['category']))
                    {
                      echo '<input type="hidden" name="category" value="'.$_GET['category'].'">';
                    }
                    if(isset($_GET['offers']))
                    {
                      echo '<input type="hidden" name="offers" value="'.$_GET['offers'].'">';
                    }
                    if(isset($_GET['brands']))
                    {
                      echo '<input type="hidden" name="brands" value="'.$_GET['brands'].'">';
                    }
                  ?>

                  <button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
                </form>  
              </div>
              <div class="add_btn_primary"> <a href="<?php echo site_url("admin/products/add");?>?redirect=<?=$redirectUrl?>"><?=$this->lang->line('add_new_lbl')?></a> </div>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12">
          <form id="filterForm" accept="" method="GET">

            <?php 
              if(isset($_GET['search_value']))
              {
                echo '<input type="hidden" name="search_value" value="'.$_GET['search_value'].'">';
              }
            ?>
            <div class="col-md-3">
              <select class="form-control select2 filter" name="category">
                <option value="">---<?=$this->lang->line('all_cats_lbl')?>--</option>
                <?php 
                  foreach ($category_list as $key => $value) {
                    ?>
                    <option value="<?=$value->id?>" <?=(isset($_GET['category']) && $_GET['category']==$value->id) ? 'selected="selected"' : '' ?>><?=$value->category_name?></option>
                    <?php
                  }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-control select2 filter" name="brands">
                <option value="">---<?=$this->lang->line('all_brand_lbl')?>--</option>
                <?php 
                  foreach ($brands as $key => $value) {
                    ?>
                    <option value="<?=$value->id?>" <?=(isset($_GET['brands']) && $_GET['brands']==$value->id) ? 'selected="selected"' : '' ?>><?=$value->brand_name?></option>
                    <?php
                  }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-control select2 filter" name="offers">
                <option value="">---<?=$this->lang->line('all_offer_lbl')?>--</option>
                <?php 
                  foreach ($offer_list as $key => $value) {
                    ?>
                    <option value="<?=$value->id?>" <?=(isset($_GET['offers']) && $_GET['offers']==$value->id) ? 'selected="selected"' : '' ?>><?=$value->offer_title?></option>
                    <?php
                  }
                ?>
              </select>
            </div>
          </form>

          <div class="col-md-3 col-xs-12 text-right" style="float: right;">
            <form method="post" action="">
                <div class="checkbox" style="width: 100px;margin-top: 5px;margin-left: 10px;float: left;right: 110px;position: absolute;">
                  <input type="checkbox" id="checkall">
                  <label for="checkall">
                      <?=$this->lang->line('select_all_lbl')?>
                  </label>
                </div>
                <div class="dropdown" style="float:right">
                  <button class="btn btn-primary dropdown-toggle btn_cust" type="button" data-toggle="dropdown"><?=$this->lang->line('action_lbl')?>
                  <span class="caret"></span></button>
                  <ul class="dropdown-menu" style="right:0;left:auto;">
                    <li><a href="javascript:void(0)" class="actions" data-action="enable" data-table="tbl_product"><?=$this->lang->line('enable_lbl')?></a></li>
                    <li><a href="javascript:void(0)" class="actions" data-action="disable" data-table="tbl_product"><?=$this->lang->line('disable_lbl')?></a></li>
                    <li><a href="javascript:void(0)" class="actions" data-action="delete" data-table="tbl_product"><?=$this->lang->line('delete_lbl')?></a></li>
                    <li><a href="javascript:void(0)" class="actions" data-action="set_today_deal" data-table="tbl_product"><?=$this->lang->line('set_todays_deal_lbl')?></a></li>
                    <li><a href="" class="actions" data-action="remove_today_deal" data-table="tbl_product"><?=$this->lang->line('remove_todays_deal_lbl')?></a></li>
                  </ul>
                </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-12 mrg-top">
        <?php 
          if(!empty($products)){ 
        ?>
        <div class="row">
          <?php 
            $i=0;
            define('IMG_PATH', base_url().'assets/images/products/');

            $CI=&get_instance();
            
            foreach ($products as $key => $row) 
            {
          ?>
          <div class="col-lg-4 col-sm-6 col-xs-12 item_holder">
            <div class="block_wallpaper add_wall_category" style="box-shadow:0px 3px 8px rgba(0, 0, 0, 0.3)">  
              <div class="wall_category_block" style="background-color: <?php 
                    if($row->amount < 10 && $row->amount > 0 ){
                      echo "orange";  
                    }else if($row->amount == 0){
                      echo "red";
                    }
                  ?>">
                <h2 style="font-size: 20px">
                  <!-- <font color=<?php 
                    if($row->amount < 10 && $row->amount > 0 ){
                      echo "red";  
                    }else if($row->amount == 0){
                      echo "yellow";
                    } else{
                      echo "white";
                    }
                  ?>> -->
                  <?php 
                    if($row->amount < 10 && $row->amount > 0 ){
                      echo '! Sắp hết hàng';  
                    }else if($row->amount == 0){
                      echo '! Hết hàng';
                    } else{
                      echo 'Còn hàng';
                    }
                  ?>
                  <!-- </font> -->
                </h2>
                <?php 
                  $curr_date=date('d-m-Y');

                  if($row->today_deal_date!='' && $curr_date==date('d-m-Y',$row->today_deal_date)){
                    ?>
                    <a href="" data-id="<?php echo $row->id;?>" class="btn_today" data-action="deactive" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('todays_deal_lbl')?>" style="width: 30px;height: 30px;z-index: 1"><div style="color:green;"><i class="fa fa-check-circle"></i></div></a>
                    <?php
                  }
                  else{

                    $CI->deactive_today($row->id,true);

                    ?>
                    <a href="" data-id="<?php echo $row->id;?>" class="btn_today" data-id="active" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('todays_deal_lbl')?>" style="width: 30px;height: 30px;z-index: 1"><div><i class="fa fa-circle"></i></div></a>
                    <?php
                  }

                ?>

                <a href="<?php echo site_url("admin/products/duplicate-product/".$row->product_slug);?>?redirect=<?=$redirectUrl?>" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('duplicate_lbl')?>" style="width: 30px;height: 30px;z-index: 1;margin-right: 5px"><div><i class="fa fa-clone"></i></div></a>

                <?php if($this->db->get_where('tbl_verify', array('id' => '1'))->row()->android_envato_purchased_status==1)
                  {
                ?>
                <a href="javascript:void(0)" data-type="product" data-sub_id="0" data-title="<?php echo $row->product_title;?>" data-id="<?php echo $row->id;?>" data-image="<?=($row->featured_image!='') ? IMG_PATH.$row->featured_image : ''?>" class="btn_notification <?=($row->status) ? 'show_notification_icon' : 'hide_notification_icon'?>" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('send_notification_lbl')?>" style="width: 30px;height: 30px;z-index: 1;margin-right: 5px"><div><i class="fa fa-bell"></i></div></a>

                <?php } ?>

                <div class="checkbox" style="float: right;z-index: 1">
                  <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $row->id; ?>" class="post_ids">
                  <label for="checkbox<?php echo $i;?>">
                  </label>
                </div>
                 
              </div>        
              <div class="wall_image_title">
                <h2>
                  <?php 
                    if($row->you_save_per!=0){
                  ?>
                  <span class="label label-danger"><?=$row->you_save_per.$this->lang->line('per_off_lbl')?></span><br/><br/>
                  <?php } ?>

                  <a href="<?php echo site_url("admin/products/edit/".$row->id);?>?redirect=<?=$redirectUrl?>" style="text-shadow: 1px 1px 1px #000;font-size: 16px" title="<?=$row->product_title?>">
                  <?php 
                    if(strlen($row->product_title) > 30){
                      echo substr(stripslashes($row->product_title), 0, 30).'...';  
                    }else{
                      echo $row->product_title;
                    }
                  ?>
                  </a>
                </h2>
                
                <p style="margin-bottom: 0px;font-size: 14px">
                  <?php echo $CI->get_sub_category_info($row->sub_category_id, 'sub_category_name')?$CI->get_sub_category_info($row->sub_category_id, 'sub_category_name'):'-';?><?php echo $CI->get_brand_info($row->brand_id, 'brand_name') ? ' ('.$CI->get_brand_info($row->brand_id, 'brand_name').')' : '';?>
                </p>
                <p style="margin-bottom: 0px;font-size: 14px">Số lượng: 
                  <?php echo $row->amount?>
                </p>
                <ul>                
                  <li><a href="javascript:void(0)" data-toggle="tooltip" data-tooltip="<?=$ci->number_format_short($row->total_views)?> <?=$this->lang->line('view_lbl')?>"><i class="fa fa-eye"></i></a></li>

                  <li><a href="javascript:void(0)" data-toggle="tooltip" data-tooltip="<?=$row->rate_avg?> <?=$this->lang->line('rating_lbl')?>"><i class="fa fa-star"></i></a></li>

                  <li><a href="<?php echo site_url("admin/products/edit/".$row->id);?>?redirect=<?=$redirectUrl?>" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('edit_lbl')?>"><i class="fa fa-edit"></i></a></li>               
                  <li><a href="" data-toggle="tooltip" class="btn_delete_a" data-id="<?=$row->id?>" data-tooltip="<?=$this->lang->line('delete_lbl')?>"><i class="fa fa-trash"></i></a></li>
                  <li>
                    <div class="row toggle_btn">
                      <input type="checkbox" id="enable_disable_check_<?=$i?>" data-id="<?=$row->id?>" data-table="tbl_product" data-column="status" class="cbx hidden enable_disable" <?php if($row->status==1){ echo 'checked';} ?>>
                      <label for="enable_disable_check_<?=$i?>" class="lbl"></label>
                    </div>
                  </li>
                  
                </ul>
              </div>
              <span>
                <?php 
                  if(file_exists(IMG_PATH.$row->featured_image) || $row->featured_image==''){
                    ?>
                    <img src="https://via.placeholder.com/300x300?text=No image" style="height: 300px !important">
                    <?php
                  }else{
                    ?>
                    <img src="<?=IMG_PATH.$row->featured_image?>" style="height: 300px !important"/>
                    <?php
                  }
                ?>
              </span>
            </div>
          </div>  
        <?php 
            $i++;
          }
        ?>
        </div>
        <?php }else{ ?>
          <div class="col-lg-12 col-sm-12 col-xs-12" style="">
            <h3 class="text-muted" style="font-weight: 400"><?=$this->lang->line('no_data')?></h3>
          </div>
        <?php } ?>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-12 col-xs-12">
          <div class="pagination_item_block">
            <nav>
              <?php 
                  if(!empty($links)){
                    echo $links;  
                  } 
              ?>
            </nav>
          </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

  $(document).on("click", ".btn_delete_a", function(e){
      e.preventDefault();
      var _id=$(this).data("id");

      e.preventDefault(); 
      var href='<?=base_url()?>admin/product/delete/'+_id;
      var _currentElement = $(this);

      var confirmDlg = duDialog(null, "<?=$this->lang->line('are_you_sure_msg')?>", {
        init: true,
        dark: false, 
        buttons: duDialog.OK_CANCEL,
        okText: 'Proceed',
        callbacks: {
          okClick: function(e) {
            $(".dlg-actions").find("button").attr("disabled",true);
            $(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> '+Settings.please_wait);

            $.ajax({
              type: 'POST',
              url: href,
              dataType: 'json',
            })
            .done(function(res) {
              confirmDlg.hide();
              if (res.status == '1') {
                myAlert(res.msg,'myalert-success');
                _currentElement.closest('.item_holder').fadeOut("200");
              } else {
                myAlert(res.msg,'myalert-danger');
              }
            })
            .fail(function(response) {
              myAlert(Settings.err_something_went_wrong,'myalert-danger');
            });
          }
        }
      });
      confirmDlg.show();
  });

  $(".btn_today").on("click",function(e){
    e.preventDefault();
    var href;
    var btn = this;
    var _id=$(this).data("id");

    var _for=$(this).data("action");
    if(_for=='deactive'){
      href='<?=base_url()?>admin/product/deactive_today/'+_id;
    }else{
      href='<?=base_url()?>admin/product/active_today/'+_id;
    }

    $.ajax({
      type:'GET',
      url:href,
      success:function(res){
          location.reload();
        }
    });

  });

  $(".filter").on("change",function(e){

    var uri = window.location.toString();

    $("#filterForm *").filter(":input").each(function(){
      if ($(this).val() == '')
        $(this).prop("disabled", true);
    });
    $("#filterForm").submit();
  });

</script>