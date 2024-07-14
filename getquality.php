<?php 

require_once ('dbClass.php');



$QualityName = $_REQUEST['selectedValue'];

$query = "SELECT * from master_quality WHERE ID = '$QualityName' ";

$result = db::getInstance()->db_select($query);



//$result = mysql_query($query);

//$row = mysql_fetch_assoc($result);

$row = $result['result_set'];

$arr = array("HSN" => $row[0]['HSN'], "BaleNo" => "Test" );

echo json_encode($arr);

 ?>

