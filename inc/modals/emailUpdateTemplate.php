<?php

require_once __DIR__.'/../../configuration.php';
require_once __DIR__.'/../../functions/harefn.php';

$orderId = $_GET["orderId"];

$email = emailFromOrderId($orderId);
$uuid = UUIDFromOrderId($orderId);
$orderViewLink = "http://hjul.aktivnrth.com/vieworder.php?orderId=".$uuid;

$message = "Hi! 
	
Something's changed on your order and we've sent an email to let you know.
View your order by clicking the link below. 

[link]

Thanks."

?>
	  
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4>Send Email</h4>
	
</div>
<div class="modal-body" id="modal-content">
	
	<form id="sendEmailForm">
	<input type="hidden" name="orderId" value="<?php echo $orderId ?>">
	<dl class="dl-horizontal">
	<dt>Email</dt>
	<dd><input size="40" type="text" name="email" id="email" value="<?php echo $email ?>"></dd>		
	<dt>Message</dt>
	<dd><textarea name="message" id="message" cols="45" rows="10"><?php echo $message ?></textarea></dd>
	</dl>
	
	<div>
		<button id="email_button" type="submit" class="btn btn-default">Send Email</button>
	</div>
</dl>
	
	</form>
	
</div>

<script>
	
	$( "#sendEmailForm" ).submit(function( event ) {
		event.preventDefault();
	    var form = $( this );
	    var itemData = form.serializeArray();
		
		if( !$('#email').val() ){
			alert( 'can\'t send email, no email associated with this customer' );
			return false;
		}
		
	    $.post( "/actions/sendOrderUpdateEmail.php", itemData, function(data){ 
	  	  console.log(data);	  
	  	  var reponseData = $.parseJSON(data);
	        console.log(reponseData);
	  	  var itemId = reponseData.itemId;		
	  	  if( reponseData.err ){
	  		  alert( 'error :' + reponseData.err );
	  	  }else{
			  $('#email_button').replaceWith( '<span id="email_button">Email Sent</span>' );
	  	  }
	     } );
		
		
	});
	
</script>	