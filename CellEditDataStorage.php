<?php
require_once('dbClass.php');

session_start();

// Retrieve parameters
$ID = $_REQUEST['ID'] ?? "";
$Reg = $_REQUEST['Reg'] ?? "";
$EntryNo = $_REQUEST['EntryNo'] ?? "";
$EntryDate = $_REQUEST['EntryDate'] ?? "";
$ChlNo = $_REQUEST['ChlNo'] ?? "";
$TrspPay = $_REQUEST['TrspPay'] ?? "";
$Sender = $_REQUEST['Sender'] ?? "";
$Broker = $_REQUEST['Broker'] ?? "";
$Remark = $_REQUEST['Remark'] ?? "";
$Total = $_REQUEST['Total'] ?? "";
$Meter = $_REQUEST['Meter'] ?? "";
$Lumps = $_REQUEST['Lumps'] ?? "";
$PartyName = $_REQUEST['PartyName'] ?? "";
$BrokerName = $_REQUEST['BrokerName'] ?? "";
$CreatedAt = $_REQUEST['CreatedAt'] ?? "";
$CreatedBy = $_REQUEST['CreatedBy'] ?? "";



// Prepare the SQL query using placeholders
$sql = "UPDATE `view_purchaseinward` 
        SET Reg = '$Reg', EntryNo = '$EntryNo'
        WHERE ID = 'ID'";

// Use prepared statements to execute the query
$result = db::getInstance()->db_insertQuery($sql);
?>
