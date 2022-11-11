<?php
	
function fetchWeekStartDate( $date ){//should be date in format 'd-m-Y'($DATE_FMT)) 
		
	$dayofweek = date('w', strtotime($date));
	$result    = date("d-m-Y", strtotime((1 - $dayofweek).' day', strtotime($date)));
	
	return $result;
}

function refreshSnapshot($snapshot){
	
	$weeklyOrders = fetchOrdersForWeekBeginning($snapshot->week);
	
	$snapshot->frame_orders_value = calcOrdersValue($weeklyOrders, true);
	$snapshot->bike_orders_value = calcOrdersValue($weeklyOrders, false);
	$snapshot->frame_orders_units = calcOrdersUnits($weeklyOrders, true);
	$snapshot->bike_orders_units = calcOrdersUnits($weeklyOrders, false);
	
	$monthlyOrders = fetchOrdersForMonth($snapshot->week);
	$snapshot->mtd_frame_orders_value = calcOrdersValue($monthlyOrders, true);
	$snapshot->mtd_bike_orders_value = calcOrdersValue($monthlyOrders, false);
	$snapshot->mtd_frame_orders_units = calcOrdersUnits($monthlyOrders, true);
	$snapshot->mtd_bike_orders_units = calcOrdersUnits($monthlyOrders, false);
	

	$weeklySales = fetchSalesForWeekBeginning($snapshot->week);
	$snapshot->frame_sales_value = calcOrdersValue($weeklySales, true);
	$snapshot->bike_sales_value = calcOrdersValue($weeklySales, false);
	$snapshot->frame_sales_units = calcOrdersUnits($weeklySales,true);
	$snapshot->bike_sales_units = calcOrdersUnits($weeklySales, false);

	$monthlySales = fetchSalesForMonth($snapshot->week);
	$snapshot->mtd_frame_sales_value = calcOrdersValue($monthlySales, true);
	$snapshot->mtd_bike_sales_value = calcOrdersValue($monthlySales, false);
	$snapshot->mtd_frame_sales_units = calcOrdersUnits($monthlySales,true);
	$snapshot->mtd_bike_sales_units = calcOrdersUnits($monthlySales, false);
	
	$snapshot->mb_total_orders_units = 30;
	$snapshot->mb_total_orders_value = 67237;
	$snapshot->mb_total_sales_units = 25;
	$snapshot->mb_total_sales_value = 68750;
	
	
	$orderBook = fetchOrderBook($snapshot->week); 
	$snapshot->order_book_value = calcOrdersValue($orderBook, true) + calcOrdersValue($orderBook, false);
	$snapshot->order_book_units = calcOrdersUnits($orderBook, true) + calcOrdersUnits($orderBook, false);
	
	$snapshot->enquiries = countWeeklyEnquiries($snapshot->week);
	
	return $snapshot;
}

function fetchOrderBook($week){
	
	$start = new DateTime($week);
	$month = $start->format('m');	
	$year = $start->format('Y');	
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$query = 	"select 
					orders.id, frame_only, vat_exempt, sname, cancelled 
				from 
					orders 
				left join 
					shipping
				on 
					shipping.order_id = orders.id 
				where 
					shipping.shipping_date is NULL
				AND 
					cancelled = 0";
					
	// pre($query);
		
	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute() ) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	
	$stmt->bind_result( $orderId, $frameOnly, $vatExempt, $sname, $cancelled );		
	
	$stmt->store_result();

	$orders = array();
		
	while( $stmt->fetch() ){
		$order = new Order($orderId);
		$order->frameOnly = $frameOnly;
		$order->vatExempt = $vatExempt;
		$order->sname = $sname;
		$order->cancelled = $cancelled;
		array_push($orders, $order);
	}					
	
	return $orders;
}


function calcOrdersValue($orders, $frameOnly ){
	
	$totalFrames = 0;
	$totalBikes = 0;
	
	forEach( $orders as $order ){
	
		
	
		$total = fetchTotalPrice($order->orderId);
		if( !$order->vatExempt ){
			$total = round($total/1.2, 0);
		}
			
		if( $order->frameOnly ){
			$totalFrames+=$total;
		}else{
			$totalBikes+=$total;
		}
	}
	
	return $frameOnly ? $totalFrames : $totalBikes;
}

