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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>

<style>
  .wrapper {
      display: flex;
      flex-direction: column;
      height: 100%;
  }

  #myGrid {
      flex: 1 1 auto;
        /* width: 1380px; */
      height:595px;
  }

  .my-chart {
    flex: 1 1 auto; 
    height: 100px;
    /* width: 100px; */
  }

  #pdf{
    width:100px
  }

  
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
  let rowGroupColumns;

 
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
        $('.pivotgrid-div').html('<div class="wrapper"> <button id="pdf" onClick="exportToPDF(gridApi)">Export to PDF</button><div id="myGrid" class="ag-theme-balham my-grid"></div><div id="myChart" class="ag-theme-quartz my-chart"></div></div>');
        // $('.pivotgrid-div').html('<div>   <div id="myGrid" style="width: 1380px; height: 590px" class="ag-theme-balham"></div></div>');
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
        $('.pivotgrid-div').html('<div class="wrapper"> <button id ="pdf" onClick="exportToPDF(gridApi)">Export to PDF</button><div id="myGrid" class="ag-theme-balham my-grid"></div><div id="myChart" class="ag-theme-quartz my-chart"></div></div>');
        // $('.pivotgrid-div').html('<div id="myGrid" style="width: 1380px; height: 590px" class="ag-theme-balham"></div>');
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
                  tempName="";
                  modal_templateId=0;
                  templateDescription = "";
                  call_for_template = true;
                  // console.log(call_for_template);
                  // console.log("sort model from modal ",gridApi.getFilterModel());
                  modalh = gridApi.getState();
                  modalj=gridApi.getColumnState();
                  pivotglobalstate = modalh;
                  modalh.columns=modalj;
                  console.log("template for saving",modalh);
                  // return sendStorageRequest("organisatieKey", "text", "PUT", modalh);
                  // sendStorageRequest("storageKey", "text", "PUT", gridApi.getState());
                  // if(tempName_for_modal){
                  //   modal_templateId=0;
                  // }
                  console.log("tmeplate descro in moda",templateDescription);
                  console.log("tmeplate name in moda",tempName);
                  return sendStorageRequestSaveAs(tempName_for_modal,"text","POSt",h,templateDescription,0)
                }
                </script>
              <!-- </form> -->
            </div>   
          </div>                                                                       
        </div>                                          
      </div>
      <!-- <div class="col-md-12">
      </div> -->

      <div class="col-md-12" id="groupColumnIdsDiv">

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
    console.log("templatId",templateId);
    console.log("templateDescription",templateDescription);
    if(templateDescription){
      console.log("inco");
    }
    return sendStorageRequestSaveAs(tempName,"text","POST",h,templateDescription,templateId)
    alert("after send storeage in sendStorage save as")
  }
  
  function getDataFromAPI(reportID, templateId, tempName, templateDescription, typeId, callback) {
    $.ajax({
      url: `AgGridReportDataStorage.php?ReportID=${reportID}&templateId=${templateId}&tempName=${tempName}&templateDescription=${templateDescription}&typeID=${typeId}`,
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json"
      },
      type: "POST",
      dataType: "json",
      success: function(data) {
        callback(null, data);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        callback(new Error(textStatus || errorThrown));
        console.log(errorThrown);
      }
    });
  }  

  function sendStorageRequestSaveAs(tempName, datatype, type, data,templateDescription, templateId) {
    console.log(tempName+""+datatype+""+type+""+data+""+templateDescription+""+templateId);
    alert("sendStorage")
    $.post(
      'AgGridReportDataStorage.php', 
      { ReportID:<?php echo $ReportID; ?>,templateId:templateId,tempName:tempName,templateDescription:templateDescription,typeID:1,data: JSON.stringify(data)} ,
      function(returnedData){
          console.log(returnedData);
      }, 
      'json')
      .fail(function(){
          console.log("error");
      });
    // return;
  }
  
  // class CompanyLogoRenderer {
  //   eGui;
  
  //   // Optional: Params for rendering. The same params that are passed to the cellRenderer function.
  //   init(params) {
  //     console.log("params",params);
  //     // alert("llllll ")
  //     let companyLogo = document.createElement('img');
  //     companyLogo.src = `${params.value}`
  //     console.log(companyLogo.src);
  //     // companyLogo.src = `https://images.pexels.com/photos/674010/pexels-photo-674010.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1`
  //     companyLogo.setAttribute('style', 'display: block; width: 25px; height: auto; max-height: 50%; margin-right: 12px; filter: brightness(1.1)');
  //     // let companyName = document.createElement('p');
  //     // companyName.textContent = params.value;
  //     // companyName.setAttribute('style', 'text-overflow: ellipsis; overflow: hidden; white-space: nowrap;');
  //     // Add click event to enlarge image     
  //       companyLogo.addEventListener('click', function() {
  //         if (companyLogo.style.transform === 'scale(10)') {
  //           companyLogo.style.transform = 'scale(1)';
  //           companyLogo.style.position = 'static';
  //           companyLogo.style.zIndex = '1';
  //         } else {
  //           companyLogo.style.transform = 'scale(10)';
  //           companyLogo.style.position = 'absolute';
  //           companyLogo.style.zIndex = '1000';
  //         }
  //       });
  //     this.eGui = document.createElement('span');
  //     this.eGui.setAttribute('style', 'display: flex; height: 100%; width: 100%; align-items: center')
  //     this.eGui.appendChild(companyLogo)
  //     // this.eGui.appendChild(companyName)
  //   }
    
  //   // Required: Return the DOM element of the component, this is what the grid puts into the cell
  //   getGui() {
  //     return this.eGui;
  //   }
    
  //   // Required: Get the cell to refresh.
  //   refresh(params) {
  //     return false
  //   }
  // }

  class CompanyLogoRenderer {
    eGui;
    modal;
    modalContent;
    modalImage;
    closeButton;

    init(params) {
      console.log("params", params);

      // Create an image element
      let companyLogo = document.createElement('img');
      companyLogo.src = `${params.value}`;
      console.log(companyLogo.src);
      
      companyLogo.setAttribute('style', 'display: block; width: 25px; height: auto; max-height: 50%; margin-right: 12px; filter: brightness(1.1); cursor: pointer;');

      // Add click event to show modal with enlarged image
      companyLogo.addEventListener('click', this.showModal.bind(this, companyLogo.src));

      this.eGui = document.createElement('span');
      this.eGui.setAttribute('style', 'display: flex; height: 100%; width: 100%; align-items: center');
      this.eGui.appendChild(companyLogo);

      this.createModal();
    }

    getGui() {
        return this.eGui;
    }

    refresh(params) {
        return false;
    }

    createModal() {
      // Create modal element
      this.modal = document.createElement('div');
      this.modal.setAttribute('style', 'display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.8); align-items: center; justify-content: center;');

      // Create modal content wrapper
      this.modalContent = document.createElement('div');
      this.modalContent.setAttribute('style', 'position: relative; display: flex; align-items: center;');

      // Create modal image
      this.modalImage = document.createElement('img');
      this.modalImage.setAttribute('style', 'max-width: 60%; max-height: 60%; margin: auto; display: block;');

      // Create close button
      this.closeButton = document.createElement('span');
      this.closeButton.innerHTML = '&times;';
      this.closeButton.setAttribute('style', 'color: white; font-size: 40px; font-weight: bold; cursor: pointer; margin-left: 20px; background-color: rgba(0, 0, 0, 0.5); padding: 10px; border-radius: 50%;');

      this.closeButton.addEventListener('click', this.hideModal.bind(this));

      this.modalContent.appendChild(this.modalImage);
      this.modalContent.appendChild(this.closeButton);
      this.modal.appendChild(this.modalContent);
      document.body.appendChild(this.modal);
    }

    showModal(src) {
      this.modalImage.src = src;
      this.modal.style.display = 'flex';
    }

    hideModal() {
      this.modal.style.display = 'none';
    }
  }


  const columnDefs = [];
  let pivotmode=false;

  const gridOptions = {
    columnDefs: columnDefs,
    defaultColDef: {
      flex: 1,
      minWidth: 150,
      enableRowGroup: true,
      enablePivot: true,
      enableValue: true,
      filter: true,
      floatingFilter: true,
      // suppressSizeToFit: true,
    },
    enableCharts:true,
    enableRangeSelection: true,
    popupParent:document.body,
    sideBar:true,
    enableRangeSelection: true,
    groupHideOpenParents: true,
    // groupDisplayType: "multipleColumns",
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
      // filter: 'agGroupColumnFilter', `
      // cellRenderer:null
        // cellRenderer:"agGroupCellRenderer",
    },
    onGridReady:e=>{
      topg=0;
      // console.log("in Grid");
      if(tempName){ 
        getDataFromAPI(1, templateId, tempName, templateDescription, typeId, (error, data) => {
          if (error) {
            console.error('Error fetching data:', error);
          } else {
            pivotglobalsource=data;
            // console.log("After fetched",data);
          gridApi.applyColumnState({ 
            state: data.columns,
            applyOrder: true,
          });
          // console.log("toppivot in grid ready",toppivot);
            gridApi.setGridOption("pivot", toppivot);

          }
        });
      }
    },                                                      
    // onStateUpdated:event =>{
    //   topg++
    //   // console.log("State",gridApi.getState());
    //   // console.log("pivot state",gridApi.getPivotColumns());
    //   if(topg==3){
    //     if(toppivotmode){
    //       gridApi.setGridOption("pivotMode", true);
    //     }
    //     gridApi.applyColumnState({ 
    //       state: pivotglobalsource.columns,
    //       applyOrder: true,
    //     });
    //     gridApi.setFilterModel(topfilters);
    //   }
    //   let rowg = gridApi.getState();
    //   let groupColIds = rowg.rowGroup.groupColIds;

    //   // Ensure every column group's state is tracked
    //   groupColIds.forEach(id => {
    //     if (!checkboxState.some(([columnId]) => columnId === id)) {
    //       checkboxState.push([id, false]);  // Default to unchecked
    //     }
    //   });

    //   document.getElementById("groupColumnIdsDiv").innerHTML = generateCheckboxHTML(groupColIds);
    //   console.log("checkboxstate", checkboxState);

    //   addCheckboxListeners();
    // }
      onStateUpdated: event => {
    topg++;
    if (topg == 3) {
      if (toppivotmode) {
        gridApi.setGridOption("pivotMode", true);
      }
      gridApi.applyColumnState({
        state: pivotglobalsource.columns,
        applyOrder: true,
      });
      gridApi.setFilterModel(topfilters);
    }

    let rowg = gridApi.getState();
    let groupColIds = rowg.rowGroup.groupColIds;
    // console.log(groupColIds);

    // Initialize checkboxState for each column group
    groupColIds.forEach(id => {
      "log in for loop"
      if (!checkboxState.some(([columnId]) => columnId === id)) {
        checkboxState.push([id, false]); // Default to unchecked
      }
    });

      console.log("checkbox state",checkboxState);
    document.getElementById("groupColumnIdsDiv").innerHTML = generateCheckboxHTML(groupColIds);
    console.log("checkboxstate after update:", checkboxState);

    addCheckboxListeners();
  }

  };

    const checkboxState = [];


  function generateCheckboxHTML(groupColIds) {
    console.log("generate ggroupcoliDs",groupColIds);
  return groupColIds.map(id => `
    <div style="margin-right: 10px; display: inline-block;">
      <input type="checkbox" id="${id}" name="${id}" value="${id}" ${isChecked(id) ? 'checked' : ''}>
      <label for="${id}">${id}</label>
    </div>
  `).join('');
}

