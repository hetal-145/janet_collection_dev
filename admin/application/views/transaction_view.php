<?php //echo "<pre>"; print_r($transaction_details); exit; ?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>View Transaction Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>View Transaction Details</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <!-- Add Order Details -->
    <div class="row property_disabled">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                
                <div class="ibox-title">
                    <?php if($transaction_details["is_returned"] == 0 || $transaction_details["is_cancelled"] == 0) { ?>
                        <h5>Order Transaction Details</h5>
                    <?php } else if($transaction_details["is_returned"] == 1) { ?>
                        <h5>Order Returned Transaction Details</h5>
                    <?php } else if($transaction_details["is_cancelled"] == 1) { ?>
                        <h5>Order Cancelled Transaction Details</h5>
                    <?php } ?>
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
                                    <th>Order Transaction Summary</th>
                                    <td>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th>Transaction ID</th>
                                                    <th>Payment Method</th>
                                                    <th>Payment Status</th>
                                                </tr> 
                                                <tr>
                                                    <td><?php echo $transaction_details["transaction_id"]; ?></td>
                                                    <td><?php echo $transaction_details["payment_method"]; ?></td>
                                                    <td><?php echo $transaction_details["payment_status"]; ?></td>
                                                </tr>     
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>  
                                <tr>
                                    <th>Transaction Amount</th>
                                    <td><?php echo $transaction_details["transaction_amount"]; ?></td>
                                </tr>  
                                <tr>
                                    <th>Payment Method</th>
                                    <td><?php echo $transaction_details["payment_method"]; ?></td>
                                </tr> 
                                <tr>
                                    <th>Date of Transaction</th>
                                    <td><?php echo date('d F, Y H:i:s', strtotime($transaction_details["orders"]["order_date"])); ?></td>
                                </tr> 
                                
                                <tr>
                                    <th>Order Summary</th>
                                    <td>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th>Order Date</th>
                                                    <td><?php echo date('d F, Y H:i:s', strtotime($transaction_details["orders"]["order_date"])); ?></td>                                                  
                                                </tr> 
                                                <tr>
                                                    <th>Order No</th>
                                                    <td><?php echo $transaction_details["orders"]["order_no"]; ?></td>                                                  
                                                </tr> 
                                                <tr>
                                                    <th>Delivery Type</th>
                                                    <td><?php echo $transaction_details["orders"]["delivery_type"]; ?></td>                                                  
                                                </tr> 
                                                <tr>
                                                    <th>Net Amount</th>
                                                    <td><?php echo $transaction_details["orders"]["net_amount"]; ?></td>                                                  
                                                </tr>
                                                <tr>
                                                    <th>Delivery Charges</th>
                                                    <td><?php echo $transaction_details["orders"]["delivery_charges"]; ?></td>                                                  
                                                </tr>
                                                <tr>
                                                    <th>Order Payment Type</th>
                                                    <td><?php echo $transaction_details["orders"]["order_payment_type"]; ?></td>                                                  
                                                </tr>
                                                <tr>
                                                    <th>Order Status</th>
                                                    <td><?php echo $transaction_details["orders"]["order_status1"]; ?></td>                                                  
                                                </tr>
                                                <?php if($transaction_details["orders"]["order_status"] == 5 || $transaction_details["orders"]["order_status"] == 7) { ?>
                                                    <tr>
                                                        <th>Order Cancellation reason</th>
                                                        <td><?php echo $transaction_details["orders"]["order_cancellation_reason"]; ?></td>                                                  
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>  
                                <tr>
                                    <th>Order Product Summary</th>
                                    <td>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th>Name of Alcohol product</th>
                                                    <th>Price</th>
                                                    <th>Qty</th>
                                                    <th>Delivery Type</th>
                                                    <th>Seller Name</th>
                                                </tr> 
                                                <?php foreach($transaction_details["orders"]["products"] as $oproducts) { ?>
                                                <tr>
                                                    <td><?php echo $oproducts["product_name"]; ?></td>
                                                    <td><?php echo $transaction_details["currency"].' '.$oproducts["price"]; ?></td>
                                                    <td><?php echo $oproducts["qty"]; ?></td>
                                                    <td><?php 
                                                        if( $oproducts["allow_split_order"] == 0 ) {
                                                            echo 'Normal Delivery';
                                                        } else if( $oproducts["allow_split_order"] == 1 ) { 
                                                            echo 'Split Delivery';
                                                        }
                                                    ?></td>
                                                    <td><?php echo $oproducts["seller_name"]; ?></td>
                                                </tr> 
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>Seller Details</th>
                                    <td>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th>Seller Name</th>
                                                    <th>Seller Email</th>
                                                    <th>Seller Contact No</th>
                                                    <th>Seller Address</th>
                                                </tr> 
                                                <?php foreach($transaction_details["orders"]["seller"] as $oseller) { ?>
                                                <tr>
                                                    <td><?php echo $oseller["seller_name"]; ?></td>
                                                    <td><?php echo $oseller["email"]; ?></td>
                                                    <td><?php echo $oseller["contact_no"]; ?></td>
                                                    <td><?php echo $oseller["address"]; ?></td>
                                                </tr>                   
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </td>
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
                                                    <td><?php echo $transaction_details["user"]["userno"]; ?></td>
                                                    <td><?php echo $transaction_details["user"]["firstname"].' '.$transaction_details["user"]["lastname"]; ?></td>
                                                    <td><?php echo $transaction_details["user"]["mobileno"]; ?></td>
                                                    <td><?php echo $transaction_details["user"]["email"]; ?></td>
                                                </tr>  
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>  
                                
                                <?php if( $transaction_details["orders"]["order_status"] == 4 ) { ?>
                                
                                <tr>
                                    <th>Order Delivery Date</th>
                                    <td><?php echo date('d F, Y', strtotime($transaction_details["orders"]["delivered_date"])); ?></td>
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
                                                    <td><?php echo $transaction_details["user"]["userno"]; ?></td>
                                                    <td><?php echo $transaction_details["user"]["firstname"].' '.$transaction_details["user"]["lastname"]; ?></td>
                                                    <td><?php echo $transaction_details["user"]["mobileno"]; ?></td>
                                                    <td><?php echo $transaction_details["user"]["email"]; ?></td>
                                                </tr>  
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>  
                                <?php } ?>
                                
                                <?php if( $transaction_details["orders"]["order_status"] == 7 ) { ?>
                                
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
                                                    <td><?php echo $transaction_details["user"]["userno"]; ?></td>
                                                    <td><?php echo $transaction_details["user"]["firstname"].' '.$transaction_details["user"]["lastname"]; ?></td>
                                                    <td><?php echo $transaction_details["user"]["mobileno"]; ?></td>
                                                    <td><?php echo $transaction_details["user"]["email"]; ?></td>
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
                        <?php // } ?>
                    </div>
                </div> 
            </div>
        </div>
    </div>   
</div>
