<?php 

require '../../configuration.php';
require '../../functions/harefn.php';
require '../../functions/invfn.php';

$year = $_GET["year"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
/*
$query = 
	"select count(id), month(order_date) as monthNum, monthname(order_date) as month from orders where year(order_date) = ".$year." group by month order by monthNum"; 
*/

$query = "select month(order_date) as mth, monthname(order_date) as monthName,
	(select count(id) from orders where year(order_date)=2016 and month(order_date) = mth and internal = 0 ) as twsix,
	(select count(id) from orders where year(order_date)=2017 and month(order_date) = mth and internal = 0 ) as twsev,
	(select count(id) from orders where year(order_date)=2018 and month(order_date) = mth and internal = 0 ) as tweig,
	(select count(id) from orders where year(order_date)=2019 and month(order_date) = mth and internal = 0 ) as twnin,
	(select count(id) from orders where year(order_date)=2020 and month(order_date) = mth and internal = 0 ) as twzero,
	(select count(id) from orders where year(order_date)=2021 and month(order_date) = mth and internal = 0 ) as twone,
	(select count(id) from orders where year(order_date)=2022 and month(order_date) = mth and internal = 0 ) as twtwo
from orders group by mth;";

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result( $month, $monthName, $sixteenSales, $seventeenSales, $eighteenSales, $nineteenSales, $zeroSales, $oneSales, $twoSales );
$stmt->store_result();
			

$total16 = 0;
$total17 = 0;
$total18 = 0;
$total19 = 0;
$total20 = 0;
$total21 = 0;
$total22 = 0;

$rows = array();
while ($stmt->fetch()) {

	$total16 = $total16 + $sixteenSales;
	$total17 = $total17 + $seventeenSales;
	$total18 = $total18 + $eighteenSales;
	$total19 = $total19 + $nineteenSales;
	$total20 = $total20 + $zeroSales;
	$total21 = $total21 + $oneSales;
	$total22 = $total22 + $twoSales;

	$rows[] = array('c' => array(
						array('v'=>$monthName),
						array('v'=>$total16),
					 	array('v'=>$total17),
						array('v'=>$total18),
						array('v'=>$total19),
						array('v'=>$total20),
						array('v'=>$total21),
						array('v'=>$total22))
	); 
}

$table['cols'] = array(

    array('label' => 'Month', 'type' => 'string'),
    array('label' => '2016 Sales', 'type' => 'number'),
	array('label' => '2017 Sales', 'type' => 'number'),
	array('label' => '2018 Sales', 'type' => 'number'),
	array('label' => '2019 Sales', 'type' => 'number'),
	array('label' => '2020 Sales', 'type' => 'number'),
	array('label' => '2021 Sales', 'type' => 'number'),
	array('label' => '2022 Sales', 'type' => 'number')
);

$table['rows'] = $rows;

echo json_encode($table);

?>