$(document).ready(function() {
	$("#fruitsForm").submit(function(event) {
		var form = $(this);
		event.preventDefault();
		$.ajax({
			type: "POST",
			url: "http://localhost:8080/Slim/fruits",
			data: form.serialize(), // serializes the form's elements
			success: function(data) {
				window.location.replaces("http://localhost:8080/slimApiClient/fruits");
			}
		})
	});

	$("#fruitsEdit").submit(function(event) {
		// var id = window.location.href.split('/');
		var id = $("#id").attr("value");
		var form = $(this);
		console.log(form.serialize());
		event.preventDefault();
		$.ajax({
			type: "PUT",
			url: "http://localhost:8080/Slim/fruits/" + id,
			data: form.serialize(), // serializes the form's elements
			success: function(data) {
				window.location.replace("http://localhost:8080/slimApiClient/fruits");
			}
		});
	});

	$( ".deletebtn" ).click(function() {
		var id = $(this).attr("data-id");
		if (confirm("Are you sure you want to continue?")) {
			$.ajax({
				type: "DELETE",
				url: "http://localhost:8080/Slim/fruits/" + id,
				success: function(data) {
					alert("Successful!");
				}
			});
		}
	});

});
