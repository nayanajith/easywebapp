<?php
include_once("config.php");
include_once("student.php");

openDB2('mcq');
//$lines = file('test.csv');

/*
$query_export="SELECT order_id,product_name,qty
FROM orders
INTO OUTFILE '$csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '\"'
LINES TERMINATED BY '\n'";
*/
$csv='test.csv';
$table='';
$query_import = "LOAD DATA INFILE '$csv' 
INTO TABLE $table 
FIELDS TERMINATED BY ':' 
LINES TERMINATED BY '\\r\\n' (examId,courseId,indexNo,answers)";

$lines = file('test.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Loop through our array, show HTML source as HTML source; and line numbers too.
foreach ($lines as $line_num => $line) {
   echo "Line #<b>{$line_num}</b> : " . htmlspecialchars($line) . "<br >\n";
}

// Using the optional flags parameter since PHP 5
?>
