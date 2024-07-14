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
$mail->addAddress('miteshrocks007@gmail.com', 'MS');
//Set the subject line

$mail->Subject = 'SUB';
$mail->msgHTML('THIS');

// only for testMail.php file for testing the mail server 
//$attachmentFile = file_get_contents('testInvoiceMail.html');
//	$mail->addAttachment($attachmentFiles);

//$mail->Subject = 'Your Order is booked '.$_SESSION['uid'];

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$file = dirname(__FILE__).'\sampleEmailInvoice.php';
$mail->msgHTML(file_get_contents($file));
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