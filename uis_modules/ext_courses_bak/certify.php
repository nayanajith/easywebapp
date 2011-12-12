<?php


$table = "reg";
$query = "SELECT * FROM ".$table." WHERE certificate_id = '".$_REQUEST['cert']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);

if($reg = mysql_fetch_array($res)){



$table = "student";
$query = "SELECT * FROM ".$table." WHERE student_id = '". $reg['student_id']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);
$student = mysql_fetch_array($res);

$table = "schedule";
$query = "SELECT * FROM ".$table." WHERE session_id = '". $reg['session_id']."'" ;
$res2 = exec_query($query,Q_RET_MYSQL_RES);
$session = mysql_fetch_array($res2);
   
$table = "course";
$query = "SELECT * FROM ".$table." WHERE course_id = '". $session['course_id']."'" ;
$res2 = exec_query($query,Q_RET_MYSQL_RES);
$course = mysql_fetch_array($res2);  

echo '<h1>Course Completion Certificate</h1>';
echo '<h2> Awarded to '.$student['title']." ". $student['first_name']." ".$student['last_name'].'</h2>';
echo '<h2> For the Successful completion of '. $course['long_name'].'</h2>';
echo '<h3> Conducted by '.$course['lecturer'].'</h3>';
echo '<h3> From '.$session['start_date'].' to '.$session['end_date'].'</h2>';  

}else{
echo '<p style = "color:red" >Invalid Certificate</p>';
}
?>
