<?php 

require '../configuration.php';
require '../functions/harefn.php';
require '../functions/invfn.php';

$list = fetchInvLocations(); 
	
echo json_encode($list);

?>