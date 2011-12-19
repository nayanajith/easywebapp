<?php 
if (isset($_SESSION['username'])){
	if($_REQUEST['page']=='registration' && $_REQUEST['module']=='donations'){
	}else{
		include("donation_to.php");
	}
}else{
	echo "
	<h3>Login</h3>
	<br>
	<br>
	<br>
	<br>
	<br>
	<center>
	<h4>If you are not registered please <a href='javascript:open_page(\"donations\",\"registration\")'>register</a> in the UCSC donation/funding program</h4>
	<h4>Please enter your username (email) and password to login to the system</h4>";
	echo before_login();
	echo "<br>";
	echo "</center>";
}
?>
