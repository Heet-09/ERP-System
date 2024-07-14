<?php 
//include "k_files/k_config.php";
$k_head_keywords = "";
$k_head_desc = "Admin Template";
$k_head_author = "KreonSolutions.com";
$k_head_login_check = 1;
$k_page_title = $k_head_title; 
include "k_files/k_header.php";
$k_table_title = $k_head_title;
$FormID = isset($FormID) ? $FormID : (isset($_GET['form']) ? $_GET['form'] : 0);
$k_debug = isset($k_debug) ? $k_debug : 0;
$radio = array();
//if($_SESSION["user_id"] == 1)    
$bypassUserRoles = 1; //else $bypassUserRoles = 0;
//$k_debug=1;
//$k_user_id = $_SESSION["user_id"] = 1;
//include_once('dbClass.php');

function checkVariable($str){ //CHECK IF THERE IS A VARIABLE AND CONVERT IT
	if(strpos($str,"$") !== false){
		//echo "<br/>c".$cc = substr($str, strpos($str,"$"));	
		//echo "<br/>p".$pp = substr($cc,0,strpos($cc," "));
		$cc = substr($str, strpos($str,"$"));
		$pp = substr($cc,0,strpos($cc," "));
		$pp = " ". $pp;
		$ini = strpos($pp, "[");
		$ini += 1;
		$len = strpos($pp, "]", $ini) - $ini;
		$dd = substr($pp, $ini, $len);
		//replace Session for this below value
		return $_SESSION[$dd];
	}else{
		return $str;
	}
}

?>

<script>
	window.onload = function() { // Focus On first Input field
		$('#form').find('input[type=text]').eq(0).focus();
	}

	$(document).ready(function(){

		$('.amodal').click(function(){ //Open modal for Add Master entry
			var BtnVal = $(this).val().split('-');
			var FormId = BtnVal[0];  //Get formid
			var targetFieldId = BtnVal[1]; //Get target field id
			var formType = BtnVal[2];
			var AddModal = 'AddMoreModal'; //Modal id
			var AddModalForm = 'ModelContent'; //Model bidy id where we display form
			$.ajax({
				type : 'POST',
				dataType: 'json',
				data : {},
				url : '/api/form.php?FormID='+FormId+'&editID=0',
				success : function(result){
					// console.log(result);
					showModalField(result,FormId,targetFieldId,formType,AddModal,AddModalForm); //On ajax response create input fields
				}
			});
		});	

		function showModalField(result, FormId, targetFieldId,formType,AddModal,AddModalForm){
			var code = result['code'];
			var mfld = "";
			var mreqv = [];
			var mreqf = [];	

			mfld += "<div class='container1'><div class='row' ><div class='col-md-12 allfields'>";
			for(var i = 0; i < code.length; i++){
				mreqv[i] = "";
				mreqf[i] ="";
				if(code[i][5] == 1){
					mreqf[i] = 'required';
					mreqv[i] = '<span class="required"> *</span>'
				}
				mfld += "<div class='"+code[i][0]+"'>";
				if(code[i][1] == 1){ //Text
					mfld += "<div class='col-md-6'><label>"+code[i][2]+"</label>"+mreqv[i]+"<input class='form-control dyn1' "+mreqf[i]+" type='text' name='"+code[i][0]+"' id='"+code[i][0]+"' value=''></div>";
				}

				if(code[i][1] == 3){ //Textarea
					mfld += "<div class='col-md-6'><label>"+code[i][2]+"</label>"+mreqv[i]+"<textarea class='form-control dyn1' "+mreqf[i]+" name='"+code[i][0]+"' id='"+code[i][0]+"' value=''></textarea></div>";
				}

				if(code[i][1] == 6){ //Date
					mfld += "<div class='col-md-6'><label>"+code[i][2]+"</label>"+mreqv[i]+"<input class='form-control dyn1' "+mreqf[i]+" type='date' name='"+code[i][0]+"' id='"+code[i][0]+"' value=''></div>";
				}

				if(code[i][1] == 7){ //Number
					mfld += "<div class='col-md-6'><label>"+code[i][2]+"</label>"+mreqv[i]+"<input class='form-control dyn1' "+mreqf[i]+" type='number' name='"+code[i][0]+"' id='"+code[i][0]+"' value=''></div>";
				}

				if(code[i][1] == 8){ //Email
					mfld += "<div class='col-md-6'><label>"+code[i][2]+"</label>"+mreqv[i]+"<input class='form-control dyn1' "+mreqf[i]+" type='email' name='"+code[i][0]+"' id='"+code[i][0]+"' value=''></div>";
				}

				if(code[i][1] == 5){ //Select From DB
					var v =  result[code[i][0]]['result_set'];
					var k =  result[code[i][0]]['keys'];

					mfld += "<div class='col-md-6'><label>"+code[i][2]+"</label>"+mreqv[i]+"<select style='width:-webkit-fill-available;' name='"+code[i][0]+"' id='"+code[i][0]+"'>";

					mfld += "<option value='0' ></option>";

					for(var j=0; j < v.length; j++){ 
						mfld += "<option value='"+v[j][k[0]]+"' >"+v[j][k[1]]+"</option>";
					}

					mfld += "</select></div>";
				}

				if(code[i][1] == 9){ //Checkbox From DB
					var v =  result[code[i][0]]['result_set'];
					var k =  result[code[i][0]]['keys'];
					mfld += "<div class='col-md-6'><label>"+code[i][2]+"</label></br>";
					for(var j=0; j < v.length; j++){ 
						mfld += "<label><input '"+code[i][5]+"' type='checkbox' name='"+code[i][0]+"[]' id='"+code[i][0]+"' value=''>"+v[j][k[1]]+"</label></br>";
					}
					mfld += "</div>";
				}

				if(code[i][1] == 4){ //Radio From DB
					var v =  result[code[i][0]]['result_set'];
					var k =  result[code[i][0]]['keys'];
					mfld += "<div class='col-md-6'><label>"+code[i][2]+"</label></br>";

					for(var j=0; j < v.length; j++){ 
						mfld += "<label><input '"+code[i][5]+"' type='radio' name='"+code[i][0]+"' value=''>"+v[j][k[1]]+"</label></br>";
					}

					mfld += "</div>";
				}

				mfld += "</div>";
			}

			mfld += "<input type='hidden' id='formid' value='"+FormId+"'>";
			mfld += "<input type='hidden' id='hiddenTargetField' value='"+targetFieldId+"'>";
			mfld += "<input type='hidden' id='formType' value='"+formType+"'>";
			mfld += "</div></div></div>";
			$('#'+AddModalForm).html(mfld);
			$('#'+AddModal).modal('show');
		}

		$("#modalForm").submit(function(event){
			var targetFieldId = document.getElementById('hiddenTargetField').value;
			var FormID = document.getElementById('formid').value;
			var formType = document.getElementById('formType').value;
			saveAddMaster(targetFieldId,FormID,formType);
			return false;
		});

		function saveAddMaster(targetFieldId,FormID,formType){
			$.ajax({
				type : 'POST',
				dataType: 'json',
				data : $('form#modalForm').serializeArray(),

				url : '/api/appdb.php?FormID='+FormID,

				success : function(result){
					console.log(result['data']);
					if(formType == 14){
						var gridIdentifier = ($('#'+targetFieldId).parent().parent().attr("id")).slice(0,1);
						var gridCnt = $("#cnt" + gridIdentifier).val();
						var lastElementFlag = false;			

						for(var i = gridCnt - 1; i >= 0; i--){
							// debugger;
							if($('#' + gridIdentifier + i ).length){ //if element exists
								if(!lastElementFlag){	//if last element then select the added value
									if($('#' + gridIdentifier + i +' select[name="aQualityName[]"]').val() > 0){ //If last element has pre selected value then add a new row
										$('.add' + gridIdentifier).click();
										i++;
									}

									$('#' + gridIdentifier + i + ' select[name="aQualityName[]"]').append($("<option></option>")
										.attr("value", result['data'][0]['ID'])
										.text(result['data'][0]['Name']));
									lastElementFlag = true;
									$('#' + gridIdentifier + i +' select[name="aQualityName[]"]').val(result['data'][0]['ID']).trigger('change');
								}else{

									$('#' + gridIdentifier + i + ' select[name="aQualityName[]"]').append($("<option></option>")
									.attr("value", result['data'][0]['ID'])
									.text(result['data'][0]['Name']))
								}
							}
						}
					}else{
						$('#'+targetFieldId).append($("<option></option>")
						.attr("value", result['data'][0]['ID'])
						.text(result['data'][0]['Name']));
						$('#'+targetFieldId).val(result['data'][0]['ID']).trigger('change');
					}

					$("#AddMoreModal").modal('hide');
					// $('#success-info').show();
					// $("#success-info").delay(5000).fadeOut(); 
				},
			});
		}
	});

	function onLostFocus(thiss){
		var thissParameters = $("#" + thiss.name + "-hidden").val();
		var fieldName = $(thiss).attr('name');
		var fieldValue = $(thiss).val();
		
		var str1 = "response";
		var responsePos = -1;
		if(thissParameters.indexOf(str1) != -1){
			responsePos = thissParameters.indexOf(str1);
		}

		var str2 = "url";
		var urlPos = -1;
		if(thissParameters.indexOf(str2) != -1){
			urlPos = thissParameters.indexOf(str2);
		}

		var newArr = ["", ""];
		if(urlPos != -1 && responsePos != -1){ //to split between both the parameters

			var splitIndex = urlPos > responsePos ? urlPos : responsePos;
 			newArr[0] = thissParameters.substring(0, splitIndex - 1)
 			newArr[1] = thissParameters.substring(splitIndex);
	
			var responseParams = newArr[0].substring(
						newArr[0].indexOf("{") + 1, 
						newArr[0].lastIndexOf("}")
					);

			var urlParams = newArr[1].substring(
						newArr[1].indexOf("{") + 1, 
						newArr[1].lastIndexOf("}")
					);

			// console.log(responseParams);	//#Broker : 5 | response.ID | response.Name
			// console.log(urlParams);
			var responseP = responseParams.split(',');	//THIS will give all the parameters required in response
			var finalParams = [];
			var finalParamsValue = [];
			var Parameters = [];
			for(var i = 0; i < responseP.length; i++){
				finalParams[i] = responseP[i].trim().split('|');
				for(var j=0; j<finalParams[i].length; j++)	{
					finalParamsValue[j] =  $('#'+finalParams[i][j].trim()).val();
					var paramkv = finalParams[i][j] + "=" + "'"+finalParamsValue[j]+"'";
					Parameters.push(paramkv);
				}
			}
		}
		
		$.ajax({
			type : 'POST',
			dataType: 'json',
			data : {selectedValue: Parameters},
			url : urlParams,
			success : function(result){
				//CHECK TYPE OF PARAMETERS
				console.log(result);
				if(result['rows'] > 0){
					
					$( "."+fieldName ).find('div').append( "<p id='"+fieldName+"-hide' style='color:red;'>"+fieldValue+" already exists.</p>" );
					setTimeout(function() {
						$("#"+fieldName+"-hide").remove();
						// $("#"+fieldName).val('');
					}, 2000);
				}
								
			}
		});
	}

	function dynamicFreeFill(thiss){
		var str1 = "response";
		var responsePos = -1;
		if(thiss.indexOf(str1) != -1){
			responsePos = thiss.indexOf(str1);
		}
		var str2 = "url";
		var urlPos = -1;
		if(thiss.indexOf(str2) != -1){
			urlPos = thiss.indexOf(str2);
		}

		var newArr = ["", ""];
		if(urlPos != -1 && responsePos != -1){ //to split between both the parameters
			var splitIndex = urlPos > responsePos ? urlPos : responsePos;
 			newArr[0] = thiss.substring(0, splitIndex - 1)
 			newArr[1] = thiss.substring(splitIndex);
			// console.log(newArr[0]);
			// console.log(newArr[1]);
			var responseParams = newArr[0].substring(
						newArr[0].indexOf("{") + 1, 
						newArr[0].lastIndexOf("}")
					);

			var urlParams = newArr[1].substring(
						newArr[1].indexOf("{") + 1, 
						newArr[1].lastIndexOf("}")
					);

			// console.log(responseParams);	//#Broker : 5 | response.ID | response.Name
			// console.log(urlParams);
			var responseP = responseParams.split(',');	//THIS will give all the parameters required in response
			var finalParams = [];
			for(var i = 0; i < responseP.length; i++){
				finalParams[i] = responseP[i].trim().split('|');
			}
		}
		// console.log(finalParams);
		$.ajax({
			type : 'POST',
			dataType: 'json',
			data : {},
			url : urlParams,
			success : function(result){
				//CHECK TYPE OF PARAMETERS
				console.log(result);
				for(var i = 0; i < finalParams.length; i++){
					if(finalParams[i][1].trim() == 0 || finalParams[i][1].trim() == 1 || finalParams[i][1].trim() == 3 || finalParams[i][1].trim() == 6 || finalParams[i][1].trim() == 7 || finalParams[i][1].trim() == 8 ){	//type = textbox & others //#TrspPay | 1 | TrspPay 
						$(finalParams[i][0].trim()).val(result[finalParams[i][2].trim()]);
					}
				}				
			}
		});
	}

	function CreateOnChangeScriptGridCalculation(thiss, calcuType){
		var table = document.getElementById("GridTable");
		var gridIdentifier = $(thiss).parent().closest('tr').attr('id');
		var sum = 0;
		const arrayvalue = [];

		for(var i=0; i<table.rows.length-1; i++){
			var fieldvalue=$("#a" + i + " td " + "#" + thiss.id).val();
			if(fieldvalue != undefined){
				arrayvalue.push(fieldvalue);
			}
		}
		// console.log(arrayvalue);
		var calculation;
		if(calcuType == 1){
			calculation = eval(arrayvalue.join('+'));
		}else if(calcuType == 2){
			var average = eval(arrayvalue.join('+'));
			calculation = average / arrayvalue.length;
		}else if(calcuType == 3){
			calculation = arrayvalue.length;
		}
		var total=$("#" + thiss.id + "Footer").val(calculation);
	}

	function CreateOnkeyupScriptGrid(thisElement){
		var total = 1;
		var thiss = $("#" + thisElement.name.slice(0,-2) + "-hidden").val();
		var gridIdentifier = $(thisElement).parent().closest('tr').attr('id');

		var string = thiss.substring(
			thiss.indexOf("{") + 1, 
			thiss.lastIndexOf("}")
		);

		var calculationParams = string.split('=');	
		var str = calculationParams[1].replace(/\s/g, ""); // Remove Space from string
		var string = str.split(/([+,-,*,(,),/])/); //Split string into array
		var calcuParams = string.filter(function(e){return e});
		var arrayWithOperator = []; // Get field value using 

		for (let i = 0; i < calcuParams.length; i++) {
			if (calcuParams[i] !== '+' && calcuParams[i] !== '-' && calcuParams[i] !== '*' && calcuParams[i] !== '/' && calcuParams[i] !== '(' && calcuParams[i] !== ')') {
				var fieldValue = $("#" + gridIdentifier + " td " + "#" + calcuParams[i].trim()).val();
				arrayWithOperator.push(fieldValue);	
			}else{
				arrayWithOperator.push(calcuParams[i].trim());
			}	
		}
		// console.log(arrayWithOperator);
		arrayWithValue = arrayWithOperator.join("");
		total = eval(arrayWithValue); 

		if(!isNaN(total)){
			$("#" + gridIdentifier + " td " + "#" + calculationParams[0].trim()).val(total);
		}
	}

	function CreateOnchangeScriptGrid(thisElement){
		selectedValue = thisElement.value;
		//elementId = thisElement.name.slice(0,-2);
		var thiss = $("#" + thisElement.name.slice(0,-2) + "-hidden").val();
		// function CreateOnchangeScript(thiss,elementId,type){
		var gridIdentifier = $(thisElement).parent().closest('tr').attr('id');
		var elementId = "#" + gridIdentifier + " td #" + thisElement.name.slice(0,-2);
		var type = 5;
		var str1 = "response";
		var responsePos = -1;
		if(thiss.indexOf(str1) != -1){
			responsePos = thiss.indexOf(str1);
		}

		var str2 = "url";
		var urlPos = -1;
		if(thiss.indexOf(str2) != -1){
			urlPos = thiss.indexOf(str2);
		}

		var newArr = ["", ""];
		if(urlPos != -1 && responsePos != -1){ //to split between both the parameters

			var splitIndex = urlPos > responsePos ? urlPos : responsePos;
 			newArr[0] = thiss.substring(0, splitIndex - 1)
 			newArr[1] = thiss.substring(splitIndex);
			console.log(newArr[0]);
			console.log(newArr[1]);
			var responseParams = newArr[0].substring(
						newArr[0].indexOf("{") + 1, 
						newArr[0].lastIndexOf("}")
					);

			var urlParams = newArr[1].substring(
						newArr[1].indexOf("{") + 1, 
						newArr[1].lastIndexOf("}")
					);

			console.log(responseParams);	//#Broker : 5 | response.ID | response.Name
			console.log(urlParams);
			var responseP = responseParams.split(',');	//THIS will give all the parameters required in response
			var finalParams = [];

			for(var i = 0; i < responseP.length; i++){
				finalParams[i] = responseP[i].trim().split('|');
			}
		}

		//loggedin user id, company id, year code, division, auth key for api

		$.ajax({
			type : 'POST',
			dataType: 'json',
			data : { selectedValue : selectedValue },
			url : urlParams,
			success : function(result){
				//CHECK TYPE OF PARAMETERS
				// console.log(result);
				for(var i = 0; i < finalParams.length; i++){
					if(finalParams[i][1].trim() == 0 || finalParams[i][1].trim() == 1 || finalParams[i][1].trim() == 3 || finalParams[i][1].trim() == 6 || finalParams[i][1].trim() == 7 || finalParams[i][1].trim() == 8 ){	//type = textbox & others //#TrspPay | 1 | TrspPay 
						console.log(result[finalParams[i][2]]);
						$("#" + gridIdentifier + " td " + finalParams[i][0].trim()).val(result[finalParams[i][2].trim()])
					}
				}				
			}
		});
	}

	function CreateOnchangeScript(thiss,elementId,type){
		console.log(thiss.value);
		var str1 = "response";
		var responsePos = -1;
		if(thiss.indexOf(str1) != -1){
			responsePos = thiss.indexOf(str1);
		}

		var str2 = "url";
		var urlPos = -1;
		if(thiss.indexOf(str2) != -1){
			urlPos = thiss.indexOf(str2);
		}

		var newArr = ["", ""];
		if(urlPos != -1 && responsePos != -1){ //to split between both the parameters
			var splitIndex = urlPos > responsePos ? urlPos : responsePos;
 			newArr[0] = thiss.substring(0, splitIndex - 1)
 			newArr[1] = thiss.substring(splitIndex);
			console.log(newArr[0]);
			console.log(newArr[1]);
			var responseParams = newArr[0].substring(
						newArr[0].indexOf("{") + 1, 
						newArr[0].lastIndexOf("}")
					);

			var urlParams = newArr[1].substring(
						newArr[1].indexOf("{") + 1, 
						newArr[1].lastIndexOf("}")
					);
			// console.log(responseParams);	//#Broker : 5 | response.ID | response.Name
			// console.log(urlParams);
			var responseP = responseParams.split(',');	//THIS will give all the parameters required in response
			var finalParams = [];
			for(var i = 0; i < responseP.length; i++){
				finalParams[i] = responseP[i].trim().split('|');
			}
		}
		// alert(elementId);
		if(type == 4){
			var selectedValue = $('input:radio[name="'+ elementId+'"]:checked').val();
		}else{
			var selectedValue = $("#" + elementId).val();
		}
		// alert(selectedValue);
		//loggedin user id, company id, year code, division, auth key for api

		$.ajax({
			type : 'POST',
			dataType: 'json',
			data : { selectedValue : selectedValue },
			url : urlParams,
			success : function(result){
				// alert(result);
				//CHECK TYPE OF PARAMETERS
				for(var i = 0; i < finalParams.length; i++){
					console.log(finalParams);
					if(finalParams[i][1].trim() == 0 || finalParams[i][1].trim() == 1 || finalParams[i][1].trim() == 3 || finalParams[i][1].trim() == 6 || finalParams[i][1].trim() == 7 || finalParams[i][1].trim() == 8 ){	//type = textbox & others //#TrspPay | 1 | TrspPay 
						$(finalParams[i][0].trim()).val(result[finalParams[i][2].trim()])
					}

					if(finalParams[i][1].trim() == 5){	//type = dropdown //#Broker | 5 | BrokerName|ID | Name
						resultArr = result[finalParams[i][2].trim()];
						var selectDD = "";
						for(var j = 0; j < resultArr.length; j++){
							var ddID = finalParams[i][3].trim();
							var ddVal = finalParams[i][4].trim();
							if( i == 0 ){
								selectDD += "<option selected value='" + resultArr[j][ddID] + "'>" + resultArr[j][ddVal] + "</option>"
							}else{
								selectDD += "<option value='" + resultArr[j][ddID] + "'>" + resultArr[j][ddVal] + "</option>"					
							}
							$(finalParams[i][0].trim()).html(selectDD);
						}
					}

					if(finalParams[i][1].trim() == 10){	//type = dropdown //#Broker | 5 | BrokerName|ID | Name
						resultArr = result[finalParams[i][2].trim()];
						var selectDD = "";
						for(var j = 0; j < resultArr.length; j++){
							var ddID = finalParams[i][3].trim();
							var ddVal = finalParams[i][4].trim();
							$(finalParams[i][0].trim()).select2('data', {id: ddID, text: ddVal});      
						}
					}

					if(finalParams[i][1].trim() == 4){	//type = radio button //#Broker | 4 | BrokerName|ID
						resultArr = result[finalParams[i][2].trim()];
						var selectRB = "";
						for(var j = 0; j < resultArr.length; j++){
							var ddID = finalParams[i][3].trim();
							var ddVal = finalParams[i][4].trim();
							selectRB += "<label><input type='radio' name='"+ finalParams[i][2] +"' value='"+ resultArr[j][ddID] +"' >&nbsp;"+ resultArr[j][ddVal]+"</label></br>";
						}
						$(finalParams[i][0].trim()+ "-div").html(selectRB);
					}

					if(finalParams[i][1].trim() == 9){	//type = checkbox //#Broker | 9 | BrokerName|ID
						resultArr = result[finalParams[i][2].trim()];
						var selectCB = "";
						for(var j = 0; j < resultArr.length; j++){
							var ddID = finalParams[i][3].trim();
							var ddVal = finalParams[i][4].trim();
							selectCB += "<label> <input type='checkbox' name='"+ finalParams[i][2].trim() +"[]' id='"+finalParams[i][2].trim()+"' value='"+ resultArr[j][ddID] +"' >&nbsp;"+ resultArr[j][ddVal]+"</label></br>";
						}
						$(finalParams[i][0].trim() + "-multi-div").html(selectCB);
					}
				}				
			}
		});
	}
