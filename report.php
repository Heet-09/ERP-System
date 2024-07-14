<?php
$ReportID = isset($_REQUEST['ReportID']) ? $_REQUEST['ReportID'] : 0;
$k_head_title="Report";
$k_head_include = "";
include "report-init.php";

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
    $url = "https://";   
else  
    $url = "http://";  
$url .= $_SERVER['HTTP_HOST'];   
$url.= $_SERVER['REQUEST_URI'];
$urlpos = strpos($url, '&', strpos($url, '&') + 1);
$url = substr($url, 0, $urlpos);
// echo $url;

$sql = "SELECT * FROM $db[0] WHERE 1=1 ";
$requestArray = [];

for($i = 0; $i < sizeof($filterCode); $i++){
    if(isset($_REQUEST[$filterCode[$i][1]]) || isset($_REQUEST[$filterCode[$i][2]])){
        if(isset($_REQUEST[$filterCode[$i][1]])){
            $requestArray[$filterCode[$i][1]] = $_REQUEST[$filterCode[$i][1]];
        }else{
            $requestArray[$filterCode[$i][2]] = $_REQUEST[$filterCode[$i][2]];
        }
    }
}

foreach($_REQUEST as  $name => $value){
    if($name != "ReportID" && $name != "view"){
        // $requestArray[$name] = $value;
        if(isset($name)){
            if(gettype($value) == "array"){
                if(sizeof($value) > 0) $sql .= " AND $name IN ( '" . implode("','", $value) . "' ) ";
            }else{
				if(is_numeric($value)){
					$valueINT = (int) $value;
					if($valueINT !== 0) $sql .= " AND $name = $valueINT ";
				}else{
                	if(strlen($value) > 0) $sql .= " AND $name = '$value' ";
				}
            }
        }
    }
}
// echo $sql;

// var_dump($_REQUEST);

$viewResult = db::getInstance()->db_select($sql);

?>

<link rel="stylesheet" type="text/css" href="/assets/vendor/jquery-datatables/extras/TableTools/css/dataTables.tableTools.css">
<script src="assets/js/dataTables.buttons.min.js"></script>
<script src="assets/js/buttons.bootstrap4.min.js"></script>
<script src="assets/js/buttons.html5.min.js"></script>
<script src="assets/js/buttons.print.min.js"></script>
<script type="text/javascript" language="javascript" src="/assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>


<section class="panel">
    <header class="panel-heading">
		<div class="panel-actions">
            <input class="btn btn-primary bizbtn" type="button" onclick="tableToExcel('datatable-tabletools', 'GSTR1 Report')" value="Excel">
            <!--<button onclick="exportTableToExcel('datatable')" class="btn btn-primary bizbtn" style="font-size:10px;">EXCEL</button>-->
            <!--<button onclick="window.print()"  class="btn btn-primary bizbtn" style="font-size:10px;"> Test PRINT</button>-->
            <button onclick="printData()" id="download_pdf" class="btn btn-primary bizbtn" style="font-size:10px;">PDF/PRINT</button>
            
        </div>
		<h2 class="panel-title"><?php $viewReportSettings[0]; ?></h2>
		<p class="panel-subtitle"></p>
	</header>
	<form method="get">
	<div class="panel-body">
        <?php
            foreach($_GET as $name => $value) {
                $name = htmlspecialchars($name);
                if($name == "ReportID" || $name == "view"){
                    $value = htmlspecialchars($value);
                    echo '<input type="hidden" name="'. $name .'" value="'. $value .'">';
                }
            }
        ?>
	    <div class="row">
            <div class="reportTable">
        	<?php $div="";
                
            	for($i = 0; $i < sizeof($filterCode); $i++){
            
            	    echo '<div class="'.$filterCode[$i][0].'">';

                    echo createReportFilters($filterCode[$i],$viewResult,$requestArray);

            	    echo  '</div>';
					

            	}


        	?>

        </div>
			</div>
        <br/>
        <div class="row">
    		<div class="col-md-6">
    			<input class="btn btn-danger" type="submit" id="submit" onclick1="filterData();" value="FILTER">
    			<a href="<?php echo $url; ?>" class="btn btn-primary">CLEAR</a><br />
    			<!-- <img src="loading-gif.gif" id="loading" style="width: 60px;margin-top: 30px;"/> -->
    		</div>
    	</div>
	</div>
	</form>
    <!--<div class="panel-body">-->
    <div class="panel-body" id="datatable">
    
        <table class="table table-bordered table-striped mb-none" border="1" id="datatable-tabletools" data-swf-path="assets/vendor/jquery-datatables/extras/TableTools/swf/copy_csv_xls_pdf.swf">
            <?php echo createReportTable($result1, $viewResult); ?>
        </table>
            
    </div>
