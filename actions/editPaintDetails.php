<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$orderId = $_POST["orderId"];
$paintDetails = $_POST["paintDetails"];
$paintConfirmed = ( $_POST["paintConfirmed"] == "true" ? "1" : "0" );

deleteLineItems( $orderId, "pnt" );

foreach($_POST as $key => $value)
{	
    if (strstr($key, 'li_'))
    {
        $x = str_replace('li_','',$key);
        $name = str_replace('_',' ',$x);
        insertLineItem($name, $value,$orderId,"pnt");
    }
}

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$statement = "update orders set paint_confirmed = ".$paintConfirmed." where id = ".$orderId; 
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
$stmt->execute();


$statement = "delete from paint_details where order_id = ".$orderId; 
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
$stmt->execute();

$statement = "insert into paint_details (paint_details, order_id ) values (?, ?)";
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("si", $paintDetails, $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();
$mysqli->close();
	
include ( __DIR__.'/../panels/paint_details_panel.php' );

?>