</script>



<?php
//code array
//Types: 0=hidden 1=Text Box; 2=SelectDropDown 3=textbox 4=radio 5=Select-from-DB 6=date 7=number 8=Email 9=Checkbox-From-DB 10=MULTI-SELECT-from-DB 11=MULTI SELECT SEARCH from DB WITH MAPPING TABLE 12=Single Media Upload 13=Multiple Media Upload
//Image needs an extra folder name, Type allowed, max size allowed, single or multiple

// Add More Form Modal
	echo '<div class="modal fade" id="AddMoreModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button> 
					<h4 class="modal-title">Add</h4>                                                             
				</div> 
				<div class="modal-body" >
					<form id="modalForm" name="modal" role="form">
						<div id="ModelContent" >
						</div>
						<div class="modal-footer" style="margin-top:10px;">					
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<input type="submit" class="btn btn-success" id="submit">
						</div>
					</form>
				</div>   
			</div>                                                                       
		</div>                                          
	</div>';

	//Multy selected dropdown modal
	echo '<div class="modal fade" id="DropDownModal" tabindex="-1" role="dialog" aria-labelledby="modalLabelLarge" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" id="modalLabelLarge">Multi Select Drop Down</h4>
					</div>
					<div class="modal-body">
					<p><button id="button">Save</button></p>
						<table id="ddTable" class="display" cellspacing="0" width="100%">
							<thead><tr></tr></thead>
						</table>
					</div>
				</div>
			</div>
		</div>';

