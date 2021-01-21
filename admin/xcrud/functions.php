<?php
function publish_action($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE base_fields SET `bool` = b\'1\' WHERE id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function unpublish_action($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE base_fields SET `bool` = b\'0\' WHERE id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function exception_example($postdata, $primary, $xcrud) {
    $xcrud->set_exception('ban_reason', 'Lol!', 'error');
    $postdata->set('ban_reason', 'lalala');
}

function test_column_callback($value, $fieldname, $primary, $row, $xcrud) {
    return $value . ' - nice!';
}

function after_upload_example($field, $file_name, $file_path, $params, $xcrud) {
    $ext = trim(strtolower(strrchr($file_name, '.')), '.');
    if ($ext != 'pdf' && $field == 'uploads.simple_upload') {
        //unlink($file_path);
        $xcrud->set_exception('simple_upload', 'This is not PDF', 'error');
    }
}

function date_example($postdata, $primary, $xcrud) {
    $created = $postdata->get('datetime')->as_datetime();
    $postdata->set('datetime', $created);
}

function active_inactive_status($status) {
    if ($status == '1') {
        return 'Active';
    } else if ($status == '0') {
        return 'Inactive';
    }
}

function active_user($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $query = 'UPDATE user SET `status` = \'1\' WHERE user_id = ' . $id;
        $db->query($query);

    }
}

