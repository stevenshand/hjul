<?php 

require '../configuration.php';
require '../functions/harefn.php';
require '../functions/invfn.php';

$list = fetchAllInventoryCategoryGroups();
	
echo json_encode($list);

?>