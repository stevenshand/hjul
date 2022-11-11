<?php 

require '../../configuration.php';
require '../../functions/harefn.php';
require '../../functions/invfn.php';

$year = $_GET["year"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
	"select count(order_id) qty, country, country_name from address, countries where countries.id = country group by country order by qty desc"; 

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result( $sales, $country, $countryName );
$stmt->store_result();
			

$total = 0;
$rows = array();
while ($stmt->fetch()) {
	
	$rows[] = array('c' => array(
						array('v'=>$countryName),
						array('v'=>$sales) )
	); 
	
	$total += $sales;		
}	

$table['cols'] = array(

    array('label' => 'Country', 'type' => 'string'),
    array('label' => 'Sales ['.$total.']', 'type' => 'number')
);
$table['rows'] = $rows;

echo json_encode($table);

?>