<?php
session_start();
include_once("dbClass.php");


// print_r($_POST);

$role = isset($_POST['Label']) ? $_POST['Label'] : 0;
$editID = isset($_POST['editID']) ? $_POST['editID'] : 0;

if((int)$editID > 0){
    $sql = "UPDATE pagerolemaster SET Label = '" . $role . "' WHERE RoleId = " . $editID;
    $result = db::getInstance()->db_update($sql);
    // exit();
    $sql = "Delete from pageroleaccess where RoleID = " . $editID;
    $result = db::getInstance()->db_update($sql);
    $roleID = $editID;
}else{
    $sql ="INSERT into pagerolemaster (Label) VALUES ('".$role."')";
    $result = db::getInstance()->db_insertQuery($sql);
    $roleID = $result['last_id'];
    // exit();
}

    
    $queryVals = "INSERT INTO `pageroleaccess` (`RoleID`, `PageAccessID`, `AddBtn`,`EditBtn`,`ListView`,`OtherBtn`, `DeleteBtn`) 
    							VALUES ";
    $separator = "";
    for($i = 0; $i < $_POST['totalPages']; $i++){
    	$queryVals .= $separator . "(" . $roleID . ", " . $_POST['accessId'][$i][0] . ", ";
    	if(isset($_POST['add'][$i][0])){  $queryVals .=  ($_POST['add'][$i][0] == "on" ? 1 : 0) . ", "; }else{ $queryVals .=   "0" . ", ";}
    	if(isset($_POST['edit'][$i][0])){ $queryVals .=  ($_POST['edit'][$i][0] == "on" ? 1 : 0) . ", "; }else{ $queryVals .=  "0" . ", ";}
    	if(isset($_POST['listview'][$i][0])){ $queryVals .=  ($_POST['listview'][$i][0] == "on" ? 1 : 0) . ", "; }else{ $queryVals .=  "0" . ", ";}
    	if(isset($_POST['other'][$i][0])){ $queryVals .=  ($_POST['other'][$i][0] == "on" ? 1 : 0) . ", "; }else{ $queryVals .=  "0" . ", ";}
    	if(isset($_POST['delete'][$i][0])){ $queryVals .=  ($_POST['delete'][$i][0] == "on" ? 1 : 0) . ""; }else{ $queryVals .=  "0" . "";}
    	$queryVals .= ")";
    	$separator = ", ";
    }
    // echo $queryVals;
    $result = db::getInstance()->db_insertQuery($queryVals);
    // exit();
// echo '<script>alert("data sent successfully");</script>';
echo '<script>window.location="roles.php";</script>';
?>	