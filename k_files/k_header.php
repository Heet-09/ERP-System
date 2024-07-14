<?php
session_start();
///include 'k_files/k_config.php';
include_once('./dbClass.php');
$userflag=0;    
$logincheck = isset($k_head_login_check) ? $k_head_login_check : 1;
$check_access = isset($k_head_access) ? $k_head_access : 0;
$user_access = isset($user_access) ? $user_access : 1;
if($user_access){
	// echo strlen($_SESSION['user_id']);
	if(isset($_SESSION['user_id']) && strlen($_SESSION['user_id'])>0){
		$userflag = 1;
		$logincheck =0;
		$uid = $_SESSION['user_id'];
		//$sql = "SELECT ID as uid, Name, Password FROM users WHERE ID= $uid LIMIT 1";
		$sql = "SELECT ID as uid, name,pagerolemaster.Label as Role,users.Role as RoleID FROM users LEFT JOIN pagerolemaster on users.Role= pagerolemaster.RoleId WHERE ID='".$uid."' ";
		$result = db::getInstance()->db_select($sql);	
		for($i = 0; $i < $result['num_rows']; $i++){
			$name = $result['result_set'][$i]['name'];
			$RoleID = $result['result_set'][$i]['RoleID'];
			if($i == 0) break;  //instead of TOP 1 or LIMIT 1
		}
	}
	if($userflag == 0 && $logincheck==1){
		echo '<script>window.location.replace("signin.php");</script>';
		exit();
	}
}

$menubar="";
    $sqlmenu  = "SELECT a.*,b.displayname,b.filename,b.FaIcon,b.PageOrder,b.parentMenu FROM pageroleaccess a 
                LEFT JOIN kpageaccess b on a.PageAccessID = b.ID WHERE a.RoleID='".$RoleID."' ORDER BY b.PageOrder ASC"; 
	$result = db::getInstance()->db_select($sqlmenu);
	$pageURL =  basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
// echo basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);

	
		$menubar .= '<ul class="nav nav-main"> ';
	    $pageAccessForUser = array();
		for($i = 0; $i < $result['num_rows']; $i++){
		    if(str_contains($result['result_set'][$i]['filename'],$pageURL)){
		        $pageAccessForUser = $result['result_set'][$i];
		    }
			$pageaccessid = $result['result_set'][$i]['PageAccessID'];
			$displayname = $result['result_set'][$i]['displayname'];
			$filename = $result['result_set'][$i]['filename'];
			$parentMenu = $result['result_set'][$i]['parentMenu'];
			$FaIcon = $result['result_set'][$i]['FaIcon'];

			if($parentMenu == 0) {
				if($result['result_set'][$i]['AddBtn'] !=0 || $result['result_set'][$i]['ListView'] !=0 || $result['result_set'][$i]['EditBtn'] !=0){
					$sqlmenu1  = "SELECT * FROM kpageaccess  WHERE parentMenu='".$pageaccessid."' ORDER BY PageOrder ASC;"; 
					$result1 = db::getInstance()->db_select($sqlmenu1);

					if($result1['num_rows'] > 0){
						
						$menubar .= '<li class="nav-parent"><a><i class="fa fa-address-book" aria-hidden="true"></i>'.$displayname.'</a> ';
							$menubar .= '<ul class="nav nav-children"> ';
								for($j = 0; $j < $result1['num_rows']; $j++){
									$subpageaccessid = $result1['result_set'][$j]['ID'];
									$submenufilename = $result1['result_set'][$j]['filename'];
									$submenuFaIcon = $result1['result_set'][$j]['FaIcon'];
									$submenudisplayname = $result1['result_set'][$j]['displayname'];

									
									$sqlmenu2  = "SELECT * FROM kpageaccess  WHERE parentMenu='".$subpageaccessid."' ORDER BY PageOrder ASC;"; 
									$result2 = db::getInstance()->db_select($sqlmenu2);

									if($result2['num_rows'] > 0){
										$menubar .= '<li class="nav-parent"><a><i class="fa fa-address-book" aria-hidden="true"></i>'.$submenudisplayname.'</a> ';
											$menubar .= '<ul class="nav nav-children"> ';
											for($k = 0; $k < $result2['num_rows']; $k++){
												$subchildmenufilename = $result2['result_set'][$k]['filename'];
												$subchildmenuFaIcon = $result2['result_set'][$k]['FaIcon'];
												$subchildmenudisplayname = $result2['result_set'][$k]['displayname'];

												$menubar .= '<li><a href="'.$subchildmenufilename.'"><i class="'.$subchildmenuFaIcon.'" aria-hidden="true"></i>'.$subchildmenudisplayname.'</a></li>';

											}
											$menubar .= '</ul> ';
										$menubar .= '</li> ';
									}else{
										$menubar .= '<li><a href="'.$submenufilename.'"><i class="'.$submenuFaIcon.'" aria-hidden="true"></i>'.$submenudisplayname.'</a></li>';
									}

								}
							$menubar .= '</ul> ';
						$menubar .= '</li> ';
					}else{
						$menubar .= '<li><a href="'.$filename.'"><i class="'.$FaIcon.'" aria-hidden="true"></i>'.$displayname.'</a></li>';
					
					}
				}
			}
			elseif($parentMenu == -1) {
			    $menubar .='';
			}
		}
