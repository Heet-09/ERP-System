<?php

// if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {

//   header('Method Not Allowed', true, 405);

//   echo "This method requests are not accepted for this resource";

//   exit;

// }





// FOR VAPT

session_start(); 

 $token = filter_input(INPUT_POST, 'csrftoken', FILTER_SANITIZE_STRING);

// echo "<br />" . $_SESSION['csrftoken'];

if (!$token || $token !== $_SESSION['csrftoken']) {

    // return 405 http status code

    header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');

    exit;

}









$FormID = isset($_POST['FormID']) ? $_POST['FormID'] : 0;

if($FormID == 0){

	echo "No Model Exists.";

	exit();

}

include 'model.php'; 







function printDebug($k_debug, $var, $title){

	if($k_debug > 0){

		echo "<br />" . $title . ": ";

		if($k_debug == 2){ 

			print"<pre>";

			print_r($var);

			print"</pre>";

		}else

			print_r($var);

		echo "<br />";

	}

}



$editID = isset ($_POST['editID']) ? $_POST['editID'] : 0; 

$user_id = isset ($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if($user_id == 0){

	//REDIRECT TO LOGOUT AS USER ID NOT AVAILABLE IN SESSION

}else{

	$_POST['created_by'] = $user_id;

}



if($k_debug) print_r($_POST);

// printDebug(2, $_POST, "POST VARIABLE");

echo "<br/><br/>";



if($k_debug) print_r($code);

echo "<br/><br/>"; 



if($k_debug) print_r($_FILES);

// printDebug(2, $_FILES, "Attachments/ Files");

echo "<br/><br/>"; 



//if($k_debug) print_r($dynamix);

printDebug(2, $dynamix, "Dynamix Grid");

echo "<br/><br/>"; 

//  exit();

/*

ADD & EDIT FOR ALL

	Image				Gallery		

1	Enable				Disable				

2	Disable				Enable				

3	No Data				No Data				

4	Data				No Data				

5	No Data				Data				

6	Data				Data				

7	Multiple Enabled	Disable		

						if only 1 image

*/



/**************MEDIA****************/

if(isset($_FILES)){

	$mediaFields = array();

	$mf = 0;

	$atname="";

	//FOR MEDIA TYPE 12

	for($i = 0; $i<sizeof($code); $i++){

		if($code[$i][1] == 12){

			$atname = $code[$i][0];

			$mediaFields[$mf] = $_FILES[$code[$i][0]];

			$mediaFields[$mf]["CodeArray"] = $i;

			$mediaFields[$mf]["MediaId"][0] = 0;

			

			//Other Attributes added on 18 01 2021 

			$mediaFields[$mf]["folder"] = "img/"; 

			if($k_debug) echo "<br />OTHER Attributes: " . $code[$i][8] . "<br />";

			$att_value = extractAttribute($code[$i][8], "IMAGEPATH");

			if(strlen($att_value) > 0 && $att_value != null) $mediaFields[$mf]["folder"] = $att_value;

			//Other Attributes added on 18 01 2021

			

			//MODIFIED 19 03 2021 for multiple image controls on one page

			if(isset($_FILES[$atname])){

				if($_FILES[$atname]["size"][0] > 0){

					//echo "MF-".$mf;

					//print_r($mediaFields);

					$z = $mf;

					//for($z = 0; $z <= $mf; $z++){	

						for($j=0; $j<count($mediaFields[$z]['name']); $j++){ //ARRAY for Multiple Files Uploaded in same input

							//$target_dir = "img/";

							$target_dir = SITE_ROOT . $mediaFields[$z]["folder"];

							$imageFileType = strtolower(pathinfo($mediaFields[$z]["name"][$j],PATHINFO_EXTENSION));

							$imageFileName = basename($mediaFields[$z]["name"][$j],".".$imageFileType);

							$imageSize = $mediaFields[$z]["size"][$j];

							//$check = getimagesize($mediaFields[$z]["tmp_name"]);

							if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "csv" && $imageFileType != "xlsx" ) { 

								//Allow certain file formats (use strcmp here)

								echo "<script> alert('Sorry, only JPG, JPEG, PNG, CSV, XLSX & GIF files are allowed.');</script>";

								// echo '<script>window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';

								exit();

							}

							$existCnt = 0;

							$existCheck = "";

							recheck:

								if($existCnt > 0) 	$existCheck = " (" . $existCnt . ")";

								$target_file = $target_dir . $imageFileName . $existCheck . "." . $imageFileType ;

								//if($k_debug) echo "<br />" . $target_file;

								if (file_exists($target_file)) { // Check if file already exists

									$existCnt++;

									goto recheck;

								}else{

									if($k_debug) echo "<br />CHECKED Duplicates & file adjusted to- " . $target_file;

								}

							if($k_debug) echo "<br />" .  "Starting Move File...";

							if(move_uploaded_file($mediaFields[$z]["tmp_name"][$j], $target_file)){

								if($k_debug) echo "<br />The file ". basename($mediaFields[$z]["name"][$j]). " has been uploaded.";

								$mediaFields[$z]["name"][$j] = basename($target_file);

								

								/****Insert Media****/

								$set = array();

								$val = array();

								$k = 0;

								$set[$k] = "MediaName";

								$val[$k++] = $mediaFields[$z]['name'][$j];

								$set[$k] = "MediaType";

								$val[$k++] = strtolower(pathinfo($mediaFields[$z]["name"][$j],PATHINFO_EXTENSION));

								$set[$k] = "MediaFolder";

								$val[$k++] = $mediaFields[$z]["folder"];

								$result = db::getInstance()->db_insert("kmainmedia",$set,$val);

								if($k_debug) echo "<br /><br />The uploaded file has been saved to DB- ";

								if($k_debug) print_r($result); 

								$mediaFields[$z]['MediaId'][$j] = $result['last_id'];

								/*******/

							}else{

								echo "<script> alert('Sorry, there was an error uploading your file.');</script>";

								// echo '<script>window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';

								exit();

							}

						}

					//}

				}

			}else{

				$mediaFields[$i]['MediaId'][$j] = 0;

			}

			$mf++;

		}

	}  

	//FOR MEDIA TYPE 13

	$atname="";

	for($i = 0; $i<sizeof($code); $i++){

		//$mf = 0;

	    if($code[$i][1] == 13 ){

			//echo "<br />-*-*-*-*-*-*-*-*-*-*- STARTING 13 -*-*-*-*-*-*-*-*-*-<br />";

			$atname = $code[$i][0];

			$mediaFields[$mf] = $_FILES[$code[$i][0]];

			$mediaFields[$mf]["CodeArray"] = $i;

			$mediaFields[$mf]["MediaId"][0] = 0;			

			$mediaFields[$mf]["folder"] = "img/"; 

			if($k_debug) echo "<br />OTHER Attributes: " . $code[$i][8] . "<br />";

			$att_value = extractAttribute($code[$i][8], "IMAGEPATH");

			if(strlen($att_value) > 0 && $att_value != null) $mediaFields[$mf]["folder"] = $att_value;

			

			if(isset($_FILES[$atname])){

				if($_FILES[$atname]["size"][0] > 0){

					//echo "start Media Upload? what are u doin";

					//echo "MF-".$mf;

					//print_r($mediaFields);

					$z = $mf;

					//for($z = 0; $z <= $mf; $z++){	

						for($j=0; $j<count($mediaFields[$z]['name']); $j++){ //ARRAY for Multiple Files Uploaded in same input

							//$target_dir = "img/";

							$target_dir = SITE_ROOT . $mediaFields[$z]["folder"];

							$imageFileType = strtolower(pathinfo($mediaFields[$z]["name"][$j],PATHINFO_EXTENSION));

							$imageFileName = basename($mediaFields[$z]["name"][$j],".".$imageFileType);

							$imageSize = $mediaFields[$z]["size"][$j];

							//$check = getimagesize($mediaFields[$z]["tmp_name"]);

							if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "csv" && $imageFileType != "xlsx" ) { 

								//Allow certain file formats (use strcmp here)

								echo "<script> alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";

								echo '<script>window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';

								exit();

							}

							$existCnt = 0;

							$existCheck = "";

							recheck13:

								if($existCnt > 0) 	$existCheck = " (" . $existCnt . ")";

								$target_file = $target_dir . $imageFileName . $existCheck . "." . $imageFileType ;

								//if($k_debug) echo "<br />" . $target_file;

								if (file_exists($target_file)) { // Check if file already exists

									$existCnt++;

									goto recheck13;

								}else{

									if($k_debug) echo "<br />CHECKED Duplicates & file adjusted to- " . $target_file;

								}

							if($k_debug) echo "<br />" .  "Starting Move File...";

							if(move_uploaded_file($mediaFields[$z]["tmp_name"][$j], $target_file)){

								if($k_debug) echo "<br />The file ". basename($mediaFields[$z]["name"][$j]). " has been uploaded.";

								$mediaFields[$z]["name"][$j] = basename($target_file);

								

								/****Insert Media****/

								$set = array();

								$val = array();

								$k = 0;

								$set[$k] = "MediaName";

								$val[$k++] = $mediaFields[$z]['name'][$j];

								$set[$k] = "MediaType";

								$val[$k++] = strtolower(pathinfo($mediaFields[$z]["name"][$j],PATHINFO_EXTENSION));

								$set[$k] = "MediaFolder";

								$val[$k++] = $mediaFields[$z]["folder"];

								$result = db::getInstance()->db_insert("kmainmedia",$set,$val);

								if($k_debug) echo "<br /><br />The uploaded file has been saved to DB- ";

								if($k_debug) print_r($result); 

								$mediaFields[$z]['MediaId'][$j] = $result['last_id'];

								/*******/

							}else{

								echo "<script> alert('Sorry, there was an error uploading your file.');</script>";

								echo '<script>window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';

								exit();

							}

						}

					//}

				}

			}else{

				$mediaFields[$i]['MediaId'][$j] = 0;

			}

			$mf++;

		}

	}  

	

	//FOR GRID MEDIA TYPE 12

	$imageInGrid = array();

    $igCnt = 0;

    $POSTgrid = array();

    for($i=0; $i < sizeof($dynamix); $i++){ //iterate to number of GRIDS

        $snglGrid = $dynamix[$i][0];

        $gridAlphabet = $dynamix[$i][1][3];

        for($m = 0; $m < sizeof($snglGrid); $m++){  //iterate to the grid field

            if($snglGrid[$m][0] == 12){             //check if field is an image

                $imageInGrid[$igCnt] = array();

                $imageInGrid[$igCnt][0] = $snglGrid[$m][1];     //Name of field

                $imageInGrid[$igCnt][1] = $gridAlphabet;        //Alphabet for accessing

                

                echo "<br /><br/>" . $key = $imageInGrid[$igCnt][1] . $imageInGrid[$igCnt][0];

                $POSTgrid[$key] = array();

                if(array_key_exists($key, $_FILES)){

                    $temp = array();

                    for($j = 0; $j < sizeof($_FILES[$key]["error"]); $j++){ //if no error then image exist

                        $POSTgrid[$key][$j] = 0; 

                        $temp[$j]=0;

                        if($_FILES[$key]["error"][$j] == 0){  

                            //STORE IMAGE AND SAVE IN DATABASE

                            if($_FILES[$key]["size"][$j] > 0){

                                if($k_debug) echo "<br /> Checking => " . $_FILES[$key]["name"][$j];

                                $target_dir = "img/";

            					$target_dir = SITE_ROOT . $target_dir;

            					$imageFileType = strtolower(pathinfo($_FILES[$key]["name"][$j],PATHINFO_EXTENSION));

            					$imageFileName = basename($_FILES[$key]["name"][$j],".".$imageFileType);

            					$imageSize = $_FILES[$key]["size"][$j];

            					if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "csv" && $imageFileType != "xlsx" ) {   //Allow certain file formats (use strcmp here)

            						echo "<script> alert('Sorry, only JPG, JPEG, PNG, CSV, XLSX & GIF files are allowed.');</script>";

            						exit();

            					}

            					$existCnt = 0;

            					$existCheck = "";

            					recheck2:

            						if($existCnt > 0) 	$existCheck = " (" . $existCnt . ")";

            						$target_file = $target_dir . $imageFileName . $existCheck . "." . $imageFileType ;

            						if (file_exists($target_file)) { // Check if file already exists

            							$existCnt++;

            							goto recheck2;

            						}else{

            							if($k_debug) echo "<br />CHECKED Duplicates & file adjusted to- " . $target_file;

            						}

            					if($k_debug) echo "<br />Uploading file ". $target_file . "...";

            					if(move_uploaded_file($_FILES[$key]["tmp_name"][$j], $target_file)){

            						if($k_debug) echo "<br />The file ". basename($_FILES[$key]["name"][$j]). " has been uploaded as " . $target_file;

            						$_FILES[$key]["name"][$j] = basename($target_file);

            						

            						/****Insert Media****/

                						$set = array();

                						$val = array();

                						$k = 0;

                						$set[$k] = "MediaName";

                						$val[$k++] = $_FILES[$key]['name'][$j];

                						$set[$k] = "MediaType";

                						$val[$k++] = strtolower(pathinfo($_FILES[$key]["name"][$j],PATHINFO_EXTENSION));

                						$set[$k] = "MediaFolder";

                						$val[$k++] = "img/";

                						$result = db::getInstance()->db_insert("kmainmedia",$set,$val);

                						if($k_debug) echo "<br /><br />The uploaded file has been saved to DB- ";

                						if($k_debug) print_r($result); 

                						$dynamix[$i][1][4] = $POSTgrid[$key][$j] = $temp[$j] = $result['last_id'];

            						/*******/

            					}else{

            					    echo move_uploaded_file($_FILES[$key]["tmp_name"][$j], $target_file);

            						echo "<script> alert('Sorry, there was an error uploading your file.');</script>";

            						exit();

            					}

            				}

                        }

                    }

                    $_POST[$key] = $temp;

                }

                $igCnt++;

            }

        }

    }

    // print_r($imageInGrid);

    // echo "<br/><br/>"; 

    // printDebug(2, $POSTgrid, "POST Image Grid");

}

