<?php
session_start();
$k_head_title="Master Roles";
$k_page_title ='Master Roles';
include 'k_files/k_header.php';
$k_head_title="Master Roles";
$k_head_include = '
		<link rel="stylesheet" href="assets/vendor/select2/css/select2.css" />
		<link rel="stylesheet" href="assets/vendor/select2-bootstrap-theme/select2-bootstrap.min.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css" />
		
		<style>
			.multi-check {
				border: 1px solid #cacaca;
				padding: 5px;
				padding-left: 10px;
				max-height: 90px;
				overflow-y: scroll;
			}
			button.btn.btn-danger {
                display: none;
         }
		</style>
';
$viewpage = 1;
$editID = 2;
//$editID = isset($_POST['editID']) ? $_POST['editID'] : 0;
//$viewID = isset($_POST['viewID']) ? $_POST['viewID'] : 0;
//$viewpage = isset($_GET['view']) ? $_GET['view'] : 0;

$k_table_title = "Master Roles";
    $k_table_headings = '<tr>
								<th>Sr No.</th>
	                            <th>Role Name</th>
	                            <th>Edit</th>
						</tr>';
	
	$sql="SELECT * FROM `pagerolemaster`";
	$result = db::getInstance()->db_select($sql);	
	$k_table_body = "";
	//echo '<input type="checkbox" name="RequestID[]" value="0">';
	for($i = 0; $i < $result['num_rows']; $i++){
		  
		    $name= $result['result_set'][$i]['Label']; 
		
			$srno = $i + 1;
	        $k_table_body .= '<tr>
								<td class="action">'.$srno.'</td>
								 <td>'.$name.'</td>
								 <td><form name="form" action="/page-role-access.php" method="POST">
        							<input type="hidden" name="editID" value="'.$result['result_set'][$i]['RoleId'].'">							
        							<button class="btn btn-primary bizbtn" style="font-size;10px;" type="submit" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="edit"><i class="fa fa-pencil"></i></button>
        						 </form></td>
							</tr>';	
            }	
	// $k_table_title = 'Project Name: <b>'.$Name.'</b>';
	$k_table_title .= '<a href="page-role-access.php" style="float: right;margin-right:60px;"><input type="submit" value="Add" class="btn btn-danger" /></a>';
	include "k_files/k_table.php";
	

	$k_footer_before = '
		<script src="assets/vendor/select2/js/select2.js"></script>
		<script src="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>
		';
	$k_footer_before = '
		<script src="assets/vendor/select2/js/select2.js"></script>
		<script src="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>
		';
	include "k_files/k_footer.php"; 
?>