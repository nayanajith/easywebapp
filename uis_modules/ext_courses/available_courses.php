<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}

//First timers will provided with a registration number
if(isset($_SESSION['first_time']) &&  $_SESSION['first_time']==true){
   $_SESSION['first_time']=false;
   //Get the students record_id
	$arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['student']." WHERE email_1='".$_SESSION['username']."'",Q_RET_ARRAY);

   //Generate the registration number for the student
	$_SESSION['rec_id']=$arr[0]['rec_id'];
	$_SESSION['user_id']=gen_reg_no($arr[0]['rec_id']);

   //Update the students record with registration number
   exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES']['student']." SET registration_no='".$_SESSION['user_id']."' WHERE email_1='".$_SESSION['username']."'",Q_RET_NON);
   if(!enroll_student(get_current_batch($_SESSION['course_id']),$_SESSION['user_id'])){
      echo "Sorry current batch for the requested course is full. Please contact CSC or apply for the next batch."; 
   }else{
      include 'payment.php';
      return;
   }

}elseif(isset($_REQUEST['un_enroll']) && $_REQUEST['un_enroll'] == true){
   un_enroll_student($_REQUEST['batch_id'],$_SESSION['user_id']);

}elseif(isset($_REQUEST['make_payment']) && $_REQUEST['make_payment'] == true){
   $_SESSION['batch_id']=$_REQUEST['batch_id'];
   $_SESSION['enroll_id']=get_enroll_id($_REQUEST['batch_id'],$_SESSION['user_id']);
   //header('Location: ?module='.MODULE.'&page=payment&batch_id='.$_REQUEST['batch_id']);
   include 'payment.php';
   return;

}elseif(isset($_REQUEST['reserve_a_seat']) && $_REQUEST['reserve_a_seat'] == true){
   enroll_student($_REQUEST['batch_id'],$_SESSION['user_id']);
   $_SESSION['batch_id']=$_REQUEST['batch_id'];
   //header('Location: ?module='.MODULE.'&page=payment&batch_id='.$_REQUEST['batch_id']);

   //If this is first time and no problem found, proceed with the payment for the selected course
   include 'payment.php';
   return;
}

function get_current_batch($course_id){
   $batch_arr=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['batch']." WHERE course_id='".$course_id."' AND start_date > current_date ORDER BY start_date DESC LIMIT 1 ",Q_RET_ARRAY);
   return $batch_arr[0]['batch_id'];
}

/**
 * Return the number of seats available for the course given in current batch
 */
function get_available_seats($batch_id){
   //Registering the student with his first course
   $batch_arr=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['batch']." b,".$GLOBALS['MOD_P_TABLES']['course']." c WHERE b.course_id=c.course_id AND b.batch_id='".$batch_id."' AND start_date > current_date ORDER BY start_date DESC LIMIT 1 ",Q_RET_ARRAY);
   $max_seats  =$batch_arr[0]['seats'];

   $seats_avail=exec_query("SEELCT (".$max_seats." - COUNT(*)) seats FROM ".$GLOBALS['MOD_P_TABLES']['enroll']." WHERE batch_id='".$batch_id."'",Q_RET_ARRAY);
   return $seats_avail[0]['seats'];
}

/**
 * Enroll a student with a given course or batch
 */
function enroll_student($batch_id,$registration_no){
   if(get_available_seats($batch_id) >=0){
      exec_query("INSERT INTO ".$GLOBALS['MOD_P_TABLES']['enroll']."(`registration_no`,`batch_id`)values('".$registration_no."','".$batch_id."') ",Q_RET_NON);
      $_SESSION['enroll_id']=get_enroll_id($batch_id,$registration_no);
      return true;
   }else{
      return false;
   }
}

/**
 * Enroll a student with a given course or batch
 */
function get_enroll_id($batch_id,$registration_no){
      $arr=exec_query("SElECT id FROM ".$GLOBALS['MOD_P_TABLES']['enroll']." WHERE `registration_no`='".$registration_no."' AND `batch_id`='".$batch_id."'",Q_RET_ARRAY);
      return $arr[0]['id'];
}



