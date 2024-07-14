<?php 

require_once ('dbClass.php');


$dbTable = $_REQUEST['dbTable'];

$PurchaseInwardId = $_REQUEST['PurchaseInwardId'];

$dbField = $_REQUEST['dbField'];

$PartyId = $_REQUEST['PartyId'];

$query = "INSERT INTO $dbTable($dbField) VALUES ($PurchaseInwardId,$PartyId[0])";

$result = db::getInstance()->db_insertQuery($query);
// $query = "Select * From master_party";

// $result = db::getInstance()->db_select($query);

// $row = $result['result_set'];

$arr = array("data" => $result);

echo json_encode($arr);

 ?>

