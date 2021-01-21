<table style="width:100%" width="100%" border="0" cellspacing="0" cellpadding="20" bgcolor="#ffffff">
    <tbody>
        <tr>
            <td valign="top">
                <p style="direction:ltr;font-size:14px;line-height:1.4em;color:#444444;font-family:Helvetica Neue,Helvetica,Arial,sans-serif;margin:0 0 1em 0;font-family:'Open Sans',sans-serif;font-size:14px;color:#000000;font-weight:400;margin:0 0 10px 0">
                    Hi , <?= $firstname." ".$lastname; ?> 
                </p>
                <p style="direction:ltr;font-size:14px;line-height:1.4em;color:#444444;font-family:Helvetica Neue,Helvetica,Arial,sans-serif;margin:0 0 1em 0;font-family:'Open Sans',sans-serif;font-size:14px;color:#000000;font-weight:400;margin:0 0 10px 0">

                </p>


                <p>Thank you for joining Drinxin</p>

                <p> To complete your registration, please verify your email by clicking the link below. </p>

             
                <p style=" direction:ltr;font-size:14px;line-height:1.4em;color:#6fb2e2;font-family:Helvetica Neue,Helvetica,Arial,sans-serif;margin:0 0 1em 0;font-family: 'Open Sans'">
                    <a href="<?= site_url('api/verify/' . sha1($user_id)) ?>"style="text-decoration:underline;color:#6fb2e2;font-family: Calibri; " target="_blank">Click Here to Verify Your Account.</a>
                </p>

<!--                <p style="margin-top: 0pt; margin-bottom: 0pt; margin-left: 0in<!--; direction: ltr; unicode-bidi: embed; word-break: normal;"><span style="font-family: Calibri; font-weight: bold; font-size: 14px;">For Support:</span>
                </p>
                <p style="margin-top: 0pt; margin-bottom: 0pt; margin-left: 0in; direction: ltr; unicode-bidi: embed; word-break: normal;"><span style="font-size: 14px;"><span style="font-family: Calibri; color: black; font-style: italic;">Email:</span><span style="font-family: Calibri; color: black;"> </span><span style="font-family: Calibri; color: black;"><a href="mailto:Support@Elev8tion.com">Support@Elev8tion.com</a></span><span style="font-family: Calibri; color: black;">
                        </span></span>
                </p>-->


            </td>
        </tr>
    </tbody>
</table>