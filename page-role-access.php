<?php
$k_head_title = 'Page Role Access';
$k_head_keywords ='Page Role Access';
$k_head_desc ='';
$k_head_author ='KreonSolutions.com';
$k_page_title ='Page Role Access';
include 'k_files/k_header.php';
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
		</style>
';

//$k_debug = 1;
$editID = isset($_POST['editID']) ? $_POST['editID'] : 0;
$viewpage = isset($_GET['view']) ? $_GET['view'] : 0;
$viewID = isset($_POST['viewID']) ? $_POST['viewID'] : 0;
$val = "";
if((int)$editID > 0){
	$sql="SELECT * FROM `pagerolemaster` where RoleId = " . $editID;
	$result = db::getInstance()->db_select($sql);	
	for($i = 0; $i < $result['num_rows']; $i++){
		$val = $result['result_set'][$i]['Label'];
	}
}
?>	
<script>
    $(document).ready(function() {
        $('#addAll').change(function() {
            if(this.checked) {
                var returnVal = confirm("Are you sure?");
                $(this).prop("checked", returnVal);
                if(returnVal)   $('.ch_add').prop('checked', true);
            }else{
                $('.ch_add').prop('checked', false);
            }     
        });
        $('#editAll').change(function() {
            if(this.checked) {
                var returnVal = confirm("Are you sure?");
                $(this).prop("checked", returnVal);
                if(returnVal)   $('.ch_edit').prop('checked', true);
            }else{
                $('.ch_edit').prop('checked', false);
            }     
        });
        $('#delAll').change(function() {
            if(this.checked) {
                var returnVal = confirm("Are you sure?");
                $(this).prop("checked", returnVal);
                if(returnVal)   $('.ch_delete').prop('checked', true);
            }else{
                $('.ch_delete').prop('checked', false);
            }     
        });
        $('#listAll').change(function() {
            if(this.checked) {
                var returnVal = confirm("Are you sure?");
                $(this).prop("checked", returnVal);
                if(returnVal)   $('.ch_listview').prop('checked', true);
            }else{
                $('.ch_listview').prop('checked', false);
            }     
        });
        $('#otherAll').change(function() {
            if(this.checked) {
                var returnVal = confirm("Are you sure?");
                $(this).prop("checked", returnVal);
                if(returnVal)   $('.ch_other').prop('checked', true);
            }else{
                $('.ch_other').prop('checked', false);
            }     
        });
        
    });

</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<section class="panel">
	<header class="panel-heading">
		<h2 class="panel-title">Page Role Access</h2>
	</header>
<div class="panel-body">
	<div class="col-md-12">
		<form class="form-bordered" method="post" action="PageRoleAccessdb.php" enctype="multipart/form-data">
			<div class="row">
    			<div class="col-md-8">
    				<div class="col-md-4"><label>Role Name <span class="required">*</span></label>
				        <input required="" class="form-control dyn1" value="<?php echo $val; ?>" type="text" name="Label" id="Label" />
				    </div>
				</div>
			</div>
			
		<hr/>
		<table class="table table-bordered table-striped mb-none">
		<thead class=" thead-light">
			<tr>
				<th style="width:60px" width="60">Pages</th>
				<th style="width:60px" width="60">Add <input type="checkbox" id="addAll" /></th>
				<th style="width:60px" width="60">Edit <input type="checkbox" id="editAll" /></th>
				<th style="width:60px" width="60">Delete <input type="checkbox" id="delAll" /></th>
				<th style="width:60px" width="60">List View <input type="checkbox" id="listAll" /></th>
				<th style="width:60px" width="60">Other Button <input type="checkbox" id="otherAll" /></th>
			</tr>	
		</thead>	
		<tbody>	
		<?php 
			$sql="SELECT kpageaccess.* FROM `kpageaccess`";
			$result = db::getInstance()->db_select($sql);	
		//	print_r($result);
			for($i = 0; $i < $result['num_rows']; $i++){
				$accessId 		= $result['result_set'][$i]['ID'];
				$displayname 	= $result['result_set'][$i]['displayname'];
				$AddCheck = ""; $EditCheck = ""; $ListCheck = ""; $OtherCheck = ""; $DeleteCheck = "";
				if((int)$editID > 0){
    				$sql2 = "SELECT * FROM pageroleaccess where PageAccessID = " . $result['result_set'][$i]['ID'] . " AND RoleID = " . $editID;
    		        $result2 = db::getInstance()->db_select($sql2);
    		        for($j = 0; $j < $result2['num_rows']; $j++){
    		            $AddCheck = $result2['result_set'][$j]['AddBtn'] == 1 ? "checked" : "";
        				$EditCheck = $result2['result_set'][$j]['EditBtn'] == 1 ? "checked" : "";
        				$ListCheck = $result2['result_set'][$j]['ListView'] == 1 ? "checked" : "";
        				$OtherCheck = $result2['result_set'][$j]['OtherBtn'] == 1 ? "checked" : "";
        				$DeleteCheck = $result2['result_set'][$j]['DeleteBtn'] == 1 ? "checked" : "";
    		        }
				}
    		    ?>
        		<tr>
        			<td><input type="hidden" id="accessId" name="accessId[<?php echo $i; ?>][]" value="<?php echo $accessId;?>"><?php echo $displayname;?></td>
        			<td><input type="checkbox" class="ch_add" <?php echo $AddCheck; ?> id="add" name="add[<?php echo $i; ?>][]" /></td>
        			<td><input type="checkbox" class="ch_edit" <?php echo $EditCheck; ?> id="edit" name="edit[<?php echo $i; ?>][]" /></td>
        			<td><input type="checkbox" class="ch_delete" <?php echo $DeleteCheck; ?> id="delete" name="delete[<?php echo $i; ?>][]" /></td>
        			<td><input type="checkbox" class="ch_listview" <?php echo $ListCheck; ?> id="listview" name="listview[<?php echo $i; ?>][]" /></td>
        			<td><input type="checkbox" class="ch_other" <?php echo $OtherCheck; ?> id="other" name="other[<?php echo $i; ?>][]" /></td>
        		</tr>
    			<?php
		    }
		    if((int)$editID > 0){
		        $btnName = "Update";
		    }else{
		        $btnName = "SAVE";
		    }
			 ?>
		</tbody>
		</table>				
			<div class="row">
    			<div class="col-md-12">
    				<label class="col-md-4 control-label"></label>
    				<input type="hidden" value="<?php echo $editID; ?>" name="editID"><br>
    				<input type="submit" value="<?php echo $btnName; ?>" class="btn btn-primary">
    				<input type="hidden" id="totalPages" name="totalPages" value="<?php echo $i; ?>" />
    				<a href="roles.php?view=1" class="btn btn-danger">CANCEL</a>			
    			</div>
			</div>
		</form> 
	</div>	
</div>
<?php include "k_files/k_footer.php"; ?>s