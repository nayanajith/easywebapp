<html>
<head>
</head>

<?php
if((isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") || isset($_SESSION['user_id']) == false ){

echo "Please login to the system <a href='javascript:open_page(\"courses\",\"login\")'>HERE</a>";
}else{
include A_CLASSES."/data_entry_class.php";
$table = $GLOBALS['MOD_P_TABLES']['course'];


$query = "SELECT * FROM ".$table." WHERE display = 'YES' ";

$res = exec_query($query,Q_RET_MYSQL_RES);


echo "<h1>Find Courses</h1>";
echo "<hr style='border:1px solid silver;'/>";
echo '<table> <tr><td width = 70% valign = "top" >';
echo "<h4>This page lists all the courses which you can apply for. If you find one that you are interested in, you can click on apply to find out more about the course as well as to proceed with the application for it.</h4>";
echo "<hr style='border:1px solid silver;'/>";
//echo "<script type = javascript >document.getElementById('right').innerHTML = window.location.href;</script>"


$print = 'NO';
while($row = mysql_fetch_array($res))
  {

  
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
 
  if($chkapp == 'NO'){
   echo '<table width = 100%><tr><td >';
   echo '<h2>'.$row['long_name'].'</h2>';

   echo '</td><td align = "right" >';
   echo '<p style = "font-size:14px">  Course Code : '.$row['short_name'].'</p>';   
   echo '</td></tr></table>';

   //echo '<p>'.$row['descr'].'</p>';
   echo '<table width = 100%><tr><td>';   
   echo '<p>Conducted by  '.$row['lecturer'].' at '.$row['venue'].'</p>';  
   echo '</td><td align = "right" >';
   d_r('dijit.form.Button');
   echo '<form action= ""  method="get">
<input type = "hidden" name="module" value = "'.MODULE.'"/>
<input type = "hidden" name="page" value = "course_page"/>
<input type = "hidden" name="id" value = "'. $row['course_id'].'"/>
<button dojoType="dijit.form.Button" type="submit" >Apply</button>
</form>';
   echo '</td></tr></table>';
   echo "<hr style='border:1px solid silver;'/>";
   /*
  echo "<tr>";
  echo "<td><p>" . $row['short_name'] . "</p></td>";
  echo "<td><p>" . $row['long_name'] . "</p></td>";  
  echo "<td><p>" .'<form action= ""  method="get">
<input type = "hidden" name="module" value = "courses"/>
<input type = "hidden" name="page" value = "course_page"/>
<input type = "hidden" name="id" value = "'. $row['course_id'].'"/>
<button dojoType="dijit.form.Button" type="submit" >Apply</button>
</form>'. "</td>";
echo "</tr>";*/

$print = 'YES';
}else{
 // echo "<td><p> Already Applied </p></td>";
}


  }

if($print == 'NO'){
echo 'Sorry, You are not eligible for any of the courses currently offered. Please check again later. Thank you';
}

echo "</td><td valign = 'top' style = 'border-left:1px solid silver'>";
echo '<h4>Course Application procedure</h4>';
echo "<ol>
		<li>Find a course that you are interested in completing from the <a href='javascript:open_page(\"courses\",\"courses\")'>Find Courses</a> Page</li>
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
