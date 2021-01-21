<?php //echo "<pre>"; print_r($notifications); echo "</pre>"; exit; ?>
<section class="page_section prod_usr_ratings mt-66 h-100vh">
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                <h2 class="main_title">Notification Lists</h2>
            </div>
            <div class="col-4 text-right align-self-center d-none">  
                <input type="hidden" name="offset" id="offset" value="<?php echo $offset; ?>">
            </div>
            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12 prod_ratings mt-4">
                <div class="row" id="notify_list">
                    <?php $i=1; if(!empty($notifications)) { foreach($notifications as $key => $notify) { ?>
                   
                        <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                            <div class="card mt-2">
                                <div class="media">
                                    <div class="review_left_box">
                                    </div>
                                    <div class="media-body">
                                        <p class="desc mt-0 mb-0"><?php echo $notify["message"]; ?></p>
                                    </div>
                                </div>                            
                            </div>
                        </div>
                    <?php }} ?>                    
                </div>
		<div class="col-12 text-center mt-4 loadMore" <?php if($flag == "0") { ?> style="display: none;" <?php } ?>>
                    <a href="javascript:void(0);" class="btn btn-pink" id="loadMore">Load More</a>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">

$(document).ready(function() {
    $("#loadMore").on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: "<?php echo base_url() . 'notifications/get_notification_list'; ?>",
            type: "post",
            data: "offset="+$("#offset").val(), 
            success: function (resp)
            {
                //console.log(resp);
                if(resp == 'error') {
                    $(".loadMore").hide();
                }
                else if(resp == 2) {
                    window.location.reload();
                }
                else {
                    var res = $.parseJSON(resp);   
		    
		    if(res.flag == "0") {
			$(".loadMore").hide();
		    }
		    
		    $.each(res["notifications"], function(key, value) {
                        $("#notify_list").append('<div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12"><div class="card mt-2"><div class="media"><div class="review_left_box"></div><div class="media-body"><p class="desc mt-0 mb-0">'+ value["message"] +'</p></div></div></div></div>');
                    });
                    $("#offset").val("");
                   // console.log(res["offset"]);
                    $("#offset").val(res["offset"]);
                }
                
            }
        });
    });
});
</script>