<?php
   if (isset($_SESSION['username'])){
   	//after_login();
		//echo "Welcome ".$_SESSION['fullname'];
		if($_REQUEST['page']=='convocation' && $_REQUEST['module']=='registration'){
		}else{
			header('Location: ?module=registration&page=convocation');
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
		<h4>Please use your <a href='http://pglms.ucsc.cmb.ac.lk'>Postgradute LMS</a> username and password to log in to the system</h4>";
		echo before_login();
		echo "<br>";
		echo "If you have forgot your password please reset through  <a href=\"https://pglms.ucsc.cmb.ac.lk/lms/login/forgot_password.php\" target='_blank'>Postgraduate LMS</a><br>";
		echo "</center>";
	}
?>
