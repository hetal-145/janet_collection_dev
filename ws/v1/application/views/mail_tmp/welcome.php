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
								<h3 style="margin: 0;letter-spacing: .5px;">Congratulations! <br/><span style="color: #ec1d74;"><?= $firstname." ".$lastname; ?></span></h3>
							    </td>
							</tr>
							<tr>
							    <td height="20"></td>
							</tr>
							<tr>
							    <td align="center" class="center white_color" style="font-family: 'Open Sans',sans-serif; font-size: 20px; text-transform:capitalize; mso-line-height-rule:exactly; line-height:35px;">
								<p style="font-size: 16px;letter-spacing: .5px;color: #171717;">Thank you for joining Drinxin.</p>
							    </td>
							</tr>
							<tr>
							    <td height="20"></td>
							</tr>
							<tr>
							    <td align="center" class="center white_color" style="font-family: 'Open Sans',sans-serif; font-size: 20px; text-transform:capitalize; mso-line-height-rule:exactly; line-height:35px;">
								<p style="font-size: 16px;letter-spacing: .5px;color: #171717;">To complete your registration, <br> please verify your email.</p>
							    </td>
							</tr>
							<tr>
							    <td height="20"></td>
							</tr>
							<tr>
							    <td align="center" class="center white_color" style="font-family: 'Open Sans',sans-serif; font-size: 20px; text-transform:capitalize; mso-line-height-rule:exactly; line-height:35px;">
								<p style="font-size: 16px;letter-spacing: .5px;"><a style="color: #ec1d74;text-decoration: underline !important;" href="<?= site_url('api/verify/' . sha1($user_id)) ?>">Click Here to Verify Your Account.</a></p>
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