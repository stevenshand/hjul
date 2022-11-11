<?php 

require '../configuration.php';
require '../functions/harefn.php';
require '../functions/invfn.php';

$list = fetchCategories(false);

echo json_encode($list);

?>