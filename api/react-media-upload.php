<?php
    // header('Access-Control-Allow-Origin: http://localhost:3000');
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');

    include_once('../dbClass.php');
	$output=array();	
    $base=$_REQUEST['image'];
    
    $img = explode(',', $base);
    $ini =substr($img[0], 11);
    $type = explode(';', $ini);
// echo $type[0];
    
    // $pos  = strpos($base, ';');
    // $type = explode(':', substr($base, 0, $pos))[1];
    // echo $type = substr($base, 5, strpos($base, ';')-5);
    
    
	$binary=base64_decode($base);
// 	header('Content-Type: bitmap; charset=utf-8');
	$filename = md5(uniqid(rand(),true)) . ".jpg";
// 	echo $filename .= ".".$type[0]; //".jpg";
// 	exit();
    $file = fopen($filename, 'wb');
    fwrite($file, $binary);
    fclose($file);
    $Caption = 	isset($_REQUEST['Caption']) ? $_REQUEST['Caption'] : "";
    $MediaFolder = 	isset($_REQUEST['MediaFolder']) ? $_REQUEST['MediaFolder'] : "";
    //echo 'Image upload complete!!, Please check your php file directory……';

if(!isset($_REQUEST['image']) )
{
    $output["response"] = "false";
	$output["data"] = "Please enter all fields.";
}

  elseif(isset($_REQUEST['image']))
 {
    if($_REQUEST['image']!="")
    {
    	
       $sql="INSERT INTO kmainmedia (MediaName,MediaFolder) 
                    values (
						'".$filename."','".$MediaFolder."'
						)";
	$result = db::getInstance()->db_insertQuery($sql);
	$lastid = $result['last_id'];
    }
 }
 $output["response"] = "true";
 $output["data"]=$result;
echo json_encode($output);
	exit();
?>