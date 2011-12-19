<?php
if(!isset($_SESSION['username'])){return;}
/*
 *
 * This file is to be included in reports.php
 * As a part of it 
 * 
 * It will not work alone
 *
 *
 */
echo "
<table class=data_table ><tr><td class='summery round'><h3>Summary</h3>
<div class=summery style='border:0px;'>
";

/*
 * All Conditions  which incorporated in presenting course results
 *
 */
$condition_c      ="examid='$examid' AND courseid='$courseid'";

$condition_pass   =$condition_c." AND (marks3 >= 50 or final = 'cm')";
$condition_fail1  =$condition_c." AND (!(final='ab' OR final='mc') AND final = 'nc' )";
$condition_fail2  =$condition_c." AND (!(final='ab' OR final='mc') AND marks3 < 50 )";
$condition_repeat =$condition_c." AND !(final='ab' OR final='mc') AND (indexno NOT LIKE '".find_repeat($examid,false,$courseid)."%')";
$condition_absent =$condition_c." AND (final='ab' OR final='mc')";
$condition_medical=$condition_c." AND (final='mc') ";
$condition_stat   =$condition_c." AND !(final='ab' OR final='mc')";


//prnt summery of the course
if($summery || (!empty($courseid) && !empty($examid) && !empty($table))){

   echo "<table class=clean border=1><tr>";

   //printing headers with urls
   foreach($menu_action as $key => $value){
      if($key != "summery"){
         echo "<th><a href='$url&$key=1'>$value</a></th>";
      }
   }
   echo "<tr>";

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
   echo "<td>".round(($pass_count/$all_count)*100,2)."|".round(($pass_count/($all_count-$ab))*100,2)."</td></tr></table><br>";

   //Print statistics of the course
   $cols_stat=array("max(marks3)"=>"Max","min(marks3)"=>"Min","avg(marks3)"=>"Avg","std(marks3)"=>"Std");
   print_table($table,$cols_stat,$condition_stat,"indexno","Statistics",$stat);
      echo "<br>";
      echo print_grade_count($table,$examid,$courseid,true);
}

echo "</div></td><td class='detail round'><h3>Detail</h3>";
echo "<div class=detail style='border:0px;'>";
if($all==1){
   print_table($table,$cols,$condition_c,"final,abs(indexno)","All",$all);
}
if($pass==1){
   print_table($table,$cols,$condition_pass,"marks3 DESC,abs(indexno)",'Pass',$pass);
}
if($fail==1){
   if($courseid == 'ENH1001' || $courseid == 'ENH1002'){
      print_table($table,$cols,$condition_fail1,"marks3 DESC,abs(indexno)","Fail",$fail);
   }else{
      print_table($table,$cols,$condition_fail2,"marks3 DESC,abs(indexno)","Fail",$fail);
   }
}
if($repeat==1){
   print_table($table,$cols,$condition_repeat,"final,abs(indexno)","Repeaters",$repeat);
}
if($absent==1){
   print_table($table,$cols,$condition_absent,"final,abs(indexno)","Absent",$absent);
}
if($medical==1){
   print_table($table,$cols,$condition_medical,"final,abs(indexno)","Medical",$medical);
}

echo "</div></td></tr></table>";

?>
