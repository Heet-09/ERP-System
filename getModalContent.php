<?php 

require_once ('dbClass.php');

$Party = $_REQUEST['selectedValue'];

$query = "SELECT * from kmainfields WHERE FormId = '1' ";

$result = db::getInstance()->db_select($query);

$row = $result['result_set'];
// $flds = "";
for($i = 0; $i < count($row); $i++){
    if($row[$i]['FormId'] == 1){ 

		$flds .=  "<input value='' type='text'  name='".$row[$i]['DbFieldName']."' />";

	}
 
}

//$result = mysql_query($query);

//$row = mysql_fetch_assoc($result);


echo json_encode($flds);

 ?>

