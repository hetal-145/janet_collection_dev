<?php 
$CI =& get_instance();
//echo "<pre>";print_r($_POST); exit;
$category = $CI->m_tools->get_category(0);
$price_filter = $CI->m_tools->price_filter();
$get_abv_list = $CI->m_tools->get_abv_list();
$get_brand_list = $CI->m_tools->get_brand_list();
$country = $this->db->select('*')->where('name is not null or name != ""')->get('country')->result_array();
?>
<div class="modal right fade" id="modal_main_filter" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable">
	<div class="modal-content">
	    <div class="modal-header">
		<h5 class="modal-title" id="myModalLabel2">Filter</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	    </div>
	    <form id="filter_product" name="filter_product" action="<?php echo base_url() . 'products/filtered_product'; ?>" method="post">
		<div class="modal-body">
		    <div class="filter_menu">
			<div id="filter_accordion">
			    <div class="card">
				<div class="card-header">
				    <a class="card-link" data-toggle="collapse" href="#category">Category</a>
				</div>
				<div id="category" class="collapse show" data-parent="#filter_accordion">
				    <div class="card-body">
					<select class="form-control categories" name="category">
					    <option value="">Select category</option>
					    <?php foreach($category as $ct) { ?>
					    <option value="<?php echo $ct["category_id"]; ?>" <?php if(!empty($_POST["category"]) && $_POST["category"] == $ct["category_id"]) { ?> selected="selected" <?php } ?>><?php echo $ct["category_name"]; ?></option>
					    <?php } ?>
					</select>
				    </div>
				</div>
			    </div>

			    <div class="card">
				<div class="card-header">
				    <a class="collapsed card-link" data-toggle="collapse" href="#sub_category">Sub Category</a>
				</div>
				<div id="sub_category" class="collapse" data-parent="#filter_accordion">
				    <div class="card-body">
					<select class="form-control subcategories" name="subcategory">
					    <option value="">Select sub category</option>
					</select>
				    </div>
				</div>
			    </div>

			    <div class="card">
				<div class="card-header">
				    <a class="collapsed card-link" data-toggle="collapse" href="#brands">Brands</a>
				</div>
				<div id="brands" class="collapse" data-parent="#filter_accordion">
				    <div class="card-body brand_list">
					<?php foreach($get_brand_list as $b) { ?>
					    <div class="pretty p-icon p-round">					
						<input class="brands" type="checkbox" name="brand[]" value="<?php echo $b["brand_id"]; ?>" <?php if(!empty($_POST["brand"]) && $_POST["brand"] == $b["brand_id"]) { ?> checked="checked" <?php } ?> />
						<div class="state p-success">
						    <i class="icon mdi mdi-check"></i>
						    <label><?php echo $b["brand_name"]; ?></label>
						</div>					
					    </div>
					<?php } ?>

				    </div>
				</div>
			    </div>

			    <div class="card">
				<div class="card-header">
				    <a class="collapsed card-link" data-toggle="collapse" href="#price_country">Country</a>
				</div>
				<div id="price_country" class="collapse" data-parent="#filter_accordion">
				    <div class="card-body">
					<?php foreach($country as $c) { ?>
					    <div class="pretty p-icon p-round">
						<input type="radio" name="country" value="<?php echo $c["name"]; ?>" <?php if(!empty($_POST["country"]) && $_POST["country"] == $c["name"]) { ?> checked="checked" <?php } ?> />
						<div class="state p-success">
						    <i class="icon mdi mdi-check"></i>
						    <label><?php echo $c["name"]; ?></label>
						</div>
					    </div>
					<?php } ?>
				    </div>
				</div>
			    </div>

			    <div class="card">
				<div class="card-header">
				    <a class="collapsed card-link" data-toggle="collapse" href="#price_collapse">Price</a>
				</div>
				<div id="price_collapse" class="collapse" data-parent="#filter_accordion">
				    <div class="card-body">
					<div class="">
					    <input type="text" class="js-range-slider price_filter" name="price_filter" value="" />
					</div>
				    </div>
				</div>
			    </div>
			    <div class="card">
				<div class="card-header">
				    <a class="collapsed card-link" data-toggle="collapse" href="#volume">Volume</a>
				</div>
				<div id="volume" class="collapse" data-parent="#filter_accordion">
				    <div class="card-body">
					<div class="form-group mb-4">
					    <select class="form-control volume_type" name="volume_type">
						<option value="">Select volume type</option>
					    </select>
					</div>
					<div class="">
					    <input type="text" class="js-range-slider volume_filter" name="volume_filter" value="" />
					</div>
				    </div>
				</div>
			    </div>
			    <div class="card">
				<div class="card-header">
				    <a class="collapsed card-link" data-toggle="collapse" href="#abv_collapse">ABV</a>
				</div>
				<div id="abv_collapse" class="collapse" data-parent="#filter_accordion">
				    <div class="card-body">
					<div class="">
					    <input type="text" class="js-range-slider abv_filter" name="abv_filter" value="" />
					</div>
				    </div>
				</div>
			    </div>
			    
			    <div class="card">
				<div class="card-header">
				    <a class="collapsed card-link" data-toggle="collapse" href="#sort_by">Sort By</a>
				</div>
				<div id="sort_by" class="collapse" data-parent="#filter_accordion">
				    <div class="card-body">
					<select class="form-control sort_bys" name="sort_by">
					    <option value="">Sort By</option>
					    <option value="1" <?php if(!empty($_POST["sort_by"]) && $_POST["sort_by"] == 1) { ?> selected="selected" <?php } ?>>Cheapest</option>
					    
					    <option value="2" <?php if(!empty($_POST["sort_by"]) && $_POST["sort_by"] == 2) { ?> selected="selected" <?php } ?>>Fastest</option>
					    
					    <option value="3" <?php if(!empty($_POST["sort_by"]) && $_POST["sort_by"] == 3) { ?> selected="selected" <?php } ?>>Best Match</option>
					</select>
				    </div>
				</div>
			    </div>
			</div>
		    </div>
		</div>
		<div class="modal-footer">
		    <button type="button" class="btn btn-pink_squre" id="save_filter">Done</button>
		    <button type="button" class="btn btn-pink_squre" id="clear_filter">Clear All</button>
		</div>
	    </form>
	</div>
    </div>
