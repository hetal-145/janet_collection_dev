<?php // echo "<pre>"; print_r($products); exit;  ?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2> Edit Prices</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong> Edit Prices</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4">
	<button type="button" style="margin-top: 30px;" class="btn btn-info" id="back" onclick="history.go(-1);"><- Go Back</button>
	&nbsp;&nbsp;&nbsp;
	<button type="button" style="margin-top: 30px;" class="btn btn-warning" id="edit_all">Update All Product Prices</button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5> Edit Prices</h5>
                </div>

                <div class="ibox-content">                    
		    <?php if(!empty($products)) { foreach($products as $key => $value) { ?>
		    <div class="row">
			<div class="col-sm-12">
			    <form name="update_product_price_<?php echo $value["product_detail_id"]; ?>" class="update_product_price_<?php echo $value["product_detail_id"]; ?>" action="#" method="post">
				<div class="row samevoldiv">				
				    <div class="form-group col-md-4">
					<span class="help-block m-b-none"><b>Product Name</b></span>
					<input type="text" class="form-control product_name" name="product_name" value="<?php echo $value["product_name"]; ?>" disabled="disabled">
					<input type="hidden" class="form-control product_detail_id" name="product_detail_id" value="<?php echo $value["product_detail_id"]; ?>" readonly="readonly">
				    </div>

				    <div class="form-group col-md-2">
					<span class="help-block m-b-none"><b>Volume</b></span>
					<input type="text" class="form-control volumne_value" name="volumne_value" value="<?php echo $value["volumne_value"]; ?>" disabled="disabled">
				    </div>

				    <div class="form-group col-md-2">
					<span class="help-block m-b-none"><b>Actual Price (MRP)</b></span>
					<input type="text" class="form-control actual_price" name="actual_price" value="<?php echo $value["actual_price"]; ?>">
				    </div>
				    
				    <div class="form-group col-md-2">
					<span class="help-block m-b-none"><b>Discount</b></span>
					<input type="text" class="form-control normal_discount" name="normal_discount" value="<?php echo $value["normal_discount"]; ?>">
				    </div>
				    
				    <div class="form-group col-md-2">
					<span class="help-block m-b-none"><b>Sell Price</b></span>
					<input type="text" class="form-control normal_sell_price" name="normal_sell_price" value="<?php echo $value["normal_sell_price"]; ?>" readonly="readonly">
				    </div>
				    
				    <div class="form-group col-md-2">
					<span class="help-block m-b-none"><b>Loyality Club Discount</b></span>
					<input type="text" class="form-control loyalty_club_discount" name="loyalty_club_discount" value="<?php echo $value["loyalty_club_discount"]; ?>">
				    </div>
				    
				    <div class="form-group col-md-2">
					<span class="help-block m-b-none"><b>Loyality Club Sell Price</b></span>
					<input type="text" class="form-control loyalty_club_sell_price" name="loyalty_club_sell_price" value="<?php echo $value["loyalty_club_sell_price"]; ?>" readonly="readonly">
				    </div>
				    
				    <div class="form-group col-md-2">
					<span class="help-block m-b-none"><b>VIP Club Price</b></span>
					<input type="text" class="form-control vip_club_discount" name="vip_club_discount" value="<?php echo $value["vip_club_discount"]; ?>">
				    </div>
				    
				    <div class="form-group col-md-2">
					<span class="help-block m-b-none"><b>VIP Club Sell Price</b></span>
					<input type="text" class="form-control vip_club_sell_price" name="vip_club_sell_price" value="<?php echo $value["vip_club_sell_price"]; ?>" readonly="readonly">
				    </div>

				    <div class="form-group col-md-6">
					<a class="btn btn-primary save_btn" data-id="<?php echo $value["product_detail_id"]; ?>"> Save Single Product</a>
				    </div>
				</div>
			    </form> 
			</div>
		    </div>
		    <?php } } else { ?>
			<div class="form-group">
                            <span class="help-block m-b-none"><b>No Products</b></span>
                        </div>
		    <?php } ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(e){
	//discount column changes
	$(".normal_discount").on("input",function(){ 	    
	    var normal_discount = $(this).val();
	    var actual_price = $(this).closest('div.samevoldiv').find('.actual_price').val();
	    var normal_sell_price = parseFloat(actual_price) - ((parseFloat(actual_price) * parseFloat(normal_discount)) / 100);
	    $(this).closest('div.samevoldiv').find('.normal_sell_price').val(normal_sell_price);	    
	});
	
	$(".loyalty_club_discount").on("input",function(){ 	    
	    var normal_discount = $(this).val();
	    var actual_price = $(this).closest('div.samevoldiv').find('.actual_price').val();
	    var normal_sell_price = parseFloat(actual_price) - ((parseFloat(actual_price) * parseFloat(normal_discount)) / 100);
	    $(this).closest('div.samevoldiv').find('.loyalty_club_sell_price').val(normal_sell_price);	    
	});
	
	$(".vip_club_discount").on("input",function(){ 	    
	    var normal_discount = $(this).val();
	    var actual_price = $(this).closest('div.samevoldiv').find('.actual_price').val();
	    var normal_sell_price = parseFloat(actual_price) - ((parseFloat(actual_price) * parseFloat(normal_discount)) / 100);
	    $(this).closest('div.samevoldiv').find('.vip_club_sell_price').val(normal_sell_price);	    
	});
	
	//actual price column changes
	$(".actual_price").on("input",function(){ 	    
	    var actual_price = $(this).val();
	    //normal price
	    var normal_discount = $(this).closest('div.samevoldiv').find('.normal_discount').val();
	    var normal_sell_price = parseFloat(actual_price) - ((parseFloat(actual_price) * parseFloat(normal_discount)) / 100);
	    $(this).closest('div.samevoldiv').find('.normal_sell_price').val(normal_sell_price);
	    
	    //loyalty club price
	    var loyalty_club_discount = $(this).closest('div.samevoldiv').find('.loyalty_club_discount').val();
	    var loyalty_club_sell_price = parseFloat(actual_price) - ((parseFloat(actual_price) * parseFloat(loyalty_club_discount)) / 100);
	    $(this).closest('div.samevoldiv').find('.loyalty_club_sell_price').val(loyalty_club_sell_price);
	    
	    //vip club price
	    var vip_club_discount = $(this).closest('div.samevoldiv').find('.vip_club_discount').val();
	    var vip_club_sell_price = parseFloat(actual_price) - ((parseFloat(actual_price) * parseFloat(vip_club_discount)) / 100);
	    $(this).closest('div.samevoldiv').find('.vip_club_sell_price').val(vip_club_sell_price);
	});
	
	$('#edit_all').on('click', function () {
	    var valid = true;     
	    var frm = $('form');
	    
	    var data = new FormData();
	    if (valid == true) {
		$(this).html('Processing...');
		
		$.each(frm, function(index, value){
		    data.append(index, $(frm[index]).serialize());		   
		});
		
		$.ajax({
		    url: "product/update_price_all",
		    type: "post",
		    data: data,
		    contentType: false,
		    cache: false,
		    processData:false,
		    success: function (resp)
		    {
//			console.log(resp);
			if(resp == 1) {
			    alert('Product Price Updated.');
			    $('.save_btn').html('Save Single Product');
			    window.location.reload();
			}			
		    }

		});
	    } else {
		return false;
	    }
	});
	
	$('.save_btn').on('click', function () {
	    var product_detail_id = $(this).data("id");
	    
	    var valid = true;        
	    var frm = $(this).parents().find('form[name = "update_product_price_'+product_detail_id+'"]');
//	    console.log(frm); return false;

	    if (valid == true) {
		$(this).html('Processing...');
		var data = new FormData(frm[0]);
		$.ajax({
		    url: "product/update_price",
		    type: "post",
		    data: data,
		    contentType: false,
		    cache: false,
		    processData:false,
		    success: function (resp)
		    {
//			console.log(resp);
			if(resp == 1) {
			    alert('Product Price Updated.');
			    $('.save_btn').html('Save Single Product');
			    window.location.reload();
			}			
		    }

		});
	    } else {
		return false;
	    }
	});
    });
</script>
