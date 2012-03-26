<?php
   if (isset($_SESSION['username'])){
      //after_login();
      //echo "Welcome ".$_SESSION['fullname'];
   }else{
      echo "
<h3 style='color:red'>System Users Login</h3>
<br>
<br>
<br>
<br>
<br>
<center>";
      echo "<p>Use the username and password which you use to login ZIMBRA</p>";
      echo before_login();
      /*
      echo "<br>";
      echo "I forgot my <a href=\"javascript:open_page('system','reset_password')\">password</a><br>";
       */
      echo "</center>";
   }
?>
