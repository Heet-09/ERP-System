<?php
/*
<html><head><meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html;charset=UTF-8"></head><body>
RETURNS ARRAY of Array with following format:
		    [0] => Db Field Name
		    [1] => Type
		    [2] => Label 
		    [4] => DB Value (only if EditID passed)
		    [5] => Required
		    [7] => display order
*/
$output = array();
$k_debug = 0;
include_once('../dbClass.php');
$FormID = isset($_GET['FormID']) ? $_GET['FormID'] : 0;
$editID = isset($_GET['editID']) ? $_GET['editID'] : 0;

if($FormID == 0){
	echo "No Model Exists.";
	exit();
}
include '../model.php';

if($editID > 0){ //EDIT MODE
	$sql = "SELECT * FROM ".$db[0]." WHERE ".$db[1]." =".$editID;
	$result = db::getInstance()->db_select($sql);
	for($i = 0; $i < $result['num_rows']; $i++){ //WHILE LOOP FOR $row
		for($j = 0; $j < sizeof($code); $j++){
			if($code[$j][1] == 10){  //FOR MAPPING TABLE Multi-Select
				$temp = array();
				//SELECT * FROM map_vendor_categories LEFT JOIN categories ON map_vendor_categories.cat_id = categories.cat_id WHERE vendor_id = 1
				$mapArray  = $extradb[((-1)*$code[$j][3])-1][5];
				$joinTable = $extradb[((-1)*$code[$j][3])-1][1];
				$joinIndex = $extradb[((-1)*$code[$j][3])-1][2];
				$mapTable = $mapArray[0];
				$mapIndex = $mapArray[1];
				$mapVariant = $mapArray[2];
				$sql = 'SELECT * FROM ' . $mapTable  . ' WHERE ' . $mapIndex . ' = ' . $editID ;///. ' LEFT JOIN ' . $joinTable . ' ON ' . $mapTable.'.'.$mapVariant . ' = ' . $joinTable.'.'.$joinIndex . ' WHERE ' . $mapIndex . ' = ' . $editID ;
				$rs = db::getInstance()->db_select($sql);
				//print_r($rs['result_set']);
				$code[$j][4] = $rs['result_set'];
			}else{
				if($code[$j][1] == 11 || $code[$j][1] == 9){  //FOR MAPPING TABLE Multi-Select
					$temp = array();
					//SELECT * FROM map_vendor_categories LEFT JOIN categories ON map_vendor_categories.cat_id = categories.cat_id WHERE vendor_id = 1
					$mapArray  = $extradb[((-1)*$code[$j][3])-1];
					$joinTable = $extradb[((-1)*$code[$j][3])-1][1];
					$joinIndex = $extradb[((-1)*$code[$j][3])-1][2];
					$mapTable = $mapArray[6];
					$mapIndex = $mapArray[7];
					$mapVariant = $mapArray[8];
					//$sql = 'SELECT * FROM ' . $mapTable  . ' WHERE ' . $mapIndex . ' = ' . $editID ;///. ' LEFT JOIN ' . $joinTable . ' ON ' . $mapTable.'.'.$mapVariant . ' = ' . $joinTable.'.'.$joinIndex . ' WHERE ' . $mapIndex . ' = ' . $editID ;
					$sql = 'SELECT ' . $mapVariant . ' FROM ' . $mapTable  . ' WHERE ' . $mapIndex . ' = ' . $editID ;///. ' LEFT JOIN ' . $joinTable . ' ON ' . $mapTable.'.'.$mapVariant . ' = ' . $joinTable.'.'.$joinIndex . ' WHERE ' . $mapIndex . ' = ' . $editID ;
					$rs = db::getInstance()->db_select($sql);
		// 			print_r($rs['result_set']);
					$za = array();
					for($z = 0; $z < sizeof($rs['result_set']); $z++){
					    $za[] = $rs['result_set'][$z][$mapVariant];
					}
					// $code[$j][4] = $rs['result_set'];
					$code[$j][4] = $za;
				}else{
					$code[$j][4] = $result['result_set'][$i][$code[$j][0]];
				}
			}
		}
	}
}
	
