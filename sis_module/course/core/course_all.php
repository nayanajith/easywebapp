<?php
if(!isset($_SESSION['username'])){return;}
/*
 *
 * This file is to be included in reports.php
 * As a part of it 
 * 
 * It will not work alone
 *
 */

echo "
<table class=data_table ><tr><td class='summery round'><h3 id=summery_title>Summary of $examid</h3>
<div class=summery style='border:0px;' id=summery>
";


$query  = "SELECT DISTINCT courseid FROM $table WHERE examid='$examid' ORDER BY courseid";   
$courses=item_array($query);

echo "<table class=clean border=1 style='border-collapse:collapse;' cellpadding=5>";
$th     ="<tr><th>Courseid</th>"; 
   //printing headers with urls
   foreach($menu_action as $key => $value){
      if($key != "summery"){
         $th.="<th>$value</th>";
      }
   }
$th.="<th>MAX</th><th>MIN</th><th>AVG</th><th>STD</th><th>Grades count</th><tr>";

echo $th;


foreach($courses as $courseid){
   $condition_c      ="examid='$examid' AND courseid='$courseid'";

   $condition_pass   =$condition_c." AND (marks3 >= 50 or final = 'cm')";
   $condition_fail1  =$condition_c." AND (!(final='ab' OR final='mc') AND final = 'nc' )";
   $condition_fail2  =$condition_c." AND (!(final='ab' OR final='mc') AND marks3 < 50 )";
   $condition_repeat =$condition_c." AND !(final='ab' OR final='mc') AND (indexno NOT LIKE '".find_repeat($examid,false,$courseid)."%')";
   $condition_absent =$condition_c." AND (final='ab' OR final='mc')";
   $condition_medical=$condition_c." AND (final='mc') ";
   $condition_stat   =$condition_c." AND !(final='ab' OR final='mc')";


   if($summery || (!empty($courseid) && !empty($examid) && !empty($table))){

      echo "<tr><th><a href='?table=$table&examid=$examid&courseid=$courseid'>$courseid</a></th>";

      //set all functions to count mode
      $count     =2;

      //Count all students
      $all_count  =print_table($table,$cols,$condition_c,"final","All",$count);

      //Count passed students
      $pass_count =print_table($table,$cols,$condition_pass,"marks3 DESC",'Pass',$count);

      //Count failed students ENH1001 and ENH1002 have exceptions
      if($courseid == 'ENH1001' || $courseid == 'ENH1002'){
         $fail_count =print_table($table,$cols,$condition_fail1,"marks3 DESC","Fail",$count);
      }else{
         $fail_count =print_table($table,$cols,$condition_fail2,"marks3 DESC","Fail",$count);
      }

      //Count Repeaters
      print_table($table,$cols,$condition_repeat,"indexno DESC","Repeaters",$count);

      //Count Absent students
      $ab=print_table($table,$cols,$condition_absent,"final","Absent",$count);

      //Count the students who submit medicals
      print_table($table,$cols,$condition_medical,"final","Medical",$count);

      //Print Pass precentage over all students
      echo "<td>".round(($pass_count/$all_count)*100,2)."|".round(($pass_count/($all_count-$ab))*100,2)."</td>";

      //Print statistics of the course
      //$cols_stat=array("max(marks3)"=>"Max","min(marks3)"=>"Min","avg(marks3)"=>"Avg","std(marks3)"=>"Std");
      //print_table($table,$cols_stat,$condition_stat,"indexno","Statistics",$stat);
      
      print_stat($table,$examid,$courseid);
      echo "<td>";
      echo "<a title='".print_grade_count($table,$examid,$courseid)."'>&#187;    &#187;</a>";
      echo "</td>";
      echo "</tr>";
   }
}
   echo $th;
   echo "</table>";
   echo "
<script type="text/javascript">
function print_area(id){
   print_text=document.getElementById(id).innerHTML;
   title=document.getElementById('summery_title').innerHTML;
   win_obj=window.open(null,null,'height=600,width=600');
   win_obj.document.open();
   win_obj.document.write('<h3>'+title+'</h3>'+print_text);

}
</script> 
      ";
echo "</div>
   <a href='javascript:print_area(\"summery\")'>print</a>
   </td><td class='detail round'><h3>Detail</h3>";
echo "<div class=data style='border:0px;'>";
echo "</div></td></tr></table>";
?>
