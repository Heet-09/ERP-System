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

  let headers;
  let result; // Declare result globally

  fetch("https://dummy.kreonsolutions.in/api/edit.php?FormID=1&whereCon=%20family_details.Id%20%3E%200%20limit%2020")
  // fetch("https://dummy.kreonsolutions.in/api/edit.php?FormID=2&whereCon=%20user_id%20%3E%202525%20limit%2020")
    .then(response => response.json())
    .then(data => {
      // console.log(data);

      result = data.data.result_set; // Assign result to the global variable
      headers = data.header;
      
      // Check if result is an array and has at least one element
      if (Array.isArray(result) && result.length > 0) {
        // console.log(result[0]);
        // console.log(headers);
        
        // Configure columns based on the first object in the result set
        dynamicallyConfigureColumnsFromObject(result[0]);
        
        // Set row data for the grid
        gridApi.setGridOption('rowData', result);
      } else {
        console.error('Expected resultSet to be a non-empty array, but got:', result);
      }
    });

  
  

  
    function dynamicallyConfigureColumnsFromObject(anObject) {
    const colDefs = gridApi.getColumnDefs();
    colDefs.length = 0;

    // const uniqueValues = {};

    // // Collect unique values for columns that use select editors
    // headers.forEach(header => {
    //   const [key, secondValue] = header;
    //   uniqueValues[key] = new Set();
    // });
    
    // // console.log(uniqueValues)
    // // console.log(uniqueValues.bloodgroup)
    // // console.log(uniqueValues.nativeplace);
    
    // // Populate unique values from the result set
    // result.forEach(row => {
    //   Object.keys(row).forEach(key => {
    //     // Initialize the Set if it doesn't exist for the key
    //     if (!uniqueValues[key]) {
    //       uniqueValues[key] = new Set();
    //     }
        
    //     // Add value to Set only if it's not "-"
    //     if (row[key] !== "-") {
    //       uniqueValues[key].add(row[key]);
    //     }
    //   });
    // });


    const uniqueValues = {};

    // Collect unique values for columns that use select editors
    headers.forEach(header => {
      const [key, secondValue] = header;
      uniqueValues[key] = {
        map: new Map(),
        counter: 1 // Initialize a counter for each key
      };
    });

    // Populate unique values from the result set
    result.forEach(row => {
      Object.keys(row).forEach(key => {
        // Ensure the key exists in uniqueValues (handles the case where a key might not be in headers)
        if (!uniqueValues[key]) {
          uniqueValues[key] = {
            map: new Map(),
            counter: 1
          };
        }

        const value = row[key];

        // Add value to Map only if it's not "-" and not already present
        if (value !== "-" && !uniqueValues[key].map.has(value)) {
          uniqueValues[key].map.set(value, uniqueValues[key].counter);
          uniqueValues[key].counter += 1; // Increment counter for the next unique value
        }
      });
    });

    // Example of how to access the unique values map
    console.log(uniqueValues.bloodgroup.map);
    console.log(uniqueValues.nativeplace.map);
    const keys = Object.keys(anObject);

    keys.forEach(key => {
      const header = headers.find(header => header[0] === key);
      if (!header) return;

      const secondValue = header[1];
      let cellEditor;
      let filter;
      let cellEditorParams;

      if (secondValue === "6") {
      anObject[key]=anObject[key].replaceAll("-","/");
      // console.log(anObject[key])
      cellEditor = 'agDateCellEditor';
        filter = 'agDateColumnFilter';
      } 
      else if (secondValue === "4" || secondValue === "5") {
    cellEditor = 'agSelectCellEditor';
    // Extract the keys from the Map (unique values)
    cellEditorParams = { values: Array.from(uniqueValues[key].map.keys()) };
    console.log(Array.from(uniqueValues[key].map.keys()));
    
    filter = 'agSetColumnFilter';
    } else if (secondValue === "11") {
      cellEditor = 'agRichSelectCellEditor';
      // Extract the keys from the Map (unique values)
      cellEditorParams = { values: Array.from(uniqueValues[key].map.keys()) };
      filter = 'agMultiColumnFilter';
    }

      // else if (secondValue === "4" || secondValue === "5") {
      //   cellEditor = 'agSelectCellEditor';
      //   cellEditorParams = { values: Array.from(uniqueValues[key]) };
      //   filter = 'agSetColumnFilter';
      // } else if (secondValue === "11") {
      //   cellEditor = 'agRichSelectCellEditor';
      //   cellEditorParams = { values: Array.from(uniqueValues[key]) };
      //   filter = 'agMultiColumnFilter';
      // }

       else if (secondValue === "8") {
        cellEditor = 'agTextCellEditor';
        filter = 'agTextColumnFilter';
      } else if (secondValue === "9") {
        field: "boolean",
        cellEditor = 'agCheckboxCellEditor';
        filter = 'agMultiColumnFilter';
        // console.log(cellEditor);
        
      } else if (secondValue === "10") {
        cellEditor = 'agRichSelectCellEditor';
        cellEditorParams = { values: Array.from(uniqueValues[key]) };
        filter = 'agMultiColumnFilter';
      } else if(secondValue === "7"){
        cellEditor='agNumberCellEditor';
        filter='agNumberColumnFilter'
       
      }else if (secondValue === "1" || !secondValue) {
        cellEditor = 'agTextCellEditor';
        filter = 'agMultiColumnFilter';
      }
      
    
      if (cellEditor) {
        if(cellEditor=='agCheckboxCellEditor'){
          colDefs.push({ field: key, filter: filter, cellEditor: cellEditor, cellRenderer: 'agCheckboxCellRenderer', cellDataType: 'boolean', cellEditorParams: cellEditorParams });
          // console.log("kkkkk");
          
        }
        else
          colDefs.push({ field: key, filter: filter, cellEditor: cellEditor, cellEditorParams: cellEditorParams });
      } else {
        colDefs.push({ field: key, filter: filter, cellEditor: cellEditor});
      }
    });

    // Set the new column definitions to the grid
    gridApi.setGridOption('columnDefs', colDefs);
    // console.log("Cell Instances", gridApi.getCellEditorInstances());
  }


  function dateChecker(str) {
    return str.split('-').length - 1 === 2;
  }

  function onCellValueChanged(event) {
    console.log("mm",event.data);
    console.log("mm",event.data.Id);
    // Create an object to store the key-value pairs for dynamic fields
    const dynamicParams = {};
    const idValue = event.data.Id;

    // Loop through the headers to map event.data values
    headers.forEach(header => {
        const fieldName = header[0]; // e.g., 'name', 'username', 'password'
        if (fieldName !== 'ID' && event.data[fieldName] !== undefined) {
            dynamicParams[fieldName] = event.data[fieldName];
        }
    });

    // Call the function to send data to the backend
    sendStorageRequestSaveAs(idValue,dynamicParams);
    console.log(idValue);
    
  }

  function sendStorageRequestSaveAs(idValue,dynamicParams) {
      alert("sendStorage");

      // Construct the query parameters dynamically
      const queryString = Object.keys(dynamicParams)
          .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(dynamicParams[key])}`)
          .join('&');

      // const url = `https://dummy.kreonsolutions.in/api/appdb.php?FormID=1&editID=${idValue}&${queryString}`;

      // $.post(url, function(returnedData) {
      //     console.log(returnedData);
      // }, 'json').fail(function() {
      //     console.log("error");
      // });
  }

  
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
