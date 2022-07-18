<?php 
$ci =& get_instance();
?>
<style type="text/css">
.dataTables_wrapper{
  overflow: auto !important;
  clear: both !important;
}
.btn-success{
  background-color: #398439 !important;
  border-color: #398439 !important;
}
.label-success-cust{
  background: green !important;
}
</style>
<div class="row card_item_block" style="padding-left:30px;padding-right: 30px">
  <div class="col-xs-12">
    <div class="card mrg_bottom">
      <div class="col-md-12 mrg-top">
        <div style="float: left;font-size:1.2em;padding-top: 20px;color:#666666;"><?=$page_title?></div>

        <div class="clearfix"></div>
        <div class="col-md-12" style="margin-top: 2em;padding: 0px">
          <hr/>
          <form id="filterForm" accept="" method="GET">
            <div class="col-md-3" style="padding: 0px">
              <label style="font-weight: 400;font-size: 1em;"><?=$this->lang->line('status_filter_lbl')?></label>
              <select class="form-control select2 filter" name="ord_status" style="width: 100%">
                <option value="">---All---</option>
                <?php 
                foreach ($status_titles as $key => $value) {
                  ?>
                  <option value="<?=$value->id?>" <?=(isset($_GET['ord_status']) && $_GET['ord_status']==$value->id) ? 'selected="selected"' : ''?>><?=$value->title?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-md-3">
              <label style="font-weight: 400;font-size: 1em;"><?=$this->lang->line('date_filter_lbl')?></label>
              <input type="text" name="date_filter" autocomplete="off" value="<?=(isset($_GET['date_filter'])) ? $_GET['date_filter'] : ''?>" placeholder="DD-MM-YYYY" class="form-control filter_datepicker">
            </div>
          </form>

          <div class="col-md-3 col-xs-12 text-right" style="float: right;margin-bottom:20px;padding-right:0">
            <div class="checkbox" style="width: 100px;margin-top: 5px;margin-left: 10px;float: left;right: 95px;position: absolute;">
              <input type="checkbox" id="checkall">
              <label for="checkall">
               <?=$this->lang->line('select_all_lbl')?>
             </label>
           </div>
           <div class="dropdown" style="float:right">
            <button class="btn btn-primary dropdown-toggle btn_cust" type="button" data-toggle="dropdown"><?=$this->lang->line('action_lbl')?>
            <span class="caret"></span></button>
            <ul class="dropdown-menu" style="right:0;left:auto;">
             <li><a href="javascript:void(0)" class="actions" data-table="tbl_order_details" data-action="delete"><?=$this->lang->line('delete_lbl')?></a></li>
           </ul>
         </div>
       </div>
     </div>

     <table class="datatable table table-striped table-bordered table-hover">
      <thead>
        <tr>
          <th></th>
          <th><?=$this->lang->line('ord_id_lbl')?></th>						 
          <th><?=$this->lang->line('user_nm_lbl')?></th>
          <th><?=$this->lang->line('user_phone_lbl')?></th>
          <th nowrap=""><?=$this->lang->line('ord_on_lbl')?></th>
          <th><?=$this->lang->line('status_lbl')?></th>	 
          <th class="cat_action_list"><?=$this->lang->line('action_lbl')?></th>
        </tr>
      </thead>
      <tbody>
       <?php 
       $i=1;

       foreach ($order_list as $key => $row) 
       {
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
          <td><a href="<?php echo site_url("admin/orders/".$row->order_unique_id);?>"><?php echo $row->order_unique_id;?></a></td>
          <td style="word-wrap: break-word;"><?php echo $row->name;?></td>
          <td><?php echo $row->mobile_no;?></td>
          <td><?php echo date('d-m-Y',$row->order_date).'<br/>'.date('h:i A',$row->order_date);?></td>
          <td>
            <?php 

            $_bnt_class='label-primary';
            $_btn_title=$ci->get_status_title($row->order_status);

            switch ($row->order_status) {
              case '1':
              $_bnt_class='label-default';
              break;
              case '2':
              $_bnt_class='label-warning';
              break;
              case '3':
              $_bnt_class='label-success';
              break;

              case '4':
              $_bnt_class='label-success-cust';
              break;

              default:
              $_bnt_class='label-danger';
              break;
            }

            ?>

            <span class="label <?=$_bnt_class?>"><?=$_btn_title?></span>

          </td>
          <td nowrap="">

            <a href="" class="btn btn-warning btn_edit btn_status" data-toggle="tooltip" data-id="<?=$row->id?>" data-tooltip="<?=$this->lang->line('ord_status_lbl')?>"><i class="fa fa-wrench"></i></a>

            <a class="btn btn-info btn_cust" <?=($row->order_status != 4) ? 'disabled="disabled"' : ''?> href="<?php echo site_url("admin/orders/print/".$row->order_unique_id);?>" target="_blank" data-tooltip="<?=$this->lang->line('print_lbl')?>"><i class="fa fa-print"></i></a>

            <a href="" class="btn btn-danger btn_delete" data-toggle="tooltip" data-id="<?=$row->id?>" data-tooltip="<?=$this->lang->line('delete_lbl')?>"><i class="fa fa-trash"></i></a>

          </td>
        </tr>
        <?php		
        $i++;
      }
      ?>
    </tbody>
  </table>
</div>
<div class="clearfix"></div>
</div>
</div>
</div>

<div id="orderStatus" class="modal fade" role="dialog" style="">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?=$this->lang->line('update_ord_status_lbl')?></h4>
      </div>
      <div class="modal-body" style="padding-top: 0px">

      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

  $(".btn_status").on("click",function(e){  

    e.preventDefault();

    $("#orderStatus").modal("show");

    var _id=$(this).data("id");

    var href='<?=base_url()?>admin/order/order_status_form/'+_id;

    $("#orderStatus .modal-body").load(href);

  });

  $(".filter").on("change",function(e){
    $("#filterForm *").filter(":input").each(function(){
      if ($(this).val() == '')
        $(this).prop("disabled", true);
    });
    $("#filterForm").submit();
  });

  $(document).on("click", ".btn_delete", function(e){
      e.preventDefault();
      var _id=$(this).data("id");

      e.preventDefault(); 
      var href='<?=base_url()?>admin/order/delete/'+_id;
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