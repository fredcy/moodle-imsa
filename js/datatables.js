requirejs.config({
    paths: {
        'datatables':  '//cdn.datatables.net/1.10.9/js/jquery.dataTables.min',
	'bootstrap':   '//cdn.datatables.net/1.10.9/js/dataTables.bootstrap',
	'buttons':     '//cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons',
	'buttons_bs':  '//cdn.datatables.net/buttons/1.0.3/js/buttons.bootstrap',
	'select':      '//cdn.datatables.net/select/1.0.1/js/dataTables.select.min',
    }
});

require(['jquery', 'datatables', 'bootstrap', 'buttons', 'buttons_bs', 'select'], function ($) {
    $('table.datatable').DataTable({
        'autoWidth': false,
        'paginate': false,
        'order': [],		// disable initial sort
	'select': true,
	'dom': 'Bfrtip',	// somehow, this locates the buttons
	'buttons': [
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
	    }
	],
    });
});