function inactive_user($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE user SET `status` = \'0\' WHERE user_id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function category_callback($category_id){
    $db = Xcrud_db::get_instance();
   
    if( !empty($category_id)) {
        $db->query("SELECT c1.category_id, c1.category_name AS category FROM category_mst c1 WHERE c1.parent_id = 0 and c1.category_id='".$category_id."'
UNION
select c2.category_id, CONCAT ( c1.category_name,' > ',c2.category_name ) AS category FROM category_mst as c1 JOIN category_mst as c2 ON c2.parent_id=c1.category_id where c1.parent_id = 0 and c2.category_id='".$category_id."'
UNION
SELECT c3.category_id, CONCAT(c1.category_name,' > ',c2.category_name,' >',c3.category_name) AS category FROM category_mst c1 JOIN category_mst c2 ON c2.parent_id = c1.category_id JOIN category_mst c3 ON c3.parent_id = c2.category_id WHERE c1.parent_id = 0 and c3.category_id='".$category_id."'
UNION
SELECT c4.category_id, CONCAT(c1.category_name,' > ',c2.category_name,' >',c3.category_name,' >',c4.category_name) AS category FROM category_mst c1 JOIN category_mst c2 ON c2.parent_id = c1.category_id JOIN category_mst c3 ON c3.parent_id = c2.category_id JOIN category_mst c4 ON c4.parent_id = c3.category_id WHERE c1.parent_id = 0 and c4.category_id='".$category_id."'
UNION
SELECT c5.category_id, CONCAT(c1.category_name,' > ',c2.category_name,' >',c3.category_name,' >',c4.category_name,' >',c5.category_name) AS category FROM category_mst c1 JOIN category_mst c2 ON c2.parent_id = c1.category_id JOIN category_mst c3 ON c3.parent_id = c2.category_id JOIN category_mst c4 ON c4.parent_id = c3.category_id JOIN category_mst c5 ON c5.parent_id = c4.category_id WHERE c1.parent_id = 0 and c5.category_id='".$category_id."'");
        
        $result = $db->result();         
        $new_result = array();
        
        if($result) {
            foreach ($result as $key => $value) { 
               array_push($new_result,$value["category"]); 
            }
            return $new_result[0];
        }
    }
    else {
        return false;
    }
}

function brand_category($brand_id){
    $db = Xcrud_db::get_instance();
   
    if( !empty($brand_id)) {
        $db->query("select GROUP_CONCAT(c1.category_name SEPARATOR ', ') as category_name from brand_category_allocation bc join category_mst c1 ON c1.category_id=bc.category_id WHERE bc.brand_id=".$brand_id." group by brand_id");
        
        $result = $db->result();  
        
        if(!empty($result)){
            return $result[0]["category_name"]; 
        }
        else{
            return '';
        }
    }
    else {
        return false;
    }
}

function subcategory_callback($category_id){
   $db = Xcrud_db::get_instance();
   
    if( !empty($category_id)) {
        $db->query("select GROUP_CONCAT(category_name SEPARATOR ', ') as category_name from category_mst WHERE parent_id=".$category_id." and status = 1 group by parent_id");
        
        $result = $db->result(); 
        
        //print_r($result);
        
        if(!empty($result)){
            return $result[0]["category_name"]; 
        }
        else{
            return '';
        }
    }
    else {
        return false;
    }
}

function volume_type_callback($volume_id) {
    
   $db = Xcrud_db::get_instance();
   $query = 'SELECT concat(volume_mst.volumne_value," ",volume_type.volume_type) as volume, volume_mst.volume_id FROM volume_mst INNER JOIN volume_type ON volume_type.volume_type_id = volume_mst.type WHERE volume_mst.volume_id='.$volume_id;
   $db->query($query);
   $row = $db->row();
   return $row["volume"];
}

function top_pick($product_id) {
    
    $db = Xcrud_db::get_instance();
    $db->query("select top_pick from products WHERE status=1 and product_id = ".$product_id);
    $res = $db->row();
    
    if($res["top_pick"] == 0){
        $checkbox = '<input type="checkbox" class="top_pick_checkbox" name="top_pick" data-productid="'.$product_id.'">';
    } 
    else if($res["top_pick"] == 1){
        $checkbox = '<input type="checkbox" class="top_pick_checkbox" name="top_pick" data-productid="'.$product_id.'" checked>';
    } 
    
   return $checkbox;
}

function loyalty_club_menu($product_id) {
    
    $db = Xcrud_db::get_instance();
    $db->query("select loyalty_club from products WHERE status=1 and product_id = ".$product_id);
    $res = $db->row();
     
    if($res["loyalty_club"] == 0){
        $checkbox = '<input type="checkbox" class="loyalty_club_checkbox" name="loyalty_club" data-productid="'.$product_id.'">';
    } 
    else if($res["top_pick"] == 1){
        $checkbox = '<input type="checkbox" class="loyalty_club_checkbox" name="loyalty_club" data-productid="'.$product_id.'" checked>';
    } 
    
   return $checkbox;
}

function is_top_brand($brand_id) {
    
    $db = Xcrud_db::get_instance();
    $db->query("select is_top_brand from brand_mst WHERE is_top_brand=1 and brand_id = ".$brand_id);
    $res = $db->row();
     
    if($res["is_top_brand"] == 0){
        $checkbox = '<input type="checkbox" class="is_top_brand_checkbox_view" name="is_top_brand" data-brand_id="'.$brand_id.'">';
    } 
    else if($res["is_top_brand"] == 1){
        $checkbox = '<input type="checkbox" class="is_top_brand_checkbox_view" name="is_top_brand" data-brand_id="'.$brand_id.'" checked>';
    } 
    
   return $checkbox;
}

function update_product_flag($postdata, $primary, $xcrud){
    $db = Xcrud_db::get_instance();
    $db->query('UPDATE products set have_return_policy = 1 WHERE product_id = ' . $db->escape($postdata->get('product_id')));
}

function check_exist_return_policy($postdata, $xcrud){
    $db = Xcrud_db::get_instance();
   // echo 'select * from product_return_policy WHERE product_id = ' . $db->escape($postdata->get('product_id')); exit;
    $db->query('select product_return_policy_id from product_return_policy WHERE product_id = ' . $db->escape($postdata->get('product_id')));
    $result = $db->row();
    //print_r($result); exit;
    if(!empty($result)){
        $xcrud->set_exception('product_return_policy_id','Return Policy Already Exists','error');
    }
}

function check_date($postdata, $xcrud){
    $db = Xcrud_db::get_instance();
    $today = strtotime(date('Y-m-d H:i:s'));
    $expiry_date = strtotime($postdata->get('expiry_date'));
    if($today > $expiry_date){
        $xcrud->set_exception('expiry_date','Expiry date should be greater than current date','error');
    }
}

function check_exist_zipcode($postdata, $xcrud){
    $db = Xcrud_db::get_instance();
    $db->query('select zipcode_id from zipcode WHERE zipcode = ' . $db->escape($postdata->get('zipcode')));
    $result = $db->row();
    if(!empty($result)){
        $xcrud->set_exception('zipcode_id','Zip Code Already Exists','error');
    }
}

function active_zipcode($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $query = 'UPDATE zipcode SET `status` = \'1\' WHERE zipcode_id = ' . $id;
        $db->query($query);
    }
}

function inactive_zipcode($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE zipcode SET `status` = \'0\' WHERE zipcode_id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function remove_product($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $query = '';
              
        $filepath_product = '../../upload/product/';
        $filepath_product_thumb = '../../upload/product/thumbs/';
        $filepath_gallery = '../../upload/product/gallery/';
        $filepath_gallery_thumb = '../../upload/product/gallery/thumbs/';
        
        //remove images from product folder
        $get_product_img = 'select image_name from product_images WHERE product_id = ' . (int) $xcrud->get('primary');
        $db->query($get_product_img);
        $img_res = $db->result();        

        //remove images from product folder
        $get_product = 'select * from products WHERE product_id = ' . (int) $xcrud->get('primary');
        $db->query($get_product);
        $img_resp = $db->row();

        //delete product images
        $query1 = 'delete from product_images WHERE product_id = ' . $id;
        $db->query($query1);
        
        //delete product return policy
        $query2 = 'delete from product_return_policy WHERE product_id = ' . $id;
        $db->query($query2);
        
        //delete product details
        $query3 = 'delete from product_details WHERE product_id = ' . $id;
        $db->query($query3);
        
        //delete product
        $query4 = 'delete from products WHERE product_id = ' . $id;
        $db->query($query4);

    }
}

