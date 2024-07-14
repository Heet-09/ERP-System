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
  echo $sql = "SELECT * FROM $db[0] WHERE 1=1 LIMIT 10";
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
  echo $viewReportSettings[3];

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

  // echo 
  $viewResult = db::getInstance()->db_select($sql);
  // print_r($viewResult);
  // echo json_encode($viewResult['result_set']);
  $dateColumns = array();
  $dateColumnCount = 0;
  
  for($i =0; $i < $viewResult['num_rows']; $i++){
    foreach($viewResult['result_set'][$i] as $key => $value){
      if (validateDate($value, 'Y/m/d')) {  // it's a date
        if(!in_array($key, $dateColumns)) $dateColumns[$dateColumnCount++] = $key;
      }
      if (validateDate($value, 'Y-m-d')) {  // it's a date
        if(!in_array($key, $dateColumns)) $dateColumns[$dateColumnCount++] = $key;
      }
    }
  }
  // print_r($dateColumns);
  if($dateColumnCount > 0){
    $dateDataSource = "fields :[";
    $separator = "";
    for($i = 0; $i < $dateColumnCount; $i++){
      $dateDataSource .= $separator . "{
          dataField:'".$dateColumns[$i]."',
          dataType:'date'
        }";
      $separator = ",";
    }
      $dateDataSource .= "],";
  }
  function validateDate($date, $format = 'Y-m-d')
  {
      $d = DateTime::createFromFormat($format, $date);
      // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
      return $d && $d->format($format) === $date;
  }
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
    <!-- <script src="https://cdn.jsdelivr.net/npm/ag-grid-community@31.3.2/dist/ag-grid-community.js?t=1715777153731"></script> -->
       <script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise/dist/ag-grid-enterprise.js"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script> -->



<style>
  #pivotgrid {
    margin-top: 20px;
  }

  .currency {
    text-align: center;
  }

  .dx-treeview-node .dx-checkbox {
    display: none;
  }
  span.cross-filter {
    cursor: pointer;
    margin-left: 4px;
    margin-right: 4px;
    background: #bfbfbf;
    padding: 0 5px;
    border-radius: 10px;
  }
  .filter-selected{
    background: #bebebe96;
    border-radius: 15px;
    padding: 0 0 0 8px;
    color: #424242;
    margin-right: 2px;
    margin-left: 2px; 
  }


  #selectedFiltersList div{
    display: flex;
  }

  /* #filters{
    display:none;
    
  } */


</style>

<!-- SCRIPT FOR TYPE AND TEMPLATE -->
<script>

	var templateId = "0";
	var typeId="0";
  var tempName='' ;
  var templateDescription ='';
  var kreon =0;
  var tempName_for_modal;
  let call_for_template = false;
  let ogTemplateId;
 
  agGrid.LicenseManager.setLicenseKey("[TRIAL]_this_{AG_Charts_and_AG_Grid}_Enterprise_key_{AG-059380}_is_granted_for_evaluation_only___Use_in_production_is_not_permitted___Please_report_misuse_to_legal@ag-grid.com___For_help_with_purchasing_a_production_key_please_contact_info@ag-grid.com___You_are_granted_a_{Single_Application}_Developer_License_for_one_application_only___All_Front-End_JavaScript_developers_working_on_the_application_would_need_to_be_licensed___This_key_will_deactivate_on_{30 June 2024}____[v3]_[0102]_MTcxOTcwMjAwMDAwMA==59fc6bfa6d27f2fc6c8e0be66a04b355");
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
        $('.pivotgrid-div').html('<div>   <div id="myGrid" style="width: 1380px; height: 590px" class="ag-theme-quartz"></div></div>');
        $('.pivotgrid-display').show(); // Show pivotgrid-display if Pivot Grid is selected
        loadPivotGridData();
      } else if (selectedType === "2") {
        $('.gridTemplate1').hide(); // Hide templates with TemplateTypeID 1 (Pivot Grid)
        $('.gridTemplate2').show(); // Show templates with TemplateTypeID 2 (Data Grid)
      }
      // console.log("CHANGED TEMPLATE ID - " + typeId);
		  typeId = $("#typeSelect").val();
	  });
    $('.templateSelect').change(function () {
      if (selectedType === "1") {
        $('.pivotgrid-div').html('<div id="myGrid" style="width: 1380px; height: 590px" class="ag-theme-quartz"></div>');
        $('.pivotgrid-display').show(); // Show pivotgrid-display if Pivot Grid is selected
        $('.datagrid-display').hide(); // Hide datagrid-display
        $('.datagrid-div').html('');
        loadPivotGridData();
      } else if (selectedType === "2") {
        $('.datagrid-div').html('<div class="datagrid-display"><div class="dx-viewport"><div class="demo-container"><div id="gridContainer"></div><div id="scrolling-mode"></div><div id=editing-mode></div></div></div></div></div></div>');
        $('.datagrid-display').show(); // Show datagrid-display if Data Grid is selected
        $('.pivotgrid-display').hide(); // Hide pivotgrid-display
        $('.pivotgrid-div').html('');
        loadDataGridData();
      }
      templateId = $("#templateSelect").val();
      ogTemplateId=templateId;
      // console.log("CHANGED TEMPLATE ID - " + templateId);
      tempName=$('#templateSelect').find('option:selected').text();
      getDataFromAPI(1, templateId, tempName, templateDescription, typeId, (error, data) => {
        console.log("in api");
        if (error) {
          console.error('Error fetching data:', error);
          console.log("in if");
        } else {
          // console.log('Data retrieved:', data);
          // pivotglobalstate = data;
          // console.log("after fetching",pivotglobalstate);
          // console.log(gridApi.getState());
          // console.log("Before destroy",gridApi.getState());
          // const state = gridApi.getState();
          // gridApi.destroy();

          // fetch("https://www.ag-grid.com/example-assets/olympic-winners.json")
          // fetch("https://www.ag-grid.com/example-assets/row-data.json")
          // .then((response) => response.json())
          // .then((data) => gridApi.setGridOption("rowData", data));
          // console.log("After Destru",gridApi.getState());

          // fetch('https://www.ag-grid.com/example-assets/row-data.json').then(function (response) {
          //     return response.json();
          // }).then(function (data) {
          //     dynamicallyConfigureColumnsFromObject(data[0])
          //     gridApi.setRowData(data);
          // });
          // console.log("After Destroy",gridApi.getState());

          // gridApi.setState(pivotglobalstate);
          // Process the data as needed
          // gridApi.applyColumnState({
            // state: pivotglobalstate,
          // });
          // console.log("direct",state);
          // console.log("direct",gridApi.getState());
          // gridOptions.initialState = data;
          // console.log("after setting intial state",gridOptions.intialState);
          // console.log("after setting intial state",gridApi.getState());
        // gridApi.setColumnState(data)
        }
      });
      
    });
  });