</div>
<?php //print_r($price_filter); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.0/js/ion.rangeSlider.min.js"></script>
<script type="text/javascript">
//price filter
$(".js-range-slider.price_filter").ionRangeSlider({
    type: "double",
    grid: false,
    min: <?php echo $price_filter["min_amount"]; ?>,
    max: <?php echo $price_filter["max_amount"]; ?>,
    from: <?php echo $price_filter["min_amount"]; ?>,
    to: <?php echo $price_filter["max_amount"]; ?>,
    skin: "round",
    step: 1,
    prefix: '<?php echo CURRENCY_CODE;?>'
});
var d1_instance = $(".js-range-slider.price_filter").data("ionRangeSlider");

//abv filter
$(".js-range-slider.abv_filter").ionRangeSlider({
    type: "double",
    grid: false,
    min: <?php echo $get_abv_list["min_abv_percent"]; ?>,
    max: <?php echo $get_abv_list["max_abv_percent"]; ?>,
    from: <?php echo $get_abv_list["min_abv_percent"]; ?>,
    to: <?php echo $get_abv_list["max_abv_percent"]; ?>,
    skin: "round",
    suffix: '%',
    step: 0.1
});
var d2_instance = $(".js-range-slider.abv_filter").data("ionRangeSlider");

//volume filter
$(".js-range-slider.volume_filter").ionRangeSlider({
    type: "double",
    grid: false,
    min: 0,
    max: 0,
    from: 0,
    to: 0,
    skin: "round",
    step: 1
});
var d5_instance = $(".js-range-slider.volume_filter").data("ionRangeSlider");
    