function delete_zipcode($zipcode_id) {
    
    $db = Xcrud_db::get_instance();
    $db->query("select zipcode_id from zipcode WHERE status=1 and zipcode_id = ".$zipcode_id);
    $res = $db->row();

    $checkbox = '<input type="checkbox" class="zipcode_id_checkbox" name="zipcode_id" data-zipcode_id="'.$zipcode_id.'">';

    return $checkbox;
}

function active_category($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $query = 'UPDATE category_mst SET `status` = \'1\' WHERE category_id = ' . $id;
        $db->query($query);
        $query1 = 'UPDATE brand_category_allocation SET `status` = \'1\' WHERE category_id = ' . $id;
        $db->query($query1);
        $query2 = 'UPDATE products SET `status` = \'1\' WHERE category_id = ' . $id;
        $db->query($query2);

    }
}

function inactive_category($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE category_mst SET `status` = \'0\' WHERE category_id = ' . (int) $xcrud->get('primary');
        $db->query($query);
        $query1 = 'UPDATE brand_category_allocation SET `status` = \'0\' WHERE category_id = ' . (int) $xcrud->get('primary');
        $db->query($query1);
        $query2 = 'UPDATE products SET `status` = \'0\' WHERE category_id = ' . (int) $xcrud->get('primary');
        $db->query($query2);
    }
}

function active_brand($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        
        //check if category is active or inactive
        $search = "SELECT brand_category_allocation.category_id FROM brand_category_allocation INNER JOIN category_mst ON brand_category_allocation.category_id = category_mst.category_id AND category_mst.status=1 WHERE brand_id = ". $id;
        $db->query($search);
        $result = $db->result();
        
        if(!empty($result)){        
            $query1 = 'UPDATE brand_mst SET `status` = \'1\' WHERE brand_id = ' . $id;
            $db->query($query1);
            $query2 = 'UPDATE products SET `status` = \'1\' WHERE brand_id = ' . $id;
            $db->query($query2);
        }
        else{
            $xcrud->set_exception('brand_id',"Cannot active brand as brand's Category is inactive.",'error');
        }

    }
}

function inactive_brand($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query1 = 'UPDATE brand_mst SET `status` = \'0\' WHERE brand_id = ' . (int) $xcrud->get('primary');
        $db->query($query1);
        $query2 = 'UPDATE products SET `status` = \'0\' WHERE brand_id = ' . (int) $xcrud->get('primary');
        $db->query($query2);
    }
}

function active_product($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        
        //check if category is active or inactive
        $search1 = "SELECT products.category_id FROM products INNER JOIN category_mst ON products.category_id = category_mst.category_id AND category_mst.status=1 WHERE product_id = ". $id;
        $db->query($search1);
        $result1 = $db->result();
        
        if(!empty($result1)){             
            //check if brand is active or inactive
            $search2 = "SELECT products.brand_id FROM products INNER JOIN brand_mst ON products.brand_id = brand_mst.brand_id AND brand_mst.status=1 WHERE product_id = ". $id;
            $db->query($search2);
            $result2 = $db->result();
            
            if(!empty($result2)){
                $query2 = 'UPDATE products SET `status` = \'1\' WHERE product_id = ' . $id;
                $db->query($query2);
//                $query3 = 'UPDATE product_details SET `status` = \'1\' WHERE product_id = ' . $id;
//                $db->query($query3);
            }
            else{
                $xcrud->set_exception('product_id',"Cannot active product as product's Brand is inactive.",'error');
            }
        }
        else{
            $xcrud->set_exception('product_id',"Cannot active product as product's Category is inactive.",'error');
        }
    }
}

function inactive_product($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query2 = 'UPDATE products SET `status` = \'0\' WHERE product_id = ' . (int) $xcrud->get('primary');
        $db->query($query2);
//        $query3 = 'UPDATE product_details SET `status` = \'0\' WHERE product_id = ' . (int) $xcrud->get('primary');
//        $db->query($query3);
    }
}

function active_seller($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $query = 'UPDATE seller SET `status` = \'1\' WHERE seller_id = ' . $id;
        $db->query($query);
	
	$query2 = 'UPDATE products SET `status` = \'1\' WHERE seller_id = ' . $id;
        $db->query($query2);
    }
}

function inactive_seller($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE seller SET `status` = \'0\' WHERE seller_id = ' . (int) $xcrud->get('primary');
        $db->query($query);
	
	$query2 = 'UPDATE products SET `status` = \'0\' WHERE seller_id = ' . (int) $xcrud->get('primary');
        $db->query($query2);
    }
}

