<!--  -->



<div id="myGrid" style="width: 100%; height: 100%" class="ag-theme-quartz"></div>
<script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise/dist/ag-grid-enterprise.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
<script>
    // Row Data Interface

// Grid API: Access to Grid API methods
let gridApi;

// Grid Options: Contains all of the grid configurations
// const gridOptions = {
//   // Data to be displayed
//   rowData: [
//     { make: "Tesla", model: "Model Y", price: 64950, electric: true },
//     { make: "Ford", model: "F-Series", price: 33850, electric: false },
//     { make: "Toyota", model: "Corolla", price: 29600, electric: false },
//     { make: "Mercedes", model: "EQA", price: 48890, electric: true },
//     { make: "Fiat", model: "500", price: 15774, electric: false },
//     { make: "Nissan", model: "Juke", price: 20675, electric: false },
//   ],
//   // Columns to be displayed (Should match rowData properties)
//   columnDefs: [
//     { field: "make" },
//     { field: "model" },
//     { field: "price" },
//     { field: "electric" },
//   ],
//   defaultColDef: {
//     flex: 1,
//     editable:true,
//   },
// };
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
      floatingFilter: true,editable: true,

    },
    
    // editing:true,
    
   
    autoGroupColumnDef: {
      minWidth: 180,
    },
    
  };

    fetch("https://jsonplaceholder.typicode.com/todos/")
    .then(function (response) {
      return response.json();
    }).then(function (data) {
      dynamicallyConfigureColumnsFromObject(data[0])
      // gridApi.setRowData(data);
      gridApi.setGridOption('rowData',data)
      
      // cellRenderer: 'agCheckboxCellRenderer',
      //   cellEditor: 'agCheckboxCellEditor',
      

      // console.log(gridApi.getState());
    });



      function dynamicallyConfigureColumnsFromObject(anObject) {
    const colDefs = gridApi.getColumnDefs();
    colDefs.length = 0;
    // Get keys and create column definitions
    // colDefs.push({headerName: "Checkbox Cell Editor",field:"Checkbox",cellEditor:"agCheckboxCellEditor",cellRenderer: 'agCheckboxCellRenderer',})
    const keys = Object.keys(anObject);
    keys.forEach(key => {
        let filter;
        // console.log("key",key);
        const dataType = typeof anObject[key];
        if(dataType=="string"){
            if(anObject[key].length >10){
                console.log("Can do it ")
            }
            console.log(anObject.[key])
          filter='agMultiColumnFilter'}
        else if(dataType=="number")
          filter='agNumberColumnFilter'
        else if(dataType=="date")
          filter ='agDateColumnFilter'
        // if conditon for displayin checkbox u   sing checkboxSelection: true,showDisabledCheckboxes: true,
        // if(colDefs.length==0)
        // colDefs.push({ field: key, filter:filter ,checkboxSelection: true,showDisabledCheckboxes: true,});
        // // else if(key=="Reg")
        if(key=="url")
          colDefs.push({ field: key, filter:filter,cellRenderer: CompanyLogoRenderer});
        // // for all other columns
        else
          if(key == "country")
            colDefs.push({ field: key, filter:filter,cellRenderer: CountryRenderer,cellEditor: "agRichSelectCellEditor",cellEditorParams: {cellRenderer: CountryRenderer,values: ["Great Britain","Norway","Canada"],}, });
          else
            colDefs.push({ field: key, filter:filter, });
        
    });
    
    // Set the new column definitions to the grid
    // gridApi.setColumnDefs(colDefs);
    gridApi.setGridOption('columnDefs',colDefs)
  }
// Create Grid: Create new grid within the #myGrid div, using the Grid Options object
gridApi = agGrid.createGrid(document.querySelector("#myGrid"), gridOptions);
</script>

