<!-- https://js.devexpress.com/jQuery/Documentation/ApiReference/Data_Layer/PivotGridDataSource/ -->


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
  // print_r($viewResult);
  // echo json_encode($viewResult['result_set']);/

?>

<!-- <link rel="stylesheet" type="text/css" href="/assets/vendor/jquery-datatables/extras/TableTools/css/dataTables.tableTools.css"> -->
<!-- <script src="assets/js/dataTables.buttons.min.js"></script>
<script src="assets/js/buttons.bootstrap4.min.js"></script>
<script src="assets/js/buttons.html5.min.js"></script>
<script src="assets/js/buttons.print.min.js"></script>
<script type="text/javascript" language="javascript" src="/assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script> -->
<link rel="stylesheet" href="	https://cdn3.devexpress.com/jslib/23.2.4/css/dx.light.css">
<script type="text/javascript" src="https://cdn3.devexpress.com/jslib/23.2.4/js/dx.all.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.1.1/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>



<style>
    #pivotgrid {
    margin-top: 20px;
  }

  .currency {
    text-align: center;
  }
</style>
<script>
	var templateId = "0";
	var typeId="0";
  var templateName='' ;
  var templateDescription ='';
  var kreon =0;
  var tempName_for_downloading;
let currentDate = new Date();
let time = currentDate.getHours() + ":" + currentDate.getMinutes() + ":" + currentDate.getSeconds();
// console.log(time);  
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
      console.log("CHANGED TEMPLATE ID - " + typeId);
		  typeId = $("#typeSelect").val();
	  });
    $('.templateSelect').change(function () {
      if (selectedType === "1") {
        $('.pivotgrid-div').html('<div class="pivotgrid-display"><div class="dx-viewport demo-container"><div id="pivotgrid-demo"><div id="sales"></div><div id="sales-popup"></div><div id="pivotgrid-chart"></div><div id="pivotgrid"></div></div></div></div>');
        $('.pivotgrid-display').show(); // Show pivotgrid-display if Pivot Grid is selected
        $('.datagrid-display').hide(); // Hide datagrid-display
        $('.datagrid-div').html('');
        loadPivotGridData();
      } else if (selectedType === "2") {
        $('.datagrid-div').html('<div class="datagrid-display"><div class="dx-viewport"><div class="demo-container"><div id="gridContainer"></div></div></div></div>');
        $('.datagrid-display').show(); // Show datagrid-display if Data Grid is selected
        $('.pivotgrid-display').hide(); // Hide pivotgrid-display
        $('.pivotgrid-div').html('');
        loadDataGridData();
      }
      templateId = $("#templateSelect").val();
      // console.log("CHANGED TEMPLATE ID - " + templateId);
      tempName=$('#templateSelect').find('option:selected').text();
      // console.log(tempName);
      
    });
  });
</script>
<section class="panel report-filters-section">
    <header class="panel-heading">
		  <div class="panel-actions">
              <a href="#" class="panel-action panel-action-toggle filter-section" data-panel-toggle=""></a>
        </div>
      <h2 class="panel-title">Report Filters</h2>
      <!-- <p class="panel-subtitle">2 of 4 filters selected</p> -->
	  </header>
		<div class="panel-body">
      <form method="get">
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
          <!-- <br/> -->
          <!-- <div class="row"> -->
            <div class="col-md-4">
              <label>&nbsp;</label><br/> 
              <input class="btn btn-danger" type="submit" id="submit" onclickx="filterData(e);" value="FILTER">
              <a href="<?php echo $url; ?>" class="btn btn-primary">CLEAR</a><br />
              <!-- <img src="loading-gif.gif" id="loading" style="width: 60px;margin-top: 30px;"/> -->
            </div>
          <!-- </div> -->
        </div>
      </form>
    </div>
</section>
<section class="panel">
  <header class="panel-heading">
    <div class="panel-actions">
      <a href="#" class="panel-action panel-action-toggle" data-panel-toggle=""></a>
    </div>
    <h2 class="panel-title">Report</h2>
    <!-- <p class="panel-subtitle">2 of 4 filters selected</p> -->
  </header>
  <div class="panel-body">
    <div class="col-md-3 type" >
      <label for="type">Type:</label>
      <select name="type" id="typeSelect" class="typeSelect form-control">

        <option value></option>
        <?php
        $sql ='SELECT ID, Label FROM kreporttypes';
        $result = db::getInstance()->db_select($sql);
        for($i = 0; $i < $result['num_rows']; $i++){ //WHILE LOOP FOR $row
            echo '<option value="'.$result['result_set'][$i]['ID'].'">' . $result['result_set'][$i]['Label'] . '</option>';
          }
          ?>
      </select>
    </div>
    <div class="col-md-3 template" >
      <label for="template">Template:</label>
      <select name="template" class="templateSelect form-control" id="templateSelect">
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
    </div>
    <!-- <a href='#' class='btn btn-primary' style='margin-top: 25px' onclick='$("#saveAsModal").modal("show");'>Save Template As</a>
    <a href='#' class='btn btn-primary' style='margin-top: 25 px;' onclick='save()' >Save </a> -->
    <div style='text-align:right;'>
      <a href='#' class='btn btn-primary' style='margin-top: 25px;' onclick='$("#saveAsModal").modal("show");'>Save Template As</a>
      <a href='#' class='btn btn-primary' style='margin-top: 25px; margin-left: 10px;' onclick='save()'>Save</a>
    </div>

    <div class="modal fade" id="saveAsModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button> 
            <h4 class="modal-title">Add</h4>                                                             
          </div> 
          <div class="modal-body" >
            <!-- <form id="modalForm" name="modal" role="form">  -->
              <div id="ModelContent" >
                <input class=" form-control" type="text" id ="templatetitle" name="templatetitle" placeholder="Enter the template name:"><br/><br/>
                <input class=" form-control" type="text" id ="templatedescription" name="templatedescription" placeholder="Enter the template description:">
              </div>
              <div class="modal-footer" style="margin-top:10px;">					
                <button type="button" class="btn btn-primary" data-dismiss="modal" onClick="modalvalue()">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
              </div>
              <script>
                function modalvalue(){
                    console.log("in modal Valur");
                    templateName = $("#templatetitle").val();
                    templateDescription = $("#templatedescription").val();
                    $("#templatetitle").val("");
                    $("#templatedescription").val("");
                    console.log(templateName);
                    console.log(templateDescription);
                  sendStorageRequest("organisatieKey", "text", "PUT", globalstate);
                    templateName="";
                    templateDescription = "";
                  // $("#templatetitle").val('');
                  // $("#templatedescription").val('');

                }
              </script>
            <!-- </form> -->
          </div>   
        </div>                                                                       
      </div>                                          
    </div>
          <!-- </div> -->
</section>
<section class="pivotgrid-div">
  
</section>
<section class="datagrid-div">
  
</section>


<!-- SCRIPT FOR DATA GRID -->
<script>
  window.jsPDF = window.jspdf.jsPDF;
