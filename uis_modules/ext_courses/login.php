<?php 
   if (isset($_SESSION['username'])){
   	//after_login();
		//echo "Welcome ".$_SESSION['fullname'];
		if($_REQUEST['page']=='available_courses' && $_REQUEST['module']=='ext_courses'){
		}else{
			//header('Location: ?module=registration&page=postgraduate_apl');
			//include("payment.php");
			include("available_courses.php");
			//include("admission.php");
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
		<p>If you are not registered yet please <a href='javascript:open_page(\"ext_courses\",\"registration\")'>register</a> with us to reserve seats in our courses</p>
		<p>Please enter your <b>email as username</b> and <b>NIC number as the password</b> to log in to the system if you are a registerd user</p>";
		echo before_login();
		echo "<br/>";
		echo "</center>";
	}
?>
