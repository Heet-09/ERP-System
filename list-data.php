<?php

$FormID = isset($_REQUEST['form']) ? $_REQUEST['form'] : 0;

$k_head_title="Form";

$k_head_include = "";

$editID = isset($_POST['editID']) ? $_POST['editID'] : 0;

$viewpage = isset($_GET['view']) ? $_GET['view'] : 0;



include "form-init.php";



if($viewpage > 0){

	

}else{

?>



<div class="container1">

    <div class="row">

        <div class="col-md-12 allfields">

        	<?php $div="";
                echo '<div id="loader" class="spinner-border text-primary center" role="status"><span class="sr-only">Loading...</span></div>';
            	for($i = 0; $i < sizeof($code); $i++){
	
            	    echo '<div class="'.$code[$i][0].'">';

                    echo createInputs($code[$i]);

            	    echo  '</div>';

            	}

        	?>

        </div>

        <div class="col-md-12 allgrids">

        <br /><br />

        	<?php 

        	for($i = 0; $i < sizeof($dynamix); $i++){

        		echo createMore1($dynamix[$i][0], $dynamix[$i][1], $radio, $editID);

        	}

        	?>

        </div>

    </div>

</div>


<!--GENERAL ON LOAD SCRIPT FOR THE ENTIRE PAGE VIEW EDIT AND ADD-->
<script>
	$(document).ready(function() {
	    $(".page-header").html("<h2><?php echo $viewSettings[1]; ?></h2>");
	    $(document).prop('title', '<?php echo $viewSettings[1]; ?>');
	   // $('form').attr('action', 'db_backup_31.php');
	});
	function getTodaysDate(){
	    var d = new Date();
        var month = d.getMonth()+1;
        var day = d.getDate();
        var output = ((''+day).length<2 ? '0' : '') + day + '/'
            + ((''+month).length<2 ? '0' : '') + month + '/' 
            + d.getFullYear();
        return(output);
	}
	function formatDate(dt){
        const d = new Date(dt);
        // return d.getDate().toString().padStart(2, '0') + '/' + d.getMonth() + 1 + '/' + d.getFullYear();
        var month = d.getMonth()+1;
        var day = d.getDate();
        var output = ((''+day).length<2 ? '0' : '') + day + '/'
            + ((''+month).length<2 ? '0' : '') + month + '/' 
            + d.getFullYear();
        return(output);
	}
	function formatReverseDate(dt){
        const d = new Date(dt);
        // return d.getDate().toString().padStart(2, '0') + '/' + d.getMonth() + 1 + '/' + d.getFullYear();
        var month = d.getMonth()+1;
        var day = d.getDate();
        var output = d.getFullYear() + '-'
            + ((''+month).length<2 ? '0' : '') + month + '-' 
            + ((''+day).length<2 ? '0' : '') + day
            ;
        return(output);
	}
	
	//FUNCTION NOT YET USED BUT CAN BE USED INSTEAD OF formatReverseDate
	function getDateFormat(date) {
        var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
        
        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;
        var date = new Date();
        date.toLocaleDateString();
        
        return [day, month, year].join('-');
    }
    document.onreadystatechange = function() {
        if (document.readyState !== "complete") {
            document.querySelector("#loader").style.visibility = "visible";
        } 
        else {
            document.querySelector("#loader").style.display = "none";
        }
    };

</script>


<?php }

	include "form-close.php"; 

?>