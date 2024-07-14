<?php

include '../dbClass.php';	

$output=array();

if(!isset($_GET['profileid']))
{
    //please provide loginid
    $output["response"] = "false";
    $output["data"] ="Please enter ID";
}else{

	$id=$_GET['profileid'];
    $sql="delete from family_details where Id='".$id."'";
	$result = db::getInstance()->db_update($sql);
 	//print_r($result);
    if(!empty ($result))
    {
        $output["response"] = "true";
    }else{
        $output["response"] = "false";
        $output["data"] ="Failed";
    }
}

echo json_encode($output);
exit();
?>