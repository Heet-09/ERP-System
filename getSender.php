<?php 

require_once ('dbClass.php');

$whereString = "";
$whereString = str_replace(',',' and ',$_REQUEST['where']);

if(strlen($whereString) > 0){
    $whereCondition = " where ".$whereString;  
}

$tblColumn =  $_REQUEST['tblResponse'];

// print_r($columns);

$query = "SELECT $tblColumn from master_party $whereCondition";

$result = db::getInstance()->db_select($query);

$row = $result['result_set'];

$fields = explode(',',$tblColumn);
$columns = [];
for($i=0; $i<count($fields); $i++){
    array_push($columns,(array("data" => trim($fields[$i]), "name" => trim($fields[$i]))));
}
// print_r($columns);
// echo "<br>";

$arr = array("data" => $row, "columns" => $columns);

echo json_encode($arr);

 ?>

