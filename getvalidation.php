<?php 

require_once ('dbClass.php');

$fieldTypeId = $_REQUEST['Id'];

$query = "SELECT * from master_validation WHERE FieldType = '$fieldTypeId' ";

$result = db::getInstance()->db_select($query);

$response["displayField"] = $result['result_set'];

$query1 = "SELECT * from master_validation WHERE FieldType <> '$fieldTypeId' ";

$result1 = db::getInstance()->db_select($query1);

$response["hideField"] = $result1['result_set'];


// if($row > 0){
//     for($i=0; $i<count($row); $i++){
//         $dbFieldName = $row[$i]['Name'];
//         $db_update = "UPDATE kmainfields set Visibility = 1 where DbFieldName = '$dbFieldName' ";
//         $result1 = db::getInstance()->db_update($db_update);

//     }
// }
echo json_encode($response);


 ?>

