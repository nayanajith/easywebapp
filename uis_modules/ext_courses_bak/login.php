<?php 
   if (isset($_SESSION['username'])){
   	//after_login();
		//echo "Welcome ".$_SESSION['fullname'];
		if($_REQUEST['page']=='personal' && $_REQUEST['module']=='ext_courses'){
		
		}else{
			//header('Location: ?module='.MODULE.'&page=personal');
			//include("personal.php");
		}
			
	}else{
		echo "
		<h3>Login</h3>
		<br/>
		<br/>
		<br/>
		<br/>
		<br/>
		<center>
		<h4>If you are not registered yet please <a href='javascript:open_page(\"".MODULE."\",\"st_reg\")'>register</a> for the external courses registration system</h4>
		<h4>Please enter your email as username and NIC number as the password to log in to the system if you are already applied</h4>";
		echo before_login();
		echo "<br/>";
		echo "</center>";
	}
?>
