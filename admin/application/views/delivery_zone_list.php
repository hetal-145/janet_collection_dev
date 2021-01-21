<?php //echo "<pre>"; print_r($delivery_zones); exit; ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Delivery Zone</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Delivery Zone</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">

                
                
                <div class="ibox-title">
                    <h5>Manage Delivery Zone</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">                                        
                    <span class="clearfix"></span>
                    <?php echo $content; ?>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        
        //Select All
        var clicked = false;
        $("a.select_all").click(function(e) {
            e.preventDefault();           
            $(".zipcode_id_checkbox").prop("checked", !clicked);
            clicked = !clicked;
        });
        
        //delete all
        $("a.delete_all").click(function(e) {
            e.preventDefault();
            var zipcode_box = [];
            $.each($("input[name='zipcode_id']:checked"), function(){  
                zipcode_box.push($(this).data('zipcode_id'));
            }); 
            
            //console.log(zipcode_box);
            
            $.ajax({
                url: "zipcode/delete_all",
                type: "post",
                data: {'zipcode':zipcode_box},
                success: function (resp)
                { 
                    console.log(resp);
                    if (resp == 'success') {
                        alert('Zipcode Deleted Successfully.');
                        window.location.reload();
                    }
                }

            });
        });
        
        //Upload xls
        $('.upload_importxl').click(function (e) {
            e.preventDefault();
            
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_import_xls"]');
            
            $(this).html('Processing...');
            var data = new FormData(frm[0]);
            //console.log(data);
            $.ajax({
                url: "zipcode/import_xls",
                type: "post",
                data: data,
                contentType: false,
                cache: false,
                processData:false,
                success: function (resp)
                { 
                    console.log(resp);
                    if (resp == 'success') {
                        alert('Zipcode Uploaded Successfully.');
                        $('.upload_importxl').html('Save');
                        window.location.reload();
                    }
                }

            });
           
        });
        
    });
</script>