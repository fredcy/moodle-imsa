requirejs.config({
    paths: {
        'datatables':  '//cdn.datatables.net/1.10.9/js/jquery.dataTables.min',
        'fixedheader': '//cdn.datatables.net/fixedheader/3.0.0/js/dataTables.fixedHeader.min'
    }
});

require(['jquery', 'datatables'], function ($) {
    $('table.datatable').dataTable({
        'bAutoWidth': false,
        'bInfo': false,
        'bPaginate': false,
        'aaSorting': [], /* disable initial sort */
    });
});
