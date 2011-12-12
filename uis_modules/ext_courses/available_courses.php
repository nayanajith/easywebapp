<?php
if(isset($_SESSION['first_time']) &&  $_SESSION['first_time']==true){
}

$arr=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['registration'],Q_RET_ARRAY,'batch_id'):
foreach($arr as $batch_id => $info){

}

?>
