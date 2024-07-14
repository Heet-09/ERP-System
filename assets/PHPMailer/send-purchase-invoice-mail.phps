<?php
/**
 * This example shows sending a message using a local sendmail binary.
 */
 
require 'PHPMailerAutoload.php';
$imglogo = 'img/surprising-box-logo.jpg';
//Create a new PHPMailer instance
$mail = new PHPMailer;
// Set PHPMailer to use the sendmail transport
$mail->isSendmail();
//Set who the message is to be sent from
$mail->setFrom('priyank.lalan4@gmail.com', 'SURBOX');
//Set an alternative reply-to address
$mail->addReplyTo('priyank.lalan4@gmail.com', 'SURBOX');
//Set who the message is to be sent to
$mail->addAddress($EmailIdCustomer, $customerName);
//Set the subject line
$mail->Subject = $mail_subject;
//$mail->msgHTML($mail_body);



$HTML = '<div id=":jo" class="ii gt adP adO"><div id=":l3" class="a3s aXjCH m15d2b33d73faa7a4"><div dir="ltr"><div class="gmail_default adM" style="font-family:trebuchet ms,sans-serif"><br></div><div class="gmail_quote"><div class="adM"><br></div><u></u>
<div>
<table cellpadding="0" cellspacing="0" border="0" style="width:640px">
    <tbody>
    <tr>

        <td style="background:#f2f2f2;border-width:1px 0 0 1px;border-style:solid;border-color:#ccc;border-radius:5px 0 0 0;width:20px">&nbsp;</td>
        <td style="background:#f2f2f2;border-top:1px solid #ccc;padding:20px 0 10px 0;width:600px">

            <h1 style="width:50%;float:left;color:#333;font-family:Arial,sans-serif;font-size:18px;font-weight:bold;line-height:27px;padding:0;margin:0">
                <img src="http://www.keygst.com/wp-content/uploads/2017/07/Key-GST-online-return-filing-logo-1.png" alt="keygst.com"/>

            </h1>
			
			<h4 style="float:right;text-align:right;width:50%;"><a href="#">SIGN UP FOR FREE </a></h4>
			<p style="float:left;width:100%;"><em>UNBLOCKING GST FOR YOUR BUSINESS</em></p>
        </td>		
        <td style="background:#f2f2f2;border-width:1px 1px 0 0;border-style:solid;border-color:#ccc;border-radius:0 5px 0 0;width:20px;text-align:right;;"></td>
    </tr>
    
    <tr>

        <td style="background:#f2f2f2;border-left:1px solid #ccc;width:20px">&nbsp;</td>

        <td style="width:600px;background:#fff;border:1px solid #ccc;border-radius:5px">

            <table cellpadding="0" cellspacing="0" border="0" style="width:600px">

                <tbody>

                <tr>

                    <td style="width:20px">&nbsp;</td>

                    <td style="padding-top:10px;padding-bottom:35px;width:560px">



                        <h2 style="color:#333;font-family:Arial,sans-serif;font-size:22px;line-height:33px">Purchase Invoice </h2>

                        <table cellpadding="0" cellspacing="0" border="0" style="width:500px">

                            <tbody>

                            <tr>

                                <td style="width:20px;border-style:solid;border-width:1px 0 1px 1px;border-color:#ccc;border-radius:5px 0 0 0">&nbsp;</td>

                                <td colspan="2" style="padding-top:20px;padding-bottom:20px;width:460px;font-family:Arial,sans-serif;font-size:15px;color:#333;line-height:20px;border:1px solid #ccc;border-right:0;border-left:0">
                                    <strong>YOUR INVOICE DETAILS</strong>

                                </td>

                                <td style="width:20px;border-style:solid;border-width:1px 1px 1px 0;border-color:#ccc;border-radius:0 5px 0 0">&nbsp;</td>

                            </tr>

                            <tr>

                                <td style="width:20px;border-left:1px solid #ccc">&nbsp;</td>

                                <td valign="top" style="padding-top:30px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px">

                                    <strong>Invoice ID</strong>

                                </td>

                                <td valign="top" style="padding-top:30px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px">

                                    '.$invoiceID.';

                                </td>

                                <td style="width:20px;border-right:1px solid #ccc">&nbsp;</td>

                            </tr>

                            <tr>

                                <td style="width:20px;border-left:1px solid #ccc">&nbsp;</td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px">

                                    <strong>Invoice Date</strong>

                                </td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px">

                                    '.$datefordb.'

                                </td>

                                <td style="width:20px;border-right:1px solid #ccc">&nbsp;</td>

                            </tr>



                            <tr>

                                <td style="width:20px;border-left:1px solid #ccc">&nbsp;</td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px" width="180">

                                    <strong>Business Name</strong>

                                </td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px">

                                    '.$biz_name.'

                                </td>

                                <td style="width:20px;border-right:1px solid #ccc">&nbsp;</td>

                            </tr>
                            <tr>

                                <td style="width:20px;border-left:1px solid #ccc">&nbsp;</td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px" width="180">

                                    <strong>GSTIN</strong>

                                </td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px">

                                    '.$gstin.'

                                </td>

                                <td style="width:20px;border-right:1px solid #ccc">&nbsp;</td>

                            </tr>
                            <tr>

                                <td colspan="4" style="border-style:solid;border-width:0 1px 0 1px;border-color:#ccc;padding-top:10px">&nbsp;</td>

                            </tr>

                            

                            <tr>

                                <td style="width:20px;border-left:1px solid #ccc">&nbsp;</td>

                                <td valign="top" style="padding-top:20px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px;border-top:1.5px dashed #9e9e9e">

                                    <strong>Taxable Value:</strong>

                                </td>

                                <td valign="top" style="padding-top:20px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px;border-top:1.5px dashed #9e9e9e">

                                    <strong>'.$totaltaxablevalue.'</strong>

                                </td>

                                <td style="width:20px;border-right:1px solid #ccc">&nbsp;</td>

                            </tr>

                            

							
                            <tr>

                                <td style="width:20px;border-left:1px solid #ccc">&nbsp;</td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:13px;color:#999;line-height:20px">

									Total CGST


                                </td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:13px;color:#999;line-height:20px">

                                    '.$totalcgstvalue.'

                                </td>

                                <td style="width:20px;border-right:1px solid #ccc">&nbsp;</td>

                            </tr>
							
                            <tr>

                                <td style="width:20px;border-left:1px solid #ccc">&nbsp;</td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:13px;color:#999;line-height:20px">

                                    Total SGST

                                </td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:13px;color:#999;line-height:20px">

                                    '.$totalsgstvalue.'

                                </td>

                                <td style="width:20px;border-right:1px solid #ccc">&nbsp;</td>

                            </tr>
							
                            <tr>

                                <td style="width:20px;border-left:1px solid #ccc">&nbsp;</td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:13px;color:#999;line-height:20px">

                                    Total IGST

                                </td>

                                <td valign="top" style="padding-top:10px;font-family:Arial,sans-serif;font-size:13px;color:#999;line-height:20px">

                                    '.$totaligstvalue.'

                                </td>

                                <td style="width:20px;border-right:1px solid #ccc">&nbsp;</td>

                            </tr>
							

                            <tr>

                                <td colspan="4" style="border-style:solid;border-width:0 1px 0 1px;border-color:#ccc;padding-top:10px">&nbsp;</td>

                            </tr>


                            

                            <tr>

                                <td style="width:20px;border-left:1px solid #ccc;border-bottom:1px solid #ccc;border-radius:0 0 0 5px">&nbsp;</td>

                                <td valign="top" style="padding-top:20px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px;padding-bottom:30px;border-top:1.5px dashed #9e9e9e;border-bottom:1px solid #ccc" width="180">
                                    <strong>GRAND TOTAL:</strong>

                                </td>

                                <td valign="top" style="padding-top:20px;font-family:Arial,sans-serif;font-size:14px;color:#333;line-height:20px;padding-bottom:30px;border-top:1.5px dashed #9e9e9e;border-bottom:1px solid #ccc">
                                    <strong>'.$totalgrandvalue.'</strong>

                                </td>

                                <td style="width:20px;border-right:1px solid #ccc;border-bottom:1px solid #ccc;border-radius:0 0 5px 0">&nbsp;</td>

                            </tr>

                            </tbody>

                        </table>


                        <p style="font-family:Arial,sans-serif;font-size:13px;color:#333;line-height:20px;padding-bottom:10px;width:500px;margin-top:20px">

                            Please pay your invoice amount to activate your order. In case youve already paid, please ignore this.

                        </p>



                        <table cellspacing="0" cellpadding="0" border="0" style="border:0;width:120px;font-family:Arial,sans-serif;text-align:center">

                            <tbody>

                            <tr>

                                <td width="200" valign="middle" bgcolor="#3c860b" align="center" height="50" style="background:#2fbe15;border:1px solid #18900b;border-radius:4px;display:block;margin:0;padding:13px 0 9px 0;height:36px;width:200px">

                                    <a style="color:#fff;display:block;font-size:14px;font-weight:bold;text-decoration:none;width:200px" href="http://manage.bigrock.in/linkhandler/servlet/PrePaymentServlet?transid_type=%20%2074091238_invoice&amp;payfor=customer&amp;role=customer&amp;paymentkey=JwLBgG5B9l" target="_blank" data-saferedirecturl="https://www.google.com/url?hl=en-GB&amp;q=http://manage.bigrock.in/linkhandler/servlet/PrePaymentServlet?transid_type%3D%2520%252074091238_invoice%26payfor%3Dcustomer%26role%3Dcustomer%26paymentkey%3DJwLBgG5B9l&amp;source=gmail&amp;ust=1499754829216000&amp;usg=AFQjCNFzAllq1uS0E6eJMlLgae5n9UEp0g">
                                        Pay Invoice  <br style="min-height:1px;line-height:1px">

                                         <span style="color:#fff;clear:both;display:block;font-size:10px;font-weight:normal;padding:2px 0;margin:0;width:200px">

                                             To Activate your Order

                                         </span>

                                    </a>

                                </td>

                            </tr>

                            </tbody>

                        </table>



                    </td>

                    <td style="width:20px">&nbsp;</td>

                </tr>

                </tbody>

            </table>



        </td>

        <td style="background:#f2f2f2;border-right:1px solid #ccc;width:20px">&nbsp;</td>

    </tr>



    <tr>

        <td style="background:#f2f2f2;border-width:0 0 1px 1px;border-style:solid;border-color:#ccc;border-radius:0 0 0 5px;width:20px">&nbsp;</td>
        <td style="background:#f2f2f2;border-bottom:1px solid #ccc;padding:20px 0;width:600px">



            <table cellpadding="0" cellspacing="0" border="0" style="width:600px">

                <tbody>

                <tr>

                    <td style="color:#333;font-family:Arial,sans-serif;font-size:11px;font-weight:bold;padding-bottom:0;padding-left:25px">

                        SUPPORT

                    </td>

                </tr>

                <tr>

                    <td valign="top" style="color:#333;font-family:Arial,sans-serif;font-size:11px;padding-left:25px">

                        <p style="line-height:20px">

                            Email: <a href="mailto:support@keygst.com" target="_blank">support@keygst.com</a><br>Tel No.: +91 7021676069<br>

                        </p>

                    </td>

                </tr>

                </tbody>

            </table>

        </td>

        <td style="background:#f2f2f2;border-width:0 1px 1px 0;border-style:solid;border-color:#ccc;border-radius:0 0 5px 0;width:20px">&nbsp;</td>
    </tr>

    </tbody>



</table><div class="yj6qo"></div><div class="adL">

</div></div><div class="adL">

</div></div><div class="adL"><br></div></div><div class="adL">
</div></div></div>';
// only for testMail.php file for testing the mail server 
//$attachmentFile = file_get_contents('testInvoiceMail.html');
//	$mail->addAttachment($attachmentFiles);

//$mail->Subject = 'Your Order is booked '.$_SESSION['uid'];

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$file = dirname(__FILE__).'\sampleEmailInvoice.php';
$mail->msgHTML($HTML);
//Replace the plain text body with one created manually

$mail->AltBody = 'This is a plain-text message body';
//Attach an image file

//$mail->addAttachment($imglogo);
//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
	/* header('Location: ./product.php');*/
}
?>