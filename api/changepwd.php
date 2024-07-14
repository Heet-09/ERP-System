<?php

include '../dbClass.php';	

$output=array();

if(!isset($_GET['userid']))
{
    //please provide loginid
    $output["response"] = "false";
     $output["data"] ="Please enter UserId";
}else{

	$id=$_GET['userid'];
	$oldpwd=$_GET['oldpwd'];
	$newpwd=$_GET['newpwd'];
	
     $sql="select password from users where user_id='".$id."'";
	$result = db::getInstance()->db_select($sql);
 	//print_r($result);
    if(!empty ($result))
    {
			 //user exists
    		for($i = 0; $i < $result['num_rows']; $i++){
         	    $db_pwd = $result['result_set'][$i]['password'];
         	 } 
			$user_pwd = $oldpwd;
		    if(strcmp($db_pwd,$user_pwd) == 0)
            {
                $sql="update users set password='".$newpwd."' where user_id='".$id."'";
	            $result = db::getInstance()->db_update($sql);
	            $output["response"] = "true";
	            $output["data"] ="password changed successfully";
            }
            else
            {
                $output["response"] = "false";
                $output["data"] ="wrong password";
            }
        
    }else{
        $output["response"] = "false";
        $output["data"] ="Failed";
    }
}

echo json_encode($output);
exit();
?>