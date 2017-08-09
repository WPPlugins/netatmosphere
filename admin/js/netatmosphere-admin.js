jQuery(document).ready(function($) {
	$('.admin_form_js_confirmation').submit(function() {
		var msg = $(this).children('input[name="msg"]:first').val();
		return confirm(msg);
	});
});