function createInputs($arr){

	global $k_debug;
	
	// Datatable script for multiselected dropdown
	$ddtableIncludes = '
		<link rel="stylesheet" type="text/css" href="/assets/vendor/jquery-datatables/media/css/jquery.dataTables.css">
		<link rel="stylesheet" type="text/css" href="/assets/vendor/jquery-datatables/extras/TableTools/css/dataTables.tableTools.css">
		<script type="text/javascript" language="javascript" src="/assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>
		<style>.toolbar {float:left;}</style>
		';

	//  print_r($arr);
	// echo "<pre>";
	if($arr[5] == 1){ //to check if required field
		$reqf = 'required';
		$reqv = '<span class="required"> *</span>';
	}
	else $reqf = $reqv = '';

// size = width; maxlength = input ka length; min = minimum value; max = maximum value;

	$divClass = "";
	$textLength="";

	if($arr[21] == 1){ // Onload dynamic free fill data
		if(strlen($arr[4]) == 0){
			echo "<script>
			window.addEventListener('load', function() {
				dynamicFreeFill('".$arr[22]."');
			}); </script>";
			$readOnly = "readonly";
		}
	}

	if($arr[9] != 0){ //to check text length size
		$textLength = "maxlength = $arr[9]";
	}

	$maxValue = "";

	if($arr[10] != 0){ //to check field max value
		$maxValue = "max = $arr[10]";
	}

	$minValue = "";

	if($arr[11] != 0){ //to check if field minimum value
		$minValue = "min = $arr[11]";
	}

	$readOnly = "";
	if($arr[12] != 0){ //to check if field readonly
		$readOnly = "readonly";
	}

	if($arr[13] == 0 ){  //to check visibility
		$divClass .= " hidden ";
	}

	//SIZES: col-md >= 992; col-sm >= 768; col-xs < 768 

	if($arr[16] !=0 ){	//Fields Size XS

		$divClass .= " col-xs-" . $arr[16] . " ";

	}

	if($arr[14] !=0 ){	//Field Size SM

		$divClass .= " col-sm-" . $arr[14] . " ";

	}

	if($arr[15] !=0 ){	//Fields Size MD

		$divClass .= " col-md-" . $arr[15] . " ";

	}

	if($arr[14] == 0 && $arr[15] == 0 && $arr[16] == 0 ){ //both the sizes are not set

		$divClass .= " col-md-4 ";

	}

	$textareaHeight = "";

	if($arr[20] != 0){ //to check textarea height

		$textareaHeight = "rows = $arr[20]";

	}

	if($arr[21] != 0){ //to check if field readonly

		$readOnly = "readonly";

	}

	$otherInputParams = $maxValue . " " . $minValue . " " . $textLength . " " . $readOnly;
	$formType = 1; // Added For Add More functionality

	if($arr[1] == 12){ 	//SINGLE IMAGE UPLOAD 
	    return "<div class='".$divClass."'><label class='custom-file-upload'><i class='fa fa-cloud-upload'></i> ".$arr[2].$reqv." <input type='file' ".$arr[6]." name='".$arr[0]."[]' id='".$arr[0]."[]' ".$arr[6]."/>".$arr[4]."</label></div>";
// 		return "<div class='col-md-4'><label>".$arr[2].$reqv."</label><input type='file' ".$arr[6]." name='".$arr[0]."[]' id='".$arr[0]."[]' ".$arr[6]."/>".$arr[4]."</div>";
	}

	if($arr[1] == 13){ 	//MULTIPLE IMAGE UPLOAD 
		return "<div class='".$divClass."'><label>".$arr[2].$reqv."</label><input style='display: block;' type='file' ".$arr[6]." name='".$arr[0]."[]' id='".$arr[0]."[]' ".$arr[6]." multiple='true' />".$arr[4]."</div>";
	}

	if($arr[1] == 0){ 	//hidden
		return "<input value='".$arr[4]."' type='hidden' ".$arr[6]." name='".$arr[0]."' />";
	}

	// print_r($arr);
	// echo "<pre></pre>";

	if($arr[1] == 1){ 	//Textbox
		//echo $arr[19];
		if(strlen($arr[19]) < 2){	//CONDITION TO CHECK ShowTextBoxInGrid

			//if($arr[0] != "Total"){	//CONDITION TO CHECK ShowTextBoxInGrid

				// if(strcmp($arr[6], "maxlength=")) $maxChar = "maxlength='200'";    //ADDED FOR VAPT  // else $maxChar = '';

			$flds = "<div class='".$divClass."'><label>".$arr[2].$reqv."</label><input ".$reqf." class='form-control dyn1' value='".$arr[4]."' type='text' ".$otherInputParams." ".$arr[6]." name='".$arr[0]."' id='".$arr[0]."' ";
			$onlostfocusHidden = "";
			if($arr[26] == 1){
				$flds .= "onfocusout=\"onLostFocus(this);\"";
				$onlostfocusHidden = '<input type="hidden" id="'.$arr[0].'-hidden" value="'.$arr[27].'">';

			}
			$flds .= "/>".$onlostfocusHidden."</div>";
			return $flds;
		}else{
			return "<script>window.addEventListener('load', function() {

						$('#".$arr[19]."Footer').attr('name', '".$arr[0]."'); 

						$('#".$arr[19]."Footer').val($arr[4]);

					}); </script>";
		}
	}

	if($arr[1] == 2){ 	//SELECT
		global $radio;
		$r = $radio;
		$m = $arr;
		$p = $arr[4];

		$flds = '<div class="col-md-4"><label>'.$arr[2].$reqv.'</label><select '.$reqf.' name="'.$m[0].'" id="'.$m[0].'" class="form-control" '.$arr[6].' ><option value="0"></option>';

		for($j = 0; $j < sizeof($r[$m[3]]); $j++){
			$k = $j+1;  //value
			if($p == $k) {
				$flds .= '<option selected value="'. $k .'" >'.$r[$m[3]][$j].'</option>&nbsp;';
			}
			else {
				$flds .= '<option value="'. $k .'">'.$r[$m[3]][$j].'</option>&nbsp;';
			}
		}

		$flds .= '</select></div>';
		return $flds;
	}

	if($arr[1] == 3){ 	//Textarea
	    // if(strcmp($arr[6], "maxlength=")) $maxChar = "maxlength='1000'";    //ADDED FOR VAPT 
	    // else $maxChar = '';
	    if(extractAttribute($arr[8], "RichTextEditor") !== null){     //FOR ADD NEW FUNCTION added on 15062023 for react cap api
		    return "<div class='".$divClass."'><label>".$arr[2].$reqv."</label><textarea ".$reqf." ".$textareaHeight." class='form-control dyn1' ".$arr[6]." ".$otherInputParams." name='".$arr[0]."' id='".$arr[0]."'>".$arr[4]."</textarea></div><script type='text/javascript'>CKEDITOR.replace(\"".$arr[0]."\", {height:'300px'});</script>";
		}else{
		    return "<div class='".$divClass."'><label>".$arr[2].$reqv."</label><textarea ".$reqf." ".$textareaHeight." class='form-control dyn1' ".$arr[6]." ".$otherInputParams." name='".$arr[0]."' id='".$arr[0]."'>".$arr[4]."</textarea></div>";
		}
	}

	if($arr[1] == 4){ 	//Radio
		/*global $radio;
		$r = $radio;
		$m = $arr;
		$p = $arr[4];
		$flds = '<div class="col-md-4"><label>'.$arr[2].$reqv.'</label><br />';
		//print_r($r[$m[3]]);
		for($j = 0; $j < sizeof($r[$m[3]]); $j++){
			$k = $j+1;  //value
			if($p == $k) {
				$flds .= '<input type="radio" checked value="'. $k .'" name="'.$m[0].'" />&nbsp;'.$r[$m[3]][$j].'<br/>';
			}
			else {
				$flds .= '<input type="radio" value="'. $k .'" name="'.$m[0].'" />&nbsp;'.$r[$m[3]][$j].'<br/>';
			}
		}
		$flds .= '</div>';
		return $flds;*/

		global $extradb;
		$eresult = array();

		for($i = 0; $i < sizeof($extradb); $i++){
			if($extradb[$i][0] == $arr[3]){

				$edb = $extradb[$i][1];

				$eid = $extradb[$i][2];

				$elb = $extradb[$i][3];

				$end = checkVariable($extradb[$i][4]);

				$eval = array();
				
				$esql = "SELECT ".$eid.",".$elb." FROM ".$edb . " " . $end;

				if($k_debug) echo '<br/>'.$esql.'<br/>';

				$eresult = db::getInstance()->db_select($esql);

				if($k_debug){ echo '<br/>'; print_r($eresult); }

				break;

			}
		}

		if(sizeof($eresult) > 0){

			if(sizeof($eresult['result_set']) > 0){

				$m = $arr;
				$p = $arr[4];	

				// $flds = '<div class="'.$divClass.'" id="'.$arr[0].'-div"><label>'.$arr[2].$reqv.'</label>';

				$flds = '<div class="'.$divClass.'"><label>'.$arr[2].$reqv.'</label><br /><div id="'.$arr[0].'-div">';

				for($j = 0; $j < sizeof($eresult['result_set']); $j++){

					$k = $eresult['result_set'][$j][$eid];  //ID

					if($p == $k) { 

						$flds .= '<input '.$reqf.'  type="radio" '.$arr[6].' checked value="'. $k .'" name="'.$m[0].'" ';

						if($arr[17] == 1){

							$onchange = " onchange=\"CreateOnchangeScript('" . $arr[18] . "', '".$m[0]."', '".$arr[1]."');\"";

							$flds .= $onchange;

						}

						$flds .= '/>&nbsp;'.$eresult['result_set'][$j][$elb].'<br/>';
					}
					else {

						$flds .= '<input '.$reqf.' type="radio" '.$arr[6].' value="'. $k .'" name="'.$m[0].'" ';

						if($arr[17] == 1){

							$onchange = " onchange=\"CreateOnchangeScript('" . $arr[18] . "', '".$m[0]."', '".$arr[1]."');\"";

							$flds .= $onchange;

						}

						$flds .= '/>&nbsp;'.$eresult['result_set'][$j][$elb].'<br/>';
					}
				}
				$flds .= '</div>';
				return $flds;
			}else{
				return null;
			}
		}else{
			return null;
		}
	}

	if($arr[1] == 5){ 	//SELECT from DB
		global $extradb;
		// print_r($arr);
		$eresult = array();
		for($i = 0; $i < sizeof($extradb); $i++){
			if($extradb[$i][0] == $arr[3]){
				$edb = $extradb[$i][1];
				$eid = $extradb[$i][2];
				$elb = $extradb[$i][3];
				// $end = $extradb[$i][4];
				$end = checkVariable($extradb[$i][4]);
				$eval = array();

				$esql = "SELECT ".$eid.",".$elb." FROM ".$edb . " " . $end;

				if($k_debug) echo '<br/>'.$esql.'<br/>';

				$eresult = db::getInstance()->db_select($esql);

				if($k_debug){ echo '<br/>'; print_r($eresult); }
				break;
			}
		}

		$m = $arr;
		$p = $arr[4];	
		$flds = '<div class="'.$divClass.'"><label>'.$arr[2].$reqv.'</label>';
		// print_r($arr);
		if($arr[24] == 1){
			//response{ID | Name } url { https://krdemo.kreonsolutions.in/getSender.php} parameters{ID: Sender}
			echo $ddtableIncludes;
			echo "<script>
			$(document).ready(function(){
				$('#MultyDDModal-". $m[0]."').click(function(){
					var thiss = '".$arr[25]."';
					var str1 = 'response';
					var responsePos = -1;
					if(thiss.indexOf(str1) != -1){
						responsePos = thiss.indexOf(str1);
					}

					var str2 = 'url';
					var urlPos = -1;
					if(thiss.indexOf(str2) != -1){
						urlPos = thiss.indexOf(str2);
					}

					var newArr = ['', ''];

					if(urlPos != -1 && responsePos != -1){ //to split between both the parameters
						var splitIndex = urlPos > responsePos ? urlPos : responsePos;
						newArr[0] = thiss.substring(0, splitIndex - 1)
						newArr[1] = thiss.substring(splitIndex);

						var str3 = 'parameters';
						var parametersPos = -1;
						if(newArr[1].indexOf(str3) != -1){
							parametersPos = newArr[1].indexOf(str3);
						}

						if(parametersPos == -1){
							newArr[2] = newArr[1];
						}else{
							newArr[2] =newArr[1].substring(0,parametersPos);
							newArr[3] = newArr[1].substring(parametersPos);
						}

						var responseParams = newArr[0].substring(
							newArr[0].indexOf('{') + 1, 
							newArr[0].lastIndexOf('}')
						);

						var urlParams = newArr[2].substring(
							newArr[2].indexOf('{') + 1, 
							newArr[2].lastIndexOf('}')
						);

						if(newArr[3] != undefined){
							var parametersParams = newArr[3].substring(
								newArr[3].indexOf('{') + 1, 
								newArr[3].lastIndexOf('}')
							);
						}

						var finalResponseParams = responseParams.trim().split('|');
						// console.log(finalResponseParams);
						var stringResponse = finalResponseParams.join(',');

						var Parameters = [];
						var IParameters = [];
						if(parametersParams != undefined){
							var finalParametersParams = parametersParams.split(',');
							for(var i=0; i < finalParametersParams.length; i++){
								IParameters = finalParametersParams[i].split(':');
								var parameterValue =  $('#'+IParameters[1].trim()).val();
								var paramkv = IParameters[0].trim()+\"='\" +parameterValue+ \"'\";
								Parameters.push(paramkv);
							}
							// var stringParameters = Parameters.join('&');
							// console.log(stringParameters);
						}
					}

					  jqxhr = $.ajax('getSender.php?tblResponse='+stringResponse+'&where='+Parameters)
					  .done(function () {
							tableName= '#ddTable',
						  	data = JSON.parse(jqxhr.responseText);
							// console.log(data);

							// Iterate each column and print table headers for Datatables
							$.each(data.columns, function (k, colObj) {
								str = '<th>' + colObj.name + '</th>';
								$(str).appendTo(tableName+'>thead>tr');
							});

							// Add some Render transformations to Columns
							// Not a good practice to add any of this in API/ Json side
							data.columns[0].render = function (data, type, row) {
								return '<h4>' + data + '</h4>';
							}

							var table = $(tableName).dataTable({
								'data': data.data,
								'columns': data.columns,
								select: true,
								dom: 'T<\"clear\">lfrtip',
								tableTools: {
									'sRowSelect': 'multi',
									'aButtons': [ 'select_all', 'select_none' ]
								},
								// 'columnDefs' : [
								// 	//hide the second & fourth column
								// 	{ 'visible': false, 'targets': [0] }
								// ],
								
								// tableTools: { 'aButtons': [ 'print' ] },
								// layout: {
								// 	topStart: {
								// 		buttons: [
								// 			{
								// 				text: 'My button',
								// 				action: function (e, dt, node, config) {
								// 					alert('Button activated');
								// 				}
								// 			}
								// 		]
								// 	}
								// },
								// 'order': [[ 1, 'asc' ]],
								// targets: 0,
								// initComplete: function(){
								// 	console.log('Hi');
								// 	$('div.toolbar').html('<button>Hi</button>');           
								//  } 
							});
							
							// table.on('order.dt search.dt', function () {
							// 		let i = 1;
							// 		table.cells(null, 0, { search: 'applied', order: 'applied' })
							// 			.every(function (cell) {
							// 				this.data(i++);
							// 			});
							// 	})
							// 	.draw();
							var itemvalue = [];
							var itemlabel = [];
							table.on('click', 'tbody tr', function (e) {
								var dataItemvalue = $(this).find('td:eq(0)').text();
								var dataItemlabel = $(this).find('td:eq(1)').text();
								if(itemvalue.indexOf(dataItemvalue) === -1){
									itemvalue.push(dataItemvalue);
									itemlabel.push(dataItemlabel)
								}else{
									var tempIndex = itemvalue.indexOf(dataItemvalue);
									var tempLabelIndex = itemlabel.indexOf(dataItemlabel);
									if (tempIndex > -1) { // only splice array when item is found
										itemvalue.splice(tempIndex, 1); // 2nd parameter means remove one item only
									}
									if (tempLabelIndex > -1) { // only splice array when item is found
										itemlabel.splice(tempLabelIndex, 1); // 2nd parameter means remove one item only
									}
								}

								$.ajax({
									type : 'POST',
									dataType: 'json',
									data : {},
									url : urlParams,
									success : function(result){
										console.log(result);
										for(var i = 0; i < finalParams.length; i++){
											if(finalParams[i][1].trim() == 0){	//type = textbox & others //#TrspPay | 1 | TrspPay 
												$(finalParams[i][0].trim()).val(result[finalParams[i][2].trim()]);
											}
										}				
									}
								});
								
							});

							// document.querySelector('#button').addEventListener('click', function () {
							// 	console.log(items);
							// 	var rowCount = $('#ddTable tbody tr.selected').length;
							// });
							
						});
					$('#DropDownModal').modal('show');
				});
			});

			</script>";

			$flds .= '<button type="button" id="MultyDDModal-'.$m[0].'" class="btn btn-primary ddModal" >Open</button>';// data-toggle="modal"  data-target="#largeShoes"

			$ddtableIncludes = "";

		}

		if($arr[23] > 0){	

			$flds .= '<button type="button" id="AddBtnModal-'.$m[0].'" style="height:29px;margin-left:46px;" value="'.$arr[23].'-'.$m[0].'-'.$formType.'" class="btn btn-primary amodal">Add</button>';

		}

		if(strlen($arr[28]) > 0){
			echo "<script>
				$(document).ready(function(){
					var thiss = '".$arr[28]."';
					// console.log(thiss);
					var str1 = 'Response';
					var responsePos = -1;
					if(thiss.indexOf(str1) != -1){
						responsePos = thiss.indexOf(str1);
					}

					var str2 = 'DB';
					var dbPos = -1;
					if(thiss.indexOf(str2) != -1){
						dbPos = thiss.indexOf(str2);
					}

					var newArr = ['', ''];
					if(dbPos != -1 && responsePos != -1){
						var splitIndex = dbPos > responsePos ? dbPos : responsePos;
						newArr[0] = thiss.substring(0, splitIndex - 1);
						newArr[1] = thiss.substring(splitIndex);
						// console.log(newArr[0]);
						// console.log(newArr[1]);
					

						var responseParams = newArr[0].substring(
							newArr[0].indexOf('{') + 1, 
							newArr[0].lastIndexOf('}')
						);

						var dbParams = newArr[1].substring(
									newArr[1].indexOf('{') + 1, 
									newArr[1].lastIndexOf('}')
								);
						// console.log(responseParams);	//#Broker : 5 | response.ID | response.Name
						// console.log(dbParams);
						var responseP = responseParams.split(',');	//THIS will give all the parameters required in response
						var finalParams = [];
						for(var i = 0; i < responseP.length; i++){
							finalParams[i] = responseP[i].trim().split('|');
						}
					}
					
					$('#".$m[0]."').select2({
						//tags: true,
						// multiple: true,
						tokenSeparators: [',', ' '],
						minimumInputLength: 3,
						//minimumResultsForSearch: 10,
						ajax: {
							url: 'getDropDownValue.php',
							dataType: 'json',
							type: 'POST',
							data: function (params) {
								console.log(params.term);
								var queryParameters = {
									searchText: params.term
									,searchParam:finalParams[0][1]
									,fromRepo:dbParams
									,responseParams:finalParams.toString()
								}
								return queryParameters;
							},
							processResults: function (data) {
								return {
									results: $.map(data['data'], function (item) {
										// console.log(ddVal);
										//console.log(item[0].Label);
										return {
											
											text: item.ddVal,	
											id: item.ddID
										}
									})
								};
							}
						}
					});
				});
			</script>";
		}
		
		$flds .= '<select '.$reqf.'   name="'.$m[0].'" id="'.$m[0].'" class="form-control populate"';

		if(strlen($arr[28]) == 0){
			$flds .= 'data-plugin-selectTwo';
		}

		if($arr[17] == 1){
			$onchange = " onchange=\"CreateOnchangeScript('" . $arr[18] . "', '".$m[0]."', '".$arr[1]."');\"";
			$flds .= $onchange;
		}	

		$flds .= '><option value="0"></option>'; 

		if(sizeof($eresult) > 0){
			if(sizeof($eresult['result_set']) > 0){
				for($j = 0; $j < sizeof($eresult['result_set']); $j++){
					$k = $eresult['result_set'][$j][$eid];  //ID	

					if($p == $k) {
						$flds .= '<option selected value="'. $k .'" >'.$eresult['result_set'][$j][$elb].'</option>&nbsp;';
					}
					else {
						if(strlen($arr[28]) == 0){
							$flds .='<option value="'. $k .'" >'.$eresult['result_set'][$j][$elb].'</option>&nbsp;';
						}
					}
				}
			}
		}

		$flds .= '</select></div>';
		return $flds;
	}

	if($arr[1] == 6){ 	//Date
		if (gettype($arr[4]) != "string"){
			$arr[4] = $arr[4]->format('Y-m-d');
		}
		// if($arr[4] != NULL){	
		// 	echo "HHH".gettype($arr[4]);	
		// 	if (gettype($arr[4]) == "string"){
		// 		$arr[4] =  date("d-m-Y", strtotime($arr[4]));
			
		// 	}else{
		// 		$arr[4] = $arr[4]->format('Y-m-d');
		// 	}
		// }
		return "<div class='".$divClass."'><label>".$arr[2].$reqv."</label><input ".$reqf." class='form-control dyn1' value='".$arr[4]."' type='date' ".$arr[6]." ".$otherInputParams." name='".$arr[0]."' id='".$arr[0]."' /></div>";
	}

	if($arr[1] == 7){ 	//Number
	    // if(strcmp($arr[6], "maxlength=")) $maxChar = "maxlength='100'";    //ADDED FOR VAPT 
	    // else $maxChar = '';
		return "<div class='".$divClass."'><label>".$arr[2].$reqv."</label><input ".$reqf." class='form-control dyn1' value='".$arr[4]."' type='number' ".$arr[6]." ".$otherInputParams." name='".$arr[0]."' id='".$arr[0]."' /></div>";
	}

	if($arr[1] == 8){ 	//Email
		return "<div class='".$divClass."'><label>".$arr[2].$reqv."</label><input ".$reqf." class='form-control dyn1' ".$otherInputParams." value='".$arr[4]."' type='email' name='".$arr[0]."' id='".$arr[0]."' /></div>";
	}

	if($arr[1] == 9){ 	//Checkbox-From-DB
		global $extradb;
		$eresult = array();
		for($i = 0; $i < sizeof($extradb); $i++){
			if($extradb[$i][0] == $arr[3]){
				// print_r($extradb);
				$edb = $extradb[$i][1];
				$eid = $extradb[$i][2];
				$elb = $extradb[$i][3];
				$end = $extradb[$i][4];
				$emnydb = $extradb[$i][6];
				$emnyid = $extradb[$i][7];
				$emnyfk = $extradb[$i][8];
				$eval = array();
				$esql = "SELECT ".$eid.",".$elb." FROM ".$edb . " " . $end;

				if($k_debug) echo '<br/>'.$esql.'<br/>';
				$eresult = db::getInstance()->db_select($esql);
				if($k_debug){ echo '<br/>'; print_r($eresult); }
				break;
			}
		}

		if(sizeof($eresult) > 0){
			if(sizeof($eresult['result_set']) > 0){
				$m = $arr;
				$p = $arr[4];
				// print_r($p);
				$addNewFn = "";

    		if(extractAttribute($arr[8], "AddNewFn") !== null){     //FOR ADD NEW FUNCTION added on 23052023 for react cap api
    		    $AddNewFnName = extractAttribute($arr[8], "AddNewFn");
    		    $addNewFn = '<a style="padding:3px;" href="javascript:void(0)" class="btn btn-success" onclick="' . $AddNewFnName . '()">
    				<span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
    		} 		

				$flds = '<div class="'.$divClass.'"><label>'.$arr[2].$reqv.'</label>'.$addNewFn.'<br/><div class="multi-check" id="'.$arr[0].'-multi-div">';

				if(extractAttribute($arr[8], "TreeCheckbox") !== null){     //FOR ADD NEW FUNCTION added on 23052023 for react cap api
        		  //  $AddNewFnName = extractAttribute($arr[8], "TreeCheckbox");
        		  //  for($j = 0; $j < sizeof($eresult['result_set']); $j++){
    				// 	$k = $eresult['result_set'][$j][$eid];  //ID
    					/*[
					{title: "n1", expanded: true, key: "1", children: [
						{title: "n1.1 (selected)", key: "1.1"},
						{title: "n1.2", key: "1.2"},
						{title: "n1.3", key: "1.3"}
					]},
					{title: "n2", expanded: true, key: "2", children: [
						{title: "n2.1 (selected)", selected: true, key: "2.1"},
						{title: "n2.2", selected: false, key: "2.2"},
						{title: "n2.3", selected: null, key: "2.3"}
					]},
					{title: "n3", expanded: true, key: "3", children: [
						{title: "n3.1", expanded: true, key: "3.1", children: [
							{title: "n3.1.1 (unselectable)", unselectable: true, key: "3.1.1"},
							{title: "n3.1.2 (unselectable)", unselectable: true, key: "3.1.2"},
							{title: "n3.1.3", key: "3.1.3"}
						]},

						{title: "n3.2", expanded: true, key: "3.2", children: [
							{title: "n3.2.1 (unselectableStatus: true)", unselectableStatus: true, key: "3.2.1"},
							{title: "n3.2.2 (unselectableStatus: false)", unselectableStatus: false, key: "3.2.3"},
							{title: "n3.2.3", key: "3.2.3"}
						]},
						{title: "n3.3", expanded: true, key: "3.3", children: [
							{title: "n3.3.1 (unselectableStatus: true, unselectableIgnore)",
									unselectableStatus: true,  unselectableIgnore: true, key: "3.3.1"},
							{title: "n3.3.2 (unselectableStatus: false, unselectableIgnore)",
									unselectableStatus: false, unselectableIgnore: true, key: "3.3.2"},
							{title: "n3.3.3", key: "3.3.3"}
						]}
					]}
					]*/
    				// 	if($p == $k) {
    				// 		$flds .= '<label><input type="checkbox" checked '.$arr[6].' name="'.$m[0].'" id="'.$m[0].'" value="'. $k .'" >&nbsp;'.$eresult['result_set'][$j][$elb].'</label><br/>';
    				// 	}
    				// 	else { //CHANGED ON 03022020
    				// 		$flds .= '<label><input type="checkbox" '.$arr[6].' name="'.$m[0].'" id="'.$m[0].'" value="'. $k .'" >&nbsp;'.$eresult['result_set'][$j][$elb].'</label><br/>';
    				// 	}
    				// }

					if(1){
						$flds .= '
							<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
							<script src="//code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
							<link href="fancytree/src/skin-win8/ui.fancytree.css" rel="stylesheet">
							<script src="fancytree/src/jquery.fancytree.js"></script>
							<link href="fancytree/lib/prettify.css" rel="stylesheet">
							<script src="fancytree/lib/prettify.js"></script>
							<script src="fancytree/src/jquery.fancytree.filter.js"></script>
							<style type="text/css">
								p.warning,
							p.info,
							div.info {
								font-size: small;
								background-color: #fff3cd;
								background-image: url(../doc/iconInfo_32x32.png);
								background-repeat: no-repeat;
								padding: 5px;
								padding-left: 40px;
								min-height: 25px;
							}
							.sampleButtonContainer
							{
								margin-right: 10px;
							}
							.sampleButtonContainer a
							{
								color: #212529;
								text-decoration: undereline;
								text-decoration-style: dotted;
								padding: 1px 3px;
								font-size: 70%;
							}
							.sampleButtonContainer button
							{
								margin-bottom: 3px;
							}
							p#sampleButtons h5
							{
								font-size: 9pt;
								margin-top: 3px;
								margin-bottom: 3px;
								font-weight: bold;
							}
							.description a,
							.description a:hover,
							.description a:visited,
							p.sample-links a,
							p.sample-links a:hover,
							p.sample-links a:visited
							{
								color: #212529;
							/* text-decoration: none; */
							text-decoration-style: dotted;
							/* font-weight: 600; */
							}

							.description a:hover,
							p.sample-links a:hover
							{
								text-decoration: underline;
							}
							p.sample-links a,
							p.sample-links a:hover,
							p.sample-links a:visited
							{
								margin-left: 15px;
								padding: 1px 3px;
								font-size: small;
							}
							pre{
							background:#f7f7f7;
							border:1px solid #999;
							padding:.5em;
							margin:.5em;
							font-size:.9em;
							}

							body.example button {
								display: inline-block;
								font-weight: 400;
								color: #212529;
								text-align: center;
								vertical-align: middle;
								-webkit-user-select: none;
								-moz-user-select: none;
								-ms-user-select: none;
								user-select: none;
								background-color: transparent;
								border: 1px solid transparent;
								padding: .375rem .75rem;
								font-size: 1rem;
								line-height: 1.5;
								border-radius: .25rem;
								transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
								/* small: */
								padding: .15rem .3rem;
								font-size: .675rem;
								line-height: 1.2;
								border-radius: .2rem;
								/* .btn-secondary */
								color: #fff;
								background-color: #6c757d;
								border-color: #6c757d;
							}
							[type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled), button:not(:disabled) {
							cursor: pointer;
							}

							body.example button:hover {
								text-decoration: none;
								/* .btn-secondary */
								background-color: #5a6268;
								border-color: #545b62;
							}

							body.example button:focus {
								box-shadow: 0 0 0 0.2rem rgba(130,138,145,.5);
							}

							body.example input[type=submit],
							body.example button[type=submit] {
								/* .btn-primary */
								color: #fff;
								background-color: #007bff;
								border-color: #007bff;
							}

							body.example input[type=submit]:hover,
							body.example button[type=submit]:hover {
								/* .btn-primary */
							background-color: #0069d9;
							border-color: #0062cc;
							}

							ul.fancytree-container {
								margin: 4px; /* leave some room for the safari focus border */
							}

							iframe.embedded-plunkr {
								border: 1px solid #f7f7f7;
								width: 100%;
								height: 500px;
							}

							iframe.embedded-jsbin {
								border: 1px solid #f7f7f7;
								width: 100%;
								height: 500px;
							}

							</style>

							<script type="text/javascript">
								(function ($) {
								var PLUGIN_NAME = "skinswitcher",
									defaultOptions = {
										base: "",
										choices: [],
										// extraChoices: [],
										// Events:
										change: $.noop,
									},
									methods = {
										init: function (options) {
											var i,
												opts = $.extend({}, defaultOptions, options),
												hrefs = [],
												$link = null,
												initialChoice;
											if (!this.length) {
												return this;
											}

											// Attach options to skinswitcher combobox for later access
											this.data("options", opts);
											// Find the <link> tag that is used to includes our skin CSS.
											// Add a class for later access.
											$.each(opts.choices, function () {
												hrefs.push(this.href.toLowerCase());
											});

											$("head link").each(function () {
												for (i = 0; i < hrefs.length; i++) {
													if (this.href.toLowerCase().indexOf(hrefs[i]) >= 0) {
														$link = $(this);
														$link.addClass(PLUGIN_NAME);
														initialChoice = opts.choices[i];
													}
												}
											});
											if (!$link) {
												$link = $("link." + PLUGIN_NAME);
											}

											if (!$link.length) {
												$.error(
													"Unable to find <link> tag for skinswitcher. Either set `href` to a known skin url or add a `skinswitcher` class."
												);
											}
											//
											return this.each(function () {
												// Add options to dropdown list
												var $combo = $(this);
												$combo
													.empty()
													.skinswitcher("addChoices", opts.choices)
													.change(function (event) {
														var choice = $(":selected", this).data("choice");
														$("link." + PLUGIN_NAME).attr(
															"href",
															opts.base + choice.href
														);
														opts.change(choice);
													});
												// Find out initial selection
												if (opts.init) {
													$combo.val(opts.init).change();
												} else if (initialChoice) {
													// select combobox value to match current <link> tag
													// decouple this call to prevent IE6 exception
													setTimeout(function () {
														$combo.val(initialChoice.value);
														opts.change(initialChoice);
													}, 100);
												}
											});
										},
										option: function (name, value) {
											var opts = this.data("options");
											if (typeof value !== "undefined") {
												opts[name] = value;
												return this;
											}
											return opts[name];
										},
										addChoices: function (choices) {
											var $combo = $(this);
											if ($.isPlainObject(choices)) {
												choices = [choices];
											}
											$.each(choices, function (i, choice) {
												var $opt = $("<option>", {
													text: choice.name,
													value: choice.value,
												}).data("choice", choice);
												$combo.append($opt);
											});
											return this;
										},
										change: function (value) {
											$(this).val(value).change();
											return this;
										},
										reset: function () {
											$(this).val("").change();
											return this;
										},
									};

								$.fn[PLUGIN_NAME] = function (method) {
									// Method calling logic
									if (methods[method]) {
										return methods[method].apply(
											this,
											Array.prototype.slice.call(arguments, 1)
										);
									} else if (typeof method === "object" || !method) {
										return methods.init.apply(this, arguments);
									}
									$.error(
										"Method " + method + " does not exist on jQuery." + PLUGIN_NAME
									);
								};
							})(jQuery);

							/**
							* Replacement for $().toggle(func1, func2), which was deprecated with jQuery 1.8
							* and removed in 1.9.;
							* Taken from http://stackoverflow.com/a/4911660/19166
							* By Felix Kling
							*/
							(function ($) {
								var SAMPLE_BUTTON_DEFAULTS = {
									id: undefined,
									label: "Sample",
									newline: true,
									code: function () {
										alert("not implemented");
									},
								};
								$.fn.clickToggle = function (func1, func2) {
									var funcs = [func1, func2];
									this.data("toggleclicked", 0);
									this.click(function () {
										var data = $(this).data(),
										tc = data.toggleclicked;
										$.proxy(funcs[tc], this)();
										data.toggleclicked = (tc + 1) % 2;
									});
									return this;
								};

								window.getUrlVars = function () {
									var vars = {};
									window.location.href.replace(
										/[?&]+([^=&]+)=([^&]*)/gi,
										function (m, key, value) {
											vars[key] = value;
										}
									);
									return vars;
								};
								window.getUrlParam = function (parameter, defaultvalue) {
									var urlparameter = defaultvalue;
									if (window.location.href.indexOf(parameter) > -1) {
										urlparameter = window.getUrlVars()[parameter];
									}
									return urlparameter;
								};
								window.addSampleButton = function (options) {
									var sourceCode,
										opts = $.extend({}, SAMPLE_BUTTON_DEFAULTS, options),
										$buttonBar = $("#sampleButtons"),
										$container = $("<span />", {
											class: "sampleButtonContainer",
										});

									$("<button />", {
										id: opts.id,
										title: opts.tooltip,
										text: opts.label,
									})
										.click(function (e) {
											e.preventDefault();
											opts.code();
										})
										.appendTo($container);
									$("<a />", {
										text: "Source code",
										href: "#",
										class: "showCode",
									})
										.appendTo($container)
										.click(function (e) {
											try {
												prettyPrint();
											} catch (e2) {
												alert(e2);
											}
											var $pre = $container.find("pre");
											if ($pre.is(":visible")) {
												$(this).text("Source code");
											} else {
												$(this).text("Hide source");
											}
											$pre.toggle("slow");
											return false;
										});
									sourceCode = "" + opts.code;
									// Remove outer function(){ CODE }
									//    sourceCode = sourceCode.match(/[]\{(.*)\}/);
									sourceCode = sourceCode.substring(
										sourceCode.indexOf("{") + 1,
										sourceCode.lastIndexOf("}")
									);
									//    sourceCode = $.trim(sourceCode);
									// Reduce tabs from 8 to 2 characters
									sourceCode = sourceCode.replace(/\t/g, "  ");
									// Format code samples
									$("<pre />", {
										text: sourceCode,
										class: "prettyprint",
									})
										.hide()
										.appendTo($container);
									if (opts.newline) {
										$container.append($("<br />"));
									}
									if (opts.header) {
										$("<h5 />", { text: opts.header }).appendTo($("p#sampleButtons"));
									}
									if (!$("#sampleButtons").length) {
										$.error(
											"addSampleButton() needs a container with id #sampleButtons"
										);
									}
									$container.appendTo($buttonBar);
								};

								function initCodeSamples() {
									var info,
										$source = $("#sourceCode");

										$("#codeExample").clickToggle(
										function () {
											$source.show("fast");
											if (!this.old) {
												this.old = $(this).html();
												$.get(
													this.href,
													function (code) {
														// Remove <!-- Start_Exclude [...] End_Exclude --> blocks:
														code = code.replace(
															/<!-- Start_Exclude(.|\n|\r)*?End_Exclude -->/gi,
															"<!-- (Irrelevant source removed.) -->"
														);

														// Reduce tabs from 8 to 2 characters
														code = code.replace(/\t/g, "  ");
														$source.text(code);
														// Format code samples
														try {
															prettyPrint();
														} catch (e) {
															alert(e);
														}
													},
													"html"
												);
											}
											$(this).html("Hide source code");
										},

										function () {
											$(this).html(this.old);
											$source.hide("fast");
										}
									);
									if (jQuery.ui) {
										info =
											"Fancytree " +
											jQuery.ui.fancytree.version +
											", jQuery UI " +
											jQuery.ui.version +
											", jQuery " +
											jQuery.fn.jquery;
										$("p.sample-links").after(
											"<p class=\'version-info\'>" + info + "</p>"
										);
									}
								}

								$(function () {
									if (top.location === window.location) {
										$(".hideOutsideFS").hide();
									} else {
										$(".hideInsideFS").hide();
									}
									initCodeSamples();
									$("select#skinswitcher")
										.skinswitcher({
											base: "fancytree/src/",
											choices: [
												{
													name: "XP",
													value: "xp",
													href: "skin-xp/ui.fancytree.css",
												},
												{
													name: "Vista (classic Dynatree)",
													value: "vista",
													href: "skin-vista/ui.fancytree.css",
												},
												{
													name: "Win7",
													value: "win7",
													href: "skin-win7/ui.fancytree.css",
												},
												{
													name: "Win8",
													value: "win8",
													href: "skin-win8/ui.fancytree.css",
												},
												{
													name: "Win8-N",
													value: "win8n",
													href: "skin-win8-n/ui.fancytree.css",
												},
												{
													name: "Win8 xxl",
													value: "win8xxl",
													href: "skin-win8-xxl/ui.fancytree.css",
												},
												{
													name: "Lion",
													value: "lion",
													href: "skin-lion/ui.fancytree.css",
												},
											],
											change: function (choice) {
												$("#connectorsSwitch").toggle(choice.value !== "xp");
											},
										})
										.after($("<label id=\'connectorsSwitch\'><input name=\'cbConnectors\' type=\'checkbox\'>Connectors</label>"));
									$("input[name=cbConnectors]").on("change", function (e) {
										$(".fancytree-container").toggleClass("fancytree-connectors",$(this).is(":checked"));
									});
								});
							})(jQuery);

							</script>

							<script type="text/javascript">
								$(function(){
									$("#btnDeselectAll3").click(function(){
										$.ui.fancytree.getTree("#tree3").selectAll(false);
										return false;
									});
									$("#btnSelectAll3").click(function(){
										$.ui.fancytree.getTree("#tree3").selectAll();
										return false;
									});

									$("#btnGetSelected3").click(function(){
										var selNodes = $.ui.fancytree.getTree("#tree3").getSelectedNodes();
										var selData = $.map(selNodes, function(n){
											return n.toDict();
										});

										// alert(JSON.stringify(selData));
										return false;
									});

									$("#tree3").fancytree({
										checkbox: true,
										selectMode: 3,
										extensions: ["filter"],
										quicksearch: true,
										filter: {
											autoApply: true,   // Re-apply last filter if lazy data is loaded
											autoExpand: true, // Expand all branches that contain matches while filtered
											counter: true,     // Show a badge with number of matching child nodes near parent icons
											fuzzy: false,      // Match single characters in order,
											hideExpandedCounter: true,  // Hide counter badge if parent is expanded
											hideExpanders: false,       // Hide expanders if all child nodes are hidden by filter
											highlight: true,   // Highlight matches by wrapping inside <mark> tags
											leavesOnly: false, // Match end nodes only
											nodata: true,      // Display a no data status node if result is empty
											mode: "dimm"       // Grayout unmatched nodes (pass "hide" to remove unmatched node instead)
										},

										icon: false,
										source:[], /*
										source: [
											{title: "n1", expanded: true, key: "1", children: [
												{title: "n1.1 (selected)", key: "1.1"},
												{title: "n1.2", key: "1.2"},
												{title: "n1.3", key: "1.3"}
											]},

											{title: "n2", expanded: true, key: "2", children: [
												{title: "n2.1 (selected)", selected: true, key: "2.1"},
												{title: "n2.2", selected: false, key: "2.2"},
												{title: "n2.3", selected: null, key: "2.3"}
											]},

											{title: "n3", expanded: true, key: "3", children: [
												{title: "n3.1", expanded: true, key: "3.1", children: [
													{title: "n3.1.1 (unselectable)", unselectable: true, key: "3.1.1"},
													{title: "n3.1.2 (unselectable)", unselectable: true, key: "3.1.2"},
													{title: "n3.1.3", key: "3.1.3"}
												]},

												{title: "n3.2", expanded: true, key: "3.2", children: [
													{title: "n3.2.1 (unselectableStatus: true)", unselectableStatus: true, key: "3.2.1"},
													{title: "n3.2.2 (unselectableStatus: false)", unselectableStatus: false, key: "3.2.3"},
													{title: "n3.2.3", key: "3.2.3"}
												]},

												{title: "n3.3", expanded: true, key: "3.3", children: [
													{title: "n3.3.1 (unselectableStatus: true, unselectableIgnore)",
															unselectableStatus: true,  unselectableIgnore: true, key: "3.3.1"},
													{title: "n3.3.2 (unselectableStatus: false, unselectableIgnore)",
															unselectableStatus: false, unselectableIgnore: true, key: "3.3.2"},

													{title: "n3.3.3", key: "3.3.3"}
												]}
											]}
											],*/

										init: function(event, data) {
											data.tree.visit(function(n) { n.expanded = true; });
										},

										select: function(event, data) {
											var selKeys = $.map(data.tree.getSelectedNodes(), function(node){ return node.key; });
											$("#echoSelection3").text(selKeys.join(", "));
											$("#'.$m[0].'").val(selKeys.join(", "));
											var selRootNodes = data.tree.getSelectedNodes(true);
											console.log(selRootNodes);
											var selRootKeys = $.map(selRootNodes, function(node){ return node.key; });
											// console.log(selRootKeys);
											$("#echoSelectionRootKeys3").text(selRootKeys.join(", "));
										},

										cookieId: "fancytree-Cb3",
										idPrefix: "fancytree-Cb3-"
									});
		
									$("input[name=fancyTreeCBSearch]").on("change search", function(e){

										if (e.type === "change" && e.target.onsearch !== undefined ) { return; }
										var n, tree = $.ui.fancytree.getTree(),
										match = $.trim($(this).val());
										n = tree.filterNodes(match, { mode: "hide" });
									$("span.matches").text(n ? "(" + n + " matches)" : "");
									});
								});

							</script>

						<div class="cbtree" style="display:none;">
							<p><a href="#" id="btnSelectAll3">Select all</a> - <a href="#" id="btnDeselectAll3">Deselect all</a> - <a href="#" id="btnGetSelected3">Get selected</a></p>
							<label for="filter">Filter:</label>
							<input type="search" name="fancyTreeCBSearch" incremental placeholder="Search text" autocomplete="off">
							<span class="matches"></span>
							<div>Selected keys: <span id="echoSelection3">-</span></div>
							<input type="text" name="'.$m[0].'" id="'.$m[0].'" value="'. $k .'" />
							<div>Selected root keys: <span id="echoSelectionRootKeys3">-</span></div>
						</div>
						<div id="tree3"></div>';
					}
        		}else{ //END OF ADDED FOR CHECKBOX TREE 31052023
					// echo "<br>";
					// print_r($arr);
					
    				for($j = 0; $j < sizeof($eresult['result_set']); $j++){
						$k = $eresult['result_set'][$j][$eid];  //ID
						
						$flag = 0;
						if(!(is_null($p) || $p == "")) {
							for($g = 0; $g < sizeof($p); $g++){
								if($k == $p[$g][$emnyfk]){
									$flag = 1;
									break;
								}
							}
						}
						
    					if($flag) {
    						$flds .= '<label><input type="checkbox" checked ".$otherInputParams." '.$arr[6].' name="'.$m[0].'[]" id="'.$m[0].'" value="'. $k .'" >&nbsp;'.$eresult['result_set'][$j][$elb].'</label><br/>';
    					}
    					else { //CHANGED ON 03022020
    						$flds .= '<label><input type="checkbox" '.$arr[6].' name="'.$m[0].'[]" id="'.$m[0].'" value="'. $k .'" >&nbsp;'.$eresult['result_set'][$j][$elb].'</label><br/>';
    					}
    				}
        		}
				$flds .= '</div></div>';
				return $flds;
			}else{
				return null;
			}
		}else{
			return null;
		}
	}

	if($arr[1] == 10){ 	//MULTI SELECT SEARCH from DB
	/*HEADER
		$k_head_include = '
			<link rel="stylesheet" href="assets/vendor/select2/css/select2.css" />
			<link rel="stylesheet" href="assets/vendor/select2-bootstrap-theme/select2-bootstrap.min.css" />
			<link rel="stylesheet" href="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css" />'; */
	/*FOOTER
		$k_footer_before = '
		<script src="assets/vendor/select2/js/select2.js"></script>
		<script src="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>';		*/

		global $extradb;
		$eresult = array();
		for($i = 0; $i < sizeof($extradb); $i++){
			if($extradb[$i][0] == $arr[3]){
				$edb = $extradb[$i][1];
				$eid = $extradb[$i][2];
				$elb = $extradb[$i][3];
				$eval = array();
				$esql = "SELECT ".$eid.",".$elb." FROM ".$edb;// . " " . $end;

				if($k_debug) echo '<br/>'.$esql.'<br/>';
				$eresult = db::getInstance()->db_select($esql);
				if($k_debug){ echo '<br/>'; print_r($eresult); }

				break;
			}
		}
		//print_r($eresult);
		if(sizeof($eresult) > 0){
			if(sizeof($eresult['result_set']) > 0){
				//print_r($arr);
				$m = $arr;
				$p = $arr[4]; //print_r($eresult);
				$flds = '<div class="'.$divClass.'"><label>'.$arr[2].$reqv.'</label><select multiple data-plugin-selectTwo '.$reqf.' name="'.$m[0].'[]" id="'.$m[0].'" class="form-control populate" '.$arr[6].'>';

				for($j = 0; $j < sizeof($eresult['result_set']); $j++){
					$k = $eresult['result_set'][$j][$eid];  //ID
					$flag = 0;
					//echo "HHHH";
					// print_r($p);
					if(!empty($p)){
						for($g = 0; $g < sizeof($p); $g++){
							if($k == $p[$g][$extradb[0][2]]){
								$flag = 1;
								break;
							}
						}
					}

					if($flag) {
						$flds .= '<option selected value="'. $k .'" >'.$eresult['result_set'][$j][$elb].'</option>&nbsp;';
					}
					else {
						$flds .= '<option value="'. $k .'" >'.$eresult['result_set'][$j][$elb].'</option>&nbsp;';
					}
				}

				$flds .= '</select></div>';
				return $flds;

			}else{
				return null;
			}
		}else{
			return null;
		}
	}


	if($arr[1] == 11){ 	//MULTI SELECT SEARCH from DB WITH MAPPING TABLE
		global $extradb;
		$eresult = array();
		
		for($i = 0; $i < sizeof($extradb); $i++){
			if($extradb[$i][0] == $arr[3]){

				$edb = $extradb[$i][1];
				$eid = $extradb[$i][2];
				$elb = $extradb[$i][3];
				$end = $extradb[$i][4];
				$ear = $extradb[$i][5];
				$emnydb = $extradb[$i][6];
				$emnyid = $extradb[$i][7];
				$emnyfk = $extradb[$i][8];
				
				$eval = array();

				$esql = "SELECT ".$eid.",".$elb." FROM ".$edb;// . " " . $end;
				
				if($k_debug) echo '<br/>'.$esql.'<br/>';
				
				$eresult = db::getInstance()->db_select($esql);
				
				if($k_debug){ echo '<br/>'; print_r($eresult); }
				break;
			}
		}
		
		$m = $arr;
		$p = $arr[4]; 
		
		$addNewFn = "";

		if(extractAttribute($arr[8], "AddNewFn") !== null){     //FOR ADD NEW FUNCTION added on 23052023 for react cap api

		    $AddNewFnName = extractAttribute($arr[8], "AddNewFn");
		    $addNewFn = '<a style="padding:3px;" href="javascript:void(0)" class="btn btn-success" onclick="' . $AddNewFnName . '()">

				<span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
		} 

		$flds = '<div class="'.$divClass.'"><label>'.$arr[2].$reqv.'</label>&nbsp;'.$addNewFn.' ';

		// print_r($arr);
		if($arr[24] == 1){
			//response{ID | Name } url { https://krdemo.kreonsolutions.in/getSender.php} parameters{ID: Sender}
			echo $ddtableIncludes;

			$flds .= '<input type="button" class="btn btn-primary ddModal" id="MultyDDModal-'.$m[0].'" name="MultyDDModal" value="Open">';
			// $flds .= '<input type="button" id="MultyDDModal-'.$m[0].'" class="btn btn-primary ddModal" value="Open">';// data-toggle="modal"  data-target="#largeShoes"
 
			echo "<script>
			// window.addEventListener('load', function() {
			// 	$('#".$arr[0]."').next('.select2-container').hide();
			// });
			$(document).ready(function(){
				$('#MultyDDModal-". $m[0]."').click(function(){
					console.log($('#".$arr[0]."').next('.select2-container .selection span ul'));
					// $('#".$arr[0]."').next('.select2-container').hide();
					
					var thiss = '".$arr[25]."';
					var str1 = 'response';
					var responsePos = -1;
					if(thiss.indexOf(str1) != -1){
						responsePos = thiss.indexOf(str1);
					}

					var str2 = 'url';
					var urlPos = -1;
					if(thiss.indexOf(str2) != -1){
						urlPos = thiss.indexOf(str2);
					}

					var newArr = ['', ''];

					if(urlPos != -1 && responsePos != -1){ //to split between both the parameters
						var splitIndex = urlPos > responsePos ? urlPos : responsePos;
						newArr[0] = thiss.substring(0, splitIndex - 1)
						newArr[1] = thiss.substring(splitIndex);

						var str3 = 'parameters';
						var parametersPos = -1;
						if(newArr[1].indexOf(str3) != -1){
							parametersPos = newArr[1].indexOf(str3);
						}

						if(parametersPos == -1){
							newArr[2] = newArr[1];
						}else{
							newArr[2] =newArr[1].substring(0,parametersPos);
							newArr[3] = newArr[1].substring(parametersPos);
						}

						var responseParams = newArr[0].substring(
							newArr[0].indexOf('{') + 1, 
							newArr[0].lastIndexOf('}')
						);

						var urlParams = newArr[2].substring(
							newArr[2].indexOf('{') + 1, 
							newArr[2].lastIndexOf('}')
						);

						if(newArr[3] != undefined){
							var parametersParams = newArr[3].substring(
								newArr[3].indexOf('{') + 1, 
								newArr[3].lastIndexOf('}')
							);
						}

						var finalResponseParams = responseParams.trim().split('|');
						// console.log(finalResponseParams);
						var stringResponse = finalResponseParams.join(',');

						var Parameters = [];
						var IParameters = [];
						if(parametersParams != undefined){
							var finalParametersParams = parametersParams.split(',');
							for(var i=0; i < finalParametersParams.length; i++){
								IParameters = finalParametersParams[i].split(':');
								var parameterValue =  $('#'+IParameters[1].trim()).val();
								var paramkv = IParameters[0].trim()+\"='\" +parameterValue+ \"'\";
								Parameters.push(paramkv);
							}
							// var stringParameters = Parameters.join('&');
							// console.log(stringParameters);
						}
					}

					jqxhr = $.ajax('getSender.php?tblResponse='+stringResponse+'&where='+Parameters)
					.done(function () {
							tableName= '#ddTable',
							data = JSON.parse(jqxhr.responseText);
							// console.log(data);

							// Iterate each column and print table headers for Datatables
							$.each(data.columns, function (k, colObj) {
								str = '<th>' + colObj.name + '</th>';
								$(str).appendTo(tableName+'>thead>tr');
							});

							// Add some Render transformations to Columns
							// Not a good practice to add any of this in API/ Json side
							data.columns[0].render = function (data, type, row) {
								return '<h4>' + data + '</h4>';
							}

							var table = $(tableName).dataTable({
								'data': data.data,
								'columns': data.columns,
								select: true,
								dom: 'T<\"clear\">lfrtip',
								tableTools: {
									'sRowSelect': 'multi',
									'aButtons': [ 'select_all', 'select_none' ]
								},
								// 'columnDefs' : [
								// 	//hide the second & fourth column
								// 	{ 'visible': false, 'targets': [0] }
								// ],
								
								
							});
							
							var itemvalue = [];
							var itemlabel = [];
							table.on('click', 'tbody tr', function (e) {
								var dataItemvalue = $(this).find('td:eq(0)').text();
								var dataItemlabel = $(this).find('td:eq(1)').text();
								if(itemvalue.indexOf(dataItemvalue) === -1){
									itemvalue.push(dataItemvalue);
									itemlabel.push(dataItemlabel)
								}else{
									var tempIndex = itemvalue.indexOf(dataItemvalue);
									var tempLabelIndex = itemlabel.indexOf(dataItemlabel);
									if (tempIndex > -1) { // only splice array when item is found
										itemvalue.splice(tempIndex, 1); // 2nd parameter means remove one item only
									}
									if (tempLabelIndex > -1) { // only splice array when item is found
										itemlabel.splice(tempLabelIndex, 1); // 2nd parameter means remove one item only
									}
								}
								$('#MultyDDModal-". $m[0]."').val(itemlabel);

								console.log($('#".$arr[0]."').next('.select2-container span span span ul li').append('<li>Test</li>'));
							});							
						});
					$('#DropDownModal').modal('show');
				});
			});

			</script>";


			$ddtableIncludes = "";

		}

		if(strlen($arr[28]) > 0){
			echo "<script>
				$(document).ready(function(){
					var thiss = '".$arr[28]."';
					// console.log(thiss);
					var str1 = 'Response';
					var responsePos = -1;
					if(thiss.indexOf(str1) != -1){
						responsePos = thiss.indexOf(str1);
					}

					var str2 = 'DB';
					var dbPos = -1;
					if(thiss.indexOf(str2) != -1){
						dbPos = thiss.indexOf(str2);
					}

					var newArr = ['', ''];
					if(dbPos != -1 && responsePos != -1){
						var splitIndex = dbPos > responsePos ? dbPos : responsePos;
						newArr[0] = thiss.substring(0, splitIndex - 1);
						newArr[1] = thiss.substring(splitIndex);
						// console.log(newArr[0]);
						// console.log(newArr[1]);
					

						var responseParams = newArr[0].substring(
							newArr[0].indexOf('{') + 1, 
							newArr[0].lastIndexOf('}')
						);

						var dbParams = newArr[1].substring(
									newArr[1].indexOf('{') + 1, 
									newArr[1].lastIndexOf('}')
								);
						// console.log(responseParams);	//#Broker : 5 | response.ID | response.Name
						// console.log(dbParams);
						var responseP = responseParams.split(',');	//THIS will give all the parameters required in response
						var finalParams = [];
						for(var i = 0; i < responseP.length; i++){
							finalParams[i] = responseP[i].trim().split('|');
						}
					}
					
					$('#".$m[0]."').select2({
						//tags: true,
						multiple: true,
						tokenSeparators: [',', ' '],
						minimumInputLength: 3,
						//minimumResultsForSearch: 10,
						ajax: {
							url: 'getDropDownValue.php',
							dataType: 'json',
							type: 'POST',
							data: function (params) {
								console.log(params.term);
								var queryParameters = {
									searchText: params.term
									,searchParam:finalParams[0][1]
									,fromRepo:dbParams
									,responseParams:finalParams.toString()
								}
								return queryParameters;
							},
							processResults: function (data) {
								return {
									results: $.map(data['data'], function (item) {
										// console.log(ddVal);
										//console.log(item[0].Label);
										return {
											
											text: item.ddVal,	
											id: item.ddID
										}
									})
								};
							}
						}
					});
				});
			</script>";
		}

		$flds .= '<select '.$reqf.' name="'.$m[0].'[]" id="'.$m[0].'" class="form-control populate" '.$arr[6].' ';
		
		if(strlen($arr[28]) == 0){
			$flds .= 'multiple data-plugin-selectTwo';
		}

		$flds .= '>';

		if(sizeof($eresult) > 0){

			if(sizeof($eresult['result_set']) > 0){

				for($j = 0; $j < sizeof($eresult['result_set']); $j++){

					$k = $eresult['result_set'][$j][$eid];  //ID
					$flag = 0;

					if(!(is_null($p) || $p == "")) {

						for($g = 0; $g < sizeof($p); $g++){

							if($k == $p[$g][$emnyfk]){

								$flag = 1;
								break;
							}
						}
					}

					if($flag) {

						$flds .= '<option selected value="'. $k .'" >'.$eresult['result_set'][$j][$elb].'</option>&nbsp;';

					}else{

						$flds .= '<option value="'. $k .'" >'.$eresult['result_set'][$j][$elb].'</option>&nbsp;';

					}
				}
			}
		}
		$flds .= '</select></div>';
		return $flds;
	}	

	if($arr[1] == 15){ 	//Toggle Switch
		$flds = "<div class='".$divClass."'><label>".$arr[2].$reqv."</label><br /><div class='switch switch-sm switch-primary'>";

		$flds .= "<input type='checkbox' name='".$arr[0]."' id='".$arr[0]."' value='1' data-plugin-ios-switch=''  style='display: none;' ";
		if($arr[4] == 1){
			$flds .= "checked='checked'";
		}
	
		$flds .= "></div></div>";
		return $flds;
	}
}


