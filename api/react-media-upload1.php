<?php
    // header("Access-Control-Allow-Origin: *");
    // header("Access-Control-Allow-Methods: PUT, GET, POST, OPTIONS");
    // header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    
    $folderPath = "";
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
//     print_r($request);
//     exit();
// stdClass Object
// (
//     [image] => data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAT0AAAB7CAIAAACJj8APAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAKHSURBVHhe7dfLVeswFEBR6qIg6qEamqEYiBzs+BOHx/C87D2KrCvZk7NYvHwBNbqFHt1Cj26hR7fQo1vo0S306BZ6dAs9uoUe3UKPbqFHt9CjW+jRLfToFnp0Cz26hR7dQo9uoUe30KNb6NEt9OgWenQLPbqFHt1Cj26hR7fQo1vo0S306BZ6dAs9uoUe3UKPbqFHt9CjW+jRLfToFnp0Cz26hR7dQo9uoUe30KNb6NEt9OgWenQLPbqFHt1Cj26hR7fQ80Tdfry9rL19XJ59vr9ef8wuQ6/vnz+L6/5sfr4ZGZceDqxuHPvLcn3b5thvnwFbz9XtoYWHwWybvIxeF7eRfaST9akxMP8ez1fD09n1nG75d7o9C2Z0tm9yMo88GrhurCbWBc9u27rlb3R7EsxhZzGNfNzpcHE9u051/fvm/GW65aGn/f/2tz90Y/q028mjsH5mlgsO75ks79Atf+Pv7UkwS1MH15Fx8mRg2F08lscQl5fplr/R7ebhan0a7nzPmD0td1/ivdtuzx58Btzx5N3u4tvUNW3dTlyW18XqnjF/v69xeNPp7rbdelrd/ww4evZuL6ZmfuwG1lvL3uaebXA34/nh8ehxsf+WB58BO0/ULfw3dAs9uoUe3UKPbqFHt9CjW+jRLfToFnp0Cz26hR7dQo9uoUe30KNb6NEt9OgWenQLPbqFHt1Cj26hR7fQo1vo0S306BZ6dAs9uoUe3UKPbqFHt9CjW+jRLfToFnp0Cz26hR7dQo9uoUe30KNb6NEt9OgWenQLPbqFHt1Cj26hR7fQo1vo0S306BZ6dAs9uoUe3UKPbqFHt9CjW+jRLfToFnp0Cz26hR7dQo9uoebr6xsSHIlqHbOL8AAAAABJRU5ErkJggg==
//     [Caption] => This is an Image
//     [MediaFolder] => 
//     [Ext] => png
// )$request
      
    include_once('../dbClass.php');
    $output=array();
    $output["response"] = "false";
	$output["data"] = "Please enter all fields.";

      
    if(isset($request->image)){
        $Ext = 	isset($request->Ext) ? ($request->Ext) : "";
        
        $image_parts = explode(";base64,", $request->image);
          
        $image_type_aux = explode("image/", $image_parts[0]);
          
        $image_type = $image_type_aux[1];
          
        $image_base64 = base64_decode($image_parts[1]);
          
        $file = $folderPath . uniqid() . '.' . $Ext;
        
        file_put_contents($file, $image_base64); 
        
        
        $Caption = 	isset($request->Caption) ? ($request->Caption) : "";
        $MediaFolder = 	isset($request->MediaFolder) ? ($request->MediaFolder) : "";
        $sql="INSERT INTO kmainmedia (MediaName,MediaFolder) values ('".$file."','".$MediaFolder."')";
    	$result = db::getInstance()->db_insertQuery($sql);
    	$lastid = $result['last_id'];
    	$output["response"] = "true";
        $output["data"]=$result;

    }
    
        echo json_encode($output);
    	exit();
?>