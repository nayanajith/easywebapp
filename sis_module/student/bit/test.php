<?php

echo "a";
//Enable disable Errors
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);



$GLOBALS['CONNECTION'] = mysql_connect('localhost', 'root', 'letmein');

mysql_select_DB('bit', $GLOBALS['CONNECTION']);


$query="
   select m.Index_No,m.E2104,g.gpv  
   from bit_all m, grades g 
   WHERE m.A2104='2010' and m.E2104=g.grade 
   order by g.grade
   ";

echo $query;

$result  = mysql_query($query, $GLOBALS['CONNECTION']);

while($row = mysql_fetch_array($result)){
   echo $row['Index_No'].",".$row['E2104'].",".$row['gpv']."<br>";
}

?>
