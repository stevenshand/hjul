<?php 

require '../configuration.php';
require '../functions/harefn.php';
require '../functions/invfn.php';

$list = fetchAllPOStatuses(true); 
	
echo json_encode($list);

?>