</script>


<!-- HTML FOR DISPLAYING FILTERS -->
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
              <input class="btn btn-danger" type="submit" id="submit" onclick="savingTemplate()" value="FILTER">
              <a href="<?php echo $url; ?>" class="btn btn-primary">CLEAR</a><br />
              <!-- <img src="loading-gif.gif" id="loading" style="width: 60px;margin-top: 30px;"/> -->
            </div>
          <!-- </div> -->
        </div>
      </form>
    </div>
</section>

<script>
  function savingTemplate(){
    // sendStorageRequest()
    sendStorageRequest("storageKey", "text", "PUT", h);
  }
</script>
<!-- HTML FOR DISPLAYING REPORT -->
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
                    // console.log("in modal Valur");
                    // templateId=0;
                    tempName = $("#templatetitle").val();
                    templateDescription = $("#templatedescription").val();
                    // console.log("tempName",tempName);
                    $("#templatetitle").val("");
                    $("#templatedescription").val("");
                    tempName_for_modal=tempName;
                    console.log(tempName_for_modal)
                    // console.log("modal report ID",);
                    // console.log("modal templateId",templateId);
                    // console.log("modal tempName",tempName);
                    // console.log("modal template Description",templateDescription);
                    // console.log("modal typeID",typeId);
                    // console.log("modal state",h);
                    tempName="";
                    templateDescription = "";
                    call_for_template = true;
                    // console.log(call_for_template);
                    sendStorageRequest("storageKey", "text", "PUT", h);
                }
              </script>
            <!-- </form> -->
          </div>   
        </div>                                                                       
      </div>                                          
    </div>
  </div>
  <!-- HTML FOR DISPLAYING PIVOT GRID -->
  <section class="pivotgrid-div"> 
    <!-- WRITTEN ON LINE 183 -->
  </section>

  <!-- HTML FOR DISPLAYING PIVOT GRID -->
  <section class="datagrid-div">
    <!-- WRITTEN ON LINE 189 -->
  </section>
