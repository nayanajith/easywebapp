<?php

echo '<table > <tr><td width = 150% valign = "top" >';
echo '<h1>Summary</h1>';


   $table4 = "course";
   $query4 = "SELECT count(*) as count FROM ".$table4." WHERE display = 'YES'";
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $count = mysql_fetch_array($res4);
   
   echo '<p>There are '.$count['count'].' courses currently available for registration</p>';

  
   $table2 = "course";
   $query2 = "SELECT * FROM ".$table2." WHERE display = 'YES'" ;
   $res2 = exec_query($query2,Q_RET_MYSQL_RES);
   
if($count['count'] > 0){

echo "<table style = 'border:1px solid black' >";
echo "<tr  >";
//echo "<th>ID</th>";
echo "<th >Course Code</th>";
echo "<th >Name</th>";
echo "<th >Lecturer</th>";

while($course = mysql_fetch_array($res2)){

echo '<tr>';
   
   echo "<td>".$course['short_name']."</td>";
   echo "<td>".$course['long_name']."</td>";
   echo "<td>".$course['lecturer']."</td>";
   
   echo "</tr>"; 

}

echo "</table>";
}



   $table4 = "schedule";
   $query4 = "SELECT count(*) as count FROM ".$table4." WHERE start_date > Now() AND start_date < 'Now()+30'";
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $count = mysql_fetch_array($res4); 
   
echo '<p> There are '.$count['count'].' sessions which will start in the next 30 days</p>';
   
   $table2 = "schedule";
   $query2 = "SELECT * FROM ".$table4." WHERE start_date > Now() AND start_date < 'Now()+30'";
   $res2 = exec_query($query2,Q_RET_MYSQL_RES);
   
if($count['count'] > 0){

echo "<table style = 'border:1px solid black' >";
echo "<tr  >";
//echo "<th>ID</th>";
echo "<th >Session ID</th>";
echo "<th >Course Code</th>";
echo "<th >Session Name</th>";
echo "<th >Start Date </th>";
echo "<th >End Date</th>";
echo "<th >Places Taken</th>";

echo "</tr>";

while($session = mysql_fetch_array($res2)){

echo '<tr>';

   $table4 = "reg";
   $query4 = "SELECT count(*) as count FROM ".$table4." WHERE session_id = '".$session['session_id']."' AND ( status = 'RESERVED' OR status = 'CONFIRMED' OR status = 'PENDING' OR status = 'PAID' )" ;
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $row4 = mysql_fetch_array($res4); 
   
   $table4 = "course";
   $query4 = "SELECT * FROM ".$table4." WHERE  course_id = '".$session['course_id']."'";
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $course = mysql_fetch_array($res4);  
   
   
   echo "<td>".$session['session_id']."</td>";
   echo "<td>".$course['short_name']."</td>";
   echo "<td>".$session['session_name']."</td>";
   echo "<td>".$session['start_date']."</td>";
   echo "<td>".$session['end_date']."</td>"; 
   echo "<td align = 'right' >".$row4['count']." / ".$course['seating_limit']."</td>";      
   echo "</tr>"; 

}

echo "</table>";
}


   $table4 = "reg";
   $query4 = "SELECT count(*) as count FROM ".$table4." WHERE status = 'PENDING'";
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $count = mysql_fetch_array($res4); 
   
echo '<p> There are '.$count['count'].' offline payments awaiting receipt</p>';
   
   $table2 = "reg";
   $query2 = "SELECT * FROM ".$table2." WHERE status = 'PENDING'" ;
   $res2 = exec_query($query2,Q_RET_MYSQL_RES);
   
if($count['count'] > 0){

echo "<table style = 'border:1px solid black' >";
echo "<tr  >";
//echo "<th>ID</th>";
echo "<th >Student ID</th>";
echo "<th >Student Name</th>";
echo "<th >Course Code</th>";
echo "<th >Session Name</th>";
echo "<th >Start date</th>";
echo "</tr>";

while($reg = mysql_fetch_array($res2)){

echo '<tr>';
   $table4 = "student";
   $query4 = "SELECT * FROM ".$table4." WHERE student_id = '".$reg['student_id']."'";
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $student = mysql_fetch_array($res4); 

   $table4 = "schedule";
   $query4 = "SELECT * FROM ".$table4." WHERE  session_id = '".$reg['session_id']."'";
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $session = mysql_fetch_array($res4);    
   
   $table4 = "course";
   $query4 = "SELECT * FROM ".$table4." WHERE  course_id = '".$session['course_id']."'";
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $course = mysql_fetch_array($res4);  
   
   
   echo "<td>".$student['student_id']."</td>";
   echo "<td>".$student['title']." ".$student['first_name']." ".$student['last_name']."</td>";
   echo "<td>".$course['short_name']."</td>";
   echo "<td>".$session['session_name']."</td>";
   echo "<td>".$session['start_date']."</td>";
   
   echo "</tr>"; 

}

echo "</table>";
}





   $table4 = "reg";
   $query4 = "SELECT count(*) as count FROM ".$table4." WHERE status = 'RESERVED'";
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $count = mysql_fetch_array($res4); 
   
echo '<p> There are '.$count['count'].' reservations which have not yet been confirmed.</p>';
   
   $table2 = "reg";
   $query2 = "SELECT * FROM ".$table2." WHERE status = 'RESERVED'" ;
   $res2 = exec_query($query2,Q_RET_MYSQL_RES);
   
if($count['count'] > 0){

echo "<table style = 'border:1px solid black' >";
echo "<tr  >";
//echo "<th>ID</th>";
echo "<th >Student ID</th>";
echo "<th >Student Name</th>";
echo "<th >Course Code</th>";
echo "<th >Session Name</th>";
echo "<th >Start date</th>";
echo "</tr>";

while($reg = mysql_fetch_array($res2)){

echo '<tr>';
   $table4 = "student";
   $query4 = "SELECT * FROM ".$table4." WHERE student_id = '".$reg['student_id']."'";
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $student = mysql_fetch_array($res4); 

   $table4 = "schedule";
   $query4 = "SELECT * FROM ".$table4." WHERE  session_id = '".$reg['session_id']."'";
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $session = mysql_fetch_array($res4);    
   
   $table4 = "course";
   $query4 = "SELECT * FROM ".$table4." WHERE  course_id = '".$session['course_id']."'";
   $res4 = exec_query($query4,Q_RET_MYSQL_RES); 
   $course = mysql_fetch_array($res4);  
   
   
   echo "<td>".$student['student_id']."</td>";
   echo "<td>".$student['title']." ".$student['first_name']." ".$student['last_name']."</td>";
   echo "<td>".$course['short_name']."</td>";
   echo "<td>".$session['session_name']."</td>";
   echo "<td>".$session['start_date']."</td>";
   
   echo "</tr>"; 

}

echo "</table>";
}




echo "</td></tr></table>";










?>
