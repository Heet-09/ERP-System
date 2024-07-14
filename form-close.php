<?php
$FormID = isset($_REQUEST['form']) ? $_REQUEST['form'] : 0;
if($viewpage == 1){ //VIEW MODE
	
}else{ //ADD-EDIT MODE
$_SESSION['csrftoken'] = md5(uniqid(mt_rand(), true));
 ?>
 
    <div class="row">
    <div class="col-md-12">
    	<label class="col-md-4 control-label"></label>
    	 <input  type="hidden" value="<?php echo $editID?>" name="editID" /><br/>
    	 <input  type="submit" value="<?php echo $buttonname?>" class="btn btn-primary">
		 <?php 
		 	if($saveAddMore == 1){
				echo '<input  type="submit" name="saveButton" value="'.$buttonsaveaddmore.'" class="btn btn-primary">';
			}
		 ?>
		 <?php
		 	if($saveAndPreview == 1){
				echo '<a href="" class="btn btn-primary">SavePreview</a>';
			}
		 ?>
    	 <a href="<?php echo $_SERVER['PHP_SELF']."?form=$FormID"; ?>" class="btn btn-secondary">CLEAR</a>
    	 <a href="<?php echo $_SERVER['PHP_SELF']."?view=1&form=$FormID"; ?>" class="btn btn-tertiary">CANCEL</a>
    	 <input type="hidden" name="csrftoken" value="<?php echo $_SESSION['csrftoken'] ?? '' ?>">

    </div>
    </div>
</form>
<?php } ?>
	</div>
</section>
<!-- end: page -->
<?php
	$k_footer_before = '';
	//if(is_resource($connect)) mysql_close($connect);
	include "k_files/k_footer.php";
?>