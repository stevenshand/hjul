<?php
require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';
	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$componentTemplates = fetchComponentTemplates();

include 'inc/header.php';

?>
<?php include "inc/modals/newFabTemplateM.php" ?>

<div class="container">
	
	<div class="row">
		<div class="col-md-10">
			<button title="Add New Template" data-toggle="modal" data-target="#newTemplateModal" class="btn btn-default">Add Template</button>
		</div>	
	</div>
			
	<div class="row">
		<div class="col-md-6">
			
			<div class="table-responsive">
				<table id="inventoryTable" class="table table-bordered table-condensed">
					<tbody>
						<tr>
							<th>Template</th><th>Cost</th><th></th>
						</tr>
						<?php  foreach ( $componentTemplates as $componentTemplate ){ ?>
						
						<tr>
							<td><a title="view template" href="componenttemplate.php?templateId=<?php echo $componentTemplate->id ?>"><?php echo $componentTemplate->name ?></a></td>
							<td><?php echo curry($componentTemplate->cost) ?></td>
							<td>
								<a title="delete template" href="actions/deleteBOMtemplate.php?templateId=<?php echo $componentTemplate->id ?>"><span style="float:right;" class="glyphicon glyphicon-remove"></span></a>
							</td>
						</tr>		
						<?php } ?>
					</tbody>		
				</table>
			</div>	
		</div>	
	</div>

<script>
	
	$( "#newTemplateForm" ).submit(function( event ) {
		event.preventDefault();
	  var form = $( this );
	  var itemData = form.serializeArray();

	  $.post( "/actions/addBOMTemplate.php", itemData, function(data){
	  		  console.log(data);
	  		  var reponseData = $.parseJSON(data);
	      console.log(reponseData);
	  		  if( reponseData.err ){
	  			  alert( 'error :' + reponseData.err );
	  		  }else{
	  			 location = "/editcomponenttemplates.php";
	  		  }
	   } );
	});	
	
	
	
</script>	

</div>		

<?php $stmt->close(); ?>

<?php include 'inc/footer.php'; ?>

