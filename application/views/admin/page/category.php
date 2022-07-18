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
                  <button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
                </form>  
              </div>
              <div class="add_btn_primary"> <a href="<?php echo site_url("admin/category/add");?>?redirect=<?=$redirectUrl?>"><?=$this->lang->line('add_new_lbl')?></a> </div>
            </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-12 mrg-top">
        <?php 
          if(!empty($category_list)){ 
        ?>
        <div class="row">
          <?php 
            define('IMG_PATH', base_url().'assets/images/category/');
            $i=0;
            foreach ($category_list as $key => $row) 
            {
          ?>
          <div class="col-lg-3 col-sm-6 col-xs-12 item_holder">
            <div class="block_wallpaper add_wall_category" style="box-shadow:0px 3px 8px rgba(0, 0, 0, 0.3)">   
              <div class="wall_category_block">

                <?php 
                  if($row->set_on_home)
                  {
                ?>
                  <a href="" data-id="<?php echo $row->id;?>" class="btn_status" data-status="0" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('remove_to_home_lbl')?>" style="width: 30px;height: 30px;z-index: 1"><div style="color:green;"><i class="fa fa-home"></i></div></a>
                <?php 
                  }
                  else{
                    ?>
                    <a href="javascript:void(0)" data-id="<?php echo $row->id;?>" class="btn_status" data-status="1" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('show_on_home_lbl')?>" style="width: 30px;height: 30px;z-index: 1"><div><i class="fa fa-home"></i></div></a>
                    <?php
                  }
                ?>

                <?php 
                  if($this->db->get_where('tbl_verify', array('id' => '1'))->row()->android_envato_purchased_status==1)
                  {
                ?>
                <a href="javascript:void(0)" data-type="category" data-sub_id="0" data-title="<?php echo $row->category_name;?>" data-id="<?php echo $row->id;?>" data-image="<?=($row->category_image!='') ? IMG_PATH.$row->category_image : ''?>" class="btn_notification <?=($row->status) ? 'show_notification_icon' : 'hide_notification_icon'?>" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('send_notification_lbl')?>" style="width: 30px;height: 30px;z-index: 1;margin-right: 5px"><div><i class="fa fa-bell"></i></div></a>

                <?php } ?>
                
              </div>
              <div class="wall_image_title">
                <h2><a href="<?php echo site_url("admin/category/edit/".$row->id);?>?redirect=<?=$redirectUrl?>" title="<?=$row->category_name?>" style="text-shadow: 1px 1px 1px #000;font-size: 16px">
                  <?php 
                    if(strlen($row->category_name) > 20){
                      echo substr(stripslashes($row->category_name), 0, 20).'...';  
                    }else{
                      echo $row->category_name;
                    }
                  ?>
                </a></h2>
                <ul>                
                  <li><a href="<?php echo site_url("admin/category/edit/".$row->id);?>?redirect=<?=$redirectUrl?>" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('edit_lbl')?>"><i class="fa fa-edit"></i></a></li>               
                  <li><a href="" data-toggle="tooltip" class="btn_delete_a" data-id="<?=$row->id?>" data-tooltip="<?=$this->lang->line('delete_lbl')?>"><i class="fa fa-trash"></i></a></li>
                  <li>
                    <div class="row toggle_btn">
                      <input type="checkbox" id="enable_disable_check_<?=$i?>" data-id="<?=$row->id?>" data-table="tbl_category" data-column="status" class="cbx hidden enable_disable" <?php if($row->status==1){ echo 'checked';} ?>>
                      <label for="enable_disable_check_<?=$i?>" class="lbl"></label>
                    </div>
                  </li>
                </ul>
              </div>
              <span>
                <?php 
                  if(file_exists(IMG_PATH.$row->category_image) || $row->category_image==''){
                    ?>
                    <img src="https://via.placeholder.com/247x150?text=No image" style="height: 150px !important">
                    <?php
                  }else{
                    ?>
                    <img src="<?=IMG_PATH.$row->category_image?>" style="height: 150px !important"/>
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
      var href = '<?=base_url()?>admin/category/delete/'+_id;
      var _currentElement = $(this);

      var confirmDlg = duDialog("<?=$this->lang->line('are_you_sure_msg')?>", "<?=$this->lang->line('remove_category_confirm_lbl')?>", {
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

  $(".btn_status").on("click",function(e){

    e.preventDefault();

    var href;
    var _id=$(this).data("id");
    var _status=$(this).data("status");

    href = 'category/category_on_home';

    $.ajax({
      type:'POST',
      url:href,
      data:{"id":_id,"status":_status},
      success:function(res){
          location.reload();
        }
    });

  });

</script>
