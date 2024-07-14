<?php
$k_debug = 0;
include_once('../dbClass.php');
$_POST = $_GET;
//$model = isset($_POST['modelFile']) ? $_POST['modelFile'] : '';
$FormID = isset($_POST['FormID']) ? $_POST['FormID'] : 0;
if($FormID == 0){
	echo "No Model Exists.";
	exit();
}
include '../model.php';
$output = array();
$editID = isset ($_POST['editID']) ? $_POST['editID'] : 0; 
$user_id = isset ($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
if($user_id == 0){
	//REDIRECT TO LOGOUT AS USER ID NOT AVAILABLE IN SESSION
}else{
	$_POST['created_by'] = $user_id;
}
$_POST = $_REQUEST;

if($k_debug) echo '<br/>'; 
if($k_debug) print_r($_REQUEST); 
if($k_debug) echo '<br/>';
//print_r("<pre>" . $code."</pre>");	
//echo "<br/>"; 
//print_r($_FILES); 
// print_r($_POST); 
//exit();
// $_POST = $REQUEST;
$mf = 0;
for($i = 0; $i<sizeof($code); $i++){
	if($code[$i][1] == 12){
		$mediaFields[$mf]["CodeArray"] = $i;
		$mediaFields[$mf++]['MediaId'][0]	= $_POST[$code[$i][0]];
	}
	if($code[$i][1] == 13 ){
		$mediaFields[$mf]["CodeArray"] = $i;
		$mediaFields[$mf++]['MediaId']	= explode(",", $_POST[$code[$i][0]]);
	}
}
//print_r($mediaFields);
/**************MEDIA****************//*
$mediaFields = array();
$mf = 0;
for($i = 0; $i<sizeof($code); $i++){
	if($code[$i][1] == 12 || $code[$i][1] == 13 ){
		$mediaFields[$mf] 	= $_FILES[$code[$i][0]];
		$mediaFields[$mf]["CodeArray"] = $i;
		$mf++;
	}
}
for($i = 0; $i<$mf; $i++){	
	for($j = 0; $j<count($mediaFields[$i]['name']) ; $j++){	 //ARRAY for Multiple Files Uploaded in same input
		$target_dir = "img/";
		$imageFileType = strtolower(pathinfo($mediaFields[$i]["name"][$j],PATHINFO_EXTENSION));
		$imageFileName = basename($mediaFields[$i]["name"][$j],".".$imageFileType);
		$imageSize = $mediaFields[$i]["size"][$j];
		//$check = getimagesize($mediaFields[$i]["tmp_name"]);
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) { 
			//Allow certain file formats (use strcmp here)
			echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			exit();
		}
		$existCnt = 0;
		$existCheck = "";
		recheck:
			if($existCnt > 0) 	$existCheck = " (" . $existCnt . ")";
			$target_file = $target_dir . $imageFileName . $existCheck . "." . $imageFileType ;
			if (file_exists($target_file)) { // Check if file already exists
				$existCnt++;
				goto recheck;
			}
		if(move_uploaded_file($mediaFields[$i]["tmp_name"][$j], $target_file)){
			//echo "The file ". basename($mediaFields[$i]["name"][$j]). " has been uploaded.";
			$mediaFields[$i]["name"][$j] = basename($target_file);
			
			$set = array();
			$val = array();
			$k = 0;
			$set[$k] = "MediaName";
			$val[$k++] = $mediaFields[$i]['name'][$j];
			$set[$k] = "MediaType";
			$val[$k++] = strtolower(pathinfo($mediaFields[$i]["name"][$j],PATHINFO_EXTENSION));
			$result = db::getInstance()->db_insert("kMainMedia",$set,$val);
			$mediaFields[$i]['MediaId'][$j] = $result['last_id'];
		}else{
			echo "Sorry, there was an error uploading your file.";
		}
	}
}
/**************END MEDIA***************/
//print("<pre>".print_r($mediaFields)."</pre>"); 
//exit();

	$many2many = array(); $m=0;
	$m2mdb = array(); $mdb=0;
if($editID > 0){
	//EDIT
	$separator = "";
	$sql = "UPDATE `".$db[0]."` SET ";//`".$code[0][0]."` = '". mysql_real_escape_string($_POST[$code[0][0]]) ."'";
	for($i = 0; $i<sizeof($code); $i++){
		if($code[$i][1] == 14 || $code[$i][7] == 0){ 	//If Grid or If Display order = 0
			
		}
		else{
			if($code[$i][1] == 10){	//FOR MANY TO MANY MAPPING
				$many2many[$m++] = $i;
			}else{
				if($code[$i][1] == 9 || $code[$i][1] == 11 || $code[$i][1] == 13){	//FOR MANY TO MANY MAPPING IN OTHER TABLE 
					$m2mdb[$mdb++] = $i;
				}else{ //ALL OTHER DO GO IN MAIN DATABASE TABLE OF THE MODEL
					$val = isset($_POST[$code[$i][0]]) ? ($_POST[$code[$i][0]]) : "";
					$val = db::getInstance()-> real_escape_string($val);
					$sql .= $separator." `".$code[$i][0]."` = '". $val ."' ";
					$separator = ",";
				}
			}
		}
	}
	$sql = $sql . " WHERE `".$db[1]."` = ".$editID;
	if($k_debug) echo '<br/>'.$sql.'<br/>';
	$result = db::getInstance()->db_update($sql);	
	if($k_debug) print_r($result);
	if((int)$result['num_rows'] > 0){
		$output["response"] = "true";
		$output["request_code"] = "Date Updated successfully";
	}else{
		$output["response"] = "false";
		$output["data"] = "There is some problem while saving.";
	}
	
	//DELETE ALL MANY TO MANY ENTRIES
	for($i = 0; $i < $m; $i++){	
		$mapArray = $extradb[((-1)*$code[$many2many[$i]][3])-1][5];
		$mapTable = $mapArray[0];
		$mapIndex = $mapArray[1];
		$mapVariant = $mapArray[2];
		$mapValues = $_POST[$code[$many2many[$i]][0]];
		for($j = 0; $j < sizeof($mapValues); $j++){		
			//echo '<br/>'. $sql = 'DELETE FROM '. $mapTable.' WHERE '. $mapIndex . ' = '. $editID;
			$result = db::getInstance()->db_update($sql);
		}
		//print_r($mapValues); exit();
	}
	//DELETE ALL MANY TO MANY ENTRIES IN MAPPING TABLE (number 11)
	for($i = 0; $i < $mdb; $i++){	
		$mapArray = $extradb[((-1)*$code[$m2mdb[$i]][3])-1];
		$mapTable = $mapArray[6];
		$mapIndex = $mapArray[7];
		$mapVariant = $mapArray[8];
		$mapValues = $_POST[$code[$m2mdb[$i]][0]];
		//print_r($mapValues);
		for($j = 0; $j < sizeof($mapValues); $j++){		
			$sql = 'DELETE FROM '. $mapTable.' WHERE '. $mapIndex . ' = '. $editID;
			if($k_debug)  echo '<br/>'. $sql;
			$result = db::getInstance()->db_update($sql);
		}
		//print_r($mapValues); exit();
	}

	//DELETE ALL MOREDB ENTRIES
	for($i = 0; $i < sizeof($dynamix); $i++){
		$db = $dynamix[$i][1][0];
		$fk = $dynamix[$i][1][1];
		$sql = 'DELETE FROM '. $db.' WHERE '. $fk . ' = '. $editID;
		if($k_debug) echo '<br/>'.$sql.'<br/>';
		$result = db::getInstance()->db_update($sql);
		if($k_debug) print_r($result);
	}	
}
else{
	// print_r($_POST);
	//INSERT	
	$set = array();
	$val = array();
	$k = 0;
	if($k_debug) echo '<br/>' . print_r($code); 
	for($i = 0; $i<sizeof($code); $i++){
		if($code[$i][1] == 14 || $code[$i][7] == 0){ 	//If Grid or If Display order = 0
			
		}else{
			if($code[$i][1] == 10){ //FOR MANY TO MANY MAPPING 
				$many2many[$m++] = $i;
			}else{
				if($code[$i][1] == 9 || $code[$i][1] == 11 || $code[$i][1] == 13){ //FOR MANY TO MANY MAPPING IN OTHER TABLE & for Multiple Images
					$m2mdb[$mdb++] = $i;
					if($code[$i][1] == 13){ //MULTIPLE IMAGES
						for($l = 0; $l<$mf; $l++){	
							if($mediaFields[$l]['CodeArray'] == $i){
								$_POST[$code[$i][0]] = $mediaFields[$l]['MediaId'];
								break;
							}
						}
					}
				}else{
					if($code[$i][1] == 12){ //SINGLE IMAGE UPLOAD
						$val[$k] = 0;
						for($l = 0; $l<$mf; $l++){	
							if($mediaFields[$l]['CodeArray'] == $i){
								$val[$k] = $mediaFields[$l]['MediaId'][0];
								break;
							}
						}
					}else{
						$val[$k] = isset($_POST[$code[$i][0]]) ? db::getInstance()->real_escape_string($_POST[$code[$i][0]]) : "";
					}
					$set[$k] = $code[$i][0];
					$k++;
				}
			}
		}
	}
	//print_r($set);
	//print_r($val);
	$result = db::getInstance()->db_insert($db[0],$set,$val);
	//print_r($result);
	$editID = $result['last_id'];

	$sql1 = "SELECT * from ".$db[0]." WHERE ID = '".$editID."'";
	$result1 = db::getInstance()->db_select($sql1);
	$row1 =  $result1['result_set'];
	
	if($editID > 0){
		$output["response"] = "true";
		$output["request_code"] = "Date Inserted successfully";
		$output["data"] = $row1;
	}else{
		$output["response"] = "false";
		$output["data"] = "There is some problem while saving.";
	}
}


	//CODE FOR MULTIPLE MANY TO MANY RELATION MAPPING TABLE CODE 10
	for($i = 0; $i < $m; $i++){	
		$mapArray = $extradb[((-1)*$code[$many2many[$i]][3])-1][5];
		$mapTable = $mapArray[0];
		$mapIndex = $mapArray[1];
		$mapVariant = $mapArray[2];
		$mapValues = $_POST[$code[$many2many[$i]][0]];
		if(is_array($mapValues)){
		for($j = 0; $j < sizeof($mapValues); $j++)	{	
			$result = db::getInstance()->db_insert($mapTable, array($mapIndex, $mapVariant),array($editID, $mapValues[$j]));
		}
		}
	}
	
	//CODE FOR MULTIPLE MANY TO MANY RELATION MAPPING TABLE CODE 11 & 13
	for($i = 0; $i < $mdb; $i++){	 //SAMPLE USED IN model-vendor.php
		$mapArray = $extradb[((-1)*$code[$m2mdb[$i]][3])-1];
		$mapTable = $mapArray[6];
		$mapIndex = $mapArray[7];
		$mapVariant = $mapArray[8];
		$mapValues = $_POST[$code[$m2mdb[$i]][0]];
		if(is_array($mapValues)){
		for($j = 0; $j < sizeof($mapValues); $j++){
			//echo "<br/>INSERT INTO ". $mapTable.$mapTable . $mapIndex . $mapVariant . $editID . $mapValues[$j];
			$result = db::getInstance()->db_insert($mapTable, array($mapIndex, $mapVariant),array($editID, $mapValues[$j]));
		}
		}
	}
	
//COMMON DYNAMIC FIELDS INSERT
	for($i = 0; $i < sizeof($dynamix); $i++){ //LOOPING ALL THE DYNAMIC ARRAYS
		$db = $dynamix[$i][1][0];
		$fk = $dynamix[$i][1][1];
		$sql = "INSERT INTO ". $db . " (".$fk."";
		for($j = 0; $j < sizeof($dynamix[$i][0]); $j++){ //LOOPING ON FIELDS
			$sql .= "," . $dynamix[$i][0][$j][1];
		}
		$sql = $sql . " ) VALUES";
		$vals = "";
// 		echo "<br/>" . $_POST['cnt'.$dynamix[$i][1][3]];
        $cntTemp = sizeof($_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]]);
// 		for($j = 0; $j < ($_POST['cnt'.$dynamix[$i][1][3]]); $j++){ //LOOPING ON NUMBER OF POSTS PASSED BY FORM
		for($j = 0; $j < $cntTemp; $j++){ //Changed for Darshan REACT API
		  //  echo "<br /> => " . $_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]][$j];
		    //echo $_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]][$j] . " => " . " - " . $dynamix[$i][0][0][1] . $dynamix[$i][1][3] . "<br />";
			if(isset($_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]][$j])){
				if(strlen($_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]][$j]) > 0){
					$vals .= "(".$editID ;
					for($k = 0; $k < sizeof($dynamix[$i][0]); $k++){	//LOOPING NUMBER OF FIELDS IN THE DYNAMIC
						$vals .= ", '". db::getInstance()->real_escape_string($_POST[$dynamix[$i][1][3].$dynamix[$i][0][$k][1]][$j]) ."'";
					}
					$vals .= "),";
				}
			}
		}
		$sql = $sql . substr($vals, 0, -1);
		//echo '<br/>'. $sql;
		if($k_debug) echo '<br/>'.$sql.'<br/>';
		$result = db::getInstance()->db_insertQuery($sql);
	}

echo json_encode($output);
exit();
?>