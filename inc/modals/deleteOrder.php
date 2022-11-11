<?php

require_once __DIR__.'/../../configuration.php';
require_once __DIR__.'/../../functions/harefn.php';

$orderId = $_GET["orderId"];

?>
	  
<div class="modal-header">
	<h4>Delete Order</h4>
</div>
<div class="modal-body" id="modal-content">	
<p>If you're really, really, <i>really</i> sure you want to delete this order click the button below.</p>	
<p>This <i>cannot</i> be undone</p>
<p>
	<button id="deleteButton">delete order</button>
</div>


<script>
	$( "#deleteButton" ).click(function( event ) {
		event.preventDefault();

		document.location.href = "/actions/deleteOrder.php?orderId=<?php echo $orderId ?>";
	});
</script>	