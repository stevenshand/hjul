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

$query = "select month(order_date) as mth, monthname(order_date) as monthName, year(order_date) as yr,
	(select count(id) from orders where year(order_date)=2018 and month(order_date) = mth and sex = 0 ) as male,
	(select count(id) from orders where year(order_date)=2018 and month(order_date) = mth and sex = 1 ) as female
from orders group by mth;";

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result( $month, $monthName, $yr, $male, $female );
$stmt->store_result();
			

$totalMale = 0;
$totalFeale = 0;
$rows = array();
while ($stmt->fetch()) {

	$totalMale = $totalMale + $male;
	$totalFemale = $totalFemale + $female;

	$rows[] = array('c' => array(
						array('v'=>$monthName),
					 	array('v'=>$totalMale),
						array('v'=>$totalFemale))
	); 
}

$table['cols'] = array(

    array('label' => 'Month', 'type' => 'string'),
	array('label' => 'Male Sales', 'type' => 'number'),
	array('label' => 'Female Sales', 'type' => 'number')
);

$table['rows'] = $rows;

echo json_encode($table);

?>