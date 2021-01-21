
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Manage Ratings & Reviews</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Ratings & Reviews</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                
                <div class="ibox-title">
                    <h5>Manage Ratings & Reviews</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    
                    <?php echo $content; ?>
                </div>
            
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        
        $(".slider_img_block").hide();
        $(".sub_category_id_div").hide();
       
        $(".category_id").multipleSelect({
            filter: true,
        });
        
        $(".sub_category_id").multipleSelect({
            filter: true,
        });        
        
        $("a.add_brand").click(function(){            
            $(".brand_name").val('');
            $(".volumne_value").val('');
            $('.action').val('add'); 
            $(".volume_hide").show();       
            $(".type").prop('selectedIndex',0);
            $('.ms-parent.category_id .ms-drop > ul').find('li.selected').removeClass('selected');
            $('.ms-parent.sub_category_id .ms-drop > ul').find('li.selected').removeClass('selected');
            $("#slider_img_view").text('');
            $("#brand_logo_view").text('');
            $(".brand_id").val('');          
            $('select.category_id').multipleSelect('uncheckAll', true);
            $('select.sub_category_id').multipleSelect('uncheckAll', true);
            $(".sub_category_id_div").hide();
            $( ".is_top_brand_checkbox" ).attr( 'checked', false );
            $(".slider_img_block").hide();
        });
        
        $(".is_top_brand_checkbox").click(function (e) {            
            if($(this).is(":checked")) {
                $(".slider_img_block").show();
            } 
            else {
                $(".slider_img_block").hide();
            } 
        });
        
        $(".is_top_brand_checkbox_view").click(function (e) {
            
            var brand_id; var tb_status;
            
            if($(this).is(":checked")) {
                $(".slider_img_block").show();
                brand_id = $(this).data('brand_id');
                tb_status = 1;
            } 
            else {
                $(".slider_img_block").hide();
                brand_id = $(this).data('brand_id');
                tb_status = 0;
            } 
            
            $.ajax({
                url: 'brand/top_brands',
                data: 'brand_id=' + brand_id + '&tb_status=' + tb_status,
                type: 'post',
                success: function () {}
            });
        });
        
        //get sub category
        $('.category_id').on('click.multile.select', function (e, arg1) {
            var selected = [];
            $('.category_id > .ms-drop > ul').find('li.selected label input[type=checkbox]').each(function(i){
                selected.push($(this).val());
            });
            //console.log(selected);
            
            $.ajax({
                url: "brand/get_subcategory",
                type: "post",
                data: 'category_id=' + selected,
                success: function (subcategory)
                {   
                    //console.log(subcategory);
                    if(subcategory != ''){
                        $(".sub_category_id_div").show();
                       // $(".sub_cat").show();
                        if(subcategory) {
                            var subcategory_list = $.parseJSON(subcategory);
                            //console.log(subcategory_list);
                            $.each(subcategory_list, function(index, value){
                                $(".sub_category_id").append('<option value="'+value.category_id+'">'+value.category_name+'</option>');
                            });
                            
                            $(".sub_category_id").multipleSelect({
                                filter: true
                            });
                        }
                    }
                }
            });
        });
              
        //Add Data
        $('.submit_btn').click(function (e) {
            
            var selected = [];
            $('.sub_category_id .ms-drop > ul').find('li.selected label input[type=checkbox]').each(function(i){
                selected.push($(this).val());
            });
            
            var selected1 = [];
            $('.category_id .ms-drop > ul').find('li.selected label input[type=checkbox]').each(function(i){
                selected1.push($(this).val());
            });
            
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_add_brand"]');
            
            //Brand
            var brand_name = frm.find('[name = "brand_name"]').val();
            if (!brand_name || !brand_name.trim()) {
                frm.find('[name = "brand_name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter brand name'));
                valid = false;
            } 
            
            if($('.action').val() == 'add') {
                //Volume
                var volumne_value = frm.find('[name = "volumne_value"]').val();
                if (!volumne_value || !volumne_value.trim()) {
                    frm.find('[name = "volumne_value"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter volumne value'));
                    valid = false;
                } 

                //Volume Type
                var type = frm.find('[name = "type"]').val();
                if (!type || !type.trim()) {
                    frm.find('[name = "type"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter volumne type'));
                    valid = false;
                } 
            }

            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);                
                data.append('sub_category_id', selected);
                data.append('category_id', selected1);
                //console.log(data);
                $.ajax({
                    url: "brand/save",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function (resp)
                    {
                        //console.log(resp);
                        if (resp === 'exist') {
                            frm.find('[name = "brand_name"]').addClass('input_error').parents('.form-group').append(error_msg('Brand already exists.'));
                            valid = false;
                            $('.submit_btn').html('Save');
                        }else if (resp === 'success') {
                            if($("input[name='brand_id']").val() != ''){
                                $('.mdl_brand').modal('toggle');
                                $('.submit_btn').html('Save');
                                Xcrud.reload();
                            }
                            else {
                                window.location = "brand";
                            }
                        }
                    }

                });
            }
        });
        
        $(document).on('click', '.edit_data', function () {  
            
            $('.ms-parent.category_id .ms-drop > ul').find('li.selected').removeClass('selected');
            $('.ms-parent.sub_category_id .ms-drop > ul').find('li.selected').removeClass('selected');
            $('select.category_id').multipleSelect('uncheckAll', true);
            $('select.sub_category_id').multipleSelect('uncheckAll', true);
            $(".sub_category_id_div").hide();
            $("button.ms-choice span").text('');
            $(".volume_hide").hide();                        
            $('.action').val('edit'); 
            
            var brand_id = $(this).attr('data-primary');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            
             $.ajax({
                url: 'brand/get_brand',
                data: 'brand_id=' + brand_id,
                type: 'post',
                success: function (brand) {
                    if (brand) {        
                        brand = JSON.parse(brand);
                        //console.log(brand);
                        
                        $('#brand_logo_view').html(brand.brand_logo);
                        $('#brand_logo_view').attr('href', '../upload/brand/'+brand.brand_logo);
                        
                        $('#slider_img_view').html(brand.slider_img);
                        $('#slider_img_view').attr('href', '../upload/brand/'+brand.slider_img);
                        
                        if(brand.is_top_brand == '1'){
                            $( ".is_top_brand_checkbox" ).attr( 'checked', 'checked' );
                            $(".slider_img_block").show();
                        } else if(brand.is_top_brand == '0') {
                            $( ".is_top_brand_checkbox" ).attr( 'checked', false );
                            $(".slider_img_block").hide();
                        }
                        
                        delete(brand.brand_logo);
                        delete(brand.slider_img);
                        $('[name="frm_add_brand"]').populate(brand);
                        
                        if(brand.sub_category.length > 0) {
                            $(".sub_category_id_div").show();
                            
                            //sub category
                            $.each(brand.sub_category_list, function(index1, subcategory_list){   
                                $.each(subcategory_list, function(index, value){
                                    $('select.sub_category_id').append('<option value="'+value.category_id+'">'+value.category_name+'</option>').multipleSelect('refresh', true);
                                });                                
                            });
                           // $('select.sub_category_id').multipleSelect('refresh', true);
                            
                            $.each(brand.category, function(index2, value2){ 
                                //alert(value2.category_id);
                                $('.category_id .ms-drop > ul').find('li label input[value="' + value2.category_id + '"]').prop('checked',true);
                                $('.ms-parent.category_id').find('button.ms-choice span').append( $('.category_id .ms-drop > ul').find('li label input[value="' + value2.category_id + '"]').parent().find('span').text() + ',');
                                $('.category_id .ms-drop > ul').find('li label input[value="' + value2.category_id + '"]').parent().parent().addClass('selected');                                 
                            });
                            
                            $.each(brand.sub_category, function(index3, value3){
                               // alert(value3.category_id);
                                $('.sub_category_id .ms-drop > ul').find('li label input[value="' + value3.category_id + '"]').prop('checked',true);
                                $('.ms-parent.sub_category_id').find('button.ms-choice span').append( $('.sub_category_id .ms-drop > ul').find('li label input[value="' + value3.category_id + '"]').parent().find('span').text() + ',');
                                $('.sub_category_id .ms-drop > ul').find('li label input[value="' + value3.category_id + '"]').parent().parent().addClass('selected');                                 
                            });
                        }
                        else {
                            $(".sub_category_id_div").hide();
                            $.each(brand.category, function(index, value){ 
                                $('.category_id .ms-drop > ul').find('li label input[value="' + value.category_id + '"]').prop('checked',true);
                                $('.ms-parent.category_id').find('button.ms-choice span').append( $('.category_id .ms-drop > ul').find('li label input[value="' + value.category_id + '"]').parent().find('span').text() + ',');
                                $('.category_id .ms-drop > ul').find('li label input[value="' + value.category_id + '"]').parent().parent().addClass('selected');                                 
                            });
                        }
                    }
                }
             });
        });
        
    });    
</script>

