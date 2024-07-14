<?php 

require_once ('dbClass.php');

$query = "SELECT max(id), CONCAT( 'E-', LPAD(max(id)+1,7,'0') ) as ID from purchaseinward ";

$result = db::getInstance()->db_select($query);

$row = $result['result_set'];

if($row[0]['ID'] == NULL){
    $chlNumber = 'E-0000001';
}else{
    $chlNumber = $row[0]['ID'];
}
$date = date('Y-m-d');

$arr = array("ChlNo" => $chlNumber,"ChlDate" => $date );
echo json_encode($arr);

 ?>