//		<li class="nav-parent">
// 										<a><i class="fa fa-cubes" aria-hidden="true"></i><span>VA Masters</span></a>
// 										<ul class="nav nav-children">
// 											<li><a href="'.$menu[1][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[1][0].'</a></li>	
// 											<li><a href="'.$menu[2][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[2][0].'</a></li>
// 											<li><a href="'.$menu[3][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[3][0].'</a></li>
// 											<li><a href="'.$menu[4][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[4][0].'</a></li>
// 										</ul>
// 									</li>

$menu[0][0]	= "Kmain Form"; 
$menu[0][1]	= "list-data.php?view=1&form=3";
$menu[1][0]	= "Kmain Form Fields"; 
$menu[1][1]	= "list-data.php?view=1&form=4";
$menu[2][0]	= "User Master"; 
$menu[2][1]	= "list-data.php?view=1&form=1";
$menu[3][0]	= "Purchase Inward"; 
$menu[3][1]	= "list-data.php?view=1&form=2";
$menu[4][0]	= "Kmain Grid"; 
$menu[4][1]	= "list-data.php?view=1&form=5";
$menu[5][0]	= "Kmain Grid Fields"; 
$menu[5][1]	= "list-data.php?view=1&form=6";
$menu[6][0]	= "Roles"; 
$menu[6][1]	= "roles.php";
$menu[7][0]	= "Page Role Access"; 
$menu[7][1]	= "page-role-access.php";
$menu[8][0]	= "Add Menu Pages"; 
$menu[8][1]	= "list-data.php?view=1&form=8";
$menu[9][0]	= "Project Completion"; 
$menu[9][1]	= "list-data.php?view=1&form=14";
$menu[10][0]	= "VA Progress"; 
$menu[10][1]	= "list-data.php?view=1&form=14";
$menu[11][0]	= "Unit Master"; 
$menu[11][1]	= "list-data.php?view=1&form=5";
$menu[12][0]	= "Audit Scheduling"; 
$menu[12][1]	= "list-data.php?view=1&form=16";
$menu[13][0]	= "Audit Initiation"; 
$menu[13][1]	= "list-data.php?view=1&form=17";
$menu[14][0]	= "Pre Audit Performa"; 
$menu[14][1]	= "list-data.php?view=1&form=18";
$menu[15][0]	= "Audit Progress"; 
$menu[15][1]	= "list-data.php?view=1&form=19";
$menu[16][0]	= "Audit Execution"; 
$menu[16][1]	= "list-data.php?view=1&form=20";
$menu[17][0]	= "VA Dashboard"; 
$menu[17][1]	= "va-dashboard.php";
$menu[18][0]	= "CSA Dashboard"; 
$menu[18][1]	= "csa-dashboard.php";
$menu[19][0]	= "VA Lifecycle Information"; 
$menu[19][1]	= "report-valifecycle.php";
$menu[20][0]	= "Scoring Factor 1"; 
$menu[20][1]	= "list-data.php?view=1&form=24";
$menu[21][0]	= "Quantification Factor"; 
$menu[21][1]	= "list-data.php?view=1&form=25";
$menu[22][0]	= "Scoring Factor 2"; 
$menu[22][1]	= "list-data.php?view=1&form=26";
$menu[23][0]	= "Scoring Factor 3"; 
$menu[23][1]	= "list-data.php?view=1&form=27";
$menu[24][0]	= "Scoring Questionnaire"; 
$menu[24][1]	= "list-data.php?view=1&form=28";
$menu[25][0]	= "Audit Completion"; 
$menu[25][1]	= "list-data.php?view=1&form=29";
$menu[26][0]	= "Audit Follow-up"; 
$menu[26][1]	= "list-data.php?view=1&form=30";
$menu[27][0]	= "Backup"; 
$menu[27][1]	= "backup.php";
$menu[28][0]	= "Restore"; 
$menu[28][1]	= "restore.php";
$menu[31][0]	= "Backup/ Restore"; 
$menu[31][1]	= "list-data.php?view=1&form=31";
$menu[32][0]	= "Users"; 
$menu[32][1]	= "list-data.php?view=1&form=1";
$menu[33][0]	= "Master Scoring Factor"; 
$menu[33][1]	= "list-data.php?view=1&form=32";



