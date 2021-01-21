<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Settings</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong>Settings</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight col-sm-10 col-sm-offset-1">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Settings</h5>

                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form name="frm_settings" class="frm_settings form-horizontal" >
                        <div class="panel-body">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Currency (in symbol) (*) </b></span>
                                    <input type="text" name="currency" class="form-control" placeholder="£" >
                                </div>
                            </div>
			    
			    <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Currency (in code) (*) </b></span>
                                    <input type="text" name="currency_code" class="form-control" placeholder="GBP" >
                                </div>
                            </div>
			    
			    <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Country (*) </b></span>
                                    <input type="text" name="country" class="form-control" placeholder="GB" >
                                </div>
                            </div>
			    
			    <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Email address where admin wants all notification mails (*) </b></span>
                                    <input type="text" name="admin_email_address" class="form-control" placeholder="Email address where admin wants all notification mails" >
                                </div>
                            </div>
			    
			    <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Miles limit near user to search product (*) </b></span>
                                    <input type="text" name="mile_limit" class="form-control" placeholder="10" >
                                </div>
                            </div>
			    
			    <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Loyalty Points Needed to be a VIP Club member (*) </b></span>
                                    <input type="text" name="vip_loyalty_points" class="form-control" placeholder="20000" >
                                </div>
                            </div>
			    
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Maximum Discount Allowed (in %) (*) </b></span>
                                    <input type="text" name="max_discount" class="form-control" placeholder="20" >
                                </div>
                            </div>
			    
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Amount for Free Delivery (in £ ) (*) </b></span>
                                    <input type="text" name="amount_for_free_delivery" class="form-control" placeholder="50" >
                                </div>
                            </div>
			    
			    <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Janet-Collection Commission (in % ) (*) </b></span>
                                    <input type="text" name="Janet-Collection_commission" class="form-control" placeholder="25" >
                                </div>
                            </div>
			    
			    <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Seller Commission (in % ) (*) </b></span>
                                    <input type="text" name="seller_commission" class="form-control" placeholder="75" >
                                </div>
                            </div>
			    
			    <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Rush Hour Start Time </b></span>
                                    <input type="text" name="rush_hour_start_time" class="form-control stime_picker" placeholder="06:30">
                                </div>
                            </div>
			    
			    <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Rush Hour End Time </b></span>
                                    <input type="text" name="rush_hour_end_time" class="form-control etime_picker" placeholder="06:30">
                                </div>
                            </div>
			    
			    <div class="col-sm-6">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Delivery Charges (in £ ) (*) </b></span>
                                    <input type="text" name="delivery_charges" class="form-control" placeholder="1.50" >
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
<?php // echo "<pre>"; print_r($setting_data); exit; ?>

<?php
if (isset($setting_data) && $setting_data) {
    ?>
    <script>
        $(document).ready(function () {
            
           var formdata = {                
                'admin_email_address': '<?= (isset($setting_data[15]['value'])) ? $setting_data[15]['value'] : ''; ?>',
                'max_discount': '<?= (isset($setting_data[12]['value'])) ? $setting_data[12]['value'] : ''; ?>',
                'amount_for_free_delivery': '<?= (isset($setting_data[13]['value'])) ? $setting_data[13]['value'] : ''; ?>',
                'delivery_charges': '<?= (isset($setting_data[14]['value'])) ? $setting_data[14]['value'] : ''; ?>',
                'vip_loyalty_points': '<?= (isset($setting_data[16]['value'])) ? $setting_data[16]['value'] : ''; ?>',
		'currency': '<?= (isset($setting_data[20]['value'])) ? $setting_data[20]['value'] : ''; ?>',
		'currency_code': '<?= (isset($setting_data[28]['value'])) ? $setting_data[28]['value'] : ''; ?>',
		'mile_limit': '<?= (isset($setting_data[29]['value'])) ? $setting_data[29]['value'] : ''; ?>',
		'country': '<?= (isset($setting_data[30]['value'])) ? $setting_data[30]['value'] : ''; ?>',
		'driver_unique_code_bonus': '<?= (isset($setting_data[10]['value'])) ? $setting_data[10]['value'] : ''; ?>',
		'Janet-Collection_commission': '<?= (isset($setting_data[11]['value'])) ? $setting_data[11]['value'] : ''; ?>',
		'seller_commission': '<?= (isset($setting_data[9]['value'])) ? $setting_data[9]['value'] : ''; ?>',
		'rush_hour_start_time': '<?= (isset($setting_data[35]['value'])) ? $setting_data[35]['value'] : ''; ?>',
		'rush_hour_end_time': '<?= (isset($setting_data[36]['value'])) ? $setting_data[36]['value'] : ''; ?>'
            };
            $('[name="frm_settings"]').populate(formdata);

<?php } ?>
    });
</script>

<script>
    $(document).ready(function () {       
            
        $('.submit_btn').click(function (e) {

            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_settings"]');

            var admin_email_address = frm.find('[name="admin_email_address"]').val();
            if (!admin_email_address || !admin_email_address.trim()) {
                frm.find('[name="admin_email_address"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter email address where admin wants all notification mails'));
                valid = false;
            }
            
            var max_discount = frm.find('[name="max_discount"]').val();
            if (!max_discount || !max_discount.trim()) {
                frm.find('[name="max_discount"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter maximum discount allowed on order'));
                valid = false;
            }
            
            var amount_for_free_delivery = frm.find('[name="amount_for_free_delivery"]').val();
            if (!amount_for_free_delivery || !amount_for_free_delivery.trim()) {
                frm.find('[name="amount_for_free_delivery"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter amount over which delivery will be free'));
                valid = false;
            }
            
            var delivery_charges = frm.find('[name="delivery_charges"]').val();
            if (!delivery_charges || !delivery_charges.trim()) {
                frm.find('[name="delivery_charges"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter delivery charges'));
                valid = false;
            }
            
            var vip_loyalty_points = frm.find('[name="vip_loyalty_points"]').val();
            if (!vip_loyalty_points || !vip_loyalty_points.trim()) {
                frm.find('[name="vip_loyalty_points"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter loyalty points needed to be a VIP Club member'));
                valid = false;
            }


            if (valid) {
                $(this).html('Processing...');
                var datastring = $("form[name=\"frm_settings\"]").serialize();
                $.ajax({
                    url: 'setting/update_settings',
                    data: datastring,
                    type: 'post',
                    success: function (data) {
                        $('.submit_btn').html('Save Settings');
                        if (data == 'success') {
                            $('.success_msg').html('Your settings is successfully saved');
                        } 
                    }
                });
            }
        });

    });
</script>
