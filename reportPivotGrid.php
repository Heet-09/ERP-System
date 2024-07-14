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
  // Fetch data from the URL
  fetch("https://dummyjson.com/products")
    .then((response) => response.json())
    .then((dataObject) => {
      // Check if dataObject is an object and has 'products' property
      if (
        typeof dataObject === "object" &&
        dataObject.hasOwnProperty("products")
      ) {
        const productsArray = dataObject.products; // Extract the 'products' array
        console.log("Fetched products:", productsArray); // Log the fetched products to console for inspection

        $("#pivotGrid").dxPivotGrid({
          allowSortingBySummary: true,
          allowSorting: true, // Ensure general sorting is enabled
          allowFiltering: true,
          dataSource: {
            // fields: [
            //   { sortBySummaryField: 'price',},
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
            //     allowSorting: true, // Enable sorting for 'rating' field
            //   },
            //   {
            //     dataField: "price",
            //     area: "data",
            //     caption: "Price",
            //     dataType: "number",
            //     summaryType: "sum",
            //     format: "currency",
            //     allowSorting: true, // Enable sorting for 'price' field
            //   },
            //   {
            //     dataField: "stock",
            //     area: "data",
            //     caption: "Stock",
            //     dataType: "number",
            //     summaryType: "sum",
            //     allowSorting: true, // Enable sorting for 'stock' field
            //   },
            // ],
            store: {
              type: "array",
              data: productsArray,
            },
          },
          fieldPanel: {
            visible: true,
            showFilterFields: true,
          },
          fieldChooser: {
            allowSearch: true,
          },
          export: {
            enabled: true,
          },
          stateStoring: {
            enabled: true,
            type: "custom",
            customLoad: function () {
              return sendStorageRequest("organisatieKey", "json", "GET");
            },
            customSave: function (gridState) {
              return sendStorageRequest("organisatieKey", "text", "PUT", gridState);
            },
          },
        });
      } else {
        console.error(
          "Error: Invalid data format or missing 'products' array."
        );
      }
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });
});

// $(() => {
//   const pivotgrid = $('#pivotgrid').dxPivotGrid({
//     allowSortingBySummary: true,
//     allowSorting: true,
//     allowFiltering: true,
//     allowExpandAll: true,
//     showBorders: true,
//     onContextMenuPreparing: contextMenuPreparing,
//     height: 570,
//     fieldChooser: {
//       enabled: true,
//     },
//     fieldPanel: {
//       visible: true,
//     },
//     stateStoring: {
//       enabled: true,
//       type: 'localStorage',
//       storageKey: 'dx-widget-gallery-pivotgrid-storing',
//     },
//     dataSource: {
//       fields: [{
//         caption: 'Region',
//         width: 120,
//         dataField: 'region',
//         area: 'row',
//         sortBySummaryField: 'sales',
//       }, {
//         caption: 'City',
//         dataField: 'city',
//         width: 150,
//         area: 'row',
//       }, {
//         dataField: 'date',
//         dataType: 'date',
//         area: 'column',
//       }, {
//         groupName: 'date',
//         groupInterval: 'year',
//       }, {
//         groupName: 'date',
//         groupInterval: 'quarter',
//       }, {
//         dataField: 'sales',
//         dataType: 'number',
//         summaryType: 'sum',
//         format: 'currency',
//         area: 'data',
//       }],
//       store: sales,
//     },
//   }).dxPivotGrid('instance');

//   $('#reset').dxButton({
//     text: "Reset the PivotGrid's State",
//     onClick() {
//       pivotgrid.getDataSource().state({});
//     },
//   });

//   function contextMenuPreparing(e) {
//     const dataSource = e.component.getDataSource();
//     const sourceField = e.field;

//     if (sourceField) {
//       if (!sourceField.groupName || sourceField.groupIndex === 0) {
//         e.items.push({
//           text: 'Hide field',
//           onItemClick() {
//             let fieldIndex;
//             if (sourceField.groupName) {
//               fieldIndex = dataSource
//                 .getAreaFields(sourceField.area, true)[sourceField.areaIndex]
//                 .index;
//             } else {
//               fieldIndex = sourceField.index;
//             }

//             dataSource.field(fieldIndex, {
//               area: null,
//             });
//             dataSource.load();
//           },
//         });
//       }

//       if (sourceField.dataType === 'number') {
//         const setSummaryType = function (args) {
//           dataSource.field(sourceField.index, {
//             summaryType: args.itemData.value,
//           });

//           dataSource.load();
//         };
//         const menuItems = [];

//         e.items.push({ text: 'Summary Type', items: menuItems });

//         $.each(['Sum', 'Avg', 'Min', 'Max'], (_, summaryType) => {
//           const summaryTypeValue = summaryType.toLowerCase();

//           menuItems.push({
//             text: summaryType,
//             value: summaryType.toLowerCase(),
//             onItemClick: setSummaryType,
//             selected: e.field.summaryType === summaryTypeValue,
//           });
//         });
//       }
//     }
//   }
// });


function sendStorageRequest(key, datatype, type, data) {
  var deferred = $.Deferred();
  if(data !== undefined)
    var d = JSON.stringify(data);
  else
    var d = "";
  var storageRequestSettings = {
    url:
      "https://valiant.kreonsolutions.com/api/kreonerp_pivotGrid_storage.php?data=" + d,
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
    storageRequestSettings.data = JSON.stringify(data);
  }
  $.ajax(storageRequestSettings);
  return deferred.promise();
}
</script>

</body>
</html>
?>