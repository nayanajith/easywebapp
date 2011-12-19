<?php
$bank				=$banks['sampath'];

/*'Receipt' is the key which returns the receipt string with*/
$status_enc		='';


if(isset($_REQUEST['Receipt'])){
	$status_enc		=$_REQUEST['Receipt'];
}else{
	echo "No receipt found!";

	//Getting full program record
	$program_arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['program']." WHERE code='".$break_id['program_code']."'",Q_RET_ARRAY);
	$program_arr   =$program_arr[0];

	echo "
Redirecting...<br>
<form method='POST' action='".$program_arr['tp_ip']."/".$program_arr['tp_callback']."' id='m_form'>
<input type='hidden' name='receipt' value='' >
Please press <input type='submit' value='redirect'> if not redirected automatically
</form>
<script type="text/javascript">
document.getElementById('m_form').submit();
</script>
";
	return;
}

//Create instance of  IPGInvoice decrypt and get the information of the receipt
include BANK_A_ROOT."/".$bank['root_dir']."/ipginvoce.php";
$igpinvoice		= new IGPInvoice($bank["gateway_url"],$bank["callback_url"],$bank["igpkey_dir"]."/",$bank["merchent_id"]);
$status			=explode(':',$igpinvoice->get_status($status_enc));

//Composited id of the transaction 
$composite_id	=$status[0];

//State of the transaction
$trans_status	=$status[1];

//Use transaction class to break the composite id
include MOD_CLASSES."/transaction_class.php";
$transaction	= new Transaction();
$break_id		=$transaction->break_composite_id($composite_id);

//Changing the status of the payment
$res				=exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['payment']." SET status='$trans_status' WHERE transaction_id='$composite_id'",Q_RET_MYSQL_RES);

//Getting full payment record 
$payment_arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['payment']." WHERE transaction_id='$composite_id'",Q_RET_ARRAY);
$payment_arr	=$payment_arr[0];

//Getting full program record
$program_arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['program']." WHERE program_code='".$break_id['program_code']."'",Q_RET_ARRAY);
$program_arr   =$program_arr[0];

//Getting full pay_for record
$pay_for_arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['pay_for']." WHERE pay_for_code='".$break_id['pay_for_code']."'",Q_RET_ARRAY);
$pay_for_arr   =$pay_for_arr[0];



$user_info		=exec_query("SELECT  * FROM ".$GLOBALS['MOD_S_TABLES']['payment']." LEFT JOIN ".$GLOBALS['MOD_S_TABLES']['employee']." ON ".$GLOBALS['MOD_S_TABLES']['employee'].".employee_id=".$GLOBALS['MOD_S_TABLES']['payment'].".employee_id WHERE ".$GLOBALS['MOD_S_TABLES']['payment'].".transaction_id='$composite_id'",Q_RET_ARRAY);
$user_info		=$user_info[0];

?>
<?php 

switch($trans_status){
   case 'PENDING':
		/*
		echo "<h3 style='color:orange'>Transaction pending...</h4>";
		*/
   break;
   case 'ACCEPTED':
		/*
		echo "<h3>Transaction completed successrully!</h3>";
		echo "Receipt was sent to your mail";
		include_once A_CLASSES."/mail_class.php";
		include_once MOD_CLASSES."/mail_templates_class.php";
		$mail			=new Mail_native();
		$templates	=new Mail_templates();
		$mail_body	=$templates->payment_invoice($user_info,$program_arr,$pay_for_arr);
		 */
	break;
	case'REJECTED':
		/*
		echo "<h3 style='color:red'>Transaction rejected!</h3>";
		*/
	break;
}

//How to redirect
/*
header("POST $path HTTP/1.1");
header("Host: $host");
header('Connection: close');
header('Content-type: application/x-www-form-urlencoded');
header('Content-length: '.strlen($data));
header('');
header($data);
*/

/*
echo "
Redirecting...
<form method='POST' action='http://".$program_arr['tp_ip']."/".$program_arr['tp_callback']."' id='m_form'>
<input type='hidden' name='tp_ref_id' value='".$payment_arr['tp_ref_id']."' >
<input type='hidden' name='tr_ref_id' value='".$payment_arr['transaction_id']."' >
<input type='hidden' name='state' value='".$payment_arr['status']."' >
<input type='hidden' name='tax' value='".$payment_arr['tax']."' >
</form>
<script type="text/javascript">
document.getElementById('m_form').submit();
</script>
";
 */
include(MOD_CLASSES."/crypt.php");
$message_crypt 	= new Message_crypt($program_arr['tp_key']);
$receipt=$message_crypt->genReceipt($payment_arr['tp_ref_id'],$payment_arr['transaction_id'],$payment_arr['tax'],$payment_arr['status']);

echo "
Redirecting...<br>
<form method='POST' action='".$program_arr['tp_ip']."/".$program_arr['tp_callback']."' id='m_form'>
<input type='hidden' name='receipt' value='".$receipt."' >
Please press <input type='submit' value='redirect'> if not redirected automatically
</form>
<script type="text/javascript">
document.getElementById('m_form').submit();
</script>
";

?>

