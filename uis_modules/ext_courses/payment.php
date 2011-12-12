<h3>Verify your details and do the payment</h3>
<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}

//reg no format YYSSSSSC : Y-> year S-> sequence C-> check digit
function gen_index_no($sequence){
	$reg_no_length	=8;
	$seq_length		=5;
	$year				=11;
	$modulus			=5;
	
	$composite_no=$year;
	for($j=$seq_length;$j>strlen($sequence);$j--){
		$composite_no.='0';
	}
	$composite_no.=$sequence;

	$check=0;
	foreach(str_split($composite_no) as $digit){
		$check+=(int)$digit;
	}
	$check=($check%$modulus);
	$composite_no.=$check;
	return $composite_no;
}

if(isset($_SESSION['username'])){
	$arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['registration']." WHERE email_1='".$_SESSION['username']."'",Q_RET_ARRAY);
	$_SESSION['user_id']=$arr[0]['rec_id'];
}

exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES']['registration']." set registration_no='".gen_index_no($_SESSION['user_id'])."' WHERE rec_id='".strtoupper($_SESSION['user_id'])."'",Q_RET_NONE);

$reg_arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['registration']." WHERE rec_id='".strtoupper($_SESSION['user_id'])."'",Q_RET_ARRAY);
$reg_arr=$reg_arr[0];

$_SESSION['downloaded']=$reg_arr['downloaded'];

if(isset($_SESSION['downloaded']) && !$_SESSION['downloaded']){
//if(isset($_SESSION['first_time'])&& $_SESSION['first_time']){
/*
echo " You can go back and change the information if required. <span style='color:red'>After you <b>logout</b> you will not be able to change them in next logins. Please <a href=\"javascript:open_page('ext_courses','application_pdf&data=true')\">download</a> and verify your application. </span> Click either pay offline to bank or pay online. The printed application and the payment invoice should be send in registered post to the following address: ";
*/
echo "<p> You can go back and change the information if required. <span style='color:red'>After you downloaded the application you will not be able to change the information again</span>. After verifying your information please <a href=\"javascript:open_page('ext_courses','application_pdf&data=true')\">download</a> your application. </span></p>
<p>Click either pay offline to bank  if you want to pay the application processing fee offline or click pay online if you want to pay application processing fee using your credit card to.</p> 
<p>The printed application should be signed and send in registered post to the following address with the payment invoice: </p>";

}else{
echo "<p>You can <a href=\"javascript:open_page('ext_courses','application_pdf&data=true')\">download</a> your application. </span></p>
<p>Click either pay offline to bank  if you want to pay the application processing fee offline or click pay online if you want to pay application processing fee using your credit card to.</p> 
<p>The printed application should be signed and send in registered post to the following address with the payment invoice: </p>";
}

echo"
<pre style='font:inherit'>
<b>Postal address:</b>
Senior Assistant Registrar/Academic and Publications,
UCSC,
No: 35 Reid Avenue,
Colombo 07.
</pre>
";

	echo "<br/><br/><br/><div align='right' class='buttonBar' >";
//if(isset($_SESSION['first_time'])&& $_SESSION['first_time']){
if(isset($_SESSION['downloaded']) && !$_SESSION['downloaded']){
	echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('ext_courses','registration')\">&laquo;&nbsp;Edit my info</button>";
}

if($reg_arr['status'] == 'ACCEPTED' || $reg_arr['status'] == 'ACCEPTED' ){
		echo "
		<div align=left>
		<h3>Online payment status</h3>
		<span style='color:green'>You have successfully completed the payment online!</span><br/>
		</div>
		";

}else{
	echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('ext_courses','available_courses')\">&laquo;&nbsp;Back</button>";
	echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('ext_courses','pay_offline')\">Pay offline to bank</button>";
	echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('ext_courses','pay_online')\">Pay online&nbsp;&raquo;</button>";
}
echo "</div>";

?>