/*
//echo createMore1($dynamix[0][0], $dynamix[0][1], $radio, $editID);
function createMore1($m,$d,$r,$id){
	$v = array();
	$row1 = array();
	if($id > 0){
		$sql = "SELECT * FROM ".$d[0]." WHERE ".$d[1]." = ".$id;
		$result = db::getInstance()->db_select($sql);
		$row = $result['result_set'];
		//print_r($result);
		$size = sizeof($m);
		for($i = 0; $i < $result['num_rows']; $i++){
			$v[$i] = array($size);
			for($j = 0; $j < $size; $j++){
				//echo $m[$j][1]."<br />";
				if(strlen($row[$i][$m[$j][1]]) == 0){
					$v[$i][$j] = $row[$i][ucfirst($m[$j][1])];
				}else{
					$v[$i][$j] = $row[$i][$m[$j][1]];
				}
			}
			$v[$i][$j] = $row[$i][$d[1]];
		}
	}
	//print_r($v);
	//$d[2]; $d[3];

	for($i = 0; $i < sizeof($m); $i++){		   
		if($m[$i][0] == 1){	//IF TEXT BOX
			echo 'value="" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" placeholder="'.$m[$i][2].'" '.$m[$i][6];
		}
// 		if($m[$i][0] == 2){	//IF RADIO/DD
// 			//print_r($m[$i]);
// 			if($m[$i][3] < 0){	//IF to be taken from DB
// 				$index = ((-1) * $m[$i][3]) - 1;
// 				global $moreextradb;
// 				$dbarray = $moreextradb[$index];
// 				$sql1 = "SELECT ".$moreextradb[$index][1].",".$moreextradb[$index][2]." FROM ".$moreextradb[$index][0]." ".$moreextradb[$index][3];
// 				$sql1 = "SELECT ".$moreextradb[$index][1].",".$moreextradb[$index][2]." FROM ".$moreextradb[$index][0]." ".$moreextradb[$index][3];
// 				$result1 = db::getInstance()->db_select($sql1);
// 				//print_r($result1);
// 				$row1 = $result1['result_set'];
// 				$flds .= '<td><select name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control" '.$m[$i][6].' ><option value="0">'.$m[$i][2].'</option>';
// 				for($j = 0; $j < sizeof($row1); $j++){ 
// 					$flds .= '<option value="'. $row1[$j][$dbarray[1]] .'" >'.$row1[$j][$dbarray[2]].'</option>&nbsp;';
// 				}
// 				$flds .= '</select></td>';
// 			}else{			
// 				$flds .= '<td><select name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control" '.$m[$i][6].' ><option value="0">'.$m[$i][2].'</option>';
// 				for($j = 0; $j < sizeof($r[$m[$i][3]]); $j++){
// 					$k = $j+1;  //value
// 					$flds .= '<option value="'. $k .'" >'.$r[$m[$i][3]][$j].'</option>&nbsp;';
// 				}
// 				$flds .= '</select></td>';
// 			}
// 		}
	}
// 	$flds .= '</div>';
// 	$end = '</div></tr>';		

// 		if(empty($v)){
// 			$fieldCount = 1;
// 		}else
// 		{
// 			$tableStart = '<table style="table-layout: fixed;word-wrap: break-word;" class="table table-responsive table-bordered table-hover">';
// 			$tableEnd = '</table>';
// 			$tableHead = '<thead class=" thead-light"><tr><th style="width:30px;padding:10px 3px !important;"><a  style="padding:3px;"  href="javascript:void(0)" class="btn btn-success add'.$d[3].'"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>';
// 			$fvals = '';
// 			$headerGridStart = '<tr class="grp'.$d[3].'" id="'.$d[3];
// 			$headerGridEnd   = '">';
	
// 			global $moreextradb;
// 			//print_r($m);
// 			//print_r($v);
// 			for($i = 0; $i < sizeof($m); $i++){		   //style='width:".$m[$i][7]."px' width='".$m[$i][7]."'
// 				$tableHead .= "<th style='width:".$m[$i][7]."px' width='".$m[$i][7]."'>".$m[$i][2]."</th>";
// 			}
// 			for($k = 0; $k < sizeof($v); $k++){
// 				$fvals .= $headerGridStart . $k . $headerGridEnd .'<td style="padding:10px 3px !important;"><a style="padding:3px;" href="javascript:void(0)" class="btn btn-danger removes'.$d[3].'"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>';
// 				for($i = 0; $i < sizeof($m); $i++){
// 					$inputVal = $v[$k][$i];
// 					if($m[$i][0] == 1){
// 					    $fvals .= '<td ><input type="text" value="'.$inputVal.'" class="form-control" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" placeholder="'.$m[$i][2].'" '.$m[$i][6].' /></td>';
// 						//$fvals .= '<td><input type="text" value="'.$inputVal.'" class="form-control" name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" /></td>';
// 					}
// 					if($m[$i][0] == 2){ // iF DD
// 						if($m[$i][3] < 0){ //FROM DB
// 							$index = ((-1) * $m[$i][3]) - 1;
// 							global $moreextradb;
// 							$dbarray = $moreextradb[$index];
// 							$sql1 = "SELECT ".$dbarray[1].",".$dbarray[2]." FROM ".$dbarray[0]." ".$dbarray[3];
// 							$result1 = db::getInstance()->db_select($sql1);
// 							$row1 = $result1['result_set'];							
// 							$fvals .= '<td><select name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control" '.$m[$i][6].' ><option value="0">'.$m[$i][2].'</option>';
// 							for($j = 0; $j < sizeof($row1); $j++){ 
// 								if($inputVal == $row1[$j][$dbarray[1]])
// 									$fvals .= '<option value="'. $row1[$j][$dbarray[1]] .'" selected>'.$row1[$j][$dbarray[2]].'</option>&nbsp;';
// 								else
// 									$fvals .= '<option value="'. $row1[$j][$dbarray[1]] .'" >'.$row1[$j][$dbarray[2]].'</option>&nbsp;';
// 							}
// 							$fvals .= '</select></td>';
// 						}else{
// 							$fvals .= '<td><select name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control" '.$m[$i][6].' ><option value="0">'.$m[$i][2].'</option>';
// 							for($j = 0; $j < sizeof($r[$m[$i][3]]); $j++){
// 								$p = $j+1;  //value
// 								if($p == $inputVal) $fvals .= '<option selected value="'. $p .'" >'.$r[$m[$i][3]][$j].'</option>&nbsp;';
// 								else $fvals .= '<option value="'. $p .'" >'.$r[$m[$i][3]][$j].'</option>&nbsp;';
// 							}
// 							$fvals .= '</select></td>';
// 						}
// 					}
// 				}
// 				$fvals .= '</tr>';
// 				//$fvals .= '<td><a href="javascript:void(0)" class="btn btn-danger removes'.$d[3].'"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>';
// 			}
// 			$fieldCount = $k + 1 ;
// 		}
// 		$fldCnt = '<input type="hidden" value="'.$fieldCount.'" name="cnt'.$d[3].'" id="cnt'.$d[3].'">';
// 		$tableHead .= "</tr></thead>";
// 		//echo htmlspecialchars($tableHead);
// 		if(empty($v)){
// 			return $script . $temp . $fldCnt . $tableStart . $tableHead . $headerGridStart . "0" . $headerGridEnd . $flds . $end . $tableEnd."</div>";
// 		}else{
// 			return $script . $temp . $fldCnt . $tableStart . $tableHead .  $fvals . $end . $tableEnd."</div>";
// 		}
	}
*/
// print_r($code);
$output["code"] = $code;
$gridExists = false;
for($k = 0; $k < sizeof($code); $k++){
    $flag =0;
    //For Type 5
    if($code[$k][1] == 5 || $code[$k][1] == 4){
    	$arr = $code[$k];
        for($i = 0; $i < sizeof($extradb); $i++){
        	if($extradb[$i][0] == $arr[3]){
        		$edb = $extradb[$i][1];
        		$eid = $extradb[$i][2];
        		$elb = $extradb[$i][3];
        		//$end = checkVariable($extradb[$i][4]);
        		$end = $extradb[$i][4];
        		$eval = array();
        		$esql = "SELECT ".$eid.",".$elb." FROM ".$edb . " " . $end;
        		if($k_debug) echo '<br/>'.$esql.'<br/>';
        		$eresult = db::getInstance()->db_select($esql);
        		if($k_debug){ echo '<br/>'; print_r($eresult); }
        		$flag=1;
        		break;
        	}
        }
    }
    //For Type 12
    // if($code[$k][1] == 12 && $editID > 0){
    // 	$arr = $code[$k];
    //     for($i = 0; $i < sizeof($extradb); $i++){
    //     	if($extradb[$i][0] == $arr[3]){
    //     		$edb = $extradb[$i][1];
    //     		$eid = $extradb[$i][2];
    //     		$elb = $extradb[$i][3];
    //     		//$end = checkVariable($extradb[$i][4]);
    //     		$end = $extradb[$i][4];
    //     		$eval = array();
    //     		$esql = "SELECT ".$eid.",".$elb." FROM ".$edb . " " . $end . " where " . $eid . " = " . $code[$k][4];
    //     // 		$esql = "SELECT ".$eid.",".$elb." FROM ".$edb . " " . $end; // . " where " . $eid . " = " . $code[$k][4];
    //     		if($k_debug) echo '<br/>'.$esql.'<br/>';
    //     		$eresult = db::getInstance()->db_select($esql);
    //     		if($k_debug){ echo '<br/>'; print_r($eresult); }
    //     		$flag=1;
    //     		break;
    //     	}
    //     }
    // }
     //For Type 9
    if($code[$k][1] == 9){
    	$arr = $code[$k];
        for($i = 0; $i < sizeof($extradb); $i++){
        	if($extradb[$i][0] == $arr[3]){
        		$edb = $extradb[$i][1];
        		$eid = $extradb[$i][2];
        		$elb = $extradb[$i][3];
        		//$end = checkVariable($extradb[$i][4]);
        		$end = $extradb[$i][4];
        		$eval = array();
        		$esql = "SELECT ".$eid.",".$elb." FROM ".$edb . " " . $end;
        		if($k_debug) echo '<br/>'.$esql.'<br/>';
        		$eresult = db::getInstance()->db_select($esql);
        		if($k_debug){ echo '<br/>'; print_r($eresult); }
        		$flag=1;
        		break;
        	}
        }
    }
    //FOR TYPE = 11	
    if($code[$k][1] == 11){
    	$arr = $code[$k];
        for($i = 0; $i < sizeof($extradb); $i++){
        	if($extradb[$i][0] == $arr[3]){
        		$edb = $extradb[$i][1];
        		$eid = $extradb[$i][2];
        		$elb = $extradb[$i][3];
        		$end = $extradb[$i][4];
        		$ear = $extradb[$i][5];
        		$emnydb = $extradb[$i][6];
        		$emnyid = $extradb[$i][7];
        		$emnyfk = $extradb[$i][8];
        		$eval = array();
        		$esql = "SELECT ".$eid.",".$elb." FROM ".$edb;// . " " . $end;
        		if($k_debug) echo '<br/>'.$esql.'<br/>';
        		$eresult = db::getInstance()->db_select($esql);
        		if($k_debug){ echo '<br/>'; print_r($eresult); }
        		$flag=1;
        		break;
        	}
        }
    }
    if($code[$k][1] == 14){
        $gridExists = true;
    }
    if($flag){
        $t = array();
        $t["keys"] = array($eid, $elb);
        $t["result_set"] = $eresult["result_set"];
        $output[$code[$k][0]] = $t;
    }
}	


