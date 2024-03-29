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
                <form method="GET" action="">
                    <input class="form-control input-sm" placeholder="<?=$this->lang->line('search_lbl')?>" aria-controls="DataTables_Table_0" type="search" name="search_value" required value="<?php if(isset($_GET['search_value'])){ echo $_GET['search_value']; }?>">
                    <button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
                </form>  
              </div>
              <div class="add_btn_primary"> <a href="<?php echo site_url("admin/offers/add");?>?redirect=<?=$redirectUrl?>"><?=$this->lang->line('add_new_lbl')?></a> </div>
            </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-12 mrg-top">
        <?php 
          if(!empty($offers_list)){ 
        ?>
        <div class="row">
          <?php 
            define('IMG_PATH', base_url().'assets/images/offers/');
            $i=0;
            foreach ($offers_list as $key => $row) 
            {
          ?>
          <div class="col-lg-4 col-sm-6 col-xs-12 item_holder">
            <div class="block_wallpaper add_wall_category" style="box-shadow:0px 3px 8px rgba(0, 0, 0, 0.3)">           
              <div class="wall_image_title">
                <h2 style="margin-bottom: 5px;">
                  <a href="<?php echo site_url("admin/offers/edit/".$row->id);?>?redirect=<?=$redirectUrl?>" style="text-shadow: 1px 1px 1px #000;font-size: 16px" title="<?=$row->offer_title?>">
                    <?php 
                      if(strlen($row->offer_title) > 35){
                        echo substr(stripslashes($row->offer_title), 0, 35).'...';  
                      }else{
                        echo $row->offer_title;
                      }
                    ?>
                  </a>
                </h2>
                <p style="margin-bottom: 5px;"><?=$row->offer_percentage?>% OFF</p>
                <ul style="margin-top: 0px">

                    <?php if($this->db->get_where('tbl_verify', array('id' => '1'))->row()->android_envato_purchased_status==1)
                      {
                    ?>
                    <li class="<?=($row->status) ? 'show_notification_icon' : 'hide_notification_icon'?>">
                      <a href="javascript:void(0)" data-type="offer" data-sub_id="0" data-title="<?php echo $row->offer_title;?>" data-id="<?php echo $row->id;?>" data-image="<?=($row->offer_image!='') ? IMG_PATH.$row->offer_image : ''?>" class="btn_notification" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('send_notification_lbl')?>" style="width: 30px;height: 30px;z-index: 1;"><div><i class="fa fa-bell"></i></div></a>
                    </li>
                    <?php } ?>
                    
                    <li><a href="" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('view_lbl')?>" class="offer_detail"><i class="fa fa-eye"></i></a>
                      <div class="detailsHolder" style="display: none;">
                        <div class="modal-body">
                          <div class="row">
                            <div style="text-align: justify;">
                              <h4><?=$this->lang->line('offer_details_lbl')?></h4>
                              <hr/>
                              <?=$row->offer_desc?>
                            </div>
                          </div>
                          <hr/>
                          <div class="row">
                            <div class="col-md-4">
                              <p style="font-weight: normal !important;"><strong>Discount: </strong><?=$row->offer_percentage?>%</p>
                            </div>
                            
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-danger btn_delete" data-dismiss="modal">&times; Close</button>
                        </div>
                      </div>
                  </li>
                  <li><a href="<?php echo site_url("admin/offers/edit/".$row->id);?>?redirect=<?=$redirectUrl?>" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('edit_lbl')?>"><i class="fa fa-edit"></i></a></li>               
                  <li><a href="" data-toggle="tooltip" class="btn_delete_a" data-id="<?=$row->id?>" data-tooltip="<?=$this->lang->line('delete_lbl')?>"><i class="fa fa-trash"></i></a></li>
                  <li>
                    <div class="row toggle_btn">
                      <input type="checkbox" id="enable_disable_check_<?=$i?>" data-id="<?=$row->id?>" data-table="tbl_offers" data-column="status" class="cbx hidden enable_disable" <?php if($row->status==1){ echo 'checked';} ?>>
                      <label for="enable_disable_check_<?=$i?>" class="lbl"></label>
                    </div>
                  </li>
                </ul>
              </div>
              <span>
                <?php 
                  if(file_exists(IMG_PATH.$row->offer_image) || $row->offer_image==''){
                    ?>
                    <img src="https://via.placeholder.com/300x300?text=No image" style="height: 180px !important">
                    <?php
                  }else{
                    ?>
                    <img src="<?=IMG_PATH.$row->offer_image?>" style="height: 180px !important"/>
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

<div id="offer_detail" class="modal fade" role="dialog" style="">
  <div class="modal-dialog">
    <div class="modal-content">
      
    </div>

  </div>
</div>

<script type="text/javascript">

  $(".offer_detail").click(function(e){
    e.preventDefault();
    $("#offer_detail").modal("show");
    $("#offer_detail .modal-content").html($(this).next(".detailsHolder").html());
  });

  $(document).on("click", ".btn_delete_a", function(e){
      e.preventDefault();
      var _id=$(this).data("id");

      e.preventDefault(); 
      var href = '<?=base_url()?>admin/offers/delete/'+_id;
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
</script>