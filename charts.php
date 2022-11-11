<?php
require 'configuration.php';
require 'functions/harefn.php';

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderListQuery = 
"SELECT email,
		orders.id as orderId,
		order_date as dt,
		models.name,
		sizes.size as framesize,
		frame_only,
		fname,
		sname,
		line1,
		line2,
		line3,
		town,
		postcode,
		if( ( instr( models.name, 'rohloff' ) > 0 ), 'yes', 'no' ) 
FROM 	orders 
LEFT JOIN 
		address
ON orders.id = order_id		
LEFT JOIN 
		models
ON orders.model = models.id		
LEFT JOIN 
		shipping
ON orders.id = shipping.order_id		
LEFT JOIN 
		sizes
ON orders.size = sizes.id GROUP BY orders.id ORDER BY order_date ASC";

if (!($stmt = $mysqli->prepare($orderListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($email, $orderId, $orderDate, $modelName, $frameSize, $frameOnly, $fname, $sname, $line1, $line2, $line3, $town, $postcode, $rohloff );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

include 'inc/header.php';
?>

<script src="js/jquery.tabletoCSV.js"></script>

<div class="container">
	
	<div class="row">
		<a href="reports.php" title="Reports" class="btn btn-default">Reports</a>
	</div>	

	<div class="row">
		<div class="col-md-12">
			<div class="chart" id="salesByMonth"></div>
			<div class="chart" id="salesByMonthCum"></div>
			<div class="chart" id="shippedByMonth"></div>
			<div class="chart" id="salesByCountry"></div>
			<div class="chart" id="salesBySexCum"></div>
		</div>
	</div>	
		
		
</div>		
<script>

	$("#export").click(function(){
	  $("#abtable").tableToCSV();
  	});
	

	google.charts.load('current', {packages: ['corechart']});
	google.charts.setOnLoadCallback(annualSalesByMonth);
	google.charts.setOnLoadCallback(annualSalesByMonthCum);
	google.charts.setOnLoadCallback(shippedByMonth);
	google.charts.setOnLoadCallback(annualSalesByCountry);
	google.charts.setOnLoadCallback(salesBySexCum);
	
	
	var commonOptions = {
		height :500,
		width:1000,
			legend : {
				position : 'in',
				alignment:'end'	
			},
			annotations : {
				alwaysOutside : true
			},
			hAxis:{
				textStyle:{
					fontSize:10,
				},
				slantedText:true,
				title:''
			},
			titleTextStyle:{
					fontSize:20
			},
	        vAxis: {
	          title: ''
	        },
	        hAxis: {
	          title: ''
	        }
			
	      };
	
	
	
  	function annualSalesByMonth() {

  		var jsonData = $.ajax({
  		          url: "data/charts/annualSalesByMonth.php",
  		          dataType: "json",
  		          async: false
  		          }).responseText;
	  
  		  var data = new google.visualization.DataTable(jsonData);

  	      var options = commonOptions;
  		  options.title = "Sales by Month";


  	      var materialChart = new google.visualization.ColumnChart(document.getElementById('salesByMonth'));
  	      materialChart.draw(data, options);
  	}
	
	function annualSalesByMonthCum() {

		var jsonData = $.ajax({
		          url: "data/charts/annualSalesByMonthCumulative.php",
		          dataType: "json",
		          async: false,
				  data: { year : 2017 }
		          }).responseText;
		  
		  var data = new google.visualization.DataTable(jsonData);

	      var options = commonOptions;
		  options.title = "Sales by Month (Cumulative)";
	      options.trendlines = {}
  
	      var materialChart = new google.visualization.LineChart(document.getElementById('salesByMonthCum'));
	      materialChart.draw(data, options);
	}
	
	function shippedByMonth() {
		
		var jsonData = $.ajax({
		          url: "data/charts/shippingByMonth.php",
		          dataType: "json",
		          async: false
		          }).responseText;
		  
		  var data = new google.visualization.DataTable(jsonData);

	      var options = commonOptions;
		  options.title = "Shipped by Month";
	      
		  
	      var materialChart = new google.visualization.ColumnChart(document.getElementById('shippedByMonth'));
	      materialChart.draw(data, options);
	}
	
		
	function annualSalesByCountry() {
	
		var jsonData = $.ajax({
		          url: "data/charts/annualSalesByCountry.php",
		          dataType: "json",
		          async: false
		          }).responseText;
	  
		  var data = new google.visualization.DataTable(jsonData);

	      var options = commonOptions;
		  options.title = "Sales by Country (all time)";
	  
	      var materialChart = new google.visualization.ColumnChart(document.getElementById('salesByCountry'));
	      materialChart.draw(data, options);
	}
	
	
	function salesBySexCum() {

		var jsonData = $.ajax({
		          url: "data/charts/salesBySexCumulative.php",
		          dataType: "json",
		          async: false
				}).responseText;
		  
		  var data = new google.visualization.DataTable(jsonData);

	      var options = commonOptions;
		  options.title = "Sales by Sex (Cumulative)";
	      options.trendlines = {}
  
	      var materialChart = new google.visualization.LineChart(document.getElementById('salesBySexCum'));
	      materialChart.draw(data, options);
	}
	
</script>

<?php include 'inc/footer.php'; ?>
