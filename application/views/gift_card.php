<?php //echo "<pre>"; print_r($gift_cards); exit; ?>
<section class="cart gift_cards mt-66">
    <div class="container-fluid">
        <div class="row">
            <?php if(!empty($gift_cards)) { ?>
	    <input type="hidden" name="offset" id="offset" value="<?php echo $offset; ?>">
            <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8 p-4 content-left order-2 order-sm-2 order-md-2 order-lg-1 order-xl-1">
                <div class="row mb-4">
                    <div class="col-12 align-self-center">
                        <h4 class="title mb-0">Gift Cards</h4>
                    </div>
                </div>
		
                <div class="row">                    
                    <div class="col-xl-12">
                        <div class="row" id="gcard">   
                            <?php foreach($gift_cards as $gcard) { ?>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-4">
                                <a href="javascript:void(0);" class="selected_card" data-card_id="<?php echo $gcard["card_id"]; ?>" data-toggle="modal" data-target="#mdl_gift_card_info">
                                    <div class="card p-3 text-center">
                                        <div class="row gift_card align-items-center">
                                            <div class="col-12">
                                                <h2 class="text-capitalize">gift card</h2>
                                                <h5 class="text-capitalize"><?php echo $gcard["code"]; ?></h5>
                                                <p class="gc_name mb-2 mt-2"><?php echo $gcard["sender_name"]; ?></p>
                                                <p class="desc mb-2">Expires on <?php echo $gcard["expiry_date"]; ?></p>
                                                <p class="desc mb-0">Remaining Balance <strong class="c-pink"><?php echo $gcard["remaining_amount"]; ?></strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>	
		
		<div class="row mb-4">
		    <div class="col-12 text-center mt-4 loadMore">
			<a href="javascript:void(0);" class="btn btn-pink" id="loadMore">Load More</a>
		    </div>
		</div>
            </div>
            <?php } else { ?>
	    <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8 p-4 content-left order-2 order-sm-2 order-md-2 order-lg-1 order-xl-1">
                <div class="row mb-4">
                    <div class="col-12 align-self-center">
                        <h4 class="title mb-0">Gift Cards</h4>
                    </div>
                </div>
                <div class="row">                    
                    <div class="col-xl-12">
                        <div class="row">   
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-4">
                                No Gift Cards
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
	    <?php } ?>
	    
            <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 p-4 content-right order-1 order-sm-1 order-md-1 order-lg-2 order-xl-2">
		
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="title mb-0">Send a Gift Card</h4>
                    </div>
                </div>
                <div class="row mb-4 justify-content-center">
                    <div class="col-xl-12 mb-4">
                        <div class="card p-3 text-center">
                            <div class="row gift_card align-items-center">
                                <div class="col-12">
                                    <h2 class="text-capitalize">gift card</h2>
                                    <p class="gc_name mb-2 mt-2 gc_nm">John Doe</p>
                                    <p class="desc mb-2">Expires on <?php echo date('d M, Y', strtotime("+1 month", strtotime( date('Y-m-d H:i:s') ))); ?></p>
                                    <p class="desc mb-0">Remaining Balance <strong class="c-pink"><?php //echo $gcard["currency"]; ?><span class="gc_amt">120.00</span></strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 send_gift_card">
                        <form method="post" action="#" id="gc_form" name="gc_form">
                            <div class="form-group">
                                <input class="form-control" type="text" id="name" name="name" placeholder="Name">
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" id="gift_email" name="email" placeholder="E-mail">
                            </div>
                            <div class="form-group">
                                <input class="form-control numeric" type="text" id="amount" name="amount" placeholder="Amount">
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" rows="5" type="text" id="message" name="message" placeholder="Message"></textarea>
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" id="sender_name" name="sender_name" placeholder="Sender Name">
                            </div>
                            <div>
                                <button type="button" class="btn btn-checkout" id="gift_btn">Send</button>
                                <br>
                                <span class="success_msg" style="color:green"> </span>
                                <span class="error_msg" style="color:red"> </span>
                            </div>
                        </form>
                    </div>
                </div>
		
		<hr>
		
		<div class="row mb-4">
                    <div class="col-12">
                        <h4 class="title mb-0">Redeem a Gift Card</h4>
                    </div>
		    <div class="col-12">
			<br>
                        <button type="button" class="btn btn-checkout" data-toggle="modal" data-target="#mdl_redeem">Redeem</button>
                    </div>
		    
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="mdl_gift_card_info" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-body" id="gift_history">
                <div class="row mb-3">
                    <div class="col-10">
                        <h4 class="mb-0">Gift Card Details</h4>
                    </div>
                    <div class="col-2">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card p-3 text-center">
                            <div class="row gift_card align-items-center">
                                <div class="col-12">
                                    <h2 class="text-capitalize">gift card</h2>
                                    <h5 class="text-capitalize" id="gc_code">gift card</h5>
                                    <p class="gc_name mb-2 mt-2" id="gc_name">John Doe</p>
                                    <p class="desc mb-2" id="gc_exipry">Expiry on 05 Mar 2019</p>
                                    <p class="desc mb-2">Remaining Price <strong class="c-pink" id="gc_remain_amt">$120.00</strong></p>
                                    <p class="desc mb-2">Total Price <strong class="c-pink" id="gc_total_amt">$1000.00</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
<!--                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-8">
                                <p class="desc mb-0">05 Mar 2019</p>
                                <p class="mb-0">Order: AB454542109</p>
                            </div>
                            <div class="col-4 align-self-center text-right">
                                <h5 class="mb-0"><strong class="c-pink">$100.00</strong></h5>
                            </div>
                        </div>
                        <hr>
                    </div>-->
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mdl_redeem" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-10 col-sm-10 col-md-9 col-lg-9 col-xl-9">
                        <h4 class="mb-0">Redeem</h4>
                    </div>
                    <div class="col-2 col-sm-2 col-md-3 col-lg-3 col-xl-3">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                        <div class="form-group">
			    <label>Enter your unique Gift Card Redeem Code</label>
                            <input class="form-control" id="redeem_code" name="redeem_code" type="text" placeholder="Enter redeem code">
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-lg-6 col-md-6 col-xl-6">
                        <button type="button" class="btn btn-pink_squre redeem_code_now" id="redeem_code_now">Redeem</button>
			<div class="error_redeem" style="color:red;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#loadMore").on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: "<?php echo base_url() . 'gift_card/get_gift_card'; ?>",
            type: "post",
            data: "offset="+$("#offset").val(), 
            success: function (resp)
            {
               // console.log(resp); return false;
		if(resp == 'error') {
                    $(".loadMore").hide();
                }
                else {
                    var res = $.parseJSON(resp);  
		    console.log(res);
                    $.each(res["gift_cards"], function(key, value) {
			
                        $("#gcard").append('<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-4"><a href="javascript:void(0);" class="selected_card" data-card_id="'+value["card_id"]+'" data-toggle="modal" data-target="#mdl_gift_card_info"><div class="card p-3 text-center"><div class="row gift_card align-items-center"><div class="col-12"><h2 class="text-capitalize">gift card</h2><h5 class="text-capitalize">'+value["code"]+'</h5><p class="gc_name mb-2 mt-2">'+value["sender_name"]+'</p><p class="desc mb-2">Expires on '+value["expiry_date"]+'</p><p class="desc mb-0">Remaining Balance <strong class="c-pink">'+value["remaining_amount"]+'</strong></p></div></div></div></a></div>');
                    });
                    $("#offset").val("");
                    $("#offset").val(res["offset"]);
                }        
            }
        });
    });
    
    $("#sender_name").on("input", function() {
        var sender_name = $(this).val();
        if(sender_name == '') {
            $(".gc_nm").text("John Doe");
        }
        else {
            $(".gc_nm").text(sender_name);
        }
    });
    
    $("#amount").on("input", function() {
        var amount = $(this).val();
        if(amount == '') {
            $(".gc_amt").text("120.00");
        }
        else {
            $(".gc_amt").text(parseFloat(amount));
        }
    });
    
    $("#redeem_code_now").on("click", function() {
        $('.success_msg').html('');
        $('.error_msg').html('');
        $('.form-control').removeClass('input_error');
        var valid = true;
        
        var redeem_code = $("#redeem_code").val();
        if (!redeem_code || !redeem_code.trim()) {
	    $("#redeem_code").parents('.form-group').find(".error").remove();
            $("#redeem_code").addClass('input_error').parents('.form-group').append(error_msg('Please enter your unique gift card redeem code'));
            valid = false;
        }    
	
        if (valid) {
            //$("#redeem_code_now").attr("disabled", true);
            $("#redeem_code_now").html('Processing...');
            var redeem_code = $("#redeem_code").val();
            $.ajax({
                url: "<?php echo base_url() . 'gift_card/redeem_gift_card'; ?>",
                type: "post",
                data: "redeem_code=" + redeem_code,
                success: function (resp)
                {
		    $("#modal_submit_reviews").find(".alert_msg").html("");
                    //console.log(resp);  return false; 
		    if(resp == 1) {
			$(".error_redeem").html("No card found.");
			 $("#redeem_code_now").attr("disabled", false);
			 $("#redeem_code_now").html('Redeem');
		    }
		    else if(resp == 2) {
			$(".error_redeem").html("You have already redeem thid card.");
			 $("#redeem_code_now").attr("disabled", false);
			 $("#redeem_code_now").html('Redeem');
		    } 
		    else if(resp == 3) {
			$("a.modal_submit_reviewss").trigger("click");
			$("#modal_submit_reviews").find(".alert_msg").html("Gift card redeem successfully.");
                
			window.location.reload();
		    }
		    
                }
            });
	    return false;
        }
    });
    
    $("#gift_btn").on("click", function() {
        $('.success_msg').html('');
        $('.error_msg').html('');
        $('.form-control').removeClass('input_error');
        var valid = true;
        var frm = $(this).closest($('form[name = "gc_form"]'));
        
        var name = frm.find('[name = "name"]').val();
        if (!name || !name.trim()) {
            frm.find('[name = "name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter name of the person'));
            valid = false;
        } 
        
        var amount = frm.find('[name = "amount"]').val();
        if (!amount || !amount.trim()) {
            frm.find('[name = "amount"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter amount to be send in gift card'));
            valid = false;
        } 
        
        var email = frm.find('[name = "email"]').val();
        if (!email || !email.trim()) {
            frm.find('[name = "email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter email address of the person'));
            valid = false;
        } 
        
        var sender_name = frm.find('[name = "sender_name"]').val();
        if (!sender_name || !sender_name.trim()) {
            frm.find('[name = "sender_name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your name'));
            valid = false;
        } 
            
        if (valid) {
           // $(this).attr("disabled", "disabled");
           // $(this).html('Processing...');
            var data = new FormData(frm[0]);
            $.ajax({
                url: "<?php echo base_url() . 'gift_card/send_gift_card'; ?>",
                type: "post",
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (resp)
                {
                    console.log(resp);
                    var res = $.parseJSON(resp);
                    window.location.href = "<?php echo base_url(); ?>gift_card/do_gift_card_payment?card_id="+res["card_id"];
                }
            });
        }
    });
    
    $(document).on("click", ".selected_card", function(){
        var card_id = $(this).data("card_id");
        $.ajax({
            url: "<?php echo base_url() . 'gift_card/get_card_details'; ?>",
            type: "post",
            data: "card_id="+card_id,
            success: function (resp)
            {
                var res = $.parseJSON(resp);
                //console.log(res);
                $("#gc_code").text(res["code"]);
                $("#gc_name").text(res["sender_name"]);
                $("#gc_exipry").text("Expiry on "+res["expiry_date"]);
                $("#gc_remain_amt").text(res["currency"]+res["remaining_amount"]);
                $("#gc_total_amt").text(res["currency"]+res["amount"]);          
                if("history" in res){
                    $.each(res["history"], function(key, value) {
			console.log(value);
                        $("#gift_history").append('<br><div class="col-xl-12 gift_history_appended"><div class="row"><div class="col-8"><p class="desc mb-0">'+ value["order"]["order_date"] +'</p><p class="mb-0">Order: '+ value["order"]["order_no"] +'</p></div><div class="col-4 align-self-center text-right"><h5 class="mb-0"><strong class="c-pink">'+ res["currency"]+value["order"]["net_amount"] +'</strong></h5></div></div><hr></div>');
                    });
                }
                else {      
		    $('#gift_history').find('br').remove();
                    $(".gift_history_appended").remove();
                }                
            }
        });
    });
});
</script>