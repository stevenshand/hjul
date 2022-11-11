jQuery.fn.tableToCSV = function( includeHeaders ) {
    
	console.log( includeHeaders );
	
    var clean_text = function(text){
        text = text.replace(/"/g, '""');
        return '"'+text+'"';
    };
    
	$(this).each(function(){
			var table = $(this);
			var caption = $(this).find('caption').text();
			var title = [];
			var rows = [];

			$(this).find('tr').each(function(){
				if(!$(this).hasClass("no-export") ){
					console.log('adding:'+this);
					var data = [];
					$(this).find('th').each(function(){
						if( includeHeaders ){
	                    	var text = clean_text($(this).text());
							title.push(text);
						}
					});
					$(this).find('td').each(function(){
	                    var text = clean_text($(this).text());
						data.push(text);
						});
					data = data.join(",");
					rows.push(data);
					}else{
						console.log('skipping:'+this);
					}
				});
			title = title.join(",");
			rows = rows.join("\n");

			var csv = title + rows;
			var uri = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
			var download_link = document.createElement('a');
			download_link.href = uri;
			var ts = new Date().getTime();
			if(caption==""){
				download_link.download = ts+".csv";
			} else {
				download_link.download = caption+"-"+ts+".csv";
			}
			document.body.appendChild(download_link);
			download_link.click();
			document.body.removeChild(download_link);
	});
    
};
