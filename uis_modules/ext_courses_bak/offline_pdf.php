<?php
if((isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") || isset($_SESSION['user_id']) == false ){
	echo "Please login to the system.";
	return;
}

include(MOD_CLASSES."/offline_voucher_class.php");

//Change offline payment status to PENDING

$table = $GLOBALS['MOD_P_TABLES']["reg"];
$query = "UPDATE ".$table." SET status = 'PENDING' WHERE reg_id = '". $_SESSION['voucher_reg_id']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);


//Acquire payer information
$table = $GLOBALS['MOD_P_TABLES']["reg"];
$query = "SELECT * FROM ".$table." WHERE reg_id = '".$_SESSION['voucher_reg_id']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);
$reg = mysql_fetch_array($res);

$table = $GLOBALS['MOD_P_TABLES']["student"];
$query = "SELECT * FROM ".$table." WHERE student_id = '". $_SESSION['user_id']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);
$student = mysql_fetch_array($res);

$table = $GLOBALS['MOD_P_TABLES']["schedule"];
$query = "SELECT * FROM ".$table." WHERE session_id = '". $reg['session_id']."'" ;
$res2 = exec_query($query,Q_RET_MYSQL_RES);
$session = mysql_fetch_array($res2);
   
$table = $GLOBALS['MOD_P_TABLES']["course"];
$query = "SELECT * FROM ".$table." WHERE course_id = '". $session['course_id']."'" ;
$res2 = exec_query($query,Q_RET_MYSQL_RES);
$course = mysql_fetch_array($res2);  

// int to eng converter
function convert_number($number) 
{ 
    if (($number < 0) || ($number > 999999999)) 
    { 
    throw new Exception("Number is out of range");
    } 

    $Gn = floor($number / 1000000);  /* Millions (giga) */ 
    $number -= $Gn * 1000000; 
    $kn = floor($number / 1000);     /* Thousands (kilo) */ 
    $number -= $kn * 1000; 
    $Hn = floor($number / 100);      /* Hundreds (hecto) */ 
    $number -= $Hn * 100; 
    $Dn = floor($number / 10);       /* Tens (deca) */ 
    $n = $number % 10;               /* Ones */ 

    $res = ""; 

    if ($Gn) 
    { 
        $res .= convert_number($Gn) . " Million"; 
    } 

    if ($kn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($kn) . " Thousand"; 
    } 

    if ($Hn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($Hn) . " Hundred"; 
    } 

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 
        "Nineteen"); 
    $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
        "Seventy", "Eigthy", "Ninety"); 

    if ($Dn || $n) 
    { 
        if (!empty($res)) 
        { 
            $res .= " and "; 
        } 

        if ($Dn < 2) 
        { 
            $res .= $ones[$Dn * 10 + $n]; 
        } 
        else 
        { 
            $res .= $tens[$Dn]; 

            if ($n) 
            { 
                $res .= "-" . $ones[$n]; 
            } 
        } 
    } 

    if (empty($res)) 
    { 
        $res = "zero"; 
    } 

    return $res; 
} 

//Build the array which is a parameter of the voucher generator
$payment_info=array(
   "RS ".sprintf("%.02f",$course['course_fee']),
   convert_number($course['course_fee']),
   $student['first_name']." ".$student['last_name'],
   $reg['reg_id'],
	$student['NIC']
);

//Generic information of the convocation payment voucher
$acc_no		="086-1001-511-89665";
$inv_title	="COURSE APPLICATION PAYMENT";
$purpose		= $course['short_name']." - ".$session['session_name'];
	
//Generate the voucher 
//__construct($payer_info,$acc_no,$inv_title)
$voucher=new Voucher($payment_info,$acc_no,$inv_title,$purpose);

//Acquire pdf document
$pdf=$voucher->getPdf();

$pdf->Output('payment_voucher.pdf', 'I');
//$pdf->Output("/tmp/tt.pdf", 'F');
//return $pdf_file;

?>