function verified_user($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $query = 'UPDATE user SET `is_admin_verified` = \'1\' WHERE user_id = ' . $id;
        $db->query($query);
    }
}

function not_verified_user($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE user SET `is_admin_verified` = \'0\' WHERE user_id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function verified_seller($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $query = 'UPDATE seller SET `is_admin_verified` = \'1\' WHERE seller_id = ' . $id;
        $db->query($query);
    }
}

function not_verified_seller($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE seller SET `is_admin_verified` = \'0\' WHERE seller_id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function remove_brand($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $query = '';
              
        $filepath_product = '../../upload/brand/';
        $filepath_product_thumb = '../../upload/brand/thumbs/';
        
         //remove images from brand folder
        $get_product = 'select * from brand_mst WHERE brand_id = ' . (int) $xcrud->get('primary');
        $db->query($get_product);
        $img_resp = $db->row();
        
//        if(!empty($img_resp)) {
//            unlink($filepath_product.$img_resp["slider_img"]);
//            unlink($filepath_product_thumb.$img_resp["slider_img"]);
//            unlink($filepath_product.$img_resp["brand_logo"]);
//            unlink($filepath_product_thumb.$img_resp["brand_logo"]);
//        }
        //delete volume
        $query1 = 'delete from volume_mst WHERE brand_id = ' . $id;
        $db->query($query1);
        
        //delete brand_category_allocation
        $query2 = 'delete from brand_category_allocation WHERE brand_id = ' . $id;
        $db->query($query2);
        
        //delete brand
        $query4 = 'delete from brand_mst WHERE brand_id = ' . $id;
        $db->query($query4);

    }
}

function push_notification_order_history($postdata, $xcrud){ 
    if($postdata->get('order_status') == 4){        
        $message = 'Order Delivered';
        $db = Xcrud_db::get_instance();  
        $query2 = "select * from orders where order_id = ".$xcrud."";
        $db->query($query2);
        $result = $db->row();
         //echo $result['user_id']; exit;
        $query = "INSERT INTO notification (to_user_id, notification_type, message) VALUES (".$result['user_id'].", 3, '".$message."')";     
        $db->query($query);
        ?>
        <script>
            $.ajax({
                url: 'order_history/push_notify',
                data: 'to_user_id='+'<?php echo $result['user_id']; ?>',
                type: 'post',
                success: function () {}
            });
        </script>
        <?php
    }
}

function verifiy_document($user_id) {
    
    $db = Xcrud_db::get_instance();
    $db->query("select 	is_admin_verified from user WHERE status=1 and user_id = ".$user_id);
    $res = $db->row();
    
    if($res["is_admin_verified"] == 0){
        $checkbox = '<input type="checkbox" class="is_admin_verified_checkbox" name="is_admin_verified" data-userid="'.$user_id.'">';
    } 
    else if($res["is_admin_verified"] == 1){
        $checkbox = '<input type="checkbox" class="is_admin_verified_checkbox" name="is_admin_verified" data-userid="'.$user_id.'" checked>';
    } 
    
   return $checkbox;
}

function verifiy_seller_document($seller_id) {
    
    $db = Xcrud_db::get_instance();
    $db->query("select 	is_admin_verified from seller WHERE status=1 and seller_id = ".$seller_id);
    $res = $db->row();
    
    if($res["is_admin_verified"] == 0){
        $checkbox = '<input type="checkbox" class="is_admin_verified_checkbox" name="is_admin_verified" data-userid="'.$seller_id.'">';
    } 
    else if($res["is_admin_verified"] == 1){
        $checkbox = '<input type="checkbox" class="is_admin_verified_checkbox" name="is_admin_verified" data-userid="'.$seller_id.'" checked>';
    } 
    
   return $checkbox;
}

function delete_user_doc($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');                      
        //$filepath_product = '../../upload/verification_docs/';
        
         //remove verification document from folder
        $get_doc = 'select verification_doc from user WHERE user_id = ' . (int) $xcrud->get('primary');
        $db->query($get_doc);
        $img_resp = $db->row();        
       // print_r($img_resp); exit;
        
        if(!empty($img_resp["verification_doc"])) {
            //unlink($filepath_product.$img_resp["verification_doc"]);
            //update msg
            $query1 = 'update user set doc_deleted = "1" where user_id = '.$id;
            $db->query($query1);
        }
        else{
            $xcrud->set_exception('user_id',"No Document Uploaded",'error');
        }
    }
}

