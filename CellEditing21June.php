<div id="myGrid" style="width: 100%; height: 100%" class="ag-theme-quartz"></div>
<script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise/dist/ag-grid-enterprise.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>

  let gridApi;
  const columnDefs = [];
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
      editable: true,
    },
    autoGroupColumnDef: {
      minWidth: 180,
    },
    onCellValueChanged: onCellValueChanged // Reference to the function
  };

  // Fetch data and initialize grid
  // fetch("reportGetDataFromView.php?ViewName=2")
  // fetch("https://dummy.kreonsolutions.in/api/form.php?FormID=1")
    .then(function (response) {
      return response.json();
    }).then(function (data) {
      console.log(data.code);
      dynamicallyConfigureColumnsFromObject(data[0]);
      gridApi.setGridOption('rowData', data);
    });

  function dynamicallyConfigureColumnsFromObject(anObject) {
    const colDefs = gridApi.getColumnDefs();
    colDefs.length = 0;
    
    const keys = Object.keys(anObject);
    keys.forEach(key => {
      let filter;
      let cellEditor;
      const dataType = typeof anObject[key];
      if (dataType == "string") {       
        filter = 'agMultiColumnFilter';
        cellEditor = "agTextCellEditor";
        if (dateChecker(anObject[key]) === true) {
          cellEditor = 'agDateCellEditor';
        }
      } else if (dataType == "number") {
        filter = 'agNumberColumnFilter';
        cellEditor = 'agNumberCellEditor';
      } else if (dataType == "date") {
        filter = 'agDateColumnFilter';
        cellEditor = 'agDateCellEditor';
      }
      if (key == "url") {
        colDefs.push({ field: key, filter: filter, cellRenderer: CompanyLogoRenderer, cellEditor: cellEditor });
      } else {
        if (key == "country") {
          colDefs.push({ field: key, filter: filter, cellRenderer: CountryRenderer, cellEditor: "agRichSelectCellEditor", cellEditorParams: { cellRenderer: CountryRenderer, values: ["Great Britain", "Norway", "Canada"] } });
        } else if (key == "gender") {
          colDefs.push({ field: key, filter: filter, cellEditor: 'agSelectCellEditor', cellEditorParams: { values: ['Male', 'Female'] } });
        } else {
          colDefs.push({ field: key, filter: filter, cellEditor: cellEditor });
        }
      }
    });

    // Set the new column definitions to the grid
    gridApi.setGridOption('columnDefs', colDefs);
    console.log("Cell Instances", gridApi.getCellEditorInstances());
  }

  function dateChecker(str) {
    return str.split('-').length - 1 === 2;
  }

  function onCellValueChanged(event) {
    console.log(event.data);

    // Extract values from event.data
    const { ID, Reg, EntryNo } = event.data;

    // Call the function to send data to the backend
    getDataFromAPI(ID, Reg, EntryNo);
  }

  // function sendStorageRequestSaveAs(ID, Reg, EntryNo) {
  //   console.log(ID, Reg, EntryNo);
  //   alert("sendStorage");
  //   $.post(
  //     'CellEditDataStorage.php', 
  //     {
  //       ID: ID,
  //       Reg: Reg,
  //       EntryNo: EntryNo
  //     },
  //     function(returnedData) {
  //       console.log(returnedData);
  //     }, 
  //     'json'
  //   )
  //   .fail(function() {
  //     console.log("error");
  //   });
  // }

//   function sendStorageRequestSaveAs(ID, Reg, EntryNo) {
//   console.log(ID, Reg, EntryNo);
//   alert("sendStorage");
//   $.post(
//     'CellEditDataStorage.php', 
//     {
      
//       ID: ID,
//       Reg: Reg,
//       EntryNo: EntryNo
//     },
//     function(returnedData) {
//       console.log(returnedData);
//     }, 
//     'json'
//   )
//   .fail(function(jqXHR, textStatus, errorThrown) {
//     console.log("Error details:");
//     console.log("Text Status: " + textStatus);
//     console.log("Error Thrown: " + errorThrown);
//     console.log("Response Text: " + jqXHR.responseText);
//   });
// }

function getDataFromAPI(ID, Reg, EntryNo) {
    $.ajax({
      url: `CellEditStorage.php?ID=${ID}&Reg=${Reg}&EntryNo=${EntryNo}`,
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
        // callback(new Error(textStatus || errorThrown));
        console.log(errorThrown);
      }
    });
  }

  gridApi = agGrid.createGrid(document.querySelector("#myGrid"), gridOptions);
</script>