$(document).ready(function (){  
    
    <?php 
    if(!empty($POST["price_filter"])) { 
	$price = explode(";", $_POST["price_filter"]);
    ?>
	d1_instance.update({
	    min: <?php echo $price[0]; ?>,
	    max: <?php echo $price[1]; ?>,
	    from: <?php echo $price[0]; ?>,
	    to: <?php echo $price[1]; ?>,
	    step: 1,
	    prefix: '<?php echo CURRENCY_CODE;?>'
	});  
    <?php } ?>
    
    <?php 
    if(!empty($POST["volume_filter"])) { 
	$volume = explode(";", $_POST["volume_filter"]);
    ?>
	d5_instance.update({
	    min: <?php echo $volume[0]; ?>,
	    max: <?php echo $volume[1]; ?>,
	    from: <?php echo $volume[0]; ?>,
	    to: <?php echo $volume[1]; ?>,
	    step: 1,
	});  
    <?php } ?>
    
    <?php 
    if(!empty($POST["abv_filter"])) { 
	$abv = explode(";", $_POST["abv_filter"]);
    ?>
	d2_instance.update({
	    min: <?php echo $abv[0]; ?>,
	    max: <?php echo $abv[1]; ?>,
	    from: <?php echo $abv[0]; ?>,
	    to: <?php echo $abv[1]; ?>,
	    suffix: '%',
	    step: 0.1
	}); 
    <?php } ?>
    
    $("#save_filter").click(function(){
	$("#filter_product").submit();
//	var frm = $(this).closest($('form[name = "filter_product"]'));
//	
//	$(this).attr("disabled", "disabled");
//	$(this).html('Processing...');
//	var data = new FormData(frm[0]);
//	$.ajax({
//	    url: "<?php //echo base_url() . 'products/filtered_product'; ?>",
//	    type: "post",
//	    data: data,
//	    contentType: false,
//	    cache: false,
//	    processData: false,
//	    success: function (resp)
//	    {
//		console.log(resp);
//	    }
//	});
    });
    
    $(".categories").change(function(){
	var cat = $(this).val();
	
	//subcategory
	$.ajax({
	    url: "<?php echo base_url() . 'home/get_subcategory'; ?>",
	    type: "post",
	    data: "category="+cat,
	    success: function (resp)
	    {
		//console.log(resp);
		$(".subcategories").html('');
		var response = $.parseJSON(resp);
		$(".subcategories").append('<option value="">Select subcategory</option>');
		$.each(response, function(index, value){
		    $(".subcategories").append('<option value="'+value.category_id+'">'+value.category_name+'</option>');
		});
	    }
	});
	
	//brands
	$.ajax({
	    url: "<?php echo base_url() . 'home/get_brand'; ?>",
	    type: "post",
	    data: "category="+cat,
	    success: function (resp1)
	    {
		//console.log(resp);
		$(".brand_list").html('');
		var response1 = $.parseJSON(resp1);
		$.each(response1, function(index1, value1){
		    $(".brand_list").append('<div class="pretty p-icon p-round"><input class="brands" type="checkbox" name="brand[]" value="'+value1.brand_id+'" /><div class="state p-success"><i class="icon mdi mdi-check"></i><label>'+value1.brand_name+'</label></div></div>');
		});
	    }
	});
	
	//volume
	$.ajax({
	    url: "<?php echo base_url() . 'home/get_volume_type'; ?>",
	    type: "post",
	    data: "category="+cat,
	    success: function (resp3)
	    {
		//console.log(resp);
		$(".volume_type").html('');
		var response3 = $.parseJSON(resp3);
		$(".volume_type").append('<option value="">Select Volume type</option>');
		
		$.each(response3, function(index3, value3){
		    $(".volume_type").append('<option value="'+value3.type+'">'+value3.volume_type+'</option>');
		});
		
		$(".volume_type").change(function(){
		    var cat = $(".categories").val();
		    var type = $(this).val();

		    //volume
		    $.ajax({
			url: "<?php echo base_url() . 'home/get_volume'; ?>",
			type: "post",
			data: "category="+cat+"&volume_type="+type,
			success: function (resp)
			{
			    console.log(resp);
			    var response = $.parseJSON(resp);
			    //volume filter
			    d5_instance.update({
				min: response.min_volume,
				max: response.max_volume,
				from: response.min_volume,
				to: response.max_volume,
				step: 1
			    });
			}
		    });
		});
	    }
	});
    });
    
    $(".subcategories").change(function(){
	var cat = $(".categories").val();
	var subcat = $(this).val();
		
	//brands
	$.ajax({
	    url: "<?php echo base_url() . 'home/get_brand'; ?>",
	    type: "post",
	    data: "category="+cat+"&subcategory="+subcat,
	    success: function (resp1)
	    {
		//console.log(resp);
		$(".brand_list").html('');
		var response1 = $.parseJSON(resp1);
		$.each(response1, function(index1, value1){
		    $(".brand_list").append('<div class="pretty p-icon p-round"><input class="brands" type="checkbox" name="brand[]" value="'+value1.brand_id+'" /><div class="state p-success"><i class="icon mdi mdi-check"></i><label>'+value1.brand_name+'</label></div></div>');
		});
	    }
	});
	
	//volume
	$.ajax({
	    url: "<?php echo base_url() . 'home/get_volume_type'; ?>",
	    type: "post",
	    data: "category="+cat+"&subcategory="+subcat,
	    success: function (resp3)
	    {
		//console.log(resp);
		$(".volume_type").html('');
		var response3 = $.parseJSON(resp3);
		$(".volume_type").append('<option value="">Select Volume type</option>');
		
		$.each(response3, function(index3, value3){
		    $(".volume_type").append('<option value="'+value3.type+'">'+value3.volume_type+'</option>');
		});
		
		$(".volume_type").change(function(){
		    var cat = $(".categories").val();
		    var subcat = $(".subcategories").val();
		    var type = $(this).val();

		    //volume
		    $.ajax({
			url: "<?php echo base_url() . 'home/get_volume'; ?>",
			type: "post",
			data: "category="+cat+"&subcategory="+subcat+"&volume_type="+type,
			success: function (resp3)
			{
			    //console.log(resp);
			    var response3 = $.parseJSON(resp3);
			    //volume filter
			    d5_instance.update({
				min: response3.min_volume,
				max: response3.max_volume,
				from: response3.min_volume,
				to: response3.max_volume,
				step: 1
			    });
			}
		    });
		});
	    }
	});
    });
    
    $(document).on("click", ".brands", function(){
	//alert($(this).val());
	var brands = [];
	$('.brands:checked').each(function(i){
	    brands[i] = $(this).val();
        });    
	//alert(brands);
	
	//volume
	$.ajax({
	    url: "<?php echo base_url() . 'home/get_volume_type'; ?>",
	    type: "post",
	    data: "brands="+brands,
	    success: function (resp3)
	    {
		console.log(resp3);
		$(".volume_type").html('');
		var response3 = $.parseJSON(resp3);
		$(".volume_type").append('<option value="">Select Volume type</option>');
		
		$.each(response3, function(index3, value3){
		    $(".volume_type").append('<option value="'+value3.type+'">'+value3.volume_type+'</option>');
		});
		
		$(".volume_type").change(function(){
		    var cat = $(".categories").val();
		    var subcat = $(".subcategories").val();
		    var brands = brands;
		    var type = $(this).val();

		    //volume
		    $.ajax({
			url: "<?php echo base_url() . 'home/get_volume'; ?>",
			type: "post",
			data: "category="+cat+"&subcategory="+subcat+"&volume_type="+type+"&brands="+brands,
			success: function (resp3)
			{
			    //console.log(resp);
			    var response3 = $.parseJSON(resp3);
			    //volume filter
			    d5_instance.update({
				min: response3.min_volume,
				max: response3.max_volume,
				from: response3.min_volume,
				to: response3.max_volume,
				step: 1
			    });
			}
		    });
		});
	    }
	});
    });
    
    $("#clear_filter").click(function() {
	$('select').prop('selectedIndex',0);
	
	$(".subcategories").html('');
	$(".subcategories").append('<option value="">Select subcategory</option>');
	
	$(".volume_type").html('');
	$(".volume_type").append('<option value="">Select Volume type</option>');
	
	$('input:checkbox').removeAttr('checked');
	$('input:radio').removeAttr('checked');
	
	d5_instance.update({
	    min: 0,
	    max: 0,
	    from: 0,
	    to: 0,
	    step: 1
	});
	
	d2_instance.update({
	    min: <?php echo $get_abv_list["min_abv_percent"]; ?>,
	    max: <?php echo $get_abv_list["max_abv_percent"]; ?>,
	    from: <?php echo $get_abv_list["min_abv_percent"]; ?>,
	    to: <?php echo $get_abv_list["max_abv_percent"]; ?>,
	    suffix: '%',
	    step: 0.1
	});
	
	d1_instance.update({
	    min: <?php echo $price_filter["min_amount"]; ?>,
	    max: <?php echo $price_filter["max_amount"]; ?>,
	    from: <?php echo $price_filter["min_amount"]; ?>,
	    to: <?php echo $price_filter["max_amount"]; ?>,
	    step: 1,
	    prefix: '<?php echo CURRENCY_CODE;?>'
	});
    });
    
    if($(".categories").val() != "") {
	var cat = $(".categories").val();
	
	//subcategory
	$.ajax({
	    url: "<?php echo base_url() . 'home/get_subcategory'; ?>",
	    type: "post",
	    data: "category="+cat,
	    success: function (resp)
	    {
		//console.log(resp);
		$(".subcategories").html('');
		var response = $.parseJSON(resp);
		$(".subcategories").append('<option value="">Select subcategory</option>');
		$.each(response, function(index, value){
		    var selectd = value.category_id == '<?php if(!empty($_POST["subcategory"])) { echo $_POST["subcategory"]; } else { echo ''; } ?>' ? 'selected="selected"' : '';
		    $(".subcategories").append('<option value="'+value.category_id+'" '+ selectd +'>'+value.category_name+'</option>');
		});
	    }
	});
	
	//brands
	$.ajax({
	    url: "<?php echo base_url() . 'home/get_brand'; ?>",
	    type: "post",
	    data: "category="+cat,
	    success: function (resp1)
	    {
		//console.log(resp);
		$(".brand_list").html('');
		var response1 = $.parseJSON(resp1);
		$.each(response1, function(index1, value1){
		    $(".brand_list").append('<div class="pretty p-icon p-round"><input class="brands" type="checkbox" name="brand[]" value="'+value1.brand_id+'" /><div class="state p-success"><i class="icon mdi mdi-check"></i><label>'+value1.brand_name+'</label></div></div>');
		});
	    }
	});
	
	//volume
	$.ajax({
	    url: "<?php echo base_url() . 'home/get_volume_type'; ?>",
	    type: "post",
	    data: "category="+cat,
	    success: function (resp3)
	    {
		//console.log(resp);
		$(".volume_type").html('');
		var response3 = $.parseJSON(resp3);
		$(".volume_type").append('<option value="">Select Volume type</option>');
		
		$.each(response3, function(index3, value3){
		    var selectd2 = value3.type == '<?php if(!empty($_POST["volume_type"])) { echo $_POST["volume_type"]; } else { echo ''; } ?>' ? 'selected="selected"' : '';
		    $(".volume_type").append('<option value="'+value3.type+'" '+selectd2+'>'+value3.volume_type+'</option>');
		});
		
		$(".volume_type").change(function(){
		    var cat = $(".categories").val();
		    var type = $(this).val();

		    //volume
		    $.ajax({
			url: "<?php echo base_url() . 'home/get_volume'; ?>",
			type: "post",
			data: "category="+cat+"&volume_type="+type,
			success: function (resp)
			{
			    console.log(resp);
			    var response = $.parseJSON(resp);
			    //volume filter
			    d5_instance.update({
				min: response.min_volume,
				max: response.max_volume,
				from: response.min_volume,
				to: response.max_volume,
				step: 1
			    });
			}
		    });
		});
	    }
	});
    }
    
    
});
</script>