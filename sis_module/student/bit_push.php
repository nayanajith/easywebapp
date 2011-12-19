<?php 
include(A_CLASSES."/student_eligibility_class.php");

$res=exec_query("SELECT index_no from ".$GLOBALS['P_TABLES']["marks"]." where exam_id like '08%' limit 1,100",Q_RET_MYSQL_RES);
while($row=mysql_fetch_assoc($res)){
   //$eligibility=new Eligibility('0211303','BIT');
   $eligibility=new Eligibility($row['index_no'],'DIT');
   $student_state=$eligibility->eval_criteria();
   if($student_state['final']=='DIT'){
      echo "<br>".$row['index_no'].":";
      echo "Pass";   
   }
}

?>
