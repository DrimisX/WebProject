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

// function to display one book

function showbook(id,title,plot,last,first,middle) {
	$(".bookcontainer").html("<div class='fullbook'>"+
	"<img class='coverart' src='images/"+id+".jpg'>"+
	"<div class='id'>Book ID: "+id+"</div>"+
	"<div class='title'>Title: "+title+"</div>"+
	"<div class='first'>First: "+first+"</div>"+
	"<div class='middle'>Middle: "+middle+"</div>"+
	"<div class='last'>Last: "+last+"</div>"+
	"<div class='plot'>Plot: "+plot+"</div>"+
	"</div>"+
	"<div class='backbutton' onclick='goback()'>back</div>");
}

function goback() {
	$(".bookcontainer").html(""+
		"<div class='spinner'>"+
			"<div class='spinner-container container1'>"+
			"<div class='circle1'></div>"+
			"<div class='circle2'></div>"+
			"<div class='circle3'></div>"+
			"<div class='circle4'></div>"+
		"</div>"+
		"<div class='spinner-container container2'>"+
			"<div class='circle1'></div>"+
			"<div class='circle2'></div>"+
			"<div class='circle3'></div>"+
			"<div class='circle4'></div>"+
		"</div>"+
		"<div class='spinner-container container3'>"+
			"<div class='circle1'></div>"+
			"<div class='circle2'></div>"+
			"<div class='circle3'></div>"+
			"<div class='circle4'></div>"+
		"</div>"+
		"</div>");
	getData();
}
