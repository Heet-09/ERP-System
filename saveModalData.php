<?php 

require_once ('dbClass.php');

$Name = $_REQUEST['Name'];

$Description = $_REQUEST['Description'];

$query = "INSERT INTO master_party(`Name`,`Description`) VALUES ('".$Name."','".$Description."')";

$result = db::getInstance()->db_insertQuery($query);


$sql = "SELECT * from master_party WHERE ID = '".$result['last_id']."'";

$result1 = db::getInstance()->db_select($sql);

$row = $result1['result_set'];


echo json_encode($row);

 ?>

