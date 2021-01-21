<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Website</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Dashboard</a>
            </li>
            <li class="active">
                <strong>Homepage</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight col-sm-10 col-sm-offset-1">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Website -> Homepage</h5>

                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form name="frm_homepage" class="frm_homepage form-horizontal" method="post" action="#">
			<h4>Banner Section</h4><hr>
                        <div class="panel-body">
			    <div class="row">
				
				<div class="col-sm-12" id="show_video">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Banner Background Video </b></span>
					<input type="file" name="home_banner_video" class="form-control" />
					<?php if(!empty($setting_data[37]['value'])) { ?>
					<br><a href="<?php echo $setting_data[37]['value']; ?>" target="_blank">Banner Video</a>
					<?php } ?>
				    </div>
				</div>
				
				<div class="col-sm-12" id="show_image">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Banner Background Video Image </b></span>
					<input type="file" name="home_banner_image" class="form-control" />
					<?php if(!empty($setting_data[38]['value'])) { ?>
					<br><a href="<?php echo $setting_data[38]['value']; ?>" target="_blank">Banner Image</a>
					<?php } ?>
				    </div>
				</div>
			    </div>   
			</div>
			
			<h4>Statistics Section</h4><hr>
                        <div class="panel-body">
			    <div class="row">
				<div class="col-sm-12">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Minute Delivery</b></span>
					<div class="form-group">
					    <input type="text" name="statistics_digit_1" class="form-control numeric" placeholder="60" value="<?php echo $setting_data[39]['value']; ?>" />
					</div>
				    </div>
				</div>

				<div class="col-sm-12">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Brands</b></span>
					<div class="form-group">
					    <input type="text" name="statistics_digit_2" class="form-control numeric" placeholder="150" value="<?php echo $setting_data[40]['value']; ?>" />
					</div>
				    </div>
				</div>
				
				<div class="col-sm-12">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Pound Minimum Order</b></span>
					<div class="form-group">
					    <input type="text" name="statistics_digit_3" class="form-control numeric" placeholder="10" value="<?php echo $setting_data[41]['value']; ?>" />
					</div>
				    </div>
				</div>
			    </div>   
			</div>
			
			<div class="panel-body">
			    <div class="row">                            
				<a class="btn submit_btn btn-primary pull-right">Save</a>
				<span class="success_msg pull-right" style="color:green; padding: 7px;"></span>
			    </div>
			</div>
                    </form>
                </div>            
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {  
        $('.submit_btn').click(function (e) {

            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_homepage"]');

            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);
                $.ajax({
                    url: 'websetting/save_home_content',
		    type: 'post',
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,                    
                    success: function (data) {
                        $('.submit_btn').html('Save');
                        if (data == 'success') {
                            $('.success_msg').html('Your content for home page is successfully saved');
                        } 
                    }
                });
            }
        });

    });
</script>
