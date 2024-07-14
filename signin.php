<!doctype html>

<?php 
	if(isset($_GET['error'])){
		$error_msg = $_GET['error'];
	}else{
		$error_msg = 0;
	}
	if(isset($_GET['newreg'])){
		$newreg = $_GET['newreg'];
	}else{
		$newreg = 0;
	}
	session_start();
?>
<html class="fixed">
	<head>
		<style>
		h1.title.text-uppercase.text-weight-bold.m-none 

		{

			color: #0088cc !important;

			background: #ffffff !important;

		}

		</style>



		<!-- Basic -->

		<meta charset="UTF-8">



		<meta name="keywords" content="Login to Kreon" />

		<meta name="description" content="">

		<meta name="author" content="">



		<!-- Mobile Metas -->

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />



		<!-- Web Fonts  -->

		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">



		<!-- Vendor CSS -->

		<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css" />



		<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css" />

		<link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css" />

		<link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/datepicker3.css" />



		<!-- Theme CSS -->

		<link rel="stylesheet" href="assets/stylesheets/theme.css" />



		<!-- Skin CSS -->

		<link rel="stylesheet" href="assets/stylesheets/skins/default.css" />



		<!-- Theme Custom CSS -->

		<link rel="stylesheet" href="assets/stylesheets/theme-custom.css">



		<!-- Head Libs -->

		<script src="assets/vendor/modernizr/modernizr.js"></script>

	</head>
<?php 
// echo "<h1>ERROR: Not connected</h1>"; exit(); 
?>
	<body>

		<!-- start: page -->

		<section class="body-sign">

			<div class="center-sign">

				<a href="/" class="logo pull-left">

					<img src="assets/images/kreon-logo.png" height="54" alt="" />

				</a>



				<div class="panel panel-sign">

					<div class="panel-title-sign mt-xl text-right">

						<!--<a href="signup.php"><h1 class="title text-uppercase text-weight-bold m-none"><i class="fa fa-user mr-xs"></i> REGISTER</h1></a>-->

						<h2 class="title text-uppercase text-weight-bold m-none"><i class="fa fa-user mr-xs"></i> LOGIN</h2>

						

					</div>

		

					<div class="panel-body">

						<?php

							if($error_msg  == 1){

								?>

							<div class="alert alert-danger">

								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

								<strong>Sorry!</strong> The username-password combination did not match our records. Please try again.

							</div><?php

							}

							if($error_msg  == 2){

								?>

							<div class="alert alert-danger">

								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

								<strong>Captcha did not match!</strong> Please try again.

							</div><?php

							}

							if($error_msg  == 3){

								?>

							<div class="alert alert-danger">

								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

								<strong>Sorry!</strong> The user is de-activated by the Admin. Please<a href="http://keygst.com/contact-us"> contact them</a> for re-activaton.

							</div><?php

							}

							if($newreg  == 1){

								?>

							<div class="alert alert-danger">

								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

								<strong>Congratulations!</strong> You have successfully registered. Please login with the registered details.

							</div><?php

							}
				// 			echo "ERR_SSL_PROTOCOL_ERROR";
				// 			exit();
							

						?>

						<form action="login_db.php" method="post">

							<div class="form-group mb-lg">

								<label>Email</label>

								<div class="input-group input-group-icon">

									<input name="email" type="text" required class="form-control input-lg" />

									<span class="input-group-addon">

										<span class="icon icon-lg">

											<i class="fa fa-user"></i>

										</span>

									</span>

								</div>

							</div>



							<div class="form-group mb-lg">

								<div class="clearfix">

									<label class="pull-left">Password</label>

								</div>

								<div class="input-group input-group-icon">

									<input name="pwd" type="password" required class="form-control input-lg" />

									<span class="input-group-addon">

										<span class="icon icon-lg">

											<i class="fa fa-lock"></i>

										</span>

									</span>

								</div>

							</div>
							
		<!--<label><strong>Enter Captcha:</strong></label><br />-->
  <!--      <input type="text" name="captcha" />-->
  <!--      <p><br /><img src="captcha.php?rand=<?php echo rand(); ?>" id='captcha_image'></p>-->
  <!--      <p>Can't read the image? <a href='javascript: refreshCaptcha();'>click here</a> to refresh</p>-->

  <!--      <script>-->
            
  <!--          function refreshCaptcha(){-->
  <!--              var img = document.images['captcha_image'];-->
  <!--              img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;-->
  <!--          }-->
  <!--      </script>-->


							<div class="form-group mb-lg">

								<div class="col-sm-8">

									<div class="checkbox-custom checkbox-default">

										<input id="RememberMe" name="rememberme" type="checkbox"/>

										<label for="RememberMe">Remember Me</label><br/> 

									</div>

									<i style="color: gainsboro;">(Do Not use for Public Devices)</i>

								</div>

								<div class="class-sm-4">

									<!--a href="recover-password.php" tabindex="-1" class="pull-right">Forgot Password?</a-->

								</div>

							</div>



							<div class="row">

								<div class="col-sm-12">

									<button style="width:100%" type="submit" class="btn btn-primary hidden-xs">Login</button>

									<button style="width:100%" type="submit" class="btn btn-primary btn-block btn-lg visible-xs mt-lg">Login</button>

								</div>

							</div>

						</form>

					</div>

				</div>



				<p class="text-center text-muted mt-md mb-md">&copy; Copyright <?php echo date("Y");?>. All Rights Reserved.<br>Powered by <a href="http://KreonSolutions.com">KreonSolutions.com</a></p>

			</div>

		</section>

		<!-- end: page -->



		<!-- Vendor -->

		<script src="assets/vendor/jquery/jquery.js"></script>
		<script src="assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
		<script src="assets/vendor/nanoscroller/nanoscroller.js"></script>
		<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="assets/vendor/magnific-popup/magnific-popup.js"></script>
		<script src="assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>

		

		<!-- Theme Base, Components and Settings -->

		<script src="assets/javascripts/theme.js"></script>

		

		<!-- Theme Custom -->

		<script src="assets/javascripts/theme.custom.js"></script>

		

		<!-- Theme Initialization Files -->

		<script src="assets/javascripts/theme.init.js"></script>



	</body>

</html>

<?php 



if(is_resource($connect)) mysql_close($connect);

?>