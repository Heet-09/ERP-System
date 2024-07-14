<?php
include_once('../dbClass.php');
$output=array();
$otp = $_GET['otp'];
$card = isset($_GET['card']) ? $_GET['card'] : 0;
$result = db::getInstance()->db_insertQuery("INSERT INTO test_selen_otp (OTPCode, Card) Values('".$otp."', '".$card."')");
echo json_encode($result);
exit();
//http://kreonsolutions.com/mewad/api/selen_otp.php?otp=123457&card=3582
?>