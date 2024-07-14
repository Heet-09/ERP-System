<?php
include("dbClass.php");
session_start();
$apicase = isset($_POST['apicase']) ? $_POST['apicase'] : 0;

/************************
ALTER TABLE `kmainmedia` ADD `isDeleted` BOOLEAN NOT NULL DEFAULT FALSE AFTER `MediaFolder`;
**********************/


switch($apicase){
    case "1": //DELETING IMAGE IN FORM EDIT MODE
        //used at form-init
        $formID = isset($_POST['fid']) ? $_POST['fid'] : 0;
        $fieldType = isset($_POST['fieldType']) ? $_POST['fieldType'] : 0;
        $fieldName = isset($_POST['fieldName']) ? $_POST['fieldName'] : null;
        $mediaID = isset($_POST['mediaID']) ? $_POST['mediaID'] : 0;
        if($formID == 0 || $fieldType == 0 ||  $fieldName == NULL){ //$mediaID == 0 ||
            $response["error"] = true;
            $response["error_msg"] = "Error Processing your Request. Please try again.";
        }else{
            $sql = "SELECT OtherAttributes, ValueFromDb, TableMapTable, TableMapPrimary, TableMapOtherKey, TableName, TablePrimaryKey FROM kmainfields 
                    LEFT JOIN kmainforms ON kmainfields.FormId = kmainforms.FormId
                    WHERE kmainfields.FormId = " . $formID . " AND DbFieldName LIKE '" . $fieldName ."'" ;
            $result = db::getInstance()->db_select($sql);
	        if($result['num_rows'] > 0){
	            $row = $result['result_set'][0];
	            $FormTable = $row["TableName"];
	            $FormTablePK = $row["TablePrimaryKey"];
	            $MapTable = $row["TableMapTable"];
	            $MapTablePK = $row["TableMapPrimary"];
	            $MapTableOtherKey = $row["TableMapOtherKey"];
	            
	            if($fieldType == 12){ //direct delete from table
                    $sql = "UPDATE " . $FormTable . " SET  " . $fieldName . " =  0 WHERE " . $fieldName . " = " . $mediaID . "";
                    $result = db::getInstance()->db_update($sql);
        	        $sql = "UPDATE kmainmedia SET isDeleted = 1 WHERE MediaId = " . $mediaID . "";
                    $result = db::getInstance()->db_update($sql);
        	        $response["error"] = false;
        	        $response["data"] = "Image Deleted Successfully.";
                } 
                if($fieldType == 13){ //Find mapping table and then delete from table
                    $sql = "DELETE FROM " . $MapTable . " WHERE " . $MapTableOtherKey . " = " . $mediaID . "";
                    $result = db::getInstance()->db_update($sql);
        	        $sql = "UPDATE kmainmedia SET isDeleted = 1 WHERE MediaId = " . $mediaID . "";
                    $result = db::getInstance()->db_update($sql);
        	        $response["error"] = false;
        	        $response["data"] = "Image Deleted Successfully.";
                }
	        }else{
	            $response["error"] = true;
                $response["error_msg"] = "Error while deleting the image. Please try again.";
	        }
        }
        break;
    default: break;
}

echo json_encode($response);
exit();
/*
$email = isset($_POST['email']) ? $_POST['email'] : null;
$pwd = isset($_POST['pwd']) ? $_POST['pwd'] : null;
$passed = false;

if(strlen($email) > 5 && strlen($pwd) > 5){
    $sql="SELECT customers.*, CASE 
                	WHEN view_customer_walletbalance.bal IS NULL then 0 
                    ELSE view_customer_walletbalance.bal
                END as custBal
            FROM customers 
            LEFT JOIN view_customer_walletbalance ON customers.ID = view_customer_walletbalance.CustomerId 
            WHERE email =  '" . $email . "' LIMIT 1";
	$result = db::getInstance()->db_select($sql);
	if($result['num_rows'] > 0){
		$row = $result['result_set'][0];
		$userid = $row['ID'];
		$custname = $row['Name'];
		$phone = $row['Phone'];
		$dbpwd = $row['Password'];
		$salt = $row['Salt'];
		
		$userpwd = MD5($pwd . $salt);
		
		if (strcmp($userpwd,$dbpwd)==0){
		    $passed = true;
		    $_SESSION["userid"] = $userid;
		    $_SESSION["userbal"] = $row['custBal'];
		    $_SESSION["user_name"] = $custname;
		    $_SESSION["user_phone"] = $phone;
		    unset($row['Password']);
		    unset($row['Salt']);
		    $response["data"] = $row;
		}
	}else{
	    $passed = false;
	}
}

if(!$passed){
    //failed to login
    $response["error"] = true;
    $response["error_msg"] = "Your Login Id or Password is Incorrect!";
}else{
    $response["error"] = false;
}
echo json_encode($response);
exit();*/
?>