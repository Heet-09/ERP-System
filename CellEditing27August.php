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
    //   console.log(data);

      result = data.data.result_set; // Assign result to the global variable
      headers = data.header;
      result.forEach(person => {
        // Convert dateofbirth to dd-mm-yyyy format
        // if (person.dateofbirth) {
        //    person.dateofbirth=person.dateofbirth.replaceAll("-","/");
        // }

        // // Convert dateanniv to dd-mm-yyyy format
        // if (person.dateanniv) {
        //   person.dateofbirth=person.dateofbirth.replaceAll("-","/");
        // }
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

    const uniqueValues={}

  
    // function dynamicallyConfigureColumnsFromObject(anObject) {
    //     const colDefs = gridApi.getColumnDefs();
    //     colDefs.length = 0;
    //     // Collect unique values for columns that use select editors
    //     headers.forEach(header => {
    //         const [key, secondValue] = header;
    //         uniqueValues[key] = {
    //             map: new Map(),
    //             counter: 1 // Initialize a counter for each key
    //         };  
    //     });
    //     // Populate unique values from the result set
    //     result.forEach(row => {
    //     Object.keys(row).forEach(key => {
    //         // Ensure the key exists in uniqueValues (handles the case where a key might not be in headers)
    //         if (!uniqueValues[key]) {
    //         uniqueValues[key] = {
    //             map: new Map(),
    //             counter: 1
    //         };
    //         }

    //         const value = row[key];
    //         // Add value to Map only if it's not "-" and not already present
    //         if (value !== "-" && !uniqueValues[key].map.has(value)) {
    //         uniqueValues[key].map.set(value, uniqueValues[key].counter);
    //         uniqueValues[key].counter += 1; // Increment counter for the next unique value
    //         console.log(uniqueValues[key]);
    //         }
            
    //     });
    //     });
    //  // Example of how to access the unique values map
    //     // console.log(uniqueValues.bloodgroup.map);
    //     // console.log(uniqueValues.nativeplace.map);
    //     const keys = Object.keys(anObject);
    //     keys.forEach(key => {
    //     const header = headers.find(header => header[0] === key);
    //     if (!header) return;

    //     const secondValue = header[1];
    //     let cellEditor;
    //     let filter;
    //     let cellEditorParams;

    //     if (secondValue === "6") {
    //         anObject[key]=anObject[key].replaceAll("-","/");
    //         console.log(key)
    //         cellEditor = 'agDateCellEditor';
    //         filter = 'agDateColumnFilter';
    //     }else if (secondValue === "4" || secondValue === "5") {
    //         cellEditor = 'agSelectCellEditor';
    //         // Extract the keys from the Map (unique values)
    //         cellEditorParams = { values: Array.from(uniqueValues[key].map.keys()) };
    //         // console.log(Array.from(uniqueValues[key].map.values()));
    //         filter = 'agSetColumnFilter';
    //         } else if (secondValue === "11") {
    //     cellEditor = 'agRichSelectCellEditor';
    //     // Extract the keys from the Map (unique values)
    //     cellEditorParams = { values: Array.from(uniqueValues[key].map.keys()) };
    //     filter = 'agMultiColumnFilter';
    //         }
    //     else if (secondValue === "8") {
    //         cellEditor = 'agTextCellEditor';
    //         filter = 'agTextColumnFilter';
    //     } else if (secondValue === "9") {
    //         field: "boolean",
    //         cellEditor = 'agCheckboxCellEditor';
    //         filter = 'agMultiColumnFilter';
    //         // console.log(cellEditor);
            
    //     } else if (secondValue === "10") {
    //         cellEditor = 'agRichSelectCellEditor';
    //         cellEditorParams = { values: Array.from(uniqueValues[key]) };
    //         filter = 'agMultiColumnFilter';
    //     } else if(secondValue === "7"){
    //         cellEditor='agNumberCellEditor';
    //         filter='agNumberColumnFilter'
        
    //     }else if (secondValue === "1" || !secondValue) {
    //         cellEditor = 'agTextCellEditor';
    //         filter = 'agMultiColumnFilter';
    //     }
        
        
    //     if (cellEditor) {
    //         if(cellEditor=='agCheckboxCellEditor'){
    //         colDefs.push({ field: key, filter: filter, cellEditor: cellEditor, cellRenderer: 'agCheckboxCellRenderer', cellDataType: 'boolean', cellEditorParams: cellEditorParams });
    //         // console.log("kkkkk");
            
    //         }
    //         else
    //         colDefs.push({ field: key, filter: filter, cellEditor: cellEditor, cellEditorParams: cellEditorParams });
    //     } else {
    //         colDefs.push({ field: key, filter: filter, cellEditor: cellEditor});
    //     }
    //     });

    //     // Set the new column definitions to the grid
    //     gridApi.setGridOption('columnDefs', colDefs);
    //     // console.log("Cell Instances", gridApi.getCellEditorInstances());
    // }

    function dynamicallyConfigureColumnsFromObject(anObject) {
        const colDefs = gridApi.getColumnDefs();
        colDefs.length = 0;  // Clear existing column definitions

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

        console.log("temporary Values",temporaryValues);
        
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
              // console.log(key+"-->"+uniqueValues);
        });

        console.log("uniqueValues",uniqueValues);
        

        // Step 3: Synchronize and Map Values Based on Header Conditions
        const keys = Object.keys(anObject);
        keys.forEach(key => {
            const header = headers.find(header => header[0] === key);
            if (!header) return;

            const secondValue = header[1];
            const thirdValue = header[2];
            const fourthValue = header[3];
            let cellEditor;
            let filter;
            let cellEditorParams;

            // Synchronize with the id series based on fourth value
            if (fourthValue < 0) {
                const idKey = `id${String.fromCharCode(97 + (-fourthValue - 1))}`; // Convert fourth value to corresponding id series
                // console.log(idKey+" "+anObject[idKey]);
                // console.log(anObject[idKey]);
                
                if (anObject[idKey]) {
                    const mappedValue = uniqueValues[key].map.get(anObject[idKey]);
                    // console.log(mappedValue+" "+key);
                    
                    anObject[key] = [...uniqueValues[key].map.keys()][mappedValue - 1] || anObject[key];
                    // if()
                    console.log(key);
                    
                    console.log(uniqueValues[key].map);
                    
                }
            }

            // Define cell editors and filters based on secondValue
            if (secondValue === "6") {
                anObject[key] = anObject[key].replaceAll("-", "/");
                cellEditor = 'agDateCellEditor';
                filter = 'agDateColumnFilter';
            } else if (secondValue === "4" || secondValue === "5") {
                cellEditor = 'agSelectCellEditor';
                cellEditorParams = { values: Array.from(uniqueValues[key].map.keys()) };
                filter = 'agSetColumnFilter';
            } else if (secondValue === "11") {
                cellEditor = 'agRichSelectCellEditor';
                cellEditorParams = { values: Array.from(uniqueValues[key].map.keys()) };
                filter = 'agMultiColumnFilter';
            } else if (secondValue === "8") {
                cellEditor = 'agTextCellEditor';
                filter = 'agTextColumnFilter';
            } else if (secondValue === "9") {
                cellEditor = 'agCheckboxCellEditor';
                filter = 'agMultiColumnFilter';
            } else if (secondValue === "10") {
                cellEditor = 'agRichSelectCellEditor';
                cellEditorParams = { values: Array.from(uniqueValues[key]) };
                filter = 'agMultiColumnFilter';
            } else if (secondValue === "7") {
                cellEditor = 'agNumberCellEditor';
                filter = 'agNumberColumnFilter';
            } else if (secondValue === "1" || !secondValue) {
                cellEditor = 'agTextCellEditor';
                filter = 'agMultiColumnFilter';
            }

            // Push the column definition
            if (cellEditor) {
                if (cellEditor === 'agCheckboxCellEditor') {
                    colDefs.push({ field: key, filter: filter, cellEditor: cellEditor, cellRenderer: 'agCheckboxCellRenderer', cellDataType: 'boolean', cellEditorParams: cellEditorParams });
                } else {
                    colDefs.push({ field: key, filter: filter, cellEditor: cellEditor, cellEditorParams: cellEditorParams });
                }
            } else {
                colDefs.push({ field: key, filter: filter, cellEditor: cellEditor });
            }
        });

        // Set the new column definitions to the grid
        gridApi.setGridOption('columnDefs', colDefs);
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

    // }


  
  
    function dateChecker(str) {
        return str.split('-').length - 1 === 2;
    }


    // Response 1 Working without checkbox
    // function onCellValueChanged(event) {
    //     console.log("Initial uniqueValues:", uniqueValues);
    //     console.log("Event data before changes:", event.data);

    //     // Create an object to store the key-value pairs for dynamic fields
    //     const dynamicParams = {};
    //     const idValue = event.data.Id;

    //     // Iterate over each key in the event data
    //     Object.keys(event.data).forEach(key => {
    //         // Find the corresponding header for the current key
    //         const header = headers.find(header => header[0] === key);
            
    //         if (header) {
    //             const thirdValue = parseInt(header[3], 10);
                
    //             // Check if the third value is less than 0
    //             if (thirdValue < 0) {
    //                 // Get the mapped value for the current key from uniqueValues
    //                 const mappedValue = uniqueValues[key].map.get(event.data[key]);
                    
    //                 // Replace the value with its mapped value if found
    //                 if (mappedValue !== undefined) {
    //                     event.data[key] = mappedValue;
    //                 } else {
    //                     // Handle cases where no mapped value is found
    //                     event.data[key] = null;
    //                 }
    //             }
    //         }
            
    //         // Add the final value to dynamicParams
    //         dynamicParams[key] = event.data[key];
    //     });

    //     // Call the function to send data to the backend
    //     sendStorageRequestSaveAs(idValue, dynamicParams);

    //     console.log("Event data after changes:", event.data);
    // }

    // Reespins 2 Working without checkboc
    // function onCellValueChanged(event) {
    //     console.log(uniqueValues);
    //     console.log("llll");
        
    //     console.log("mm", event.data);
    //     console.log("mm", event.data.Id);
        
    //     // Create an object to store the key-value pairs for dynamic fields
    //     const dynamicParams = {};
    //     const idValue = event.data.Id;

    //     // Iterate over each key in the event data
    //     Object.keys(event.data).forEach(key => {
    //         // Find the corresponding header for the current key
    //         const header = headers.find(header => header[0] === key);
    //         if (header) {
    //             // Check if the header[3] value is less than 0
    //             const thirdValue = parseInt(header[3], 10);
    //             if (thirdValue < 0) {
    //                 // Get the mapped value for the current event.data[key]
    //                 const displayedValue = event.data[key];
    //                 const mappedValue = uniqueValues[key]?.map.get(displayedValue);

    //                 // Replace the value in event.data with the mapped value
    //                 if (mappedValue !== undefined) {
    //                     event.data[key] = mappedValue;
    //                 } else {
    //                     // If mappedValue is not found, you can handle it as needed (e.g., setting it to null)
    //                     event.data[key] = null;
    //                 }

    //                 // Store the updated value in dynamicParams
    //                 dynamicParams[key] = event.data[key];
    //             } else {
    //                 // If header[3] is not less than 0, just copy the value to dynamicParams
    //                 dynamicParams[key] = event.data[key];
    //             }
    //         }
    //     });

    //     // Call the function to send data to the backend
    //     sendStorageRequestSaveAs(idValue, dynamicParams);

    //     console.log("AfterChanges", event.data);
    // }


    // Response 3 Working with checkbox but not storing as string
    // function onCellValueChanged(event) {
    //     console.log("Initial uniqueValues:", uniqueValues);
    //     console.log("Event data before changes:", event.data);

    //     // Create an object to store the key-value pairs for dynamic fields
    //     const dynamicParams = {};
    //     const idValue = event.data.Id;

    //     // Iterate over each key in the event data
    //     Object.keys(event.data).forEach(key => {
    //         // Find the corresponding header for the current key
    //         const header = headers.find(header => header[0] === key);
            
    //         if (header) {
    //             const thirdValue = parseInt(header[3], 10);
                
    //             if (thirdValue < 0) {
    //                 const mappedValue = uniqueValues[key].map.get(event.data[key]);
                    
    //                 // Handle checkbox specifically
    //                 if (header[1] === "9") { // Assuming '9' is used for checkboxes
    //                     // Check if the value is selected
    //                     if (event.data[key] === true) {
    //                         event.data[key] = 1; // Set to 1 if selected
    //                     } else {
    //                         event.data[key] = 2; // Set to 2 if not selected
    //                     }
    //                 } else {
    //                     // For other types
    //                     if (mappedValue !== undefined) {
    //                         event.data[key] = mappedValue;
    //                     } else {
    //                         // Handle cases where no mapped value is found
    //                         event.data[key] = null;
    //                     }
    //                 }
    //             }
    //         }
            
    //         // Add the final value to dynamicParams
    //         dynamicParams[key] = event.data[key];
    //     });

    //     // Call the function to send data to the backend
    //     sendStorageRequestSaveAs(idValue, dynamicParams);

    //     console.log("Event data after changes:", event.data);
    // }

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
