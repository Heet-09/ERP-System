<?php
	session_start();
	echo "Logged out Successfully ";
	session_unset();
	session_destroy(); 
	//header('Location: signin.php');
	echo '<script>window.location.replace("signin.php");</script>';
	exit();
		    
?>