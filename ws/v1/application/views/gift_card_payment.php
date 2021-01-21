<?php
header("Access-Control-Allow-Origin: *");
$config_data = $this->db->where_in('key', array('client_key', 'currency'))->get('setting')->result_array();
//print_r($config_data); exit;

?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!--<script type="text/javascript" src="payment.js"></script>-->
<!-- Latest compiled JavaScript -->

<style>
    .icon-container {
        margin-bottom: 20px;
        padding: 0px 0;
        font-size: 35px;

    }
    .swal-button--no{
        background: #ccc;
    }
</style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<div class="col-xs-12 col-md-4">
    <div class="panel panel-default">
        <div class="panel-body">
            <span class="paymentErrors alert-danger"></span>
            <form action="save_gift_card_transation" method="POST"  id="paymentForm">

                <input name="amount" value="<?= $total_price ?>" type="hidden"/>
                <input name="currency" value="<?= CURRENCY; ?>" type="hidden"/>
                <input name="user_id" class="user_id" value="<?= $user_id ?>" type="hidden"/>
                <input name="card_id" value="<?= $card_id ?>" type="hidden"/>
                <!--<label for="fname">Accepted Cards</label>-->
                <div class="col-xs-12">
                    <div class="icon-container">
                        <i class="fa fa-cc-visa" style="color:navy;"></i>
                        <i class="fa fa-cc-amex" style="color:blue;"></i>
                        <i class="fa fa-cc-mastercard" style="color:red;"></i>
                        <i class="fa fa-cc-discover" style="color:orange;"></i>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="custName" class="form-control name" value="">
                    </div>
                </div>
                <!--                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="custEmail" class="form-control email" value="">
                                    </div>
                                </div>-->
<!--                <div class="col-xs-12">
                    <div class="form-group">
                        <label for="Zipcode">Zipcode</label>
                        <input type="Zipcode" name="zipcode" class="form-control zipcode" value="">
                    </div>
                </div>-->

                <input type="hidden" name="custEmail" class="form-control email" value="pay@stripe.com">
                <div class="col-xs-12">
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" name="cardNumber" size="20" autocomplete="off" id="cardNumber" class="form-control" value="" />
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="form-group">
                        <label>CVC (code)</label>
                        <input type="text" name="cardCVC" size="4" autocomplete="off" id="cardCVC" class="form-control"/>
                    </div>
                </div>


                <div class="col-xs-4">
                    <div class="form-group">
                        <label> Exp (MM)</label>
                        <input type="text" name="cardExpMonth" placeholder="MM" size="2" id="cardExpMonth" class="form-control" value="" />
                    </div>
                </div>

                <div class="col-xs-4">
                    <div class="form-group">
                        <label>Exp (YYYY)</label>
                        <input type="text" name="cardExpYear" placeholder="YYYY" size="4" id="cardExpYear" class="form-control" value="" />
                    </div>
                </div>

                <br>

                <?php
                $s_price = $total_price / 100;
                ?>
                <div class="col-xs-12">
                    <center> 
                        <input type="submit" id="makePayment" class="btn btn-success center" style="width: 180px" value="  Pay <?= CURRENCY_CODE . $s_price ?>">
                    </center>

                </div>
                <br>
                <center> 
<!--                    <img src="http://iviou.com/iviou/admin/assets/stripe/powered_by_stripe@3x.png" height="25px" width="110px">-->
                </center>
            </form>
        </div>
    </div>
</div>
      <style>
            .error{
                color:#ed5565;
                text-align: right;
                margin-right: 2px;
            }
            .input_error{
                border: thin solid #ed5565 !important;
            }
        </style>

<script>

// set your stripe publishable key
    Stripe.setPublishableKey('<?= $config_data[0]["value"]; ?>');
    $(document).ready(function () {
        $("#paymentForm").submit(function (event) {
            $('#makePayment').attr("disabled", "disabled");
// create stripe token to make payment
  event.preventDefault();
            //return false;
            $('.form-control').removeClass('input_error');
            $('.form-group').removeClass('input_error');

            if ($('.name').val() == "") {

                $('.name').addClass('input_error');
                $('.name').focus();
                return false;
            }
//            if ($('.zipcode').val() == "") {
//
//                $('.zipcode').addClass('input_error');
//                $('.zipcode').focus();
//                return false;
//            }
            if ($('#cardCVC').val() == "") {

                $('#cardCVC').addClass('input_error');
                $('#cardCVC').focus();
                return false;
            }
            if ($('#cardNumber').val() == "") {
                  $('#cardNumber').addClass('input_error');
                $('#cardNumber').focus();
                return false;
            }
            if ($('#cardExpMonth').val() == "") {
                  $('#cardExpMonth').addClass('input_error');
                $('#cardExpMonth').focus();
                return false;
            }
            if ($('#cardExpYear').val() == "") {
                  $('#cardExpYear').addClass('input_error');
                $('#cardExpYear').focus();
                return false;
            }
            Stripe.createToken({
                number: $('#cardNumber').val(),
                cvc: $('#cardCVC').val(),
                exp_month: $('#cardExpMonth').val(),
                exp_year: $('#cardExpYear').val(),
                //address_zip: $('.zipcode').val(),
            }, handleStripeResponse);
            return false;
        });
    });
// handle the response from stripe
    function handleStripeResponse(status, response) {
	var resp = JSON.stringify(response);
        //console.log(resp);
        if (response.error) {
            $('#makePayment').removeAttr("disabled");
            $(".paymentErrors").html(response.error.message);
        } else {
            var payForm = $("#paymentForm");
//get stripe token id from response
            var stripeToken = response['id'];
//set the token into the form hidden input to make payment
            payForm.append("<input type='hidden' name='stripeToken' value='" + stripeToken + "' />");
	    payForm.append("<input type='hidden' name='stripeResponse' value='" + resp + "' />");
	    payForm.append("<input type='hidden' name='status' value='" + status + "' />");


//save card

            var carddata = {
                'cardNumber': $('#cardNumber').val(),
                'cardCVC': $('#cardCVC').val(),
                'cardExpMonth': $('#cardExpMonth').val(),
                'cardExpYear': $('#cardExpYear').val(),
                'name': $('.name').val(),
                //'email': $('.email').val(),
                //'zipcode': $('.zipcode').val(),
                'user_id': $('.user_id').val()
            };
	    
	    payForm.get(0).submit();
	    return false;
            /*swal("Do you want to save your card?", {
                buttons: {
                    yes: {
                        text: "Yes",
                        value: "yes",
                    },
                    no: {
                        text: "No",
                        value: "no",
                    },
                },
            }).then(function (value) {
                switch (value) {

                    case "yes":
                        $.ajax({
                            url: 'http://35.177.141.159/ws/v1/api/save_cards',
                            type: 'post',
                            data: carddata,
                            success: function (resp) {
                                payForm.get(0).submit();
                            }
                        });
                        break;

                    case "no":
                        payForm.get(0).submit();
                        break;

                    default:
                        break;
                }
            });*/

        }
    }

</script>