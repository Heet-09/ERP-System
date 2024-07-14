<!DOCTYPE html>
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
<html>
<head>
   
</head>
<body class="dx-viewport">
    <div id="pivotGrid"></div>
    <style>
        #pivotGrid {
          height: 70vh;
        }
    </style>

  <script>
        

$(function () {
  // Fetch data from the URL    http://localhost/kreonprimary2/reportGetDataFromView.php?ViewName=1
  fetch("reportGetDataFromView.php?ViewName=<?php echo $ReportID; ?>")
    .then((response) => response.json())
    .then((dataObject) => {
      // Check if dataObject is an object and has 'products' property
      if (
        typeof dataObject === "object" &&
        dataObject.hasOwnProperty("data")
      ) {
        const dataArray = dataObject.data; // Extract the 'data' array
        console.log("Fetched data:", dataArray); // Log the fetched data  to console for inspection

        $("#pivotGrid").dxPivotGrid({
          dataSource: {
            // fields: [
            //   {
            //     dataField: "id",
            //     area: "row",
            //   },
            //   {
            //     dataField: "title",
            //     area: "column",
            //   },
            //   {
            //     dataField: "description",
            //     area: "column",
            //   },
            //   {
            //     dataField: "rating",
            //     area: "data",
            //   },
            //   {
            //     dataField: "price",
            //     area: "data",
            //     caption: "Price",
            //     dataType: "number",
            //     summaryType: "sum",
            //     format: "currency",
            //   },
            //   {
            //     dataField: "stock",
            //     area: "data",
            //     caption: "Stock",
            //     dataType: "number",
            //     summaryType: "sum",
            //   },
            // ],
            store: {
              type: "array",
              data: dataArray,
            },
          },
          fieldPanel: {
            visible: true,
            showFilterFields: true,
          },
          fieldChooser: {
            allowSearch: true,
          },
          allowSorting: true,
          allowSortingBySummary: true,
          export: { //pdf, print, excel/csv 
            enabled: true,
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
          },
        });
      } else {
        console.error(
          "Error: Invalid data format or missing data."
        );
      }
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });
});

// $(function () {
//   // Fetch data from the URL
//   fetch("reportGetDataFromView.php?ViewName=<?php echo $ReportID; ?>")
//     .then((response) => response.json())
//     .then((dataObject) => {
//       // Check if dataObject is an object and has 'data' property
//       if (
//         typeof dataObject === "object" &&
//         dataObject.hasOwnProperty("data")
//       ) {
//         const dataArray = dataObject.data; // Extract the 'data' array
//         console.log("Fetched data:", dataArray); // Log the fetched data  to console for inspection

//         $("#pivotGrid").dxPivotGrid({
//           dataSource: {
//           // fields: [
//           //   // Row fields
//           //   {
//           //     dataField: Meter,
//           //     area: "row",
//           //   },
//           //   // Column fields
//           //   {
//           //     dataField: Lumps,
//           //     area: "column",
//           //   }
//           // ],
//             store: {
//               type: "array",
//               data: dataArray,
//             },

//           },
//           // Define fields for row, column, and data

//           fieldPanel: {
//             visible: true,
//             showFilterFields: true,
//           },
//           fieldChooser: {
//             allowSearch: true,
//           },
//           allowSorting: true,
//           allowSortingBySummary: true,
//           export: {
//             enabled: true,
//           },
//           onContextMenuPreparing: contextMenuPreparing,
//           stateStoring: {
//             enabled: true,
//             type: "custom",
//             customLoad: function () {
//               return sendStorageRequest("organisatieKey", "json", "GET");
//             },
//             customSave: function (gridState) {
//               console.log(gridState);
//               return sendStorageRequest("organisatieKey", "text", "PUT", gridState);
//             },
//           },
//         });
//       } else {
//         console.error(
//           "Error: Invalid data format or missing data."
//         );
//       }
//     })
//     .catch((error) => {
//       console.error("Error fetching data:", error);
//     });
// });



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
      }

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


    </script>
</body>
</html>
?>