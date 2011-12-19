<h3>Email verification</h3>
<?php
if(isset($_REQUEST['code']) && isset($_REQUEST['rec_id'])){
	$arr=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['registration']." WHERE rec_id='".$_REQUEST['rec_id']."'",Q_RET_ARRAY);
	$arr=$arr[0];
	if($arr['status'] == 'PENDING'){
		exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['registration']." SET status='ACCEPTED' WHERE verification_code='".$_REQUEST['code']."'",Q_RET_NON);
		if(is_query_ok() && get_affected_rows() > 0){
			echo "Your email has successfully verified.<br>";
			echo "Please <a href=\"javascript:open_page('donations','login')\">login</a> to the system.<br>";
		}else{
			exec_query("DELETE FROM ".$GLOBALS['MOD_S_TABLES']['registration']." WHERE rec_id='".$_REQUEST['rec_id']."'",Q_RET_NON);
			echo "Email verification failed!";	
			echo "Please follow the <a href=\"javascript:open_page('donations','registration')\">registration</a> procedure again.<br>";
		}
	}
}else{
	echo "<p>To complete the registration procedure please follow the  instructions recieved to your mail address.</p>";
	echo "<p>If you are not yet registered please follow the <a href=\"javascript:open_page('donations','registration')\">registration</a> procedure.</p>";
}
?>
