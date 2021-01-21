<table style="width:100%" width="100%" border="0" cellspacing="0" cellpadding="20" bgcolor="#ffffff">
    <tbody>
        <tr>
            <td valign="top">
                <h1><strong>Order Details</strong></h1>
                <table style="width:100%" width="100%" border="1" cellspacing="0" cellpadding="20" bgcolor="#eee">
                    <tr>
                        <th>Order No</th>
                        <td><?= $order_no; ?></td>
                    </tr>
                    <tr>
                        <th>Shipping Address</th>
                        <td><?= $shipping_details["name"] . '<br>' . $shipping_details["address"] . '<br>' . $shipping_details["zipcode"] . '<br>' . $shipping_details["contactno"]; ?></td>
                    </tr>                    
                    <tr>
                        <th>Order Date</th>
                        <td><?= date('d M, Y H:i:s', strtotime($order_date)); ?></td>
                    </tr>
                    <tr>
                        <th>Delivery Charges</th>
                        <td><?= '£'.$delivery_charges; ?></td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td><?= '£'.$net_amount; ?></td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </tbody>
</table>