function insert_notification($postdata, $xcrud) {
    $db = Xcrud_db::get_instance();
    $get_user = "select user_id from user where status = 1";
    $db->query($get_user);
    $result = $db->result();  
    
    //get notification count
    $get_count = 'select notification_count from notification where notification_type = 2 order by date desc limit 1';
    $db->query($get_count);
    $row = $db->row();  
    $notification_count = $row["notification_count"] + 1;
    //print_r($result); exit;

    foreach($result as $res) {
        $query = 'insert into notification (to_user_id, notification_type, notification_count, message) values ('.$res["user_id"].', 2, '.$notification_count.', "'.$postdata->get("message").'")';
        $db->query($query);
    ?>
    <script>
        $.ajax({
            url: 'users/push_notify',
            data: 'to_user_id='+'<?php echo $res["user_id"]; ?>'+'&message='+'<?php echo $postdata->get("message"); ?>',
            type: 'post',
            success: function () {}
        });
    </script>
    <?php
    }    
}

function notification_callback($notification_type) {
   // echo $notification_type; exit;
    
   $db = Xcrud_db::get_instance();
   $query = 'SELECT distinct (message) FROM notification WHERE notification_type = '.$notification_type.' order by date desc';
   $db->query($query);
   $row = $db->row();
   return $row["message"];
}

function seller_label($seller_id) {    
   $db = Xcrud_db::get_instance();
   if($seller_id == '0'){
       $row["seller_name"] = "Admin";
   }
   else {
    $query = 'SELECT seller_name FROM seller WHERE seller_id = '.$seller_id.'';
    $db->query($query);
    $row = $db->row();
   }
   return $row["seller_name"];
}

function get_username($user_id) {    
    $db = Xcrud_db::get_instance();   
    $query = 'SELECT concat(firstname," ",lastname) as name FROM user WHERE user_id = '.$user_id.'';
    $db->query($query);
    $row = $db->row();
   return $row["name"];
}

function transaction_amount($order_transaction_id) {    
    $db = Xcrud_db::get_instance();   
    $query = 'SELECT * FROM order_transaction WHERE order_transaction_id = '.$order_transaction_id.'';
    $db->query($query);
    $row = $db->row();
    $json_decode = json_decode($row["payment_history"], true);
    $query1 = 'SELECT * FROM setting WHERE `key` = "currency"';
    $db->query($query1);
    $currency = $db->row();
    if(isset($json_decode["amount"])) {
        if($row["payment_mode"] == 1){
            return $currency["value"].$json_decode["amount"] / 100;
        }
        else {
            return $currency["value"].$json_decode["amount"];
        }
    }
    else {
        return $currency["value"].'0';
    }
}

function add_days($postdata, $xcrud){
    //echo $xcrud;
   // echo "<pre>"; print_r($postdata); exit;
    //echo $postdata->get('schedule_order_list_id'); exit;
    //insert schedule title    
    if($postdata->get('type') == 1){
        $schedule_on_title = $postdata->get('no_of_days')." Days";
        $total_days = $postdata->get('no_of_days');
    }
    else if($postdata->get('type') == 2){
        $schedule_on_title = $postdata->get('no_of_days')." Weeks";
        $total_days = $postdata->get('no_of_days') * 7;
    }
    else if($postdata->get('type') == 3){
        $schedule_on_title = $postdata->get('no_of_days')." Months";
        $total_days = $postdata->get('no_of_days') * 30;
    }
    $db = Xcrud_db::get_instance();   
    $query = 'UPDATE schedule_order_list SET schedule_on_title = "Every '.$schedule_on_title.'", total_days = "'.$total_days.'" WHERE schedule_order_list_id = '.$xcrud.'';
    $db->query($query);
}

function admin_notification_callback($notification_id) {
    $db = Xcrud_db::get_instance();
    $query = 'SELECT message FROM notification WHERE notification_type = 2 and notification_id = '.$notification_id.' group by notification_count order by date desc';
    $db->query($query);
    $row = $db->row();
    //print_r($row); exit;
    if(!empty($row)) {
        return $row["message"];
    }
}

function active_delivery_zone($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $query = 'UPDATE delivery_zone SET `status` = \'1\' WHERE dzone_id = ' . $id;
        $db->query($query);

    }
}

function inactive_delivery_zone($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE delivery_zone SET `status` = \'0\' WHERE dzone_id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function check_exist_zone($postdata, $xcrud){
    $db = Xcrud_db::get_instance();
    $db->query('select dzone_id from delivery_zone WHERE post_code = "' . $postdata->get('post_code'). '"');
    $result = $db->row();
    if(!empty($result)){
        $xcrud->set_exception('dzone_id','Postal Code Code Already Exists','error');
    }
    else {
        //$postdata->set('area_name', ucwords($postdata->get('area_name')));
        $postdata->set('area_code', strtoupper($postdata->get('area_code')));
        $postdata->set('city', ucwords($postdata->get('city')));
    }
}

