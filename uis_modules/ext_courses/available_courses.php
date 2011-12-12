<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}


//First timers will provided with a registration number
if(isset($_SESSION['first_time']) &&  $_SESSION['first_time']==true){
   //Get the students record_id
	$arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['student']." WHERE email_1='".$_SESSION['username']."'",Q_RET_ARRAY);

   //Generate the registration number for the student
	$_SESSION['user_id']=gen_reg_no($arr[0]['rec_id']);

   //Update the students record with registration number
   exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES']['student']." set registration_no='".$_SESSION['user_id']."' WHERE rec_id='".strtoupper($_SESSION['user_id'])."'",Q_RET_NONE);

   //Registering the student with his first course
   $batch_arr=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['batch']." WHERE course_code='".$_SESSION['course_code']."' AND b.start_date > current_date ORDER BY start_date DESC LIMIT 1 ",Q_RET_ARRAY);
   $max_seats  =$batch_arr[0]['seats'];
   $batch_id   =$batch_arr[0]['batch_id'];

   $seats_avail=exec_query("SEELCT (".$max_seats." - COUNT(*)) seats FROM ".$GLOBALS['MOD_P_TABLES']['enroll']." WHERE batch_id='".$batch_id."'",Q_RET_ARRAY);
   if($seats_avail[0]['seats'] >=0){
      exec_query("INSERT INTO ".$GLOBALS['MOD_P_TABLES']['enroll']."(`registration_no`,`batch_id`)values('".$_SESSION['user_id']."','".$batch_id."') ",Q_RET_NONE);
   }else{
      echo "Sorry current batch for this course is full. Please contact CSC or apply for the next batch."; 
   }

}
echo $_SESSION['user_id'];
$reg_arr=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['registration'],Q_RET_ARRAY,null,'batch_id');
$my_courses=array();
foreach($reg_arr as $batch_id => $info){
   $my_courses[]=$info['course_code'];
   
}
$filter="";
if(sizeof($my_courses) > 0 ){
   $filter="AND b.course_code NOT IN('".implode("','",$my_courses)."')";
}

$batch_arr=exec_query('SELECT DISTINCT c.course_code,c.description FROM '.$GLOBALS['MOD_P_TABLES']['course'].' c,'.$GLOBALS['MOD_P_TABLES']['batch'].' b WHERE  c.course_code=b.course_code '.$filter.' and c.disabled=0 and b.start_date > current_date',Q_RET_ARRAY,null,'course_code');

print_r($batch_arr);

?>
