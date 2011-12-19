<?php
session_start();
include_once("config.php");
include_once('common.php');
$page_title="Grades";
include_once('header.php');
?>
<table width=600px; style='margin-left:auto;margin-right:auto' >
<tr>
<td>
<?php
echo "
<ul class=ul_menu>
   <li><a href='$url&enter=1'>Enter</a></li>
   <li><a href='$url&reports=1'>Reports</a></li>
</ul>
";
?>
</td>
<td>
<?php
include "course_browse.php";
?>
</td>
<td>
<?php

$indexno =$_GET['indexno'];
$courseid=$_GET['courseid'];
$examid  =$_GET['examid'];
$table   =$_GET['table'];

$menu_action=array(
'all'    =>'All',
'pass'   =>'Pass',
'fail'   =>'Fail',
'repeat' =>'Repeat',
'absent' =>'Absent',
'medical'=>'Medical',
'pass%'  =>'Pass%',
'summery'=>'Summery'
);

$url     ="?"; 

foreach($_GET as $key => $value){
   if(!array_key_exists($key,$menu_action)){
      $url.="$key=$value&";
   }
}



echo "<ul class=ul_menu>";
foreach($menu_action as $key => $value){
   echo "<li><a href='$url&$key=1'>$value</a></li>";
}
echo "</ul>";
?>
</td>
</tr>
</table>
<?php
openDB();

$all     =$_GET['all'];
$repeat  =$_GET['repeat'];
$absent  =$_GET['absent'];
$medical =$_GET['medical'];
$fail    =$_GET['fail'];
$pass    =$_GET['pass'];
$summery =$_GET['summery'];

$cols =array('indexno'=>'Index No','marks3'=>'Marks','final'=>'Grades');

function count_rows($table,$condition){
   $query="
   SELECT  COUNT(indexno)
   FROM     $table 
   WHERE    $condition 
   ";

   $result  = mysql_query($query, $GLOBALS['CONNECTION']);
   $row     = mysql_fetch_array($result);
   return $row['COUNT(indexno)'];
}

function count_rowsq($query){
   $result  = mysql_query($query, $GLOBALS['CONNECTION']);
   $row     = mysql_fetch_array($result);
   return $row['COUNT(indexno)'];
}

function find_repeat($examid,$indexno,$courseid){
   //1011,0931,1031
   //09002323
   $query="select indexno from csmarks where examid='$examid' and courseid='$courseid' ORDER BY ABS(indexno) DESC LIMIT 1,1 ;";
   $result  = mysql_query($query, $GLOBALS['CONNECTION']);
   $row     = mysql_fetch_array($result);

   $index   =$row[0];

   $cur_year   =(int)substr($examid,0,2); 
   $ac_year    =(int)substr($examid,-2,1); 
   $reg_year   =substr($index,0,2);

   //echo "cur:$cur_year,ac:$ac_year,reg:$reg_year<br>";
   if($indexno){
      $reg_year   =substr($indexno,0,2);

   //Some registration numbers have letters, eg: A1113
      if(is_numeric($reg)){
         $reg_year   =(int)$reg_year;
      }else if(substr($indexno,0,1) == 'A'){
         $reg_year   =3;
      }

     /*If index number provided it will return the remainder 0 for CURRENT students >0 for REPEAT STUDENTS*/
      return $ac_year-($cur_year-$reg_year);
   }
   else{
      /*will return CURRENT (not repeating) students registration year*/
      return $reg_year;
   }
   
}

/*
 * $table      : table to be query
 * $cols_th    : Columns to select from the query
 * $condition  : Further conditions to filter data in query 
 * $orderby    : Order by which column?
 * $title      : Title of the printed table
 * $co         : if $co=>true it will return count of the rows else return full table
 */

function print_table($table,$cols_th,$condition,$orderby,$title,$co){
   $count=0;
   global $examid;
   $th   =array();
   $cols =array();

   foreach($cols_th as $key => $value){
      $cols[]  =$key;
      $th[]    =$value; 
   }

   if($co==2){
      $count= count_rows($table,$condition);
      echo "<td>$count</td>";
   }else{
      $count= count_rows($table,$condition);

      $query="
      SELECT   ".implode(',',$cols)." 
      FROM     $table 
      WHERE    $condition 
      ORDER BY $orderby
      ";

      $result  = mysql_query($query, $GLOBALS['CONNECTION']);
      echo "<table border=1 class=clean>\n<tr><th colspan=".sizeof($cols).">$title ($count)</th></tr>\n";
      echo "<tr><th>".implode('</th><th>',$th)."</th></tr>\n";
       while($row = @ mysql_fetch_array($result)){
         echo "<tr>\n";
         foreach($cols as $col){
            echo "<td class=color".find_repeat($examid,$row['indexno']).">".$row[$col]."</td>\n"; 
         }
         echo "</tr>\n";
      }
      echo "</table>\n";
   }
   return $count;
}

/*
 * $cols_th    : Columns to select from the query
 * $condition  : Query 
 * $orderby    : Order by which column?
 * $title      : Title of the printed table
 * $co         : if $co=>true it will return count of the rows else return full table
 */

function print_tableq($cols_th,$query,$orderby,$title,$co){
   $count=0;
   global $examid;
   $th   =array();
   $cols =array();

   foreach($cols_th as $key => $value){
      $cols[]  =$key;
      $th[]    =$value; 
   }

   if($co==2){
      $count= count_rowsa($query);
      echo "<td>$count</td>";
   }else{
      $count= count_rows($query);

      $result  = mysql_query($query, $GLOBALS['CONNECTION']);
      echo "<table border=1 class=clean>\n<tr><th colspan=".sizeof($cols).">$title ($count)</th></tr>\n";
      echo "<tr><th>".implode('</th><th>',$th)."</th></tr>\n";
       while($row = @ mysql_fetch_array($result)){
         echo "<tr>\n";
         foreach($cols as $col){
            echo "<td class=color".find_repeat($examid,$row['indexno']).">".$row[$col]."</td>\n"; 
         }
         echo "</tr>\n";
      }
      echo "</table>\n";
   }
   return $count;
}


//include ("stream.php");
//include ("batch.php");
//include ("semester.php");
//include ("course.php");
include ("course_all.php");
//include ("student.php");
?>

</body>
</html>