function add_areacode_in_zone($postdata){
    //$postdata->set('area_name', ucwords($postdata->get('area_name')));
    $postdata->set('area_code', strtoupper($postdata->get('area_code')));
    $postdata->set('city', ucwords($postdata->get('city')));
}

function active_testimonial($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $query = 'UPDATE testimonials SET `status` = \'1\' WHERE testimonials_id = ' . $id;
        $db->query($query);

    }
}

function inactive_testimonial($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE testimonials SET `status` = \'0\' WHERE testimonials_id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function active_alcohol_awareness($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $query = 'UPDATE alcohol_awareness SET `status` = \'1\' WHERE aid = ' . $id;
        $db->query($query);

    }
}

function inactive_alcohol_awareness($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE alcohol_awareness SET `status` = \'0\' WHERE aid = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function list_img_function($value, $fieldname, $primary_key, $row, $xcrud) {   
    if(!empty($value)) {
	$url = $xcrud->get_var('s3_path');
	return '<img alt="" src="'. $url . $value .'" style="max-height: 55px;">';
    }
    else {
	return '';
    }
}

function view_img_function($value, $fieldname, $primary_key, $row, $xcrud) {
    if(!empty($value)) {
	$url = $xcrud->get_var('s3_path');
	return '<img alt="" src="'. $url . $value .'" style="max-height: 300px;">';
    }
    else {
	return '';
    }
}

function list_pdf_function($value, $fieldname, $primary_key, $row, $xcrud) {   
    if(!empty($value)) {
	$url = $xcrud->get_var('s3_path');
	return '<a href="'. $url . $value .'" target="_blank">'. $value .'</a>';
    }
    else {
	return '';
    }
}

function active_user_request($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
	//get user requested details
	$db->query('SELECT * FROM driver_requests WHERE request_id = ' . $id);
        $requested_details = $db->row();
	
	$db->query('SELECT * FROM user WHERE user_id = ' . $requested_details["user_id"]);
        $user_details = $db->row();
	
	if(!empty($requested_details["firstname"])) {
	    $firstname = $requested_details["firstname"];
	}
	else {
	    $firstname = $user_details["firstname"];
	}
	
	if(!empty($requested_details["lastname"])) {
	    $lastname = $requested_details["lastname"];
	}
	else {
	    $lastname = $user_details["lastname"];
	}
	
	if(!empty($requested_details["birthdate"])) {
	    $requested_details["birthdate"] = str_replace('/', '-', $requested_details["birthdate"]);
	    $birthdate = date('Y-m-d', strtotime($requested_details["birthdate"]));
	}
	else {
	    $birthdate = $user_details["birthdate"];
	}
	
	if(!empty($requested_details["mobileno"])) {
	    $mobileno = $requested_details["mobileno"];
	}
	else {
	    $mobileno = $user_details["mobileno"];
	}
	
	if(!empty($requested_details["email"])) {
	    $email = $requested_details["email"];
	}
	else {
	    $email = $user_details["email"];
	}
	
	if(!empty($requested_details["image"])) {
	    $image = $requested_details["image"];
	}
	else {
	    $image = $user_details["profile_image"];
	}
	
	//update in user table
	$db->query('UPDATE user SET firstname = "'.$firstname.'", lastname = "'.$lastname.'", birthdate = "'.$birthdate.'", mobileno = "'.$mobileno.'", email = "'.$email.'", profile_image = "'.$image.'" WHERE user_id = ' . $requested_details["user_id"]);
	
        //update in request table
	$db->query('UPDATE driver_requests SET `status` = \'1\' WHERE request_id = ' . $id);

	//send notification to user
	$message = "Your reuqest for update has been accepted & your profile details have been updated";
        $query = 'insert into notification (to_user_id, notification_type, driver_id, message) values ('.$requested_details["user_id"].', 20, '.$requested_details["user_id"].', "'.$message.'")';
        $db->query($query);
    ?>
    <script>
        $.ajax({
            url: 'users/push_notify',
            data: 'to_user_id='+'<?php echo $requested_details["user_id"]; ?>'+'&message='+'<?php echo $message; ?>&notification_type=20',
            type: 'post',
            success: function () {}
        });
    </script>
    <?php       
    }
}

function inactive_user_request($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE driver_requests SET `status` = \'0\' WHERE request_id = ' . (int) $xcrud->get('primary');
        $db->query($query);
	
	//get user requested details
	$db->query('SELECT * FROM driver_requests WHERE request_id = ' . $id);
        $requested_details = $db->row();
	
	//send notification to user
	$message = "Your reuqest for update has been rejected";
        $query2 = 'insert into notification (to_user_id, notification_type, driver_id, message) values ('.$requested_details["user_id"].', 21, '.$requested_details["user_id"].', "'.$message.'")';
        $db->query($query2);
    ?>
    <script>
        $.ajax({
            url: 'users/push_notify',
            data: 'to_user_id='+'<?php echo $requested_details["user_id"]; ?>'+'&message='+'<?php echo $message; ?>&notification_type=21',
            type: 'post',
            success: function () {}
        });
    </script>
    <?php
    }
}