function createMore1($m,$d,$r,$id){
	$v = array();
	$row1 = array();
	$formType = 14; // Added For Add More functionality

	if($id > 0){
		$sql = "SELECT * FROM ".$d[0]." WHERE ".$d[1]." = ".$id;
		$result = db::getInstance()->db_select($sql);
		$row = $result['result_set'];
		// print_r($result);
		$size = sizeof($m);
		for($i = 0; $i < $result['num_rows']; $i++){
			$v[$i] = array($size);
			for($j = 0; $j < $size; $j++){
				//echo $m[$j][1]."<br />";
				if(strlen($row[$i][$m[$j][1]]) == 0){
					$v[$i][$j] = $row[$i][ucfirst($m[$j][1])];
				}else{
					$v[$i][$j] = $row[$i][$m[$j][1]];
				}
			}
			$v[$i][$j] = $row[$i][$d[1]];
		}
	}
	// print_r($v);
	$temp = '<div class="grid'.$d[3].'"><label class="col-md-12"><b>'.$d[2].'</b>&nbsp;</label>
				<div class="panel-body" id="panel'.$d[3].'" style="overflow-x:auto;">';
	$tableStart = '<table style="table-layout: fixed;word-wrap: break-word;" id="GridTable" class="table table-responsive table-bordered table-hover">';
	$tableEnd = '</table>';
	$tableHead = '<thead class=" thead-light"><tr><th style="width:30px;padding:2px 3px !important;"><a  style="padding:3px;"  href="javascript:void(0)" class="btn btn-success add'.$d[3].'">
				<span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>';
	
	//$flds = '<div class="form-group grp'.$d[3].'" id="'.$d[3].'0">';
	$flds = '<td style="padding:2px 3px !important;"><a style="padding:3px;" href="javascript:void(0)" class="btn btn-danger removes'.$d[3].'"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>';
	$headerGridStart = '<tr class="grp'.$d[3].'" id="'.$d[3];
	$headerGridEnd   = '">';

	for($i = 0; $i < sizeof($m); $i++){		   
		// print_r($m[$i]);
	    //style='width:".$m[$i][7]."px' width='".$m[$i][7]."'

		$tableHead .= "<th style='width:".$m[$i][7]."px' width='".$m[$i][7]."'>".$m[$i][2]."";

		if($m[$i][13] > 0){

			$tableHead .= '<button type="button" class="btn btn-primary btn-sm amodal" style="margin-left:2px;" id="AddBtnModal-'.$m[$i][1].'" value="'.$m[$i][13].'-'.$m[$i][1].'-'.$formType.'">Add</button>';

		}

		$tableHead .="</th>";
		
		if($m[$i][0] == 1){	//IF TEXT BOX		width="'.$m[$i][7].'" style="width:'.$m[$i][7].'px" 

			$flds .= '<td ><input type="text" value="" class="form-control" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" placeholder="'.$m[$i][2].'" '.$m[$i][6].' ';

			$onkeyupHidden = "";

			if($m[$i][10] == 1){

				$onkeyup = " onkeyup=\"CreateOnkeyupScriptGrid(this);\"";

				$flds .= $onkeyup;

				$onkeyupHidden = '<input type="hidden" id="'.$d[3].$m[$i][1].'-hidden" value="' . $m[$i][11] . '" />';//,' . $m[$i][1] . ',' . $m[$i][0] . '

				// $changeScript .= "$('[name=\"".$d[3].$m[$i][1]."[]\"]').change(function(evt) { alert('The option with value ' + $(this).val() + ' and text ' + evt.target.value + ' ' + $(this).text() + ' was selected.'); });";
			}

			if($m[$i][12] > 0){
				$onchange = " onchange=\"CreateOnChangeScriptGridCalculation(this,".$m[$i][12].");\"";

				$flds .= $onchange;

				// $flds .= "readonly";
			}

			$flds .= '/>'.$onkeyupHidden.'</td>';
		}

		if($m[$i][0] == 3){	//IF TEXTAREA		width="'.$m[$i][7].'" style="width:'.$m[$i][7].'px" 
			$flds .= '<td ><textarea class="form-control" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" placeholder="'.$m[$i][2].'" '.$m[$i][6].'></textarea></td>';
		}

		if($m[$i][0] == 12){	//IF Image
			$flds .= '<td><input type="file" class="form-controlz" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" '.$m[$i][6].' /></td>';
		}

		if($m[$i][0] == 6){	//IF Date BOX		added by sneha on 01-11-2023
			$flds .= '<td ><input type="datetime-local" value="" class="form-control" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" placeholder="'.$m[$i][2].'" '.$m[$i][6].' /></td>';
		}

		if($m[$i][0] == 7){	//IF Number BOX		added by sneha on 01-11-2023
			$flds .= '<td ><input type="number" value="0" class="form-control" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" '.$m[$i][6].' /></td>';
		}

		if($m[$i][0] == 5){	//Dropdown From DB
			//print_r($m[$i]);
			if($m[$i][3] < 0){	//IF to be taken from DB
				$index = ((-1) * $m[$i][3]) - 1;
				global $moreextradb;

				$dbarray = $moreextradb[$index];
				$sql1 = "SELECT ".$dbarray[1].",".$dbarray[2]." FROM ".$dbarray[0]." ".$dbarray[3];
				$result1 = db::getInstance()->db_select($sql1);

				//print_r($result1);
				$row1 = $result1['result_set'];	

				$flds .= '<td><select name="'.$d[3].''.$m[$i][1].'[]"  placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control" ';

				$changeScript = "";

				$onchangeHidden = "";

				if($m[$i][8] == 1){

					$onchange = " onchange=\"CreateOnchangeScriptGrid(this);\"";

					$flds .= $onchange;

					$onchangeHidden = '<input type="hidden" id="'.$d[3].$m[$i][1].'-hidden" value="' . $m[$i][9] . '" />';//,' . $m[$i][1] . ',' . $m[$i][0] . '

					// $changeScript .= "$('[name=\"".$d[3].$m[$i][1]."[]\"]').change(function(evt) { alert('The option with value ' + $(this).val() + ' and text ' + evt.target.value + ' ' + $(this).text() + ' was selected.'); });";

				}

				$flds .= '><option value="0">'.$m[$i][2].'</option>';

				for($j = 0; $j < sizeof($row1); $j++){ 
					$flds .= '<option value="'. $row1[$j][$dbarray[1]] .'" >'.$row1[$j][$dbarray[2]].'</option>&nbsp;';
				}

				$flds .= '</select>'.$onchangeHidden.'</td>';

			}else{			

				$flds .= '<td><select name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control" '.$m[$i][6].' ><option value="0">'.$m[$i][2].'</option>';

				for($j = 0; $j < sizeof($r[$m[$i][3]]); $j++){
					$k = $j+1;  //value
					$flds .= '<option value="'. $k .'" >'.$r[$m[$i][3]][$j].'</option>&nbsp;';
				}

				$flds .= '</select></td>';
			}
		}
	}

	$flds .= '</div>';
	$end = '</div></tr>';		
	$script = '<script>
			$(document).ready(function(){
				'.$changeScript.'
				//add more row fields

				$(".add'.$d[3].'").click(function(){
					var cnt = $("#cnt'.$d[3].'").val();	
					var headofGrid = \''.$headerGridStart.'\' + cnt + \''.$headerGridEnd.'\';
					cnt = +cnt + 1;
					var end =\''.$end.'\';
					var fieldHTML=\''.$flds.'\';
					document.getElementById("cnt'.$d[3].'").value = cnt;
					$("#panel'.$d[3].' table tbody").append(headofGrid + fieldHTML + end);
				});

				//remove row
				$("#panel'.$d[3].'").on("click",".removes'.$d[3].'",function(){
					var cnt = document.getElementById("cnt'.$d[3].'").value;
					cnt = +cnt - 1;
					$(this).parents(".grp'.$d[3].'").remove();
					document.getElementById("cnt'.$d[3].'").value=cnt;
				});
			});
		</script>';

		if(empty($v)){
			$fieldCount = 1;
		}else{
			$tableStart = '<table style="table-layout: fixed;word-wrap: break-word;" id="GridTable" class="table table-responsive table-bordered table-hover">';
			$tableEnd = '</table>';
			$tableHead = '<thead class=" thead-light"><tr><th style="width:30px;padding:2px 3px !important;"><a  style="padding:3px;"  href="javascript:void(0)" class="btn btn-success add'.$d[3].'"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>';
			$fvals = '';
			$headerGridStart = '<tr class="grp'.$d[3].'" id="'.$d[3];
			$headerGridEnd   = '">';

			global $moreextradb;
			// print_r($m);
			// echo "<pre></pre></br>";
			// print_r($v);
			// echo "<pre></pre></br>";
			// print_r($m[4]);

			for($i = 0; $i < sizeof($m); $i++){		   //style='width:".$m[$i][7]."px' width='".$m[$i][7]."'
				$tableHead .= "<th style='width:".$m[$i][7]."px' width='".$m[$i][7]."'>".$m[$i][2]."";

				if($m[$i][13] > 0){

					$tableHead .= '<button type="button" class="btn btn-primary btn-sm amodal" style="margin-left:2px;" id="AddBtnModal-'.$m[$i][1].'" value="'.$m[$i][13].'-'.$m[$i][1].'-'.$formType.'">Add</button>';

				}

				$tableHead .="</th>";
			}

			for($k = 0; $k < sizeof($v); $k++){
				$fvals .= $headerGridStart . $k . $headerGridEnd .'<td style="padding:2px 3px !important;"><a style="padding:3px;" href="javascript:void(0)" class="btn btn-danger removes'.$d[3].'"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>';

				for($i = 0; $i < sizeof($m); $i++){
					// echo $v[$k][5]; 
					$inputVal = $v[$k][$i];
					if($m[$i][0] == 1){

					    $fvals .= '<td ><input type="text" class="form-control" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" placeholder="'.$m[$i][2].'" value="'.$inputVal.'" ';

						$onkeyupHidden = "";

						if($m[$i][10] == 1){

							$onkeyup = " onkeyup=\"CreateOnkeyupScriptGrid(this);\"";

							$fvals .= $onkeyup;

							$onkeyupHidden = '<input type="hidden" id="'.$d[3].$m[$i][1].'-hidden" value="' . $m[$i][11] . '" />';//,' . $m[$i][1] . ',' . $m[$i][0] . '

							// $changeScript .= "$('[name=\"".$d[3].$m[$i][1]."[]\"]').change(function(evt) { alert('The option with value ' + $(this).val() + ' and text ' + evt.target.value + ' ' + $(this).text() + ' was selected.'); });";
						}		

						if($m[$i][12] > 0){	//Grid Calculation Value from DB

							$onchange = " onkeyup=\"CreateOnChangeScriptGridCalculation(this,".$m[$i][12].");\"";

							$fvals .= $onchange;

							// $fvals .= "readonly";
						} 
							
						$fvals .= '>'.$onkeyupHidden.'</td>';

						//$fvals .= '<td><input type="text" value="'.$inputVal.'" class="form-control" name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" /></td>';
					}

					if($m[$i][0] == 3){
					    $fvals .= '<td ><textarea class="form-control" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" placeholder="'.$m[$i][2].'" '.$m[$i][6].'>'.$inputVal.'</textarea></td>';
					}

            		if($m[$i][0] == 12){	//IF Image
            		    $gridImg = "";
            		    if($inputVal > 0){

            		        $sql1 = "SELECT * FROM kmainmedia WHERE MediaID = " . $inputVal;
							$result1 = db::getInstance()->db_select($sql1);

							if($result1['num_rows'] > 0)

							    $gridImg = '<a target="_blank" href="'.$result1['result_set'][0]['MediaFolder'].$result1['result_set'][0]['MediaName'].'"><img src="'.$result1['result_set'][0]['MediaFolder'].$result1['result_set'][0]['MediaName'].'" width="60" /></a>';
            		    }

            			$fvals .= '<td><input type="file" class="form-controlz" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" '.$m[$i][6].' />'.$gridImg.'</td>';
            		}

            		if($m[$i][0] == 6){	//IF Date BOX	added by sneha on 01-11-2023
            			$fvals .= '<td ><input type="datetime-local" value="'.$inputVal.'" class="form-control dyn1" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" placeholder="'.$m[$i][2].'" '.$m[$i][6].' /></td>';
            		}

					if($m[$i][0] == 7){	//IF Number BOX	added by pornima on 29-1-2024
            			$fvals .= '<td ><input type="number" value="'.$inputVal.'" class="form-control" name="'.$d[3].''.$m[$i][1].'[]" id="'.$m[$i][1].'" '.$m[$i][6].' /></td>';
            		}

					if($m[$i][0] == 5){ // iF DD

						if($m[$i][3] < 0){ //FROM DB

							$index = ((-1) * $m[$i][3]) - 1;
							global $moreextradb;



							$dbarray = $moreextradb[$index];



							$sql1 = "SELECT ".$dbarray[1].",".$dbarray[2]." FROM ".$dbarray[0]." ".$dbarray[3];

							

							$result1 = db::getInstance()->db_select($sql1);



							$row1 = $result1['result_set'];		

												

							$fvals .= '<td><select name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control"  ';

							$onchangeHidden = "";

							if($m[$i][8] == 1){

								$onchange = " onchange=\"CreateOnchangeScriptGrid(this);\"";

								$fvals .= $onchange;

								$onchangeHidden = '<input type="hidden" id="'.$d[3].$m[$i][1].'-hidden" value="' . $m[$i][9] . '" />';//,' . $m[$i][1] . ',' . $m[$i][0] . '

								// $changeScript .= "$('[name=\"".$d[3].$m[$i][1]."[]\"]').change(function(evt) { alert('The option with value ' + $(this).val() + ' and text ' + evt.target.value + ' ' + $(this).text() + ' was selected.'); });";
							}
							$fvals .= '><option value="0">'.$m[$i][2].'</option>';

							for($j = 0; $j < sizeof($row1); $j++){ 

								if($inputVal == $row1[$j][$dbarray[1]])
									$fvals .= '<option value="'. $row1[$j][$dbarray[1]] .'" selected>'.$row1[$j][$dbarray[2]].'</option>&nbsp;';
								else
									$fvals .= '<option value="'. $row1[$j][$dbarray[1]] .'" >'.$row1[$j][$dbarray[2]].'</option>&nbsp;';
							}
							$fvals .= '</select>'.$onchangeHidden.'</td>';
						}else{
							$fvals .= '<td><select name="'.$d[3].''.$m[$i][1].'[]" placeholder="'.$m[$i][2].'" id="'.$m[$i][1].'" class="form-control" '.$m[$i][6].' ><option value="0">'.$m[$i][2].'</option>';

							for($j = 0; $j < sizeof($r[$m[$i][3]]); $j++){
								$p = $j+1;  //value

								if($p == $inputVal) $fvals .= '<option selected value="'. $p .'" >'.$r[$m[$i][3]][$j].'</option>&nbsp;';

								else $fvals .= '<option value="'. $p .'" >'.$r[$m[$i][3]][$j].'</option>&nbsp;';
							}
							$fvals .= '</select></td>';
						}
					}
				}

				$fvals .= '</tr>';
				//$fvals .= '<td><a href="javascript:void(0)" class="btn btn-danger removes'.$d[3].'"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>';
			}	

			$fieldCount = $k;
		}

		$fldCnt = '<input type="hidden" value="'.$fieldCount.'" name="cnt'.$d[3].'" id="cnt'.$d[3].'">';

		$tableHead .= "</tr>";

		$tableHead .= "</thead>";

		//echo htmlspecialchars($tableHead);

		$footerGrid = "";

		if($d[6] == 1){	

			$footerGrid = '<tfoot><tr>'; //class="grp'.$d[3].'" id="'.$d[3].'GridFooter"

				$footerGrid .= '<td></td>';

				for($i = 0; $i < sizeof($m); $i++){	

					if($m[$i][12] > 0){	 

						$footerGrid .= '<td><input type="text" class="form-control" readonly name="" id="'.$m[$i][1].'Footer" value=""></td>';

					}else{

						$footerGrid .= '<td></td>';
					}
				}
			$footerGrid .= '</tr></tfoot>';
		}

		if(empty($v)){

			return $script . $temp . $fldCnt . $tableStart . $tableHead . $headerGridStart . "0" . $headerGridEnd . $flds . $end . $footerGrid . $tableEnd."</div></div>";
		}else{
			return $script . $temp . $fldCnt . $tableStart . $tableHead . $fvals . $end . $footerGrid . $tableEnd."</div></div>";
		}
	}

