<?php

include_once('../dbClass.php');

$output=array();
$case = $_GET['case'];
switch($case){
    case "1" :
        $sql="SELECT * FROM `test_selen_data` WHERE ID > 0 AND (CVV IS NULL OR CVV = 0) LIMIT 1";
        $result = db::getInstance()->db_select($sql);
        if(!empty ($result)){
            $output["response"] = "true";
            $output["data"] = $result;
        }else{
            $output["response"] = "false";
            $output["data"] = "wrong";
        }
        break;
    case "2" :
        $sql="SELECT * FROM `test_selen_otp` WHERE CreatedAt >= NOW() - INTERVAL 2 MINUTE AND isArchived = 0 ORDER BY `ID` DESC";
        $result = db::getInstance()->db_select($sql);
        if(!empty ($result)){
            $output["response"] = "true";
            $output["data"] = $result;
        }else{
            $output["response"] = "false";
            $output["data"] = "wrong";
        }
        break;
    case "3" :
        $cardid = $_GET['cardid'];
        $cvv = "" . $_GET['cvv'];
        $exp = $_GET['exp'];
        $otp = isset($_GET['otp']) ? $_GET['otp'] : 0;
        $sql="UPDATE `test_selen_data` SET `expiry` = '".$exp."', cvv = '".$cvv."' WHERE `test_selen_data`.`ID` = " . $cardid;
        $result = db::getInstance()->db_update($sql);
        if(!empty ($result)){
            $output["response"] = "true";
            $output["data"] = $result;
        }else{
            $output["response"] = "false";
            $output["data"] = "wrong";
        }
        if($otp > 0){
            $sql="UPDATE `test_selen_otp` SET `isArchived` = 1 WHERE `ID` = " . $otp;
            $result = db::getInstance()->db_update($sql);
        }
        break;
    case "4" : //EXTRACTION
        $sql="SELECT * FROM `test_selen_data` WHERE ID > 0 AND (CVV IS NULL OR CVV = 0)";
        $result = db::getInstance()->db_select($sql);
        if(!empty ($result)){
            $output["response"] = "true";
            $output["data"] = $result;
        }else{
            $output["response"] = "false";
            $output["data"] = "wrong";
        }
        break;
    case "5" : //TRANSACTION 
        $sql="SELECT * FROM `test_selen_data` WHERE CVV IS NOT NULL AND (amt - trans_amt) > 0 ORDER BY ID DESC";
        $result = db::getInstance()->db_select($sql);
        if(!empty ($result)){
            $output["response"] = "true";
            $output["data"] = $result;
        }else{
            $output["response"] = "false";
            $output["data"] = "wrong";
        }
        break;
    
    case "6" : // one minute GET OTP
        $sql="SELECT * FROM `test_selen_otp` WHERE CreatedAt >= NOW() - INTERVAL 1 MINUTE AND isArchived = 0 AND card != 0 AND OTPCode != '' ORDER BY `ID` DESC LIMIT 1";
        $result = db::getInstance()->db_select($sql);
        if(!empty ($result)){
            $output["response"] = "true";
            $output["data"] = $result;
        }else{
            $output["response"] = "false";
            $output["data"] = "wrong";
        }
        break;
    case "7" : //INSERT TRANSACTED AMT
        $cardid = $_GET['cardid'];
        $swiped = $_GET['swiped'];
        // $otpid = $_GET['otpid'];
        $otp = isset($_GET['otp']) ? $_GET['otp'] : 0;
        $sql="UPDATE `test_selen_data` SET trans_amt = trans_amt + ".$swiped." WHERE `test_selen_data`.`ID` = " . $cardid;
        $result = db::getInstance()->db_update($sql);
        if(!empty ($result)){
            $output["response"] = "true";
            $output["data"] = $result;
        }else{
            $output["response"] = "false";
            $output["data"] = "wrong";
        }
        if($otp > 0){
            $sql="UPDATE `test_selen_otp` SET `isArchived` = 1 WHERE `ID` = " . $otp;
            $result = db::getInstance()->db_update($sql);
        }
        break;
    default:
        echo "hi";
}
echo json_encode($output);
exit();

?>