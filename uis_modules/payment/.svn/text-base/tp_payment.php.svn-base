<?php
include(MOD_CLASSES."/crypt.php");
$ERROR=array(
	"PARAM_ERROR"=>-100,
	"NO_PROGRAM"=>-101,
	"NO_PAY_FOR"=>-102
);

//log_msg("lll",implode(",",array_values($_REQUEST)));
//Check for essential parameters and  proceed with furter functions
$program_arr=null;
$request_arr=null;
if(isset($_REQUEST['program']) && isset($_REQUEST['request'])){
	//Get program info
	$program_arr		=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['program']." WHERE program_code='".strtoupper($_REQUEST['program'])."'",Q_RET_ARRAY);

	//Report error if the program not available
	if(get_num_rows() < 1){
		echo $ERROR["NO_PROGRAM"];
		return;
	}
		
	$program_arr		=$program_arr[0];

	//third party key for this payment program
	$key					=$program_arr['tp_key'];

	//Decrypt the request and get the details
	$message_crypt 	= new Message_crypt($key);

	//encrypton decryption test
	/*
	print_r($message_crypt->getRequest( $message_crypt->genRquest(
		$tp_ref_id='R006',
		$pay_for='REG',
		$amount='750',
		$nic='812940201v',
      $email='nml@ucsc.lk',
		$full_name='nayanajith mahendra laxaman'
	)));
	*/

	$request_arr=$message_crypt->getRequest($_REQUEST['request']);

	//Check for the availability of the parameters
	if(
		isset($request_arr['tp_ref_id'])	&&
		isset($request_arr['nic'])			&&
		isset($request_arr['email'])		&&
		isset($request_arr['full_name'])	&&
		isset($request_arr['pay_for'])	&&
		isset($request_arr['amount'])		&&
		is_numeric($request_arr['amount']) 	&&
		strlen($request_arr['nic'])==10	&&
		strlen($request_arr['tp_ref_id'])==8
	){
	}else{
		echo $ERROR["PARAM_ERROR"];
		return;
	}
}else{
	echo $ERROR["PARAM_ERROR"];
	return;
}
	

//Get pay_for info
$pay_for_arr		=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['pay_for']." WHERE program_id='".$program_arr['program_id']."' AND pay_for_code='".strtoupper($request_arr['pay_for'])."'",Q_RET_ARRAY);
//Report error if the program not available
if(get_num_rows() < 1){
	echo $ERROR["NO_PAY_FOR"];
	return;
}
$pay_for_arr		=$pay_for_arr[0];

//Breaking fullname into firstname middle names and last name
$full_name_arr=explode(' ',$request_arr['full_name']);
$first_name		=trim($full_name_arr[0]);
$last_name		='';
$middle_names	='';

if(trim(end($full_name_arr)) != $first_name){
	$last_name		=trim(end($full_name_arr));
}

foreach($full_name_arr as $name){
	$space='';
	$name=trim($name);
	if($name != $first_name && $name != $last_name){
		$middle_names	.=$space.$name;
		$space=' ';
	}
}

//adding user if not available
$res=exec_query("REPLACE INTO ".$GLOBALS['MOD_S_TABLES']['employee']."(nic,email,first_name,middle_names,last_name) values('".$request_arr['nic']."','".$request_arr['email']."','$first_name','$middle_names','$last_name')",Q_RET_MYSQL_RES);
//$res=exec_query("INSERT INTO ".$GLOBALS['MOD_S_TABLES']['employee']."(nic,email,first_name,middle_names,last_name) values('".$request_arr['nic']."','".$request_arr['email']."','$first_name','$middle_names','$last_name')",Q_RET_MYSQL_RES);

//Getting employee id
$employee_arr=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['employee']." WHERE nic='".$request_arr['nic']."';",Q_RET_ARRAY);
$employee_arr=$employee_arr[0];

