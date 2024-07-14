<?php
$k_debug = 1;
include_once('dbClass.php');

if($dbType == 1)	include('dbMySQL.php');
if($dbType == 2)	include('dbMySQL.php');
// if($dbType == 2)	include('dbMSSQL.php'); // As of now both the files are same so we are using the same db file
?>