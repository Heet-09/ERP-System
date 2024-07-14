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
  var templateName='' ;
  var templateDescription ='';
  var kreon =0;
  var tempName_for_downloading;
  // let filtersList =[];
  let content;
 
  let currentDate = new Date();
  let time = currentDate.getHours() + ":" + currentDate.getMinutes() + ":" + currentDate.getSeconds();

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
        $('.pivotgrid-div').html('<div class="pivotgrid-display"><div class="dx-viewport demo-container"><div id="pivotgrid-demo"><div id="sales"></div><div id="sales-popup"></div><div id="pivotgrid-chart"></div><div id="pivotgrid"></div></div></div></div>');
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
        $('.pivotgrid-div').html('<div class="pivotgrid-display"><div class="dx-viewport demo-container"><div id="pivotgrid-demo"><div id="sales"></div><div id="sales-popup"></div><div id="pivotgrid-chart"></div><div id="pivotgrid"></div></div></div></div>');
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
      // console.log("CHANGED TEMPLATE ID - " + templateId);
      tempName=$('#templateSelect').find('option:selected').text();
      // console.log(tempName);
      
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
              <input class="btn btn-danger" type="submit" id="submit" onclickx="filterData(e);" value="FILTER">
              <a href="<?php echo $url; ?>" class="btn btn-primary">CLEAR</a><br />
              <!-- <img src="loading-gif.gif" id="loading" style="width: 60px;margin-top: 30px;"/> -->
            </div>
          <!-- </div> -->
        </div>
      </form>
    </div>
