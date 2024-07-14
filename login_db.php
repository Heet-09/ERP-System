<?php

	session_start();

	include_once('dbClass.php');

	$email = isset($_POST['email']) ? $_POST['email'] : "";

	$pwd = isset($_POST['pwd']) ? $_POST['pwd'] : "";

	$dbpwd="";

	

	//FOR VAPT

	$_SESSION['LoginAttempt'] = isset($_SESSION['LoginAttempt']) ? ($_SESSION['LoginAttempt'] + 1) : 1;

	// if($_SESSION['LoginAttempt'] > 3) {  echo "Too many login attempts. Please try after 20 mins."; exit(); }

	/*

	    if(mysql_num_rows($checkloginEmp) == 1){

            // Check if they're locked out

            $checkLockout = mysql_query("SELECT * FROM login

                                         WHERE LoginID = $userID

                                         AND failed_login_attempts >= 3

                                         AND last_failed_login > DATE_SUB(NOW(), INTERVAL 10 MINUTE)" or die (mysql_error());

            if (mysql_num_rows($checkLockout) > 0) {

                echo "Locked out!";

            } else {

                $row = mysql_fetch_array($checkloginEmp);

                $_SESSION['Username'] = $userID;

                $_SESSION['FName'] = $row['FName'];

                $_SESSION['SName'] = $row['SName'];

                $_SESSION['LoggedIn'] = 1;

            }

            echo "<meta http-equiv='refresh' content='1;CareMarkLogin2.php'/>";

        }

	*/

	

    //FOR VAPT

    if(strcasecmp($_SESSION['captcha'], $_POST['captcha']) != 0){   //NOT MATCHED

	    echo '<script>window.location.replace("signin.php?error=2");</script>';

	    exit();

    }

    

	$sql = "SELECT ID as uid, Password, Role FROM users WHERE Username='".$email."' "; 
	$result = db::getInstance()->db_select($sql);	

	for($i = 0; $i < $result['num_rows']; $i++){
		$user_id = $result['result_set'][$i]['uid'];
		// $salt = $row['salt'];
		$dbpwd = $result['result_set'][$i]['Password'];
		$role = $result['result_set'][$i]['Role'];
		if($i == 0) break;  //instead of TOP 1 or LIMIT 1
	}
	
	// $userpwd = MD5($salt . $pwd);

		

	if (strcmp($pwd,$dbpwd)==0){

		$_SESSION['email'] = $email;

		$_SESSION['user_id'] = $user_id;

		$_SESSION['access'] = $role;

		echo "<br/>Success!!<br />";

		echo '<script>window.location.replace("list-data.php?view=1&form=2");</script>';

	}

	else

	{

// 		if (($email == "Kreon123" || $email == "kreon123") && $pwd == "kreon123"){

// 			$_SESSION['email'] = "Kreon123";

// 			$_SESSION['user_id'] = "1";

// 			$_SESSION['access'] = "1";

// 			echo "<br/>Success!!<br />";

// 			echo '<script>window.location.replace("list-data.php?view=1&form=11");</script>';

// 		}else{

		    echo '<script>window.location.replace("signin.php?error=1");</script>';

		    exit();

// 			echo "Your Login Name or Password is invalid<br />";

// 		}

	}

	if(is_resource($connect)) mysqli_close($connect);

	exit();

?>