<?php

include_once('../dbClass.php');

$output=array();

               $sql="SELECT * FROM `kmainforms`";
              $result = db::getInstance()->db_select($sql);
                if($result)
                {
                    $output["response"] = "true";
                     	for($i = 0; $i < $result['num_rows']; $i++){
                			 $output["data"][] = $result['result_set'][$i];
                		}
                }
                else
                {
                    $output["response"] = "false";
                    $output["data"] ="Data Not Found";
                }
echo json_encode($output);
?>