function isChecked(id) {
  console.log("isChecked called for id:", id);
  return checkboxState.some(([columnId, checked]) => columnId === id && checked);
}

function addCheckboxListeners() {
  console.log("adding checkbox listeners");
  const checkboxes = document.querySelectorAll('#groupColumnIdsDiv input[type="checkbox"]');
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', (event) => {
      updateCheckboxState(event.target.id, event.target.checked);
      console.log("checkboxState after change:", checkboxState);
    });
  });
}

function updateCheckboxState(id, checked) {
  console.log("updateCheckboxState called for id:", id, "checked:", checked);
  const index = checkboxState.findIndex(([columnId]) => columnId === id);
  if (index >= 0) {
    checkboxState[index][1] = checked;
  } else {
    checkboxState.push([id, checked]);
  }
  console.log("checkboxState updated:", checkboxState);
}            

  function loadPivotGridData() {
    // console.log("loadPivotGridData",count++);
    var gridDiv = document.querySelector("#myGrid");
    gridApi = agGrid.createGrid(gridDiv, gridOptions);
  
    // fetch("https://www.ag-grid.com/example-assets/space-mission-data.json")
    // fetch("https://jsonplaceholder.typicode.com/photos")
    // fetch("reportGetDataFromView.php?ViewName=2")
    fetch("https://www.ag-grid.com/example-assets/wide-spread-of-sports.json")
    .then(function (response) {
      return response.json();
    }).then(function (data) {
      dynamicallyConfigureColumnsFromObject(data[0])
      // gridApi.setRowData(data);
      gridApi.setGridOption('rowData',data)
      
      // cellRenderer: 'agCheckboxCellRenderer',
      //   cellEditor: 'agCheckboxCellEditor',
      if(toppivotmode){
        gridApi.setGridOption("pivotMode", true);
      }

      // console.log(gridApi.getState());
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
        console.log("key",key);
        const dataType = typeof anObject[key];
        if(dataType=="string")
          filter='agMultiColumnFilter'
        else if(dataType=="number")
          filter='agNumberColumnFilter'
        else if(dataType=="date")
          filter ='agDateColumnFilter'
        // if conditon for displayin checkbox using checkboxSelection: true,showDisabledCheckboxes: true,
        // if(colDefs.length==0)
        // colDefs.push({ field: key, filter:filter ,checkboxSelection: true,showDisabledCheckboxes: true,});
        // // else if(key=="Reg")
        if(key=="url")
          colDefs.push({ field: key, filter:filter,cellRenderer: CompanyLogoRenderer});
        // // for all other columns
        else
          colDefs.push({ field: key, filter:filter });
        
    });
    
    // Set the new column definitions to the grid
    // gridApi.setColumnDefs(colDefs);
    gridApi.setGridOption('columnDefs',colDefs)
  }
