<?php
$dbType = 1;	//MySQL
// $dbType = 2;	//MSSQL/ Sql Server

if($dbType == 1)	include('dbClassMySQL.php');
if($dbType == 2)	include('dbClassMSSQL.php');
?>