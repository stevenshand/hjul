<?php 

require '../configuration.php';
require '../functions/harefn.php';
require '../functions/invfn.php';

$list = fetchCategories(true); 
	
echo json_encode($list);

?>