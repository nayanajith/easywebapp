<?php 
$db_config_file="../db_config.php";
if(file_exists($db_config_file)){
   echo "<br><br><br><br><center>System have alread installed!<br>If you want to reinstall the system please delete db_config.php</center>";
}else{
   echo "<br><br><br><br><center>Seems like UCSCSIS is not installed properly<br>Please use install/install.php to install UCSCSIS in your system</center>";
}
?>
