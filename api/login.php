<?php

include '../dbClass.php';	

$output=array();
	//echo $_GET['username'];

if(!isset($_GET['username']))
{
    //please provide loginid
    $output["response"] = "false";
    $output["data"] ="Please enter loginid";
}
elseif(!isset($_GET['password']))
{
   //please provide password
    $output["response"] = "false";
    $output["data"] ="Please enter password"; 
}	
if(isset($_GET['username']) && isset($_GET['password']))
{
	$username=$_GET['username'];
	$password = $_GET['password'];

    if($username!="" && $password!="")
    {

        $sql="select * from users where username='".$username."'";
    	$result = db::getInstance()->db_select($sql);
 		//print_r($result);
        if(!empty ($result))
        {
			 //user exists
    		for($i = 0; $i < $result['num_rows']; $i++){
         		$db_pwd = $result['result_set'][$i]['password'];
         	 } 
			//$db_pwd = $row['password'];
			$user_pwd = $password;
		    if(strcmp($db_pwd,$user_pwd) == 0)
            {
                $output["response"] = "true";
                for($i = 0; $i < $result['num_rows']; $i++){
                    $result['result_set'][$i]["ID"] = $result['result_set'][$i]["user_id"];
     		        $output["data"][] = $result['result_set'][$i];
     		       // $output["data"]["ID"] = $result['result_set'][$i]["user_id"];
     	        } 
            }
            else
            {
                $output["response"] = "false";
                $output["data"] ="wrong password";
            }
        }
        else
        {
            //user does not exists
            $output["response"] = "false";
            $output["data"] ="Login Id Not Found";
        }
    }
    else
    {
        $output["response"] = "false";
        $output["data"] ="Fields should not be blank";
    }
}

echo json_encode($output);
?>