<?php

$serverName = "116.73.25.184,9998";
$database = "erp";
$uid = "sa";
$pass = "Erp@123";

$connection = [
    "Database" => $database,
    "uid" => $uid,
    "pwd" => $pass
];

phpinfo();

echo "<pre>";
print_r(PDO::getAvailableDrivers());

$conn = sqlsrv_connect($serverName, $connection);
if(!$conn)
    die(print_r(sqlsrv_errors(), true));
    
echo "connection established" ;
$tsql = "SELECT ID as uid, Password, Role FROM users WHERE Username='kreon123'";
// $tsql = "INSERT INTO products (ProductName, ProductDetail) VALUES
// ('Test', '')";

$stmt = sqlsrv_query( $conn, $tsql);  

if ( $stmt )  
{  
    print_r($stmt);
    echo "<pre></pre>";
     echo "Statement executed.<br>\n";  
     while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        print_r($row);
        // echo "HIIIIII".$row['EntryDate']->format('d-m-Y');
        echo "<pre></pre>";
        
    }
}   
else   
{  
     echo "Error in statement execution.\n";  
     die( print_r( sqlsrv_errors(), true));  
}  

// map_purchaseinward_products

// purchaseinward

?>