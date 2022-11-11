<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

require 'configuration.php';
require 'functions/harefn.php';

require 'functions/snapshotfn.php';

echo ('hello');


$week = fetchWeekStartDate('12-7-2019');
$orders = fetchOrderBook($week);

foreach( $orders as $order ){
	pre($order->sname);
}


?>