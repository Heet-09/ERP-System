<!DOCTYPE html>
<html>
<head>
    <title>Your Report Page</title>
    <!-- Include jQuery library -->
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->
    <!-- Include DevExpress CSS -->
    <!-- Include your custom CSS -->
    <!-- <link rel="stylesheet" href="index.css"> -->
</head>
<body>
	
	<!-- Your report content goes here -->
	
	<!-- Include DevExpress JavaScript -->

<!-- Include your custom JavaScript -->
<!-- <script type="text/javascript" src="index.js"></script> -->

</body>
</html>
<?php

$reportMetaConfig = array(

    "kReportHeader", 		//0 - Report Header Table				

    "ID", 			        //1 - Report Header Table PK

    "kReportFields", 		//2 - Report Fields Table

    "ReportHeaderID", 		//3 - Report Fields Table F-K

    "kReportFilters", 		//4 - Report Filter Table

    "ReportHeaderID", 		//5 - Filters Report Header Table PK

	// "kreport_template_data" //6 Report Template Data Table

	// "Template_ID"           //7 Report Template Data Table PK

    );
	

	$viewCNT = 0;
    $ReportID = isset($ReportID) ? $ReportID : 0;

		if($ReportID == 0){

			echo "No Model Exists.";

			exit();

		}

        $sql = "SELECT * FROM ".$reportMetaConfig[0]." WHERE ".$reportMetaConfig[1]." = '".$ReportID."'";
		//echo $sql;
		$result = db::getInstance()->db_select($sql);
		// if($k_debug) print_r($result);
		$viewReportSettings = array();

		for($i = 0; $i < $result['num_rows']; $i++){ //WHILE LOOP FOR $row

			$viewReportSettings[0] = $result['result_set'][$i]['ReportName'];

			$viewReportSettings[1] = $result['result_set'][$i]['ReportTitle'];

			$viewReportSettings[2] = $result['result_set'][$i]['ViewFilters'];

			// $viewReportSettings[3] = $result['result_set'][$i]['ShowSerialNum'];
			$viewReportSettings[3] = 0; //$result['result_set'][$i]['ReportWithChart'];
			$viewReportSettings[4] = 1;

			$db[0] = $result['result_set'][$i]['ViewName'];

			if($result['result_set'][$i]['ShowSerialNum'] == 1){

				$viewcode[$viewCNT][0] = -1;

				$viewcode[$viewCNT][1] = "Sr. No.";

				$viewcode[$viewCNT][2] = 0;

				$viewcode[$viewCNT][3] = "";

				$viewcode[$viewCNT][4] = 0;

				$viewCNT++;

			}

			if($i == 0) break;  //instead of TOP 1 or LIMIT 1
		}

		$sql1 = "SELECT * FROM ".$reportMetaConfig[2]." WHERE ".$reportMetaConfig[3]." = '".$ReportID."' ORDER BY OrderNo";

		$result1 = db::getInstance()->db_select($sql1);
		// if($k_debug) print_r($result1);

		for($i = 0; $i < $result1['num_rows']; $i++){ //WHILE LOOP FOR $row

				$code[$i][0] = $result1['result_set'][$i]['ViewFieldName']; 			//Db Field Name QQQQQQ

				$code[$i][1] = $result1['result_set'][$i]['DisplayName'];				//Type

				$code[$i][2] = $result1['result_set'][$i]['OrderNo'];			//Label

				$code[$i][3] = $result1['result_set'][$i]['ShowTotal'];													//Field Attribute (negative for extradb)

				$code[$i][4] = $result1['result_set'][$i]['OtherConditions'];													//DB Value

		}

		$sql2 = "SELECT * FROM ".$reportMetaConfig[4]." WHERE ".$reportMetaConfig[5]." = '".$ReportID."' ";

		$result2 = db::getInstance()->db_select($sql2);
		// if($k_debug) print_r($result2);

		for($i = 0; $i < $result2['num_rows']; $i++){ //WHILE LOOP FOR $row

				$filterCode[$i][0] = $result2['result_set'][$i]['FilterLabel']; 			//Db Field Name QQQQQQ

				$filterCode[$i][1] = $result2['result_set'][$i]['ViewFieldName'];				//Type

				$filterCode[$i][2] = $result2['result_set'][$i]['ViewFieldID'];			//Label

				$filterCode[$i][3] = $result2['result_set'][$i]['FilterType'];													//Field Attribute (negative for extradb)

				$filterCode[$i][4] = $result2['result_set'][$i]['OtherConditions'];													//DB Value

				$filterCode[$i][5] = $result2['result_set'][$i]['FieldSizeSM'];

				$filterCode[$i][6] = $result2['result_set'][$i]['FieldSizeMD'];

				$filterCode[$i][7] = $result2['result_set'][$i]['FieldSizeXS'];

		}

		// $sql3 = "SELECT * FROM ".$reportMetaConfig[6]." WHERE ".$reportMetaConfig[7]." = '".$ReportID."' ";

		// $result3 = db::getInstance()->db_select($sql3);
		// if($k_debug) print_r($result3);

		// for($i = 0; $i < $result2['num_rows']; $i++){ //WHILE LOOP FOR $row

		// 		$filterCode[$i][0] = $result2['result_set'][$i]['TemplateName']; 			

		// 		$filterCode[$i][1] = $result2['result_set'][$i]['TemplateDescription'];				

		// 		$filterCode[$i][2] = $result2['result_set'][$i]['TemplateTypeID'];		

		// 		$filterCode[$i][3] = $result2['result_set'][$i]['UserID'];												

		// 		$filterCode[$i][4] = $result2['result_set'][$i]['ReportJson'];													



		// }

?>