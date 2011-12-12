<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}
$query="SELECT registration_no,first_name,middle_names,last_name,NIC FROM ".$GLOBALS['MOD_P_TABLES']['registration']." WHERE rec_id='".$_SESSION['user_id']."'";
$arr=exec_query($query,Q_RET_ARRAY);
$arr=$arr[0];

if(!isset($_REQUEST['pdf'])){
	echo "<h3>Downloading Admission Card</h3>";
	echo "<table style='font-size:14px;' cellpadding=10>
				<tr><td>NAME</td><td>".strtoupper($arr['first_name'].' '.$arr['middle_names'].' '.$arr['last_name'])."</td></tr>
				<tr><td>NIC/Passport Number</td><td>".$arr['NIC']."</td></tr>
			</table><br/>
		";
	echo "<hr style='border:1px solid silver;'/>";
	echo "<h2>You can <a href='?module=ext_courses&page=admission&pdf=true&data=true'><b>download</b></a> the admission card.</h2>";
	echo "<hr style='border:1px solid silver;'/>";
	echo "<h4>Further information</h4>For any queries regarding postgraduate application please contact <br/>Academic & Publications branch UCSC<br />
			   <b>TP: 0112589123</b><br/><br/>";
	echo "<hr style='border:1px solid silver;'/>";
	echo "<h4>Technical assistance</h4>For technical assistance please write to <br/> <img src='".IMG."/uis_mail.png'>";
}else{
	$registration_no=$arr['registration_no'];
	$file = MOD_A_ROOT.'/admissions/'.$registration_no.'.pdf';

	if (file_exists($file)) {
		header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private",false);
      header("Content-Type: application/pdf");
      header("Content-Disposition: attachment; filename=\"".basename($file)."\";");
      header("Content-Transfer-Encoding: binary");
      header("Content-Length: ".@filesize($file));
		/*
      ob_clean();
      flush();
		*/
      readfile($file);
      exit;
	}else{
		if(isset($_REQUEST['data'])){
			header('Location: ?module=ext_courses&page=admission&pdf=true');
		}
		echo "<h3>Downloading Admission Card</h3><br/><br/><br/>";
		echo "<h2>Your admission is not yet available please try again later</h2>";
		echo "<hr style='border:1px solid silver;'/>";
		echo "<h4>Further information</h4>For any queries regarding postgraduate application please contact <br/>Academic & Publications branch UCSC<br />
			   <b>TP: 0112589123</b><br/><br/>";
		echo "<hr style='border:1px solid silver;'/>";
		echo "<h4>Technical assistance</h4>For technical assistance please write to <br/> <img src='".IMG."/uis_mail.png'>";
	}
}
?>
