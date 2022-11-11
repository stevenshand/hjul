<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$orderId = $_POST["orderId"];
$assemblyDetails = $_POST["assemblyDetails"];

deleteLineItems( $orderId, "ass" );

foreach($_POST as $key => $value)
{	
    if (strstr($key, 'li_'))
    {
        $x = str_replace('li_','',$key);
        $name = str_replace('_',' ',$x);
        insertLineItem($name, $value,$orderId,"ass");
    }
}

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$statement = "delete from assembly_details where order_id = ".$orderId; 
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
$stmt->execute();


$statement = "insert into assembly_details ( assembly_details, order_id ) values (?, ? )";
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("si", $assemblyDetails, $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();
$mysqli->close();
	
include ( __DIR__.'/../panels/assembly_details_panel.php' );

?>