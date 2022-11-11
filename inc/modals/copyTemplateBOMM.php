<?php

$searchField;

?>


<div class="modal fade" id="copyTemplateBOMModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	  
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4>Copy BOM</h4>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<form id="newBOMForm" action="/actions/copyBOMtemplate.php" method="get">
								<input id="templateName" name="templateName" placeholder="New BOM Name">
								<input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId ?>">
								&nbsp;&nbsp;&nbsp;
								<input type="submit">  
							</form>	
						</div>
				  	</div>
			  	</div>
	  	  </div>
		</div>
	</div>
</div>
<!-- new BOMItem Modal -->

<script>
	$( "#newBOMForm" ).submit(function( event ) {
		event.preventDefault();
	  
	  	  	if(!$('#templateName').val()){
	  		alert('please choose a name for the BOM');
	  	}else{
	  		this.submit();
	  	}
	});	
</script>
