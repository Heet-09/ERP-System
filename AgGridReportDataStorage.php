    <?php 
require_once('dbClass.php');

session_start();
// Retrieve parameters
// print_r($_REQUEST);
$data = $_REQUEST['data'] ?? "";
$ReportID = $_REQUEST['ReportID'] ?? 0;
$UserID = $_SESSION['user_id'] ?? 0;
$TemplateID = $_REQUEST['templateId'] ?? 0;
// echo $TemplateID;
$TypeID = $_REQUEST['typeID'] ?? 0;
$TemplateName=$_REQUEST['tempName'] ?? 0;
$TemplateDescription=$_REQUEST['templateDescription'] ?? 0;

$lastInsertedID = 0;
// echo $TypeID;
// echo "TemplateTypeID";
// echo $TemplateID;s
if (!$TemplateID) {
    // echo $TemplateName;
    
    $sql = "INSERT INTO kreport_template_data (TemplateName, TemplateDescription, ReportID, TemplateTypeID, UserID, ReportJson) 
                VALUES ('$TemplateName', '$TemplateDescription', '$ReportID', '$TypeID', '$UserID', '$data')";
        $result = db::getInstance()->db_insertQuery($sql);
        // echo"result";
        // print_r($result);
         $lastInsertedID = $result['last_id'];
        //  print_r(result);

    $sql = "UPDATE kreport_template_data SET ReportJson = '$data',TemplateName='$TemplateName',TemplateDescription='$TemplateDescription' WHERE ID=$lastInsertedID";
     $result = db::getInstance()->db_insertQuery($sql);
     $result['result_set'][0]["ReportJson"];
    //  print_r($result);
}else{

    if (strlen($data) > 0) {
        // Check if there is an existing record for the ReportID and UserID
        // echo "in the else block";
        $query = "SELECT * FROM kreport_template_data WHERE ReportID = $ReportID AND ID =$TemplateID";
        $result = db::getInstance()->db_select($query);
        // print_r($result);resultArray
        if ($result['num_rows'] > 0) {
            // If record exists, update the existing entry
            // $UpdateID = $result["result_set"][0]["ID"];
            $sql = "UPDATE kreport_template_data SET ReportJson = '$data',TemplateTypeID='$TypeID' WHERE ID = $TemplateID";
            $result = db::getInstance()->db_insertQuery($sql);
        } else {
            // If record does not exist, insert a new entry
            $sql = "INSERT INTO kreport_template_data (ID,TypeTemplateID, ReportID, UserID, ReportJson) VALUES ('$TemplateID','$TypeID', '$ReportID', '$UserID', '$data')";
            $result = db::getInstance()->db_insertQuery($sql);
        }
    }
}

// echo $lastInsertedID;
// Check if data is provided

// Retrieve the saved state from the database
$query = "SELECT ReportJson FROM kreport_template_data WHERE ReportID = $ReportID AND ID = $TemplateID" ;
$result = db::getInstance()->db_select($query);
// print_r($result);

// Check if there's a saved state
if ($result['num_rows'] > 0) {
    // Output the saved state
    $savedState = $result['result_set'][0]["ReportJson"];
    echo $savedState;
 
} else {
    // If no saved state found, output an empty string
    echo "";
}
?>

