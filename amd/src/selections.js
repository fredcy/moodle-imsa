define(['jquery', 'core/log'], function ($, logger) {
    function trace(msg) {
	logger.debug("tool_imsa/selections: " + msg);
    }
    
    return {
	init: function (form_id, selector) {
	    trace("selector = " + selector);
	    var form = $('#' + form_id);
	    if (form.length) {
		trace("form " + form_id + " found");
	    } else {
		logger.error("tool_imsa/selections: form " + form_id + " not found");
		return;
	    }
	    var collect_selections = function () {
		// Collect ids of selected elements and put into hidden form element.
		var selected_elmts = $(selector);
		var selections = selected_elmts.map(function() {
		    return $(this).attr('id');
		}).get().join();
		trace("selections = " + selections);
		var input = $('<input type="hidden" name="selections" value="' + selections + '" />');
		form.append(input);
	    };

	    // Attach above handler to the form 'submit' event
	    form.submit(collect_selections);
	},
    };
});
