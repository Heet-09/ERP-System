<?php
include_once('../dbClass.php');
$output=array();
$ID = isset($_GET["lastOTP"]) ? $_GET["lastOTP"] : 0;
if($ID == 0){
    $sql = "SELECT MAX(ID) as lastotpid FROM test_selen_otp";
}else{
    $sql = "SELECT OTPCode FROM test_selen_otp WHERE ID = " . $ID;
}
$result = db::getInstance()->db_select($sql);
if(!empty ($result)){
    $output["response"] = "true";
    $output["data"] = $result;
}else{
    $output["response"] = "false";
    $output["data"] = "wrong";
}
//$output["sql"] = $sql;
echo json_encode($output);
exit();
//http://kreonsolutions.com/mewad/api/selen_otp_check.php?lastOTP=1
?>