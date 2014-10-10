$(document).ready(function() {
	getData();
});

function getData() {
	var path='books.rpgle';
	$.ajax({
		url: path,
		dataType: 'json',
		cache: false,
		success: function(jsonData)
		{
			var path='books.tmpl';
			var template;
	
			$.ajax({
				url: path,
				cache: false,
				success: function(source) {
					template=Handlebars.compile(source);
					$("body").html(template(jsonData));
				},
				error: errorAlert
		});
	}
	,
		error: errorAlert
	});
}

function errorAlert(ehr,reason,ex) {
	alert("Request was not successful: "+reason+" "+ex);
}
