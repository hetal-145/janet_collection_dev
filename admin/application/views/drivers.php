
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Drivers</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Drivers</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Manage Drivers</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                
                <div class="ibox-content">
                    
                    <?php echo $content; ?>
                </div>
                <div class="ibox-content">
                    
                    <?php //echo $content1; ?>
                </div>
                
            
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(e){
    //Verify Doc
    $(document).on('click', '.is_admin_verified_checkbox', function (e) {

        var userid; var tp_status;

        if($(this).is(":checked")) {
            userid = $(this).data('userid');
            tp_status = 1;
        } 
        else {
            userid = $(this).data('userid');
            tp_status = 0;
        } 

        $.ajax({
            url: 'users/admin_verified',
            data: 'user_id=' + userid + '&tp_status=' + tp_status,
            type: 'post',
            success: function () {}
        });

    });
    
    //stripe
    $(document).on('click', '.create_stripe', function (e) {	
	$(this).attr("disabled", true);
	var driver_id = $(this).data('primary');
	
        $.ajax({
            url: 'drivers/driver_account',
            data: 'driver_id=' + driver_id,
            type: 'post',
            success: function (resp) {
		var res = $.parseJSON(resp);
		//console.log(res);
		if(res["status"] == '0') {
		    $("a.modal_alert").trigger("click");
		    $("#modal_alert").find("h3.display_msg").text(res["message"]);
		    //alert(res["message"]);
		}
		else if(res["status"] == '1') {
		    $("a.modal_alert").trigger("click");
		    $("#modal_alert").find("h3.display_msg").text(res["message"]);
		    //alert(res["message"]);
		    $("#confirmed").on("click", function () {
			window.location.reload();
		    });
		}
	    }
        });
	return false;
    });
});

   
</script>


