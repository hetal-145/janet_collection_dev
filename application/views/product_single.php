<?php // echo "<pre>"; print_r($products); exit; ?>
    
<?php if(!empty($products)) { ?>    
    <input type="hidden" name="offset" id="offset" value="<?php echo $offset; ?>">
    <input type="hidden" name="flag" id="flag" value="<?php echo $flag; ?>">
    <input type="hidden" name="stype" id="stype" value="<?php echo $stype; ?>">
    
    <div class="row" id="product_list">
	<?php foreach($products as $fav) { ?>
	    <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 mb-2 mt-2">
		<div class="card product_card">
		    <a title="<?php echo $fav["product_name"]; ?>" href="<?php echo base_url() . 'ppd?pid='. urlencode(base64_encode($fav["product_id"])); ?>">
			<div class="product_img">
			    <img src="<?php echo $fav["feature_img"]; ?>" class="img-fluid" alt="">
			</div>
			<div class="product_body text-center">
			    <p class="price"><?php echo CURRENCY_CODE . $fav["price"]; ?> <small class="discount_price"><strike><?php echo CURRENCY_CODE . $fav["actual_price"]; ?></strike></small></p>
			    <p class="title d-block text-truncate" ><?php echo $fav["product_name"]; ?></p>
			    <p class="sub_title"><?php echo $fav["seller"]["seller_name"]; ?></p>
			</div>
			<div class="wish_list">
			    <?php if ($this->session->userdata('loged_in')) { ?>    
			    <input class="d-none like_unlike" type="checkbox" id="heart_1_<?php echo $fav["product_id"]; ?>" name="heart_1_<?php echo $fav["product_id"]; ?>" data-prid="<?php echo $fav["product_id"]; ?>" <?php if($fav["is_favourite"] == 1){ ?> checked="checked" <?php } ?>>
			    <label class="like_unlike_click" for="heart_1_<?php echo $fav["product_id"]; ?>"><i class="icon mdi mdi-heart"></i></label>
			    <?php } ?>
			</div>
		    </a>
		</div>
	    </div>
	<?php } ?>
    </div>    
    
    <div class="col-12 text-center mt-4 loadMore">
	<a href="javascript:void(0);" class="btn btn-pink" id="loadMore">Load More</a>
    </div>
    
<?php } else { ?>
    <?php redirect(base_url('home/no_data')); ?>
<?php } ?> 

<script type="text/javascript">    
function showProduct(resp) {
    if(resp == 'error') {
	$(".loadMore").hide();
    }
    else {
	var res = $.parseJSON(resp);  
	$.each(res["products"], function(key, value) {
	    //console.log(value); 
	    var condition = value["is_favourite"] == "1" ? 'checked="checked"' : '';
	    $("#product_list").append('<div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 mb-2 mt-2"><div class="card product_card"><a title="'+ value["product_name"] +'" href="<?php echo base_url() ?>'+'ppd?pid='+ encodeURI(btoa(value["product_id"])) +'"><div class="product_img"><img src="'+ value["feature_img"] +'" class="img-fluid" alt=""></div><div class="product_body text-center"><p class="price"><?php echo CURRENCY_CODE; ?>'+ value["price"] +' <small class="discount_price"><strike><?php echo CURRENCY_CODE; ?>'+ value["actual_price"] +'</strike></small></p><p class="title d-block text-truncate" >'+ value["product_name"] +'</p><p class="sub_title">'+ value["seller"]["seller_name"] +'</p></div><div class="wish_list"><?php if ($this->session->userdata('loged_in')) { ?><input class="d-none like_unlike" type="checkbox" id="heart_1_'+value["product_id"]+'" name="heart_1_'+value["product_id"]+'" data-prid="'+value["product_id"]+'"'+ condition +'><label class="like_unlike_click" for="heart_1_'+value["product_id"]+'"><i class="icon mdi mdi-heart"></i></label><?php } ?></div></a></div></div>');
	});
	$("#offset").val(""); 
	$("#offset").val(res["offset"]);

	$("#flag").val(""); 
	$("#flag").val(res["flag"]);

	if(res["flag"] == 0) {
	    $(".loadMore").hide();
	}
    }                
}

$(document).ready(function(){
    if( $("#flag").val() == '0' ) {

	$(".loadMore").hide();
    }

    $("#loadMore").on('click', function (e) {	
	e.preventDefault();
	//searhc type
	var stype = $("#stype").val();
	if(stype == '1') {
	    $.ajax({
		url: "<?php echo base_url() . 'products/product_list'; ?>",
		type: "post",
		data: "byajax=1&cid="+'<?php if(isset($cid)) { echo $cid; } else { echo ''; } ?>'+"&offset="+$("#offset").val(), 
		success: function (resp)
		{
		    showProduct(resp);
		}
	    });
	}
	else if(stype == '2') {
	    $.ajax({
		url: "<?php echo base_url() . 'products/search_product'; ?>",
		type: "post",
		data: "byajax=1&chr="+'<?php if(isset($chr)) { echo $chr; } else { echo ''; } ?>'+"&offset="+$("#offset").val(), 
		success: function (resp)
		{
		    showProduct(resp);
		}
	    });
	}
	else if(stype == '3') {
	    $.ajax({
		url: "<?php echo base_url() . 'products/similar_drinks'; ?>",
		type: "post",
		data: "byajax=1&pid="+'<?php if(isset($pid)) { echo $pid; } else { echo ''; } ?>'+"&offset="+$("#offset").val(), 
		success: function (resp)
		{
		    showProduct(resp);
		}
	    });
	}
	else if(stype == '4') {	
	    $.ajax({
		url: "<?php echo base_url() . 'products/favourite_products'; ?>",
		type: "post",
		data: "byajax=1&offset="+$("#offset").val(), 
		success: function (resp)
		{
		    showProduct(resp);
		}
	    });
	}
	else if(stype == '5') {	
	    $.ajax({
		url: "<?php echo base_url() . 'products/filtered_product'; ?>",
		type: "post",
		data: "byajax=1&offset="+$("#offset").val(), 
		success: function (resp)
		{
		    showProduct(resp);
		}
	    });
	}
	else if(stype == '6') {	
	    $.ajax({
		url: "<?php echo base_url() . 'loyalty_points/loyality_club'; ?>",
		type: "post",
		data: "byajax=1&offset="+$("#offset").val(), 
		success: function (resp)
		{
		    showProduct(resp);
		}
	    });
	}
	else if(stype == '7') {	
	    $.ajax({
		url: "<?php echo base_url() . 'loyalty_points/vip_club'; ?>",
		type: "post",
		data: "byajax=1&offset="+$("#offset").val(), 
		success: function (resp)
		{
		    showProduct(resp);
		}
	    });
	}
    });
    
    $(".like_unlike_click").click(function() {
        var product_id = $(this).prev().data("prid");
        if($(this).prev().prop("checked") == true){
            var fav_val = 0;
        }
        else if($(this).prev().prop("checked") == false){
            var fav_val = 1;
        }
            
        $.ajax({
            url: "<?php echo base_url() . 'products/like_unlike'; ?>",
            type: "post",
            data: "fav_val="+fav_val+"&product_id="+product_id,
            success: function (resp)
            {}
        });
   });
});
</script>