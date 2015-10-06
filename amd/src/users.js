define(['jquery'], function ($) {
    return {
	init: function (form_id) {
	    // When the user form is submitted generate a new form field that posts the selected usernames.
	    var form = $('#' + form_id);
	    
	    var collect_usernames = function () {
		var selected_rows = form.find('tr');
		var username_tds = selected_rows.find('td:first-child');
		var usernames = username_tds.map(function() {
		    return $(this).text();
		}).get().join();
		window.console.log("usernames: ", usernames);
		var input = $('<input type="hidden" name="usernames" value="' + usernames + '" />');
		form.append(input);
	    };

	    form.submit(collect_usernames);
	},
    };
});