//FOR TYPE = 14
if($gridExists){
    // 	echo "<br />";echo "<br />";
    // 	echo createMore1($dynamix[0][0], $dynamix[0][1], $radio, $editID);
    // 	echo "<br />";echo "<br />";
    // 	echo "DYNAMIX 00"; $dynamix[0][0];
    // 	echo "<br />";echo "<br />";
    // 	echo "DYNAMIX 01";
    // 	print_r($dynamix);
    // 	echo "<br />";echo "<br />";
    $output["grid"] = array();
    for($g = 0; $g < sizeof($dynamix); $g++){   
        $output["grid"][$g]["header"] = $dynamix[$g][1];
    // 	$output["grid"][$g]["fields"] = $dynamix[$g][0];
    	$output["grid"][$g]["fields"] = getDynamicRelatedValues($dynamix[$g][0]);
    	$output["grid"][$g]["values"] = getDynamicVals($dynamix[$g][0],$dynamix[$g][1],$editID);
    }
}

function getDynamicRelatedValues($m){
    for($i = 0; $i < sizeof($m); $i++){
 		if($m[$i][0] == 2){	//IF RADIO/DD
 			if($m[$i][3] < 0){	//IF to be taken from DB
 			    $m[$i][8] = array();
 				$index = ((-1) * $m[$i][3]) - 1;
 				global $moreextradb;
 				$dbarray = $moreextradb[$index];
 				$sql1 = "SELECT ".$dbarray[1].",".$dbarray[2]." FROM ".$dbarray[0]." ".$dbarray[3];
 				$result1 = db::getInstance()->db_select($sql1); //print_r($result1);
 				$row1 = $result1['result_set'];
 			// 	echo "<br />";echo "<br />";  print_r($dbarray);  echo "<br />";echo "<br />";
 				$m[$i][8] = $row1;
 				$m[$i][9] = array($dbarray[1], $dbarray[2]);
 			}
 		}
	}
	return $m;
}

