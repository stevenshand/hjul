<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

require( __DIR__.'/../configuration.php');
require(__DIR__.'/../functions/harefn.php');

$week = $_POST['week'];
$id = $_POST['id'];

$pref = "db_";

foreach( $_POST as $key=>$value ){
		
	if( substr($key,0,strlen($pref) ) === "db_" ){
		$dbKey = substr($key,strlen($pref),strlen($key));
		updateSnapshotValue($dbKey,$value,$week);
	}
}

function updateSnapshotValue($dbKey,$value,$week){
	// echo('updating value for ['.$dbKey.'] to ['.$value.'] for week:['.$week.']<br>' );
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$updateStatement = "UPDATE snapshot set ".$dbKey." = ? where week = '".$week."'";
	if (!($stmt = $mysqli->prepare($updateStatement))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->bind_param("s", $value ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();
	$stmt->close();
	$mysqli->close();
}


$header = 'Location: /snapshot.php?date='.$week;

// pre($header);

header($header, TRUE, 302);
exit();

?>
