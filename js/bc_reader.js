window.addEventListener( 'load', initBCReader );

function flashScreen(){
	
	$("body").stop().css("background-color", "#FF0000")
    .animate({ backgroundColor: "#FFFFF"}, 300);
}

function initBCReader(){
	console.log( 'Initiating BC reader' );
	Quagga.init({
	    inputStream : {
		name : "Live",
		type : "LiveStream",
	    numOfWorkers : navigator.hardwareConcurrency
		},
	    decoder : {
	      readers : ["code_128_reader"]
	    },
		locator : {
		  halfSample: false,
		  patchSize: "medium" // x-small, small, medium, large, x-large
		  }
	  }, function(err) {
	      if (err) {
	          console.log(err);
	          return
	      }
	      console.log("Initialization finished. Ready to start");
	      Quagga.start();
	  });

	  Quagga.onDetected(detectedBC);
	  Quagga.onProcessed(processedBC);
}

function processedBC( result ){
//	console.log( 'processed BC' );
	var drawingCtx = Quagga.canvas.ctx.overlay,
    drawingCanvas = Quagga.canvas.dom.overlay;

	if (result) {
	    /*
		if (result.boxes) {
	        drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
	        result.boxes.filter(function (box) {
	            return box !== result.box;
	        }).forEach(function (box) {
	            Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
	        });
	    }
		*/
		
		if (result.box) {
                Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
            }

            if (result.codeResult && result.codeResult.code) {
                Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
            }		
	}
				

} 

function detectedBC( result ){
	console.log( 'detected BC' );
	console.log( "code:" + result.codeResult.code );
	flashScreen();
	Quagga.stop();
	$("#codeout").html( result.codeResult.format + "<br>" + result.codeResult.code );
	location = "https://hjul.willowbike.com/editorder.php?orderId="+result.codeResult.code;
} 

