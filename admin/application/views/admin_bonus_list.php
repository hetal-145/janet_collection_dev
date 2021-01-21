
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-9">
        <h2>Manage Admin Bonus To Driver List</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Admin Bonus To Driver List</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-3">
	<a href="#" style="margin-top: 30px;" class="btn btn-success pull-left provide_bonus custombtn" data-toggle="modal" data-target=".mdl_provide_bonus">Provide Bonus To Driver</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Manage Admin Bonus To Driver List</h5>
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

<div class="modal fade mdl_provide_bonus" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
	<div class="modal-content">
	    <div class="modal-header">
		<button type="button" class="close closebtn" data-dismiss="modal" aria-label="Close">
		    <span aria-hidden="true">&times;</span>
		</button>
		<h4 class="modal-title" id="myModalLabel">Provide Bonus To Driver</h4>
	    </div>

	    <div class="modal-body">
		<form name="frm_add_bonus_to_driver" class="frm_add_bonus_to_driver form-horizontal">
		    <div class="panel-body">
			<div class="homediv">
			    <div class="row">                                            
				<div class="col-sm-12">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Driver Name (*)</b> </span>
					<select multiple="multiple" class="user_id" style="width:100%;" name="user_id[]">
					    <?php foreach($driver_list as $driver) { ?>
						<option value="<?php echo $driver["user_id"]; ?>"><?php echo $driver["name"]; ?></option>
					    <?php } ?>                                
					</select> 
				    </div>
				</div>
			    </div>
			    
			    <div class="row">                                            
				<div class="col-sm-12">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Amount (in £) (*)</b> </span>
					<input type="text" class="form-control numeric amount" maxlength="5" placeholder="10" name="amount">
				    </div>
				</div>
			    </div>
			    
			    <div class="row">                                            
				<div class="col-sm-12">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Reason (*)</b> </span>
					<textarea rows="5" cols="5" class="form-control reason" placeholder="Reason...." name="reason"></textarea>
				    </div>
				</div>
			    </div>
			</div>
		    </div>
		</form>

		<div class="clearfix"></div>
	    </div>

	    <div class="modal-footer">
		<button type="button" class="btn btn-secondary closebtn" data-dismiss="modal">Close</button>
		<a class="btn submit_btn btn-primary pull-right">Save</a>
		<span class="success_msg pull-right" style="color:green; padding: 7px;"></span>
	    </div>

	</div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $(".user_id").multipleSelect({
	filter: true,
	placeholder: "Select Drivers",
	selectAll: false,
//	single: true,
//	singleRadio: false,
    });
    
    $('.submit_btn').click(function (e) {
	$('.error').remove();
	$('.form-control').removeClass('input_error');
	var valid = true;
	var frm = $('form[name = "frm_add_bonus_to_driver"]');
	
	var selected = [];
	$('.user_id .ms-drop > ul').find('li.selected label input[type=checkbox]').each(function(i){
	    selected.push($(this).val());
	});
		
	if (selected.length == '0') {
	    frm.find('.user_id').addClass('input_error').parents('.form-group').append(error_msg('Please select driver'));
	    valid = false;
	} 
	
	var amount = frm.find('[name = "amount"]').val();
	if (!amount || !amount.trim()) {
	    frm.find('[name = "amount"]').addClass('input_error').parents('.form-group').append(error_msg('Please add  bonus amount'));
	    valid = false;
	} 
	
	var reason = frm.find('[name = "reason"]').val();
	if (!reason || !reason.trim()) {
	    frm.find('[name = "reason"]').addClass('input_error').parents('.form-group').append(error_msg('Please provide the reason for bonus'));
	    valid = false;
	} 

	if (valid) {
	    $(this).html('Processing...');
	    var data = new FormData(frm[0]);
	    data.append('user_id', selected);
	    
	    $.ajax({
		url: "admin_bonus/add_bonus",
		type: "post",
		data: data,
		contentType: false,
		cache: false,
		processData:false,
		success: function (resp)
		{
//		    console.log(resp);                        
		    if (resp === 'success'){
			alert("Bonus added in Driver(s) wallet");
			window.location = "admin_bonus";
		    }
		    else if (resp === 'fail'){
			alert("Issue while provinding bonus to driver(s)");
			$('.mdl_provide_bonus').modal('toggle');
			$('.submit_btn').html('Save');
			Xcrud.reload();
		    }
		}

	    });
	}
    });
});
</script>