function calcOrdersUnits($orders, $frameOnly ){
		
	$totalFrames = 0;
	$totalBikes = 0;
	
	forEach( $orders as $order ){
		if( $order->frameOnly ){
			$totalFrames++;
		}else{
			$totalBikes++;
		}
	}
	
	return $frameOnly ? $totalFrames : $totalBikes;
}

function fetchOrdersForMonth($week){
	
	$start = new DateTime($week);
	$month = $start->format('m');	
	$year = $start->format('Y');	
	
	// echo ( '<br>fetching orders for month...');
	// echo ( '<br>month:'.$month);
	// echo ( '<br>year:'.$year);
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$query = "select id, frame_only from orders where MONTH(order_date) = ? AND YEAR(order_date) = ?";
	

	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->bind_param("ii", $month, $year )) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	if (!$stmt->execute() ) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	$stmt->bind_result( $orderId, $frameOnly );

	$stmt->store_result();

	$orders = array();
	while( $stmt->fetch() ){
		$order = new Order($orderId);
		$order->frameOnly = $frameOnly;
		array_push($orders, $order );
	}
	
	return $orders;
}

function fetchOrdersForWeekBeginning($week){
	
	// echo('<br>fetching orders for week:'.$week );
	
 	$end = new DateTime ( date( 'd-m-Y', strtotime($week. ' + 6 days') ) );

	$startDate = date_format ( new DateTime($week), 'Y-m-d' );
	$endDate = date_format ( $end, 'Y-m-d' );

	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$query = "select id, frame_only, vat_exempt from orders where (order_date between '".$startDate."' and '".$endDate."' )";
		
	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute() ) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	
	$stmt->bind_result( $orderId, $frameOnly, $vatExempt );		
	
	$stmt->store_result();

	$orders = array();
	while( $stmt->fetch() ){
		$order = new Order($orderId);
		$order->frameOnly = $frameOnly;
		$order->vatExempt = $vatExempt;
		array_push($orders, $order );
		// echo('<br>adding order:'.$orderId);
	}					
	
	// echo('<br>ending orders for week:'.$week );
	
	return $orders;
}

function fetchSalesForMonth($week){
	
	$start = new DateTime($week);
	$month = $start->format('m');	
	$year = $start->format('Y');	
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$query = 	"select 
					orders.id, frame_only, vat_exempt 
				from 
					orders 
				left join 
					shipping
				on 
					shipping.order_id = orders.id 
				where 
					MONTH(shipping.shipping_date) = ? 
				AND 
					YEAR(shipping.shipping_date) = ?";
					
	// pre($query);
		
	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->bind_param("ii", $month, $year )) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute() ) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	
	$stmt->bind_result( $orderId, $frameOnly, $vatExempt );		
	
	$stmt->store_result();

	$orders = array();
	while( $stmt->fetch() ){
		$order = new Order($orderId);
		$order->frameOnly = $frameOnly;
		$order->vatExempt = $vatExempt;
		array_push($orders, $order );
	}					
	
	return $orders;
}


function fetchSalesForWeekBeginning($week){
	
 	$end = new DateTime ( date( 'd-m-Y', strtotime($week. ' + 6 days') ) );

	$startDate = date_format ( new DateTime($week), 'Y-m-d' );
	$endDate = date_format ( $end, 'Y-m-d' );
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$query = 	"select 
					orders.id, frame_only, vat_exempt 
				from 
					orders 
				left join 
					shipping
				on 
					shipping.order_id = orders.id 
				where 
					shipping.shipping_date 
				between 
				'".$startDate."' and '".$endDate."'";
					
	// pre($query);
		
	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute() ) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	
	$stmt->bind_result( $orderId, $frameOnly, $vatExempt );		
	
	$stmt->store_result();

	$orders = array();
	while( $stmt->fetch() ){
		$order = new Order($orderId);
		$order->frameOnly = $frameOnly;
		$order->vatExempt = $vatExempt;
		array_push($orders, $order );
	}					
	
	return $orders;
}