</section>








<!--GENERAL ON LOAD SCRIPT FOR THE ENTIRE PAGE -->
<script>
	$(document).ready(function() {
	    $(".page-header").html("<h2><?php echo $viewReportSettings[1]; ?></h2>");
	    $(document).prop('title', '<?php echo $viewReportSettings[1]; ?>');
	//    // $('form').attr('action', 'db_backup_31.php');
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

</script>

<script>
	function DateCheck()
	{
	  var StartDate= document.getElementById('txtStartDate').value;
	  var EndDate= document.getElementById('txtEndDate').value;
	  var eDate = new Date(EndDate);
	  var sDate = new Date(StartDate);
	  if(StartDate!= '' && StartDate!= '' && sDate> eDate)
		{
		alert("Please ensure that the End Date is greater than or equal to the Start Date.");
		return false;
		}
	} 
	function exportTableToExcel(datatable, filename = '')
	{
		var downloadLink;
		var dataType = 'application/vnd.ms-excel';
		var tableSelect = document.getElementById(datatable);
		var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
		
		// Specify file name
		filename = filename?filename+'.xls':'excel_data.xls';
		
		// Create download link element
		downloadLink = document.createElement("a");
		
		document.body.appendChild(downloadLink);
		
		if(navigator.msSaveOrOpenBlob){
			var blob = new Blob(['\ufeff', tableHTML], {
				type: dataType
			});
			navigator.msSaveOrOpenBlob( blob, filename);
		}else{
			// Create a link to the file
			downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
		
			// Setting the file name
			downloadLink.download = filename;
			
			//triggering the function
			downloadLink.click();
		}
	}
	function printData()
	{
	   var divToPrint=document.getElementById("datatable-tabletools");
	   newWin= window.open("");
	   newWin.document.write(divToPrint.outerHTML);
	   newWin.print();
	   newWin.close();
	}
	$('button').on('click',function(){
		printData();
	});
</script>
<script type="text/javascript">
	var tableToExcel = (function() {
	  var uri = 'data:application/vnd.ms-excel;base64,'
		, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
		, base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
		, format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
	  return function(table, name) {
		if (!table.nodeType) table = document.getElementById(table)
		var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
		window.location.href = uri + base64(format(template, ctx))
	  }
	})()
</script>
<script>
		$(document).ready(function() {
		$('#download_pdf').click(function () {
		var pdf = new jsPDF('1', 'pt', 'letter')
		, source = $('#datatable')[0]
		, specialElementHandlers = {
			'#bypassme': function(element, renderer){      
				return true
			}
		}

		margins = {
			top: 60,
			bottom: 60,
			left: 40,
			width: 800
		  };
		  
		pdf.fromHTML(
			source
			, margins.left
			, margins.top 
			, {
				'width': margins.width 
				, 'elementHandlers': specialElementHandlers
			},
			function (dispose) {
				pdf.save('download.pdf');
			  },
			margins
		  )
		});
			});
</script>


<?php 
	include "report-close.php"; 
?>