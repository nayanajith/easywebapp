<?php 
if((isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") || isset($_SESSION['user_id']) == false ){

echo "Please login to the system <a href='javascript:open_page(\"".MODULE."\",\"login\")'>HERE</a>";
}else{

$table = $GLOBALS['MOD_P_TABLES']["student"];
$query = "SELECT * FROM ".$table." WHERE student_id = '". $_SESSION['user_id']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);
$student = mysql_fetch_array($res);

echo '<h1>Personal</h1>';
echo "<hr style='border:1px solid silver;'/>";
echo '<table> <tr><td width = 70% valign = "top" >';
echo "<p>Welcome ".$student["first_name"]." ".$student['last_name']. " to your Personal page. Below you can view the details of the courses that you have applied for. </p>";

echo "<p> In order to change your personal details click <a href='javascript:open_page(\"courses\",\"st_reg\")'>HERE</a> </p>";
echo "<hr style='border:1px solid silver;'/>";
$table = $GLOBALS['MOD_P_TABLES']["reg"];
$query = "SELECT * FROM ".$table." WHERE student_id = '". $_SESSION['user_id']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);


//echo "<th>REG ID</th>";
d_r('dijit.form.Button');
while ($reg = mysql_fetch_array($res)){
   
   $table = $GLOBALS['MOD_P_TABLES']["schedule"];
   $query = "SELECT * FROM ".$table." WHERE session_id = '". $reg['session_id']."'" ;
   $res2 = exec_query($query,Q_RET_MYSQL_RES);
   $session = mysql_fetch_array($res2);
   
   $table = $GLOBALS['MOD_P_TABLES']["course"];
   $query = "SELECT * FROM ".$table." WHERE course_id = '". $session['course_id']."'" ;
   $res2 = exec_query($query,Q_RET_MYSQL_RES);
   $course = mysql_fetch_array($res2);   
   echo '<table width = 100%><tr><td >';
   echo '<h2>'.$course['short_name']." : ".$course['long_name'].'</h2>';
   echo '</td><td align = "right" >';
   echo '<form action= ""  method="get">
<input type = "hidden" name="module" value = "courses"/>
<input type = "hidden" name="page" value = "course_page"/>
<input type = "hidden" name="id" value = "'. $course['course_id'].'"/>'.'<button style = "align:right;font-size:12px;font-style:normal" dojoType="dijit.form.Button" type="submit" >Course Page</button>
</form>';
echo '</td></tr></table>';
   echo '<p>Conducted by  '.$course['lecturer'].' from '.$session['start_date'].' to '.$session['end_date'].' at '.$course['venue'].'</p>';
   echo '<table width = 100%><tr><td>';   
   echo 'Application Status : '.$reg['status'];
   echo '</td><td align = "right" >';

if($reg['status'] == 'RESERVED' || $reg['status'] == 'PENDING' ){
   $_SESSION['sid'] = $session['session_id'];
   echo '<form action= ""  method="get">
<input type = "hidden" name="module" value = "'.MODULE.'"/>
<input type = "hidden" name="page" value = "confirm"/>
<input type = "hidden" name = "reg_id" value = "'.$reg['reg_id'].'">
<button style = "align:right;font-size:12px;font-style:normal" dojoType="dijit.form.Button" type="submit" >Make Payment</button>
</form>';
}
   
}
echo "<h4><a href='javascript:open_page(\"".MODULE."\",\"courses\")'>Apply for new courses</a></h4>";

echo "<hr style='border:1px solid silver;'/>";

}





?>
