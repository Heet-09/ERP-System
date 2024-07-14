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

<script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise/dist/ag-grid-enterprise.js"></script>

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
  let pivotglobalsource=0;
  let gridApi;
  let topg=0;
  let topfilters;
  var pivotglobalstate;
  let h;
  let count=0;
  let toppivot;
  let toppivotmode;
  let pivotcolumn;

 
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
        $('.pivotgrid-div').html('<div>   <div id="myGrid" style="width: 1380px; height: 590px" class="ag-theme-balham"></div></div>');
        $('.pivotgrid-display').show(); // Show pivotgrid-display if Pivot Grid is selected
        // console.log("typeSelectChange FUcntion",count++);
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
        $('.pivotgrid-div').html('<div id="myGrid" style="width: 1380px; height: 590px" class="ag-theme-balham"></div>');
        $('.pivotgrid-display').show(); // Show pivotgrid-display if Pivot Grid is selected
        $('.datagrid-display').hide(); // Hide datagrid-display
        $('.datagrid-div').html('');
        // console.log("templateSelectChange FUcntion",count++);
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
      tempName=$('#templateSelect').find('option:selected').text();
      pivotmode=false;
      getDataFromAPI(1, templateId, tempName, templateDescription, typeId, (error, data) => {
        if (error) {
          console.error('Error fetching data:', error);
        } else {
          pivotglobalsource=data;
          console.log("Fetched",data);
          templateg=0;
          topfilters=0;
          if(data.filter != undefined){
            topfilters =data.filter.filterModel
          }
          toppivot=data.pivot;
          toppivotmode=data.pivot.pivotMode;
            
          if(data.pivot.pivotMode){
            // pivotmode=true
            console.log("toppivot in templatechnage",pivotmode);
            gridApi.setGridOption("pivot", toppivot);
          }
          gridApi.applyColumnState({ 
            state: data.columns,
            applyOrder: true,
          });
           
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
                    // console.log(tempName_for_modal)
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
                    // console.log("sort model from modal ",gridApi.getFilterModel());
                    modalh = gridApi.getState();
                    modalj=gridApi.getColumnState();
                    pivotglobalstate = modalh;
                    modalh.columns=modalj;
                    console.log("template for saving",modalh);
                    return sendStorageRequest("organisatieKey", "text", "PUT", modalh);
                    // sendStorageRequest("storageKey", "text", "PUT", gridApi.getState());
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

  <!-- SCRIPT FOR GRID -->
  <script>

    function save() {
      h = gridApi.getState();
      j=gridApi.getColumnState();
      pivotglobalstate = h;
      h.columns=j;
      console.log("sent for saving ",h);
      console.log("value of pivot",h.pivot);
      return sendStorageRequest("organisatieKey", "text", "PUT", h);
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
      if(call_for_template){
        templateId=0
        tempName=tempName_for_modal;
      }
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
          console.log("data sent",d);
          console.log("tempname",tempName);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          deferred.reject();
        },
      };
      templateId=ogTemplateId;
      tempName='';
      console.log("key ",key);
      if (data) {
        storageRequestSettings.data = JSON.stringify(data);
      } else {
      }
      $.ajax(storageRequestSettings);
      return deferred.promise();
    }
    
    const columnDefs = [];
      let pivotmode=false;

    const gridOptions = {
      columnDefs: columnDefs,
      defaultColDef: {
        flex: 1,
        // minWidth: 180,
        filter: true,
        floatingFilter: true,
        editable: true,
        enableRowGroup: true,
        enablePivot: true,
        enableValue: true,
        // resizable: true,
        suppressSizeToFit: true,
      },
      sideBar:true,
      enablePivot:true,
      enableFillHandle: true,
      enableRangeSelection: true,
      rowGroupPanelShow: "always",
      groupDefaultExpanded: -1,
      groupTotalRow: "bottom",
      grandTotalRow: "bottom",
      // For CheckboX Multiple Selection here
      rowSelection: "multiple",
      suppressRowClickSelection: true,
      // tiil here
      // Auto size for columns chnaged(27 May)
      autoSizeStrategy: {
        type: "fitCellContents",
      },
      autoGroupColumnDef: {
        minWidth:180 ,
        filter: 'agGroupColumnFilter',
        groupDisplayType: "multipleColumns",
      },
      onGridReady:e=>{
        topg=0;
        console.log("in Grid");
        if(tempName){ 
          getDataFromAPI(1, templateId, tempName, templateDescription, typeId, (error, data) => {
            if (error) {
              console.error('Error fetching data:', error);
            } else {
              pivotglobalsource=data;
              console.log("After fetched",data);
            gridApi.applyColumnState({ 
              state: data.columns,
              applyOrder: true,
            });
            console.log("toppivot in grid ready",toppivot);
             gridApi.setGridOption("pivot", toppivot);
          }
        });
      }
    },                                                      
    onStateUpdated:event =>{
      topg++
      console.log("State",gridApi.getState());
      console.log("pivot state",gridApi.getPivotColumns());
      if(topg==3){
        if(toppivotmode){
          gridApi.setGridOption("pivotMode", true);
        }
        gridApi.applyColumnState({ 
          state: pivotglobalsource.columns,
          applyOrder: true,
        });
        gridApi.setFilterModel(topfilters);
      }
      // params.columnApi.autoSizeAllColumns();
      // gridApi.autoSizeAllColumns()
      onst allColumnIds = [];
  gridApi.getColumns().forEach((column) => {
    allColumnIds.push(column.getId());
  });

  gridApi.autoSizeColumns(allColumnIds, skipHeader);
    },
  };

  function loadPivotGridData() {
    // console.log("loadPivotGridData",count++);
    var gridDiv = document.querySelector("#myGrid");
    gridApi = agGrid.createGrid(gridDiv, gridOptions);
  
    fetch("reportGetDataFromView.php?ViewName=2")
    .then(function (response) {
      return response.json();
    }).then(function (data) {
      dynamicallyConfigureColumnsFromObject(data[0])
      gridApi.setRowData(data);
      
      // cellRenderer: 'agCheckboxCellRenderer',
      //   cellEditor: 'agCheckboxCellEditor',
      if(toppivotmode){
        gridApi.setGridOption("pivotMode", true);
      }

      console.log(gridApi.getState());
    });
  }

  function dynamicallyConfigureColumnsFromObject(anObject) {
    const colDefs = gridApi.getColumnDefs();
    colDefs.length = 0;
    // Get keys and create column definitions
    // colDefs.push({headerName: "Checkbox Cell Editor",field:"Checkbox",cellEditor:"agCheckboxCellEditor",cellRenderer: 'agCheckboxCellRenderer',})
    const keys = Object.keys(anObject);
    keys.forEach(key => {
        let filter;
        const dataType = typeof anObject[key];
        if(dataType=="string")
          filter='agMultiColumnFilter'
        else if(dataType=="number")
          filter='agNumberColumnFilter'
        else if(dataType=="date")
          filter ='agDateColumnFilter'
        // if conditon for displayin checkbox using checkboxSelection: true,showDisabledCheckboxes: true,
        if(colDefs.length==0)
          colDefs.push({ field: key, filter:filter ,checkboxSelection: true,showDisabledCheckboxes: true,});
        // for all other columns
        else
          colDefs.push({ field: key, filter:filter });
        
    });
    
    // Set the new column definitions to the grid
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