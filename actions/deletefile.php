<?php
include_once ( __DIR__.'/../configuration.php' );

$orderId = $_GET["orderId"];
$fileName = $_GET["fileName"];

if( isset( $orderId, $fileName ) ){
	
	$originalFileDir = FILESDIR.$orderId.'/';
	$archiveFileDir = FILESDIR.$orderId.'/archive/';
	
	if( !is_dir($archiveFileDir) ){
		$mdir = mkdir($archiveFileDir, 0777, true);
	}
	chown($archiveFileDir, "ec2-user");
	
	$archiveFilePath = $archiveFileDir.$fileName;
	$originalFilePath = $originalFileDir.$fileName;
		
	$success = rename( $originalFilePath, $archiveFilePath );
	
}
	
	include ( __DIR__.'/../panels/files_panel.php' );
	
?>
	