</script>


<!-- FOR PRINTINg -->
<script>
  function getHeaderToExport(gridApi) {
    var columns = gridApi.getAllDisplayedColumns();
    return columns.map(function(column) {
      var field = column.getColDef().field;
      var sort = column.getSort();
      var headerName = column.getColDef().headerName || field;
      var headerNameUppercase = headerName[0].toUpperCase() + headerName.slice(1);
      var headerCell = {
        text: headerNameUppercase + (sort ? " (" + sort + ")" : ""),
        bold: true,
        margin: [0, 12, 0, 0]
      };
      return headerCell;
    });
  }

  function getRowsToExportPivot(gridApi) {
    var columns = gridApi.getAllDisplayedColumns();
    var getCellToExport = function(column, node) {
      return {
        text: gridApi.getValue(column, node) || '',
        ...column.getColDef().cellStyle
      };
    };
    var rowsToExport = [];
    gridApi.forEachNodeAfterFilterAndSort(function(node) {
      if (node.group) {
        var rowToExport = columns.map(function(column) {
          return getCellToExport(column, node);
        });
        rowsToExport.push(rowToExport);
      }
    });
    return rowsToExport;
  }

  function getRowsToExport(gridApi) {
    if (gridApi.isPivotMode()) {
      return getRowsToExportPivot(gridApi);
    }
    var columns = gridApi.getAllDisplayedColumns();
    var getCellToExport = function(column, node) {
      return {
        text: gridApi.getValue(column, node) || '',
        ...column.getColDef().cellStyle
      };
    };
    var rowsToExport = [];
    gridApi.forEachNodeAfterFilterAndSort(function(node) {
      var rowToExport = columns.map(function(column) {
        return getCellToExport(column, node);
      });
      rowsToExport.push(rowToExport);
    });
    return rowsToExport;
  }

  var HEADER_ROW_COLOR = '#f8f8f8';
  var EVEN_ROW_COLOR = '#fcfcfc';
  var ODD_ROW_COLOR = '#fff';
  var PDF_INNER_BORDER_COLOR = '#dde2eb';
  var PDF_OUTER_BORDER_COLOR = '#babfc7';

  function createLayout(numberOfHeaderRows) {
    return {
      fillColor: function(rowIndex) {
        if (rowIndex < numberOfHeaderRows) {
          return HEADER_ROW_COLOR;
        }
        return rowIndex % 2 === 0 ? EVEN_ROW_COLOR : ODD_ROW_COLOR;
      },
      vLineWidth: function(rowIndex, node) {
        return rowIndex === 0 || rowIndex === node.table.widths.length ? 1 : 0;
      },
      hLineColor: function(rowIndex, node) {
        return rowIndex === 0 || rowIndex === node.table.body.length ? PDF_OUTER_BORDER_COLOR : PDF_INNER_BORDER_COLOR;
      },
      vLineColor: function(rowIndex, node) {
        return rowIndex === 0 || rowIndex === node.table.widths.length ? PDF_OUTER_BORDER_COLOR : PDF_INNER_BORDER_COLOR;
      }
    };
  }

  function getDocument(gridApi) {
    var columns = gridApi.getAllDisplayedColumns();
    var headerRow = getHeaderToExport(gridApi);
    var rows = getRowsToExport(gridApi);

    return {
      pageOrientation: 'landscape',
      content: [{
        table: {
          headerRows: 1,
          widths: Array(columns.length).fill('*'),
          body: [headerRow].concat(rows),
          heights: function(rowIndex) {
            return rowIndex === 0 ? 40 : 15;
          }
        },
        layout: createLayout(1)
      }],
      pageMargins: [10, 10, 10, 10]
    };
  }

  function exportToPDF(gridApi) {
    // alert("gridApi")
    var doc = getDocument(gridApi);
    // pdfMake.createPdf(doc).download();
     pdfMake.createPdf(doc).download(tempName || 'download.pdf');
  }

  // document.getElementById("pdf").addEventListener('click', function(e) {
  //   e.preventDefault();
  //   exportToPDF(gridApi);
  // });

  // document.querySelector("button[type='submit']").addEventListener('click', function(e) {
  //   e.preventDefault();
  //   alert("in export")
  //   exportToPDF(gridApi);
  // });

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