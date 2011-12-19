<?php
if(!isset($_SESSION['username'])){
	echo "Please login first.";
	return;
}

$row=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['convocation_reg']." WHERE reg_no='".strtoupper($_SESSION['username'])."'",Q_RET_ARRAY);
$row=$row[0];

function print_user_info($row,$payment_category){
	echo"<h3>Online payment information</h3>";
	echo"<table cellpadding='5'>
	<tr><td style='font-weight:bold'>Index number</td><td>".$row['index_no']."</td></tr>
	<tr><td style='font-weight:bold'>NIC number</td><td>".$row['nic']."</td></tr>
	<tr><td style='font-weight:bold'>Name in English</td><td style='font-size:150%'>".strtoupper($row['name_in_english'])."</td></tr>
	<tr><td style='font-weight:bold'>Payment for the convocation</td><td>Rs&nbsp;".sprintf("%.02f",$payment_category[$row['category']][0])."</td></tr>
	<tr><td style='font-weight:bold'>Convenience fee for online payment </td><td>Rs&nbsp;".sprintf("%.02f",(($payment_category[$row['category']][0]/100)*$GLOBALS['TAX']))."</td></tr>
	<tr><td style='font-weight:bold'>Total payment</td><td>Rs&nbsp;".sprintf("%.02f",($payment_category[$row['category']][0]+($payment_category[$row['category']][0]/100)*$GLOBALS['TAX']))."</td></tr>
	</table>";

}

function print_instructions(){
   echo "<h4>Instructions</h4>
	<hr>
	<ol>
	<li>When you press Proceed you will be directed to sampath bank's online payment system
	<li>The payment invoice will be mailed to your mail given in Postgraduate LMS
	<li>Please use the transaction ID of the payment invoice for further queries 
	</ol>
	";
}

function payment_rejected($row,$payment_category){
	print_user_info($row,$payment_category);
	echo "
	<br>
	<br>
	<br>
	<h3>Online payment status</h3>
	<span style='color:red'>We are sorry that your payement was not successful!</span><br>
	Please try again or try our <a href=\"javascript:open_page('registration','pay_offline')\">offline payment procedure</a>.";

	//print_instructions();

	echo "<br><br><br><br><br><div align='right' class='buttonBar'  >
	<button dojoType='dijit.form.Button' jsId='back_btn'onClick=\"open_page('registration','payment')\">&laquo;&nbsp;Back</button>
	<button dojoType='dijit.form.Button' jsId='proceed_btn' type='submit' onClick=\"open_page('registration','pay_online&retry=true')\" >Try again&nbsp;&raquo;</button>
	</div>
	";
}

function payment_accepted($row,$payment_category){
	print_user_info($row,$payment_category);

	echo "
	<br>
	<br>
	<br>
	<h3>Online payment status</h3>
	<h4>
	<span style='color:green'>You have successfully completed the online payment!</span><br>
	Please check your email for the payment invoice... <br>
	Thank you!<h4>
	<!-- a href=''>Resend the invoice</a -->
	";

	/*
	print_instructions();

	echo "<br><br><br><div align='right' class='buttonBar'  >
	<button dojoType='dijit.form.Button' jsId='back_btn'onClick=\"open_page('registration','payment');this.diable;\">&laquo;&nbsp;Back</button>
	<button dojoType='dijit.form.Button' jsId='proceed_btn' type='submit' onClick=\"iframe_open()\" >Proceed&nbsp;&raquo;</button>
	</div>
	";
	*/
}

function peyment_process($row,$payment_category){
	//Change online payment status to PENDING
	exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['convocation_reg']." SET pay_online_status='PENDING'  WHERE reg_no='".strtoupper($_SESSION['username'])."'",Q_RET_MYSQL_RES);
	
	print_user_info($row,$payment_category);

	print_instructions();

	include MOD_CLASSES."/crypt.php";
	$key="TyuDFjgOK0ASxZqY6SP5HCJ1ZGHun00XXxpts9rDMzo=";
	$tp_ref_id=$row['index_no'];
	$pay_for="REG";
	$amount=$payment_category[$row['category']][0];
	$nic=$row['nic'];
	$email=$_SESSION["user_id"];
	$full_name=$row['name_in_english'];

   $msg_crypt= new Message_crypt($key);
	$request=$msg_crypt->genRquest(
		$tp_ref_id,
		$pay_for,
		$amount,
		$nic,
	   $email,
		$full_name
	);

	//print_r($msg_crypt->genRquest($request));
	$payment_gw_url="https://ucsc.lk/uis/?module=payment&page=tp_payment&data=true&program=MCON";
	
		
	//echo $request;
	echo "
	<script type="text/javascript" >
	function iframe_open(){
		//back_btn.setAttribute('disabled', true);
		//proceed_btn.setAttribute('label', 'Reload');
		proceed_btn.setAttribute('disabled', true);
		//document.getElementById('textFile').height='510';
		document.getElementById('m_form').submit();
	}
   </script>

   <!-- form method='POST' action='".$payment_gw_url."' id='m_form' target='textFile'-->
   <form method='POST' action='".$payment_gw_url."' id='m_form' target='_blank'>
		<input type='hidden' name='request' value='".$request."' >
		<!-- iframe id='textFile' name='textFile' style='border:0px;' width='100%'  height='10' align='center'>
		</iframe -->
		<br><br><br>
		<div align='right' class='buttonBar'  >
			<button dojoType='dijit.form.Button' jsId='back_btn' onClick=\"open_page('registration','payment');this.diable;\">&laquo;&nbsp;Back</button>
			<button dojoType='dijit.form.Button' jsId='proceed_btn' type='submit' onMouseUp=\"iframe_open()\" >Proceed&nbsp;&raquo;</button>
		</div>
	</form>";
}


switch($row['pay_online_status']){
case 'ACCEPTED':
	payment_accepted($row,$payment_category);
break;
case 'REJECTED':
	if(isset($_REQUEST['retry']) && $_REQUEST['retry']=='true'){
		peyment_process($row,$payment_category);
	}else{
		payment_rejected($row,$payment_category);
	}
break;
default:
	peyment_process($row,$payment_category);
break;
}

?>
