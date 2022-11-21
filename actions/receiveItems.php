<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );
include_once ( __DIR__.'/../functions/invfn.php' );

$ordQty = $_POST["orderedQuantity"];	
$recQty = $_POST["receivedQuantity"];
$lineId = $_POST["lineId"];
$itemId = $_POST["itemId"];
$poId = $_POST["poId"];

$reload = false;

$err = "";
$msg = "";

$loc = getLocation($poId);

updateStockLevel( $itemId, $recQty, $poId );

if( $recQty == $ordQty ){
	deletePOLine( $lineId );
	$lineCount = poLineCount($poId); 
	if( $lineCount == 0 ){
		deletePurchaseOrder( $poId );
		$reload = true;
	}
}else{
	updatePOQty($lineId, $recQty);
	$reload = false;
}


http_response_code(200);
$form_data['msg'] = $msg;	
$form_data['err'] = $err;	
$form_data['itemId'] = $itemId;	
$form_data['lineId'] = $lineId;	
$form_data['ordQty'] = $ordQty;	
$form_data['recQty'] = $recQty;	
$form_data['reload'] = $reload;	

echo json_encode($form_data);


function poLineCount( $poId ){
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$sql = "select id from inv_po_lines where po_id = ?";

	if (!($stmt = $mysqli->prepare($sql))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	

	if (!$stmt->bind_param("i", $poId ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();
	$stmt->store_result();
	$resultsSize = $stmt->num_rows;
	
	return $resultsSize; 		
}


function deletePOLine( $lineId ){
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$sql = "delete from inv_po_lines where id = ?";
	if (!($stmt = $mysqli->prepare($sql))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	if (!$stmt->bind_param("i", $lineId ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();
}


function updateStockLevel( $itemId, $recQty, $poId ){

	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$sql = "update inv_items set qty = qty + ? where id = ?";
	if (!($stmt = $mysqli->prepare($sql))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	if (!$stmt->bind_param("ii", $recQty, $itemId ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();
	
	
	//-- set location
	
	$location = getLocation($poId);
	if( $location ==  0 ){
		$sql = "update inv_items set livi_stock = livi_stock + ? where id = ?";
		
		if (!($stmt = $mysqli->prepare($sql))) {
		    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}	
	
		if (!$stmt->bind_param("ii", $recQty, $itemId ) ) {
		    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}	

		$stmt->execute();
	}
	//-- set location
	
	
	
	$sql = "insert into goods_in values ( ?, ?, now() )";
	if (!($stmt = $mysqli->prepare($sql))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	if (!$stmt->bind_param("ii", $itemId, $recQty ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();

}




function updatePOQty( $lineId, $recQty ){

	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$sql = "update inv_po_lines set qty = qty - ? where id = ?";
	if (!($stmt = $mysqli->prepare($sql))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	if (!$stmt->bind_param("ii", $recQty, $lineId ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();

}

function getLocation($poId){
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$sql = "select location from inv_purchase_orders where id = ?";
	if (!($stmt = $mysqli->prepare($sql))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->bind_param("i", $poId ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($location);
	$stmt->store_result();
	$stmt->fetch();
	
	return $location;
}
?>