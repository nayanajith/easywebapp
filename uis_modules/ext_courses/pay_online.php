<?php
if(!isset($_SESSION['username'])){
	echo "Please login first.";
	return;
}

$row=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['registration']." WHERE rec_id='".strtoupper($_SESSION['user_id'])."'",Q_RET_ARRAY);
$row=$row[0];

$pg_app_fee=$GLOBALS['FEE'];

function print_user_info($row,$pg_app_fee){
	echo"<h3>Online payment information</h3>";
 	echo"<h4>Payment for the postgraduate application processing</h4>";
	echo"<table cellpadding='5'>
	<tr><td style='font-weight:bold'>Registration number</td><td>".$row['registration_no']."</td></tr>
	<tr><td style='font-weight:bold'>NIC number</td><td>".$row['NIC']."</td></tr>
	<tr><td style='font-weight:bold'>Name</td><td style='font-size:150%'>".$row['first_name']." ".$row['middle_names']." ".$row['last_name']."</td></tr>
	<tr><td style='font-weight:bold'>Payment</td><td>Rs&nbsp;".sprintf("%.02f",$pg_app_fee)."</td></tr>
	<tr><td style='font-weight:bold'>Convenience fee for online payment </td><td>Rs&nbsp;".sprintf("%.02f",(($pg_app_fee/100)*$GLOBALS['TAX']))."</td></tr>
	<tr><td style='font-weight:bold'>Total payment</td><td>Rs&nbsp;".sprintf("%.02f",($pg_app_fee+($pg_app_fee/100)*$GLOBALS['TAX']))."</td></tr>
	</table>";

}

function print_instructions(){
   echo "<h4>Instructions</h4>
	<hr/>
	<ol>
	<li>When you press Proceed you will be directed to sampath bank's online payment system
	<li>The payment invoice will be mailed to your personal mail given in the application
	<li>Please use the transaction ID of the payment invoice for further queries on the online transaction
	</ol>
	";
}

function payment_rejected($row,$payment_category){
	print_user_info($row,$payment_category);
	echo "
	<br/>
	<br/>
	<br/>
	<h3>Online payment status</h3>
	<span style='color:red'>We are sorry that your payment was not successful!</span><br/>
	Please try again or try our <a href=\"javascript:open_page('ext_courses','pay_offline')\">offline payment procedure</a>.";

	//print_instructions();

	echo "<br/><br/><br/><br/><br/><div align='right' class='buttonBar'  >
	<button dojoType='dijit.form.Button' jsId='back_btn'onClick=\"open_page('ext_courses','payment')\">&laquo;&nbsp;Back</button>
	<button dojoType='dijit.form.Button' jsId='proceed_btn' type='submit' onClick=\"open_page('ext_courses','pay_online&retry=true')\" >Try again&nbsp;&raquo;</button>
	</div>
	";
}

function payment_accepted($row,$pg_app_fee){
	print_user_info($row,$pg_app_fee);

	echo "
	<br/>
	<br/>
	<br/>
	<h3>Online payment status</h3>
	<h4>
	<span style='color:green'>You have successfully completed the online payment!</span><br/>
	Please check your email for the payment invoice... <br/>
	Thank you!<h4>
	<!-- a href=''>Resend the invoice</a -->
	";

	/*
	print_instructions();

	echo "<br/><br/><br/><div align='right' class='buttonBar'  >
	<button dojoType='dijit.form.Button' jsId='back_btn'onClick=\"open_page('registration','payment');this.diable;\">&laquo;&nbsp;Back</button>
	<button dojoType='dijit.form.Button' jsId='proceed_btn' type='submit' onClick=\"iframe_open()\" >Proceed&nbsp;&raquo;</button>
	</div>
	";
	*/
}

function peyment_process($row,$pg_app_fee){
	//Change online payment status to PENDING
	exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES']['registration']." SET status='PENDING'  WHERE rec_id='".$_SESSION['user_id']."'",Q_RET_MYSQL_RES);
	
	print_user_info($row,$pg_app_fee);

	print_instructions();

	include MOD_CLASSES."/crypt.php";
	$key='amEMiw9fp7YnSWO/ea4DU1HX8QgPzn05B38jBSqxc60=';
	$tp_ref_id=$row['registration_no'];
	$pay_for="A";
	$amount=$pg_app_fee;
	$nic=$row['NIC'];
	$email=$row["email_1"];
	$full_name=$row['first_name']." ".$row['middle_names']." ".$row['last_name'];


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
	//$payment_gw_url="https://ucsc.lk/uis/?module=payment&page=tp_payment&data=true&program=M";
	$payment_gw_url="https://ucsc.lk/uis/?module=payment&page=tp_payment&data=true&program=P";
	
		
	//echo $request;
	echo "
	<script language='javascript'>
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
		<input type='hidden' name='request' value='".$request."' />
		<!-- iframe id='textFile' name='textFile' style='border:0px;' width='100%'  height='10' align='center'>
		</iframe -->
		<br/><br/><br/>
		<div align='right' class='buttonBar'  >
			<button dojoType='dijit.form.Button' jsId='back_btn' onClick=\"open_page('ext_courses','payment');this.diable;\">&laquo;&nbsp;Back</button>
			<button dojoType='dijit.form.Button' jsId='proceed_btn' type='submit' onMouseUp=\"iframe_open()\" >Proceed&nbsp;&raquo;</button>
		</div>
	</form>";
}


switch($row['status']){
case 'ACCEPTED':
	payment_accepted($row,$pg_app_fee);
break;
case 'REJECTED':
	if(isset($_REQUEST['retry']) && $_REQUEST['retry']=='true'){
		peyment_process($row,$pg_app_fee);
	}else{
		payment_rejected($row,$pg_app_fee);
	}
break;
default:
	peyment_process($row,$pg_app_fee);
break;
}

?>
