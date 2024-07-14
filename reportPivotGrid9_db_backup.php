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
<link rel="stylesheet" href="	https://cdn3.devexpress.com/jslib/23.2.4/css/dx.light.css">
<script type="text/javascript" src="https://cdn3.devexpress.com/jslib/23.2.4/js/dx.all.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.1.1/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

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

    .currency {
    text-align: center;
    }

  </style>
  <!-- <?php echo $_SESSION['user_id'];?> -->
  <div class="dx-viewport demo-container">
    <div id="pivotgrid-demo">
      <div id="sales">
      </div>
      <div id="sales-popup"></div>
      <div id="pivotgrid-chart">
      </div>
      <div id="pivotgrid">
      </div>
    </div>
  </div>
  </div>
  <!-- <script src="data.js"></script> -->
  <script>
  const pivotGridChart = $('#pivotgrid-chart').dxChart({
    commonSeriesSettings: {
      type: 'bar',
    },
    size: {
      height: 320,
    },
    adaptiveLayout: {
      width: 450,
    },
  }).dxChart('instance');

  $(() => {
    let drillDownDataSource = {};
        const exportHeaderOptions = {
        exportRowFieldHeaders: true,
        exportColumnFieldHeaders: false,
        exportDataFieldHeaders: false,
        exportFilterFieldHeaders: false,

      };

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
    export: {
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
    fieldChooser: {
      height: 500,
    },
    scrolling: {
      mode: 'virtual',
    },
        onCellClick(e) {
      if (e.area === 'data') {
        const pivotGridDataSource = e.component.getDataSource();
        const rowPathLength = e.cell.rowPath.length;
        const rowPathName = e.cell.rowPath[rowPathLength - 1];
        const popupTitle = `${rowPathName || 'Total'} Drill Down Data`;

        drillDownDataSource = pivotGridDataSource.createDrillDownDataSource(e.cell);
        console.log("on cell click drilldowndatasource");
        console.log(drillDownDataSource);
        salesPopup.option('title', popupTitle);
        salesPopup.show();
      }
    },
    dataSource: {
      store: heet,
    },
     onExporting(e) {
      const workbook = new ExcelJS.Workbook();
      const worksheet = workbook.addWorksheet('Sheet');

      DevExpress.excelExporter.exportPivotGrid({
        component: e.component,
        worksheet,
      }).then(() => {
        workbook.xlsx.writeBuffer().then((buffer) => {
          saveAs(new Blob([buffer], { type: 'application/octet-stream' }), 'Sheet.xlsx');
        });
      });
    },

    // pivotgrid.print();
    onContextMenuPreparing: contextMenuPreparing,
  }).dxPivotGrid('instance');
 
  salesPivotGrid.bindChart(pivotGridChart, {
    dataFieldsDisplayMode: 'splitPanes',
    alternateDataFields: false,
    putDataFieldsInto: "series"
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
          columns: ['PartyName', 'BrokerName', 'Remark', ],
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

const heet= [
  {
      ID: 1,
      Reg: "GREY INWARD",
      EntryNo: "test123",
      EntryDate: "2023-12-21",
      ChlNo: "E-0000001",
      ChlDate: "2023-12-21",
      TrspPay: "Testing",
      Sender: 1,
      Broker: 4,
      Remark: "test",
      Total: 572,
      Meter: 10,
      Lumps: 60,
      CreatedAt: "2024-02-27 18:23:13",
      CreatedBy: 0,
      PartyName: "Kreon Solution",
      BrokerName: "Hiten Mehta"
    },
    {
      ID: 2,
      Reg: "Reg222",
      EntryNo: "",
      EntryDate: "0000-00-00",
      ChlNo: "E-0000002",
      ChlDate: "2024-02-12",
      TrspPay: "",
      Sender: 1,
      Broker: 1,
      Remark: "",
      Total: "1000",
      Meter: 0,
      Lumps: 0,
      CreatedAt: "2024-02-22 23:26:48",
      CreatedBy: "0",
      PartyName: "Kreon Solution",
      BrokerName: "John Dias"
    },
    {
      ID: 3,
      Reg: "s",
      EntryNo: "",
      EntryDate: "2023-01-09",
      ChlNo: "E-0000003",
      ChlDate: "2024-02-14",
      TrspPay: "Testing",
      Sender: 7,
      Broker: 18,
      Remark: "testing",
      Total: 0,
      Meter: 4000,
      Lumps: 40,
      CreatedAt: "2024-02-22 23:25:11",
      CreatedBy: 0,
      PartyName: "Test4",
      BrokerName: "Bhavin Patel"
    },
    {
      ID: 4,
      Reg: "test123",
      EntryNo: "test123",
      EntryDate: "2024-02-19",
      ChlNo: "E-0000004",
      ChlDate: "2024-02-19",
      TrspPay: "",
      Sender: 2,
      Broker: 2,
      Remark: "test",
      Total: 0,
      Meter: 0,
      Lumps: "",
      CreatedAt: "2024-02-22 23:25:52",
      CreatedBy: 0,
      PartyName: "Bharat Textile",
      BrokerName: "Jitesh Jain",
    },
    {
      ID: 5,
      Reg: 22,
      EntryNo: 0,
      EntryDate: "2024-10-04",
      ChlNo: "E-0000005",
      ChlDate: "2024-02-20",
      TrspPay: "s",
      Sender: 2,
      Broker: 2,
      Remark: "ss",
      Total: 0,
      Meter: 4300,
      Lumps: 40,
      CreatedAt: "2024-02-22 23:25:55",
      CreatedBy: 0,
      PartyName: "Bharat Textile",
      BrokerName: "Jitesh Jain",
    },
    {
      ID: 6,
      Reg: "test11",
      EntryNo: "",
      EntryDate: "0000-00-00",
      ChlNo: "E-0000006",
      ChlDate: "2024-02-21",
      TrspPay: "dfd",
      Sender: 7,
      Broker: 0,
      Remark: "sgsd",
      Total: 0,
      Meter: 400,
      Lumps: 48,
      CreatedAt: "2024-02-26 19:44:49",
      CreatedBy: 0,
      PartyName: "Test4",
      BrokerName: null,
    },
    {
      ID: "7",
      Reg: "",
      EntryNo: "",
      EntryDate: "0000-00-00",
      ChlNo: "E-0000007",
      ChlDate: "2024-02-27",
      TrspPay: "",
      Sender: 0,
      Broker: 0,
      Remark: "",
      Total: 0,
      Meter: 0,
      Lumps: null,
      CreatedAt: "2024-02-27 11:30:13",
      CreatedBy: 0,
      PartyName: null,
      BrokerName: null
    },
    {
      ID: 8,
      Reg: "",
      EntryNo: "",
      EntryDate: "0000-00-00",
      ChlNo: "E-0000008",
      ChlDate: "2024-02-27",
      TrspPay: "",
      Sender: 0,
      Broker: 0,
      Remark: "",
      Total: 0,
      Meter: 0,
      Lumps: null,
      CreatedAt: "2024-02-27 11:56:12",
      CreatedBy: 0,
      PartyName: null,
      BrokerName: null
    }
  ]






</script>
<?php include("k_files/k_footer.php"); ?>