<?php 
$row_banner=$banner[0];
define('IMG_PATH', base_url().'assets/images/product/');
$CI=&get_instance();

?>
<style type="text/css">
    .top{
        position: relative !important;
        padding: 0px 0px 20px 0px !important;
    }
    .dataTables_wrapper{
        overflow: initial !important;
    }  
</style>
<div class="row card_item_block" style="padding-left:30px;padding-right: 30px">
    <div class="col-xs-12">
        <?php 
        if(isset($_GET['redirect'])){
            echo '<a href="'.$_GET['redirect'].'"><h4 class="pull-left btn_back" style=""><i class="fa fa-arrow-left"></i> Back</h4></a>';
        }
        else{
            echo '<a href="'.base_url('admin/banner').'"><h4 class="pull-left btn_back" style=""><i class="fa fa-arrow-left"></i> Back</h4></a>'; 
        }
        ?>
        <div class="card mrg_bottom">
            <div class="page_title_block">
                <div class="col-md-5 col-xs-12">
                    <div class="page_title" style="font-size: 18px;"><?=$row_banner->banner_title?></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 mrg-top">
                <table class="datatable table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="30">#</th>
                            <th><?=$this->lang->line('img_lbl')?></th>
                            <th><?=$this->lang->line('products_lbl')?></th>
                            <th><?=$this->lang->line('action_lbl')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $products=$CI->product_list($row_banner->product_ids);
                        $no=1;

                        if(!empty($products))
                        {
                            foreach ($products as $key => $value) {
                                ?>
                                <tr class="item_holder">
                                    <td><?=$no++?></td>
                                    <td>
                                        <a href="<?=($value->featured_image!='') ? base_url('assets/images/products/'.$value->featured_image) : 'javascript:void(0)'?>" target="_blank">
                                            <img src="<?=base_url('assets/images/products/'.$value->featured_image)?>" alt="" style="width: 50px;height: 50px">
                                        </a>
                                    </td>
                                    <td title="<?=$value->product_title?>">
                                        <?php 
                                        if(strlen($value->product_title) > 45){
                                            echo substr(stripslashes($value->product_title), 0, 50).'...';  
                                        }else{
                                            echo $value->product_title;
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="" data-id="<?=$value->id?>" data-banner="<?=$row_banner->id?>" class="btn btn-danger btn_delete" style="font-size: 12px"> <?=$this->lang->line('remove_lbl')?></a>
                                    </td>
                                </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        $(document).on("click", ".btn_delete", function(e){
            e.preventDefault();

            var product_id=$(this).data("id");
            var banner_id=$(this).data("banner");

            var _url = '<?=base_url()?>admin/banner/remove_product';

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
                            url: _url,
                            dataType: 'json',
                            data:{"banner_id":banner_id,"product_id":product_id},
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