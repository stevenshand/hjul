<?php 

require '../../configuration.php';
require '../../functions/harefn.php';
require '../../functions/invfn.php';

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$query = 
	"select month(shipping.shipping_date) as month, monthname(shipping.shipping_date) as monthName,
(select count(orders.id) from orders left join shipping on shipping.order_id = orders.id where month(shipping.shipping_date) = month and year(shipping.shipping_date)=2016 ) as tw6,
(select count(orders.id) from orders left join shipping on shipping.order_id = orders.id where month(shipping.shipping_date) = month and year(shipping.shipping_date)=2017 ) as tw7,
(select count(orders.id) from orders left join shipping on shipping.order_id = orders.id where month(shipping.shipping_date) = month and year(shipping.shipping_date)=2018 ) as tw8,
(select count(orders.id) from orders left join shipping on shipping.order_id = orders.id where month(shipping.shipping_date) = month and year(shipping.shipping_date)=2019 ) as tw9,
(select count(orders.id) from orders left join shipping on shipping.order_id = orders.id where month(shipping.shipping_date) = month and year(shipping.shipping_date)=2020 ) as tw0,
(select count(orders.id) from orders left join shipping on shipping.order_id = orders.id where month(shipping.shipping_date) = month and year(shipping.shipping_date)=2021 ) as tw1,
(select count(orders.id) from orders left join shipping on shipping.order_id = orders.id where month(shipping.shipping_date) = month and year(shipping.shipping_date)=2022 ) as tw2
from shipping 
group by month;";

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}


$sixteenTotal=0;
$seventeenTotal=0;
$eighteenTotal=0;
$nineteenTotal=0;
$twentyTotal=0;
$twentyOneTotal=0;
$twentyTwoTotal=0;

$stmt->bind_result( $month, $monthName, $sixteenShipped, $seventeenShipped, $eighteenShipped, $nineteenShipped, $twentyShipped, $twentyOneShipped, $twentyTwoShipped );
$stmt->store_result();


$total = 0;
$rows = array();
while ($stmt->fetch()) {
	
		$rows[] = array('c' => array(
							array('v'=>$monthName),
							array('v'=>$sixteenShipped),
						 	array('v'=>$seventeenShipped),
						 	array('v'=>$eighteenShipped),
						 	array('v'=>$nineteenShipped),
						 	array('v'=>$twentyShipped),
							array('v'=>$twentyOneShipped),
							array('v'=>$twentyTwoShipped))
	); 
	$sixteenTotal = $sixteenTotal+$sixteenShipped;
	$seventeenTotal = $seventeenTotal+$seventeenShipped;
	$eighteenTotal = $eighteenTotal+$eighteenShipped;
	$nineteenTotal = $nineteenTotal+$nineteenShipped;
	$twentyTotal = $twentyTotal+$twentyShipped;
	$twentyOneTotal = $twentyOneTotal+$twentyOneShipped;
	$twentyTowTotal = $twentyTwoTotal+$twentyTwoShipped;
}

$table['cols'] = array(

    array('label' => 'Month', 'type' => 'string'),
    array('label' => '2016 ('.$sixteenTotal.')', 'type' => 'number'),
	array('label' => '2017 ('.$seventeenTotal.')', 'type' => 'number'),
	array('label' => '2018 ('.$eighteenTotal.')', 'type' => 'number'),
	array('label' => '2019 ('.$nineteenTotal.')', 'type' => 'number'),
	array('label' => '2020 ('.$twentyTotal.')', 'type' => 'number'),
	array('label' => '2021 ('.$twentyOneTotal.')', 'type' => 'number'),
	array('label' => '2022 ('.$twentyTwoTotal.')', 'type' => 'number')
);

$table['rows'] = $rows;

echo json_encode($table);

?>