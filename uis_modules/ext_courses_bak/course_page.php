<?php
if((isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") || isset($_SESSION['user_id']) == false ){

echo "Please login to the system <a href='javascript:open_page(\"courses\",\"login\")'>HERE</a>";
}else{

$table = $GLOBALS['MOD_P_TABLES']["course"];
$query = "SELECT * FROM ".$table." WHERE course_id = '". $_REQUEST['id']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);
$row = mysql_fetch_array($res);

echo "<h1>".$row['short_name'].": ".$row['long_name']."</h1>";

echo "<hr style='border:1px solid silver;'/>";

echo '<table> <tr><td width = 70% valign = "top" >';
echo "<p>".$row['descr']."</p>";
echo "<hr style='border:1px solid silver;'/>";

echo "<table>";
echo "<tr><td>";
echo $row['descr'];
echo "</td></tr>";
echo "<tr><td>Conducted By:</td>";
echo "<td>".$row['lecturer']."</td></tr>";
echo "<tr><td>Venue:</td>";
echo "<td>".$row['venue']."</td></tr>";

echo "<tr><td>Time:</td>";
echo "<td>".$row['time']."</td></tr>";

echo "<tr><td>Course fee:</td>";
echo "<td>Rs.".$row['course_fee']."/-</td></tr>";
echo "</table>";
//echo '<br/>';
echo "<hr style='border:1px solid silver;'/>";
echo "<p>Available sessions are displayed below. Click Apply if u wish to attend one of these sessions.</p>";
//echo '<br/>';
echo "<hr style='border:1px solid silver;'/>";

$table2 = $GLOBALS['MOD_P_TABLES']["schedule"];
  $query2 = "SELECT * FROM ".$table2." WHERE course_id = '".$row['course_id']."' ";
  $res2 = exec_query($query2,Q_RET_MYSQL_RES);
  $chkapp = 'NO';
  
  while($row2 = mysql_fetch_array($res2)){
    $table3 = $GLOBALS['MOD_P_TABLES']["reg"];
  $query3 = "SELECT * FROM ".$table3." WHERE session_id = '".$row2['session_id']."' AND student_id = '".$_SESSION['user_id']."' ";
      $res3 = exec_query($query3,Q_RET_MYSQL_RES);
      
      if($row3 = mysql_fetch_array($res3)){
         $chkapp = 'YES';
      }
  } 

$table2 = $GLOBALS['MOD_P_TABLES']["schedule"];
$query2 = "SELECT * FROM ".$table2." WHERE course_id = '". $_REQUEST['id']."' AND ( start_date >= date(Now()) AND display = 'YES' )" ;
$res2 = exec_query($query2,Q_RET_MYSQL_RES);
echo "<table >";
echo "<tr  >";
//echo "<th>ID</th>";
echo "<th >Session</th>";
echo "<th >Start date</th>";
echo "<th >End date</th>";
echo "<th >Places Taken</th>";
echo "<th></th>";
echo "</tr>";
d_r('dijit.form.Button');
while ($row2 = mysql_fetch_array($res2)){
   echo "<tr>";
   
   $table3 = $GLOBALS['MOD_P_TABLES']["reg"];
   $query3 = "SELECT * FROM ".$table3." WHERE session_id = '". $row2['session_id']."' AND student_id = '".$_SESSION['user_id']."'" ;
   $res3 = exec_query($query3,Q_RET_MYSQL_RES);
   
   
  // echo "<td>".$row2['session_id']."</td>";
   echo "<td>".$row2['session_name']."</td>";
   echo "<td>".$row2['start_date']."</td>";
   echo "<td>".$row2['end_date']."</td>";
  
   $table4 = $GLOBALS['MOD_P_TABLES']["reg"];
   $query4 = "SELECT count(*) as count FROM ".$table4." WHERE session_id = '".$row2['session_id']."' AND ( status = 'RESERVED' OR status = 'CONFIRMED' OR status = 'PENDING' OR status = 'PAID' )" ;
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $row4 = mysql_fetch_array($res4); 

   echo "<td align = 'center' >".$row4['count'].'/'.$row['seating_limit']."</td>";
      
   if($row3 = mysql_fetch_array($res3)){
      echo "<td><p> You have already applied for this session</p></td>";
   }else{
      if($chkapp == 'YES'){
         echo "<td><p> You already applied for this course</p></td>";   
      }else{
         //echo $row3['count'];
         //echo $row['seating_limit'];
         if($row4['count'] >= $row['seating_limit'] ){
            echo "<td><p> Seating Limit reached</p></td>";
         }else{
            echo "<td><p>" .'<form action= ""  method="get">
            <input type = "hidden" name="module" value = "'.MODULE.'"/>
            <input type = "hidden" name="page" value = "confirm"/>
            <input type = "hidden" name="sid" value = "'. $row2['session_id'].'"/>
            <button dojoType="dijit.form.Button" type="submit" >Apply</button>
            </form>'. "</td>";
         }
      }
   }
   echo "</tr>";
}
echo "</table>";

echo "<hr style='border:1px solid silver;'/>";
echo "<p>For Inquiries regarding this course please contact: ".$row['administrator']." on ".$row['contact_number']."</p>";

echo "</td><td valign = 'top' style = 'border-left:1px solid silver' >";
echo '<h4>Course Application procedure</h4>';
echo "<ol>
		<li>Find a course that you are interested in completing from the <a href='javascript:open_page(\"".MODULE."\",\"courses\")'>Find Courses</a> Page</li>
		<li>Once you have found such a course click on the Apply button next to it to go to the course page</li>
		<li>In the course page, Apply for a session which you are able to attend. The available sessions are displayed at the bottom of that page</li>
		<li>Then you must confirm your attendance by selecting a payment method. Note that unless you confirm this, your place will not be reserved</li>
		<li>Once you have confirmed, you are then able to pay either online or offline</li>
		<li>If you pay online, your place will be confirmed as soon as the payment goes through</li>
		<li>If you pay offline, your place will be confirmed when the payment has been recieved. Until such time, the status of your application will be set to 'PENDING' </li>
			</ol>";
echo "</td></tr></table>";
}

?>
