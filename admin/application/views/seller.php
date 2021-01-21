
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Sellers</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Sellers</strong>
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
                    <h5>Manage Sellers</h5>
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
            url: 'seller/admin_verified',
            data: 'seller_id=' + userid + '&tp_status=' + tp_status,
            type: 'post',
            success: function () {}
        });

    });
    
    //stripe
    $(document).on('click', '.create_stripe', function (e) {	
	$(this).attr("disabled", true);
	var seller_id = $(this).data('primary');
	
        $.ajax({
            url: 'seller/seller_account',
            data: 'seller_id=' + seller_id,
            type: 'post',
            success: function (resp) {
		var res = $.parseJSON(resp);
		//console.log(res);
		if(res["status"] == '0') {
		    alert(res["message"]);
		}
		else if(res["status"] == '1') {
		    alert(res["message"]);
		    window.location.reload();
		}
	    }
        });
	return false;
    });
});

   
</script>


