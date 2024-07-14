<?php 

require_once ('dbClass.php');


$field =$_REQUEST['field'];
// print_r($fieldArray);
$query = "SELECT * from kgridfields where DbFieldName = '$field' ";

$result = db::getInstance()->db_select($query);


$row = $result['result_set'];

// $row[OnkeyupParameters

$arr = array( "TOTAL" => $row[0]['OnkeyupParameterS']);
echo json_encode($arr);

 ?>

