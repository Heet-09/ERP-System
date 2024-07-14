<?php 
require_once('dbClass.php');

// Retrieve parameters
$data = $_GET['data'] ?? "";
$ReportID = $_GET['ReportID'] ?? 0;
$UserID = $_SESSION['user_id'] ?? 0;

// Check if data is provided
if (strlen($data) > 0) {
    // Check if there is an existing record for the ReportID and UserID
    $query = "SELECT * FROM kreport_pivot_data WHERE ReportID = $ReportID AND UserID = $UserID";
    $result = db::getInstance()->db_select($query);
    echo $result;

    if ($result['num_rows'] > 0) {
        // If record exists, update the existing entry
        $UpdateID = $result["result_set"][0]["ID"];
        $sql = "UPDATE kreport_pivot_data SET ReportJson = '$data' WHERE ID = $UpdateID";
        $result = db::getInstance()->db_insertQuery($sql);
    } else {
        // If record does not exist, insert a new entry
        $sql = "INSERT INTO kreport_pivot_data (ReportID, UserID, ReportJson) VALUES ('$ReportID', '$UserID', '$data')";
        $result = db::getInstance()->db_insertQuery($sql);
    }
}

// Retrieve the saved state from the database
$query = "SELECT ReportJson FROM kreport_pivot_data WHERE ReportID = $ReportID AND UserID = $UserID";
$result = db::getInstance()->db_select($query);

// Check if there's a saved state
if ($result['num_rows'] > 0) {
    // Output the saved state
    $savedState = $result['result_set'][0]["ReportJson"];
     error_log("Retrieved data: " . print_r($savedState, true));
    echo $savedState;
 
} else {
    // If no saved state found, output an empty string
    echo "";
}
//    echo "jii";
?>
