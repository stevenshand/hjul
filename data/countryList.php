<?php 

require '../configuration.php';
require '../functions/harefn.php';
require '../functions/invfn.php';

$list = fetchCountriesArray();
	
echo json_encode($list);

?>