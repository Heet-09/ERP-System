<!DOCTYPE html>
<html lang="en">
    <?php
$ReportID = isset($_REQUEST['ReportID']) ? $_REQUEST['ReportID'] : 0;
$k_head_title = "ReportPivotGrid";
$k_head_include = "";
include "report-init.php";

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
    $url = "https://";   
else  
    $url = "http://";  
$url .= $_SERVER['HTTP_HOST'];   
$url .= $_SERVER['REQUEST_URI'];
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

foreach($_REQUEST as  $name => $value){
    if($name != "ReportID" && $name != "view"){
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
// echo "jeet";
$viewResult = db::getInstance()->db_select($sql);
?>
  <head>
    <title>JavaScript Example - Deep Dive - Updating Example</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="noindex" />
    <link
      href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;700&amp;display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community@31.3.2/dist/ag-grid-community.js?t=1715777153731"></script>
    <style media="only screen">
      :root,
      body {
        height: 100%;
        width: 100%;
        margin: 0;
        box-sizing: border-box;
        -webkit-overflow-scrolling: touch;
      }

      html {
        position: absolute;
        top: 0;
        left: 0;
        padding: 0;
        overflow: auto;
        font-family: -apple-system, "system-ui", "Segoe UI", Roboto,
          "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif,
          "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol",
          "Noto Color Emoji";
      }

      body {
        padding: 16px;
        overflow: auto;
        background-color: transparent;
      }

      /* .ag-paging-panel{
        padding-right:105px;
      } */

      .rag-green {
        background-color: #33cc3344;
      }

    </style>
  </head>
  <body> 
    <div id="myGrid" style="width: 1380px; height: 590px" class="ag-theme-quartz"></div>
    <script>
      // Grid API: Access to Grid API methods
      let gridApi;

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

      // Grid Options: Contains all of the grid configurations
      const gridOptions = {
        defaultColDef: {
          filter: true,
          editable: true,
          rowSelection: true,
          enableRowGroup: true,
          enablePivot: true,
          enableValue: true,
        },
        sideBar:true,
        pagination: true,

        onCellValueChanged: (event) => {
          console.log(`New Cell Value: ${event.value}`);
        },
        onSelectionChanged: event => {
          // console.log(`selection changed:${event.value}`);
        },
        // Data to be displayed
        rowData: [],
        // Columns to be displayed (Should match rowData properties)
        columnDefs: [
          // { headerName: "Make & Model", valueGetter: p => p.make + ' ' + p.model},
          {
            field: "mission",
            flex:2,
            valueFormatter: (params) => {
              return "" + params.value.toLocaleString();
            },
            checkboxSelection: true,
          
          },
          {
            field: "company",
            flex:1,
            cellRenderer: CompanyLogoRenderer,
          },
          { field: "location" },
          { field: "date" },
          {
            field: "price",
            // valueFormatter: (params) => {
            //   return "$" + params.value.toLocaleString();
            // },
            // filter:"agNumberColumnFilter"
          },
          { field: "successful" },
          { field: "rocket",
            cellClassRules: {
            // apply green to electric cars
            'rag-green': params => params.value === true,
        }

           },
        ],
      };

      // Create Grid: Create new grid within the #myGrid div, using the Grid Options object
      gridApi = agGrid.createGrid(document.querySelector("#myGrid"), gridOptions);

      // Fetch Remote Data
      fetch("https://www.ag-grid.com/example-assets/space-mission-data.json")
      .then((response) => response.json())
      .then((data) => gridApi.setGridOption("rowData", data));

    </script>
  </body>
<?php include("k_files/k_footer.php"); ?>