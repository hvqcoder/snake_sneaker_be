<?php  
  $this->load->view('site/layout/breadcrumb'); 
?>
<div class="wishlist-table-area mt-20 mb-50">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <?php 
            if(!empty($wishlist))
            {
          ?>
          <div class="wishlist-table table-responsive">

            <table>
              <thead>
                <tr>
                  <th class="product-remove"></th>
                  <th class="product-cart-img"> <span class="nobr">Product Image</span> </th>
                  <th class="product-name"> <span class="nobr">Product Name</span> </th>
                  <th class="product-price"> <span class="nobr"> Price </span> </th>
                  <th class="product-add-to-cart">Add To Cart</th>
                </tr>
              </thead>
              <tbody>
              	<?php 
                  $ci =& get_instance();
                  foreach ($wishlist as $row)
                  {
                    $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $row->featured_image);
                    
                    $img_file=$ci->_generate_thumbnail('assets/images/products/',$thumb_img_nm,$row->featured_image,50,50);

                    $is_avail=true;

                    if($row->status==0){
                      $is_avail=false;
                    }

                ?>
                <tr>
                  <td class="product-remove">
                    <a href="javascript:void(0)" class="btn_remove_wishlist" title="remove" data-id="<?=$row->product_id?>">×</a>
                  </td>
                  <td <?=(!$is_avail) ? 'style="opacity: 0.5;"' : ''?> class="product-cart-img">
                    <img src="<?=base_url($img_file)?>" alt="<?=$row->product_slug?>" title="<?=$row->product_slug?>" style="width: 50px;height: 50px">
                  </td>
                  <td class="product-name">
                    <a href="<?php echo site_url('product/'.$row->product_slug); ?>" <?=(!$is_avail) ? 'style="opacity: 0.5;"' : ''?>>
                      <?php
                        if(strlen($row->product_title) > 50){
                          echo substr(stripslashes($row->product_title), 0, 50).'...';  
                        }else{
                          echo $row->product_title;
                        }
                      ?>
                    </a>
                    <?php 
                      if(!$is_avail){
                        echo '<p style="color: red;background: #FFF;display: inline-block;box-shadow: 0px 5px 10px #ccc;padding: 5px 10px;line-height: initial">'.$this->lang->line('unavailable_lbl').'</p>';
                      }
                    ?>
                  </td>
                  <td <?=(!$is_avail) ? 'style="opacity: 0.5"' : ''?> class="product-price" nowrap="">
                    <span>
                      <?php 
                        if($row->you_save_amt!='0'){
                          ?>
                          <ins><?=CURRENCY_CODE.' '.number_format($row->selling_price, 2)?></ins> 
                          <del><?=CURRENCY_CODE.' '.number_format($row->product_mrp, 2);?></del>
                          <?php
                        }
                        else{
                          ?>
                          <ins><?=CURRENCY_CODE.' '.number_format($row->product_mrp, 2);?></ins>
                          <?php
                          
                        }
                      ?>
                    </span>
                  </td>
                  <td <?=(!$is_avail) ? 'style="opacity: 0.5"' : ''?> class="product-add-to-cart">
                    <a href="javascript:void(0)" title="wishlist-btn" class="wishlist-btn btn_cart <?=(!$is_avail) ? 'disabled"' : ''?>" data-id="<?=$row->product_id?>" data-maxunit="<?=$row->max_unit_buy?>"><?=$this->lang->line("add_cart_lbl")?></a>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <?php 
            }else{
            ?>
            <center>
              <img src="<?=base_url('assets/img/empty_wishlist.png')?>" alt="empty-wishlist" title="empty-wishlist" style="width: 200px;">
              <h2 style="font-size: 18px;font-weight: 500;color: #888;text-transform: capitalize;">
                <?=$this->lang->line('empty_wishlist_lbl')?>
              </h2>
              <br/>
              <a href="<?=base_url('/')?>" title="continue-shopping">
                <img src="<?=base_url('assets/images/continue-shopping-button.png')?>" alt="continue-shopping" title="continue-shopping" style="width: 200px;height: 100%;">
              </a>
            </center>
            <?php
          } ?>
        </div>
      </div>
    </div>
</div>
