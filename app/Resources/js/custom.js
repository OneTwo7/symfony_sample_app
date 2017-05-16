"use strict";
function customScript () {
	// micropost form validations
	$("#form_picture").bind("change", function () {
		var size_in_megabytes = this.files[0].size/1024/1024;
    if (size_in_megabytes > 5) {
      alert('Maximum file size is 5MB. Please choose a smaller file.');
    }
	});

	// follow button Ajax call
	var $form = $("#follow_form form");
	$form.submit(function (event) {
		event.preventDefault();
		var $data = $(this).serialize();
		$.ajax({
			url: $form.attr('action'),
			type: $form.attr('method'),
			data: $data,
			success: function (html) {
				$form.html($(html).find("#follow_form").eq(0).html());
				$("#followers").html($(html).find("#followers").eq(0).html());
			}
		});
	});

	// unfollow button Ajax call
	var $unfollow_button = $("#follow_form a");
	$unfollow_button.on("click", function (event) {
		event.preventDefault();
		$.ajax({
			url: $unfollow_button.attr('href'),
			success: function (html) {
				$("#follow_form").html($(html).find("#follow_form").eq(0).html());
				$("#followers").html($(html).find("#followers").eq(0).html());
			}
		});
	});
}

customScript();

$(document).ajaxComplete(customScript);