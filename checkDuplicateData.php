<?php 

require_once ('dbClass.php');

$selectedValue = $_REQUEST['selectedValue'];

$selectedValueString = implode(" and ",$selectedValue);  
$query = "SELECT * from Tasks where $selectedValueString";

$result = db::getInstance()->db_select($query);

$arr = array( "rows" => $result['num_rows']);

echo json_encode($arr);


 ?>

