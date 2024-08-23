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
      console.log(data);

      result = data.data.result_set; // Assign result to the global variable
      headers = data.header;
      result.forEach(person => {
        //Convert dateofbirth to dd-mm-yyyy format
        if (person.dateofbirth) {
          // person.dateofbirth=person.dateofbirth.reverse();
           person.dateofbirth=person.dateofbirth.replaceAll("-","/");
        }

        // Convert dateanniv to dd-mm-yyyy format
        if (person.dateanniv) {
          person.dateofbirth=person.dateofbirth.replaceAll("-","/");
        }
    });
      
      // Check if result is an array and has at least one element
      if (Array.isArray(result) && result.length > 0) {
        // console.log(result[0]);
        // console.log(headers);

        

        // Configure columns based on the first object in the result set
        dynamicallyConfigureColumnsFromObject(result[0]);
        
        // Set row data for the grid
        gridApi.setGridOption('rowData', result);
        // getAllRows();
      } else {
        console.error('Expected resultSet to be a non-empty array, but got:', result);
      }
    });

    
  
    const uniqueValues = {
    

    };

  
    function dynamicallyConfigureColumnsFromObject(anObject) {
        const colDefs = gridApi.getColumnDefs();
        colDefs.length = 0;
        // Collect unique values for columns that use select editors
        headers.forEach(header => {
            const [key, secondValue] = header;
            uniqueValues[key] = {
                map: new Map(),
                counter: 1 // Initialize a counter for each key
            };  
            // console.log("header"+key);
        });
        // // Populate unique values from the result set
        // result.forEach(row => {
        // Object.keys(row).forEach(key => {
        //     // Ensure the key exists in uniqueValues (handles the case where a key might not be in headers)
        //     if (!uniqueValues[key]) {
        //     uniqueValues[key] = {
        //         map: new Map(),
        //         counter: 1
        //     };
        //     // console.log("header"+headers);
            
        //     }
        //     const value = row[key];
        //     // Add value to Map only if it's not "-" and not already present
        //     if (value !== "-" && !uniqueValues[key].map.has(value)) {
        //     uniqueValues[key].map.set(value, uniqueValues[key].counter);
        //     uniqueValues[key].counter += 1; // Increment counter for the next unique value
        //     }
        // });
        // });


        // Initialize unique values collection
const temporaryValues = {};

// Step 1: Collect all unique values for each key
result.forEach(row => {
    Object.keys(row).forEach(key => {
        if (!temporaryValues[key]) {
            temporaryValues[key] = new Set();
        }
        const value = row[key];
        if (value !== "-") {
            temporaryValues[key].add(value); // Collect values in a Set to ensure uniqueness
        }
    });
});

// Step 2: Sort and populate the Map with unique values
Object.keys(temporaryValues).forEach(key => {
    // Sort values alphabetically
    const sortedValues = Array.from(temporaryValues[key]).sort();
    uniqueValues[key] = {
        map: new Map(),
        counter: 1
    };
    sortedValues.forEach((value, index) => {
        uniqueValues[key].map.set(value, index + 1); // Assign values starting from 1
    });
});

// Now you can print the sorted and mapped unique values
Object.keys(uniqueValues).forEach(key => {
    console.log(key + " -->");
    uniqueValues[key].map.forEach((mappedValue, originalValue) => {
        console.log("  " + originalValue + " : " + mappedValue);
    });
});

     // Example of how to access the unique values map
        console.log(uniqueValues.bloodgroup.map);
        console.log(uniqueValues.nativeplace.map);
        console.log(uniqueValues.martialstatus.map);
        // console.log(uniqueValues.buisnessprofession);
        console.log(uniqueValues.relation_with_head.map);
        console.log(uniqueValues.son.map);
        console.log(uniqueValues.daughter.map);
        console.log(uniqueValues.panth.map);
        // console.log(uniqueValues.category_buisness.map);
        // console.log(uniqueValues.type_buisness);
        console.log(uniqueValues.state.map);
        // console.log(uniqueValues.firmstate);
        console.log(uniqueValues.opt_matrimony.map)
        
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
            // console.log(key)
            cellEditor = 'agDateCellEditor';
            filter = 'agDateColumnFilter';
        }else if (secondValue === "4" || secondValue === "5") {
            cellEditor = 'agSelectCellEditor';
            // Extract the keys from the Map (unique values)
            cellEditorParams = { values: Array.from(uniqueValues[key].map.keys()) };
            // console.log(Array.from(uniqueValues[key].map.values()));
            filter = 'agSetColumnFilter';
            } else if (secondValue === "11") {
        cellEditor = 'agRichSelectCellEditor';
        // Extract the keys from the Map (unique values)
        cellEditorParams = { values: Array.from(uniqueValues[key].map.keys()) };
        filter = 'agMultiColumnFilter';
            }
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
        console.log(uniqueValues);
        
    }


  
  
    function dateChecker(str) {
      return str.split('-').length - 1 === 2;
    }




  function onCellValueChanged(event) {
      console.log("Initial uniqueValues:", uniqueValues);
      console.log("Event data before changes:", event.data);

      // Create an object to store the key-value pairs for dynamic fields
      const dynamicParams = {};
      const idValue = event.data.Id;

      // Iterate over each key in the event data
      Object.keys(event.data).forEach(key => {
          // Find the corresponding header for the current key
          const header = headers.find(header => header[0] === key);
          
          if (header) {
              const thirdValue = parseInt(header[3], 10);
              
              if (thirdValue < 0) {
                  const mappedValue = uniqueValues[key].map.get(event.data[key]);

                  // Handle checkbox specifically
                  if (header[1] === "9") { // Assuming '9' is used for checkboxes
                      // Convert boolean to string
                      if (event.data[key] === true) {
                          event.data[key] = "1"; // Store as "1" if selected
                      } else {
                          event.data[key] = "2"; // Store as "2" if not selected
                      }
                  } else {
                      // For other types, use mappedValue if available
                      event.data[key] = mappedValue !== undefined ? String(mappedValue) : "";
                  }
              }
          }

          // Add the final value to dynamicParams
          dynamicParams[key] = event.data[key];
      });

      // Call the function to send data to the backend
      sendStorageRequestSaveAs(idValue, dynamicParams);

      console.log("Event data after changes:", event.data);
      console.log(dynamicParams);
      
  }




  function sendStorageRequestSaveAs(idValue,dynamicParams) {
      alert("sendStorage");

      // Construct the query parameters dynamically
      const queryString = Object.keys(dynamicParams)
          .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(dynamicParams[key])}`)
          .join('&');

      const url = `https://dummy.kreonsolutions.in/api/appdb.php?FormID=1&editID=${idValue}&${queryString}`;

      $.post(url, function(returnedData) {
          console.log(returnedData);
      }, 'json').fail(function() {
          console.log("error");
      });
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
