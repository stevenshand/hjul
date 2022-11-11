<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$orderId = $_POST["orderId"];
$fabDetails = $_POST["fabDetails"];
$rohloffSerial = $_POST["rohloffSerial"];

deleteLineItems( $orderId, "fab" );

foreach($_POST as $key => $value)
{	
    if (strstr($key, 'li_'))
    {
        $x = str_replace('li_','',$key);
        $name = str_replace('_',' ',$x);
        insertLineItem($name, $value,$orderId,"fab");
    }
}

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$statement = "delete from fabrication_details where order_id = ".$orderId; 
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
$stmt->execute();


$statement = "insert into fabrication_details (fab_details, order_id) values (?, ? )";
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("si", $fabDetails, $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();


$statement = "delete from rohloff_serial where orderId = ".$orderId; 
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
$stmt->execute();

$statement = "insert into rohloff_serial (rohloffSerial, orderId) values (?, ? )";
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("ii", $rohloffSerial, $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();

$mysqli->close();
	
include ( __DIR__.'/../panels/fabrication_details_panel.php' );

?>