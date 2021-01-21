
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Business Users</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Business Users</strong>
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
                    <h5>Manage Business Users</h5>
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
});

   
</script>


