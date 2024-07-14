<?php
    $FormID = isset($_GET['FormID']) ? $_GET['FormID'] : 0;
    $whereCon = isset($_GET['whereCon']) ? " where " . $_GET['whereCon'] : "";
    include_once('../dbClass.php');
	include '../model.php';
	$viewvals = array();
	$sqlfields = $db[0].".". $db[1];
	$sqljoin = "";
	for($i = 0; $i < sizeof($viewcode); $i++){
		if($viewcode[$i][0] >= 0) {			//IGNORING VALUES LIKE SR.No. Edit & Delete
			if((int)$viewcode[$i][4] < 0){		//JOINING FOR VALUE FROM DB
				for($j = 0;$j < sizeof($extradb); $j++){		//echo "IN <br />" . $viewcode[$i][0];
					if($extradb[$j][0] == $viewcode[$i][4]){
						$sqlfields .= " , IFNULL(".$serials[$j].".".$extradb[$j][3].", '-') as ".$viewcode[$i][3].$serials[$j] . " , " . $serials[$j].".".$extradb[$j][2] . " as id" . $serials[$j];
						$sqljoin .= " LEFT JOIN ".$extradb[$j][1]." ".$serials[$j]." ON ".$db[0].".".$viewcode[$i][3]." = ".$serials[$j].".".$extradb[$j][2] ;
						break;
						/*
						$sqlfields .= " , IFNULL(".$serials[$j].".".$extradb[$j][3].", '-') as ".$viewcode[$i][3].$serials[$j];
						$sqljoin .= " LEFT JOIN ".$extradb[$j][1]." ".$serials[$j]." ON ".$db[0].".".$viewcode[$i][3]." = ".$serials[$j].".".$extradb[$j][2] ;
						break;
						*/
					}
				}
			}else{
				$sqlfields .= " , " . $db[0] . "." . $viewcode[$i][3];
			}
		}
	}
	
	//$sql = "SELECT ".$sqlfields." FROM ".$db[0]." " .$sqljoin . " ORDER BY ".$db[1]." DESC";
	if(strlen($db[2]) <= 1) $db[2] = " ORDER BY ".$db[1]." DESC";
   $sql = "SELECT ".$sqlfields." FROM ".$db[0]." " .$sqljoin . " " . $whereCon . " " . $db[2];
  
	$result = db::getInstance()->db_select($sql);
// echo $sql;
// print("<pre>".print_r($result,true)."</pre>");
// print("<pre>".print_r($db,true)."</pre>");
// print("<pre>".print_r($viewcode,true)."</pre>");
// exit();	
// 	$response_f['data'] = $result;
    $response_f['header'] = $viewcode;
	
// 	 echo json_encode($response_f);	
	//$serials[((-1 * $viewcode[$j][4]) - 1)]
	//[4] => -1
// 	exit();
	
	$vals = array();
// 	$k_table_headings = "<tr>";
// 	for($i=0;$i<sizeof($viewcode); $i++){
// 		$k_table_headings .= '<th>'.$viewcode[$i][1].'</th>';
// 	}
// 	$k_table_headings .= '</tr>';
// 	$k_table_body='';
	for($i = 0; $i < sizeof($result["result_set"]); $i++){
// 		$k_table_body .= '<tr>';
		$ed = $result["result_set"][$i][$db[1]];
		
		$vals[$i] = array();
		$vals[$i]["ID"] = $ed;
		for($j = 0;$j < sizeof($viewcode); $j++){
		    //echo "<br />" . $viewcode[$j][0];
			if($viewcode[$j][0] >= 0) {			//IGNORING VALUES LIKE SR.No. Edit & Delete
				if($viewcode[$j][4] < 0){		//Extra DB
					$varName = $viewcode[$j][3].$serials[(((int)-1 * (int)$viewcode[$j][4]) - 1)];
				}else{
					$varName = $viewcode[$j][3];
				}
				$vals[$i][$viewcode[$j][1]] = $result["result_set"][$i][$varName];	
			}else{
				if($viewcode[$j][0] == -4){ //ACTION
					
				}
				if($viewcode[$j][0] == -1){ //Serial Number
					$vals[$i]["Sr. No."] = $i + 1 ; 
				}
				if($viewcode[$j][0] == -2){	//EDIT 	
				}
				if($viewcode[$j][0] == -3){	//DELETE 	
					$viewlink = "";
					if(isset($viewcodeextra)){
						for($p = 0; $p < sizeof($viewcodeextra); $p++){
							if($viewcodeextra[$p][0] == -3){
								$viewlink = $viewcodeextra[$p][1];
								break;
							}
						}
					}
					$x = '<form action="' . $viewlink . '?view='.$ed.'" method="POST">
							<input type="hidden" name="viewID" value='.$ed.' />
							<button class="btn btn-primary bizbtn" style="font-size;10px;" type="submit" data-toggle="tooltip" data-placement="bottom" title="view" ><i class="fa fa-eye"></i></button>
						 </form>' ; 
				}
			}
		}
	}
// 	print("<pre>".print_r($vals,true)."</pre>");
    $response_f['data'] = $vals;
    echo json_encode($response_f);
?>