</section>

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
    <div id ="filters" >
      <h3>Filters</h3>
      <div id="selectedFiltersList"  style=" margin-right:20px;display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));">
      </div>
          <!-- </div> -->
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
                    templateName = $("#templatetitle").val();
                    templateDescription = $("#templatedescription").val();
                    $("#templatetitle").val("");
                    $("#templatedescription").val("");
                    // console.log(templateName);
                    // console.log(templateDescription);
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
  
  var heet1= <?php echo json_encode($viewResult['result_set']); ?>;
  var i=0;
  heet = heet1.map(item => {
    return {
    ...item, 
    };
  });

  let firstItemData = [];
  let expandAll = [];
  let count=0;
  let filterValues = [];
  var globalstate;
  function save() {
    return sendStorageRequest("organisatieKey", "text", "PUT", globalstate);
  }



  // Function to update the UI after changing filters
  // function updateFiltersUI() {
  //   console.log("update");
  //   var filtersDiv = document.getElementById("selectedFiltersList");
  //   // console.log(filterValues);
  //   // Create a document fragment to hold the new filter elements
  //   var fragment = document.createDocumentFragment();

  //   // Iterate over filterValues and add/update filter elements
  //   filterValues.forEach(function(item) {
  //       if (item.filterValues && item.filterValues.length > 0) {
  //           // Create filter element if filterValues exist
  //           var filterElement = document.createElement("div");
  //           var htmlData = `${item.dataField}:`;
  //           item.filterValues.forEach(function(fv) {
  //               if (fv.length > 0)
  //                   htmlData += `<div class='filter-selected'>${fv}<span class='cross-filter' onclick='removeFilter("${fv}");'> &nbsp &nbspx</span></div>`;
  //           });
  //           filterElement.innerHTML = htmlData;
  //           fragment.appendChild(filterElement);
  //       }
  //   });

  //   // Replace existing content with the new filter elements
  //   filtersDiv.innerHTML = "";
  //   filtersDiv.appendChild(fragment);

  //   //   // Show/hide the filters container based on filterValues length
  //   // var filtersContainer = document.getElementById("Filters");
  //   // if (filterValues.length > 0) {
  //   //     filtersContainer.style.display = "block"; // Show the filters container
  //   // } else {
  //   //     filtersContainer.style.display = "none"; // Hide the filters container
  //   // }

  // }

  function updateFiltersUI() {
    console.log("update");
    var filtersDiv = document.getElementById("selectedFiltersList");
    filtersDiv.innerHTML = ""; // Clear existing content of the div

    // Iterate over filterValues and add/update filter elements
    filterValues.forEach(function(item) {
        if (item.filterValues && item.filterValues.length > 0) {
            // Create filter element if filterValues exist
            var filterElement = document.createElement("div");
            var htmlData = `${item.dataField}:`;
            item.filterValues.forEach(function(fv) {
                if (fv.length > 0)
                    htmlData += `<div class='filter-selected'>${fv}<span class='cross-filter' onclick='removeFilter("${fv}");'> &nbsp &nbspx</span></div>`;
            });
            filterElement.innerHTML = htmlData;
            filtersDiv.appendChild(filterElement);
        }
    });
}


  function removeFilter(valueToRemove) {
    console.log("remove");
      filterValues.forEach(function(item, index) {
          if (item.filterValues) {
              var indexToRemove = item.filterValues.indexOf(valueToRemove);
              if (indexToRemove !== -1) {
                  item.filterValues.splice(indexToRemove, 1);
                  // Check if filterValues becomes empty after removal
                  if (item.filterValues.length === 0) {
                      // Remove the entire item from filterValues
                      filterValues.splice(index, 1);
                  }
              }
          }
      });

      // Update the UI and apply changes to the pivot grid
      $("#sales").dxPivotGrid("instance").getDataSource().state(globalstate);
      updateFiltersUI();
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
            // item.filterType='inlcude';
            // console.log("if item",item);
        }
        if (item.filterValues != null && Array.isArray(item.filterValues)) {
            filterValues.push({ dataField: item.dataField, filterValues: item.filterValues });
            // console.log("filterValues",filterValues);
        }
    });
    // console.log("filterValues",filterValues.length);
      // Update the UI after setting the state
    updateFiltersUI();
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
        // alert("Success_sendStorage");
        // console.log(data);
        // console.log("succes");
        data.fields.forEach(function(item,index){
          if(item.area == "row" || item.area == "column"){
            item.expanded=true;
            // console.log("lls");
          }
        })

        // alert("in storage req");
      
        deferred.resolve(data);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        deferred.reject();
      },
    };
    if (data) {
      // console.log("TEmplate 2 : " + templateId);
      // console.log("SENDING...");
      storageRequestSettings.data = JSON.stringify(data);
      // console.log(data);
    } else {
      // console.log("TEmplate 3 : " + templateId);
      // console.log("RECEIVING...");
    }
    // console.log("TN"+templateName);
                  // console.log("TD"+templateDescription);
    $.ajax(storageRequestSettings);
    return deferred.promise();
  }

  function loadPivotGridData() {
    $(function() {
      function isNotEmpty(value) {
        return value !== undefined && value !== null && value !== '';
      }
      var loadOptions = {
        take: 0,
         skip: 10,
         requireTotalCount: true
      };

      // console.log("j");
      // const store = new DevExpress.data.CustomStore({
      //   load() {
      //     const deferred = $.Deferred();
      //     const paramNames = [
      //        'skip', 'take', 'requireTotalCount', 'requireGroupCount',
      //       'sort', 'filter', 'totalSummary', 'group', 'groupSummary',
      //     ];
      //     const args = {
      //     };
      //     paramNames
      //       .filter((paramName) => isNotEmpty(loadOptions[paramName]))
      //       .forEach((paramName) => { args[paramName] = JSON.stringify(loadOptions[paramName]); });
      //       // let a =5;
      //       // loadOptions.skip=5;
      //     console.log("Skip",loadOptions.skip);
      //     console.log("Take",loadOptions.take);
      //     console.log("count",loadOptions.requireTotalCount);

      //     $.ajax({
      //       // url: 'https://js.devexpress.com/Demos/WidgetsGalleryDataService/api/Sales/Order',
      //       url:'https://dummyjson.com/products',
      //       dataType: 'json',
      //       data: args,
      //       success(result) {
      //         deferred.resolve(result.products  , {
      //           totalCount: result.totalCount,
      //           summary: result.summary,
      //           groupCount: result.groupCount,
      //           // console.log("totalCount",totalCount);
      //         });
      //       // console.log(deferred);
      //       },
      //       error() {
      //         deferred.reject('Data Loading Error');
      //       },
      //       timeout: 5000,
      //     });
      //     return deferred.promise();
      //   },
      // });
      
      let pivotGridChart;
      let p;
      if(<?php echo $viewReportSettings[3];?>){
        pivotGridChart = $('#pivotgrid-chart').dxChart({
          commonSeriesSettings: {
            type: 'bar',
          },
          tooltip: {
            enabled: true,
            
          },
        }).dxChart('instance');
      }

      const salesPivotGrid = $("#sales").dxPivotGrid({
        allowSortingBySummary: true,
        allowSorting: true,
        allowFiltering: true,
        height: 1200,
        showBorders: true,
        // rowHeaderLayout: "standard",
        onContextMenuPreparing: contextMenuPreparing,
        columnAutoWidth: true,
        headerFilter: {
          search: {
            enabled: true,
          },
          showRelevantValues: true,
          // width: 300,
          // height: 500,
        },
        dataFieldArea: 'column',
        wordWrapEnabled: false,
        showBorders: true,
        allowExpandAll: true,
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
            p=1;
            return sendStorageRequest("organisatieKey", "json", "GET");
          },
          customSave: function(gridState) {
            passingstate(gridState);
            if (kreon === 1) {
              return sendStorageRequest("organisatieKey", "text", "PUT", gridState);
            } 
          },
        },
        fieldChooser: {
          enabled: true,
          applyChangesMode: "instantly",
          allowSearch: true,
        },
        onCellClick(e) {
          if(<?php echo $viewReportSettings[4]?>){
            if (e.area === "data") {
              const pivotGridDataSource = e.component.getDataSource();
              // console.log(pivotGridDataSource);
              const rowPathLength = e.cell.rowPath.length;
              const rowPathName = e.cell.rowPath[rowPathLength - 1];
              const popupTitle = `${rowPathName || "Total"} Drill Down Data`;
              // console.log("e.cell"+e.cell);
              // console.log(e.cell);
              // console.log("not");
              drillDownDataSource = pivotGridDataSource.createDrillDownDataSource(e.cell); 
              // console.log(pivotGridDataSource.state());
              // console.log(drillDownDataSource);
              salesPopup.option("title", popupTitle);
              salesPopup.show();
            }
          }
        },
        // dataSource: {
        //     store: heet
        // },
        // dataSource: store,
        dataSource: {
            // ...
            remoteOperations: true,
            load: function (loadOptions) {
                let d = $.Deferred();
                $.getJSON('https://jsonplaceholder.typicode.com/todos', {
                    // Passing settings to the server
 
                    // Pass if the remoteOperations property is set to true
                    take: loadOptions.take,
                    skip: loadOptions.skip,
                    group: loadOptions.group ? JSON.stringify(loadOptions.group) : "",
                    filter: loadOptions.filter ? JSON.stringify(loadOptions.filter) : "",
                    totalSummary: loadOptions.totalSummary ? JSON.stringify(loadOptions.totalSummary) : "",
                    groupSummary: loadOptions.groupSummary ? JSON.stringify(loadOptions.groupSummary) : ""
                }).done(function (result) {
                    // You can process the received data here
                    // console.log("result",result);
                      if( result)
                          d.resolve(result  , { summary: result.summary });
                      else
                          d.resolve(result);
                });
                return d.promise();
            }
        },
        showBorders: true,
        remoteOperations: true,
        paging: {
          pageSize: 12,
        },
        pager: {
          showPageSizeSelector: true,
          allowedPageSizes: [8, 12, 20],
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
        onContentReady(e){
          // console.log(p);
          content=0;
          // if(p==2){
            // alert("k"); 
            // console.log(e.component.getDataSource().state());
            content = e.component.getDataSource().state();
            // console.log(content);
            content.fields.forEach(function(item,index){
              // if     (item.area == "row" || item.area == "column"){
              item.expanded=true;
              item.filterType='include';
              // console.log("j",item.dataField);
              // console.log("lls");
              // passingstate(content);
            // }
            });
          // }
          // if(p==2){

          //   console.log("content",content);
          // }
          // e.component.getDataSource().state(content);
          // console.log(e.component.getDataSource().state());
          p++;

          // expandAll = [];
          // filterValues = [];

          // content.fields.forEach(function(item) {
          //   if (item.area == "row" || item.area == "column") {
          //       expandAll.push(item.dataField);
          //       count++;
          //       item.expanded = true;
          //       item.filterType = 'include';
          //   }
          //   if (item.filterValues != null && Array.isArray(item.filterValues)) {
          //       filterValues.push({ dataField: item.dataField, filterValues: item.filterValues });
          //   }
          // });

          // console.log("filtervalues", filterValues);
          // updateFiltersUI(); // Update the UI after setting the state
        }
      }).dxPivotGrid("instance");


      // console.log("dataSource",heet);


      $("#btn").dxButton({        
        onClick: function() {
          for(var i=0;i<expandAll.length;i++){
            $("#sales").dxPivotGrid("instance").getDataSource().expandAll(""+expandAll[i]+"");
          }
        }
      });
      // salesPivotGrid.bindChart(pivotGridChart, {
      //   dataFieldsDisplayMode: 'splitPanes',
      //   alternateDataFields: false,
      // });

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