function debugArray($arr){		//DebugArray($result);
	print("<pre>".print_r($arr,true)."</pre>");
}

	//Reqd for More Grid ID Section Identifier. Also Reqd for View mode serials
	//$serials = array("a", "b", "c", "d", "e", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q");

	include 'model.php';

	if($viewpage == 1){	//VIEW MODE
	
		if(isset($_POST["delID"])){

			$delID = $_POST["delID"];
			if($delID > 0){

				$sql = "DELETE FROM " . $db[0] . " WHERE " . $db[1]." = " . $delID;
				$result = db::getInstance()->db_update($sql);

				if($result["num_rows"] > 0) echo "<script>alert('ENTRY DELETED!');</script>";
			}
		}

		$viewvals = array();

		$sqlfields = $db[0].".*";

		$sqljoin = "";

		$serialCntFrImages = 0;

		if(isset($extradb)){ 
			for($i = 0;$i < sizeof($extradb); $i++){
				//if(isset($extra))
				$temp = ""; $noFlag = 0; 
				//$mediaFlag = 0;
				for($j = 0; $j < sizeof($code); $j++){
					if($extradb[$i][0] == $code[$j][3]){

						$temp = $code[$j][0];

						if($code[$j][1] == 10) $noFlag=1; 

						if($code[$j][1] == 11) $noFlag=1; 

						if($code[$j][1] == 12) $noFlag=1; 

						if($code[$j][1] == 13) $noFlag=1; 

						break;
					}
				}

				if($noFlag) goto a;

				$sqlfields .= " , COALESCE(".$serials[$i].".".$extradb[$i][3].", '-') as ".$temp.$serials[$i];

				$sqljoin .= " LEFT JOIN ".$extradb[$i][1]." ".$serials[$i]." ON ".$db[0].".".$temp." = ".$serials[$i].".".$extradb[$i][2] ;

				//if($mediaFlag) goto a;
				a: $temp="";
			}
			$serialCntFrImages = sizeof($extradb);
		}

		//FOR MEDIA JOIN ONLY 12 on List View & 13 cannot be on List View
		for($j = 0; $j < sizeof($code); $j++){
			if($code[$j][1] == 12){ 

				$temp = $code[$j][0];
				$sqlfields .= " , IFNULL(".$serials[$i].".MediaName, '-') as ".$temp.$serials[$i]."Name";

				$sqlfields .= " , IFNULL(".$serials[$i].".MediaFolder, '-') as ".$temp.$serials[$i]."Folder";

				$sqlfields .= " , IFNULL(".$serials[$i].".MediaType, '-') as ".$temp.$serials[$i]."Type";

				$sqljoin .= " LEFT JOIN kmainmedia ".$serials[$i]." ON ".$db[0].".".$temp." = ".$serials[$i++].".MediaID";
			}
		}

		/************NEW 12 07 2020************/
		if(!isset($forceCondition)){
			$forceCondition = "";
		}

		if(strlen($forceCondition) > 2){
		    $db[2] = $forceCondition;
		}else{
			if(strlen($db[2]) > 2){

				$db[2] = " ".$db[2];
			}else{

				$db[2] = " Order by ".$db[0].'.'.$db[1]." Desc";
			}
		}
		/************NEW 12 07 2020 - added db2 condition from database************/

		$sql = "SELECT ".$sqlfields." FROM ".$db[0]." " .$sqljoin." ".$db[2]; //." Order by ".$db[0].'.'.$db[1]." Desc";

		//$sql = "SELECT ".$sqlfields." FROM ".$db[0]." " .$sqljoin." Order by ".$db[0].'.'.$db[1]." Desc";

		if($k_debug){ echo '<br/>' . $sql; }

		$result = db::getInstance()->db_select($sql);

		if($k_debug){ echo '<br/>'; print_r($result); }

		//print("<pre>".print_r($result)."</pre>");

		$sr = array();

		for($j = 0; $j < sizeof($code); $j++){

			if($code[$j][1] == 12 || $code[$j][1] == 13 ){
				$sr[$j] = $serialCntFrImages++;
			}
		}

		for($i = 0; $i < $result['num_rows']; $i++){ //WHILE LOOP FOR $row

			for($j = 0; $j < sizeof($code); $j++){
				//echo "<br />" . $code[$j][3];
				if($code[$j][1] == 14 ){

					//Grid 
					$viewvals[$i][$j] = "";

				}else{

					if($code[$j][1] == 10 ){ //DB REFERENCE WITH MULTIPLE

						$viewvals[$i][$j] = "";

					}else{

						if($code[$j][1] == 11 || $code[$j][1] == 13){ //DB REFERENCE WITH MULTIPLE

							$viewvals[$i][$j] = "";

						}else{

							if($code[$j][1] == 12){ //SINGLE MEDIA 

								$mediaType = $result['result_set'][$i][$code[$j][0].$serials[$sr[$j]]."Type"];

								if($mediaType == "jpg" || $mediaType == "jpeg" || $mediaType == "png" || $mediaType == "bmp" ){

									$viewvals[$i][$j] = "<a href='" . SITE_ROOT . $result['result_set'][$i][$code[$j][0].$serials[$sr[$j]]."Folder"] . "" . $result['result_set'][$i][$code[$j][0].$serials[$sr[$j]]."Name"]."' target='_blank'>" . "<img src='" . SITE_ROOT . $result['result_set'][$i][$code[$j][0].$serials[$sr[$j]]."Folder"] . "" . $result['result_set'][$i][$code[$j][0].$serials[$sr[$j]]."Name"]."' height='100px'>"."</a>";
								}else{

									$viewvals[$i][$j] = "<a href='" . SITE_ROOT . $result['result_set'][$i][$code[$j][0].$serials[$sr[$j]]."Folder"] . "" . $result['result_set'][$i][$code[$j][0].$serials[$sr[$j]]."Name"]."' target='_blank'>" . $result['result_set'][$i][$code[$j][0].$serials[$sr[$j]]."Name"]."</a>";
								}
							}else{							

								/*REMOVED EMAIL FROM DB REFERENCE 10 03 2021*/							

								if($code[$j][1] == 9 || $code[$j][1] == 10 || $code[$j][3] < 0 ) //DB REFERENCE 

									$viewvals[$i][$j] = $result['result_set'][$i][$code[$j][0].$serials[(-1*$code[$j][3])-1]];
								else 

									$viewvals[$i][$j] = $result['result_set'][$i][$code[$j][0]];
							}
						}
					}
				}
			}

			//$serialCntFrImages++;
			//print_r($viewvals); exit();

			$viewvals[$i][$j] = $result['result_set'][$i][$db[1]];

			// print_r($db); exit();
			//print_r($viewvals);
		}
		$k_table_title = "List View";
// 		$k_table_button_link = $_SERVER['PHP_SELF'].'?form='.$FormID;
// 		$k_table_button = "Add New";

		if($pageAccessForUser['AddBtn'] == 1 || $bypassUserRoles) { 
    		$k_table_button_link = $_SERVER['PHP_SELF'].'?form='.$FormID;
    		$k_table_button = "Add New";
		}

		//$k_print_title = "Print Title";
		$k_table_headings = '<tr>';

		for($i=0;$i<sizeof($viewcode); $i++){

			// $k_table_headings .= '<th>'.$viewcode[$i][1].'</th>';

            if($viewcode[$i][1] == "Edit"){

		        if($pageAccessForUser['EditBtn'] == 1 || $bypassUserRoles)

			        $k_table_headings .= '<th>'.$viewcode[$i][1].'</th>';
		    }else{

		        if($viewcode[$i][1] == "Delete"){

    		        if($pageAccessForUser['DeleteBtn'] == 1 || $bypassUserRoles)

    			        $k_table_headings .= '<th>'.$viewcode[$i][1].'</th>';

    		    }else{

    		        if($viewcode[$i][1] == "Action"){

        		        if($pageAccessForUser['OtherBtn'] == 1 || $bypassUserRoles)

        			        $k_table_headings .= '<th>'.$viewcode[$i][1].'</th>';
        		    }else{

        		        $k_table_headings .= '<th>'.$viewcode[$i][1].'</th>';
        		    }
    		    }
		    }
		}

		$k_table_headings .= '</tr>';
		$k_table_body='';
		//debugArray($viewvals);

		for($i=0;$i<sizeof($viewvals); $i++){

			$k_table_body .= '<tr>';

			for($j=0;$j<sizeof($viewcode); $j++){

				//echo '<br>'.$viewcode[$j][0].'=>'.$viewvals[$i][$viewcode[$j][0]];

				if($viewcode[$j][0] >= 0){	

					$temp = $viewvals[$i][$viewcode[$j][0]];

					//echo $viewcode[$j][0];

					if($code[$viewcode[$j][0]][1] == 2){ //if SELECT

						if($temp == 0) $temp = '-';

						else $temp = $radio[$code[$viewcode[$j][0]][3]][$temp-1];
					}

					if($code[$viewcode[$j][0]][1] == 6){ //if DATE

						if (gettype($temp) == "string"){

							$temp =  date("d-m-Y", strtotime($temp));

						}else{

							$temp = $temp->format('d-m-Y');
						}
					}

					if($code[$viewcode[$j][0]][1] == 12){ //if MEDIA

						//$temp =  "<img src=";
					}

					$k_table_body .= '<td>'.$temp.'</td>';			
				}

				if($viewcode[$j][0] == -1){ //Serial Number
					$x = $i + 1 ; 
					$k_table_body .= '<td>'. $x .'</td>';		
				}

				if($viewcode[$j][0] == -2){	//EDIT 	

					$x = '<form name="form"  action="'.$_SERVER['PHP_SELF'].'?form='.$FormID.'" method="POST">

							<input type="hidden" name="editID" value='.$viewvals[$i][sizeof($viewvals[$i])-1].' />							

							<button class="btn btn-primary bizbtn" style="font-size;10px;" type="submit" data-toggle="tooltip" data-placement="bottom" title="edit" ><i class="fa fa-pencil"></i></button>

						 </form>' ; 

					$k_table_body .= '<td>'. $x .'</td>';		
				}

				if($viewcode[$j][0] == -9){	//View 	

					$viewlink = "";

					if(isset($viewcodeextra)){

						for($p = 0; $p < sizeof($viewcodeextra); $p++){

							if($viewcodeextra[$p][0] == -3){

								$viewlink = $viewcodeextra[$p][1];
								break;
							}
						}
					}

					$x = '<form action="' . $viewlink . '?view='.$viewvals[$i][sizeof($viewvals[$i])-1].'" method="POST">

							<input type="hidden" name="viewID" value='.$viewvals[$i][sizeof($viewvals[$i])-1].' />

							<button class="btn btn-primary bizbtn" style="font-size;10px;" type="submit" data-toggle="tooltip" data-placement="bottom" title="view" ><i class="fa fa-eye"></i></button>

						 </form>' ; 

					$k_table_body .= '<td>'. $x .'</td>';		
				}

				if($viewcode[$j][0] == -3){	//DELETE 	

					$viewlink = "";

					if(isset($viewcodeextra)){

						for($p = 0; $p < sizeof($viewcodeextra); $p++){

							if($viewcodeextra[$p][0] == -3){

								$viewlink = $viewcodeextra[$p][1];

								break;
							}
						}
					}

					$x = '<form  method="POST">
							<input type="hidden" name="delID" value='.$viewvals[$i][sizeof($viewvals[$i])-1].' />

							<button class="btn btn-danger bizbtn" style="font-size;10px;" type="submit" data-toggle="tooltip" data-placement="bottom" title="view" ><i class="fa fa-trash"></i></button>

						 </form>' ; 
					$k_table_body .= '<td>'. $x .'</td>';		
				}

				if($viewcode[$j][0] == -4){	//OTHER BTN 	
					$x = '<form action="' . $viewSettings[7] . '" method="POST">

							<input type="hidden" name="editID" value='.$viewvals[$i][sizeof($viewvals[$i])-1].' />

							<button class="btn btn-primary bizbtn" style="font-size;10px;" type="submit" data-toggle="tooltip" data-placement="bottom" title="" ><i class="fa '.$viewSettings[6].'"></i></button>
						 </form>' ; 

					$k_table_body .= '<td>'. $x .'</td>';		
				}
			}

			$k_table_body .= '</tr>';
		}

		include "k_files/k_table.php";
	}
	else{	

		if($editID > 0){ //EDIT MODE

			$sql = "SELECT * FROM ".$db[0]." WHERE ".$db[1]." =".$editID;

			$result = db::getInstance()->db_select($sql);
			// print_($result);
			for($i = 0; $i < $result['num_rows']; $i++){ //WHILE LOOP FOR $row
				
				for($j = 0; $j < sizeof($code); $j++){
					
					if($code[$j][1] == 10){  //FOR MAPPING TABLE Multi-Select
						$temp = array();
						//SELECT * FROM map_vendor_categories LEFT JOIN categories ON map_vendor_categories.cat_id = categories.cat_id WHERE vendor_id = 1
						
						$mapArray  = $extradb[((-1)*$code[$j][3])-1][5];
						
						$joinTable = $extradb[((-1)*$code[$j][3])-1][1];
						
						$joinIndex = $extradb[((-1)*$code[$j][3])-1][2];
						
						$mapTable = $mapArray[0];
						
						$mapIndex = $mapArray[1];
						
						$mapVariant = $mapArray[2];
						
						$sql = 'SELECT * FROM ' . $mapTable  . ' WHERE ' . $mapIndex . ' = ' . $editID ;///. ' LEFT JOIN ' . $joinTable . ' ON ' . $mapTable.'.'.$mapVariant . ' = ' . $joinTable.'.'.$joinIndex . ' WHERE ' . $mapIndex . ' = ' . $editID ;
						
						$rs = db::getInstance()->db_select($sql);
						
						//	print_r($rs['result_set']);
						
						$code[$j][4] = $rs['result_set'];
					}else{
					

						if($code[$j][1] == 11 || $code[$j][1] == 9){  //FOR MAPPING TABLE Multi-Select

							$temp = array();

							$mapArray  = $extradb[((-1)*$code[$j][3])-1];

							$joinTable = $extradb[((-1)*$code[$j][3])-1][1];

							$joinIndex = $extradb[((-1)*$code[$j][3])-1][2];

							$mapTable = $mapArray[6];

							$mapIndex = $mapArray[7];

							$mapVariant = $mapArray[8];

							$sql = 'SELECT * FROM ' . $mapTable  . ' WHERE ' . $mapIndex . ' = ' . $editID ;

							///. ' LEFT JOIN ' . $joinTable . ' ON ' . $mapTable.'.'.$mapVariant . ' = ' . $joinTable.'.'.$joinIndex . ' WHERE ' . $mapIndex . ' = ' . $editID ;

							$rs = db::getInstance()->db_select($sql);

							//print_r($rs['result_set']);

							$code[$j][4] = $rs['result_set'];

						}else{

							if($code[$j][1] == 13){  //FOR MAPPING TABLE Multi-Select

							    //echo "<br />" . $code[$j][1];
							    //print_r($code[$j]);
								//echo "<br />";
								//print_r($extradb[((-1)*$code[$j][3])-1]);
          
					        	$temp = array();

								$mapArray  = $extradb[((-1)*$code[$j][3])-1];

								$joinTable = "kmainmedia";

								$joinIndex = "MediaId";

								$mapTable = $mapArray[6];

								$mapIndex = $mapArray[7];

								$mapVariant = $mapArray[8];

								$sql = 'SELECT kmainmedia.MediaId, kmainmedia.MediaName, kmainmedia.MediaType, kmainmedia.MediaFolder FROM ' . $mapTable . ' LEFT JOIN ' . $joinTable . ' ON ' . $mapTable.'.' .$mapVariant . ' = ' . $joinTable . '.' . $joinIndex . ' WHERE ' . $mapIndex . ' = ' . $editID ;

								$rs = db::getInstance()->db_select($sql);

								//print_r($rs['result_set']);

								$images = "";

								for($z = 0; $z < sizeof($rs['result_set']); $z++){

									$images .= "
									        <div id='".$code[$j][0]."".$rs['result_set'][$z]['MediaId']."' style='display: inline'>
    									        <a href='".SITE_ROOT.$rs['result_set'][$z]['MediaFolder'].$rs['result_set'][$z]['MediaName']."' target='_blank'><img src='".SITE_ROOT.$rs['result_set'][$z]['MediaFolder'].$rs['result_set'][$z]['MediaName']."' width='100' height='auto' style='/*max-height:100px;*/ margin-right:10px;border: 1px solid #d7d7d7a6;margin-top: 7px;'/></a>
    									        <span onclick='deleteImage(13,\"".$code[$j][0]."\",".$rs['result_set'][$z]['MediaId'].");' style='background: #d8d8d8;position: absolute;margin-left: -23px;margin-top: -3px;color: white;width: 21px;text-align: center;border-radius: 21px;cursor: pointer;'>X</span>
									        </div>";
								}
								// if($z > 0){
								    // $code[$j][4] = "<div class='' id='".$arr[0]."Img'>" . $images . "</div>";
								// }else{
								    $code[$j][4] = "$images";
								// }					

							}else{

								if($code[$j][1] == 12){

									//echo "<br />" . $code[$j][1];
									//print_r($code[$j]);
									//echo "<br />";
									//print_r($extradb[((-1)*$code[$j][3])-1]);

					                $temp = array();
									$mapArray  = $extradb[((-1)*$code[$j][3])-1];

									$joinTable = $extradb[((-1)*$code[$j][3])-1][1];

									$joinIndex = $extradb[((-1)*$code[$j][3])-1][2];

									$sql = 'SELECT kmainmedia.MediaId, kmainmedia.MediaName, kmainmedia.MediaType, kmainmedia.MediaFolder FROM kmainmedia 

									LEFT JOIN ' . $db[0] . ' ON ' . $db[0] . '.' . $code[$j][0].' = kmainmedia.MediaID'.' WHERE ' . $db[0] . '.' . $db[1] . ' = ' . $editID ;

									$rs = db::getInstance()->db_select($sql);

									$images = ""; //print_r($rs);

									for($z = 0; $z < sizeof($rs['result_set']); $z++){

										$images .= "

									        <div id='".$code[$j][0]."".$rs['result_set'][$z]['MediaId']."' style='display: inline'>

    									        <a href='".SITE_ROOT.$rs['result_set'][$z]['MediaFolder'].$rs['result_set'][$z]['MediaName']."' target='_blank'><img src='".SITE_ROOT.$rs['result_set'][$z]['MediaFolder'].$rs['result_set'][$z]['MediaName']."' width='100' height='auto' style='/*max-height:100px;*/ margin-right:10px;border: 1px solid #d7d7d7a6;margin-top: 7px;'/></a>

										        <span onclick='deleteImage(12,\"".$code[$j][0]."\",".$rs['result_set'][$z]['MediaId'].");' style='background: #d8d8d8;position: absolute;margin-left: -23px;margin-top: -3px;color: white;width: 21px;text-align: center;border-radius: 21px;cursor: pointer;'>X</span>

    										</div>";
									}
									$code[$j][4] = $images;
								}else{
									if($code[$j][1] == 14){
										//Ignore GRIDS
									}else{
									  //  echo $code[$j][1] . "<br />";
										$code[$j][4] = $result['result_set'][$i][$code[$j][0]];
									}
								}
							}
						}
					}
				}
			}

			$buttonname = "UPDATE";
			$k_table_title = "Edit";
			$buttonsaveaddmore = "UpdateAddMore";
			//	print_r($result);
			//SCRIPT FOR DELETING IMAGE
			?>
			<script>
			    function deleteImage(fieldType, fieldName, mediaID){
			        var fid = $("#FormID").val();

			        $.ajax({

                        url: "<?php echo SITE_ROOT; ?>admin/form-api.php", 
                        type: 'POST',
                        data: {apicase:'1', fid: fid, fieldType:fieldType, fieldName:fieldName, mediaID:mediaID},
                        dataType: 'json',
                        success: function(result){
                            console.log(result);
                            if(result.error){

                                alert(result.error_msg);
                                //console.log(result.error_msg);
                            }else{

								alert(result.data);

                                if(mediaID === undefined){

                                    $('#' + fieldName).html("Image deleted.");

                                    setTimeout(
                                        function() {
                                            $('#' + fieldName).html("");
                                        },
                                        2000);
                                }else{
                                    $('#' + fieldName + mediaID).html("Image deleted.");
                                    setTimeout(

										function() {

                                            $('#' + fieldName + mediaID).html("");
                                        },
                                        2000);
                                }
                            }
                        },

                        error: function(xhr,status,error){
                            console.log(status, error);
                        }
                    });
			    }
			</script>

			<?php

		} else{ 	//ADD MODE

			$k_table_title = "Add New";

			$buttonname = "SAVE";

			$buttonsaveaddmore = "SaveAddMore";

			echo '<script>
				$(window).ready(function() { 
					$("form.form-bordered").on("keypress", function (event) { 
						//console.log("aaya"); 
						var keyPressed = event.keyCode || event.which; 
						if (keyPressed === 13) { 
							//alert("You pressed the Enter key!!"); 
							event.preventDefault(); 
							return false; 
						} 
					}); 
				}); </script>
			';
		}

		?>

        <style type="text/css">

            .cke_textarea_inline{

               border: 1px solid black;
            }

            input[type="file"] {
                display: none;
            }

            .custom-file-upload {

                border: 1px solid #ccc;
                display: inline-block;
                padding: 6px 12px;
                cursor: pointer;
                width: 100%;
                margin-top: 27px;
            }
        </style>
    
		<script src='assets/ckeditor/ckeditor.js'></script>

		<section class="panel">

			<header class="panel-heading"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

				<h2 class="panel-title"><?php echo isset($k_table_title)?$k_table_title : ""; ?></h2>

				<p class="panel-subtitle"><?php echo isset($k_table_subtitle) ? $k_table_subtitle : ""; ?></p>

			</header>

				<div class="panel-body">

					<form name="form" id="form" class="form-bordered" method="post" action="db.php" enctype="multipart/form-data"> <?php

					echo '<input type="hidden" name="FormID" id="FormID" value='.$FormID.' />';
	}

?>