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


  // fetch("https://mocki.io/v1/56822da2-6b78-452e-af4d-a99666c51470")
  //   .then(function (response) {
  //     return response.json();
  //   }).then(function (data) {
  //     console.log(data);
  //     const result = data.data.result_set
  //     console.log(result[0]);
  //     console.log(data.header)
  //     headers=data.header
  //     dynamicallyConfigureColumnsFromObject(result[0])
  //     gridApi.setGridOption('rowData',result)
  //     // const dataCode = data.code
  //     // console.log(dataCode);
  //     // dynamicallyConfigureColumnsFromObject(dataCode);
  //     // gridApi.setGridOption('rowData', dataCode);
  //     // console.log(data[0]);
  //     // dynamicallyConfigureColumnsFromObject(data[0]);
  //     // gridApi.setGridOption('rowData', [data[0]]);
  //   });
  //   let headers;
  // function dynamicallyConfigureColumnsFromObject(anObject) {
  //   const colDefs = gridApi.getColumnDefs();
  //   colDefs.length = 0;
  //   console.log(anObject);
    
  //   const keys = Object.keys(anObject);
  //   // keys.forEach(key => {
  //   //   let filter;
  //   //   let cellEditor;
  //   //   const dataType = typeof anObject[key];
  //   //   if (dataType == "string") {       
  //   //     filter = 'agMultiColumnFilter';
  //   //     cellEditor = "agTextCellEditor";
  //   //     if (dateChecker(anObject[key]) === true) {
  //   //       cellEditor = 'agDateCellEditor';
  //   //     }
  //   //   } else if (dataType == "number") {
  //   //     filter = 'agNumberColumnFilter';
  //   //     cellEditor = 'agNumberCellEditor';
  //   //   } else if (dataType == "date") {
  //   //     filter = 'agDateColumnFilter';
  //   //     cellEditor = 'agDateCellEditor';
  //   //   }
  //   //   if (key == "url") {
  //   //     colDefs.push({ field: key, filter: filter, cellRenderer: CompanyLogoRenderer, cellEditor: cellEditor });
  //   //   } else {
  //   //     if (key == "country") {
  //   //       colDefs.push({ field: key, filter: filter, cellRenderer: CountryRenderer, cellEditor: "agRichSelectCellEditor", cellEditorParams: { cellRenderer: CountryRenderer, values: ["Great Britain", "Norway", "Canada"] } });
  //   //     } else if (key == "gender") {
  //   //       colDefs.push({ field: key, filter: filter, cellEditor: 'agSelectCellEditor', cellEditorParams: { values: ['Male', 'Female'] } });
  //   //     } else {
  //   //       colDefs.push({ field: key, filter: filter, cellEditor: cellEditor });
  //   //     }
  //   //   }
  //   // });
  //   // console.log(headers);
    
  //   keys.forEach(key => {
  //   const header = headers.find(header => header[0] === key);
  //   if (!header) return;

  //   const secondValue = header[1];
  //   let cellEditor;
  //   let filter;

  //   let cellEditorParams;

  //     if (secondValue === "6") {
  //     cellEditor = 'agDateCellEditor';
  //     filter = 'agDateColumnFilter';
  //     } else if (secondValue === "4") {
  //     cellEditor = 'agSelectCellEditor';
  //     cellEditorParams = { values: ['A+', 'B+', 'O+', 'AB+'] };
  //     filter = 'agSetColumnFilter';
  //     } else if (secondValue === "5") {
  //     cellEditor = 'agSelectCellEditor';
  //     cellEditorParams = { values: ['A+', 'B+', 'O+', 'AB+'] };
  //     filter = 'agSetColumnFilter';
  //     } else if (secondValue === "11") {
  //     cellEditor = 'agRichSelectCellEditor';
  //     filter = 'agMultiColumnFilter';
  //     } else if (secondValue === "8") {
  //     cellEditor = 'agTextCellEditor';
  //     filter = 'agTextColumnFilter';
  //     } else if (secondValue === "9") {
  //     cellEditor = 'agCheckboxCellEditor';
  //     filter = 'agMultiColumnFilter';
  //     } else if (secondValue === "10") {
  //     cellEditor = 'agRichSelectCellEditor';
  //     filter = 'agMultiColumnFilter';
  //     } else if (secondValue === "1" || !secondValue) {
  //     cellEditor = 'agTextCellEditor';
  //     filter = 'agMultiColumnFilter';
  //     }
  //     // console.log(cellEditor);
  //     // console.log(key)

  //     if(cellEditor){
  //     colDefs.push({ field: key, filter: filter, cellEditor: cellEditor , cellEditorParams: cellEditorParams  });
  //     // console.log("yes");
  //     }
  //     else {
  //     colDefs.push({ field: key, filter: filter, cellEditor: cellEditor});
  //     // console.log("no");
  //     }

  //     });


  //   // Set the new column definitions to the grid
  //   gridApi.setGridOption('columnDefs', colDefs);
  //   // console.log("Cell Instances", gridApi.getCellEditorInstances());
  // }

  let headers;
  let result; // Declare result globally

  fetch("https://mocki.io/v1/56822da2-6b78-452e-af4d-a99666c51470")
    .then(response => response.json())
    .then(data => {
      console.log(data);

      result = data.data.result_set; // Assign result to the global variable
      headers = data.header;
      
      // Check if result is an array and has at least one element
      if (Array.isArray(result) && result.length > 0) {
        console.log(result[0]);
        console.log(headers);
        
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

    const uniqueValues = {};

    // Collect unique values for columns that use select editors
    headers.forEach(header => {
      const [key, secondValue] = header;
      uniqueValues[key] = new Set();
    });

    // Populate unique values from the result set
    result.forEach(row => {
      Object.keys(row).forEach(key => {
        // Initialize the Set if it doesn't exist for the key
        if (!uniqueValues[key]) {
          uniqueValues[key] = new Set();
        }
        
        // Add value to Set only if it's not "-"
        if (row[key] !== "-") {
          uniqueValues[key].add(row[key]);
        }
      });
    });


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
      console.log(anObject[key])
      cellEditor = 'agDateCellEditor';
        filter = 'agDateColumnFilter';
      } else if (secondValue === "4" || secondValue === "5") {
        cellEditor = 'agSelectCellEditor';
        cellEditorParams = { values: Array.from(uniqueValues[key]) };
        filter = 'agSetColumnFilter';
      } else if (secondValue === "11") {
        cellEditor = 'agRichSelectCellEditor';
        cellEditorParams = { values: Array.from(uniqueValues[key]) };
        filter = 'agMultiColumnFilter';
      } else if (secondValue === "8") {
        cellEditor = 'agTextCellEditor';
        filter = 'agTextColumnFilter';
      } else if (secondValue === "9") {
        field: "boolean",
        cellEditor = 'agCheckboxCellEditor';
        filter = 'agMultiColumnFilter';
        console.log(cellEditor);
        
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
          console.log("kkkkk");
          
        }
        else
          colDefs.push({ field: key, filter: filter, cellEditor: cellEditor, cellEditorParams: cellEditorParams });
      } else {
        colDefs.push({ field: key, filter: filter, cellEditor: cellEditor});
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


    // Call the function to send data to the backend
    sendStorageRequestSaveAs(
    event.data.ID,
    event.data.Reg,
    event.data.EntryNo,
    event.data.Country,
    event.data.Id,
    event.data.area,
    event.data.bloodgroup,
    event.data.businessprofession,
    event.data.category_business,
    event.data.city,
    event.data.contactno,
    event.data.dateanniv,
    event.data.dateofbirth,
    event.data.daughter,
    event.data.email,
    event.data.facebook,
    event.data.fhname,
    event.data.firm_area,
    event.data.firm_city,
    event.data.firm_email,
    event.data.firm_pincode,
    event.data.firm_state,
    event.data.firmaddress,
    event.data.firmcontact,
    event.data.firmname,
    event.data.firmwebsite,
    event.data.fname,
    event.data.gender,
    event.data.graduateprofession,
    event.data.hobby,
    event.data.ida,
    event.data.idb,
    event.data.idc,
    event.data.idd,
    event.data.ide,
    event.data.idf,
    event.data.idg,
    event.data.idh,
    event.data.idj,
    event.data.idk,
    event.data.idl,
    event.data.idm,
    event.data.idn,
    event.data.idp,
    event.data.idq,
    event.data.idr,
    event.data.insta,
    event.data.line1,
    event.data.lname,
    event.data.martialstatus,
    event.data.nativeplace,
    event.data.opt_matrimony,
    event.data.panth,
    event.data.photo,
    event.data.pincode,
    event.data.qualification,
    event.data.relation_with_head,
    event.data.sakhe_mama,
    event.data.sakhe_mummy_mama,
    event.data.sakhe_papa_mama,
    event.data.shakhe,
    event.data.son,
    event.data.state,
    event.data.temp,
    event.data.twitter,
    event.data.type_business,
    event.data.user_id,
    event.data.whatsappno,
    event.data.youtube,
  );
   
  }



  function sendStorageRequestSaveAs(
    ID, Reg, EntryNo, Country, Id, area, bloodgroup, businessprofession, category_business,
    city, contactno, dateanniv, dateofbirth, daughter, email, facebook, fhname, firm_area,
    firm_city, firm_email, firm_pincode, firm_state, firmaddress, firmcontact, firmname,
    firmwebsite, fname, gender, graduateprofession, hobby, ida, idb, idc, idd, ide, idf,
    idg, idh, idj, idk, idl, idm, idn, idp, idq, idr, insta, line1, lname, martialstatus,
    nativeplace, opt_matrimony, panth, photo, pincode, qualification, relation_with_head,
    sakhe_mama, sakhe_mummy_mama, sakhe_papa_mama, shakhe, son, state, temp, twitter,
    type_business, user_id, whatsappno, youtube
) {
    alert("sendStorage");
    $.post(
        `https://dummy.kreonsolutions.in/api/appdb.php?FormID=1&editID=0&ID=${ID}&Reg=${Reg}&EntryNo=${EntryNo}&Country=${Country}&Id=${Id}&area=${area}&bloodgroup=${bloodgroup}&businessprofession=${businessprofession}&category_business=${category_business}&city=${city}&contactno=${contactno}&dateanniv=${dateanniv}&dateofbirth=${dateofbirth}&daughter=${daughter}&email=${email}&facebook=${facebook}&fhname=${fhname}&firm_area=${firm_area}&firm_city=${firm_city}&firm_email=${firm_email}&firm_pincode=${firm_pincode}&firm_state=${firm_state}&firmaddress=${firmaddress}&firmcontact=${firmcontact}&firmname=${firmname}&firmwebsite=${firmwebsite}&fname=${fname}&gender=${gender}&graduateprofession=${graduateprofession}&hobby=${hobby}&ida=${ida}&idb=${idb}&idc=${idc}&idd=${idd}&ide=${ide}&idf=${idf}&idg=${idg}&idh=${idh}&idj=${idj}&idk=${idk}&idl=${idl}&idm=${idm}&idn=${idn}&idp=${idp}&idq=${idq}&idr=${idr}&insta=${insta}&line1=${line1}&lname=${lname}&martialstatus=${martialstatus}&nativeplace=${nativeplace}&opt_matrimony=${opt_matrimony}&panth=${panth}&photo=${photo}&pincode=${pincode}&qualification=${qualification}&relation_with_head=${relation_with_head}&sakhe_mama=${sakhe_mama}&sakhe_mummy_mama=${sakhe_mummy_mama}&sakhe_papa_mama=${sakhe_papa_mama}&shakhe=${shakhe}&son=${son}&state=${state}&temp=${temp}&twitter=${twitter}&type_business=${type_business}&user_id=${user_id}&whatsappno=${whatsappno}&youtube=${youtube}`,
        function(returnedData) {
            console.log(returnedData);
        },
        'json'
    ).fail(function() {
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