// $menubar = '';
// if($user_access == 1){
// 				$menubar = '
// 								<ul class="nav nav-main">
// 									<li><a href="'.$menu[0][1].'"><i class="fa fa-file" aria-hidden="true"></i>'.$menu[0][0].'</a></li>
// 									<li><a href="'.$menu[1][1].'"><i class="fa fa-file" aria-hidden="true"></i>'.$menu[1][0].'</a></li>
// 									<li><a href="'.$menu[4][1].'"><i class="fa fa-cubes" aria-hidden="true"></i>'.$menu[4][0].'</a></li>
// 									<li><a href="'.$menu[5][1].'"><i class="fa fa-cubes" aria-hidden="true"></i>'.$menu[5][0].'</a></li>
// 									<li><a href="'.$menu[2][1].'"><i class="fa fa-users" aria-hidden="true"></i>'.$menu[2][0].'</a></li>
// 									<li><a href="'.$menu[3][1].'"><i class="fa fa-cubes" aria-hidden="true"></i>'.$menu[3][0].'</a></li>
// 									<li><a href="'.$menu[8][1].'"><i class="fa fa-users" aria-hidden="true"></i>'.$menu[8][0].'</a></li>
// 									<li><a href="'.$menu[6][1].'"><i class="fa fa-cubes" aria-hidden="true"></i>'.$menu[6][0].'</a></li>
// 									<li><a href="'.$menu[7][1].'"><i class="fa fa-cubes" aria-hidden="true"></i>'.$menu[7][0].'</a></li>
// 								</ul>
					
// 				';
// 			}

