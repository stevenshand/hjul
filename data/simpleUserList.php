<?php 

require '../configuration.php';
require '../functions/harefn.php';
require '../functions/invfn.php';

$users = userList(); 
			
echo json_encode($users);

?>