
<!DOCTYPE html>
<html lang="en">
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

  foreach($_REQUEST as $name => $value) {
      if($name != "ReportID" && $name != "view") {
          // $requestArray[$name] = $value;
          if(isset($name)) {
              if(gettype($value) == "array") {
                  if(sizeof($value) > 0) {
                      $sql .= " AND $name IN ('" . implode("','", $value) . "' ) ";
                  }
              } else {
                  if(is_numeric($value)) {
                      $valueINT = (int) $value;
                      if($valueINT !== 0) {
                          $sql .= " AND $name = $valueINT ";
                      }
                  } else {
                      if(strlen($value) > 0) {
                          $sql .= " AND $name = '$value' ";
                      }
                  }
              }
          }
      }
  }


  $viewResult = db::getInstance()->db_select($sql);

?>

<link rel="stylesheet" type="text/css" href="/assets/vendor/jquery-datatables/extras/TableTools/css/dataTables.tableTools.css">
<script src="assets/js/dataTables.buttons.min.js"></script>
<script src="assets/js/buttons.bootstrap4.min.js"></script>
<script src="assets/js/buttons.html5.min.js"></script>
<script src="assets/js/buttons.print.min.js"></script>
<script type="text/javascript" language="javascript" src="/assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>

<script>
	var templateId = "0";
	var typeId="0";
  $(document).ready(function () {
	var selectedType;
    $('.datagrid-display').hide(); // Hide datagrid-display initially
    $('.pivotgrid-display').hide(); // Hide pivotgrid-display initially
    $('.typeSelect').change(function () {
      selectedType = $(this).val();
      $('.datagrid-display').hide(); // Hide datagrid-display
      $('.pivotgrid-display').hide(); // Hide pivotgrid-display
      $("#templateSelect").val(''); // Clear template selection

        if (selectedType === "1") {
            $('.gridTemplate1').show(); // Show templates with TemplateTypeID 1 (Pivot Grid)
            $('.gridTemplate2').hide(); // Hide templates with TemplateTypeID 2 (Data Grid)
        } else if (selectedType === "2") {
            $('.gridTemplate1').hide(); // Hide templates with TemplateTypeID 1 (Pivot Grid)
            $('.gridTemplate2').show(); // Show templates with TemplateTypeID 2 (Data Grid)
        }
		typeId = $("#typeSelect").val();
		console.log("CHANGED Type ID - " + typeId);
    console.log("SelectedType" + selectedType);
	});


	$('.templateSelect').change(function () {
       if (selectedType === "1") {
			$('.pivotgrid-display').show(); // Show pivotgrid-display if Pivot Grid is selected
			$('.datagrid-display').hide(); // Hide datagrid-display
		} else if (selectedType === "2") {
			$('.datagrid-display').show(); // Show datagrid-display if Data Grid is selected
			$('.pivotgrid-display').hide(); // Hide pivotgrid-display
		}
		templateId = $("#templateSelect").val();
		console.log("CHANGED TEMPLATE ID - " + templateId);
		loadData();
	});

});
</script>
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
    <style>
        .popup {
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            display: none;
        }
        .popup-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888888;
            width: 30%;
            font-weight: bolder;
        }
        .popup-content button {
            display: block;
            margin: 0 auto;
        }
        .show {
            display: block;
        }
        #myButton{
            margin-top:px;
        }
    </style>
    <a href='#' class='btn btn-primary' onclick='$("#saveAsModal").modal("show");'>Save Template As</a>
        <div class="modal fade" id="saveAsModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">×</button> 
                        <h4 class="modal-title">Add</h4>                                                             
                    </div> 
                <div class="modal-body" >
                  <!-- <form id="modalForm" name="modal" role="form"> -->
                    <div id="ModelContent" >
                    </div>
                    <div class="modal-footer" style="margin-top:10px;">					
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <input type="submit" class="btn btn-success" id="submit">
                    </div>
                  <!-- </form> -->
                </div>   
            </div>                                                                       
        </div>                                          
          <!-- </div> -->
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
				    <?php 
					    $div="";
					    for($i = 0; $i < sizeof($filterCode); $i++){
						    echo '<div class="'.$filterCode[$i][0].'">';
                            echo createReportFilters($filterCode[$i],$viewResult,$requestArray);
                            echo  '</div>';
                        }
					?>
					<span class="col-md-3 type" required>
						<label for="type">Type:</label>
						<select name="type" id="typeSelect" class="typeSelect form-control" required>

							<option value></option>
							<?php
							$sql ='SELECT ID, Label FROM kreporttypes';
							$result = db::getInstance()->db_select($sql);
							for($i = 0; $i < $result['num_rows']; $i++){ //WHILE LOOP FOR $row
									echo '<option value="'.$result['result_set'][$i]['ID'].'">' . $result['result_set'][$i]['Label'] . '</option>';
								}
							?>
						</select>
					</span>
					<span class="col-md-3 template" required>
						<label for="template">Template:</label>
                        <select name="template" class="templateSelect form-control" id="templateSelect" required>
                            <option value=""></option>
                            <?php
                                $sql = 'SELECT ID, TemplateName, TemplateTypeID FROM kreport_template_data';
                                $result1 = db::getInstance()->db_select($sql);
                                // Output the number of rows
                                $num_rows = isset($result1['num_rows']) ? $result1['num_rows'] : count($result1['result_set']);
                                echo "Number of rows in the result set: $num_rows<br>";
                                // Loop through the result set and output options
                                for ($i = 0; $i < $num_rows; $i++) {
                                    echo '<option class="gridTemplate' . $result1['result_set'][$i]['TemplateTypeID'] . '" value="' . $result1['result_set'][$i]['ID'] . '">' . $result1['result_set'][$i]['TemplateName'] . '</option>';
                                }
                            ?>
                        </select>
                    </span>
                    <br/>
                    <div class="row">
                        <div class="col-md-6">
                            <input class="btn btn-danger" type="submit" id="submit" onclick1="filterData();" value="FILTER">
                            <a href="<?php echo $url; ?>" class="btn btn-primary">CLEAR</a><br />
                        </div>
                    </div>
                </div>      
			</div>
        </div>
    </form>
        
		<div class="pivotgrid-display">
			<div class="dx-viewport demo-container">
				<div id="sales">
                </div>
				<div id="pivotgrid-demo">		
    				<div id="pivotgrid">
	    			</div>
	    		</div>
		    </div>
    	</div>