// 			if($user_access == 1){
// 					$menubar = '
// 								<ul class="nav nav-main">
// 								    <li><a href="'.$menu[17][1].'"><i class="fa fa-cubes" aria-hidden="true"></i>'.$menu[17][0].'</a></li>
// 								    <li><a href="'.$menu[19][1].'"><i class="fa fa-cubes" aria-hidden="true"></i>'.$menu[19][0].'</a></li>
// 								    <li class="nav-parent">
// 										<a><i class="fa fa-cubes" aria-hidden="true"></i><span>VA Masters</span></a>
// 										<ul class="nav nav-children">
// 											<li><a href="'.$menu[1][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[1][0].'</a></li>	
// 											<li><a href="'.$menu[2][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[2][0].'</a></li>
// 											<li><a href="'.$menu[3][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[3][0].'</a></li>
// 											<li><a href="'.$menu[4][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[4][0].'</a></li>
// 										</ul>
// 									</li>
// 								    <li class="nav-parent">
// 										<a><i class="fa fa-cubes" aria-hidden="true"></i><span>VA Projects</span></a>
// 										<ul class="nav nav-children">
// 											<li><a href="'.$menu[6][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[6][0].'</a></li>
// 											<li><a href="'.$menu[7][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[7][0].'</a></li>
// 											<li><a href="'.$menu[8][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[8][0].'</a></li>
// 											<li><a href="'.$menu[9][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[9][0].'</a></li>
// 										</ul>
// 									</li>
// 									<li><a href="'.$menu[18][1].'"><i class="fa fa-home" aria-hidden="true"></i>'.$menu[18][0].'</a></li>
// 									<li class="nav-parent">
// 										<a><i class="fa fa-home" aria-hidden="true"></i><span>CSA Masters</span></a>
// 										<ul class="nav nav-children">
// 											<li><a href="'.$menu[11][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[11][0].'</a></li>	
// 											<li><a href="'.$menu[21][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[21][0].'</a></li>
// 											<li><a href="'.$menu[22][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[22][0].'</a></li>
// 											<li><a href="'.$menu[23][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[23][0].'</a></li>
// 											<li><a href="'.$menu[20][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[20][0].'</a></li>
// 											<li><a href="'.$menu[24][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[24][0].'</a></li>
// 											<li><a href="'.$menu[33][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[33][0].'</a></li>
// 										</ul>
// 									</li>
// 									<li class="nav-parent">
// 										<a><i class="fa fa-home" aria-hidden="true"></i><span>CSA Audits</span></a>
// 										<ul class="nav nav-children">
// 											<li><a href="'.$menu[12][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[12][0].'</a></li>
// 											<li><a href="'.$menu[13][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[13][0].'</a></li>
// 											<!--li><a href="'.$menu[14][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[14][0].'</a></li-->
// 											<li><a href="'.$menu[15][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[15][0].'</a></li>
// 											<li><a href="'.$menu[25][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[25][0].'</a></li>
// 											<li><a href="'.$menu[26][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[26][0].'</a></li>
// 										</ul>
// 									</li>
// 									<li class="nav-parent">
// 										<a><i class="fa fa-cog" aria-hidden="true"></i><span>Utilities</span></a>
// 										<ul class="nav nav-children">
// 											<li><a href="'.$menu[31][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[31][0].'</a></li>
// 										</ul>
// 									</li>
// 									<li class="nav-parent">
// 										<a><i class="fa fa-user" aria-hidden="true"></i><span>Role Master</span></a>
// 										<ul class="nav nav-children">
// 											<li><a href="'.$menu[29][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[29][0].'</a></li>
// 											<li><a href="'.$menu[30][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[30][0].'</a></li>
// 											<li><a href="'.$menu[32][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[32][0].'</a></li>
// 										</ul>
// 									</li>
// 							    </ul>
// 				';
// 			}
			/*
			
								    <li class="nav-parent">
										<a><i class="fa fa-home" aria-hidden="true"></i><span>VA</span></a>
										<ul class="nav nav-children">
											<li><a href="'.$menu[17][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[17][0].'</a></li>
											<li><a href="'.$menu[19][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[19][0].'</a></li>
											<li><a href="'.$menu[1][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[1][0].'</a></li>	
											<li><a href="'.$menu[2][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[2][0].'</a></li>
											<li><a href="'.$menu[3][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[3][0].'</a></li>
											<li><a href="'.$menu[4][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[4][0].'</a></li>
											<li><a href="'.$menu[5][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[5][0].'</a></li>
											<li><a href="'.$menu[6][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[6][0].'</a></li>
											<li><a href="'.$menu[7][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[7][0].'</a></li>
											<li><a href="'.$menu[8][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[8][0].'</a></li>
											<li><a href="'.$menu[9][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[9][0].'</a></li>
											
										</ul>
									</li>
									 <li class="nav-parent">
										<a>
											<i class="fa fa-home" aria-hidden="true"></i>
											<span>CSA</span>
										</a>
										<ul class="nav nav-children">
											<li><a href="'.$menu[18][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[18][0].'</a></li>
											<li><a href="'.$menu[11][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[11][0].'</a></li>	
											<li><a href="'.$menu[12][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[12][0].'</a></li>
											<li><a href="'.$menu[13][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[13][0].'</a></li>
											<li><a href="'.$menu[14][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[14][0].'</a></li>
											<li><a href="'.$menu[15][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[15][0].'</a></li>
											<li><a href="'.$menu[16][1].'">&nbsp;>&nbsp;&nbsp;&nbsp;'.$menu[16][0].'</a></li>
									</li>
			
			if($user_access == 2){
				$menubar = '
								<ul class="nav nav-main">
									<li><a href="'.$menu[3][1].'"><i class="fa fa-cubes" aria-hidden="true"></i>'.$menu[3][0].'</a></li>
									<li><a href="'.$menu[6][1].'"><i class="fa fa-truck" aria-hidden="true"></i>'.$menu[6][0].'</a></li>
									<li><a href="'.$menu[7][1].'"><i class="fa fa-truck" aria-hidden="true"></i>'.$menu[7][0].'</a></li>
								</ul>
					
				';
			}*/			
			// if($user_access == 3){
				// $menubar = '
								// <ul class="nav nav-main">
									// <li><a href="'.$menu[5][1].'"><i class="fa fa-shopping-cart" aria-hidden="true"></i>'.$menu[5][0].'</a></li>
									// <li><a href="'.$menu[6][1].'"><i class="fa fa-book" aria-hidden="true"></i>'.$menu[6][0].'</a></li>
									// <li><a href="'.$menu[7][1].'"><i class="fa fa-truck" aria-hidden="true"></i>'.$menu[7][0].'</a></li>
								// </ul>
				// ';
			// }
