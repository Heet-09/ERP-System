<?php 

require_once ('dbClass.php');

$senderName = $_REQUEST['senderName'];

$query = "INSERT INTO master_broker(`Name`) VALUES ($senderName)";


$result = db::getInstance()->db_insertQuery($query);

$showMessage = "success";
$jsonData = array(
	"message"	=> $showMessage
);

echo json_encode($jsonData);

 ?>

