define(['jquery', 'core/log'], function ($, logger) {
    function trace(msg) {
	logger.debug("tool_imsa>>users: " + msg);
    }
    
    return {
	init: function (form_id, selector) {
	    var form = $('#' + form_id);
	    if (! selector) {
		selector = "tr td:first-child";
	    }
	    trace("selector = " + selector);
	    var collect_usernames = function () {
		var username_elmts = form.find(selector);
		var usernames = username_elmts.map(function() {
		    return $(this).text();
		}).get().join();
		trace("usernames = " + usernames);
		var input = $('<input type="hidden" name="usernames" value="' + usernames + '" />');
		form.append(input);
	    };

	    form.submit(collect_usernames);
	},
    };
});
