<?php

include_once('../dbClass.php');

$output=array();

    $case = $_GET['case'];

    // if($user_id!="" && $case!="")
    // {

        switch($case){
            case "1" :
               $sql="SELECT * FROM `kmainforms`";

                $result = db::getInstance()->db_select($sql);

                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
                } else {
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;

            case "2" :
                $unit = isset($_GET['unit']) ? " where UnitID IN (" . $_GET['unit'] . ")" : "";
                $sql="SELECT * from view_csa_projects " . $unit;
                   $result = db::getInstance()->db_select($sql);

                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;
            case "3" :
                $whereCon = $_GET['whereCon'];
                $sql="SELECT CONCAT('OBS/',map_cap_csa_quarterlyfeedback.ID) as OBSID, map_cap_csa_quarterlyfeedback.*, cap_csa_projectfollowup.*, view_csa_projects.* 
                        FROM `map_cap_csa_quarterlyfeedback` 
                        LEFT JOIN cap_csa_projectfollowup ON FollowUpID = cap_csa_projectfollowup.ID
                        LEFT JOIN view_csa_projects ON ProjectID = view_csa_projects.ID  " . $whereCon;
                   $result = db::getInstance()->db_select($sql);

                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;
            
            case "4" :
                $sql="SELECT * from cap_units";
                   $result = db::getInstance()->db_select($sql);

                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;
                
			case "33":
			    //https://prismtesting.in:9080/SohamMobileWCF/LoginAPIList.svc/LoginAPIListGetData?EncryptedParameters=F+53i6BMF+cJY3YRhAdYW8yd6aIrH6mtiqK6V6Wkn4sZw8UaDx38NzV6avZ7Y+hK
			    $EncryptedParameters = $_REQUEST["EncryptedParameters"];
			    $URL = $_REQUEST["URL"];
			    $output = json_decode(CallAPI("GET", $URL, array("EncryptedParameters" => $EncryptedParameters)));
			    break;
			
			case "44":
			    //https://prismtesting.in:9080/SohamMobileWCF/LoginAPIList.svc/LoginAPIListGetData?EncryptedParameters=F+53i6BMF+cJY3YRhAdYW8yd6aIrH6mtiqK6V6Wkn4sZw8UaDx38NzV6avZ7Y+hK
			   // $EncryptedParameters = $_REQUEST["EncryptedParameters"];
			    $URL = $_REQUEST["URL"];
			    $output = json_decode(CallAPI("GET", $URL, array(
			                                                "LoginType" => $_REQUEST["LoginType"],
			                                                "LoginSource" => $_REQUEST["LoginSource"],
			                                                "LoginID" => $_REQUEST["LoginID"],
			                                                "ClientCode" => $_REQUEST["ClientCode"]
			                                                )));
			    break;
			
            case "5" :
                $sql="SELECT * from cap_vaproject where ID = " . $_REQUEST["Id"];
                $result = db::getInstance()->db_select($sql);
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;
            case "6" :
                $sql="SELECT * FROM `cap_m_vachecklist` where Type = " . $_REQUEST["Type"];
                $result = db::getInstance()->db_select($sql);
                // print_r($result);
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;   
            case "7" :
                $val = $_GET["Id"];
                $projectCode = "O";
                if($val === "1") $projectCode = "W";
                if($val === "2") $projectCode = "A";
                if($val === "3") $projectCode = "I";
                if($val === "4") $projectCode = "S";
                if($val === "5") $projectCode = "H";
                if($val === "6") $projectCode = "P";
                
                $sql="SELECT ProjectNumber FROM cap_vaproject WHERE ID = (SELECT MAX(ID) FROM `cap_vaproject` WHERE ProjectID = 0 AND ProjectNumber like '".date("Y")."%')";
                $result = db::getInstance()->db_select($sql);
                // print_r($result);
                if($result["num_rows"] > 0){
                    $info = $result['result_set'][0]["ProjectNumber"];
                    $num = substr($info,strpos($info, '/', strpos($info, '/') + 1) + 1 );
                    $final = (int)$num + 1;
                }else{
                    $final = 1;
                }
                if($final < 10) 
                    $final = "00" . $final;
                else{
                    if($final < 100) 
                        $final = "0" . $final;
                }
                $output["data"]["ProjectNumber"] = date("Y") . "/" . $projectCode . "/" . $final;
                $output["response"] = "true";
                    
                break;  
			
            case "8" :
                $sql="SELECT * from cap_vaproject where ID = " . $_REQUEST["Id"];
                $result = db::getInstance()->db_select($sql);
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
            		$type = $result['result_set'][0]['ProjectType'];
            		$sql="SELECT * FROM `cap_m_vachecklist` where Hierarchy = 3 and Type = " . $type;
                    $result = db::getInstance()->db_select($sql);
                    // print_r($result);
                    if($result){
                        for($i = 0; $i < $result['num_rows']; $i++){
                            $output["data"][0]["Checklist"][] = $result['result_set'][$i];
                        }
                    }
            		
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;  
            	
            case "9" :
                $sql="SELECT * FROM `cap_vaprojectstrategy` WHERE ProjectID='". $_REQUEST["Id"]."' ORDER BY ID DESC LIMIT 1" ;
                $result = db::getInstance()->db_select($sql);
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
            		$ProjectStrategyID = $result['result_set'][0]['ID'];
            		$sql="SELECT * FROM `cap_map_automatedvulnerability` where VAPStrategyID = " . $ProjectStrategyID;
                    $result = db::getInstance()->db_select($sql);
                    if($result){
                        for($i = 0; $i < $result['num_rows']; $i++){
                            $output["data"][0]["AutomatedVulnerability"][] = $result['result_set'][$i];
                        }
                    }
                    $sql2="SELECT * FROM `cap_map_manualtests` where VAPStrategyID = " . $ProjectStrategyID;
                    $result2 = db::getInstance()->db_select($sql2);
                    if($result2){
                        for($i = 0; $i < $result2['num_rows']; $i++){
                            $output["data"][0]["ManualTests"][] = $result2['result_set'][$i];
                        }
                    }
                    $sql3="SELECT * FROM `cap_map_performanceload` where VAPStrategyID = " . $ProjectStrategyID;
                    $result3 = db::getInstance()->db_select($sql3);
                    if($result3){
                        for($i = 0; $i < $result3['num_rows']; $i++){
                            $output["data"][0]["PerformanceLoad"][] = $result3['result_set'][$i];
                        }
                    }
                    $sql4="SELECT * FROM `cap_map_performancescalability` where VAPStrategyID = " . $ProjectStrategyID;
                    $result4 = db::getInstance()->db_select($sql4);
                    if($result4){
                        for($i = 0; $i < $result4['num_rows']; $i++){
                            $output["data"][0]["PerformanceScalability"][] = $result4['result_set'][$i];
                        }
                    }
                    $sql5="SELECT * FROM `cap_map_performancespike` where VAPStrategyID = " . $ProjectStrategyID;
                    $result5 = db::getInstance()->db_select($sql5);
                    if($result5){
                        for($i = 0; $i < $result5['num_rows']; $i++){
                            $output["data"][0]["PerformanceSpike"][] = $result5['result_set'][$i];
                        }
                    }
                    $sql6="SELECT * FROM `cap_map_performancestress` where VAPStrategyID = " . $ProjectStrategyID;
                    $result6 = db::getInstance()->db_select($sql6);
                    if($result6){
                        for($i = 0; $i < $result6['num_rows']; $i++){
                            $output["data"][0]["PerformanceStress"][] = $result6['result_set'][$i];
                        }
                    }
                    $sql7="SELECT * FROM `cap_map_performancevolume` where VAPStrategyID = " . $ProjectStrategyID;
                    $result7 = db::getInstance()->db_select($sql7);
                    if($result7){
                        for($i = 0; $i < $result7['num_rows']; $i++){
                            $output["data"][0]["PerformanceVolume"][] = $result7['result_set'][$i];
                        }
                    }
                    $sql8="SELECT * FROM `cap_map_performance_endurance` where VAPStrategyID = " . $ProjectStrategyID;
                    $result8 = db::getInstance()->db_select($sql8);
                    if($result8){
                        for($i = 0; $i < $result8['num_rows']; $i++){
                            $output["data"][0]["PerformanceEndurance"][] = $result8['result_set'][$i];
                        }
                    }
            		
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;  
                
             case "10" :
                 	$label= isset($_REQUEST['label']) ? $_REQUEST['label'] : "";
                $sql="INSERT INTO `cap_m_automatedvulnerability` (`Label`) VALUES ('".$label."')";
                $result = db::getInstance()->db_insertQuery($sql);
                if($result){
                    $output["response"] = "true";
                    $output["data"][] = $result['last_id'];
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;
                
              case "11" :
                 	$label= isset($_REQUEST['label']) ? $_REQUEST['label'] : "";
                $sql="INSERT INTO `cap_testingtools` (`ToolName`) VALUES ('".$label."')";
                $result = db::getInstance()->db_insertQuery($sql);
                if($result){
                    $output["response"] = "true";
                    $output["data"][] = $result['last_id'];
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break; 
            	
            case "12" :
                $sql="SELECT * from cap_vaprojectstrategy where ProjectID = " . $_REQUEST["Id"] ."  ORDER BY ID DESC LIMIT 1";
                $result = db::getInstance()->db_select($sql);
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
            		$ID = $result['result_set'][0]['ID'];
            		$sql="SELECT a.*,b.Label as TestingLabel FROM `cap_map_manualtests` a 
                        LEFT JOIN cap_m_vachecklist b on a.TestingID = b.ID  where  VAPStrategyID = " . $ID;
                    $result = db::getInstance()->db_select($sql);
                    if($result){
                        for($i = 0; $i < $result['num_rows']; $i++){
                            $output["data"][0]["ManualTestList"][] = $result['result_set'][$i];
                        }
                    }
                    $sql="SELECT * FROM `cap_map_automatedvulnerability` left join cap_m_automatedvulnerability 
                        ON cap_map_automatedvulnerability.TestingID = cap_m_automatedvulnerability.ID where VAPStrategyID = " . $ID;
                    $result = db::getInstance()->db_select($sql);
                    if($result){
                        for($i = 0; $i < $result['num_rows']; $i++){
                            $output["data"][0]["AutoTestList"][] = $result['result_set'][$i];
                        }
                    }
                    
            		
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;  
            case "13" :
                 	$label= isset($_REQUEST['label']) ? $_REQUEST['label'] : "";
                 	$type= isset($_REQUEST['type']) ? $_REQUEST['type'] : "";
                $sql="INSERT INTO `cap_m_vachecklist` (`Label`, Type, Hierarchy) VALUES ('".$label."', '".$type."', '3')";
                $result = db::getInstance()->db_insertQuery($sql);
                if($result){
                    $output["response"] = "true";
                    $output["data"][] = $result['last_id'];
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;
			
            case "14" : //PROJECT COMPLETION (FORM 14) GET DATA ON SELECTION OF PROJECT 
                $sql="SELECT cap_vaproject.*, cap_m_ProjectType.Label as ProjType, master_typeoftest.Label as VATestType
                        from cap_vaproject 
                        LEFT JOIN cap_m_ProjectType ON cap_vaproject.ProjectType = cap_m_ProjectType.ID 
                        LEFT JOIN master_typeoftest ON cap_vaproject.TypeofTest = master_typeoftest.ID
                        
                        where cap_vaproject.ID = " . $_REQUEST["Id"];
                $result = db::getInstance()->db_select($sql);
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
            		$sql="SELECT cap_vaprojectstrategy.*, cap_va_personnel.Rank, cap_va_personnel.Name FROM `cap_vaprojectstrategy` 
            		        LEFT JOIN cap_va_personnel ON cap_vaprojectstrategy.ProjectLead = cap_va_personnel.ID
            		        where ProjectID = " . $_REQUEST["Id"];
                    $result = db::getInstance()->db_select($sql);
                    if($result){
                        for($i = 0; $i < $result['num_rows']; $i++){
                            $output["data"][0]["Strategy"][] = $result['result_set'][$i];
                        }
                    }
            		$sql="SELECT * FROM `cap_vaprojectprogress` where ProjectID = " . $_REQUEST["Id"];
                    $result = db::getInstance()->db_select($sql);
                    if($result){
                        for($i = 0; $i < $result['num_rows']; $i++){
                            $output["data"][0]["Progress"][0] = $result['result_set'][$i];
                            $sql2="SELECT * FROM `view_projectprogress_vulnerabilities` where ProgressID = " . $result['result_set'][$i]["ID"];
                            $result2 = db::getInstance()->db_select($sql2);
                            $output["data"][0]["Progress"][0]["Vulnerabilities"] = $result2['result_set'];
                            $sql2="SELECT * FROM `cap_map_cve` where ProjectProgressID = " . $result['result_set'][$i]["ID"];
                            $result2 = db::getInstance()->db_select($sql2);
                            $output["data"][0]["Progress"][0]["VulnerabilitiesCVE"] = $result2['result_set'];
                            // for($m = 0; $m < 1; $m++){
                            //     if($m == 0) {
                            //         $path = $result2['result_set'][0]["vd1Media"];
                            //         if(strlen($path) > 4){
                            //             $type = pathinfo($path, PATHINFO_EXTENSION);
                            //             $data = file_get_contents($path);
                            //             $output["data2"] =  base64_encode($data);
                            //             // echo base64_encode($data);
                            //         }
                            //     }
                            // }
                        }
                    }
            		$sql="SELECT * FROM `cap_vaprojectcompletion` where ProjectID = " . $_REQUEST["Id"];
                    $result = db::getInstance()->db_select($sql);
                    if($result){
                        for($i = 0; $i < $result['num_rows']; $i++){
                            $output["data"][0]["Completion"][] = $result['result_set'][$i];
                        }
                    }
            		
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;  
            
            case "15" :
                $sql="SELECT * from view_csa_projects where ID = " . $_REQUEST["Id"];
                $result = db::getInstance()->db_select($sql);
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break; 
            
            
            case "16" :
                $sql="SELECT * FROM cap_csa_auditcompletion
                    LEFT JOIN view_csa_projects ON cap_csa_auditcompletion.ProjectNo = view_csa_projects.ID
                    LEFT JOIN cap_csa_auditinitiation ON cap_csa_auditcompletion.ProjectNo = cap_csa_auditinitiation.ProjectNo
                    where cap_csa_auditcompletion.ProjectNo =" . $_REQUEST["Id"];
                $result = db::getInstance()->db_select($sql);
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break; 
            
            case "17" : //CSA AUDIT SCORE QUESTIONNAIRE
                $sql="SELECT cap_m_csascoring_q.`ID`, cap_m_csascoring_q.`HeadID`, cap_m_csascoring_q.`Question`, cap_m_csascoring_q.`QFID`, cap_m_csascoring_q.`CIID`, cap_m_csascoring_q.`GradingMethodID`, cap_m_csascoring_heads.Label as HeadName, cap_m_csascoring_grading.Label as GradingName, cap_m_csascoring_quantification.Label as QFName FROM `cap_m_csascoring_q` 
                        LEFT JOIN cap_m_csascoring_heads ON HeadID = cap_m_csascoring_heads.ID
                        LEFT JOIN cap_m_csascoring_grading ON GradingMethodID = cap_m_csascoring_grading.ID
                        LEFT JOIN cap_m_csascoring_quantification ON QFID = cap_m_csascoring_quantification.ID
                        ORDER BY cap_m_csascoring_q.ID";
                	/*cap_m_csascoring_controlimplemented	
                	cap_m_csascoring_yesno1	 	
                	cap_m_csascoring_yesno2*/
                $result = db::getInstance()->db_select($sql);
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
            		$sql = "SELECT * FROM cap_m_csascoring_controlimplemented";
            		$result = db::getInstance()->db_select($sql);
            		$output["CI1"] = $result['result_set'];
            		$sql = "SELECT * FROM cap_m_csascoring_yesno1";
            		$result = db::getInstance()->db_select($sql);
            		$output["CI2"] = $result['result_set'];
            		$sql = "SELECT * FROM cap_m_csascoring_yesno2";
            		$result = db::getInstance()->db_select($sql);
            		$output["CI3"] = $result['result_set'];
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break; 
             case "18" :
                 //getCompletedProjectDetails
                $sql="SELECT * from view_csa_completedprojects where ID = " . $_REQUEST["Id"];
                $result = db::getInstance()->db_select($sql);
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
            		$CompletionID = $result['result_set'][0]['CompletionID'];
            		$sql="SELECT * FROM `map_audit_ppobservations` where AuditCompletionID = " . $CompletionID;
                    $result = db::getInstance()->db_select($sql);
                    // print_r($result);
                    if($result){
                        for($i = 0; $i < $result['num_rows']; $i++){
                            $output["data"][0]["ManualObservations"][] = $result['result_set'][$i];
                        }
                    }
            		
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;  
  
             case "19" :
                 //checkAuditYearExistsInInitialization
                $sql="SELECT COUNT(ID) as cnt from cap_csa_auditscheduling where AuditScheduleYear = " . $_REQUEST["year"];
                $result = db::getInstance()->db_select($sql);
                $output["data"]["cnt"] = 0;
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"]["cnt"] = $result['result_set'][$i]["cnt"];
            		}
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break;
            
            case "20" : //CSA Project Selection on Form 19 Audit Progress
                $sql="SELECT * from view_csa_projects where ID = " . $_REQUEST["Id"];
                $result = db::getInstance()->db_select($sql);
                if($result){
                    $output["response"] = "true";
                 	for($i = 0; $i < $result['num_rows']; $i++){
            			 $output["data"][] = $result['result_set'][$i];
            		}
                }else{
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
                break; 
            
            default:
                echo "hi";
        }
   
echo json_encode($output);

// Method: POST, PUT, GET etc
// Data: array("param" => "value") ==> index.php?param=value

function CallAPI($method, $url, $data = false){
    $curl = curl_init();
    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data) $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}
?>