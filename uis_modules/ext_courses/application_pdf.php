<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}

exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES']['registration']." set downloaded=true WHERE rec_id='".strtoupper($_SESSION['user_id'])."'",Q_RET_NONE);

$reg_arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['registration']." WHERE rec_id='".strtoupper($_SESSION['user_id'])."'",Q_RET_ARRAY);
$reg_arr=$reg_arr[0];
include MOD_CLASSES."/application_pdf_class.php";
$application=new Application($reg_arr);

//Acquire pdf document
$pdf=$application->getPdf();

//$pdf->Output('/Users/nayanajith/Desktop/test_pdf.pdf', 'F');
$pdf->Output('postgraduate_application.pdf', 'I');
