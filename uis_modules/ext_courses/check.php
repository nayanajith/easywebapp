<?php
include MOD_CLASSES."/crypt.php";
$key='amEMiw9fp7YnSWO/ea4DU1HX8QgPzn05B38jBSqxc60=';
$msg_crypt=  new Message_crypt($key);
$deq = $msg_crypt->getRequest($_REQUEST['request']); 


echo $deq['nic'];
echo '</br>';

   $tp_ref_no=$deq['tp_ref_id'];
	$tr_ref_no=$deq['pay_for'];
	$tax=$deq['amount'];
	$status='ACCEPTED';

  $msg_crypt=  new Message_crypt($key);
	$request=$msg_crypt->genReceipt(
		$tp_ref_no,
		$tr_ref_no,
		$tax,
		$status
	);
   
   echo "<form method = 'GET'>
   <input type = 'hidden' name = 'request' value = ".$request." />
   <input type = 'hidden' name = 'page' value = 'callback' />
   <input type = 'hidden' name = 'module' value = '".MODULE."' />
   <input type = 'hidden' name = 'datap' value = 'true' />   
   <input type = 'hidden' name = 'program' value = 'P' />
   <input type='submit' value = 'Make Payment' />
   </form>";






?>