function loadDataGridData(){

  // window.jsPDF = window.jspdf.jsPDF; 
  $(() => {
    $('#gridContainer').dxDataGrid({
      // paging: {
      //   pageSize: 10,
      // },
    //   pager: {
    //   showPageSizeSelector: true,
    //   allowedPageSizes: [10, 25, 50, 100],
    // },
          selection: {
        mode: 'multiple',
      },
          groupPanel: {
        visible: true,
      },
      sorting: {
        mode: 'multiple',
      },
       filterRow: {
        visible: true,
        applyFilter: 'auto',
      },
      // searchPanel: {
      //   visible: true,
      //   width: 240,
      //   placeholder: 'Search...',
      // },
      headerFilter: {
        visible: true,
      },
       filterPanel: { visible: true },
      //  focusedRowEnabled: true,
          summary: {
        totalItems: [{
          name: 'SelectedRowsSummary',
          showInColumn: 'SaleAmount',
          displayFormat: 'Sum: {0}',
          valueFormat: 'Meter',
          summaryType: 'custom',
        },
        ],
        calculateCustomSummary(options) {
          if (options.name === 'SelectedRowsSummary') {
            if (options.summaryProcess === 'start') {
              options.totalValue = 0;
            }
            if (options.summaryProcess === 'calculate') {
              if (options.component.isRowSelected(options.value.ID)) {
                options.totalValue += options.value.SaleAmount;
              }
            }
          }
        },
      },

               export: {
      enabled: true,
      formats: ['pdf'],
      allowExportSelectedData: true,
    },
    onExporting(e) {
      const doc = new jsPDF();

      DevExpress.pdfExporter.exportDataGrid({
        jsPDFDocument: doc,
        component: e.component,
        indent: 5,
      }).then(() => {
        doc.save(''+tempName+'-'+getTodaysDate()+'-'+time+'.pdf');
      });
    },

    //     export: {
    //   enabled: true,
    //   allowExportSelectedData: true,
    // },
    // onExporting(e) {
    //   const workbook = new ExcelJS.Workbook();
    //   const worksheet = workbook.addWorksheet('Employees');

    //   DevExpress.excelExporter.exportDataGrid({
    //     component: e.component,
    //     worksheet,
    //     autoFilterEnabled: true,
    //   }).then(() => {
    //     workbook.xlsx.writeBuffer().then((buffer) => {
    //       saveAs(new Blob([buffer], { type: 'application/octet-stream' }), ''+tempName+'-'+getTodaysDate()+'-'+time+'.xlsx');
    //     });
    //   });
    // },
      
      stateStoring: {
              enabled: true,
              type: "custom",
              customLoad: function () {
                console.log("load called from datagrid");
                  return sendStorageRequest("storageKey", "json", "GET");
              },
              customSave: function (state) {
                console.log("save called from datagrid");
                console.log(kreon);
                passingstate(state);
                if(kreon==1){
                  console.log("in if condition of cutomSave");
                  sendStorageRequest("storageKey", "text", "PUT", state);
                }
              }
          },
  
           columnChooser: {
              enabled: true,
              mode: "dragAndDrop" // or "select"
          },
      dataSource: heet,
      
      showBorders: true,
    });
  });
}

</script>

<!-- SCRIPT FOR PIVOT GRID -->
<script>
  var heet1= 
  <?php
  echo json_encode($viewResult['result_set']); 
  ?>;

  var i=0;
   heet = heet1.map(item => {
     // Ensure each field is converted to an integer
     console.log(item);

    return {
        ...item,
        Total: parseInt(item.Total, 10),
        Meter: parseInt(item.Meter, 10),
        Lumps: parseInt(item.Lumps, 10),
      };
    });

    // heet.forEach(item => {
    //   if(i==0){
    //     Object.keys(item).forEach(key => {
    //         console.log(key + ":", item[key]);
    //     });
    //     i++;
    //   }


    // });

let firstItemData = [];

heet.forEach((item, index) => {
    if (index === 0) {
        Object.keys(item).forEach(key => {
            firstItemData.push(key);
        });
    }
});

console.log(firstItemData);

      console.log(firstItemData[0]);

     
     




  var globalstate;
  function save() {
    console.log('save');
    console.log(kreon); // Log the updated value of kreon
    return sendStorageRequest("organisatieKey", "text", "PUT", globalstate);
  }
  function passingstate(state){
    globalstate=state;
    console.log(globalstate);
    console.log("globalstate");
  }
  function sendStorageRequest(key, datatype, type, data) {
    // console.log("in sr");
    var deferred = $.Deferred();
    if (data !== undefined) var d = JSON.stringify(data);
    else var d = "";
    var storageRequestSettings = {
      url: "reportOptionsDataStorage.php?ReportID=<?php echo $ReportID; ?>&templateId=" +
      templateId +
      "&templateName="+
      templateName+
      "&templateDescription="+
      templateDescription+
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
        console.log("Success_sendStorage");
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
    console.log("TN"+templateName);
                  console.log("TD"+templateDescription);
    $.ajax(storageRequestSettings);
    return deferred.promise();
  }
  function loadPivotGridData() {
    $(function() {

       let drillDownDataSource = {};
       const pivotGridChart = $('#pivotgrid-chart').dxChart({
        commonSeriesSettings: {
          type: 'bar',
        },
        tooltip: {
          enabled: true,
          
        },
        size: {
          height: 320,
        },
        adaptiveLayout: {
          width: 450,
        },
      }).dxChart('instance');

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
            // console.log(kreon, "in customsave");
            // console.log("grid,");console.log( gridState);
            passingstate(gridState);
            if (kreon === 1) {
              console.log("executig custom save from the state storing function");
              return sendStorageRequest("organisatieKey", "text", "PUT", gridState);
            } 
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
          // console.log("on Cell CLick");
          // console.log(e);
          if (e.area === "data") {
            const pivotGridDataSource = e.component.getDataSource();
            console.log(pivotGridDataSource);
            const rowPathLength = e.cell.rowPath.length;
            const rowPathName = e.cell.rowPath[rowPathLength - 1];
            const popupTitle = `${rowPathName || "Total"} Drill Down Data`;
            // console.log("e.cell"+e.cell);
            // console.log(e.cell);
            // console.log("not");
            drillDownDataSource = pivotGridDataSource.createDrillDownDataSource(e.cell); 
            // console.log(drillDownDataSource);
            salesPopup.option("title", popupTitle);
            salesPopup.show();
          }
        },
        // dataSource: {
        //   store: heet
        // },
        dataSource: {
          fields: [
            {
              caption: 'Date',
              width: 120,
              dataField: 'Entry Date',
              dataType:'date',
              area: 'columns',
            },
            {

              dataField: 'Party Name',
              width: 150,
              area: 'row',
            }, 
            {
              caption: 'Total',
              dataField: 'Total',
              dataType: 'number',
              summaryType: 'sum',
              format: 'currency',
              area: 'data',
            }
          ],
          // store: jeet,
          store: sales,
        },
        onExporting(e) {
          const workbook = new ExcelJS.Workbook();
          const worksheet = workbook.addWorksheet('Sales');

          DevExpress.excelExporter.exportPivotGrid({
            component: e.component,
            worksheet,
          }).then(() => {
            workbook.xlsx.writeBuffer().then((buffer) => {
              saveAs(new Blob([buffer], { type: 'application/octet-stream' }), ''+tempName+'-'+getTodaysDate()+'-'+time+'.xlsx');
            });
          });
        },
       }).dxPivotGrid("instance");

        salesPivotGrid.bindChart(pivotGridChart, {
          dataFieldsDisplayMode: 'splitPanes',
          alternateDataFields: false,
      });


      const salesPopup = $('#sales-popup').dxPopup({
        width: 600,
        height: 400,
        showCloseButton: true,
        contentTemplate(contentElement) {
          $('<div />')
            .addClass('drill-down')
            .dxDataGrid({
              width: 560,
              height: 300,
              columns: [firstItemData[0],'BrokerName','Remarks'],
            })
            .appendTo(contentElement);
        },
        onShowing() {
          $('.drill-down')
            .dxDataGrid('instance')
            .option('dataSource', drillDownDataSource);
        },
        onShown() {
          $('.drill-down')
            .dxDataGrid('instance')
            .updateDimensions();
        },
      }).dxPopup('instance');
    });
