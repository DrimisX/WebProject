// --------------------
// 
// Title: books.js
// 
// Version: 1.0
// 
// Date: October 10, 2014
// 
// By: Jeff Codling
// --------------------

$(document).ready(function() {
	getData();
});

function getData() {
	var path='books.rpgle';
	$.ajax({
		url: path,
		type: 'post',
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
					$(".bookcontainer").html(template(jsonData));
				},
				error: errorAlert2
		});
	}
	,
		error: errorAlert
	});
}

function errorAlert(ehr,reason,ex) {
	console.log(ehr);
	alert("1st Request was not successful: "+reason+" "+ex);
}
function errorAlert2(ehr,reason,ex) {
	alert("2nd Request was not successful: "+reason+" "+ex);
}