function fetchSnapShot($snapshotDate){

	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$snapshotQuery = "select 	id, 
								week, 
								cash_in_bank,
								enquiries,
								web_sessions,
								mf_bike_orders_units,
								mf_frame_orders_units,
								bike_sales_units,
								frame_sales_units,
								bike_orders_value,
								frame_orders_value,
								bike_sales_value,
								frame_sales_value,
								bike_orders_units,
								frame_orders_units,
								mf_frame_orders_value,
								mf_bike_orders_value,
								mf_bike_sales_value,
								mf_frame_sales_value,
								mf_frame_sales_units,
								mf_bike_sales_units,
								mb_total_sales_units,
								mb_total_sales_value,
								mb_total_orders_value,
								mb_total_orders_units,
								mtd_frame_orders_value,
								mtd_bike_orders_value,
								mtd_bike_orders_units,
								mtd_frame_orders_units,
								mtd_frame_sales_units,
								mtd_bike_sales_units,
								mtd_bike_sales_value,
								mtd_frame_sales_value,
								order_book_value,
								order_book_units,
								notes,
								locked
					from snapshot where week = '".$snapshotDate."'";  
	// pre($snapshotQuery);
	
	if (!($stmt = $mysqli->prepare($snapshotQuery))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute() ) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	
	$stmt->bind_result(		
						$id, 
						$week, 
						$cash_in_bank, 
						$enquiries,
						$webSessions,
						$mf_bike_orders_units,	
						$mf_frame_orders_units,
						$bike_sales_units,
						$frame_sales_units,
						$bike_orders_value,
						$frame_orders_value,
						$bike_sales_value,
						$frame_sales_value,
						$bike_orders_units,
						$frame_orders_units,
						$mf_frame_orders_value,
						$mf_bike_orders_value,
						$mf_bike_sales_value,
						$mf_frame_sales_value,
						$mf_frame_sales_units,
						$mf_bike_sales_units,
						$mb_total_sales_units,
						$mb_total_sales_value,
						$mb_total_orders_value,
						$mb_total_orders_units,
						$mtd_frame_orders_value,
						$mtd_bike_orders_value,
						$mtd_bike_orders_units,
						$mtd_frame_orders_units,
						$mtd_frame_sales_units,
						$mtd_bike_sales_units,
						$mtd_bike_sales_value,
						$mtd_frame_sales_value,
						$order_book_value,
						$order_book_units,
						$notes,
						$locked
					   );
	$stmt->fetch();
	
	if( !$id ){
		return false;
	}else{
		$snapshot = new Snapshot($id);
		$snapshot->week = $week;
		$snapshot->cash_in_bank			  = $cash_in_bank;
		$snapshot->enquiries  	  		  = $enquiries;
		$snapshot->web_sessions  	  	  = $webSessions;
		$snapshot->mf_bike_orders_units	  = $mf_bike_orders_units;	
		$snapshot->mf_frame_orders_units  = $mf_frame_orders_units;
		$snapshot->bike_sales_units       = $bike_sales_units;
		$snapshot->frame_sales_units      = $frame_sales_units;
		$snapshot->bike_orders_value      = $bike_orders_value;
		$snapshot->frame_orders_value     = $frame_orders_value;
		$snapshot->bike_sales_value       = $bike_sales_value;
		$snapshot->frame_sales_value      = $frame_sales_value;
		$snapshot->bike_orders_units      = $bike_orders_units;
		$snapshot->frame_orders_units     = $frame_orders_units;
		$snapshot->mf_frame_orders_value  = $mf_frame_orders_value;
		$snapshot->mf_bike_orders_value   = $mf_bike_orders_value;
		$snapshot->mf_bike_sales_value    = $mf_bike_sales_value;
		$snapshot->mf_frame_sales_value   = $mf_frame_sales_value;
		$snapshot->mf_frame_sales_units   = $mf_frame_sales_units;
		$snapshot->mf_bike_sales_units    = $mf_bike_sales_units;
		$snapshot->mb_total_sales_units   = $mb_total_sales_units;
		$snapshot->mb_total_sales_value   = $mb_total_sales_value;
		$snapshot->mb_total_orders_value  = $mb_total_orders_value;
		$snapshot->mb_total_orders_units  = $mb_total_orders_units;
		$snapshot->mtd_frame_orders_value = $mtd_frame_orders_value;
		$snapshot->mtd_bike_orders_value  = $mtd_bike_orders_value;
		$snapshot->mtd_bike_orders_units  = $mtd_bike_orders_units;
		$snapshot->mtd_frame_orders_units = $mtd_frame_orders_units;
		$snapshot->mtd_frame_sales_units  = $mtd_frame_sales_units;
		$snapshot->mtd_bike_sales_units   = $mtd_bike_sales_units;
		$snapshot->mtd_bike_sales_value   = $mtd_bike_sales_value;
		$snapshot->mtd_frame_sales_value  = $mtd_frame_sales_value;
		$snapshot->order_book_value   	  = $order_book_value;
		$snapshot->order_book_units  	  = $order_book_units;
		$snapshot->notes  	  			  = $notes;
		$snapshot->locked  	  			  = $locked;
		
		// pre($snapshot);
		return $snapshot;
	}
	
}

