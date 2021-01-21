<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Payment Settings</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong>Payment Settings</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight col-sm-6 col-sm-offset-3">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Payment Settings</h5>

                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form name="frm_payment_settings" class="frm_payment_settings form-horizontal" >
                        <div class="panel-body">
			    <div class="col-sm-12">
				<div class="form-group">
				    <span class="help-block m-b-none"><b>Mode (*) </b></span>
				    <select name="payment_mode" class="form-control payment_mode">
					<option value="1">Sandbox / Test Mode</option>
					<option value="2">Live Mode</option>
				    </select>
				</div>
			    </div>
			    
			    <div class="col-sm-12 test_public_key1">
				<div class="form-group">
				    <span class="help-block m-b-none"><b>Public Key (*) </b></span>
				    <input type="text" class="form-control test_public_key" placeholder="Public Key" name="test_public_key">
				</div>
			    </div>

			    <div class="col-sm-12 test_secret_key1">
				<div class="form-group">
				    <span class="help-block m-b-none"><b>Secret Key (*) </b></span>
				    <input type="text" class="form-control test_secret_key" placeholder="Secret Key" name="test_secret_key">
				</div>
			    </div>
			    
                            <div class="col-sm-12 live_public_key1">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Secret Key </b></span>
                                    <input type="text" name="service_key" class="form-control" placeholder="Your Service Key" >
                                </div>
                            </div>
                            <div class="col-sm-12 live_secret_key1">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Public Key </b></span>
                                    <input type="text" name="client_key" class="form-control" placeholder="Your Client Key" >
                                </div>
                            </div>

                            <a class="btn submit_btn btn-primary pull-right">Save</a>
                            <span class="success_msg pull-right" style="color:green; padding: 7px;"></span>
                        </div>
                    </form>
                </div>            
            </div>
        </div>
    </div>
</div>
<?php //echo "<pre>"; print_r($setting_data); exit; ?>

<?php
if (isset($setting_data) && $setting_data) {
    ?>
    <script>
        $(document).ready(function () {
            
           var formdata = { 
	        'payment_mode': '<?= (isset($setting_data[31]['value'])) ? $setting_data[31]['value'] : '';  ?>',
                'test_public_key': '<?= (isset($setting_data[32]['value'])) ? $setting_data[32]['value'] : '';  ?>',
		'test_secret_key': '<?= (isset($setting_data[33]['value'])) ? $setting_data[33]['value'] : '';  ?>',
                'service_key': '<?= (isset($setting_data[17]['value'])) ? $setting_data[17]['value'] : ''; ?>',
                'client_key': '<?= (isset($setting_data[18]['value'])) ? $setting_data[18]['value'] : ''; ?>',
            };
            $('[name="frm_payment_settings"]').populate(formdata);

<?php } ?>
    });
</script>

<script>
    $(document).ready(function () {   
	
	if( $(".payment_mode").val() != "" ) {
	    if( $(".payment_mode").val() == "1" ) {
		$(".live_public_key1").hide();
		$(".live_secret_key1").hide();
		$(".test_public_key1").show();
		$(".test_secret_key1").show();
	    }
	    else if( $(".payment_mode").val() == "2" ) {
		$(".test_public_key1").hide();
		$(".test_secret_key1").hide();
		$(".live_public_key1").show();
		$(".live_secret_key1").show();
	    }
	}
	else {
	    $(".live_public_key1").hide();
	    $(".live_secret_key1").hide();
	}
	
	$(".payment_mode").on("change", function(){
	    if( $(this).val() == "1" ) {
		$(".live_public_key1").hide();
		$(".live_secret_key1").hide();
		$(".test_public_key1").show();
		$(".test_secret_key1").show();
	    }
	    else if( $(this).val() == "2" ) {
		$(".test_public_key1").hide();
		$(".test_secret_key1").hide();
		$(".live_public_key1").show();
		$(".live_secret_key1").show();
	    }
	});
            
        $('.submit_btn').click(function (e) {

            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_payment_settings"]');           

            if (valid) {
                $(this).html('Processing...');
                var datastring = $("form[name=\"frm_payment_settings\"]").serialize();
                $.ajax({
                    url: 'setting/update_settings',
                    data: datastring,
                    type: 'post',
                    success: function (data) {
                        $('.submit_btn').html('Save Payment Settings');
                        if (data == 'success') {
                            $('.success_msg').html('Your settings is successfully saved');
                        } 
                    }
                });
            }
        });

    });
</script>
