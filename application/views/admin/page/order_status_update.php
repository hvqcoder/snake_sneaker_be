<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/flat-admin.css')?>">

<style type="text/css">

.btn_edit, .btn_delete, .btn_cust{
  padding: 5px 10px !important;
}
.sweet-alert h2{
  font-size: 20px !important;
}
.sa-button-container .btn{
  font-size: 14px;
  padding: 8px 20px;
}

ul.timeline {
  list-style-type: none;
  position: relative;
}
ul.timeline:before {
  content: ' ';
  background: #d4d9df;
  display: inline-block;
  position: absolute;
  left: 29px;
  width: 2px;
  height: 100%;
  z-index: 400;
}
ul.timeline > li {
  margin: 20px 0;
  padding-left: 20px;
}
ul.timeline > li:before {
  content: ' ';
  background: white;
  display: inline-block;
  position: absolute;
  border-radius: 50%;
  border: 3px solid #22c0e8;
  left: 20px;
  width: 20px;
  height: 20px;
  z-index: 400;
}
ul.timeline p{
  font-size: 13px !important;
}

.checkbox label::before, .checkbox label::after, .radio label::before, .radio label::after{
  width: 23px;
  height: 23px;
}

.select2-container--open{
  z-index: 999999;
}

</style>
<h4><?=$this->lang->line('expected_delivery_date_lbl')?>: <input type="text" name="delivery_date" class="datepicker" value="<?=date('d-m-Y',$delivery_date)?>" name="" style="padding: 10px 10px;font-size: 16px;">&nbsp;&nbsp;<button class="btn btn-success btn_update_date" style="padding: 10px 10px;font-size: 12px;margin-bottom: 0px"><i class="fa fa-pencil"></i> <?=$this->lang->line('update_lbl')?></button></h4>



<hr/>
<ul class="timeline">
  <?php 
  $ci = &get_instance();

  $max_index=max(array_keys($order_data));

  foreach ($order_data as $key => $value) {
    ?>
    <li>
      <a href="javascript:void(0)" style="color: #000"><?=$ci->get_status_title($value->status_title);?></a>
      <a href="javascript:void(0)" style="float: right;color: #000"><?=date('d-m-Y',$value->created_at)?></a>
      <p>
        <?=$value->status_desc?>
      </p>
      <?php 
      if($max_index==$key && $max_index!=0){
        ?>
        <hr style="margin-top: 10px;margin-bottom: 10px" />
        <a href="javascript:void(0)" class="btn_status_delete" data-ord="<?=$value->order_id?>" data-status="<?=$value->status_title?>">
          <span class="label label-danger"><?=$this->lang->line('delete_lbl')?></span>
        </a>
      <?php } ?>
    </li>
  <?php } ?>
</ul>
<hr/>
<form action="<?php echo site_url('admin/order/update_product_status'); ?>" method="post" id="status_form" class="form form-horizontal" enctype="multipart/form-data">

  <input type="hidden" name="order_id" value="<?=$order_data[0]->order_id?>">
  <input type="hidden" name="user_id" value="<?=$order_data[0]->user_id?>">
  <input type="hidden" name="product_id" value="<?=$product_id?>">

  <div class="section">
    <div class="section-body">
      <div class="form-group">
        <label class="col-md-3 control-label"><?=$this->lang->line('status_lbl')?> :-</label>
        <div class="col-md-9">
          <select name="order_status" class="select2" required="" style="padding: 10px 5px">
            <option value="">---<?=$this->lang->line('select_status_lbl')?>---</option>
            <?php 
            foreach ($status_titles as $key => $value) {
              ?>
              <option value="<?=$value->id?>" <?php if($value->id <= $order_data[$max_index]->status_title){ echo 'disabled';} ?>><?=$value->title?></option>
                <?php    
              }
              ?>
            </select>

          </div>
        </div>
        <div class="form-group titleInput" style="display: none;">
          <label class="col-md-3 control-label">Status Title :-</label>
          <div class="col-md-9">
            <input type="text" placeholder="Enter Title" id="order_status_title" name="order_status_title" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-3 control-label"><?=$this->lang->line('sort_desc_lbl')?> :-</label>
          <div class="col-md-9">
            <textarea name="status_desc" id="status_desc" class="form-control" required=""></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-9 col-md-offset-3">
            <button type="submit" name="btn_submit" class="btn btn-primary btn_save"><?=$this->lang->line('save_btn')?></button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <script type="text/javascript">

    $(document).ready(function(e){
      $(".select2").select2();
      $(".datepicker").datepicker({ minDate: 0,dateFormat: 'dd-mm-yy' });
    });

    $("select[name='order_status']").on("change",function(e){
      if($(this).val()=='other_status'){
        $(".titleInput").show();
      }
      else{
        $(".titleInput").hide(); 
      }
    });

    $(document).on("click", ".btn_update_date", function(e) {
      e.preventDefault();

      var delivery_date=$("input[name='delivery_date']").val();
      var order_id=$("input[name='order_id']").val();

      var _url='<?=base_url()?>admin/order/update_delivery_date';

      $.post(_url, { 'delivery_date':delivery_date, 'order_id':order_id }, function(data){

        if(data.status==1){
          myAlert(data.msg,'myalert-success');
        }
        else{
          myAlert(data.msg,'myalert-danger');
        }
      }, "json");

    });


    $(document).on("submit", "#status_form", function (e) {
      e.preventDefault();

      $(".loader").show();

      var _btn=$(this).find(".btn_save");

      _btn.attr("disabled", true);

      var _href = $(this).attr("action");

      $(this).find("button[name='btn_submit']").text("<?=$this->lang->line('please_wait_lbl')?>");

      $.ajax({
        url:_href,
        type:'post',
        data: $(this).serialize(),
        dataType: 'json',
        success:function(res){

          $(".loader").hide();
          _btn.attr("disabled", false)

          if(res.status==1){
            location.reload();
          }
          else{
            myAlert(Settings.err_something_went_wrong,'myalert-danger');
          }
        },
        error : function(res) {
          myAlert(Settings.err_something_went_wrong,'myalert-danger');
        }

      });

    });

    $(document).on("click", ".btn_status_delete", function(e){
      e.preventDefault();

      var order_id=$(this).data("ord");
      var status_id=$(this).data("status");

      e.preventDefault(); 
      
      var href='<?=base_url()?>admin/order/delete_ord_status';

      var _currentElement = $(this).parent("li");

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
              data:{'order_id':order_id,'status_id':status_id},
              dataType: 'json',
            })
            .done(function(res) {
              confirmDlg.hide();
              if (res.status == '1') {
                location.reload();
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