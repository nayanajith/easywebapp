<?php
if(!isset($_SESSION['username'])){
	return;
}

//Exit from the iframe
//Callback can be trapped inside an IFRAME, this javascript will exit from the iframe
/*
echo "
<script type='text/javascript' >
	//if (top != self) top.location.href = location.href;
if (top != self){
  	top.location.href = '?module=registration&page=pay_online';
}
</script>	
";
*/

//If no receipt was send through the request show the previouse status of the online payment and exit
if(!isset($_REQUEST['receipt'])){
	$user_info=exec_query("SELECT pay_online_status FROM ".$GLOBALS['MOD_S_TABLES']['convocation_reg']." WHERE reg_no='".strtoupper($_SESSION['username'])."'",Q_RET_ARRAY);
	$user_info=$user_info[0];
	if($user_info['pay_online_status'] == 'ACCEPTED'){
		echo "
		<h3>Online payment status</h3>
		<span style='color:green'>You have successfully completed the payment online!</span><br>
		Please check your email for the payment invoice... <br>
		Thank you!
		";
	}else{
		echo "
		<h3>Online payment status</h3>
		<span style='color:red'>We are sorry that your payement was rejected by the bank!</span><br>
		Please try again later or try our <a href=\"javascript:open_page('registration','pay_offline')\">offline payment procedure</a>.
		";
	}
	return;
}


include MOD_CLASSES."/crypt.php";
$key="TyuDFjgOK0ASxZqY6SP5HCJ1ZGHun00XXxpts9rDMzo=";
$msg_crypt=new Message_crypt($key);
$receipt=$msg_crypt->getReceipt($_REQUEST['receipt']);
//Array ( [tp_ref_no] => 08550032 [tr_ref_no] => MCON-REG-08550032-0007010720114 [tax] => 66.80 [status] => REJECTED ) 
//Check for the consistance
$test=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['convocation_reg']." WHERE reg_no='".strtoupper($_SESSION['username'])."' AND index_no='".$receipt['tp_ref_no'] ."'",Q_RET_ARRAY);
if(get_num_rows() != 1){
	session_destroy();
}


exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['convocation_reg']." SET transaction_id='".$receipt['tr_ref_no']."'  WHERE reg_no='".strtoupper($_SESSION['username'])."'",Q_RET_MYSQL_RES);
switch($receipt['status']){
   case 'PENDING':
		echo "<h3 style='color:orange'>Transaction pending...</h4>";
   break;
   case 'ACCEPTED':
		//Changing the online payment status to ACCEPTED
		exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['convocation_reg']." SET pay_online_status='ACCEPTED'  WHERE reg_no='".strtoupper($_SESSION['username'])."'",Q_RET_MYSQL_RES);
		$row=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['convocation_reg']." WHERE reg_no='".strtoupper($_SESSION['username'])."'",Q_RET_ARRAY);
		$row=$row[0];

		echo "
		<h3>Online payment status</h3>
		<span style='color:green'>You have successfully completed the payment online!</span><br>
		Please check your email for the payment invoice... <br>
		Thank you!
		";

      $tp_ref_id=$row['index_no'];
		$pay_for="REG";
		$amount=$payment_category[$row['category']][0];
		$nic=$row['nic'];
		$email=$_SESSION["user_id"];
		$full_name=$row['name_in_english'];

		$user_info=array();	
		$program_arr=array();
		$pay_for_arr=array();

		//Array ( [tp_ref_no] => 08550032 [tr_ref_no] => MCON-REG-08550032-0007010720114 [tax] => 66.80 [status] => REJECTED ) 
		$user_info['transaction_id']=$receipt['tr_ref_no'];
		$user_info['first_name']=$row['name_in_english'];
		$user_info['middle_names']='';
      $user_info['last_name']='';
      $user_info['amount']=$payment_category[$row['category']][0];
		$user_info['tax']=$receipt['tax'];
		$email=$_SESSION['user_id'];
		$user_info['email']=$email;
		//$user_info['email']='nmlaxaman@gmail.com';
		//$user_info['email']='nml@ucsc.cmb.ac.lk';

		$program_arr['description']="Convocation";
		$pay_for_arr['description']="Payment";
		$pay_for_arr['tax']="3.34";

		include_once MOD_CLASSES."/mail_templates_class.php";
		$templates	=new Mail_templates();
		$mail_body	=$templates->payment_invoice($user_info,$program_arr,$pay_for_arr);
	break;
	case'REJECTED':
		//Changing the online payment status to REJECTED
		exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['convocation_reg']." SET pay_online_status='REJECTED'  WHERE reg_no='".strtoupper($_SESSION['username'])."'",Q_RET_MYSQL_RES);

		echo "
		<h3>Online payment status</h3>
		<span style='color:red'>We are sorry that your payement was rejected by the bank!</span><br>
		Please try again later or try our <a href=\"javascript:open_page('registration','pay_offline')\">offline payment procedure</a>.
		";
	break;
}




?>