$loggeduser = isset($first_name) ? $first_name : "Admin";
$accounttype = isset($rolename) ? $rolename : "Admin Account";
?>
<!doctype html>
<html class="fixed sidebar-left-collapsed sidebar-left-xs" >
	<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<!-- Basic -->
		
		<?php echo isset($k_head_include) ? $k_head_include : "";?>

		<title><?php echo isset($k_head_title)? $k_head_title : "Head title"; ?></title>	
		<meta name="keywords" content="<?php echo isset($k_head_keywords)? $k_head_keywords : "Main Page"; ?>" />
		<meta name="description" content="<?php echo isset($k_head_desc)? $k_head_desc : "Page Desc"; ?>">
		<meta name="author" content="<?php echo isset($k_head_author)? $k_head_author : "KreonSolutions.com"; ?>">
		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />

		<!-- Specific Page Vendor CSS -->
		<link rel="stylesheet" href="assets/vendor/select2/css/select2.css" />
		<link rel="stylesheet" href="assets/vendor/jquery-ui/jquery-ui.css" />
		<?php 
			//Added due to conflict between datatables used on form for dropdownTable
			if(isset($_GET['view'])) if($_GET['view'] == 1) echo '<link rel="stylesheet" href="assets/vendor/jquery-datatables-bs3/assets/css/datatables.css" />';  
		?>
		<link rel="stylesheet" href="assets/vendor/jquery-ui/jquery-ui.theme.css" />
		<link rel="stylesheet" href="assets/vendor/dropzone/basic.css" />
		<link rel="stylesheet" href="assets/vendor/dropzone/dropzone.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css" />
		<link rel="stylesheet" href="assets/vendor/morris.js/morris.css" />
		<link rel="stylesheet" href="assets/vendor/select2-bootstrap-theme/select2-bootstrap.min.css" />

		<!-- Theme CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme.css" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="assets/stylesheets/skins/default.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme-custom.css">

		<!-- Head Libs -->
		<script src="assets/vendor/jquery/jquery.js"></script>
		<script src="assets/vendor/modernizr/modernizr.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.min.js"></script>
		
		<!--script src="jspdf/jspdf.plugin.autotable.js"></script-->
		<!--<script src="assets/vendor/jquery/jquery.js"></script>
		<script src="assets/javascripts/tables/examples.datatables.default.js"></script>
		<script src="assets/javascripts/tables/examples.datatables.row.with.details.js"></script>
		<script src="assets/javascripts/tables/examples.datatables.tabletools.js"></script>-->

		<style> 
    		
    		@font-face {
                font-family: PoppinsR;
                src: url("fonts/Poppins-Regular.ttf");
            }
              
            body {
                font-family: PoppinsR;
                color: #353535;
            }
            body .btn-primary {
                background-color: #09284e;
                border-color: #09284e;
            }
			@media (min-width: 768px){
				.userbox{
					float: right;
				}
				.content-body {
					padding: 20px;
				}
			}
			.panel-actions{
				position: absolute;				
			}
			.userbox{
				margin-right: 26px;
				margin-top: 12px;
			}
			.header-right{
				height: 0 !important;
			}
			.required { 
				font-size: 1em !important; 
			} 
			.nano-content{
				right:9px !important;
			}
			html.sidebar-left-xs .sidebar-left ul.nav-main li i {
				font-size: 1.7rem;
			}
			.header {
				border-top: 1px solid #EDEDED;
			}
			
			ul.nav-main li a { color: #ffffff;}
			ul.nav-main > li > a:hover, ul.nav-main > li > a:focus {
				background-color: #808182;
			}
			ul.nav-main > li {
				background-color: #09284e;
			}
			.page-header { background: #09284e; }
			.sidebar-left .sidebar-header .sidebar-toggle{ background: #09284e !important; }
			@media only screen and (min-width: 768px)
			html.fixed .sidebar-left .nano-content {
				background-color: #09284e !important;
			}
			html.no-overflowscrolling .sidebar-left .nano{
				background: #09284e;
				box-shadow: -5px 0 0 #2F3139 inset;
			}
			.sidebar-left .sidebar-header .sidebar-title {
				background: #09284e;
			}
			.nano-content {
				width:218px;
			}

			/* Loader css */
			#loader {
				border: 12px solid #f3f3f3;
				border-radius: 50%;
				border-top: 12px solid #3498db;
				width: 70px;
				height: 70px;
				animation: spin 1s linear infinite;
			}
 
			.center {
				position: absolute;
				top: 0;
				bottom: 0;
				left: 0;
				right: 0;
				margin: auto;
			}
 
			@keyframes spin {
				100% {
					transform: rotate(360deg);
				}
			}
			
		</style>
	</head>
	<body>
		<section class="body">
			<!-- start: header -->
			<header class="header">
				<div class="logo-container">
					<a href="#" class="logo">
						<img src="assets/images/kreon-logo.png" height="45" alt="Porto Admin" />
					</a>
					<div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
						<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
					</div>
					<span class="separator"></span>
					<div id="userbox" class="userbox">
						<a href="#" data-toggle="dropdown">
							<div class="profile-info" data-lock-name="" data-lock-email="">
								<span style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;max-width: 170px;" class="name"><?php echo $name.'<br> '.$loggeduser; ?></span>
								<!--span class="role"><?php echo $accounttype; ?></span-->
							</div>
			
							<i class="fa custom-caret"></i>
						</a>
			
						<div class="dropdown-menu">
							<ul class="list-unstyled">
								<li class="divider"></li>
								<li>
									<a role="menuitem" tabindex="-1" href="settings.php"><i class="fa fa-cog"></i> Settings</a>
								</li>
								<!--li>
									<a role="menuitem" tabindex="-1" href="#"><i class="fa fa-user"></i> My Profile</a>
								</li-->
								<li>
									<a role="menuitem" tabindex="-1" href="logout.php"><i class="fa fa-power-off"></i> Logout</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			
				<!-- start: search & user box -->
				<div class="header-right">
					
				</div>
				<!-- end: search & user box -->
			</header>
			<!-- end: header -->

			<div class="inner-wrapper">
				<!-- start: sidebar -->
				<aside id="sidebar-left" class="sidebar-left">
				
					<div class="sidebar-header">
						<div class="sidebar-title">
							Navigation
						</div>
						<div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
							<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
						</div>
					</div>
				
					<div class="nano">
						<div class="nano-content">
							<nav id="menu" class="nav-main" role="navigation">
								<?php echo $menubar; ?>						
							</nav>				
							<hr class="separator" />
						</div>
				
					</div>
				
				</aside>
				<!-- end: sidebar -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2><?php echo isset($k_page_title)?$k_page_title : "No Title"; ?></h2>
						<!--div class="right-wrapper pull-right">
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div-->
					</header>
					<?php 
						$saveNoify = isset($_GET["save"]) ? $_GET["save"] : 0;
						if($saveNoify > 0){ ?>
					<div class="alert alert-success alert-dismissible show" style="margin-top: 0px;">
						<strong>Success!</strong> Your entry has been saved successfully.
						<button type="button" class="close" data-dismiss="alert">&times;</button>
					</div>
					<?php } ?>
					<!-- start: page -->