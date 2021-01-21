<?php //echo "<pre>"; print_r($order_details); exit; ?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>View Order Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>View Order Details</strong>
            </li>
        </ol>
    </div>
</div>

<div class="modal fade mdl_change_status" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Change Status of Order </h4>
            </div>

            <div class="modal-body">
                <form name="frm_change_status" class="frm_change_status form-horizontal">
                    <div class="panel-body">
                        <input type="hidden" class="form-control order_id" name="order_id" value="<?php echo $order_details["order_id"]; ?>">
                        <input type="hidden" class="form-control user_id" name="user_id" value="<?php echo $order_details["user_id"]; ?>">
                        <div class="row">
                            <div class="col-sm-12">                            
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Order Status</b></span>
                                    <select class="form-control order_status" name="order_status">
                                        <option value="">--Select Order Status--</option>
                                        <option value="6">Accept the order</option>
                                        <option value="7">Reject/Cancel the order</option>
                                        <option value="2">Ready to Ship</option>
                                        <option value="3">Shipped</option>
                                        <option value="4">Delivered</option>
                                    </select>
                                </div>
                            </div>  
                            <div class="col-sm-12" id="show_on_select">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Reason for rejecting the order</b></span>
                                    <textarea class="form-control order_cancellation_reason" name="order_cancellation_reason"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="clearfix"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a class="btn save_status btn-primary pull-right">Save</a>
                <span class="success_msg pull-right" style="color:green; padding: 7px;"></span>
            </div>

        </div>
    </div>
</div>