/**
 * Delete a student from the enralled list
 */
function un_enroll_student($batch_id,$registration_no){
   //delete the user from database
   exec_query("DELETE FROM ".$GLOBALS['MOD_P_TABLES']['enroll']." WHERE registration_no='".$registration_no."' AND batch_id='".$batch_id."'",Q_RET_NON);
}


echo "<h3>Enroll for new courses and reserve seats</h3><br/><br/>";

d_r('dijit.form.Form');
$course_box="
<div dojoType='dijit.form.Form'  style='border:1px solid #C9D7F1;border-bottom:3px solid #C9D7F1;padding:5px;margin-top:10px;' class='round10'>
<input type='hidden' name='module' value='".MODULE."'>
<input type='hidden' name='page' value='".PAGE."'>
<input type='hidden' name='batch_id' value='%s'>
<h4 class='coolh' style='text-align:left'>%s</h4>
<p>%s</p>
<h4  style='text-align:left'>Commence date: %s</h4>
<p>%s</p>
<div  align='right' >
%s
</div>
</div>";


//Getting the list of courses enrolled by this user
$reg_arr=exec_query("SELECT c.course_id,c.title,c.description,b.start_date,b.batch_id,e.payment_status,e.payment_method,e.transaction_id FROM ".$GLOBALS["MOD_P_TABLES"]["enroll"]." e, ".$GLOBALS["MOD_P_TABLES"]["course"]." c, ".$GLOBALS["MOD_P_TABLES"]["batch"]." b WHERE registration_no='".$_SESSION['user_id']."' AND e.batch_id=b.batch_id and b.course_id=c.course_id",Q_RET_ARRAY,null,"batch_id");
$my_courses=array();
foreach($reg_arr as $batch_id => $info){
   $my_courses[]=$info['course_id'];
   $button_bar="";
   $title="";
   if($info['payment_status']=='ACCEPTED'){
      $button_bar="<font color='green'>Your payment received and a seat was reserved for you in this course</font>";
      $title=$info['title']." <font color='green'>&isin;</font>";
   }else{
      $button_bar="<button dojoType='dijit.form.Button' type='submit' name='un_enroll' value='true' >Un enroll</button><button type='submit' name='make_payment' value='true' dojoType='dijit.form.Button' >Make payment &#187;</button>";
      $title=$info['title']." <font color='red'>&notin;</font>";
   }
   $payment_status="<font color='red'>Your payment is not recceived yet. Please be noticed that the available seats will be served in first come first server basis.</font>";
   printf($course_box,$batch_id,$title,$info['description'],$info['start_date'],$payment_status,$button_bar);
}

//Getting the list of courses available (not enrolled) for this user
$filter="";
if(sizeof($my_courses) > 0 ){
   $filter="AND b.course_id NOT IN('".implode("','",$my_courses)."')";
}

$batch_arr=exec_query('SELECT DISTINCT c.course_id,c.title,c.description,b.start_date,b.batch_id FROM '.$GLOBALS['MOD_P_TABLES']['course'].' c,'.$GLOBALS['MOD_P_TABLES']['batch'].' b WHERE  c.course_id=b.course_id '.$filter.' and c.disabled=0 and b.start_date > current_date',Q_RET_ARRAY,null,'course_id');

foreach($batch_arr as $course_id => $info){
   $button_bar="<button dojoType='dijit.form.Button' type='submit' name='reserve_a_seat' value='true'>Reserve a seat &#187;</button>";
   $title=$info['title']." <font color='red'>&notin;</font>";
   $payment_status="<font color='blue'>Press 'Reserve a seat' button to continue with the payment procedure to reserve a seat in this course</font>";
   printf($course_box,$info['batch_id'],$title,$info['description'],$info['start_date'],$payment_status,$button_bar);
}

?>
