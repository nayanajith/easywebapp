<h3>Verify your details and do the payment</h3>
<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}
//Get course, batch, enroll information
$course_arr=exec_query("SELECT c.title,c.fee,b.start_date,b.batch_id FROM ".$GLOBALS['MOD_P_TABLES']['course']." c,".$GLOBALS['MOD_P_TABLES']['batch']." b, ".$GLOBALS['MOD_P_TABLES']['enroll']." e WHERE e.enroll_id='".$_SESSION['enroll_id']."' AND e.batch_id=b.batch_id AND b.course_id=c.course_id",Q_RET_ARRAY);
$course_arr=$course_arr[0];

//Get student information
$student_arr=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['student']." WHERE registration_no='".$_SESSION['user_id']."'",Q_RET_ARRAY);
$student_arr=$student_arr[0];

echo "<br/><br/><br/><table cellpadding='5'>
   <tr><th align='left'>Name</th><td>".$student_arr['first_name']." ".$student_arr['middle_names']." ".$student_arr['last_name']."</td></tr>
   <tr><th align='left'>NIC</th><td>".$student_arr['NIC']."</td></tr>
   <tr><th align='left'>Course applied</th><td>".$course_arr['title']."</td></tr>
   <tr><th align='left'>Course fee</th><td>RS ".sprintf("%.02f",$course_arr['fee'])."</td></tr>
   <tr><th align='left'>Course commence date</th><td>".$course_arr['start_date']."</td></tr>
   <tr><th align='left'>Batch</th><td>".$course_arr['batch_id']."</td></tr>
</table>";


echo "<br/><br/><br/><div align='right' class='buttonBar' >";
echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('ext_courses','available_courses')\">Available courses</button>";
echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('ext_courses','pay_offline')\">Pay offline to bank</button>";
echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('ext_courses','pay_online')\">Pay online&nbsp;&raquo;</button>";
echo "</div>";

?>
