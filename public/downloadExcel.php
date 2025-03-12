<?php
include_once "../src/config.php";

$file_url = $_GET['n'];

$desired_filename = $_GET['a']; // Replace 'new_filename.xlsx' with your desired filename

header('Content-Type: application/csv');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . $desired_filename . "\"");
header('Pragma: no-cache');

// readfile(URL_EXPORTS.$file_url.".xlsx");
readfile(URL_EXPORTS . "/" . $file_url . ".xlsx");

?>