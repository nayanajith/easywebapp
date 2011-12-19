<h3>Verify your details and do the payment</h3>
<p>Verify your details and click back for any corrections click either pay offline to bank or pay online </p>
<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}

$reg_arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['convocation_reg']." WHERE reg_no='".strtoupper($_SESSION['username'])."'",Q_RET_ARRAY);
$reg_arr=$reg_arr[0];

echo"<table cellpadding='5'>";
echo"<tr><td style='font-weight:bold'>Index number</td><td>".$reg_arr['index_no']."</td></tr>";
echo"<tr><td style='font-weight:bold'>Name in English</td><td style='font-size:150%'>".strtoupper($reg_arr['name_in_english'])."</td></tr>";
echo"<tr><td style='font-weight:bold'>Name in Sinhala</td><td style='font-size:150%'>".$reg_arr['name_in_sinhala']."</td></tr>";
echo"<tr><td style='font-weight:bold'>Name in Tamil</td><td style='font-size:150%'>".$reg_arr['name_in_tamil']."</td></tr>";
echo"<tr><td style='font-weight:bold'>Degree awarded</td><td>".$reg_arr['awarded_in']."</td></tr>";
echo"<tr><td style='font-weight:bold'>Number of guest tickets</td><td>".$reg_arr['guest_tickets']."</td></tr>";
echo"<tr><td style='font-weight:bold'>Payment for the convocation</td><td>Rs&nbsp;".sprintf("%.02f",$payment_category[$reg_arr['category']][0])."</td></tr>";
if($reg_arr['pay_online_status'] == 'ACCEPTED' || $reg_arr['pay_offline_status'] == 'ACCEPTED' ){
	echo"<tr><td style='font-weight:bold'>Payment status</td><td>COMPETED</td></tr>";
}else{
}
echo"</table>";

echo "<br><br><br><div align='right' class='buttonBar'  >
<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('registration','convocation')\">&laquo;&nbsp;Back</button>
";
if($reg_arr['pay_online_status'] == 'ACCEPTED' || $reg_arr['pay_offline_status'] == 'ACCEPTED' ){
}else{
	echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('registration','pay_offline')\">Pay offline to bank</button>";
	echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('registration','pay_online')\">Pay online&nbsp;&raquo;</button>";
}
echo "</div>";
?>
