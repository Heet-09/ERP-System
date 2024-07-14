<?php 

require_once ('dbClass.php');
// $searchText = isset($_POST['searchText']) ? $_POST['searchText'] : "";
// $searchParam = isset($_POST['searchParam']) ? $_POST['searchParam'] : "";
$ViewName =  $_GET['ViewName'] ?? "";
// $responseParams = isset($_POST['responseParams']) ? $_POST['responseParams'] : "";
// $tmpArr = explode("," , $responseParams);
// $tmpArr[0] .= " as ddID ";
// $tmpArr[1] .= " as ddVal ";
// $responseParams = implode(",", $tmpArr);
$sql = "SELECT ViewName FROM `kreportheader` where ID =" . $ViewName;
$result = db::getInstance()->db_select($sql);
$ViewName = $result['result_set'][0]["ViewName"];

$query = "SELECT * from " . $ViewName ;
$result = db::getInstance()->db_select($query);
$row = $result['result_set'];
// $arr = array("data" => $row);
// echo json_encode($arr);
echo json_encode($row);

 ?>