//Adding payment record
exec_query("SET AUTOCOMMIT=0",Q_RET_MYSQL_RES);
exec_query("START TRANSACTION",Q_RET_MYSQL_RES);
$res=exec_query("INSERT INTO ".$GLOBALS['MOD_S_TABLES']['payment']."(employee_id,amount,status,init_time,client_ip,tp_ref_id,program_id,pay_for_id) values('".$employee_arr['employee_id']."','".$request_arr['amount']."','PENDING',CURRENT_TIMESTAMP,'".$_SERVER['REMOTE_ADDR']."','".$request_arr['tp_ref_id']."','".$program_arr['program_id']."','".$pay_for_arr['pay_for_id']."')",Q_RET_MYSQL_RES);
$trans_id_arr		=exec_query("SELECT max(r_id) FROM ".$GLOBALS['MOD_S_TABLES']['payment'],Q_RET_ARRAY);
exec_query("COMMIT",Q_RET_MYSQL_RES);

$trans_id			=$trans_id_arr[0]['max(r_id)'];
include MOD_CLASSES."/transaction_class.php";
$transaction 		= new Transaction();
$composite_tr_id	=$transaction->gen_composite_id($program_arr['program_code'],$pay_for_arr['pay_for_code'],$request_arr['tp_ref_id']);

//Generat invoice using java library (generating encripted string)
$bank=$banks['sampath'];

//calculate tax and total fee
$tax_percentage	=$pay_for_arr['tax'];
$tax_fee				=($request_arr['amount']/100)*$tax_percentage;
$total_fee			=sprintf("%.02f",$tax_fee+$request_arr['amount']);
$real_amount		=($total_fee-($total_fee*$bank['commission']));

/*update transaction_id and tax*/
$res=exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['payment']." SET transaction_id='$composite_tr_id',tax='$tax_fee',real_amount='$real_amount' WHERE r_id='$trans_id'",Q_RET_MYSQL_RES);

//Generat invoice using java library (generating encripted string)
include BANK_A_ROOT."/".$bank['root_dir']."/ipginvoce.php";
$callback_url="http://localhost:8888/ucscis/?module=payment&page=tp_callback";

$igpinvoice = new IGPInvoice($bank["gateway_url"],$bank["callback_url"],$bank["igpkey_dir"]."/",$bank["merchent_id"]);
$invoice 	= $igpinvoice->gne_invoice($total_fee,$composite_tr_id);

//POSTING the invoice to the bank
$host = $bank['gateway_host'];
$path = $bank['gateway_path'];;
$data = "MerchantInvoice=$invoice";
$errno=0;
$errstr='';

/*
echo $composite_tr_id."<br/>";
echo $invoice."<br/>";
$REC="-6081889498084647830509218519790688081376939564860156282710405245765453001061062991309905241969421099618395922707919365347569718396879637888285984937502713014222486914225450821038978455239014928698315344411771266010079103487117421075658955187458791145748119931931512425230859864512765972516025211992948001425017288184717601272963821510895557152238974503509088151450546759762452807170196517491488746210909346008780797683227909573650545936832623936195988078457870947877527982277681027606626297004432523997930031584577397373979107887222926192672956473968477504944926999069438961252916558133554157403775227400265244718652310600746186386424851382117323446374479769700650120994009089693890723424769608081867656910740893486213392569179694278211180667968478699173749277106050788529412781548701354545009840317884251808385596516467287707360365226405038308193822605728911874336805042537659547394089";
echo "<a href='$callback_url&Receipt=$REC'>Callback</a>";
*/

/*
header("POST $path HTTP/1.1");
header("Host: $host");
header('Connection: close');
header('Content-type: application/x-www-form-urlencoded');
header('Content-length: '.strlen($data));
header('');
header($data);
*/
echo "
Redirecting...
<form method='POST' action='".$bank['gateway_url']."' id='m_form'>
<input type='hidden' name='MerchantInvoice' value='".$invoice."' />
Please press <input type='submit' value='redirect'> if not redirected automatically
</form>
<script>
document.getElementById('m_form').submit();
</script>
";

?>
