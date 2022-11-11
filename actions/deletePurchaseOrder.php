<?php

require('../configuration.php');
require('../functions/invfn.php');

$poId = $_GET["po_id"];

echo('1b');
deletePurchaseOrder( $poId );
echo('2b');

$header = 'Location: /purchaseorders.php'; 	

header($header, TRUE, 302);
exit();
?>