<table style="width:100%" width="100%" border="0" cellspacing="0" cellpadding="20" bgcolor="#ffffff">
    <tbody>
        <tr>
            <td valign="top">
                <p><strong>Congratulations! <?= $receiver_name; ?></strong></p>
                <p style="direction:ltr;font-size:14px;line-height:1.4em;color:#444444;font-family:Helvetica Neue,Helvetica,Arial,sans-serif;margin:0 0 1em 0;font-family:'Open Sans',sans-serif;font-size:14px;color:#000000;font-weight:400;margin:0 0 10px 0"></p>
                <p> You have received a gift card from <?= $sender_name ?> of amount <strong><?= '£'.$amount ?></strong>.</p>
                <p>
                    You can use this amount for your next purchase from <a href="https://www.drinxin.com/">drinxin.com</a>
                </p>
		
                <p><strong>Message from the sender</strong><br><?= $message ?></p>
                <p>
                    This gift card will get <strong>expired on <?= date('d M, Y H:i:s', strtotime($expiry_date)); ?></strong>.
                </p>
            </td>
        </tr>
    </tbody>
</table>