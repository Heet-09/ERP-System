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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->
 
    <!-- DevExtreme theme -->
    <!-- <link rel="stylesheet" href="https://cdn3.devexpress.com/jslib/23.2.5/css/dx.light.css"> -->
 
    <!-- DevExtreme libraries (reference only one of them) -->
    <!-- <script type="text/javascript" src="https://cdn3.devexpress.com/jslib/23.2.5/js/dx.all.js"></script> -->
    <!-- <script type="text/javascript" src="https://cdn3.devexpress.com/jslib/23.2.5/js/dx.web.js"></script> -->
    <!-- <script type="text/javascript" src="https://cdn3.devexpress.com/jslib/23.2.5/js/dx.viz.js"></script> -->
    <title>Document</title>
</head>
<body>
    <style>
        #sales {
  max-height: 570px;
}

.options {
  padding: 20px;
  margin-top: 20px;
  background-color: rgba(191, 191, 191, 0.15);
}

.caption {
  font-size: 18px;
  font-weight: 500;
}

.option {
  width: 24%;
  display: inline-block;
  margin-top: 10px;
}

    </style>
    <div class="dx-viewport demo-container">
  <div id="sales"></div>
  <div class="options">
    <div class="caption">Options</div>
    <div class="option">
      <div id="show-data-fields"></div>
    </div>
    <div class="option">
      <div id="show-row-fields"></div>
    </div>
    <div class="option">
      <div id="show-column-fields"></div>
    </div>
    <div class="option">
      <div id="show-filter-fields"></div>
    </div>
  </div>
</div>
<script src="data.js"></script>
<script>
    $(() => {
  const salesPivotGrid = $('#sales').dxPivotGrid({
    allowSortingBySummary: true,
    allowSorting: true,
    allowFiltering: true,
    height: 490,
    showBorders: true,
     rowHeaderLayout: 'tree',
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
      headerFilter:{ 
      showRelevantValues: false,
      width: 300,
      height: 400,},
    },
    stateStoring: {
      enabled: true,
      type: 'localStorage',
      storageKey: 'dx-widget-gallery-pivotgrid-storing',
    },
    fieldChooser: {
      height: 500,
    },
    scrolling: {
      mode: 'virtual',
    },

    dataSource: {
      fields: [{
        caption: 'Meter',
        width: 120,
        dataField: '',
        area: 'row',
      }, {
        caption: 'City',
        dataField: 'city',
        width: 150,
        area: 'row',
        selector(data) {
          return `${data.city} (${data.country})`;
        },
      }, {
        dataField: 'date',
        dataType: 'date',
        area: 'column',
      }, {
        dataField: 'sales',
        dataType: 'number',
        summaryType: 'sum',
        format: 'currency',
        area: 'data',
      }],
      store: sales,
    },
    onContextMenuPreparing: contextMenuPreparing,
  }).dxPivotGrid('instance');

  /*$('#show-data-fields').dxCheckBox({
    text: 'Show Data Fields',
    value: true,
    onValueChanged(data) {
      salesPivotGrid.option('fieldPanel.showDataFields', data.value);
    },
  });

  $('#show-row-fields').dxCheckBox({
    text: 'Show Row Fields',
    value: true,
    onValueChanged(data) {
      salesPivotGrid.option('fieldPanel.showRowFields', data.value);
    },
  });

  $('#show-column-fields').dxCheckBox({
    text: 'Show Column Fields',
    value: true,
    onValueChanged(data) {
      salesPivotGrid.option('fieldPanel.showColumnFields', data.value);
    },
  });

  $('#show-filter-fields').dxCheckBox({
    text: 'Show Filter Fields',
    value: true,
    onValueChanged(data) {
      salesPivotGrid.option('fieldPanel.showFilterFields', data.value);
    },
  });*/

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
  
});







</script>
</body>
</html>