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
              <div class="add_btn_primary"> <a href="<?php echo site_url("admin/users/add");?>?redirect=<?=$redirectUrl?>"><?=$this->lang->line('add_new_lbl')?></a> </div>
            </div>
          </div>
        </div>
        <div class="col-md-12">
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
                    <li><a href="javascript:void(0)" class="actions" data-action="enable" data-table="tbl_users" data-column="status"><?=$this->lang->line('enable_lbl')?></a></li>
                    <li><a href="javascript:void(0)" class="actions" data-action="disable" data-table="tbl_users" data-column="status"><?=$this->lang->line('disable_lbl')?></a></li>
                    <li><a href="javascript:void(0)" class="actions" data-action="delete" data-table="tbl_users" data-column="status"><?=$this->lang->line('delete_lbl')?></a></li>
                  </ul>
                </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
        <?php 
          if(!empty($user_list)){ 
        ?>
        <div class="col-md-12 mrg-top">
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th style="width:40px"></th>
                <th><?=$this->lang->line('register_platform_lbl')?></th>
                <th><?=$this->lang->line('name_lbl')?></th>
      				  <th><?=$this->lang->line('email_lbl')?></th>
      				  <th nowrap=""><?=$this->lang->line('register_on_lbl')?></th>
      				  <th><?=$this->lang->line('status_lbl')?></th>	 
                <th class="cat_action_list"><?=$this->lang->line('action_lbl')?></th>
              </tr>
            </thead>
            <tbody>
            	<?php 
	              define('IMG_PATH', base_url().'assets/images/users/');
		            $i=0;
		            foreach ($user_list as $key => $row) 
		            {
                  $user_img='';

                  if($row->user_image!='' && file_exists('assets/images/users/'.$row->user_image)){
                    $user_img=IMG_PATH.$row->user_image;
                  }
                  else{
                    $user_img=base_url('assets/images/2.png');
                  }
		          ?>
              <tr class="item_holder">
                <td>  
                  <div>
                      <div class="checkbox">
                        <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $row->id; ?>" class="post_ids">
                        <label for="checkbox<?php echo $i;?>">
                        </label>
                      </div>
                  </div>
                </td>
                <td><?=ucfirst($row->register_platform)?></td>
                <td nowrap="">
                  <a href="<?php echo site_url("admin/users/profile/".$row->id);?>?redirect=<?=$redirectUrl?>">
                    <div class="row" style="vertical-align: middle;">
                      <div class="col-md-3 col-xs-12">
                        <?php 
                          if($row->user_type=='Google'){
                            echo '<img src="'.base_url('assets/img/google-logo.png').'" class="social_img" style="">';
                          }
                          else if($row->user_type=='Facebook'){
                            echo '<img src="'.base_url('assets/img/facebook-icon.png').'" class="social_img" style="">';
                          }
                        ?>
                        <img src="<?=$user_img?>" style="width: 40px;height: 40px;border-radius: 4px">
                      </div>
                      <div class="col-md-9 col-xs-12" style="padding: 8px 15px">
                        <?php echo $row->user_name;?>
                      </div>
                    </div>
                  </a>
                </td>
                <td><?php echo ($row->user_email) ? $row->user_email : '-'?></td>
	              <td><?php echo date('d-m-Y',$row->created_at);?></td>

	              <td>
	           	    <input type="checkbox" id="enable_disable_check_<?=$i?>" data-id="<?=$row->id?>" data-table="tbl_users" data-column="status" class="cbx hidden enable_disable" <?php if($row->status==1){ echo 'checked';} ?>>
                  <label for="enable_disable_check_<?=$i?>" class="lbl"></label>
            		</td>
                <td nowrap="">

                  <a href="<?php echo site_url("admin/users/profile/".$row->id);?>?redirect=<?=$redirectUrl?>" class="btn btn-success btn_cust" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('profile_lbl')?>"><i class="fa fa-eye"></i></a>

                 	<a href="<?php echo site_url("admin/users/edit/".$row->id);?>?redirect=<?=$redirectUrl?>" class="btn btn-primary btn_cust" data-toggle="tooltip" data-tooltip="<?=$this->lang->line('edit_lbl')?>"><i class="fa fa-edit"></i></a>

                  <a href="javascript:void(0)" class="btn btn-danger btn_delete" data-toggle="tooltip" data-id="<?=$row->id?>" data-tooltip="<?=$this->lang->line('delete_lbl')?>"><i class="fa fa-trash"></i></a>
                </td>
              </tr>
             <?php		
    					 $i++;
    			 	   }
    			   ?>
            </tbody>
          </table>
        <?php }else{ ?>
          <div class="col-lg-12 col-sm-12 col-xs-12" style="text-align: center;">
            <h4 class="text-muted" style="font-weight: 400">Sorry! no records found...</h4>
            <br/>
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
    
  $(document).on("click", ".btn_delete", function(e){
      e.preventDefault();
      var _id=$(this).data("id");

      e.preventDefault(); 
      var href = '<?=base_url()?>admin/users/delete/'+_id;
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