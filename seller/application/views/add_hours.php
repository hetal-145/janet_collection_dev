<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2> Add Trading Hours</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong> Add Trading Hours</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5> Add Trading Hours</h5>

                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
		
                <div class="ibox-content">
                    
                    <form name="trading_hour" class="trading_hour">
                        <div class="form-group">
                            <span class="help-block m-b-none"><b>Weekday(*)</b></span>
                            <select name="weekday" id="weekday" class="form-control">
				<option value="">--Select Weekday</option>
				<?php foreach($weekday as $key => $value) { ?>
				<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
				<?php } ?>
			    </select>
                        </div>
                        
                        <div class="form-group">
                            <span class="help-block m-b-none"><b>Start Time(*)</b></span>
                            <input type="text" class="form-control stime_picker start_time" placeholder="Start Time" name="start_time">
                        </div>
			
			<div class="form-group">
                            <span class="help-block m-b-none"><b>End Time(*)</b></span>
                            <input type="text" class="form-control etime_picker end_time" placeholder="End Time" name="end_time">
                        </div>
                        
                        <div class="form-group">
                            <a class="btn btn-primary save_btn"> Save</a>
                            <span class="success_msg" style="color:green; padding: 7px;"></span>
			    <span class="error_msg" style="color:red; padding: 7px;"></span>
                        </div>

                    </form> 
                </div>


            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.save_btn', function () {
         $('.success_msg').html('');
        $('.error_msg').html('');
        $('#msg').html('');
        var valid = true;
        var frm = $('form[name = "trading_hour"]');
        
        var weekday = frm.find('[name = "weekday"]').val();
        if (weekday == '') {
            frm.find('[name = "weekday"]').addClass('input_error').parents('.form-group').append(error_msg('Please select weekday'));
            valid = false;
        }
	
	var start_time = frm.find('[name = "start_time"]').val();
        if (start_time == '') {
            frm.find('[name = "start_time"]').addClass('input_error').parents('.form-group').append(error_msg('Please select start time of slot'));
            valid = false;
        }
	
	var end_time = frm.find('[name = "end_time"]').val();
        if (end_time == '') {
            frm.find('[name = "end_time"]').addClass('input_error').parents('.form-group').append(error_msg('Please select end time of slot'));
            valid = false;
        }

        if (valid == true) {
            var btn_txt = $('.save_btn').html();
            $('.save_btn').html('Saving...');
            
            $.ajax({
                url: 'trading_hours/save',
                data: frm.serialize(),
                type: 'post',
                success: function (data) {
                    if (data == 'success') {
                        window.location = "trading_hours";
                    }
		    else if (data == 'exist') {
                        $('.error_msg').html("Time slot already exists in this particular weekday.");
                    }
                }
            });
        } else {
            return false;
        }

    });
</script>
