<?php

$fields=array(
	"rec_id"=>"Record id",
	"registration_no"=>"Registration No",
	"first_name"=>"First Name",
	"program"=>"Program",
	"status"=>"Payment Status",
	"transaction_id"=>"Transaction ID",
	"email_1"=>"email",
	"mobile_1"=>"Phone",
	"NIC"=>"nic"
);
$arr=exec_query("SELECT ".implode(',',array_keys($fields))." FROM ".$GLOBALS['MOD_P_TABLES']['registration'],Q_RET_ARRAY);

	echo "<div height='600'><table style='border:1px solid #C9D7F1;border-collapse:collapse;' border='1'>";
	echo "<tr><th colspan='5'>Postgraduate applicants</th></tr>";
	echo "<tr><th>";
	echo implode('</th><th>',array_values($fields));                                                                                          echo "</th></tr>";

	foreach($arr as $row){
		echo "<tr>";
		foreach($fields as $key => $value){
			echo "<td align='center'>".$row[$key]."</td>";
		}
		echo "</tr>";	
	}
	echo "</table></div>";

?>