function active_user_vehicle_request($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
	//get user requested details
	$db->query('SELECT * FROM driver_vehicle_requests WHERE request_id = ' . $id);
        $requested_details = $db->row();
	
	$db->query('SELECT * FROM vehicle_mst WHERE vehicle_id = ' . $requested_details["vehicle_id"]);
        $vehicle_details = $db->row();
	
	if(!empty($requested_details["car_name"])) {
	    $car_name = $requested_details["car_name"];
	}
	else {
	    $car_name = $vehicle_details["model"];
	}
	
	if(!empty($requested_details["vehicle_make"])) {
	    $vehicle_make = $requested_details["vehicle_make"];
	}
	else {
	    $vehicle_make = $vehicle_details["maker"];
	}
	
	if(!empty($requested_details["vehicle_reg_no"])) {
	    $vehicle_reg_no = $requested_details["vehicle_reg_no"];
	}
	else {
	    $vehicle_reg_no = $vehicle_details["registration_number"];
	}
	
	if(!empty($requested_details["ins_policy_no"])) {
	    $ins_policy_no = $requested_details["ins_policy_no"];
	}
	else {
	    $ins_policy_no = $vehicle_details["vehicle_policy_number"];
	}
	
	if(!empty($requested_details["ins_certificate_no"])) {
	    $ins_certificate_no = $requested_details["ins_certificate_no"];
	}
	else {
	    $ins_certificate_no = $vehicle_details["ins_certificate_no"];
	}
	
	//update in vehicle table
	$db->query('UPDATE vehicle_mst SET model = "'.$car_name.'", registration_number = "'.$vehicle_reg_no.'", maker = "'.$vehicle_make.'", vehicle_policy_number = "'.$ins_policy_no.'", ins_certificate_no = "'.$ins_certificate_no.'" WHERE vehicle_id = ' . $requested_details["vehicle_id"] . ' and driver_id = ' . $requested_details["user_id"]);
	
        //update in request table
	$db->query('UPDATE driver_vehicle_requests SET `status` = \'1\' WHERE request_id = ' . $id);
	
	//update in vehicle images table
	$db->query('SELECT * FROM driver_vehicle_image_request WHERE vehicle_id = ' . $requested_details["vehicle_id"]);
        $get_images = $db->result();	
	
	if(!empty($get_images)) {
	    $db->query('SELECT * FROM vehicle_images WHERE vehicle_id = ' . $requested_details["vehicle_id"]);
	    $check = $db->row();
	    
//	    if(!empty($check)) {	
//		$db->query('UPDATE vehicle_images SET status = 0 WHERE vehicle_id = ' . $requested_details["vehicle_id"]);
//	    }    

	    foreach($get_images as $img) {
		$db->query('INSERT INTO vehicle_images (vehicle_id, image_name) VALUES (' . $requested_details["vehicle_id"]. ', "'. $img["image_name"].'")');
	    }	

	    //update in request table
	    $db->query('UPDATE driver_vehicle_image_request SET `status` = \'1\' WHERE request_id = ' . $id);
	}
	
	//send notification to user
	$message = "Your reuqest for update has been accepted & your vehicle details have been updated";
        $query = 'insert into notification (to_user_id, notification_type, driver_id, message) values ('.$requested_details["user_id"].', 22, '.$requested_details["user_id"].', "'.$message.'")';
        $db->query($query);
    ?>
    <script>
        $.ajax({
            url: 'users/push_notify',
            data: 'to_user_id='+'<?php echo $requested_details["user_id"]; ?>'+'&message='+'<?php echo $message; ?>&notification_type=22',
            type: 'post',
            success: function () {}
        });
    </script>
    <?php      
    }
}

function inactive_user_vehicle_request($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $db->query('UPDATE driver_vehicle_requests SET `status` = \'0\' WHERE request_id = ' . (int) $xcrud->get('primary'));
	
	$db->query('UPDATE driver_vehicle_image_request SET `status` = \'0\' WHERE request_id = ' . (int) $xcrud->get('primary'));
	
	$db->query('SELECT * FROM driver_vehicle_requests WHERE request_id = ' . (int) $xcrud->get('primary'));
        $requested_details = $db->row();
	
	//send notification to user
	$message = "Your reuqest for update has been accepted & your vehicle details have been updated";
        $query = 'insert into notification (to_user_id, notification_type, driver_id, message) values ('.$requested_details["user_id"].', 23, '.$requested_details["user_id"].', "'.$message.'")';
        $db->query($query);
    ?>
    <script>
        $.ajax({
            url: 'users/push_notify',
            data: 'to_user_id='+'<?php echo $requested_details["user_id"]; ?>'+'&message='+'<?php echo $message; ?>&notification_type=23',
            type: 'post',
            success: function () {}
        });
    </script>
    <?php     
    }
}

