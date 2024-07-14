<?php 
include_once('../cvssv3/src/Cvss3.php');
use SecurityDatabase\Cvss\Cvss3;
$output=array();

//https://capapi.kreonsolutions.in/api/cvss-api.php?params=CVSS:3.1/AV:N/AC:H/PR:H/UI:R/S:U/C:L/I:L/A:N
$params = isset($_REQUEST["params"]) ? $_REQUEST["params"] : 0;

try {
    $cvss = new Cvss3();
    $cvss->register($params);   //CVSS:3.1/AV:N/AC:H/PR:H/UI:R/S:U/C:L/I:L/A:N/E:P/RL:W/CR:L/IR:L/MAV:A/MAC:H/MPR:L/MUI:N/MS:U/MC:L/MI:L/MA:L
    
    //echo "<br >"; print_r($cvss->getScores());
    // print_r($cvss->getWeight());
    // echo "<br >"; print_r($cvss->getScores());
    // echo "<br >"; print_r($cvss->getScoresLabel());
    // echo "<br >"; print_r($cvss->getSubScores());
    // echo "<br >"; print_r($cvss->getSubScoresLabel());
    // echo "<br >"; print_r($cvss->getFormula());
    // echo "<br >"; print_r($cvss->getVector());
    
    $output2["data"] = ($cvss->getScores());
    $baseScore =  $output2["data"]['baseScore'];
    if($baseScore){
        $output["response"] = "true";
        $output["data"] = $output2["data"];
    }
    else
    {
        $output["response"] = "false";
        $output["data"] ="Data Not Found";
    }
    
} catch (Exception $e) {
    print $e->getCode() . " : " . $e->getMessage();
}
echo json_encode($output);
?>