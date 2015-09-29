(function () {
    var suffix = '.min';

    requirejs.config({
	// To bring in buttons.bootstrap and buttons.colVis we have to allow modules without a
	// define() call, contrary to the default moodle requirejs config.
	enforceDefine: false,
	paths: {
            'datatables':  '//cdn.datatables.net/1.10.9/js/jquery.dataTables' + suffix,
	    'bootstrap':   '//cdn.datatables.net/1.10.9/js/dataTables.bootstrap' + suffix,
	    'buttons':     '//cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons' + suffix,
	    'buttons_bs':  '//cdn.datatables.net/buttons/1.0.3/js/buttons.bootstrap' + suffix,
	    'colvis':      '//cdn.datatables.net/buttons/1.0.3/js/buttons.colVis' + suffix,
	    'select':      '//cdn.datatables.net/select/1.0.1/js/dataTables.select' + suffix,
	},
    });

    require(['jquery', 'datatables', 'bootstrap', 'buttons', 'select'], function ($, datatables) {
	// The code in buttons.bootstrap and buttons.colVis needs jQuery as a global, but
	// moodle's requirejs config removes that global. As a kluge we add it back. Sorry.
	if (! jQuery)
	    window.jQuery = $;

	require(['colvis', 'buttons_bs'], function () {
	    $('table.datatable').DataTable({
		'autoWidth': false,
		'paginate': false,
		'order': [],		// disable initial sort
		'select': true,
		'dom': 'Bfrtip',	// somehow, this locates the buttons
		'buttons': [
		    //'colvis',
		    'selectAll',
		    'selectNone',
		    {
			extend: 'selected', // as defined by the "select" plugin
			text: 'Delete selected users',
			action: function (e, dt, button, config) {
			    var usernames = '';
			    dt.rows({selected: true}).every( function (rowIdx, tableLoop, rowLoop) {
				username = this.data()[0];
				usernames = (usernames == '') ? username : usernames + ',' + username;
			    });
			    var url = '/admin/tool/imsa/users.php';
			    var form = $('<form action="' + url + '" method="post">' +
					 '<input type="text" name="usernames" value="' + usernames +'" />' +
					 '</form>');
			    $('body').append(form);
			    form.submit();
			}
		    },
		    {
			extend: 'selected',
			text: 'Test two',
		    },
		],
	    });
	});
    });
})();
