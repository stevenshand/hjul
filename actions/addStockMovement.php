<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$itemId = $_GET["itemId"];
$qty = $_GET["qty"];
$direction = $_GET["direction"];
$reason = $_GET["reason"];


if( $itemId && $qty && $direction && $reason ){
	$source = ( $direction == 's' ? 'Moat Hall' : 'Other' );
	$destination = ( $direction == 'n' ? 'Other' : 'Moat Hall' );

	//update stock

}else{
	http_response_code(400);
	$message = 'Error<br>';
	$message = $message.'itemId:'.$itemId.'<br>';
	$message = $message.'qty:'.$qty.'<br>';
	$message = $message.'direction:'.$direction.'<br>';
	$message = $message.'reason:'.$reason.'<br>';
	echo( $message );
	exit;
}

?>

<h3>Successful Stock Transfer</h3>


<p><?php echo $qty ?> items with SKU of '<?php echo $itemId ?>' were moved from <?php echo $source ?> to <?php echo $destination ?>. The reason logged was :
	<blockquote><?php echo $reason ?></blockquote>
</p>	    