function addNewSnapshot($snapshotDate){
		
	if( validateDate($snapshotDate) ){
						
		$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

		if (mysqli_connect_errno()) {
		    echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$insertStatement = "INSERT into snapshot(week) values ( ? )";
		if (!($stmt = $mysqli->prepare($insertStatement))) {
		    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		if (!$stmt->bind_param("s", $snapshotDate )) {
		    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}

		$stmt->execute();
	}
}

function validateDate($date, $format = 'd-m-Y'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function fetchPreviousSnapshotDates(){
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$snapshotQuery = "select id, week from snapshot order by id";  
	// pre($snapshotQuery);
	
	if (!($stmt = $mysqli->prepare($snapshotQuery))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute() ) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	
	$stmt->bind_result($id, $week );
	$stmt->store_result();
	
	$dates = array();
	while ($stmt->fetch()) {
		array_push( $dates, $week );
	}
	
	return $dates;
}

//Object definitions below

class Order{
	function __construct($orderId) {
		$this->orderId = $orderId;
	}
		
	public function __set($name, $value){
        $this->$name = $value;
    }
	
	public function __get($name){
		return $this->$name;
	}
	
	private $orderId;
	private $frameOnly;
	private $vatExempt;
	private $sname;
	private $cancelled;
}

class Snapshot{
	
	function __construct($id) {
		$this->id = $id;
	}
		
	public function __set($name, $value){
        $this->$name = $value;
    }
	
	public function __get($name){
		return $this->$name;
	}
	
	private $id;
	private $week;
	private $cash_in_bank;	
	private $enquiries;
	private $webSessions;
	private $order_book_units;	
	private $order_book_value;	
	private $mf_bike_orders_units;
	private $mf_frame_orders_units;
	private $bike_sales_units;
	private $frame_sales_units;
	private $bike_orders_value;
	private $frame_orders_value;
	private $bike_sales_value;
	private $frame_sales_value;
	private $bike_orders_units;
	private $frame_orders_units;
	private $mf_frame_orders_value;
	private $mf_bike_orders_value;
	private $mf_bike_sales_value;
	private $mf_frame_sales_value;
	private $mf_frame_sales_units;
	private $mf_bike_sales_units;
	private $mb_total_sales_units;
	private $mb_total_sales_value;
	private $mb_total_orders_value;
	private $mb_total_orders_units;
	private $mtd_frame_orders_value;
	private $mtd_bike_orders_value;
	private $mtd_bike_orders_units;
	private $mtd_frame_orders_units;
	private $mtd_frame_sales_units;
	private $mtd_bike_sales_units;
	private $mtd_bike_sales_value;
	private $mtd_frame_sales_value;
	private $notes;
	private $locked;
}	
	
?>