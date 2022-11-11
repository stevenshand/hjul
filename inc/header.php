<?php

$start = microtime(true);

session_start();

$uri = $_SERVER['REQUEST_URI'];
$publicPage = (strpos($uri, 'vieworder.php'));

$valid_passwords = array (
							"steven" => "horses"
						);
						
$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = ($publicPage) || ( (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]) );

if (!$validated ) {
  header('WWW-Authenticate: Basic realm="My Realm"');
  header('HTTP/1.0 401 Unauthorized');
  die ("Not authorized");
}

$bikesShipped = fetchBikesShipped();
$bikesSold = fetchBikesSold();

$logo = "willow_logo.png"; 
$contactNumber = "+44(0)7789 430 720";
$contactEmail = "info@willowbike.com";

?>

<!DOCTYPE html>
<html>
	<head>
	
	<title>Willow dashboard</title>	
		
<!-- JQUERY -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script src="js/hare.js"></script>
<script src="js/moment-min.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">

<!-- inline editing -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<!-- inline editing -->

<!-- Google charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!-- Google charts -->

<link href="css/magnafic.css" rel="stylesheet">
<script src="js/magnafic.js"></script>

<script src="js/quagga.min.js"></script>

<script src="https://cdn.jsdelivr.net/jsbarcode/3.6.0/JsBarcode.all.min.js"></script>

<!-- mapping -->
<script src='https://api.mapbox.com/mapbox-gl-js/v0.49.0/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v0.49.0/mapbox-gl.css' rel='stylesheet' />
<!-- mapping -->

<link rel="stylesheet" href="css/hare.css"/> 

</head>
<body>
	
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
	  	<?php $link =  ($publicPage ? "#" : "/" ) ?>	
      	<a class="navbar-brand" href="<?php echo $link ?>"><img alt="Home" src="/images/<?php echo $logo ?>" class="img-fluid" height="30px"></a>	  
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
	<?php if (!$publicPage) {?>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav" style="font-size:20px;">
        <li><a href="/index.php"><span title="List View" class="glyphicon glyphicon-list-alt"></span></a></li>
        <li><a href="/board.php"><span title="Schedule View" class="glyphicon glyphicon-calendar"></span></a></li>
        <li><a href="/inventory.php"><span title="Inventory" class="glyphicon glyphicon-barcode"></span></a></li>
        <!-- <li><a href="/fabinventory.php"><span title="Fabrication Inventory" class="glyphicon glyphicon-scale"></span></a></li> -->
        <li><a href="/charts.php"><span title="Visualisation" class="glyphicon glyphicon-signal"></span></a></li>
        <!-- <li><a href="/snapshot.php"><span title="Snapshot" class="glyphicon glyphicon-camera"></span></a></li> -->
      </ul>
	  
      <ul class="nav navbar-nav navbar-right">
<li><a id="logout" href="logout.php"><?php echo $user ?></a> | week: <strong><?php echo nowWeek() ?></strong> | <?php echo date(DATEFORMAT) ?> | 
		<strong><?php echo $bikesSold ?></strong> bikes sold this month | <strong><?php echo $bikesShipped ?></strong> bikes shipped this month</li>			
      </ul>
    </div><!-- /.navbar-collapse -->
	<?php } else { ?>
      <div class="nav navbar-nav navbar-right">
		  
		<span>Contact : <?php echo $contactNumber ?> | <a target="_blank" href="mailto:<?php echo $contactEmail ?>"><?php echo $contactEmail ?></a>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	 </div>		
	<?php } ?>
		
  </div><!-- /.container-fluid -->
</nav>
