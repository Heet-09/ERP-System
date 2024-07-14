<?php
require_once('dbClass.php');

// Retrieve parameters
$data = $_GET['data'] ?? "";
$ReportID = $_GET['ReportID'] ?? 0;
$UserID = $_GET['UserID'] ?? 0;

// Check if data is provided
if (strlen($data) > 0) {
    try {
        // Check if there is an existing record for the ReportID and UserID
        $query = "SELECT * FROM kreport_datagrid_data WHERE ReportID = $ReportID AND UserID = $UserID";
        $result = db::getInstance()->db_select($query);

        if ($result['num_rows'] > 0) {
            // If record exists, update the existing entry
            $UpdateID = $result["result_set"][0]["ID"];
            $sql = "UPDATE kreport_datagrid_data SET ColumnState = '$data' WHERE ID = $UpdateID";
            db::getInstance()->db_insertQuery($sql);
        } else {
            // If record does not exist, insert a new entry
            $sql = "INSERT INTO kreport_datagrid_data (ReportID, UserID, ColumnState) VALUES ('$ReportID', '$UserID', '$data')";
            db::getInstance()->db_insertQuery($sql);
        }
    } catch (Exception $e) {
        // Handle database errors
        error_log("Database Error: " . $e->getMessage());
    }
}

// Retrieve the saved state from the database
$query = "SELECT ColumnState FROM kreport_datagrid_data WHERE ReportID = $ReportID AND UserID = $UserID";
$result = db::getInstance()->db_select($query);

// Check if there's a saved state
if ($result['num_rows'] > 0) {
    // Output the saved state
    $savedState = $result['result_set'][0]["ColumnState"];
    echo $savedState;
} else {
    // If no saved state found, output an empty string
    echo "";
}

// Log the data to PHP error log
error_log("Received data: " . json_encode($data));

// Output the data to the client
echo json_encode($data);
?>
