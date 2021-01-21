<!-- Section 1 -->
<table align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" border="0" width="600"style="background-repeat: no-repeat !important;background-position: center center;background-size: contain;width: 100%;max-width: 600px;margin: 0 auto;background-image: url('mail_img/bg_img.jpg');" class="full-width main-bg1">
    <tbody>
	<tr>
	    <td>
		<!-- Layout Table -->
		<table border="0" align="center" width="600" cellpadding="0" cellspacing="0" class="mobile-width" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
		    <tbody>
			<!-- Start Space -->
			<tr>
			    <td height="40" style="font-size: 50px; line-height: 40px;">&nbsp;</td>
			</tr>
			<!-- End Space -->
			<tr>
			    <td align="center">
				<!-- Container Table -->
				<table cellspacing="0" cellpadding="0" border="0" width="600" class="content-width">
				    <tbody>
					<tr>
					    <td>
						<!-- Left Part Content -->
						<table cellspacing="0" cellpadding="0" border="0" width="580" align="center" class="content-width center">
						    <tbody>
							<tr>
							    <td align="center" class="center white_color" style="font-family: 'Open Sans',sans-serif; font-size: 20px; text-transform:capitalize; mso-line-height-rule:exactly; line-height:35px;">
								<h3 style="margin: 0;letter-spacing: .5px;">Congratulations! <br/><span style="color: #ec1d74;"><?= $receiver_name; ?></span></h3>
							    </td>
							</tr>
							<tr>
							    <td height="20"></td>
							</tr>
							<tr>
							    <td align="center" class="center white_color" style="font-family: 'Open Sans',sans-serif; font-size: 20px; text-transform:capitalize; mso-line-height-rule:exactly; line-height:35px;">
								<p style="font-size: 16px;letter-spacing: .5px;color: #171717;">You have received a gift card<br>from <?= $sender_name ?> of amount <b><?= '£'.$amount ?></b>.</p>
							    </td>
							</tr>
							<tr>
							    <td height="20"></td>
							</tr>
							<tr>
							    <td align="center" class="center white_color" style="font-family: 'Open Sans',sans-serif; font-size: 20px; text-transform:capitalize; mso-line-height-rule:exactly; line-height:35px;">
								<p style="font-size: 16px;letter-spacing: .5px;">You can use this amount<br/>for your next purchase from <a style="color: #ec1d74;text-decoration: underline !important;" href="https://www.Janet-Collection.com/">Janet-Collection.com</a></p>
							    </td>
							</tr>
							<tr>
							    <td height="20"></td>
							</tr>
							<tr>
							    <td align="center" class="center white_color" style="font-family: 'Open Sans',sans-serif; font-size: 20px; text-transform:capitalize; mso-line-height-rule:exactly; line-height:35px;">
								<p style="font-size: 16px;letter-spacing: .5px;text-align: center;"><b>Message from the sender</b><br><?= $message ?></p>
							    </td>
							</tr>
							<tr>
							    <td height="20"></td>
							</tr>
							<tr>
							    <td align="center" class="center white_color" style="font-family: 'Open Sans',sans-serif; font-size: 20px; text-transform:capitalize; mso-line-height-rule:exactly; line-height:35px;">
								<p style="font-size: 14px;letter-spacing: .5px;text-align: center;">This gift card will get expired on <?= date('d M, Y H:i:s', strtotime($expiry_date)); ?>.</p>
							    </td>
							</tr>
						    </tbody>
						</table>
					    </td>
					</tr>
				    </tbody>
				</table>
				<!-- Container Table Ends -->
			    </td>
			</tr>
			<!-- Start Space -->
			<tr>
			    <td height="25" style="line-height: 25px;">&nbsp;</td>
			</tr>
			<!-- End Space -->
		    </tbody>
		</table>
	    </td>
	</tr>
    </tbody>
</table>
<!-- Section 1 End -->