// printDebug(2, $_POST, "POST VARIABLE");

// echo "<br/><br/>";

// exit();

/**************END MEDIA***************/

    if($k_debug) print_r($mediaFields); 

	//echo "<pre>";



	$many2many = array(); $m=0;

	$m2mdb = array(); $mdb=0; $m2mdbtype = array();

if($editID > 0){

	//EDIT

	$separator = "";

	$sql = "UPDATE ".$db[0]." SET ";//`".$code[0][0]."` = '". mysql_real_escape_string($_POST[$code[0][0]]) ."'";

	for($i = 0; $i<sizeof($code); $i++){

		if($code[$i][1] == 14 || $code[$i][7] == 0){ //If Grid or If Display order = 0

		

		}else{

			if($code[$i][1] == 10){	//FOR MANY TO MANY MAPPING

				$many2many[$m++] = $i;

			}else{

				if($code[$i][1] == 11 || $code[$i][1] == 13){ //FOR MANY TO MANY MAPPING IN OTHER TABLE & for Multiple Images

					$m2mdbtype[$mdb] = $code[$i][1];

					$m2mdb[$mdb++] = $i;

					if($code[$i][1] == 13){

						for($l = 0; $l<$mf; $l++){	

							if($mediaFields[$l]['CodeArray'] == $i){

							    if($mediaFields[$l]['MediaId'] > 0)	

							        $_POST[$code[$i][0]] = $mediaFields[$l]['MediaId'];

								break;

							}

						}

					}

				}else{ //ALL OTHER DO GO IN MAIN DATABASE TABLE OF THE MODEL

				    if($code[$i][1] == 12){ //SINGLE IMAGE UPLOAD

						/*

						$val[$k] = 0;

						for($l = 0; $l<$mf; $l++){	

							if($mediaFields[$l]['CodeArray'] == $i){

								$val[$k] = $mediaFields[$l]['MediaId'][0];

								break;

							}

						}

						*/

						//$val[$k] = 0; //commented due to error when no Media is passed

						for($l = 0; $l<$mf; $l++){

							if($mediaFields[$l]['CodeArray'] == $i){								

								//$val = isset($_POST[$code[$i][0]]) ? ($_POST[$code[$i][0]]) : "";

            					//$val = db::getInstance()-> real_escape_string($val);

								if($mediaFields[$l]['MediaId'][0] > 0){

									$sql .= $separator." ".$code[$i][0]." = '". $mediaFields[$l]['MediaId'][0] ."' ";

									$separator = ",";

								}

								break;

							}

						}

					}else{

	                    $val = isset($_POST[$code[$i][0]]) ? ($_POST[$code[$i][0]]) : "";

    					$val = str_replace("<script>","",$val);    //ADDED FOR VAPT 

	                    $val = str_replace("</script>","",$val);   //ADDED FOR VAPT 

	                    $val = str_replace("</","",$val);          //ADDED FOR VAPT 

	                    $val = str_replace("><","",$val);          //ADDED FOR VAPT 

	                    $val = str_replace("onerror","",$val);     //ADDED FOR VAPT 

	                   // \"><img src=x onerror=alert(\"Hacked\")>

    					// $val = db::getInstance()-> real_escape_string($val);
						$val = db::getInstance()->real_escape_string($val);

    					$sql .= $separator." ".$code[$i][0]." = '". $val ."' ";

    					$separator = ",";

					}

				}

			}

		}

	}

	$sql = $sql . " WHERE ".$db[1]." = ".$editID;

	if($k_debug) echo '<br/>CD108: '.$sql.'<br/>';

	$result = db::getInstance()->db_update($sql);	

	if($k_debug) print_r($result);

	

	

	printDebug($k_debug, $dynamix, "Dynamix");

	if(sizeof($dynamix) > 0){

		$dbPK = array();

		for($i = 0; $i < sizeof($dynamix); $i++){

			//echo "<br/>************************************************<br/>";

			$db = $dynamix[$i][1][0];

			$fk = $dynamix[$i][1][1];

			$pk = $dynamix[$i][1][5];

			$sql = 'SELECT '.$pk.' FROM '. $db.' WHERE '. $fk . ' = '. $editID;

			printDebug($k_debug, $sql, "CD108 in Dynamix block: ");

			$result = db::getInstance()->db_select($sql);

			printDebug($k_debug, $result, "CD108 Dynamix Result: ");

			for($j = 0; $j < $result['num_rows']; $j++){

				$dbPK[$i][$j] = $result['result_set'][$j][$pk];

			}

			$cntDB = $result['num_rows'];

			$cntFn = $_POST['cnt'.$dynamix[$i][1][3]] - 1;

			if($k_debug) echo "<br />DBcnt: " . $cntDB . "<br />";

			if($k_debug) echo "<br />FNcnt: " . $cntFn . "<br />";

		

			//print_r($dbPK);

			$t1 = array(); $cntt1 = 0; 

			$t2 = array(); $cntt2 = 0;			

			$t1[$cntt1++] = $pk;

			

			for($j = 0; $j < sizeof($dynamix[$i][0]); $j++){ //LOOPING ON FIELDS

				$t1[$cntt1++] = $dynamix[$i][0][$j][1];

			}

			for($j = 0; $j < ($_POST['cnt'.$dynamix[$i][1][3]]); $j++){ //LOOPING ON NUMBER OF POSTS PASSED BY FORM

				if(isset($_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]][$j])){

					if(strlen($_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]][$j]) > 0){

					    $t2[$cntt2] = array();

				        $ct2 = 0;

					    $t2[$cntt2][$ct2] = db::getInstance()->real_escape_string($_POST[$dynamix[$i][1][3].$dynamix[$i][0][$k][1]][$j]);

					    for($k = 0; $k < sizeof($dynamix[$i][0]); $k++){	//LOOPING NUMBER OF FIELDS IN THE DYNAMIC

					        if($dynamix[$i][0][$k][0] == 12){   //FOR MEDIA IN GRID

					            if($_POST[$dynamix[$i][1][3].$dynamix[$i][0][$k][1]][$j] == 0){

					                $sql4 = 'SELECT ' . $dynamix[$i][0][$k][1] . ' FROM '. $db.' WHERE '. $t1[0] . ' = '. $t2[$cntt2][0];

					                if($k_debug)    echo "<br/><br/>Check Existing Image Grid - " . $sql4;

                    			    $result4 = db::getInstance()->db_select($sql4);

                    			    if($result4['num_rows'] > 0)  $t2[$cntt2][$ct2++] = $result['result_set'][0][$dynamix[$i][0][$k][1]];

					            }

					        }else{

					            $t2[$cntt2][$ct2++] = db::getInstance()->real_escape_string($_POST[$dynamix[$i][1][3].$dynamix[$i][0][$k][1]][$j]);

					        }

					    }

					    $cntt2++;
						array_unshift($t2[$j], $dbPK[0][$j]);
					}

				}

			}

			if($k_debug){ echo "<br/><br/>T1: "; print_r($t1); }

			if($k_debug){ echo "<br/><br/>T2: "; print_r($t2); }

			if($cntDB == 0){ $cntDB = -1; }

			if($cntDB == $cntFn){ //Same number of COUNT of GRID

				//for($i = 0; $i < sizeof($dynamix); $i++){ //LOOPING ALL THE DYNAMIC ARRAYS					

				for($h = 0; $h < sizeof($t2); $h++){ //LOOPING thru num of entries

					$sql = "UPDATE ". $db . " SET ";

					for($j = 1; $j < sizeof($t1); $j++){ //LOOPING ON FIELDS

						$sql .= $t1[$j] . " = '" . $t2[$h][$j]. "',";

					}
					
					$sql = substr($sql, 0, -1) . " WHERE " . $t1[0] . " = " . $t2[$h][0];

					if($k_debug) echo '<br/>CD107: '.$sql.'<br/>';

					$result = db::getInstance()->db_insertQuery($sql);

					if($k_debug) print_r($result);

				}

				//}

			}else{

				if($cntDB < $cntFn){ //WHEN NEW FIELD IS ADDED

					/*for($i = 0; $i < sizeof($dynamix); $i++){ //LOOPING ALL THE DYNAMIC ARRAYS

						$db = $dynamix[$i][1][0];

						$fk = $dynamix[$i][1][1];

						$pk = $dynamix[$i][1][5];

						

						$t1 = array(); $cntt1 = 0; 

						$t2 = array(); $cntt2 = 0;

						

						$t1[$cntt1++] = $pk;

						for($j = 0; $j < sizeof($dynamix[$i][0]); $j++){ //LOOPING ON DB FIELDS

							$t1[$cntt1++] = $dynamix[$i][0][$j][1];

						}

						for($j = 0; $j < ($_POST['cnt'.$dynamix[$i][1][3]]); $j++){ //LOOPING ON NUMBER OF POSTS PASSED BY FORM

							if(isset($_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]][$j])){

								if(strlen($_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]][$j]) > 0){

								  $t2[$cntt2] = array();

								  $ct2 = 0;

								  $t2[$cntt2][$ct2++] = $dbPK[$i][$j] ;

								  for($k = 0; $k < sizeof($dynamix[$i][0]); $k++){	//LOOPING NUMBER OF FIELDS IN THE DYNAMIC

									$t2[$cntt2][$ct2++] = db::getInstance()->real_escape_string($_POST[$dynamix[$i][1][3].$dynamix[$i][0][$k][1]][$j]);

								  }

								  $cntt2++;

								}

							}

						}

						if($k_debug){ echo "<br/><br/>T1: "; print_r($t1); }

						if($k_debug){ echo "<br/><br/>T2: "; print_r($t2); }

						*/

					for($h = 0; $h < sizeof($t2); $h++){ //LOOPING thru num of entries

						if($t2[$h][0] > 0){ //Primary key from DB present or not

							$sql = "UPDATE ". $db . " SET ";

							for($j = 1; $j < sizeof($t1); $j++){ //LOOPING ON FIELDS

								$sql .= $t1[$j] . " = '" . $t2[$h][$j]. "',";

							}

							$sql = substr($sql, 0, -1) . " WHERE " . $t1[0] . " = " . $t2[$h][0];

							if($k_debug) echo '<br/>CD107: '.$sql.'<br/>';

							$result = db::getInstance()->db_insertQuery($sql);

							if($k_debug) print_r($result);

						}else{ //NEW ENTRIES

							$sql = "INSERT INTO ". $db . " (" . $fk . ",";

							for($j = 1; $j < sizeof($t1); $j++){ //LOOPING ON FIELDS

								//$sql .= $t1[$j] . " = '" . $t2[$h][$j]. "',";

								$sql .= $t1[$j] . ",";

							}

							$sql = substr($sql, 0, -1) . ") VALUES (" . $editID . ",";

							for($j = 1; $j < sizeof($t1); $j++){ //LOOPING ON FIELDS

								$sql .= "'" . $t2[$h][$j]. "',";

							}

							$sql = substr($sql, 0, -1) . ")";

							if($t2[0][1] != 0){
								if($k_debug) echo '<br/>CD107: '.$sql.'<br/>';

								$result = db::getInstance()->db_insertQuery($sql);

								if($k_debug) print_r($result);
							}

						}

					}

					//}

				}else{	//WHEN FIELD IS DELETED

					$deleteTemp = ""; $deleteSeparator = "";

					for($h = 0; $h < sizeof($t2); $h++){ //LOOPING thru num of entries

						if($t2[$h][0] > 0){ //Primary key from DB present or not

							$sql = "UPDATE ". $db . " SET ";

							for($j = 1; $j < sizeof($t1); $j++){ //LOOPING ON FIELDS

								$sql .= $t1[$j] . " = '" . $t2[$h][$j]. "',";

							}

							$sql = substr($sql, 0, -1) . " WHERE " . $t1[0] . " = " . $t2[$h][0];

							if($k_debug) echo '<br/>CD107 Del: '.$sql.'<br/>';

							$result = db::getInstance()->db_insertQuery($sql);

							if($k_debug) print_r($result);

							//DELETE REST of ENTRIES

							$deleteTemp .= $deleteSeparator . $t1[0] . " != " . $t2[$h][0];

							$deleteSeparator = " AND ";

							//if($k_debug) echo '<br/>Deleting Extras: '.$deleteTemp.'<br/>';

						}

					}

					if(strlen($deleteTemp) > 0){

						$sql = "DELETE FROM ". $db . " WHERE (" . $deleteTemp . ") AND " . $fk . " = " . $editID;

						if($k_debug) echo '<br/>CD107: '.$sql.'<br/>';

						$result = db::getInstance()->db_update($sql);

						if($k_debug) print_r($result);

					}

				}

			}

			//echo "<br/>************************************************<br/>";

		}

	}

	

	//CD104 	//DELETE ALL MANY TO MANY ENTRIES

	for($i = 0; $i < $m; $i++){	

		$mapArray = $extradb[((-1)*$code[$many2many[$i]][3])-1][5];

		$mapTable = $mapArray[0];

		$mapIndex = $mapArray[1];

		$mapVariant = $mapArray[2];

		$mapValues = $_POST[$code[$many2many[$i]][0]];

		for($j = 0; $j < sizeof($mapValues); $j++){		

			if($k_debug) echo '<br/> CD104: '. $sql = 'DELETE FROM '. $mapTable.' WHERE '. $mapIndex . ' = '. $editID;

			$result = db::getInstance()->db_update($sql);

		}

		//print_r($mapValues); exit();

	}

	

	//CD105		//DELETE ALL MANY TO MANY ENTRIES IN MAPPING TABLE (number 11)

	for($i = 0; $i < $mdb; $i++){

	    if($m2mdbtype[$i] == 11){

    		$mapArray = $extradb[((-1)*$code[$m2mdb[$i]][3])-1];

    		$mapTable = $mapArray[6];

    		$mapIndex = $mapArray[7];

    		$mapVariant = $mapArray[8];

    		$mapValues = $_POST[$code[$m2mdb[$i]][0]];

    		//echo "<br />";

    		//print_r($mapValues);

    		//echo "<br />";

    		//print_r($mapArray);

    		//print_r($mapValues);

    		// commented as required to delete only once for($j = 0; $j < sizeof($mapValues); $j++){		

    			$sql = 'DELETE FROM '. $mapTable.' WHERE '. $mapIndex . ' = '. $editID;

    			if($k_debug)  echo '<br/>CD105:'. $sql;

    			$result = db::getInstance()->db_update($sql);

    			if($k_debug) print_r($result);

    		//}

    		//print_r($mapValues); exit();

	    }

	}

	

	//CD106		//DELETE ALL MOREDB ENTRIES

	/*for($i = 0; $i < sizeof($dynamix); $i++){

		$db = $dynamix[$i][1][0];

		$fk = $dynamix[$i][1][1];

		$sql = 'DELETE FROM '. $db.' WHERE '. $fk . ' = '. $editID;

		if($k_debug) echo '<br/>CD106: '.$sql.'<br/>';

		//$result = db::getInstance()->db_update($sql);

		//if($k_debug) print_r($result);

	}*/

	

	//CD101  //CODE FOR MULTIPLE MANY TO MANY RELATION MAPPING TABLE CODE 10

	for($i = 0; $i < $m; $i++){	

		$mapArray = $extradb[((-1)*$code[$many2many[$i]][3])-1][5];

		$mapTable = $mapArray[0];

		$mapIndex = $mapArray[1];

		$mapVariant = $mapArray[2];

		$mapValues = $_POST[$code[$many2many[$i]][0]];

		for($j = 0; $j < sizeof($mapValues); $j++)	{	

			if($k_debug) echo "<br/>CD101: INSERT INTO ". $mapTable.$mapTable . $mapIndex . $mapVariant . $editID . $mapValues[$j];

			$result = db::getInstance()->db_insert($mapTable, array($mapIndex, $mapVariant),array($editID, $mapValues[$j]));

			if($k_debug) print_r($result);

		}

	}

	

	//CD102		//CODE FOR MULTIPLE MANY TO MANY RELATION MAPPING TABLE CODE 11 & 13

	for($i = 0; $i < $mdb; $i++){	 //SAMPLE USED IN model-vendor.php

		$mapArray = $extradb[((-1)*$code[$m2mdb[$i]][3])-1];

		$mapTable = $mapArray[6];

		$mapIndex = $mapArray[7];

		$mapVariant = $mapArray[8];

		$mapValues = $_POST[$code[$m2mdb[$i]][0]];

		//echo "<br />";

		//print_r($mapValues);

		//echo "<br />";

		//print_r($mapArray);

		for($j = 0; $j < sizeof($mapValues); $j++){

		    if($mapValues[$j] > 0){

    			if($k_debug)

    				echo "<br/>CD102:	INSERT INTO " . $mapTable ." (" . $mapIndex .",". $mapVariant .") VALUES(" . $editID .",". $mapValues[$j].")";

    			$result = db::getInstance()->db_insert($mapTable, array($mapIndex, $mapVariant),array($editID, $mapValues[$j]));

    			if($k_debug) print_r($result);

		    }

		}

	}

	

	//CD103 	//COMMON DYNAMIC FIELDS INSERT

	/*for($i = 0; $i < sizeof($dynamix); $i++){ //LOOPING ALL THE DYNAMIC ARRAYS

		$db = $dynamix[$i][1][0];

		$fk = $dynamix[$i][1][1];

		$sql = "INSERT INTO ". $db . " (".$fk."";

		for($j = 0; $j < sizeof($dynamix[$i][0]); $j++){ //LOOPING ON FIELDS

			$sql .= "," . $dynamix[$i][0][$j][1];

		}

		$sql = $sql . " ) VALUES";

		$vals = "";

		for($j = 0; $j < ($_POST['cnt'.$dynamix[$i][1][3]]); $j++){ //LOOPING ON NUMBER OF POSTS PASSED BY FORM

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

		//if($k_debug) echo '<br/>CD103: '.$sql.'<br/>';

		//$result = db::getInstance()->db_insertQuery($sql);

		//if($k_debug) print_r($result);

	}*/

	

	//exit();

}else{

	//INSERT	

	$set = array();

	$val = array();

	$k = 0;

	//print_r($code); 

	for($i = 0; $i<sizeof($code); $i++){

		if($code[$i][1] == 14 || $code[$i][7] == 0){ //If Grid or If Display order = 0

		

		}else{

			if($code[$i][1] == 10){ //FOR MANY TO MANY MAPPING 

				$many2many[$m++] = $i;

			}else{

			    //FIELD 9 MULTICHECKBOX ADDED FOR CAPAPI

				if($code[$i][1] == 9 || $code[$i][1] == 11 || $code[$i][1] == 13){ //FOR MANY TO MANY MAPPING IN OTHER TABLE & for Multiple Images

					$m2mdb[$mdb++] = $i;

					if($code[$i][1] == 13){

						for($l = 0; $l<$mf; $l++){	

							if($mediaFields[$l]['CodeArray'] == $i){

								$_POST[$code[$i][0]] = $mediaFields[$l]['MediaId'];

								break;

							}

						}

					}

				}else{

					if($code[$i][1] == 12){ //SINGLE IMAGE UPLOAD

						if($k_debug) echo "<br /><br />*-*-*-*-*-*-" . $i;

						$val[$k] = 0;

						for($l = 0; $l<$mf; $l++){

							//if($k_debug) echo "<br />" . $mediaFields[$l]['CodeArray'];

							if($mediaFields[$l]['CodeArray'] == $i){

								$val[$k] = $mediaFields[$l]['MediaId'][0];

								break;

							}

						}

					}else{
					 
						// $val[$k] = isset($_POST[$code[$i][0]]) ? db::getInstance()->real_escape_string($_POST[$code[$i][0]]) : "";

						$val[$k] = isset($_POST[$code[$i][0]]) ? db::getInstance()->real_escape_string($_POST[$code[$i][0]]) : "";

					    $val[$k] = str_replace("<script>", "",$val[$k]);      //ADDED FOR VAPT 

	                    $val[$k] = str_replace("</script>","",$val[$k]);      //ADDED FOR VAPT

	                    $val[$k] = str_replace("</","",$val[$k]);          //ADDED FOR VAPT 

	                    $val[$k] = str_replace("><","",$val[$k]);          //ADDED FOR VAPT 

	                    $val[$k] = str_replace("onerror","",$val[$k]);     //ADDED FOR VAPT 

					}

					$set[$k] = $code[$i][0];

					$k++;

				}

			}

		}

	}

	if($k_debug) echo "INSERT INTO VALS=> ";

	if($k_debug) print_r($val);

	$result = db::getInstance()->db_insert($db[0],$set,$val);

	if($k_debug) print_r($result); 

	//exit();

	$editID = $result['last_id'];

	

	

	//CODE FOR MULTIPLE MANY TO MANY RELATION MAPPING TABLE CODE 10

	for($i = 0; $i < $m; $i++){	

		$mapArray = $extradb[((-1)*$code[$many2many[$i]][3])-1][5];

		$mapTable = $mapArray[0];

		$mapIndex = $mapArray[1];

		$mapVariant = $mapArray[2];

		$mapValues = $_POST[$code[$many2many[$i]][0]];

		for($j = 0; $j < sizeof($mapValues); $j++)	{	

			$result = db::getInstance()->db_insert($mapTable, array($mapIndex, $mapVariant),array($editID, $mapValues[$j]));

		}

	}

    // 	echo "*****************";

    // 	print_r($extradb);

    // 	echo "*****************";

    // 	print_r($mdb);



	//FIELD 9 MULTICHECKBOX ADDED FOR CAPAPI

	//CODE FOR MULTIPLE MANY TO MANY RELATION MAPPING TABLE CODE 11 & 13

	for($i = 0; $i < $mdb; $i++){	 //SAMPLE USED IN model-vendor.php

		$mapArray = $extradb[((-1)*$code[$m2mdb[$i]][3])-1];

		$mapTable = $mapArray[6];

		$mapIndex = $mapArray[7];

		$mapVariant = $mapArray[8];

// 		echo "<br />DATA: "; print_r($_POST[$code[$m2mdb[$i]][0]]);

		if(!is_array($_POST[$code[$m2mdb[$i]][0]])){ 

		    if(strlen($_POST[$code[$m2mdb[$i]][0]]) > 0)    $mapValues = explode(',', $_POST[$code[$m2mdb[$i]][0]]);

		    else $mapValues = array();

		}else { $mapValues = $_POST[$code[$m2mdb[$i]][0]]; }

// 		echo "<br />".print_r($mapArray)."<br />";

// 		echo "<br />".print_r($mapValues)."<br />";

		for($j = 0; $j < sizeof($mapValues); $j++){

			if($k_debug) echo "<br/>INSERT INTO ". $mapTable.$mapTable . $mapIndex . $mapVariant . $editID . $mapValues[$j];

			$result = db::getInstance()->db_insert($mapTable, array($mapIndex, $mapVariant),array($editID, $mapValues[$j]));

		}

	}

	

	//COMMON DYNAMIC FIELDS INSERT

	if($k_debug) echo "<br/><br/>"; 

	if($k_debug) print_r($dynamix);

	for($i = 0; $i < sizeof($dynamix); $i++){ //LOOPING ALL THE DYNAMIC ARRAYS

		$db = $dynamix[$i][1][0];
		
		$fk = $dynamix[$i][1][1];

		$sql = "INSERT INTO ". $db . " (".$fk."";

		for($j = 0; $j < sizeof($dynamix[$i][0]); $j++){ //LOOPING ON FIELDS

			$sql .= "," . $dynamix[$i][0][$j][1];

		}

		$sql = $sql . " ) VALUES";

		$vals = "";

		$dynamixflag  = false;

		for($j = 0; $j < ($_POST['cnt'.$dynamix[$i][1][3]]); $j++){ //LOOPING ON NUMBER OF POSTS PASSED BY FORM

			if(isset($_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]][$j])){

			    //echo "<br/>". $j." -".$_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]][$j];

				if(strlen($_POST[$dynamix[$i][1][3].$dynamix[$i][0][0][1]][$j]) > 0){

					$dynamixflag = true;

					$vals .= "(".$editID ;

					for($k = 0; $k < sizeof($dynamix[$i][0]); $k++){	//LOOPING NUMBER OF FIELDS IN THE DYNAMIC
						
						// $vals .= ", '". db::getInstance()->real_escape_string($_POST[$dynamix[$i][1][3].$dynamix[$i][0][$k][1]][$j]) ."'";
						$vals .= ", '". db::getInstance()->real_escape_string($_POST[$dynamix[$i][1][3].$dynamix[$i][0][$k][1]][$j]) ."'";

					}

					$vals .= "),";

				}

			}

		}

		$sql = $sql . substr($vals, 0, -1);
			
		if($dynamixflag){

    		if($k_debug) echo '<br/> CD201: '. $sql;

    		$result = db::getInstance()->db_insertQuery($sql);

    		if($k_debug) print_r($result);

		}	

	}	

}



	if($k_debug) exit();

	

	$edited = 0;

	if ($editID > 0) $edited = 1;

	else $edited = 2;

	

    //echo '<script>window.location="'.$_SERVER['HTTP_REFERER'].'?view=1&save='.$edited.'";</script>';

    //CHANGED FOR CAPAPI

    echo '<script>window.location="'.$_SERVER['HTTP_REFERER'].'&view=1&save='.$edited.'";</script>';

	exit();	

?>