    <footer class="app-footer" style="position:relative;">
      <div class="row">
        <div class="col-xs-12">
          <div class="footer-copyright"><?=$this->lang->line('footer_context_lbl')?></div>
        </div>
      </div>
    </footer>
  </div>

  <div id="content" data-myalert data-myalert-max="1"></div>

</div>


<script type="text/javascript" src="<?=base_url('assets/js/app.js')?>"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="<?=base_url('assets/vendor/myalert/js/myalert.min.js')?>"></script>

<?php

if($this->session->flashdata('response_msg')) {
  $message = $this->session->flashdata('response_msg');
  unset($_SESSION['response_msg']);
  ?>
  <script type="text/javascript">
    var _msg="<?=$message['message']?>";
    var _class='<?=($message['class']) ? $message['class'] : 'success'?>';

    if(_class=='error'){
      _class='danger';
    }
    _msg=_msg.replace(/(<([^>]+)>)/ig,"");
    myAlert(_msg,'myalert-'+_class);
  </script>
  <?php
}
?>

<script type="text/javascript">

  function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57)){
      return false;
    }
    return true;
  }

  $(".loader").show();
  $(document).ready(function(){
    $(".loader").fadeOut("slow");
    new_arrival_orders();
    setInterval(function(){
      new_arrival_orders();
    },60000);
  });

  function new_arrival_orders() {
    var _href='<?=base_url()?>admin/order/order_notify';

    $.ajax({
      type:'POST',
      url:_href,
      success:function(res){
        var obj = $.parseJSON(atob(res));
        $(".notify_count").text(obj.count);

        $("li.ordering_heading").nextAll("li").remove();

        if(obj.count==0){
          $(".ordering_heading").after('<li class="dropdown-empty">No New Ordered</li>');
          $(".dropdown-empty").after('<li class="dropdown-footer"><a href="<?=site_url('admin/orders')?>"><?=$this->lang->line('view_all_lbl')?> <i class="fa fa-angle-right" aria-hidden="true"></i></a></li>');
        }
        else{
          $(".ordering_heading").after(obj.content);
          $(".ordering_ul").append('<li class="dropdown-footer"><a href="<?=site_url('admin/orders')?>"><?=$this->lang->line('view_all_lbl')?> <i class="fa fa-angle-right" aria-hidden="true"></i></a></li>');
        }
      }
    });
  }


  if($(".dropdown-li").hasClass("active")){
    var _act_page='<?php echo $current_page; ?>';
    $("."+_act_page).next(".cust-dropdown-container").show();
    $("."+_act_page).find(".title").next("i").removeClass("fa-angle-right");
    $("."+_act_page).find(".title").next("i").addClass("fa-angle-down");
  }

  $(document).ready(function(e){

    $(".datepicker").datepicker({ dateFormat: 'dd-mm-yy' });

    $(".filter_datepicker").datepicker({ 
      maxDate: '0',
      dateFormat: 'dd-mm-yy',
      onSelect : function (dateText, inst) {
        $("#filterForm *").filter(":input").each(function(){
          if ($(this).val() == '')
            $(this).prop("disabled", true);
        });
        $("#filterForm").submit();
      }
    });

    var _flag=false;

    $(".dropdown-a").click(function(e){

      $(this).parents("ul").find(".cust-dropdown-container").slideUp();

      $(this).parents("ul").find(".title").next("i").addClass("fa-angle-right");
      $(this).parents("ul").find(".title").next("i").removeClass("fa-angle-down");

      if($(this).parent("li").next(".cust-dropdown-container").css('display') !='none'){
        $(this).parent("li").next(".cust-dropdown-container").slideUp();
        $(this).find(".title").next("i").addClass("fa-angle-right");
        $(this).find(".title").next("i").removeClass("fa-angle-down");
      }else{
        $(this).parent("li").next(".cust-dropdown-container").slideDown();
        $(this).find(".title").next("i").removeClass("fa-angle-right");
        $(this).find(".title").next("i").addClass("fa-angle-down");
      }
    });
  });

  $(document).on("click", ".actions", function(e) {
    e.preventDefault();

    var _table = $(this).data("table");
    var _column = $(this).data("column");

    var href = '<?= base_url("admin/pages/perform_multipe") ?>';

    if (typeof $(this).data('container') !== 'undefined') {

      var _container=$(this).data('container');
      var _ids = $.map(_container.find('.post_ids:checked'), function(c) {
        return c.value;
      });
    }
    else{
      var _ids = $.map($('.post_ids:checked'), function(c) {
        return c.value;
      });
    }

    var _action = $(this).data("action");

    if (_ids != '') {

      var confirmDlg = duDialog("<?= $this->lang->line('action_lbl') ?>: " + $(this).text(), "<?=$this->lang->line('action_confirm_msg')?>", {
        init: true,
        dark: false, 
        buttons: duDialog.OK_CANCEL,
        okText: 'Proceed',
        callbacks: {
          okClick: function(e) {
            $(".dlg-actions").find("button").attr("disabled",true);
            $(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> '+Settings.please_wait);

            $.ajax({
              type: 'post',
              url: href,
              dataType: 'json',
              data: {ids: _ids, for_action: _action, table: _table, column: _column},
              success: function(res) {

                $('.notifyjs-corner').empty();
                confirmDlg.hide();
                $(".post_ids").prop('checked', false);
                $("#checkall").prop('checked', false);

                if (_action == 'enable') {
                  myAlert(res.msg,'myalert-success');

                  $.each(_ids, function(key, val) {
                    $('.enable_disable').filter('[data-id="' + val + '"]').prop('checked', true);
                  });
                }
                else if (_action == 'disable') {

                  myAlert(res.msg,'myalert-success');

                  $.each(_ids, function(key, val) {
                    $('.enable_disable').filter('[data-id="' + val + '"]').prop('checked', false);
                  });
                }
                else{
                  location.reload(); 
                }
              }
            });

          }
        }
      });
      confirmDlg.show();
    } else {
      myAlert('<?= $this->lang->line('no_record_select_msg') ?>','myalert-danger');
    }

  });

  var totalItems = 0;

  $("#checkall").on("click",function() {

    totalItems = 0;

    $("input[name='post_ids[]']").not(this).prop('checked', this.checked);

    $.each($("input[name='post_ids[]']:checked"), function() {
      totalItems = totalItems + 1;
    });

    if ($("input[name='post_ids[]']").prop("checked") == true) {
      $('.notifyjs-corner').empty();
      $.notify(
        'Total '+totalItems+' item checked', {
          position: "top center",
          className: 'success',
          clickToHide: false,
          autoHide: false
        }
        );
    } else if ($("input[name='post_ids[]']").prop("checked") == false) {
      totalItems = 0;
      $('.notifyjs-corner').empty();
    }
  });

  $(".post_ids").click(function(e) {

    if ($(this).prop("checked") == true) {
      totalItems = totalItems + 1;
    } else if ($(this).prop("checked") == false) {
      totalItems = totalItems - 1;
    }

    if (totalItems == 0) {
      $('.notifyjs-corner').empty();
      exit();
    }

    $('.notifyjs-corner').empty();

    $.notify(
      'Total '+totalItems+' item checked', {
        position: "top center",
        className: 'success',
        clickToHide: false,
        autoHide: false
      }
      );
  });

</script>

<script type="text/javascript">

  $(document).on("click", ".btn_notification", function(e){
    e.preventDefault();
    var id=$(this).data("id");
    var sub_id=$(this).data("sub_id");
    var type=$(this).data("type");
    var title=$(this).data("title");
    var image=$(this).data("image");

    var _url = '<?=base_url("admin/pages/direct_send_notification")?>';

    var confirmDlg = duDialog("<?= $this->lang->line('are_you_sure_msg') ?>", "<?=$this->lang->line('notification_confirm_msg')?>", {
      init: true,
      dark: false, 
      buttons: duDialog.OK_CANCEL,
      okText: 'Proceed',
      callbacks: {
        okClick: function(e) {
          $(".dlg-actions").find("button").attr("disabled",true);
          $(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> '+Settings.please_wait);

          $.ajax({
            type:'POST',
            url: _url,
            data:{'id':id, 'type':type, 'sub_id':sub_id, 'title': title, 'image': image},
            dataType:'json',
            success:function(res){

              confirmDlg.hide();

              if(res.status=='1'){
                myAlert(res.msg,'myalert-success');
              }
              else{
                myAlert(res.msg,'myalert-danger');
              }
            }
          });
        }
      }
    });
    confirmDlg.show();
  });

  $(document).on("click", ".btn_top_action", function(e){
    e.preventDefault();

    var _url = $(this).attr("href");

    var confirmDlg = duDialog(null, "<?= $this->lang->line('are_you_sure_msg') ?>", {
      init: true,
      dark: false, 
      buttons: duDialog.OK_CANCEL,
      okText: 'Proceed',
      callbacks: {
        okClick: function(e) {
          $(".dlg-actions").find("button").attr("disabled",true);
          $(".ok-action").html('<i class="fa fa-spinner fa-pulse"></i> '+Settings.please_wait);

          confirmDlg.hide();
          window.location.href=_url;
        }
      }
    });
    confirmDlg.show();
  });

  $(document).on("click", ".enable_disable", function(e) {

    var _action;

    var href = '<?=base_url("admin/pages/perform_multipe")?>';
    var btn = this;
    var _id = $(this).data("id");
    var _table = $(this).data("table");
    var _column = $(this).data("column");

    var _for = $(this).prop("checked");
    if (_for == false) {
      _action = "disable";
    } else {
      _action = "enable";
    }

    var _parents=$(this).parents(".block_wallpaper");

    $.ajax({
      type: 'post',
      url: href,
      dataType: 'json',
      data: {ids: _id, for_action: _action, table: _table, column: _column},
      success: function(res) {

        if(_parents.find(".btn_notification").length > 0){

          if(_action=='enable')
            _parents.find(".hide_notification_icon").removeClass("hide_notification_icon").addClass("show_notification_icon");
          else
            _parents.find(".show_notification_icon").removeClass("show_notification_icon").addClass("hide_notification_icon");
        }

        myAlert(res.msg,'myalert-success');
      }
    });
  });

</script>

</body>
</html>