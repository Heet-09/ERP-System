<?php
/**
 * This example shows sending a message using a local sendmail binary.
 $mail_recipient 		= '';
 $mail_recipient_name   = '';
 $mail_body             = '';
 $mail_sender           = '';
 $mail_sender_name      = '';
 $mail_replyto          = '';
 $mail_replyto_name     = '';
 $mail_subject          = '';
 */
 
/*  
 $mail_recipient 		= isset($mail_recipient) ? $mail_recipient : '';
 $mail_recipient_name   = isset() ? : '';
 $mail_body             = isset() ? : '';
 $mail_sender           = isset() ? : '';
 $mail_sender_name      = isset() ? : '';
 $mail_replyto          = isset() ? : '';
 $mail_replyto_name     = isset() ? : '';
 $mail_subject          = isset() ? : '';
  */
require 'PHPMailerAutoload.php';
//Create a new PHPMailer instance
$mail = new PHPMailer;
// Set PHPMailer to use the sendmail transport
$mail->isSendmail();
//Set who the message is to be sent from
$mail->setFrom($mail_sender, $mail_sender_name);
//Set an alternative reply-to address
$mail->addReplyTo($mail_replyto, $mail_replyto_name);
//Set who the message is to be sent to
$mail->addAddress($mail_recipient, $mail_recipient_name);
//Set the subject line
$mail->Subject = $mail_subject;
$mail->msgHTML($mail_body);

// only for testMail.php file for testing the mail server 
//$attachmentFile = file_get_contents('testInvoiceMail.html');
//	$mail->addAttachment($attachmentFiles);

//$mail->Subject = 'Your Order is booked '.$_SESSION['uid'];

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$file = dirname(__FILE__).'\sampleEmailInvoice.php';
//$mail->msgHTML(file_get_contents($file));
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