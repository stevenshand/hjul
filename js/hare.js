modifyStatus = function(event){
	$orderId = event.target.getAttribute("data-orderId");
	$statusId = $(event.target).val();

	$.ajax({
	  method: "POST",
	  url: "actions/updateOrderStatus.php",
	  data: { 	orderId: $orderId, 
		  		statusId: $statusId,
		  beforeSend : function(){$('#orderRow_'+$orderId).fadeOut(300)}
	   }
	})
	  .done(function( msg ) {
	    //console.log( "Data Saved: " + msg );
	  	console.log( $statusId );
		$('#orderRow_'+$orderId).fadeIn(300);
	  });

}

convertToWeekNumber = function(event) {
	var targetDate = $(event.target).val(); 	
	var d = new Date(targetDate);
	return d.getWeekNumber();
}

Date.prototype.getWeekNumber = function(){
    var d = new Date(+this);
    d.setHours(0,0,0,0);
    d.setDate(d.getDate()+4-(d.getDay()||7));
    return Math.ceil((((d-new Date(d.getFullYear(),0,1))/8.64e7)+1)/7);
};


function toggleInvoicable(orderId){

	$.ajax({
	  method: "POST",
	  url: "actions/toggleInvoicable.php",
	  data: { orderId: orderId 
	  }
	})
	  .done(function( msg ) {
	    	console.log( "Data Saved: " + msg );
		// 	  	console.log( $statusId );
		// $('#orderRow_'+$orderId).fadeIn(300);
	  });	
}

function togglePaymentPending(orderId){

	$.ajax({
	  method: "POST",
	  url: "actions/togglePaymentPending.php",
	  data: { orderId: orderId 
	  }
	})
	  .done(function( msg ) {
	    	console.log( "Data Saved: " + msg );
		// 	  	console.log( $statusId );
		// $('#orderRow_'+$orderId).fadeIn(300);
	  });	
}