function getDynamicVals($m,$d,$id){
	$v = array();
// 	$row1 = array();
	if($id > 0){
		$sql = "SELECT * FROM ".$d[0]." WHERE ".$d[1]." = ".$id;
		$result = db::getInstance()->db_select($sql);
		$row = $result['result_set'];
// 		print_r($result); print_r($m); echo "<br /><br />";
		$size = sizeof($m);
		for($i = 0; $i < $result['num_rows']; $i++){
			$v[$i] = array($size);
			for($j = 0; $j < $size; $j++){
				// echo "<br />".$m[$j][1]." => ".$row[$i][$m[$j][1]]."<br />"."<br />";
				if(strlen($row[$i][$m[$j][1]]) == 0){
					$v[$i][$j] = $row[$i][ucfirst($m[$j][1])];
				}else{
					$v[$i][$j] = $row[$i][$m[$j][1]];
				}
			}
// 			$v[$i][$j] = $row[$i][$d[1]];
		}
	}
	//print_r($v);
	//$d[2]; $d[3];

    /*
	for($i = 0; $i < sizeof($m); $i++){		   
		if($m[$i][0] == 1){	//IF TEXT BOX
			echo 'value="" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" placeholder="'.$m[$i][2].'" '.$m[$i][6];
		}
 		if($m[$i][0] == 2){	//IF RADIO/DD
 			//print_r($m[$i]);
 			if($m[$i][3] < 0){	//IF to be taken from DB
 				$index = ((-1) * $m[$i][3]) - 1;
 				global $moreextradb;
 				$dbarray = $moreextradb[$index];
 				$sql1 = "SELECT ".$moreextradb[$index][1].",".$moreextradb[$index][2]." FROM ".$moreextradb[$index][0]." ".$moreextradb[$index][3];
 				$sql1 = "SELECT ".$moreextradb[$index][1].",".$moreextradb[$index][2]." FROM ".$moreextradb[$index][0]." ".$moreextradb[$index][3];
 				$result1 = db::getInstance()->db_select($sql1);
 				//print_r($result1);
 				$row1 = $result1['result_set'];
 				$flds .= '<td><select name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control" '.$m[$i][6].' ><option value="0">'.$m[$i][2].'</option>';
 				for($j = 0; $j < sizeof($row1); $j++){ 
 					$flds .= '<option value="'. $row1[$j][$dbarray[1]] .'" >'.$row1[$j][$dbarray[2]].'</option>&nbsp;';
 				}
 				$flds .= '</select></td>';
 			}else{			
 				$flds .= '<td><select name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control" '.$m[$i][6].' ><option value="0">'.$m[$i][2].'</option>';
 				for($j = 0; $j < sizeof($r[$m[$i][3]]); $j++){
 					$k = $j+1;  //value
 					$flds .= '<option value="'. $k .'" >'.$r[$m[$i][3]][$j].'</option>&nbsp;';
 				}
 				$flds .= '</select></td>';
 			}
 		}

 	$flds .= '</div>';
 	$end = '</div></tr>';		
 		if(empty($v)){
 			$fieldCount = 1;
 		}else
 		{
 			$tableStart = '<table style="table-layout: fixed;word-wrap: break-word;" class="table table-responsive table-bordered table-hover">';
 			$tableEnd = '</table>';
 			$tableHead = '<thead class=" thead-light"><tr><th style="width:30px;padding:10px 3px !important;"><a  style="padding:3px;"  href="javascript:void(0)" class="btn btn-success add'.$d[3].'"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>';
 			$fvals = '';
 			$headerGridStart = '<tr class="grp'.$d[3].'" id="'.$d[3];
 			$headerGridEnd   = '">';
 			global $moreextradb;
 			//print_r($m);
 			//print_r($v);
 			for($i = 0; $i < sizeof($m); $i++){		   //style='width:".$m[$i][7]."px' width='".$m[$i][7]."'
 				$tableHead .= "<th style='width:".$m[$i][7]."px' width='".$m[$i][7]."'>".$m[$i][2]."</th>";
 			}
 			for($k = 0; $k < sizeof($v); $k++){
 				$fvals .= $headerGridStart . $k . $headerGridEnd .'<td style="padding:10px 3px !important;"><a style="padding:3px;" href="javascript:void(0)" class="btn btn-danger removes'.$d[3].'"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>';
 				for($i = 0; $i < sizeof($m); $i++){
 					$inputVal = $v[$k][$i];
 					if($m[$i][0] == 1){
 					    $fvals .= '<td ><input type="text" value="'.$inputVal.'" class="form-control" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" placeholder="'.$m[$i][2].'" '.$m[$i][6].' /></td>';
 						//$fvals .= '<td><input type="text" value="'.$inputVal.'" class="form-control" name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" /></td>';
 					}
 					if($m[$i][0] == 2){ // iF DD
 						if($m[$i][3] < 0){ //FROM DB
 							$index = ((-1) * $m[$i][3]) - 1;
 							global $moreextradb;
 							$dbarray = $moreextradb[$index];
 							$sql1 = "SELECT ".$dbarray[1].",".$dbarray[2]." FROM ".$dbarray[0]." ".$dbarray[3];
 							$result1 = db::getInstance()->db_select($sql1);
 							$row1 = $result1['result_set'];							
 							$fvals .= '<td><select name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control" '.$m[$i][6].' ><option value="0">'.$m[$i][2].'</option>';
 							for($j = 0; $j < sizeof($row1); $j++){ 
 								if($inputVal == $row1[$j][$dbarray[1]])
 									$fvals .= '<option value="'. $row1[$j][$dbarray[1]] .'" selected>'.$row1[$j][$dbarray[2]].'</option>&nbsp;';
 								else
 									$fvals .= '<option value="'. $row1[$j][$dbarray[1]] .'" >'.$row1[$j][$dbarray[2]].'</option>&nbsp;';
 							}
 							$fvals .= '</select></td>';
 						}else{
 							$fvals .= '<td><select name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control" '.$m[$i][6].' ><option value="0">'.$m[$i][2].'</option>';
 							for($j = 0; $j < sizeof($r[$m[$i][3]]); $j++){
 								$p = $j+1;  //value
 								if($p == $inputVal) $fvals .= '<option selected value="'. $p .'" >'.$r[$m[$i][3]][$j].'</option>&nbsp;';
 								else $fvals .= '<option value="'. $p .'" >'.$r[$m[$i][3]][$j].'</option>&nbsp;';
 							}
 							$fvals .= '</select></td>';
 						}
 					}
 				}
 				$fvals .= '</tr>';
 				//$fvals .= '<td><a href="javascript:void(0)" class="btn btn-danger removes'.$d[3].'"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>';
 			}
 			$fieldCount = $k + 1 ;
 		}
 		$fldCnt = '<input type="hidden" value="'.$fieldCount.'" name="cnt'.$d[3].'" id="cnt'.$d[3].'">';
 		$tableHead .= "</tr></thead>";
 		//echo htmlspecialchars($tableHead);
 		if(empty($v)){
 			return $script . $temp . $fldCnt . $tableStart . $tableHead . $headerGridStart . "0" . $headerGridEnd . $flds . $end . $tableEnd."</div>";
 		}else{
 			return $script . $temp . $fldCnt . $tableStart . $tableHead .  $fvals . $end . $tableEnd."</div>";
 		}
	}
	*/
	return $v;
}	
	
echo json_encode($output);
exit();

?>