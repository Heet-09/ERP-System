<?php 

require_once ('dbClass.php');
$searchText = isset($_POST['searchText']) ? $_POST['searchText'] : "";
$searchParam = isset($_POST['searchParam']) ? $_POST['searchParam'] : "";
$fromRepo = isset($_POST['fromRepo']) ? $_POST['fromRepo'] : "";
$responseParams = isset($_POST['responseParams']) ? $_POST['responseParams'] : "";
$tmpArr = explode("," , $responseParams);
$tmpArr[0] .= " as ddID ";
$tmpArr[1] .= " as ddVal ";
$responseParams = implode(",", $tmpArr);
$query = "SELECT " . $responseParams . " from " . $fromRepo . " where " . $searchParam . " like '%".$searchText."%'";
$result = db::getInstance()->db_select($query);
$row = $result['result_set'];
$arr = array("data" => $row);
echo json_encode($arr);

 ?>