<div class="modal fade mdl_driver" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Driver Details </h4>
            </div>

            <div class="modal-body">                
                <table class="table">
                    <tr>
                        <th>Driver Name</th>
                        <td><?php echo $order_details["driver_details"]["firstname"] . ' ' . $order_details["driver_details"]["lastname"]; ?></td>                            
                    </tr>
                    <tr>
                        <th>Driver Unique No</th>
                        <td><?php echo $order_details["driver_details"]["userno"]; ?></td>                            
                    </tr>
                    <tr>
                        <th>Driver Contact No</th>
                        <td><?php echo $order_details["driver_details"]["mobileno"]; ?></td>                            
                    </tr>
                    <tr>
                        <th>Driver Email Id</th>
                        <td><?php echo $order_details["driver_details"]["email"]; ?></td>                            
                    </tr>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <!-- Add Order Details -->
    <div class="row property_disabled">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                
                <div class="ibox-title">
                    <h5>Order Details</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    <div class="panel-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Order Date & Time</th>
                                    <td><?php echo date('d F, Y H:i:s', strtotime($order_details["order_date"])); ?></td>
                                </tr>  
                                <tr>
                                    <th>Order Status</th>
                                    <td><?php echo $order_details["ostatus"]; ?></td>
                                </tr>
                                <?php if( $order_details["order_status"] == 7 ) { ?>
                                <tr>
                                    <th>Order Reject / Cancellation Reason</th>
                                    <td><?php echo $order_details["order_cancellation_reason"]; ?></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <th>Order Summary</th>
                                    <td>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th>Name of Alcohol product</th>
                                                    <th>Price</th>
                                                    <th>Qty</th>
                                                    <th>Delivery Type</th>
                                                </tr> 
                                                <?php foreach($order_details["products"] as $oproducts) { ?>
                                                <tr>
                                                    <td><?php echo $oproducts["product_name"]; ?></td>
                                                    <td><?php echo $order_details["currency"].' '.$oproducts["price"]; ?></td>
                                                    <td><?php echo $oproducts["qty"]; ?></td>
                                                    <td><?php 
                                                        if( $oproducts["allow_split_order"] == 0 ) {
                                                            echo 'Normal Delivery';
                                                        } else if( $oproducts["allow_split_order"] == 1 ) { 
                                                            echo 'Split Delivery';
                                                        }
                                                    ?></td>
                                                </tr>                      
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>  
                                <tr>
                                    <th>Method of payment</th>
                                    <td><?php echo $order_details["order_payment"]; ?></td>
                                </tr>
                                <tr>
                                    <th>Order Delivery Type</th>
                                    <td><?php echo $order_details["delivery_type"]; ?></td>
                                </tr>
                                
                                <tr>
                                    <th>User Details</th>
                                    <td>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th>Userno</th>
                                                    <th>Name</th>
                                                    <th>Mobile no</th>
                                                    <th>Email</th>
                                                </tr> 
                                                <tr>
                                                    <td><?php echo $order_details["user"]["userno"]; ?></td>
                                                    <td><?php echo $order_details["user"]["firstname"].' '.$order_details["user"]["lastname"]; ?></td>
                                                    <td><?php echo $order_details["user"]["mobileno"]; ?></td>
                                                    <td><?php echo $order_details["user"]["email"]; ?></td>
                                                </tr>  
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>  
                                
                                <?php if( $order_details["order_status"] == 4 ) { ?>
                                
                                <tr>
                                    <th>Order Delivery Date</th>
                                    <td><?php echo date('d F, Y H:i:s', strtotime($order_details["delivered_date"])); ?></td>
                                </tr>
                                
                                <tr>
                                    <th>Delivery Driver Details</th>
                                    <td>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th>Userno</th>
                                                    <th>Name</th>
                                                    <th>Mobile no</th>
                                                    <th>Email</th>
                                                </tr> 
                                                <tr>
                                                    <td><?php echo $order_details["user"]["userno"]; ?></td>
                                                    <td><?php echo $order_details["user"]["firstname"].' '.$order_details["user"]["lastname"]; ?></td>
                                                    <td><?php echo $order_details["user"]["mobileno"]; ?></td>
                                                    <td><?php echo $order_details["user"]["email"]; ?></td>
                                                </tr>  
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>  
                                <?php } ?>
                                
                                <?php if( $order_details["order_status"] == 7 ) { ?>
                                
                                <tr>
                                    <th>Delivery Driver Details</th>
                                    <td>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th>Userno</th>
                                                    <th>Name</th>
                                                    <th>Mobile no</th>
                                                    <th>Email</th>
                                                </tr> 
                                                <tr>
                                                    <td><?php echo $order_details["user"]["userno"]; ?></td>
                                                    <td><?php echo $order_details["user"]["firstname"].' '.$order_details["user"]["lastname"]; ?></td>
                                                    <td><?php echo $order_details["user"]["mobileno"]; ?></td>
                                                    <td><?php echo $order_details["user"]["email"]; ?></td>
                                                </tr>  
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>  
                                <?php } ?>
                                
                                <?php if( !empty($order_cancellation_details) ) { ?>
                                
                                <tr>
                                    <th>Order Cancellation Request Detail</th>
                                    <td>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th>Date</th>
                                                    <td><?php echo date('d F, Y H:i:s', strtotime($order_cancellation_details["date"])); ?></td>                                                    
                                                </tr> 
                                                <tr>
                                                    <th>Reason</th>
                                                    <td><?php echo $order_cancellation_details["reason"]; ?></td>
                                                </tr> 
                                                <tr>
                                                    <th>Confirmation Status</th>
                                                    <td><?php echo $order_cancellation_details["confirmation_status"]; ?></td>
                                                </tr> 
                                                <tr>
                                                    <th>Amount Refunded</th>
                                                    <td><?php echo $order_details["currency"].' '.$order_cancellation_details["amount_refunded"]; ?></td>
                                                </tr> 
                                                <tr>
                                                    <th>Payment Status</th>
                                                    <td><?php echo $order_cancellation_details["payment_status"]; ?></td>
                                                </tr> 
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>  
                                <?php } ?>
                            </tbody>
                        </table>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <button type="button" style="background-color: #21b9bb;border-color: #21b9bb;color: #FFFFFF;" class="form-control btn btn-md btn-info" id="back"onclick="history.go(-1);">Back</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>   
</div>

<script>
$(document).ready(function () {
    $("#show_on_select").hide();
    
    $(".order_status").change(function(){
        var order_status = $(this).val();
        
        if(order_status == 7){
            $("#show_on_select").show();
        }
        else {
            $("#show_on_select").hide();
        }
    });
    
    $('.save_status').click(function (e) {
        e.preventDefault();
        var frm = $('form[name = "frm_change_status"]');
        $(this).html('Processing...');
        var data = new FormData(frm[0]);
        //console.log(data);
        $.ajax({
            url: "orders/save_order_status",
            type: "post",
            data: data,
            contentType: false,
            cache: false,
            processData:false,
            success: function (resp)
            { 
                console.log(resp);
                if (resp == 'success') {
                    alert('Order status changed.');
                    $('.save_status').html('Save');
                    //window.location.reload();
                } 
                else if(resp == 'error'){
                   alert('Issue in updating order status.');
                   $('.save_status').html('Save');     
                }
               // window.location.reload();
            }
        });
    });
});
</script>