<script>

function loadData() {
  $(function() {
    // Fetch data from the URL
    fetch("reportGetDataFromView.php?ViewName=<?php echo $ReportID; ?>")
      .then(response => response.json())
      .then(dataObject => 
        {
        // Check if dataObject is an array
            if (Array.isArray(dataObject)) {
                var dataArray = dataObject;
                dataArray = dataObject.map(item => {
                // Ensure each field is converted to an integer
                    return {
                        ...item,
                        Total: parseInt(item.Total, 10),
                        Meter: parseInt(item.Meter, 10),
                        Lumps: parseInt(item.Lumps, 10),
                    };
                });
                console.log("Fetched data:", dataArray);
                console.log("Fetched data:", dataArray);

          // Initialize pivot grid with appropriate settings
            const salesPivotGrid = $("#sales").dxPivotGrid({
                allowSortingBySummary: true,
                allowSorting: true,
                allowFiltering: true,
                height: 490,
                showBorders: true,
                rowHeaderLayout: "tree",
                onContextMenuPreparing: contextMenuPreparing,
                headerFilter: {
                    search: {
                        enabled: true,
                    },
                    showRelevantValues: true,
                    width: 300,
                    height: 500,
                },
                fieldPanel: {
                    showColumnFields: true,
                    showDataFields: true,
                    showFilterFields: true,
                    showRowFields: true,
                    allowFieldDragging: true,
                    visible: true,
                },
                export: {
                    enabled: true,
                },
                stateStoring: {
                    enabled: true,
                    type: "custom",
                    customLoad: function() {
                        return sendStorageRequest("organisatieKey", "json", "GET");
                    },
                    customSave: function(gridState) {
                        return sendStorageRequest("organisatieKey", "text", "PUT", gridState);
                    },
                },
                fieldChooser: {
                    enabled: true,
                    applyChangesMode: "instantly",
                    allowSearch: true,
                },
                scrolling: {
                    mode: "virtual",
                },
                onCellClick(e) {
                if (e.area === "data") {
                    const pivotGridDataSource = e.component.getDataSource();
                    const rowPathLength = e.cell.rowPath.length;
                    const rowPathName = e.cell.rowPath[rowPathLength - 1];
                    const popupTitle = `${rowPathName || "Total"} Drill Down Data`;
                    const drillDownDataSource = pivotGridDataSource.createDrillDownDataSource(e.cell);
                    salesPopup.option("title", popupTitle);
                    salesPopup.show();
                    }
                },
                dataSource: {
                    store: {
                        type: "array",
                        data: dataArray,
                    },
                },
          }).dxPivotGrid("instance");
        } else {
          console.error("Data object is missing 'data' property.");
        }
      })
      .catch(error => {
        console.error("Error fetching data:", error);
      });
  });

    function sendStorageRequest(key, datatype, type, data) {
        console.log(templateId);
        var deferred = $.Deferred();
        if (data !== undefined) var d = JSON.stringify(data);
        else var d = "";
        var storageRequestSettings = {
        url: "reportOptionsDataStorage.php?ReportID=<?php echo $ReportID; ?>&templateId=" +
                templateId +
                "&typeID=" +
                typeId +
                "&data=" +
                d,
        headers: {
            Accept: "text/html",
            "Content-Type": "text/html",
        },
        type: type,
        dataType: datatype,
        success: function(data) {
            console.log("Success");
            console.log(data);
            deferred.resolve(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            deferred.reject();
        },
        };
        if (data) {
        console.log("TEmplate 2 : " + templateId);
        console.log("SENDING...");
        storageRequestSettings.data = JSON.stringify(data);
        console.log(data);
        } else {
        console.log("TEmplate 3 : " + templateId);
        console.log("RECEIVING...");
        }
        $.ajax(storageRequestSettings);
        return deferred.promise();
    }

  function contextMenuPreparing(e) {
    const dataSource = e.component.getDataSource();
    const sourceField = e.field;

    if (sourceField) {
      if (!sourceField.groupName || sourceField.groupIndex === 0) {
        e.items.push({
          text: 'Hide field',
          onItemClick() {
            let fieldIndex;
            if (sourceField.groupName) {
              fieldIndex = dataSource
                .getAreaFields(sourceField.area, true)[sourceField.areaIndex]
                .index;
            } else {
              fieldIndex = sourceField.index;
            }

            dataSource.field(fieldIndex, {
              area: null,
            });
            dataSource.load();
          },
        });
        
        if (sourceField.dataType === 'number') {
          const setSummaryType = function (args) {
            dataSource.field(sourceField.index, {
              summaryType: args.itemData.value,
            });
            
            dataSource.load();
          };
          const menuItems = [];
          
          e.items.push({ text: 'Summary Type', items: menuItems });
          
          $.each(['Sum', 'Avg', 'Min', 'Max'], (_, summaryType) => {
            const summaryTypeValue = summaryType.toLowerCase();
            
            menuItems.push({
              text: summaryType,
              value: summaryType.toLowerCase(),
              onItemClick: setSummaryType,
              selected: e.field.summaryType === summaryTypeValue,
            });
          });
        }
      }
    }
  }  

  const salesPopup = $("#sales-popup").dxPopup({
    width: 600,
    height: 400,
    showCloseButton: true,
    contentTemplate(contentElement) {
      $("<div />")
        .addClass("drill-down")
        .dxDataGrid({
          width: 560,
          height: 300,
          columns: ["PartyName", "BrokerName", "Remark"],
        })
        .appendTo(contentElement);
    },
    onShowing() {
      $(".drill-down")
        .dxDataGrid("instance")
        .option("dataSource", drillDownDataSource);
    },
    onShown() {
      $(".drill-down").dxDataGrid("instance").updateDimensions();
    },
  }).dxPopup("instance");
}

</script>
</div>




</div>


</div>
</div>
	
    <!--<div class="panel-body">-->
    <!-- <div class="panel-body" id="datatable">
    
        <table class="table table-bordered table-striped mb-none" border="1" id="datatable-tabletools" data-swf-path="assets/vendor/jquery-datatables/extras/TableTools/swf/copy_csv_xls_pdf.swf">
            <?php echo createReportTable($result1, $viewResult); ?>
        </table>
            
    </div> -->
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
	$('#printButton').on('click',function(){
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
<script>
  function saveTemplateAs(){
    var templateName = $("#templateName").val();
  $.ajax({
				type : 'POST',
				dataType: 'json',
				data : {templateName:templateName,},
				url : '/api/form.php?FormID='+FormId+'&editID=0',
				success : function(result){
					// console.log(result);
					showModalField(result,FormId,targetFieldId,formType,AddModal,AddModalForm); //On ajax response create input fields
				}
			});
  }
</script>

<?php 
	include "report-close.php"; 
?>