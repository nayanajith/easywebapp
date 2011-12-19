<?php 
//$paper_id =1;  
//$section  =4;

/*
$res_section_four=exec_query("SELECT exam_no,question_no_50 FROM ".$GLOBALS['X_TABLES']['post_processing'],Q_RET_MYSQL_RES);
while($row=mysql_fetch_assoc($res_section_four)){
   $index_no=$row['exam_no'];
   $mark      =0;
   switch($row['question_no_50']){
      case 'CORRECT':
         $mark      =100;
      break;
      case 'INCORRECT':
         $mark      =-25;
      break;
      case 'NOT_ANSWERED':
         $mark      =0;
      break;
   }
   $res=exec_query("REPLACE INTO ".$GLOBALS["MOD_P_TABLES"]['marks']."(index_no,paper_id,section,mark) values($index_no,$paper_id,$section,$mark)",Q_RET_MYSQL_RES);
}

exit();
*/
if(isset($_REQUEST['data']) && $_REQUEST['data']=='csv'){
   $file_name="final_result_file_".$_REQUEST['paper_id'].".csv";
   $result_path=MOD_A_CSV."/".$file_name;

   header('Content-Type', 'application/vnd.ms-excel');
   header('Content-Disposition: attachment; filename='.$file_name.'.csv');
   if(file_exists($result_path)){
      readfile($result_path);
   }
   exit();
}


if(isset($_REQUEST['form']) && isset($_REQUEST['action']) && $_REQUEST['action']=='generate'){

$arr=explode('@',$_REQUEST['paper_id']);
$SQL="SELECT paper_id FROM ".$GLOBALS['MOD_P_TABLES']['paper']." WHERE course_id='".$arr[0]."' AND exam_id='".$arr[1]."'";
$arr=exec_query($SQL,Q_RET_ARRAY);
$paper_id=$arr[0]['paper_id'];




$result_path=MOD_A_CSV."/final_result_file_".$paper_id.".csv";
$result_web_path=MOD_W_CSV."/final_result_file_".$paper_id.".csv";
$result_sheet = fopen($result_path, 'w');

//$res_student_info=exec_query("SELECT a.index_no,b.exam_no,a.fullname,a.nic,c.absent FROM ".$GLOBALS['X_TABLES']['student']." a, ".$GLOBALS['X_TABLES']['student_alloc']." b, ".$GLOBALS['X_TABLES']['post_processing']." c  WHERE  a.index_no=b.index_no and b.exam_no=c.exam_no ORDER BY c.exam_no",Q_RET_MYSQL_RES);

$res_student_info=exec_query("SELECT a.index_no,b.exam_no,a.fullname,surname,surname_2,c.nic,c.absent FROM ".$GLOBALS['X_TABLES']['student']." a, ".$GLOBALS['X_TABLES']['student_alloc']." b, ".$GLOBALS['X_TABLES']['post_processing']." c  WHERE  a.index_no=b.index_no and b.exam_no=c.exam_no ORDER BY a.surname_2",Q_RET_MYSQL_RES);

$header      =array();
$values      =array();

$header_inserted=false;

while($user_info=mysql_fetch_assoc($res_student_info)){

   $marks=array(
      "section-1"=>" ",
      "section-2"=>" ",
      "section-3"=>" ",
      "section-4"=>" "
   );

   if($user_info['absent']==0){
      $res_student_mark=exec_query("SELECT section,mark FROM ".$GLOBALS["MOD_P_TABLES"]['marks']." WHERE index_no='".$user_info['exam_no']."' ORDER BY section",Q_RET_MYSQL_RES);

      while($mark_row=mysql_fetch_assoc($res_student_mark)){
         $marks["section-".$mark_row['section']]=$mark_row['mark'];
      }
   }

   $header=array_merge(array_keys($user_info),array_keys($marks));
   $values=array_merge(array_values($user_info),array_values($marks));

   if(!$header_inserted){
      fputcsv($result_sheet, $header);
      $header_inserted=true;
   }

   fputcsv($result_sheet, $values);
}

/*Close csv file*/
fclose($result_sheet);
   
return_status_json("OK","<a href=\'".gen_url()."&data=csv&paper_id=".$paper_id."\'>download</a>");
exit();
}

?>
<div id='process_frm' name='process_frm' dojoType='dijit.form.Form'>
<select name='paper_id' id='paper_id' dojoType='dijit.form.ComboBox'>
      <?php
      $SQL="SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['paper'];
      $RESULT=exec_query($SQL,Q_RET_MYSQL_RES);
      while( $ROW = mysql_fetch_assoc($RESULT) ) {
         echo "<option value='".$ROW['paper_id']."'>".$ROW['course_id']."@".$ROW['exam_id']."</option>";
      }
      ?>
</select>
</div>
<div id='inner_div'>
...
</div>

<script type="text/javascript" >
   function submit_form(action){
      update_status_bar('...');
      update_progress_bar(10);

      dojo.xhrGet({
         url         : '<?php echo gen_url(); ?>&form=main&action='+action, 
         handleAs      : 'json',
         //handleAs      : 'text',
         form         : 'process_frm', 
      
         handle: function(response,ioArg) {
            //alert(response);
             var status=response.status.toUpperCase();
            switch(status){
               case 'OK':
                  update_status_bar('report generated');
                  var idiv=document.getElementById('inner_div');
                  idiv.innerHTML=response.info;
               break;
               case 'ERROR':
                  update_status_bar(response.info);
               break;
               case 'NOT_DEFINED':
                  update_status_bar(response.info);
               break;
               default:
                  update_status_bar(response.info);
               break;
            }
            update_progress_bar(100);
         }, 
         load: function(response) {
            update_status_bar('rquest sent successfully');
            update_progress_bar(50);
         }, 

         error: function() {
            update_status_bar('<span style=\'color:red\'>error on submission</span>');
            update_progress_bar(0);
         }
      });
      return false;
   }   
</script>
