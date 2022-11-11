<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

require 'configuration.php';	
require 'functions/harefn.php';	
	

$week = '22-10-2018';

echo (countWeeklyEnquiries($week));
 
?>