<?php
namespace tool_imsa;

function js_datatables() {
    // Return javascript code that activates DataTables. I can't get this to
    // work as an "amd" module as "requirejs" is not defined there and I need
    // to set the external paths.
    return file_get_contents(__DIR__ . "/js/datatables.js");
}
