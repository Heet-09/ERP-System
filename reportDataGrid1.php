<?php
$ReportID = isset($_REQUEST['ReportID']) ? $_REQUEST['ReportID'] : 0;
$k_head_title = "ReportDataGrid";
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

<html>
<head>
<!-- ... -->
<!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->

<!-- <link rel="stylesheet" href="https://cdn3.devexpress.com/jslib/23.2.4/css/dx.light.css"> -->
<!-- <link rel="stylesheet" href="index.css"> -->

<!-- <script type="text/javascript" src="https://cdn3.devexpress.com/jslib/23.2.4/js/dx.all.js"></script> -->
<!-- <script type="text/javascript" src="index.js"></script> -->
</head>
<body class="dx-viewport">
    <style>
        #dataGrid {
            height: 500px;
        }
    </style>
    <div id="dataGrid">HIII</div>
    <script>    
        $(function () {
            // Fetch data from the URL
             fetch("reportGetDataFromView.php?ViewName=<?php echo $ReportID; ?>")
            .then((response) => response.json())
            .then((dataObject) => {
                // Check if dataObject is an object and has 'data' property
                if (typeof dataObject === "object" && dataObject.hasOwnProperty("data")) {
                    const dataArray = dataObject.data; // Extract the 'data' array
                    console.log("Fetched data:", dataArray); // Log the fetched data to console for inspection

                    $("#dataGrid").dxDataGrid({
                        dataSource: dataArray,
                        keyExpr: "ID",
                            // columns: [
                            //     { dataField: "id" },
                            //     { dataField: "title" },
                            //     { dataField: "price" },
                            //     { dataField: "rating" },
                            //     { dataField: "stock" },
                            //     { dataField: "discountprecentage" }
                            // ],
                        showBorders: true,
                        filterRow: {
                            visible: true,
                            applyFilter: "auto"
                        },
                        searchPanel: {
                            visible: true,
                            width: 240,
                            placeholder: "Search..."
                        },
                        paging: {
                            pageSize: 10
                        },
                         columnChooser: {
                             enabled: true,
                            // mode: "dragAndDrop" // or "select"
                        },
                         stateStoring: {
                            enabled: true,
                            type: "custom",
                            customLoad: function () {
                            // console.log("loading:", gridState);
                            return sendStorageRequest("organisatieKey", "json", "GET");
                        },
                            customSave: function (gridState) {
                            console.log(gridState);
                            return sendStorageRequest("organisatieKey", "text", "PUT", gridState);
                        },
                    }});
                } 
                else {
                    console.error("Error: Invalid data format or missing 'data' array.");
                }
            })
            .catch((error) => {
                console.error("Error fetching data:", error);
            });
        });

        function sendStorageRequest(key, datatype, type, data) {
            var deferred = $.Deferred();
            if(data !== undefined)
                var d = JSON.stringify(data);
            else
                var d = "";
            var storageRequestSettings = {
                url:
                    "reportPivotGridDataStorage.php?ReportID=<?php echo $ReportID; ?>&data=" + d,
                    // key,
                    headers: {
                    Accept: "text/html",
                    "Content-Type": "text/html",
                },
                type: type,
                dataType: datatype,
                success: function (data) {
                console.log("Success");
                console.log(data);
      deferred.resolve(data);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      deferred.reject();
    },
  };
  if (data) {
    console.log("SENDING...");
    storageRequestSettings.data = JSON.stringify(data); 
  }else{
    console.log("RECEIVING...");
  }
  $.ajax(storageRequestSettings);
  return deferred.promise();
}
    </script>
</body>
<!-- </html> -->
