<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}

include(MOD_CLASSES."/offline_voucher_class.php");

//Change offline payment status to PENDING
exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES']['enroll']." SET payment_status='PENDING', payment_method='OFFLINE'  WHERE id='".$_SESSION['enroll_id']."'",Q_RET_NON);

//Acquire payer information
$reg_arr	   =exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['student']." WHERE registration_no='".$_SESSION['user_id']."'",Q_RET_ARRAY);
$batch_arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['batch']." b, ".$GLOBALS['MOD_P_TABLES']['course']." c WHERE b.registration_no='".$_SESSION['user_id']."' AND b.course_id=c.course_id",Q_RET_ARRAY);
$reg_arr	   =$reg_arr[0];
$batch_arr  =$batch_arr[0];


//Build the array which is a parameter of the voucher generator
$payment_info=array(
   "RS ".sprintf("%.02f",$batch_arr['fee']),
   number_to_text($batch_arr['fee']),
   $reg_arr['first_name']." ".$reg_arr['middle_names']." ".$reg_arr['last_name'],
   $reg_arr['registration_no'],
	$reg_arr['NIC']
);

//Generic information of the convocation payment voucher
$acc_no		="086-1001-511-89665";
$inv_title	="POSTGRADUATE APPLICATION 2011";
$purpose		="REGISTRATION FEE".strtoupper($reg_arr['title']);
	
//Generate the voucher 
//__construct($payer_info,$acc_no,$inv_title)
$voucher=new Voucher($payment_info,$acc_no,$inv_title,$purpose);

//Acquire pdf document
$pdf=$voucher->getPdf();

$pdf->Output('payment_voucher.pdf', 'I');
//$pdf->Output("/tmp/tt.pdf", 'F');
//return $pdf_file;

?>
