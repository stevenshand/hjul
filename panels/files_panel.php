
<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$orderId = $_GET["orderId"];

$edit = false;
if( isset($_GET["mode"] ) ){
    if( $_GET["mode"] == "EDIT" ){
        $edit = true;
    }
}
		
$files =  fileList($orderId);

?>

<div class="panel-heading">

<?php if ( $edit ) {?>
  	<a href="javascript:cancelFilesEdit();"><span style="float:right;" class="glyphicon glyphicon-remove-circle"></span></a>
  <?php } else {?>
  	<a href="javascript:enableFilesEdit();"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	<?php }?>

    <h3 class="panel-title">Files</h3>
  </div>

<div class="panel-body">

   <?php if($edit) {?>
		<div id="dropBox">
			<div id="fileChooser">
				<form>	
					<label for="fileInput">Select file to upload</label>
					<input type="file" name="fileInput" id="fileInput" />	
				</form>
			</div>
			<div id="filePlaceHolder" style="display:none;">
				<dl class="dl-horizontal">
					<dt>File Name</dt>
					<dd id="fileName"></dd>	
					<dt>File Size</dt>
					<dd id="fileSize"></dd>	
					<dt></dt>
					<dd>
						<button id="fileUploadButton">upload</button>
						<div id="uploadMessage"></div>
					</dd>	
				</dl>
			</div>
		</div>

   <?php } ?>

<table class="table table-striped table-bordered table-hover table-condensed">
		<tbody>
		<tr>
			<th>Filename</th><th>Date</th><th colspan="2">Action</th>
		</tr>
<?php foreach ($files as $key => $value) {
	if(!is_dir($value)) {?>

		<tr>
			<td><?php echo $key ?></td>
			<td><?php echo date( FILEDATEFORMAT, $value ) ?></td>
			<td class="center"><a onclick="deleteFile('<?php echo $key ?>');"><span style="float:right;" class="glyphicon glyphicon-remove"></a></td>
			<td class="center"><a target="_blank" href="files/<?php echo($orderId) ?>/<?php echo($key) ?>"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></td>	
		</tr>	

<?php } } ?>
		</tbody>			
	</table>


<!--   <?php if($edit) {?>
 <button onclick="javascript:editOrderDetails(<?php echo $orderId ?>);" style="float:right;">save</button>
   <?php } ?> -->
 
</div>

 <script>

var orderId = '<?php echo $orderId ?>';


$('input[type=file]').on('change', fileUploadDisplay);

function fileUploadDisplay(e){
	var file = this.files[0];	
	
	if(file.size > 10485760){
		alert( "file size too big : > 10MB" );
		e.preventDefault();
		return false;
	}
	
	$('#fileName').html( file.name );
	$('#fileSize').html( (file.size / (1024*1024)).toFixed(2) + "MB");
	$('#fileChooser').hide();
	$('#filePlaceHolder').show();
	$('#fileUploadButton').on('click', function(event){uploadFile(event, file);});
}


function uploadFile(event, file){
	$("#uploadMessage").html( "uploading " + file.name + "..." );
	var data = new FormData();   
	data.append('file', file, file.name);
    data.append('orderId', orderId);
	
    var xhr = new XMLHttpRequest();     
    
    xhr.open('POST', 'actions/fileupload.php', true);  
    xhr.send(data);
	
    xhr.onload = function () {
        var response = JSON.parse(xhr.responseText);
        if(xhr.status === 200 && response.status == 'ok'){
			$('#filesPanel').load( "panels/files_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
        }else{
            $("#uploadMessage").html("Some problem occured, please try again.");
        }
    };
}

/*
function fileUploadDisplay(e){


$(function(){

    $("#dropBox").click(function(){
        $("#fileInput").click();
    });
    
    //prevent browsers from opening the file when its dragged and dropped
    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    //call a function to handle file upload on select file
    $('input[type=file]').on('change', fileUpload);
});

function fileUpload(event){
    //notify user about the file upload status
    $("#dropBox").html(event.target.value+" uploading...");
    
    //get selected file
    files = event.target.files;
    
    //form data check the above bullet for what it is  
    var data = new FormData();                                   

    //file data is presented as an array
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
		if(file.size > 10485760){
            //check file size (in bytes)
            $("#dropBox").html("Sorry, your file is too large (>10 MB)");
        }else{
            //append the uploadable file to FormData object
			data.append('file', file, file.name);
            data.append('orderId', orderId);
            //create a new XMLHttpRequest
            var xhr = new XMLHttpRequest();     
            
            //post file data for upload
            xhr.open('POST', 'actions/fileupload.php', true);  
            xhr.send(data);
            xhr.onload = function () {
                //get response and show the uploading status
                var response = JSON.parse(xhr.responseText);
                if(xhr.status === 200 && response.status == 'ok'){
                    
					$('#filesPanel').load( "panels/files_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
//					$("#dropBox").html("File has been uploaded successfully. Click to upload another.");
                }else if(response.status == 'type_err'){
                    $("#dropBox").html("Please choose an images file. Click to upload another.");
                }else{
                    $("#dropBox").html("Some problem occured, please try again.");
                }
            };
        }
    }
}	
*/
	function deleteFile(fileName){
				
		var parray = [
			{ name: "fileName", value: fileName },
			{ name: "orderId", value: orderId }
		];	

	  	var parameters = $.param( parray, true );

		var url = "actions/deletefile.php?" + parameters;
	  	  
		  $('#filesPanel').load( url, function(){
//			  $('#filesPanel').load( "panels/files_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
		  } )
	} 

	function enableFilesEdit(){
		$('#filesPanel').load( "panels/files_panel.php?mode=EDIT&orderId=<?php echo $orderId ?>" );
	} 

	function cancelFilesEdit(){
		$('#filesPanel').load( "panels/files_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
	} 


 </script>  

