<?php
setlocale(LC_MONETARY, 'en_GB.UTF-8');

include_once __DIR__.'/../configuration.php';
require  __DIR__.'/../httpful/httpful.phar';//phar(p)	

function curry( $amount ){
//    echo( money_format('%.2n', $amount ) );

//    echo( $amount );

    if( !isset($amount) )
        $amount = 0;
    $fmt = numfmt_create( 'en_EN', NumberFormatter::CURRENCY );
    return numfmt_format_currency($fmt, $amount, "GBP");
}

function steph( $amount ){
	return( money_format('%.2n', $amount ) );
}


function fetchComponentTemplates(){

	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$query = "select name, id from component_template_details order by name";
		
	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($name,$id);	
	$stmt->store_result();
	
	$numRows = $stmt->num_rows;
	
	$componentTemplates = array();
	while( $stmt->fetch() ){
		$componentTemplate = new stdClass();
		$componentTemplate->name=$name;
		$componentTemplate->id=$id;
		$componentTemplate->cost=calculateBOMCost($id);
		$componentTemplates[] = $componentTemplate;
	}	
	
	return $componentTemplates;		
}

function calculateOrderBOMCost($orderId){

    $connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $query = "select sum( (inv_items.cost*order_bom.qty) ) from order_bom left join inv_items on inv_items.id=order_bom.item where orderID =".$orderId;

    if (!($stmt = $connection->prepare($query))) {
        echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    $stmt->bind_result($bomCost);
    $stmt->store_result();
    $stmt->fetch();

    return $bomCost;
}


function calculateBOMCost($bomId){
	
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$query = "select sum( (inv_items.cost*component_template.qty) ) from component_template left join inv_items on inv_items.id=component_template.item where template_id =".$bomId;

	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($bomCost);
	$stmt->store_result();
	$stmt->fetch();

	return $bomCost;
}


function fetchCustomerMonths(){
	
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select month(order_date) as mnt, year(order_date) as yr from orders group by mnt, yr order by yr DESC, mnt DESC";
	
	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($month,$year);
	$stmt->store_result();
	
	$numRows = $stmt->num_rows;
	
	$orderDates = array();
	while( $stmt->fetch() ){
		$orderDate = new stdClass();
		$orderDate->mt=$month;
		$orderDate->yr=$year;
		$orderDates[] = $orderDate;
	}
	
	return $orderDates;		
}

function fetchGeoData($town,$country){
	
	$baseUrl = "https://nominatim.openstreetmap.org/search?";

	if( !empty($town) || !empty($country) ){
		$cityQuery = 'city='.urlencode($town).'&';
		$countryQuery = 'country='.urlencode($country);
		$query = ( !empty($town) ? $cityQuery : '' ).$countryQuery;
		$params="&format=json";
		$url = $baseUrl.$query.$params;
		
		// pre( $url );
		
		$curl = curl_init($url);

		$userAgent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2';
		curl_setopt($curl, CURLOPT_USERAGENT, $userAgent );
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$curl_response = curl_exec($curl);
		if ($curl_response === false) {
		    $info = curl_getinfo($curl);
		    curl_close($curl);
		    die('error occured during curl exec. Additioanl info: ' . var_export($info));
		}
		curl_close($curl);
		$decoded = json_decode($curl_response);

		// pre($decoded);

		$points = array( 'longitude'=> $decoded[0]->lon, 'latitude'=> $decoded[0]->lat );
		return $points;
	}
}



function pre($data) {
    print '<pre>' . print_r($data, true) . '</pre>';
}

$monthArr = array("January","February","March","April","May","June","July","August","September","October","November","December");

function fetchInitialRollingStockCount(){
	
	$stockItemsInBOMs = fetchStockItemsInBOMs();
	$initialStockCount = array();
		
	foreach ($stockItemsInBOMs as $value) {
		$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
		if (mysqli_connect_errno()) {
		     echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$query = "select qty, livi_stock 
			from inv_items
			WHERE id=".$value;
	
		if (!($stmt = $connection->prepare($query))) {
			echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
		}

		if (!$stmt->execute()) {
			echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
		}

		$stmt->bind_result($qty, $livi);
		$stmt->store_result();
		$stmt->fetch();
		
		$bomItem = new stdClass();
		$bomItem->item = $value; 
		$bomItem->qty = $qty; 
		$bomItem->livi = $livi; 
				
		$initialStockCount[$value] = $bomItem;
	}
		
	return $initialStockCount;
}

function fetchStockItemsInBoms(){

		$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
		if (mysqli_connect_errno()) {
		     echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$query = "select distinct(item) 
			from order_bom";
	
		if (!($stmt = $connection->prepare($query))) {
			echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
		}

		if (!$stmt->execute()) {
			echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
		}

		$stmt->bind_result($itemId);
		$stmt->store_result();
	
		$numRows = $stmt->num_rows;
	
		$bomItems = array();
		while( $stmt->fetch() ){
			$bomItems[]=$itemId;
		}
	
		return $bomItems;
}

function fetchItemQtyOnOrder( $itemId) {
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$query = "select sum(qty) from inv_po_lines where item_id = ".$itemId;

	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result( $qty );
	$stmt->store_result();
	$stmt->fetch();

	return $qty;
}


function fetchStockItemDetail( $osstockItems ){
	
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$query = "select id, short_name, qty, cost
	    FROM inv_items
	    WHERE id IN (".implode(',',$osstockItems).")";

	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result( $itemId, $name, $qty, $cost );
	$stmt->store_result();

	$numRows = $stmt->num_rows;

	$bomItems = array();
	while( $stmt->fetch() ){
		$onOrder = fetchItemQtyOnOrder($itemId);
		$message = $message."<tr><td>".$itemId."</td><td>".$name."</td><td>".$cost."</td><td>".$qty."</td><td>".$onOrder."</td></tr>";
	}
		
	return $message;
}

function fetchCountry($orderId){
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$query = "select country, countries.country_code, countries.country_name from address LEFT JOIN countries ON country = countries.id where address.order_id = ".$orderId;

	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	$stmt->bind_result($countryId, $code, $name );

	$stmt->store_result();
	$stmt->fetch();

	return $code;	
}

function fetchShippedDate($orderId){
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$query = "select shipping_date from shipping where shipping.order_id = ".$orderId;

	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	$stmt->bind_result($shippedDate);

	$stmt->store_result();
	$stmt->fetch();

	return $shippedDate;
}

function fetchTotalPayments($orderId){
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$query = "select sum(0 + ( select sum(payment_amount) from payments where payments.order_id = ".$orderId." ) )";

	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	$stmt->bind_result($totalPayments);

	$stmt->store_result();
	$stmt->fetch();

	return $totalPayments;
}

function fetchTotalPrice($orderId){
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$query = "select 
				sum( orders.shipping_cost + orders.base_price + 
					( ( select COALESCE(sum(amount),0) from line_items where line_items.order_id = ".$orderId.") )	)
		 		from orders where id = ".$orderId;
			
	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	$stmt->bind_result($totalPrice);

	$stmt->store_result();
	$stmt->fetch();

	return $totalPrice;
}

function userFileList($orderId) {
    
	$dir = FILESDIR."/".$orderId;
		
	$validExtensions = array( "pdf", "doc", "xls", "txt", "rtf", "xlsx" );
	$ignored = array('.', '..', '.svn', '.htaccess', 'archive' );

    $files = array();    
    foreach (scandir($dir) as $file) {
    	if (!in_array($file, $ignored)){
	    	$file_parts = pathinfo($file);
			if( in_array(strtolower( $file_parts['extension'] ), $validExtensions ) ){
				$files[$file] = filemtime($dir . '/' . $file);
			}
    	}
	}
    arsort($files);

    return $files;
}


function imageFileList($orderId) {
    
	$dir = FILESDIR."/".$orderId;
		
	$validExtensions = array( "jpg", "jpeg", "png", "jpeg", "gif" );
	$ignored = array('.', '..', '.svn', '.htaccess', 'archive' );

    $files = array();    
    foreach (scandir($dir) as $file) {
    	if (!in_array($file, $ignored)){
	    	$file_parts = pathinfo($file);
			if( in_array(strtolower( $file_parts['extension'] ), $validExtensions ) ){
				$files[$file] = filemtime($dir . '/' . $file);
			}
    	}
	}
    arsort($files);

    return $files;
}

function fileList($orderId) {

    $files = array();

    $dir = FILESDIR."/".$orderId;
		
	$ignored = array('.', '..', '.svn', '.htaccess', 'archive' );

    if( is_dir($dir)) {
        foreach (scandir($dir) as $file) {
            if (in_array($file, $ignored)) continue;
            $files[$file] = filemtime($dir . '/' . $file);
        }
    }

    arsort($files);
//    $files = array_keys($files);

    return $files;
}


function getLineItems( $orderId, $category ){

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$itemQuery = 
"SELECT id,
		description,
		amount
FROM 	line_items 
WHERE order_id = ".$orderId." AND category = '".$category."'";

if (!($stmt = $mysqli->prepare($itemQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $description, $amount);
$stmt->store_result();
$itemsSize = $stmt->num_rows;



$lineItems = array();
while( $stmt->fetch() ){
	$lineItems[$description] = $amount;
}

return $lineItems;

}

function orderIdfromUUID( $uuid ){
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select id from orders where uuid = '".$uuid."'";
		
	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	
	if (!$stmt->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	
	$stmt->bind_result($orderId);

	$stmt->store_result();
	$stmt->fetch();
	
	return $orderId;
}

function emailFromOrderId( $orderId ){
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select email from orders where id = '".$orderId."'";
		
	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	
	if (!$stmt->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	
	$stmt->bind_result($email);

	$stmt->store_result();
	$stmt->fetch();
	
	return $email;
}

function UUIDFromOrderId( $orderId ){
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select uuid from orders where id = '".$orderId."'";
		
	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	
	if (!$stmt->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	
	$stmt->bind_result($uuid);

	$stmt->store_result();
	$stmt->fetch();
	
	return $uuid;
}

function getDisplayAddress( $orderId, $shipping ){
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$tableName = "address";
	if( $shipping ){
		$tableName = "shipping_address";
	}

	$query = "select line1, line2, line3, town, postcode, country_name from ".$tableName." LEFT JOIN countries
ON ".$tableName.".country=countries.id where order_id = ".$orderId;


	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	$stmt->bind_result($line1, $line2, $line3, $town, $postcode, $country );

	$stmt->store_result();
	$stmt->fetch();

	$sep = "<br>";

	$out = $line1.($line1 ? $sep : "").$line2.($line2 ? $sep : "").$line3.($line3 ? $sep : "").$town.($town ? $sep : "").$postcode.($postcode ? $sep : "").$country.($country ? $sep : "");
	
	return $out;
}


function deleteLineItems($orderId, $category){
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$insertStatement = "DELETE from line_items where order_id = ? and category = ?";
	if (!($stmt = $mysqli->prepare($insertStatement))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	if (!$stmt->bind_param("is", $orderId, $category )) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();

	$stmt->close();
	$mysqli->close();
}


function insertLineItem($description, $amount, $orderId, $category){
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$insertStatement = "INSERT into line_items(description, amount, category, order_id) values ( ?, ?, ?, ? )";
	if (!($stmt = $mysqli->prepare($insertStatement))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	if (!$stmt->bind_param("sssi", $description, $amount, $category, $orderId )) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();

	$stmt->close();
	$mysqli->close();
}


function totalCost($orderId){
	
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select base_price from orders where id=".$orderId;
	
	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($total);
	$stmt->store_result();
	$stmt->fetch();
	
	$lineItems = totalLineItems($orderId);
	$shipping = totalShipping($orderId);
	
	return $total + $lineItems + $shipping;
}

function totalLineItems($orderId){
	
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select COALESCE(sum(line_items.amount),0) as tot from line_items where order_id=".$orderId;

	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($total);
	$stmt->store_result();
	$stmt->fetch();
	
	return $total;
}

function totalShipping($orderId){

	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select COALESCE(shipping_cost,0) from orders where id=".$orderId;
	
	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($total);
	$stmt->store_result();
	$stmt->fetch();
	
	return $total;
}


function totalPayments($orderId){

	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select sum(payment_amount) from payments where order_id =".$orderId;  
	
	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($amount);
	$stmt->store_result();
	$stmt->fetch();
	
	return $amount;
}



function getShippingNotes($orderId){
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select shipping_notes 
		from orders
		WHERE id=".$orderId;
	
	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($notes);
	$stmt->store_result();
	$stmt->fetch();
	
	return $notes;
}

function getLineItemTotal($orderId, $category){
	
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select COALESCE(sum(amount),0) from line_items where category = '".$category."' and order_id=".$orderId;
	
	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($total);
	$stmt->store_result();
	$stmt->fetch();
	
	return $total;
}

function fetchBikesShipped(){
	$shippedCount = 0;
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$query = "select count(orders.id) from orders, shipping where MONTH(shipping.shipping_date) = MONTH(NOW()) and YEAR(shipping.shipping_date) = YEAR(NOW()) AND shipping.order_id = orders.id";
	if (!($statement = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$statement->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}
	
	$statement->bind_result($count);
	$statement->store_result();

	while( $statement->fetch() ){
		$shippedCount = $count;
	}
	
	return $shippedCount;
}

function fetchBikesSold(){
	
	$soldCount = 0;
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$query = "select count(id) from orders where MONTH(order_date) = MONTH(NOW()) and YEAR(order_date) = YEAR(NOW()) AND orders.internal < 1";
	if (!($statement = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$statement->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}
	
	$statement->bind_result($count);
	$statement->store_result();

	while( $statement->fetch() ){
		$soldCount = $count;
	}
	
	return $soldCount;
}

function wkNum($date){

	return date('W', $date );
}

function dateShift( $date, $direction, $days ){
	$shift = ( $direction == "forward" ? "+" : "-" );
	$shifted = strtotime ( $shift.$days.' day' , $date ) ;
	
	return $shifted; 		
}

function ecwDate($orderDate, $format ){
 	if( !$format )
		$format = DATEFORMAT;

	$ecd = strtotime ( '+90 day' , $orderDate ) ;
	return ( date($format, $ecd ) ); 		
}

function ecw($orderDate){
 	$ecd = strtotime ( '+90 day' , $orderDate ) ;
	$ecw = ( date('W', $ecd ) );
	return $ecw;
}

function nowWeek(){
	return date('W');
}

function isLate($orderDate){
	 $ecd = strtotime ( '+90 day' , $orderDate ) ;
	 return $ecd < time();		
 }

function fetchStatusArray(){

$statusConnection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$statusQuery = "SELECT id, status from status"; 
if (!($statusStatement = $statusConnection->prepare($statusQuery))) {
    echo "Prepare failed: (" . $statusConnection->errno . ") " . $statusConnection->error;
}

if (!$statusStatement->execute()) {
    echo "Execute failed: (" . $statusStatement->errno . ") " . $statusStatement->error;
}

$statusStatement->bind_result($statusId, $statusName );
$statusStatement->store_result();
$statusResultsSize = $statusStatement->num_rows;

$statuses = array();
while( $statusStatement->fetch() ){
	$statuses[$statusId] = $statusName;
}

return $statuses;

}

function fetchBrandsArray(){


$brandsConnection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$brandsQuery = "SELECT id, name from brands"; 
if (!($brandsStatement = $brandsConnection->prepare($brandsQuery))) {
    echo "Prepare failed: (" . $brandsConnection->errno . ") " . $brandsConnection->error;
}

if (!$brandsStatement->execute()) {
    echo "Execute failed: (" . $brandsStatement->errno . ") " . $brandsStatement->error;
}

$brandsStatement->bind_result($brandId, $brandName );
$brandsStatement->store_result();
$brandsResultsSize = $brandsStatement->num_rows;

$brands = array();
while( $brandsStatement->fetch() ){
	$brands[$brandId] = $brandName;
}

return $brands;

}


function fetchCurrentModelsArray(){

	$modelsConnection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$modelsQuery = "SELECT id, name from models where current > 0"; 
	if (!($modelsStatement = $modelsConnection->prepare($modelsQuery))) {
	    echo "Prepare failed: (" . $modelsConnection->errno . ") " . $modelsConnection->error;
	}

	if (!$modelsStatement->execute()) {
	    echo "Execute failed: (" . $modelsStatement->errno . ") " . $modelsStatement->error;
	}

	$modelsStatement->bind_result($modelId, $modelName );
	$modelsStatement->store_result();
	$modelsResultsSize = $modelsStatement->num_rows;

	$models = array();
	while( $modelsStatement->fetch() ){
		$models[$modelId] = $modelName;
	}

	return $models;

}


function fetchModelsArray(){


$modelsConnection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$modelsQuery = "SELECT id, name from models"; 
if (!($modelsStatement = $modelsConnection->prepare($modelsQuery))) {
    echo "Prepare failed: (" . $modelsConnection->errno . ") " . $modelsConnection->error;
}

if (!$modelsStatement->execute()) {
    echo "Execute failed: (" . $modelsStatement->errno . ") " . $modelsStatement->error;
}

$modelsStatement->bind_result($modelId, $modelName );
$modelsStatement->store_result();
$modelsResultsSize = $modelsStatement->num_rows;

$models = array();
while( $modelsStatement->fetch() ){
	$models[$modelId] = $modelName;
}

return $models;

}

function fetchSizesArray(){

$sizesConnection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sizesQuery = "SELECT id, size from sizes"; 
if (!($sizesStatement = $sizesConnection->prepare($sizesQuery))) {
    echo "Prepare failed: (" . $sizesConnection->errno . ") " . $sizesConnection->error;
}

if (!$sizesStatement->execute()) {
    echo "Execute failed: (" . $sizesStatement->errno . ") " . $sizesStatement->error;
}

$sizesStatement->bind_result($sizeId, $size );
$sizesStatement->store_result();
$sizesResultsSize = $sizesStatement->num_rows;

$sizes = array();
while( $sizesStatement->fetch() ){
	$sizes[$sizeId] = $size;
}


return $sizes;

}

function fetchCountriesArray(){

$countriesConnection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$countriesQuery = "SELECT id, country_name from countries"; 
if (!($countriesStatement = $countriesConnection->prepare($countriesQuery))) {
    echo "Prepare failed: (" . $countriesConnection->errno . ") " . $countriesConnection->error;
}

if (!$countriesStatement->execute()) {
    echo "Execute failed: (" . $countriesStatement->errno . ") " . $countriesStatement->error;
}

$countriesStatement->bind_result($countryId, $country );
$countriesStatement->store_result();
$countriesResultsSize = $countriesStatement->num_rows;


$countries = array();
while( $countriesStatement->fetch() ){
	$countries[$countryId] = $country;
}

return $countries;

}

function fetchTemplateName($templateId){
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select name 
		from component_template_details
		WHERE id=".$templateId;
	
	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($templateName);
	$stmt->store_result();
	$stmt->fetch();
	
	return $templateName;
}

function fetchModelName($model){
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select name 
		from models
		WHERE id=".$model;
	
	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($modelName);
	$stmt->store_result();
	$stmt->fetch();
	
	return $modelName;
}

function hasBOM($orderId){
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select orderId 
		from order_bom
		WHERE orderId=".$orderId;
	
	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($modelName);
	$stmt->store_result();
	$stmt->fetch();
	
	$numRows = $stmt->num_rows;
	
	return $numRows>0;
}

function fetchModelId($orderId){
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		
	$query = "select model 
		from orders
		WHERE id=".$orderId;
	
	if (!($stmt = $connection->prepare($query))) {
		echo "Prepare failed: (" . $connection->errno . ") " . $connection->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
	}

	$stmt->bind_result($model);
	$stmt->store_result();
	$stmt->fetch();
	
	return $model;
}

function trimForCSV( $string ){
	$str = str_replace(',', ' ', trim($string) );
	$str = str_replace(array("\n", "\r"), ' ', $str);

	return $str; 
}

?>