</section>

      
<!-- SCRIPT FOR DATA GRID -->
<script>
  window.jsPDF = window.jspdf.jsPDF;
  function loadDataGridData(){
    let selectedId=[];
    let isArray=[];
    $(() => {
      let changedBySelectBox;
      let titleSelectBox;
      let clearSelectionButton;
      let showIdButton;
      let editingType=0;
      const scrollingModeOptions = ['standard', 'virtual'];
      const dataGrid = $('#gridContainer').dxDataGrid({
        dataSource: heet,
        editing: {
          mode:editingType,
        
          allowUpdating: true,
          allowAdding: true,
          allowDeleting: true,
          selectTextOnEditStart: true,
          startEditAction: 'click',
        },
        selection: {
          mode: 'multiple',
        },
        pager: {
          showPageSizeSelector: true,
          allowedPageSizes: [10, 25, 50, 100,500],
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
        headerFilter: {
          visible: true,
        },
        filterPanel: { visible: true },
        columnChooser: {
          enabled: true
        },
        // focusedRowEnabled: true,
        summary: {
          totalItems: [{
            name: 'SelectedRowsSummary',
            showInColumn: 'SaleAmount',
            displayFormat: 'Sum: {0}',
            valueFormat: 'Meter',
            summaryType: 'custom',
          }],
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
        onSelectionChanged(selectedItems) {
          var selectedKeys = dataGrid.getSelectedRowKeys();
          var selectedData = dataGrid.getSelectedRowsData();
          idArray = selectedKeys.map(item => item.id);
          const data = selectedItems.selectedRowsData;
          if (data.length > 0) {
            $('#selected-items-container').text(
              data
                .map((value) => `${value.FirstName} ${value.LastName}`)
                .join(', '),
            );
          } else {
            $('#selected-items-container').text('Nobody has been selected');
          }
          // if (!changedBySelectBox) {
          //   titleSelectBox.option('value', null);
          // }

          changedBySelectBox = false;
          clearSelectionButton.option('disabled', !data.length);
        },
        toolbar: {
          items: [
            {
              widget: 'dxButton',
              location: 'before',
              options: {
                text: 'Clear Selection',
                disabled: true,
                onInitialized(e) {
                  clearSelectionButton = e.component;
                },
                onClick() {
                  dataGrid.clearSelection();
                },
              },
            },
            {
              text:'Hide Columns',
              onClick(){
                dataGrid.showColumnChooser();
              },
            },
            {
              widget: 'dxButton',
              location: 'before',
              options: {
                text: 'Show ID',


                onClick() {
                  // console.log(idArray);
                },
              },
            },
          ],
        },
        export: {
          enabled: true,
          allowExportSelectedData: true,
        },
        stateStoring: {
          enabled: true,
          type: "custom",
          customLoad: function () {
            // console.log("load called from datagrid");
              return sendStorageRequest("storageKey", "json", "GET");
          },
          customSave: function (state) {
            // console.log("save called from datagrid");
            // console.log("save",state);
            alert("jj");
            passingstate(state);
            if(kreon==1){
              // console.log("in if condition of cutomSave");
              sendStorageRequest("storageKey", "text", "PUT", state);
            }
          }
        },
        scrolling:{
          mode:'virtual'
        },
        columnChooser: {
          enabled: true,
          mode: "dragAndDrop" // or "select"
        },
        onExporting(e) {
          const workbook = new ExcelJS.Workbook();
          const worksheet = workbook.addWorksheet('Employees');
          DevExpress.excelExporter.exportDataGrid({
            component: e.component,
            worksheet,
            autoFilterEnabled: true,
          }).then(() => {
            workbook.xlsx.writeBuffer().then((buffer) => {
              saveAs(new Blob([buffer], { type: 'application/octet-stream' }), ''+tempName+'-'+getTodaysDate()+'-'+time+'.xlsx');
            });
          });
        },
      }).dxDataGrid('instance');
      const resizingModes = ['batch', 'form'];
      $('#editing-mode').dxCheckBox({
        text: 'Show Page Size Selector',
        value: true,
        onValueChanged(data) {
          dataGrid.option('pager.showPageSizeSelector', data.value);
        },
      });
      $('#scrolling-mode').dxSelectBox({
        value: 'standard',
        items: scrollingModeOptions,
        inputAttr: { 'aria-label': 'Scrolling Mode' },
        width: 250,
        onValueChanged(e) {
          dataGrid.option('scrolling.mode', e.value);
        },
      }).dxSelectBox('instance');
    });
  }
</script>


<!-- SCRIPT FOR PIVOT GRID -->
<script>
  var pivotglobalstate;
  let gridApi;
  let agState;
  let h;
  function save() {
    pivotglobalstate = h;
    console.log("sent for saving",pivotglobalstate);
    // console.log();
    return sendStorageRequest("organisatieKey", "text", "PUT", pivotglobalstate);
  }

  function passingstate(state) {
    console.log("in global save", state);
    globalstate = state;
    expandAll = [];
    filterValues = [];
    console.log("passing");
    globalstate.fields.forEach(function(item, index) {
        if (item.area == "row" || item.area == "column") {
            expandAll.push(item.dataField);
            count++;
            item.expanded = true;
        }
        if (item.filterValues != null && Array.isArray(item.filterValues)) {
            filterValues.push({ dataField: item.dataField, filterValues: item.filterValues });
        }
    });
    // updateFiltersUI();
  }

  function getDataFromAPI(reportID, templateId, tempName, templateDescription, typeId, callback) {
    $.ajax({
      url: `AgGridReportDataStorage.php?ReportID=${reportID}&templateId=${templateId}&tempName=${tempName}&templateDescription=${templateDescription}&typeID=${typeId}`,
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json"
      },
      type: "GET",
      dataType: "json",
      success: function(data) {
        callback(null, data);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        callback(new Error(textStatus || errorThrown));
      }
    });
  }

  function sendStorageRequest(key, datatype, type, data) {
    var deferred = $.Deferred();
    if (data !== undefined) var d = JSON.stringify(data);
    else var d = "";
    // console.log(d);
    console.log("ss",tempName_for_modal);
    if(call_for_template){
      templateId=0
     tempName=tempName_for_modal;
      console.log(tempName_for_modal)
    }
    console.log(tempName);
    // console.log(call_for_tem
    var storageRequestSettings = {
      url: "AgGridReportDataStorage.php?ReportID=<?php echo $ReportID; ?>&templateId=" +
      templateId +
      "&tempName="+
      tempName+
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
        deferred.resolve(data);
        console.log("tempname",tempName);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        deferred.reject();
      },
    };
    templateId=ogTemplateId;
    tempName='';
    if (data) {
      storageRequestSettings.data = JSON.stringify(data);
    } else {
    }
    $.ajax(storageRequestSettings);
    return deferred.promise();
  }

  function saveState() {
    h=gridApi.getState();
    console.log("h",h);
    // console.log("hh",gridApi.getColumnGroupState());
  }
      
  // Row Data Interface
  class CompanyLogoRenderer {
    eGui;
    
    // Optional: Params for rendering. The same params that are passed to the cellRenderer function.
    init(params) {
      let companyLogo = document.createElement("img");
      companyLogo.src = `https://www.ag-grid.com/example-assets/space-company-logos/${params.value.toLowerCase()}.png`;
      companyLogo.setAttribute(
        "style",
        "display: block; width: 25px; height: auto; max-height: 50%; margin-right: 12px; filter: brightness(1.1)"
      );
      let companyName = document.createElement("p");
      companyName.textContent = params.value;
      companyName.setAttribute(
        "style",
        "text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"
      );
      this.eGui = document.createElement("span");
      this.eGui.setAttribute(
          "style",
          "display: flex; height: 100%; width: 100%; align-items: center"
      );
      this.eGui.appendChild(companyLogo);
      this.eGui.appendChild(companyName);
    } 
    // Required: Return the DOM element of the component, this is what the grid puts into the cell
    getGui() {
        return this.eGui;
      }
    // Required: Get the cell to refresh.
    refresh(params) {
      return false;
    }
  }
    
  const columnDefs = [
    // { headerName: "Make & Model", valueGetter: p => p.make + ' ' + p.model},
    // {field: "mission",valueFormatter: (params) => {return "" + params.value.toLocaleString();},checkboxSelection: true,},
    // {field: "company",cellRenderer: CompanyLogoRenderer,},
    // {field: "location" },
    // {field: "date" },
    // {field: "price"},
    // {field: "successful" },
    // {field: "rocket",cellClassRules: {'rag-green': params => params.value === true,}},
  ];

  // Grid Options: Contains all of the grid configurations
  const gridOptions = {
    defaultColDef: {
      filter: true,
      editable: true,

    },
    columnDefs: columnDefs,
    sideBar:true,
    pagination: true,
    onStateUpdated:event =>{
      // console.log("changd");
      saveState();
    },
    // Data to be displayed
    rowData: [],
    // Columns to be displayed (Should match rowData properties)
  };

  function loadPivotGridData() {
    // console.log("in pivot grid");

      
    // Create Grid: Create new grid within the #myGrid div, using the Grid Options object
    gridApi = agGrid.createGrid(document.querySelector("#myGrid"), gridOptions);
    // Fetch Remote Data



    fetch('https://www.ag-grid.com/example-assets/row-data.json').then(function (response) {
        return response.json();
    }).then(function (data) {
        dynamicallyConfigureColumnsFromObject(data[0])
        gridApi.setRowData(data);
    });
  }
  
function dynamicallyConfigureColumnsFromObject(anObject){
  const colDefs = gridApi.getColumnDefs();
  colDefs.length=0;
  const keys = Object.keys(anObject)
  keys.forEach(key => colDefs.push({field : key}));
  gridApi.setColumnDefs(colDefs);
}
</script>



<!--GENERAL ON LOAD SCRIPT FOR THE ENTIRE PAGE -->
    <script>
      $(document).ready(function() {
          $(".page-header").html("<h2><?php echo $viewReportSettings[1]; ?></h2>");
          $(document).prop('title', '<?php echo $viewReportSettings[1]; ?>');
      //    // $('form').attr('action', 'db_backup_31.php');
      });
    </script>
     
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