const jeet=[
  {
    "ID": 1,
    "Reg": "GREY INWARD",
    "EntryNo": "test123",
    "EntryDate": '2023/12/21',
    "ChlNo": "E-0000001",
    "ChlDate": '2023/12/21',
    "TrspPay": "Testing",
    "Sender": 1,
    "Broker": 4,
    "Remark": "test",
    "Total": 572,
    "Meter": 4,
    "Lumps": 60,
    "CreatedAt": "2024-02-27 18:23:13",
    "CreatedBy": 0,
    "PartyName": "Kreon Solution",
    "BrokerName": "Hiten Mehta"
  },
  {
    "ID": 2,
    "Reg": "Reg222",
    "EntryNo": "",
    "EntryDate": '2023/08/25',
    "ChlNo": "E-0000002",
    "ChlDate": '2024/02/12',
    "TrspPay": "",
    "Sender": 1,
    "Broker": 1,
    "Remark": "",
    "Total": 0,
    "Meter": 0,
    "Lumps": "",
    "CreatedAt": "2024-02-22 23:26:48",
    "CreatedBy": 0,
    "PartyName": "Kreon Solution",
    "BrokerName": "John Dias"
  },
  {
    "ID": 3,
    "Reg": "s",
    "EntryNo": "",
    "ntryDate":'2023/01/09',
    "ChlNo": "E-0000003",
  "ChlDate":'2024/02/14',
    "TrspPay": "Testing",
    "Sender": 7,
    "Broker": 18,
    "Remark": "testing",
    "Total": 0,
    "Meter": 4000,
    "Lumps": 40,
    "CreatedAt": "2024-02-22 23:25:11",
    "CreatedBy": 0,
    "PartyName": "Test4",
    "BrokerName": "Bhavin Patel"
  },
  {
    "ID": 4,
    "Reg": "test123",
    "EntryNo": "test123",
    "ntryDate":'2024/02/19',
    "ChlNo": "E-0000004",
  "ChlDate":'2024/02/19',
    "TrspPay": "",
    "Sender": 2,
    "Broker": 2,
    "Remark": "test",
    "Total": 0,
    "Meter": 0,
    "Lumps": "",
    "CreatedAt": "2024-02-22 23:25:52",
    "CreatedBy": 0,
    "PartyName": "Bharat Textile",
    "BrokerName": "Jitesh Jain"
  },
  {
    "ID": 5,
    "Reg": "22",
    "EntryNo": "0",
    "EntryDate": '2024/10/04',
    "ChlNo": "E-0000005",
    "ChlDate": '2024/02/20',
    "TrspPay": "s",
    "Sender": 2,
    "Broker": 2,
    "Remark": "ss",
    "Total": 0,
    "Meter": 4300,
    "Lumps": 40,
    "CreatedAt": "2024-02-22 23:25:55",
    "CreatedBy": 0,
    "PartyName": "Bharat Textile",
    "BrokerName": "Jitesh Jain"
  },
  {
    "ID": 6,
    "Reg": "test11",
    "EntryNo": "",
    "ntryDate":'2020/03/25',
    "ChlNo": "E-0000006",
  "ChlDate":'2024/02/21',
    "TrspPay": "dfd",
    "Sender": 7,
    "Broker": 0,
    "Remark": "sgsd",
    "Total": 0,
    "Meter": 400,
    "Lumps": 48,
    "CreatedAt": "2024-02-26 19:44:49",
    "CreatedBy": 0,
    "PartyName": "Test4",
    "BrokerName": null
  },
  {
    "ID": 7,
    "Reg": "",
    "EntryNo": "",
    "ntryDate":'2024/12/25',
    "ChlNo": "E-0000007",
    "ChlDate":'2024/02/27',
    "TrspPay": "",
    "Sender": 0,
    "Broker": 0,
    "Remark": "",
    "Total": 0,
    "Meter": 0,
    "Lumps": "",
    "CreatedAt": "2024-02-27 11:30:13",
    "CreatedBy": 0,
    "PartyName": null,
    "BrokerName": null
  },
  {
    "ID": 8,
    "Reg": "",
    "EntryNo": "",
    "EntryDate": '2020/02/02',
    "ChlNo": "E-0000008",
    "ChlDate": '2024/02/27',
    "TrspPay": "",
    "Sender": 0,
    "Broker": 0,
    "Remark": "",
    "Total": 0,
    "Meter": 0,
    "Lumps": "",
    "CreatedAt": "2024-02-27 11:56:12",
    "CreatedBy": 0,
    "PartyName": null,
    "BrokerName": null
  }
]

      
    const sales = [
        {
        id: 1,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 1740,
        date: '2013/01/06',
      }, {
        id: 2,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 850,
        date: '2013/01/13',
      }, {
        id: 3,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 2235,
        date: '2013/01/07',
      }, {
        id: 4,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1965,
        date: '2013/01/03',
      }, {
        id: 5,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 880,
        date: '2013/01/10',
      }, {
        id: 6,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 5260,
        date: '2013/01/17',
      }, {
        id: 7,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 2790,
        date: '2013/01/21',
      }, {
        id: 8,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 3140,
        date: '2013/01/01',
      }, {
        id: 9,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 6175,
        date: '2013/01/24',
      }, {
        id: 10,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 4575,
        date: '2013/01/11',
      }, {
        id: 11,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 3680,
        date: '2013/01/12',
      }, {
        id: 12,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 2260,
        date: '2013/01/01',
      }, {
        id: 13,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 2910,
        date: '2013/01/26',
      }, {
        id: 14,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 8400,
        date: '2013/01/05',
      }, {
        id: 15,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 1325,
        date: '2013/01/14',
      }, {
        id: 16,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 3920,
        date: '2013/01/05',
      }, {
        id: 17,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 2220,
        date: '2013/01/15',
      }, {
        id: 18,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 940,
        date: '2013/01/01',
      }, {
        id: 19,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1630,
        date: '2013/01/10',
      }, {
        id: 20,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2910,
        date: '2013/01/23',
      }, {
        id: 21,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 2600,
        date: '2013/01/14',
      }, {
        id: 22,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 4340,
        date: '2013/01/26',
      }, {
        id: 23,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 6650,
        date: '2013/01/24',
      }, {
        id: 24,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 490,
        date: '2013/01/22',
      }, {
        id: 25,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 3390,
        date: '2013/01/25',
      }, {
        id: 26,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 5160,
        date: '2013/02/20',
      }, {
        id: 27,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 5750,
        date: '2013/02/12',
      }, {
        id: 28,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 2805,
        date: '2013/02/13',
      }, {
        id: 29,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 2505,
        date: '2013/02/09',
      }, {
        id: 30,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 930,
        date: '2013/02/04',
      }, {
        id: 31,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 1240,
        date: '2013/02/03',
      }, {
        id: 32,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 315,
        date: '2013/02/04',
      }, {
        id: 33,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2870,
        date: '2013/02/18',
      }, {
        id: 34,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 5150,
        date: '2013/02/18',
      }, {
        id: 35,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 2725,
        date: '2013/02/20',
      }, {
        id: 36,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 2840,
        date: '2013/02/04',
      }, {
        id: 37,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 5840,
        date: '2013/02/13',
      }, {
        id: 38,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 6750,
        date: '2013/02/11',
      }, {
        id: 39,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 1200,
        date: '2013/02/03',
      }, {
        id: 40,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 4550,
        date: '2013/02/08',
      }, {
        id: 41,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 6040,
        date: '2013/02/17',
      }, {
        id: 42,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 2205,
        date: '2013/02/08',
      }, {
        id: 43,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 990,
        date: '2013/02/20',
      }, {
        id: 44,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 700,
        date: '2013/02/11',
      }, {
        id: 45,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 2325,
        date: '2013/02/15',
      }, {
        id: 46,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 930,
        date: '2013/02/21',
      }, {
        id: 47,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 1560,
        date: '2013/02/04',
      }, {
        id: 48,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 1740,
        date: '2013/03/04',
      }, {
        id: 49,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 3575,
        date: '2013/03/20',
      }, {
        id: 50,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 4500,
        date: '2013/03/04',
      }, {
        id: 51,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1605,
        date: '2013/03/17',
      }, {
        id: 52,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 800,
        date: '2013/03/21',
      }, {
        id: 53,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 640,
        date: '2013/03/08',
      }, {
        id: 54,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 735,
        date: '2013/03/19',
      }, {
        id: 55,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2520,
        date: '2013/03/20',
      }, {
        id: 56,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 6675,
        date: '2013/03/18',
      }, {
        id: 57,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 3625,
        date: '2013/03/25',
      }, {
        id: 58,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 1200,
        date: '2013/03/07',
      }, {
        id: 59,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 2000,
        date: '2013/03/07',
      }, {
        id: 60,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 1410,
        date: '2013/03/10',
      }, {
        id: 61,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 2700,
        date: '2013/03/19',
      }, {
        id: 62,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 5950,
        date: '2013/03/24',
      }, {
        id: 63,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 5120,
        date: '2013/03/08',
      }, {
        id: 64,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 1980,
        date: '2013/03/17',
      }, {
        id: 65,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1110,
        date: '2013/03/08',
      }, {
        id: 66,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 980,
        date: '2013/03/21',
      }, {
        id: 67,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 5460,
        date: '2013/03/19',
      }, {
        id: 68,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 3800,
        date: '2013/03/12',
      }, {
        id: 69,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 2610,
        date: '2013/03/04',
      }, {
        id: 70,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 3080,
        date: '2013/03/22',
      }, {
        id: 71,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 2010,
        date: '2013/03/23',
      }, {
        id: 72,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 1200,
        date: '2013/03/04',
      }, {
        id: 73,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 7680,
        date: '2013/04/15',
      }, {
        id: 74,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 1325,
        date: '2013/04/07',
      }, {
        id: 75,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 2835,
        date: '2013/04/10',
      }, {
        id: 76,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 3660,
        date: '2013/04/10',
      }, {
        id: 77,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 390,
        date: '2013/04/12',
      }, {
        id: 78,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 4420,
        date: '2013/04/08',
      }, {
        id: 79,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 1755,
        date: '2013/04/13',
      }, {
        id: 80,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2580,
        date: '2013/04/15',
      }, {
        id: 81,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 850,
        date: '2013/04/01',
      }, {
        id: 82,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 2825,
        date: '2013/04/10',
      }, {
        id: 83,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 540,
        date: '2013/04/06',
      }, {
        id: 84,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 1520,
        date: '2013/04/08',
      }, {
        id: 85,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 8760,
        date: '2013/04/26',
      }, {
        id: 86,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 1110,
        date: '2013/04/16',
      }, {
        id: 87,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 6850,
        date: '2013/04/19',
      }, {
        id: 88,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 1940,
        date: '2013/04/23',
      }, {
        id: 89,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 1980,
        date: '2013/04/21',
      }, {
        id: 90,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 3090,
        date: '2013/04/03',
      }, {
        id: 91,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1640,
        date: '2013/04/24',
      }, {
        id: 92,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 3585,
        date: '2013/04/01',
      }, {
        id: 93,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1770,
        date: '2013/04/01',
      }, {
        id: 94,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 4005,
        date: '2013/04/04',
      }, {
        id: 95,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2870,
        date: '2013/04/02',
      }, {
        id: 96,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 960,
        date: '2013/04/20',
      }, {
        id: 97,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 8640,
        date: '2013/05/14',
      }, {
        id: 98,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 5450,
        date: '2013/05/24',
      }, {
        id: 99,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 2535,
        date: '2013/05/07',
      }, {
        id: 100,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1155,
        date: '2013/05/20',
      }, {
        id: 101,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 3140,
        date: '2013/05/18',
      }, {
        id: 102,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 2260,
        date: '2013/05/19',
      }, {
        id: 103,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 1215,
        date: '2013/05/23',
      }, {
        id: 104,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1210,
        date: '2013/05/08',
      }, {
        id: 105,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 875,
        date: '2013/05/25',
      }, {
        id: 106,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 5400,
        date: '2013/05/03',
      }, {
        id: 107,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 5940,
        date: '2013/05/25',
      }, {
        id: 108,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 4700,
        date: '2013/05/03',
      }, {
        id: 109,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 5520,
        date: '2013/05/12',
      }, {
        id: 110,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 9210,
        date: '2013/05/22',
      }, {
        id: 111,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 7950,
        date: '2013/05/12',
      }, {
        id: 112,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 3740,
        date: '2013/05/24',
      }, {
        id: 113,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 990,
        date: '2013/05/02',
      }, {
        id: 114,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 3190,
        date: '2013/05/03',
      }, {
        id: 115,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 2430,
        date: '2013/05/11',
      }, {
        id: 116,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 7380,
        date: '2013/06/15',
      }, {
        id: 117,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 4475,
        date: '2013/06/08',
      }, {
        id: 118,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 1290,
        date: '2013/06/10',
      }, {
        id: 119,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 2250,
        date: '2013/06/10',
      }, {
        id: 120,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 350,
        date: '2013/06/22',
      }, {
        id: 121,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 5480,
        date: '2013/06/24',
      }, {
        id: 122,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 2355,
        date: '2013/06/10',
      }, {
        id: 123,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1960,
        date: '2013/06/23',
      }, {
        id: 124,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 4125,
        date: '2013/06/06',
      }, {
        id: 125,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 7925,
        date: '2013/06/12',
      }, {
        id: 126,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 1120,
        date: '2013/06/22',
      }, {
        id: 127,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 5100,
        date: '2013/06/01',
      }, {
        id: 128,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 1500,
        date: '2013/06/25',
      }, {
        id: 129,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 5130,
        date: '2013/06/10',
      }, {
        id: 130,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 2475,
        date: '2013/06/10',
      }, {
        id: 131,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 2100,
        date: '2013/06/06',
      }, {
        id: 132,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 3570,
        date: '2013/06/10',
      }, {
        id: 133,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 550,
        date: '2013/06/02',
      }, {
        id: 134,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 2850,
        date: '2013/06/26',
      }, {
        id: 135,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 4280,
        date: '2013/06/19',
      }, {
        id: 136,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 1460,
        date: '2013/06/17',
      }, {
        id: 137,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 960,
        date: '2013/06/17',
      }, {
        id: 138,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1520,
        date: '2013/06/03',
      }, {
        id: 139,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 6750,
        date: '2013/06/21',
      }, {
        id: 140,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 7260,
        date: '2013/07/14',
      }, {
        id: 141,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 2450,
        date: '2013/07/11',
      }, {
        id: 142,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 3540,
        date: '2013/07/02',
      }, {
        id: 143,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1950,
        date: '2013/07/03',
      }, {
        id: 144,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 360,
        date: '2013/07/07',
      }, {
        id: 145,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 4500,
        date: '2013/07/03',
      }, {
        id: 146,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 4575,
        date: '2013/07/21',
      }, {
        id: 147,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2310,
        date: '2013/07/18',
      }, {
        id: 148,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 7500,
        date: '2013/07/04',
      }, {
        id: 149,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 3575,
        date: '2013/07/23',
      }, {
        id: 150,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 760,
        date: '2013/07/01',
      }, {
        id: 151,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 2400,
        date: '2013/07/11',
      }, {
        id: 152,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 3330,
        date: '2013/07/04',
      }, {
        id: 153,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 3480,
        date: '2013/07/23',
      }, {
        id: 154,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 4875,
        date: '2013/07/11',
      }, {
        id: 155,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 4980,
        date: '2013/07/19',
      }, {
        id: 156,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 2580,
        date: '2013/07/04',
      }, {
        id: 157,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 2650,
        date: '2013/07/16',
      }, {
        id: 158,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1190,
        date: '2013/07/02',
      }, {
        id: 159,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 960,
        date: '2013/07/26',
      }, {
        id: 160,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 3600,
        date: '2013/08/08',
      }, {
        id: 161,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 2250,
        date: '2013/08/01',
      }, {
        id: 162,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 1275,
        date: '2013/08/02',
      }, {
        id: 163,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 3885,
        date: '2013/08/14',
      }, {
        id: 164,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 1900,
        date: '2013/08/05',
      }, {
        id: 165,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 2300,
        date: '2013/08/09',
      }, {
        id: 166,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 2895,
        date: '2013/08/15',
      }, {
        id: 167,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 350,
        date: '2013/08/20',
      }, {
        id: 168,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 4200,
        date: '2013/08/22',
      }, {
        id: 169,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 7175,
        date: '2013/08/14',
      }, {
        id: 170,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 4420,
        date: '2013/08/24',
      }, {
        id: 171,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 5200,
        date: '2013/08/21',
      }, {
        id: 172,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 7920,
        date: '2013/08/17',
      }, {
        id: 173,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 6990,
        date: '2013/08/22',
      }, {
        id: 174,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 2125,
        date: '2013/08/05',
      }, {
        id: 175,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 2220,
        date: '2013/08/16',
      }, {
        id: 176,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 1575,
        date: '2013/08/23',
      }, {
        id: 177,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1880,
        date: '2013/08/12',
      }, {
        id: 178,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 710,
        date: '2013/08/25',
      }, {
        id: 179,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 390,
        date: '2013/08/20',
      }, {
        id: 180,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 4635,
        date: '2013/08/04',
      }, {
        id: 181,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 4350,
        date: '2013/08/19',
      }, {
        id: 182,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 6020,
        date: '2013/08/02',
      }, {
        id: 183,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 3660,
        date: '2013/08/19',
      }, {
        id: 184,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 4525,
        date: '2013/08/24',
      }, {
        id: 185,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 4410,
        date: '2013/09/12',
      }, {
        id: 186,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 1725,
        date: '2013/09/07',
      }, {
        id: 187,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 2715,
        date: '2013/09/14',
      }, {
        id: 188,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 2820,
        date: '2013/09/08',
      }, {
        id: 189,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2310,
        date: '2013/09/12',
      }, {
        id: 190,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 780,
        date: '2013/09/08',
      }, {
        id: 191,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 2370,
        date: '2013/09/19',
      }, {
        id: 192,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1410,
        date: '2013/09/09',
      }, {
        id: 193,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 1825,
        date: '2013/09/23',
      }, {
        id: 194,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 4075,
        date: '2013/09/06',
      }, {
        id: 195,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 1020,
        date: '2013/09/04',
      }, {
        id: 196,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 4320,
        date: '2013/09/25',
      }, {
        id: 197,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 7530,
        date: '2013/09/13',
      }, {
        id: 198,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 2820,
        date: '2013/09/08',
      }, {
        id: 199,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 3050,
        date: '2013/09/04',
      }, {
        id: 200,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 5080,
        date: '2013/09/25',
      }, {
        id: 201,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 1125,
        date: '2013/09/13',
      }, {
        id: 202,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 850,
        date: '2013/09/24',
      }, {
        id: 203,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1440,
        date: '2013/09/19',
      }, {
        id: 204,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1950,
        date: '2013/09/02',
      }, {
        id: 205,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 6390,
        date: '2013/10/11',
      }, {
        id: 206,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 4625,
        date: '2013/10/02',
      }, {
        id: 207,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 3510,
        date: '2013/10/24',
      }, {
        id: 208,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 2730,
        date: '2013/10/15',
      }, {
        id: 209,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2070,
        date: '2013/10/15',
      }, {
        id: 210,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 2320,
        date: '2013/10/18',
      }, {
        id: 211,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 4260,
        date: '2013/10/24',
      }, {
        id: 212,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 840,
        date: '2013/10/18',
      }, {
        id: 213,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 7300,
        date: '2013/10/24',
      }, {
        id: 214,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 5950,
        date: '2013/10/11',
      }, {
        id: 215,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 3220,
        date: '2013/10/25',
      }, {
        id: 216,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 3480,
        date: '2013/10/08',
      }, {
        id: 217,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 4830,
        date: '2013/10/26',
      }, {
        id: 218,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 4470,
        date: '2013/10/05',
      }, {
        id: 219,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 3675,
        date: '2013/10/23',
      }, {
        id: 220,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 4260,
        date: '2013/10/01',
      }, {
        id: 221,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 4245,
        date: '2013/10/26',
      }, {
        id: 222,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1470,
        date: '2013/10/01',
      }, {
        id: 223,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1810,
        date: '2013/10/02',
      }, {
        id: 224,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 600,
        date: '2013/10/23',
      }, {
        id: 225,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 7500,
        date: '2013/11/03',
      }, {
        id: 226,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 4625,
        date: '2013/11/02',
      }, {
        id: 227,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 2625,
        date: '2013/11/09',
      }, {
        id: 228,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1440,
        date: '2013/11/15',
      }, {
        id: 229,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2420,
        date: '2013/11/15',
      }, {
        id: 230,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 4180,
        date: '2013/11/15',
      }, {
        id: 231,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 3720,
        date: '2013/11/25',
      }, {
        id: 232,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2730,
        date: '2013/11/08',
      }, {
        id: 233,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 3775,
        date: '2013/11/17',
      }, {
        id: 234,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 3525,
        date: '2013/11/15',
      }, {
        id: 235,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 5320,
        date: '2013/11/08',
      }, {
        id: 236,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 5340,
        date: '2013/11/13',
      }, {
        id: 237,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 8850,
        date: '2013/11/01',
      }, {
        id: 238,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 7050,
        date: '2013/11/14',
      }, {
        id: 239,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 4200,
        date: '2013/11/18',
      }, {
        id: 240,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 4960,
        date: '2013/11/04',
      }, {
        id: 241,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 2280,
        date: '2013/11/13',
      }, {
        id: 242,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 590,
        date: '2013/11/11',
      }, {
        id: 243,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 810,
        date: '2013/11/12',
      }, {
        id: 244,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 2625,
        date: '2013/11/07',
      }, {
        id: 245,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 8280,
        date: '2013/12/01',
      }, {
        id: 246,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 5650,
        date: '2013/12/19',
      }, {
        id: 247,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 2760,
        date: '2013/12/14',
      }, {
        id: 248,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 2670,
        date: '2013/12/03',
      }, {
        id: 249,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2520,
        date: '2013/12/20',
      }, {
        id: 250,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 4080,
        date: '2013/12/21',
      }, {
        id: 251,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 4140,
        date: '2013/12/22',
      }, {
        id: 252,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 390,
        date: '2013/12/04',
      }, {
        id: 253,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 1400,
        date: '2013/12/19',
      }, {
        id: 254,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 7275,
        date: '2013/12/22',
      }, {
        id: 255,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 4100,
        date: '2013/12/20',
      }, {
        id: 256,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 5520,
        date: '2013/12/25',
      }, {
        id: 257,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 9210,
        date: '2013/12/24',
      }, {
        id: 258,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 7290,
        date: '2013/12/05',
      }, {
        id: 259,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 625,
        date: '2013/12/22',
      }, {
        id: 260,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 4460,
        date: '2013/12/12',
      }, {
        id: 261,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 3825,
        date: '2013/12/13',
      }, {
        id: 262,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 2850,
        date: '2013/12/17',
      }, {
        id: 263,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 2780,
        date: '2013/12/07',
      }, {
        id: 264,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 840,
        date: '2013/12/18',
      }, {
        id: 265,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 2970,
        date: '2013/12/23',
      }, {
        id: 266,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 945,
        date: '2013/12/06',
      }, {
        id: 267,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 2625,
        date: '2013/12/04',
      }, {
        id: 268,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 390,
        date: '2013/12/01',
      }, {
        id: 269,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 2250,
        date: '2013/12/02',
      }, {
        id: 270,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 7710,
        date: '2014/01/18',
      }, {
        id: 271,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 7975,
        date: '2014/01/10',
      }, {
        id: 272,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 3285,
        date: '2014/01/13',
      }, {
        id: 273,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 2580,
        date: '2014/01/22',
      }, {
        id: 274,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2160,
        date: '2014/01/26',
      }, {
        id: 275,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 1100,
        date: '2014/01/25',
      }, {
        id: 276,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 4425,
        date: '2014/01/21',
      }, {
        id: 277,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1360,
        date: '2014/01/22',
      }, {
        id: 278,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 3250,
        date: '2014/01/14',
      }, {
        id: 279,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 5550,
        date: '2014/01/21',
      }, {
        id: 280,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 2860,
        date: '2014/01/25',
      }, {
        id: 281,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 5320,
        date: '2014/01/08',
      }, {
        id: 282,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 4050,
        date: '2014/01/14',
      }, {
        id: 283,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 3450,
        date: '2014/01/24',
      }, {
        id: 284,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 5425,
        date: '2014/01/11',
      }, {
        id: 285,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 4860,
        date: '2014/01/12',
      }, {
        id: 286,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 4695,
        date: '2014/01/16',
      }, {
        id: 287,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 610,
        date: '2014/01/05',
      }, {
        id: 288,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1580,
        date: '2014/01/15',
      }, {
        id: 289,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 3780,
        date: '2014/02/18',
      }, {
        id: 290,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 5400,
        date: '2014/02/21',
      }, {
        id: 291,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 630,
        date: '2014/02/18',
      }, {
        id: 292,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 3960,
        date: '2014/02/04',
      }, {
        id: 293,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2010,
        date: '2014/02/25',
      }, {
        id: 294,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 5000,
        date: '2014/02/01',
      }, {
        id: 295,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 1995,
        date: '2014/02/20',
      }, {
        id: 296,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 860,
        date: '2014/02/12',
      }, {
        id: 297,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 2150,
        date: '2014/02/10',
      }, {
        id: 298,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 4050,
        date: '2014/02/06',
      }, {
        id: 299,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 2960,
        date: '2014/02/18',
      }, {
        id: 300,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 1780,
        date: '2014/02/26',
      }, {
        id: 301,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 8700,
        date: '2014/02/03',
      }, {
        id: 302,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 3390,
        date: '2014/02/03',
      }, {
        id: 303,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 4425,
        date: '2014/02/15',
      }, {
        id: 304,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 1180,
        date: '2014/02/23',
      }, {
        id: 305,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 360,
        date: '2014/02/08',
      }, {
        id: 306,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 2310,
        date: '2014/02/13',
      }, {
        id: 307,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1770,
        date: '2014/02/20',
      }, {
        id: 308,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 3060,
        date: '2014/02/26',
      }, {
        id: 309,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 1750,
        date: '2014/02/12',
      }, {
        id: 310,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 2280,
        date: '2014/03/09',
      }, {
        id: 311,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 7600,
        date: '2014/03/25',
      }, {
        id: 312,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 1035,
        date: '2014/03/23',
      }, {
        id: 313,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1245,
        date: '2014/03/01',
      }, {
        id: 314,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2860,
        date: '2014/03/19',
      }, {
        id: 315,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 440,
        date: '2014/03/19',
      }, {
        id: 316,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 4665,
        date: '2014/03/02',
      }, {
        id: 317,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2270,
        date: '2014/03/15',
      }, {
        id: 318,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 5000,
        date: '2014/03/09',
      }, {
        id: 319,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 5100,
        date: '2014/03/23',
      }, {
        id: 320,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 2120,
        date: '2014/03/11',
      }, {
        id: 321,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 5140,
        date: '2014/03/05',
      }, {
        id: 322,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 6210,
        date: '2014/03/19',
      }, {
        id: 323,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 9510,
        date: '2014/03/19',
      }, {
        id: 324,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 7600,
        date: '2014/03/21',
      }, {
        id: 325,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 5420,
        date: '2014/03/15',
      }, {
        id: 326,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 1980,
        date: '2014/03/05',
      }, {
        id: 327,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1820,
        date: '2014/03/07',
      }, {
        id: 328,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1670,
        date: '2014/03/21',
      }, {
        id: 329,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 4800,
        date: '2014/03/08',
      }, {
        id: 330,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 2925,
        date: '2014/03/03',
      }, {
        id: 331,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 2940,
        date: '2014/04/11',
      }, {
        id: 332,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 3525,
        date: '2014/04/13',
      }, {
        id: 333,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 2475,
        date: '2014/04/22',
      }, {
        id: 334,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 3315,
        date: '2014/04/08',
      }, {
        id: 335,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 3140,
        date: '2014/04/07',
      }, {
        id: 336,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 2520,
        date: '2014/04/01',
      }, {
        id: 337,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 1200,
        date: '2014/04/10',
      }, {
        id: 338,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2060,
        date: '2014/04/21',
      }, {
        id: 339,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 7875,
        date: '2014/04/02',
      }, {
        id: 340,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 1450,
        date: '2014/04/07',
      }, {
        id: 341,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 2640,
        date: '2014/04/22',
      }, {
        id: 342,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 1960,
        date: '2014/04/16',
      }, {
        id: 343,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 2250,
        date: '2014/04/23',
      }, {
        id: 344,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 4500,
        date: '2014/04/05',
      }, {
        id: 345,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 5050,
        date: '2014/04/11',
      }, {
        id: 346,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 2940,
        date: '2014/04/02',
      }, {
        id: 347,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 2880,
        date: '2014/04/14',
      }, {
        id: 348,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1050,
        date: '2014/04/19',
      }, {
        id: 349,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1850,
        date: '2014/04/02',
      }, {
        id: 350,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 3160,
        date: '2014/04/01',
      }, {
        id: 351,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 875,
        date: '2014/04/04',
      }, {
        id: 352,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 3200,
        date: '2014/04/08',
      }, {
        id: 353,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 1380,
        date: '2014/04/21',
      }, {
        id: 354,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 3060,
        date: '2014/04/06',
      }, {
        id: 355,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 6690,
        date: '2014/05/19',
      }, {
        id: 356,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 4525,
        date: '2014/05/15',
      }, {
        id: 357,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 4665,
        date: '2014/05/10',
      }, {
        id: 358,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 4530,
        date: '2014/05/18',
      }, {
        id: 359,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 1330,
        date: '2014/05/08',
      }, {
        id: 360,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 1720,
        date: '2014/05/20',
      }, {
        id: 361,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 3750,
        date: '2014/05/16',
      }, {
        id: 362,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1290,
        date: '2014/05/10',
      }, {
        id: 363,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 4925,
        date: '2014/05/14',
      }, {
        id: 364,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 4300,
        date: '2014/05/22',
      }, {
        id: 365,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 5740,
        date: '2014/05/08',
      }, {
        id: 366,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 3760,
        date: '2014/05/18',
      }, {
        id: 367,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 7920,
        date: '2014/05/22',
      }, {
        id: 368,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 1440,
        date: '2014/05/21',
      }, {
        id: 369,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 5975,
        date: '2014/05/25',
      }, {
        id: 370,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 4440,
        date: '2014/05/05',
      }, {
        id: 371,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 2310,
        date: '2014/05/24',
      }, {
        id: 372,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 2250,
        date: '2014/05/06',
      }, {
        id: 373,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 2320,
        date: '2014/05/14',
      }, {
        id: 374,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 8370,
        date: '2014/05/06',
      }, {
        id: 375,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 5190,
        date: '2014/06/26',
      }, {
        id: 376,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 925,
        date: '2014/06/04',
      }, {
        id: 377,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 3240,
        date: '2014/06/20',
      }, {
        id: 378,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 3180,
        date: '2014/06/23',
      }, {
        id: 379,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 780,
        date: '2014/06/13',
      }, {
        id: 380,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 4680,
        date: '2014/06/08',
      }, {
        id: 381,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 2475,
        date: '2014/06/25',
      }, {
        id: 382,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1920,
        date: '2014/06/20',
      }, {
        id: 383,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 7500,
        date: '2014/06/25',
      }, {
        id: 384,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 5025,
        date: '2014/06/26',
      }, {
        id: 385,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 2400,
        date: '2014/06/08',
      }, {
        id: 386,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 1720,
        date: '2014/06/09',
      }, {
        id: 387,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 2880,
        date: '2014/06/21',
      }, {
        id: 388,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 5430,
        date: '2014/06/03',
      }, {
        id: 389,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 4475,
        date: '2014/06/19',
      }, {
        id: 390,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 1420,
        date: '2014/06/20',
      }, {
        id: 391,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 2670,
        date: '2014/06/25',
      }, {
        id: 392,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1930,
        date: '2014/06/02',
      }, {
        id: 393,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 580,
        date: '2014/06/25',
      }, {
        id: 394,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1620,
        date: '2014/06/12',
      }, {
        id: 395,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 4530,
        date: '2014/06/02',
      }, {
        id: 396,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 6025,
        date: '2014/06/23',
      }, {
        id: 397,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 3540,
        date: '2014/07/21',
      }, {
        id: 398,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 3000,
        date: '2014/07/01',
      }, {
        id: 399,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 3240,
        date: '2014/07/26',
      }, {
        id: 400,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 2265,
        date: '2014/07/22',
      }, {
        id: 401,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 400,
        date: '2014/07/09',
      }, {
        id: 402,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 1460,
        date: '2014/07/08',
      }, {
        id: 403,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 1620,
        date: '2014/07/18',
      }, {
        id: 404,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2400,
        date: '2014/07/25',
      }, {
        id: 405,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 5275,
        date: '2014/07/04',
      }, {
        id: 406,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 4475,
        date: '2014/07/03',
      }, {
        id: 407,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 3980,
        date: '2014/07/21',
      }, {
        id: 408,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 5240,
        date: '2014/07/11',
      }, {
        id: 409,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 1200,
        date: '2014/07/21',
      }, {
        id: 410,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 5700,
        date: '2014/07/18',
      }, {
        id: 411,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 5575,
        date: '2014/07/01',
      }, {
        id: 412,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 2160,
        date: '2014/07/02',
      }, {
        id: 413,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 960,
        date: '2014/07/09',
      }, {
        id: 414,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1280,
        date: '2014/07/04',
      }, {
        id: 415,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1040,
        date: '2014/07/02',
      }, {
        id: 416,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 5520,
        date: '2014/07/21',
      }, {
        id: 417,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1760,
        date: '2014/07/25',
      }, {
        id: 418,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 4080,
        date: '2014/07/07',
      }, {
        id: 419,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1000,
        date: '2014/07/21',
      }, {
        id: 420,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 3270,
        date: '2014/07/12',
      }, {
        id: 421,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 1770,
        date: '2014/08/23',
      }, {
        id: 422,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 2700,
        date: '2014/08/09',
      }, {
        id: 423,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 2175,
        date: '2014/08/03',
      }, {
        id: 424,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 3375,
        date: '2014/08/11',
      }, {
        id: 425,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2040,
        date: '2014/08/01',
      }, {
        id: 426,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 3000,
        date: '2014/08/21',
      }, {
        id: 427,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 3900,
        date: '2014/08/16',
      }, {
        id: 428,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1370,
        date: '2014/08/20',
      }, {
        id: 429,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 5700,
        date: '2014/08/01',
      }, {
        id: 430,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 1275,
        date: '2014/08/22',
      }, {
        id: 431,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 4060,
        date: '2014/08/13',
      }, {
        id: 432,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 2320,
        date: '2014/08/18',
      }, {
        id: 433,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 7590,
        date: '2014/08/24',
      }, {
        id: 434,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 4560,
        date: '2014/08/20',
      }, {
        id: 435,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 7575,
        date: '2014/08/20',
      }, {
        id: 436,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 700,
        date: '2014/08/25',
      }, {
        id: 437,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 2400,
        date: '2014/08/16',
      }, {
        id: 438,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1390,
        date: '2014/08/15',
      }, {
        id: 439,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1320,
        date: '2014/08/09',
      }, {
        id: 440,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 1680,
        date: '2014/08/09',
      }, {
        id: 441,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 1500,
        date: '2014/08/11',
      }, {
        id: 442,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 6150,
        date: '2014/09/21',
      }, {
        id: 443,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 3675,
        date: '2014/09/02',
      }, {
        id: 444,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 2250,
        date: '2014/09/05',
      }, {
        id: 445,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 3585,
        date: '2014/09/10',
      }, {
        id: 446,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 1470,
        date: '2014/09/01',
      }, {
        id: 447,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 2260,
        date: '2014/09/02',
      }, {
        id: 448,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 3765,
        date: '2014/09/03',
      }, {
        id: 449,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1640,
        date: '2014/09/04',
      }, {
        id: 450,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 4475,
        date: '2014/09/09',
      }, {
        id: 451,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 5975,
        date: '2014/09/04',
      }, {
        id: 452,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 1100,
        date: '2014/09/16',
      }, {
        id: 453,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 1320,
        date: '2014/09/18',
      }, {
        id: 454,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 8610,
        date: '2014/09/19',
      }, {
        id: 455,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 9210,
        date: '2014/09/09',
      }, {
        id: 456,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 3700,
        date: '2014/09/01',
      }, {
        id: 457,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 3620,
        date: '2014/09/19',
      }, {
        id: 458,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 4275,
        date: '2014/09/01',
      }, {
        id: 459,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 2370,
        date: '2014/09/03',
      }, {
        id: 460,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1870,
        date: '2014/09/10',
      }, {
        id: 461,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 2070,
        date: '2014/09/25',
      }, {
        id: 462,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 5025,
        date: '2014/09/19',
      }, {
        id: 463,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 1080,
        date: '2014/10/15',
      }, {
        id: 464,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 1400,
        date: '2014/10/22',
      }, {
        id: 465,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 4260,
        date: '2014/10/01',
      }, {
        id: 466,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 2745,
        date: '2014/10/01',
      }, {
        id: 467,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2920,
        date: '2014/10/23',
      }, {
        id: 468,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 3520,
        date: '2014/10/11',
      }, {
        id: 469,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 4035,
        date: '2014/10/20',
      }, {
        id: 470,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1730,
        date: '2014/10/05',
      }, {
        id: 471,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 975,
        date: '2014/10/06',
      }, {
        id: 472,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 5700,
        date: '2014/10/06',
      }, {
        id: 473,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 5080,
        date: '2014/10/18',
      }, {
        id: 474,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 2940,
        date: '2014/10/24',
      }, {
        id: 475,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 2670,
        date: '2014/10/04',
      }, {
        id: 476,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 1230,
        date: '2014/10/11',
      }, {
        id: 477,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 600,
        date: '2014/10/08',
      }, {
        id: 478,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 3700,
        date: '2014/10/08',
      }, {
        id: 479,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 3375,
        date: '2014/10/11',
      }, {
        id: 480,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1500,
        date: '2014/10/17',
      }, {
        id: 481,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 370,
        date: '2014/10/05',
      }, {
        id: 482,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2340,
        date: '2014/10/16',
      }, {
        id: 483,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 1080,
        date: '2014/10/08',
      }, {
        id: 484,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 2775,
        date: '2014/10/21',
      }, {
        id: 485,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 4380,
        date: '2014/11/09',
      }, {
        id: 486,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 5500,
        date: '2014/11/21',
      }, {
        id: 487,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 1920,
        date: '2014/11/24',
      }, {
        id: 488,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 765,
        date: '2014/11/24',
      }, {
        id: 489,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 370,
        date: '2014/11/18',
      }, {
        id: 490,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 3500,
        date: '2014/11/25',
      }, {
        id: 491,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 825,
        date: '2014/11/09',
      }, {
        id: 492,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 490,
        date: '2014/11/23',
      }, {
        id: 493,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 7075,
        date: '2014/11/20',
      }, {
        id: 494,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 1350,
        date: '2014/11/25',
      }, {
        id: 495,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 1440,
        date: '2014/11/15',
      }, {
        id: 496,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 2820,
        date: '2014/11/13',
      }, {
        id: 497,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 2280,
        date: '2014/11/12',
      }, {
        id: 498,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 1110,
        date: '2014/11/03',
      }, {
        id: 499,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 1150,
        date: '2014/11/23',
      }, {
        id: 500,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 2040,
        date: '2014/11/20',
      }, {
        id: 501,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 3090,
        date: '2014/11/24',
      }, {
        id: 502,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1940,
        date: '2014/11/24',
      }, {
        id: 503,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 3090,
        date: '2014/11/16',
      }, {
        id: 504,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 4900,
        date: '2014/11/05',
      }, {
        id: 505,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 3465,
        date: '2014/11/07',
      }, {
        id: 506,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1110,
        date: '2014/11/20',
      }, {
        id: 507,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 1650,
        date: '2014/11/02',
      }, {
        id: 508,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 5280,
        date: '2014/12/04',
      }, {
        id: 509,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 3075,
        date: '2014/12/02',
      }, {
        id: 510,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 690,
        date: '2014/12/07',
      }, {
        id: 511,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1305,
        date: '2014/12/15',
      }, {
        id: 512,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 1970,
        date: '2014/12/01',
      }, {
        id: 513,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 3760,
        date: '2014/12/18',
      }, {
        id: 514,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 1920,
        date: '2014/12/22',
      }, {
        id: 515,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1360,
        date: '2014/12/12',
      }, {
        id: 516,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 2525,
        date: '2014/12/06',
      }, {
        id: 517,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 5575,
        date: '2014/12/20',
      }, {
        id: 518,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 5560,
        date: '2014/12/10',
      }, {
        id: 519,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 4880,
        date: '2014/12/13',
      }, {
        id: 520,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 8850,
        date: '2014/12/03',
      }, {
        id: 521,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 2820,
        date: '2014/12/10',
      }, {
        id: 522,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 4000,
        date: '2014/12/12',
      }, {
        id: 523,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 5820,
        date: '2014/12/02',
      }, {
        id: 524,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 1275,
        date: '2014/12/12',
      }, {
        id: 525,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1310,
        date: '2014/12/01',
      }, {
        id: 526,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 2180,
        date: '2014/12/26',
      }, {
        id: 527,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 4470,
        date: '2014/12/17',
      }, {
        id: 528,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2990,
        date: '2014/12/15',
      }, {
        id: 529,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 7650,
        date: '2014/12/18',
      }, {
        id: 530,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 780,
        date: '2014/12/02',
      }, {
        id: 531,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2970,
        date: '2014/12/13',
      }, {
        id: 532,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 1155,
        date: '2014/12/05',
      }, {
        id: 533,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 4470,
        date: '2015/01/10',
      }, {
        id: 534,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 1125,
        date: '2015/01/21',
      }, {
        id: 535,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 645,
        date: '2015/01/17',
      }, {
        id: 536,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 675,
        date: '2015/01/05',
      }, {
        id: 537,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2840,
        date: '2015/01/05',
      }, {
        id: 538,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 2660,
        date: '2015/01/04',
      }, {
        id: 539,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 4560,
        date: '2015/01/12',
      }, {
        id: 540,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2880,
        date: '2015/01/20',
      }, {
        id: 541,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 500,
        date: '2015/01/02',
      }, {
        id: 542,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 3925,
        date: '2015/01/07',
      }, {
        id: 543,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 5660,
        date: '2015/01/18',
      }, {
        id: 544,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 1460,
        date: '2015/01/22',
      }, {
        id: 545,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 5040,
        date: '2015/01/10',
      }, {
        id: 546,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 4830,
        date: '2015/01/13',
      }, {
        id: 547,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 3075,
        date: '2015/01/22',
      }, {
        id: 548,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 3120,
        date: '2015/01/14',
      }, {
        id: 549,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 3525,
        date: '2015/01/23',
      }, {
        id: 550,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1930,
        date: '2015/01/09',
      }, {
        id: 551,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 2890,
        date: '2015/01/02',
      }, {
        id: 552,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 1545,
        date: '2015/01/17',
      }, {
        id: 553,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 3630,
        date: '2015/01/20',
      }, {
        id: 554,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 4035,
        date: '2015/01/14',
      }, {
        id: 555,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 345,
        date: '2015/01/06',
      }, {
        id: 556,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 7000,
        date: '2015/01/07',
      }, {
        id: 557,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 3060,
        date: '2015/02/13',
      }, {
        id: 558,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 6425,
        date: '2015/02/04',
      }, {
        id: 559,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 615,
        date: '2015/02/22',
      }, {
        id: 560,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1755,
        date: '2015/02/07',
      }, {
        id: 561,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 1540,
        date: '2015/02/21',
      }, {
        id: 562,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 2820,
        date: '2015/02/24',
      }, {
        id: 563,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 4305,
        date: '2015/02/10',
      }, {
        id: 564,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 1520,
        date: '2015/02/26',
      }, {
        id: 565,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 4725,
        date: '2015/02/18',
      }, {
        id: 566,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 6750,
        date: '2015/02/16',
      }, {
        id: 567,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 5540,
        date: '2015/02/07',
      }, {
        id: 568,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 1880,
        date: '2015/02/24',
      }, {
        id: 569,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 6180,
        date: '2015/02/26',
      }, {
        id: 570,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 9300,
        date: '2015/02/03',
      }, {
        id: 571,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 3700,
        date: '2015/02/26',
      }, {
        id: 572,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 740,
        date: '2015/02/01',
      }, {
        id: 573,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 4755,
        date: '2015/02/23',
      }, {
        id: 574,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 2570,
        date: '2015/02/20',
      }, {
        id: 575,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 2860,
        date: '2015/02/19',
      }, {
        id: 576,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 5430,
        date: '2015/03/21',
      }, {
        id: 577,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 4525,
        date: '2015/03/21',
      }, {
        id: 578,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 1515,
        date: '2015/03/10',
      }, {
        id: 579,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 630,
        date: '2015/03/15',
      }, {
        id: 580,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 1310,
        date: '2015/03/01',
      }, {
        id: 581,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 3200,
        date: '2015/03/17',
      }, {
        id: 582,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 3945,
        date: '2015/03/20',
      }, {
        id: 583,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2990,
        date: '2015/03/18',
      }, {
        id: 584,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 1125,
        date: '2015/03/22',
      }, {
        id: 585,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 7950,
        date: '2015/03/17',
      }, {
        id: 586,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 2960,
        date: '2015/03/25',
      }, {
        id: 587,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 6300,
        date: '2015/03/20',
      }, {
        id: 588,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 8670,
        date: '2015/03/07',
      }, {
        id: 589,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 3930,
        date: '2015/03/23',
      }, {
        id: 590,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 6975,
        date: '2015/03/02',
      }, {
        id: 591,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 4220,
        date: '2015/03/17',
      }, {
        id: 592,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 3090,
        date: '2015/03/25',
      }, {
        id: 593,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 2380,
        date: '2015/03/01',
      }, {
        id: 594,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1510,
        date: '2015/03/07',
      }, {
        id: 595,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 1020,
        date: '2015/03/19',
      }, {
        id: 596,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 6700,
        date: '2015/03/26',
      }, {
        id: 597,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 4890,
        date: '2015/04/02',
      }, {
        id: 598,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 7225,
        date: '2015/04/13',
      }, {
        id: 599,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 795,
        date: '2015/04/07',
      }, {
        id: 600,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1785,
        date: '2015/04/03',
      }, {
        id: 601,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 1850,
        date: '2015/04/03',
      }, {
        id: 602,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 5120,
        date: '2015/04/12',
      }, {
        id: 603,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 615,
        date: '2015/04/07',
      }, {
        id: 604,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2860,
        date: '2015/04/05',
      }, {
        id: 605,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 1525,
        date: '2015/04/24',
      }, {
        id: 606,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 7425,
        date: '2015/04/15',
      }, {
        id: 607,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 6080,
        date: '2015/04/13',
      }, {
        id: 608,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 2940,
        date: '2015/04/04',
      }, {
        id: 609,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 5580,
        date: '2015/04/16',
      }, {
        id: 610,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 9390,
        date: '2015/04/19',
      }, {
        id: 611,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 3200,
        date: '2015/04/26',
      }, {
        id: 612,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 4380,
        date: '2015/04/05',
      }, {
        id: 613,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 4725,
        date: '2015/04/06',
      }, {
        id: 614,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 930,
        date: '2015/04/25',
      }, {
        id: 615,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 1910,
        date: '2015/04/05',
      }, {
        id: 616,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 2725,
        date: '2015/04/16',
      }, {
        id: 617,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 4720,
        date: '2015/04/02',
      }, {
        id: 618,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 5190,
        date: '2015/04/10',
      }, {
        id: 619,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 2800,
        date: '2015/04/26',
      }, {
        id: 620,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 3780,
        date: '2015/04/24',
      }, {
        id: 621,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 2340,
        date: '2015/04/17',
      }, {
        id: 622,
        region: 'North America',
        country: 'USA',
        city: 'New York',
        sales: 4830,
        date: '2015/05/12',
      }, {
        id: 623,
        region: 'North America',
        country: 'USA',
        city: 'Los Angeles',
        sales: 2075,
        date: '2015/05/23',
      }, {
        id: 624,
        region: 'North America',
        country: 'USA',
        city: 'Denver',
        sales: 3420,
        date: '2015/05/21',
      }, {
        id: 625,
        region: 'North America',
        country: 'CAN',
        city: 'Vancouver',
        sales: 1440,
        date: '2015/05/10',
      }, {
        id: 626,
        region: 'North America',
        country: 'CAN',
        city: 'Edmonton',
        sales: 1680,
        date: '2015/05/15',
      }, {
        id: 627,
        region: 'South America',
        country: 'BRA',
        city: 'Rio de Janeiro',
        sales: 3440,
        date: '2015/05/16',
      }, {
        id: 628,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 4695,
        date: '2015/05/10',
      }, {
        id: 629,
        region: 'South America',
        country: 'PRY',
        city: 'Asuncion',
        sales: 2380,
        date: '2015/05/06',
      }, {
        id: 630,
        region: 'Europe',
        country: 'GBR',
        city: 'London',
        sales: 1875,
        date: '2015/05/25',
      }, {
        id: 631,
        region: 'Europe',
        country: 'DEU',
        city: 'Berlin',
        sales: 7550,
        date: '2015/05/14',
      }, {
        id: 632,
        region: 'Europe',
        country: 'ESP',
        city: 'Madrid',
        sales: 3340,
        date: '2015/05/01',
      }, {
        id: 633,
        region: 'Europe',
        country: 'RUS',
        city: 'Moscow',
        sales: 1400,
        date: '2015/05/22',
      }, {
        id: 634,
        region: 'Asia',
        country: 'CHN',
        city: 'Beijing',
        sales: 6060,
        date: '2015/05/22',
      }, {
        id: 635,
        region: 'Asia',
        country: 'JPN',
        city: 'Tokyo',
        sales: 8370,
        date: '2015/05/13',
      }, {
        id: 636,
        region: 'Asia',
        country: 'KOR',
        city: 'Seoul',
        sales: 3550,
        date: '2015/05/26',
      }, {
        id: 637,
        region: 'Australia',
        country: 'AUS',
        city: 'Sydney',
        sales: 2620,
        date: '2015/05/17',
      }, {
        id: 638,
        region: 'Australia',
        country: 'AUS',
        city: 'Melbourne',
        sales: 2400,
        date: '2015/05/21',
      }, {
        id: 639,
        region: 'Africa',
        country: 'ZAF',
        city: 'Pretoria',
        sales: 1740,
        date: '2015/05/21',
      }, {
        id: 640,
        region: 'Africa',
        country: 'EGY',
        city: 'Cairo',
        sales: 500,
        date: '2015/05/26',
      }, {
        id: 641,
        region: 'South America',
        country: 'ARG',
        city: 'Buenos Aires',
        sales: 780,
        date: '2015/05/07',
      }
  ];
    function filterData(e){
      e.preventDefault()
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

  }
</script>



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
    <!-- <script>
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
    </script> -->
    <script>
      $(document).ready(function () {
        setTimeout(function(){ 
        $(".filter-section").click();
        }, 1000);
        
      });
    </script>
  </>
<?php 
	include "report-close.php"; 
?>