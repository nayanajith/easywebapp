<?php
if((isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") || isset($_SESSION['user_id']) == false ){

echo "Please login to the system <a href='javascript:open_page(\"courses\",\"login\")'>HERE</a>";
}else{
include MOD_CLASSES."/crypt.php";
$key='amEMiw9fp7YnSWO/ea4DU1HX8QgPzn05B38jBSqxc60=';
$msg_crypt=  new Message_crypt($key);
if(isset($_REQUEST['request']) ){
$deq = $msg_crypt->getReceipt($_REQUEST['request']); 


//echo $deq['status'];
echo '</br>';

   /*tp_ref_no
	tr_ref_no
	tax
	status
   */

d_r("dijit.form.Button");
if($deq['status'] == 'REJECTED'){
$deq['tp_ref_no'] -= 10000000;
echo"<h1 style='color:red'>Transaction Unsuccessful</h1>";  
	echo "<hr style='border:1px solid silver;'/>"; 
echo "<p>Your transaction has been unsuccessful. Click retry to attempt payment again or click 'Back to Personal Page' to return to the personal page</p>";
echo '<table width = 100%><tr valign = "top" ><td>';
echo '<form action= ""  method="get">
<input type = "hidden" name="module" value = "courses"/>
<input type = "hidden" name="page" value = "confirm"/>
<input type = "hidden" name = "reg_id" value = "'.$deq['tp_ref_no'].'">
<button dojoType="dijit.form.Button" type="submit" >Retry</button>
</form>';
echo '</td><td>';
echo '<form action= ""  method="get">
<input type = "hidden" name="module" value = "courses"/>
<input type = "hidden" name="page" value = "personal"/>
<button dojoType="dijit.form.Button" type="submit" >Back to Personal Page</button>
</form>';
echo '';

}else{
$deq['tp_ref_no'] -= 10000000;
$table = "reg";
$query = "UPDATE ".$table." SET status = 'CONFIRMED', payment_id = '".$deq['tr_ref_no']."' WHERE reg_id = '". $deq['tp_ref_no']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);

$table = "reg";
$query = "SELECT * FROM ".$table." WHERE reg_id = '".$deq['tp_ref_no']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);
$reg = mysql_fetch_array($res);

$table = "student";
$query = "SELECT * FROM ".$table." WHERE student_id = '". $reg['student_id']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);
$student = mysql_fetch_array($res);

$table = "schedule";
$query = "SELECT * FROM ".$table." WHERE session_id = '". $reg['session_id']."'" ;
$res2 = exec_query($query,Q_RET_MYSQL_RES);
$session = mysql_fetch_array($res2);
   
$table = "course";
$query = "SELECT * FROM ".$table." WHERE course_id = '". $session['course_id']."'" ;
$res2 = exec_query($query,Q_RET_MYSQL_RES);
$course = mysql_fetch_array($res2);  

      $tp_ref_id=$deq['tp_ref_no'];
		$pay_for=$course['short_name'];
		$amount=$course['course_fee'];
		$nic=$student['NIC'];
		$email=$student['email'];
		$full_name=$student['first_name']." ".$student['last_name'];

		$user_info=array();	
		$program_arr=array();
		$pay_for_arr=array();

		//Array ( [tp_ref_no] => 08550032 [tr_ref_no] => MCON-REG-08550032-0007010720114 [tax] => 66.80 [status] => REJECTED ) 
		$user_info['transaction_id']=$deq['tr_ref_no'];
		$user_info['first_name']=$student['first_name'];
		//echo $student['first_name'];
		//$user_info['first_name'] = 'efgrfeg';
		$user_info['middle_names']=" ";
      $user_info['last_name']=$student['last_name'];
      $user_info['amount']=$course['course_fee'].'.00';
		//$user_info['amount']='1500.00';
		$user_info['tax']=$deq['tax'];
		//$user_info['tax']='500.00';
		//$email=$student['email'];
		//$user_info['email']=$email;
		$user_info['email']='navin.gunatillaka@gmail.com';
		$email='navin.gunatillaka@gmail.com';
		//$user_info['email']='nml@ucsc.cmb.ac.lk';
      
     // print_r($user_info);
		
		$program_arr['description']="External Course - ".$course['short_name']." (".$session['session_name'].")" ;
		$pay_for_arr['description']="Payment";
		//$pay_for_arr['tax']=$GLOBALS['TAX'];
		$pay_for_arr['tax'] = '3';

		include_once MOD_CLASSES."/mail_templates_class.php";
		$templates	=new Mail_templates();
		$mail_body	=$templates->payment_invoice($user_info,$program_arr,$pay_for_arr);
		//echo $mail_body;
   echo"<h1 style='color:green'>Transaction Successful</h1>";  
	echo "<hr style='border:1px solid silver;'/>";   
   echo "<p>Please check your email for the payment invoice</p>" ;
   		
		include_once MOD_CLASSES."/mail_templates_class.php";
		echo $templates -> payment_invoice_html($user_info,$program_arr,$pay_for_arr);
//$reg = mysql_fetch_array($res);  
//echo $res;
//echo $reg['reg_id'];


  /* echo"<h3>Invoice</h3>";
	echo"<table cellpadding='5'>
	<tr><td style='font-weight:bold'>Registration number</td><td>".$deq['tp_ref_no']."</td></tr>
	<tr><td style='font-weight:bold'>Transaction ref number </td><td>".$deq['tr_ref_no']."</td></tr>
	<tr><td style='font-weight:bold'>Tax</td><td>Rs&nbsp;".$deq['tax']."</td></tr>
	</table>";*/
echo '<br/>';
echo '<form action= ""  method="get">
<input type = "hidden" name="module" value = "courses"/>
<input type = "hidden" name="page" value = "personal"/>
<button dojoType="dijit.form.Button" type="submit" >Back to Personal Page</button>
</form>';
}
}
}
?>
