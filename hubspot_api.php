<?php

/*

require __DIR__.'/configuration.php';
require  __DIR__.'/httpful/httpful.phar';	

$uri="https://api.hubapi.com/contacts/v1/lists/all/contacts/recent?count=3&hapikey=".HUBSPOT_API_KEY;
$response = \Httpful\Request::get($uri)->send();

$entries = $response->body->contacts;
		
?>

<html>
<head>

	<script src="https://code.jquery.com/jquery-3.4.1.js"></script>

</head>	
<body>
	<?php
		
	foreach( $entries as $entry ){
	
		$ID="999999";
		echo( '<span data-id="'.$ID.'" class="deleteUser">delete</span>&nbsp;<br>');
		$idProfile = $entry->{'identity-profiles'}; 
	 	// print_r($idProfile[0]);
	 	// print_r($idProfile[0]->identities[0]->value);
		// echo($entry);
	 	echo( '<br>' );
	 	echo( '<hr>' );
	
	

	}
		
	?>


<script>
	$(document).ready(function() {
	    $(".deleteUser").on("click", deleteUser);
	});

	function deleteUser() {

		
		alert('delete');
	    var id = $(this).data("id");
		$delurl = "https://api.hubapi.com/contacts/v1/contact/vid/"+id+"?hapikey=<?php echo(HUBSPOT_API_KEY)?>";
	    var confirmation = confirm("are you sure?");
	    if (confirmation) {
	        $.ajax({
	            type: "DELETE",
	            url: $delurl
	        });
	    } else {
	        return false;
	    }
	};
</script>	
</body>	
</html>		

?>