function request_to_update_image($id) {
    $db = Xcrud_db::get_instance();
    $db->query("select request_id from driver_vehicle_image_request where request_id = ".$id);
    $result = $db->result();     
    if(!empty($result)) {
	return 'Yes';
    }
    else {
	return 'No';
    }
}

function active_driver($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $db->query('UPDATE user SET `status` = \'1\' WHERE user_id = ' . $id);
	$db->query('UPDATE driver_by_invite SET `status` = \'1\' WHERE driver_id = ' . $id);
	$db->query('UPDATE driver_docs SET `status` = \'1\' WHERE user_id = ' . $id);
    }
}

function inactive_driver($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE user SET `status` = \'0\' WHERE user_id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function view_volumne($volumne_id) {
    $db = Xcrud_db::get_instance();
    $db->query('SELECT CONCAT(volume_mst.volumne_value," ",volume_type.volume_type) AS volumne FROM volume_mst JOIN volume_type ON volume_type.volume_type_id = volume_mst.type WHERE volume_mst.volume_id = ' . $volumne_id);	  $row = $db->row();
    return $row["volumne"];
}

function view_product_volume($product_id) {
    $db = Xcrud_db::get_instance();
    $db->query('Select CONCAT(volume_mst.volumne_value," ",volume_type.volume_type) AS volume From product_details Join volume_mst on volume_mst.volume_id = product_details.volume_id Join volume_type ON volume_type.volume_type_id = volume_mst.type Where product_details.product_id = '.$product_id.'');
    $result = $db->result();
    //echo "<pre>"; print_r($result); exit;
    if(!empty($result)) {
	if(count($result) > 1) {
	    return $result[0]["volume"]." and ".count($result)." more";
	}
	else {
	    return $result[0]["volume"];
	}
    }
    else {
	return "no volume";
    }
}

function view_product_price($product_id) {
    $db = Xcrud_db::get_instance();
    $db->query("Select normal_sell_price From product_details Where product_id = ".$product_id."");
    $result = $db->result();
    //echo "<pre>"; print_r($result); exit;
    if(!empty($result)) {
	return "£".$result[0]["normal_sell_price"];
    }
    else {
	return "£0.00";
    }
}	

function view_product_actual_price($product_id) {
    $db = Xcrud_db::get_instance();
    $db->query("Select actual_price From product_details Where product_id = ".$product_id."");
    $result = $db->result();
    //echo "<pre>"; print_r($result); exit;
    if(!empty($result)) {
	return "£".$result[0]["actual_price"];
    }
    else {
	return "£0.00";
    }
}

function view_product_discount($product_id) {
    $db = Xcrud_db::get_instance();
    $db->query("Select normal_discount From product_details Where product_id = ".$product_id."");
    $result = $db->result();
    //echo "<pre>"; print_r($result); exit;
    if(!empty($result)) {
	return $result[0]["normal_discount"];
    }
    else {
	return "0";
    }
}

function select_multiple($product_id) {    
    return '<input type="checkbox" class="select_multiple_checkbox" name="select_multiple" data-productid="'.$product_id.'" value="'.$product_id.'">';
}

function select_multiple_delivery_zone($dzone_id) {    
    return '<input type="checkbox" class="select_multiple_checkbox" name="select_multiple" data-dzone_id="'.$dzone_id.'" value="'.$dzone_id.'">';
}

function active_bonus($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $id = (int) $xcrud->get('primary');
        $db->query('UPDATE bonus_list SET `status` = \'1\' WHERE bonus_id = ' . $id);
	$db->query('UPDATE bonus_list SET `new_created_date` = NOW() WHERE bonus_id = ' . $id);
    }
}

function inactive_bonus($xcrud) {
    if ($xcrud->get('primary')) {
        $db = Xcrud_db::get_instance();
        $query = 'UPDATE bonus_list SET `status` = \'0\' WHERE bonus_id = ' . (int) $xcrud->get('primary');
        $db->query($query);
    }
}

function before_bonus_list_callback($grid_data, $xcrud){
    $db = Xcrud_db::get_instance();
    $db->query("select bonus_id, no_of_days, new_created_date, status from bonus_list where status = 1");
    $result = $db->result();
    
    foreach($result as $key => $value) {
	$diff = strtotime(date('Y-m-d', strtotime($value["new_created_date"]))) - strtotime(date('Y-m-d')); 
	$total_days = abs(round($diff / 86400)); 	
	if($total_days > $value["no_of_days"]) {
	    $db->query('UPDATE bonus_list SET `status` = 0 WHERE bonus_id = ' . $value["bonus_id"]);
	}
    }
}
