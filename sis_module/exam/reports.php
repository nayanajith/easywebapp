<?php
include A_CLASSES."/xhr_combobox_class.php";
include A_CLASSES."/student_class.php";
$xhr_combobox=new XHR_Combobox();

function gen_exam_summery(){
   //Get grade count for all courses of the selected exam
   $arr_grade_count=exec_query("SELECT course_id,g.grade grade,COUNT(g.grade) count FROM ".$GLOBALS['P_TABLES']['marks']." m,".$GLOBALS['P_TABLES']['grades']." g WHERE m.exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND m.state='PR' AND m.final_mark=g.mark GROUP BY g.grade,m.course_id ORDER BY course_id,ABS(grade);",Q_RET_ARRAY);
   $arr_grade_count_pr=array();
   foreach($arr_grade_count as $grade_arr){
      $arr_grade_count_pr[$grade_arr['course_id']][$grade_arr['grade']]=$grade_arr['count'];
   }


   //Get state count of all courses of the selected exam
   $arr_state_count=exec_query("SELECT course_id,state,COUNT(state) count FROM ".$GLOBALS['P_TABLES']['marks']."  WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."' GROUP BY state,course_id ORDER BY course_id;",Q_RET_ARRAY);
   $arr_state_count_pr=array();
   foreach($arr_state_count as $state_arr){
      if(!isset($arr_state_count_pr[$state_arr['course_id']])){
         $arr_state_count_pr[$state_arr['course_id']]=array('AB'=>0,'MC'=>0,'EO'=>0,'PR'=>0);
      }
      $arr_state_count_pr[$state_arr['course_id']][$state_arr['state']]=$state_arr['count'];
   }

   //Get the statistics of each course of the selected exam
   $arr_statistics=exec_query("SELECT course_id,MAX(final_mark) max,MIN(final_mark) min,ROUND(AVG(final_mark),2) avg,ROUND(STD(final_mark),2) std FROM ".$GLOBALS['P_TABLES']['marks']." WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND state='PR' GROUP BY course_id;",Q_RET_ARRAY,null,'course_id');

   $exam_info_arr=explode(':',$_SESSION[PAGE]['exam_hid']);
   //Generating the summery report  for all the courses of the selected exam
   $title= "Examination date:".$exam_info_arr[0]." Year:".$exam_info_arr[1]." Semester:".$exam_info_arr[2];
   $report="<h4 class='coolh'>$title</h4>";
   //Hide the table when you want to convert the table in to datagrid
   $report.= "<table id='exam_summary' class='clean' border='1'>";
   $header_arr=array(
      "Course_ID"=>array('width'=>'80','label'=>"Course ID"),
      "STD"=>array('width'=>'50','label'=>"STD"),
      "AVG"=>array('width'=>'50','label'=>"AVG"),
      "MAX"=>array('width'=>'50','label'=>"MAX"),
      "MIN"=>array('width'=>'50','label'=>"MIN"),
      "PRESENT"=>array('width'=>'50','label'=>"PR"),
      "ABSENT"=>array('width'=>'50','label'=>"AB"),
      "MEDICAL"=>array('width'=>'50','label'=>"MED"),
      "OFFENDED"=>array('width'=>'50','label'=>"EO"),
      "GRADE_COUNT"=>array('width'=>'450','label'=>"GRADE COUNT")
   );
   $header="<thead><tr><th>".implode("</th><th>",array_keys($header_arr))."</th></tr></thead>";
   $report.=$header;
   $report.="<tbody>";
   foreach($arr_statistics as $course_id => $stat_arr){
      $report.= "<tr>";
      $report.= "<td>$course_id</td>";
      $report.= "<td>".$stat_arr['std']."</td><td>".$stat_arr['avg']."</td><td>".$stat_arr['max']."</td><td>".$stat_arr['min']."</td>";
      $report.= "<td>".$arr_state_count_pr[$course_id]['PR']."</td><td>".$arr_state_count_pr[$course_id]['AB']."</td><td>".$arr_state_count_pr[$course_id]['MC']."</td><td>".$arr_state_count_pr[$course_id]['EO']."</td>";
      //Grade count
      $report.= "<td title=''>";
      $bar='';
      $in_row=8;
      $count=1;
      if(isset($arr_grade_count_pr[$course_id])){
         foreach(getGradeGPVArr() as $grade => $gpa){
            if(isset($arr_grade_count_pr[$course_id][$grade])){
               if($count%$in_row == 0)$report.="<br>";
               $report.= "$bar$grade:".$arr_grade_count_pr[$course_id][$grade];
               $bar='&nbsp;|&nbsp;';
               $count++;
            }
         }
      }
      $report.="</td>";
      $report.= "</tr>";
   } 
   $report.= $header;
   $report.= "</tbody></table>";

   //When the user request a reload of the page the table will display as a dojo grid
   //THIS IS TESTING FUNCTIONALITY TODDO:
   /*
   if(!isset($_REQUEST['action'])){
      include A_CLASSES."/data_grid_class.php";
      $data_grid= new Data_grid();
      $report.=$data_grid->gen_data_grid($header_arr,'exam_summary','Course_ID','TABLE',$title);
   }
    */

   $report.="<center>".date("M d, Y")."</center>";
   if(isset($_REQUEST['action'])&&$_REQUEST['action']=='pdf'){
      include A_CLASSES."/letterhead_pdf_class.php";
      $letterhead=new Letterhead("A4","P");

      //insert the content to the pdf
      $letterhead->include_content(str_replace("'","\"",$report));

      //Acquire pdf document
      $pdf=$letterhead->getPdf();

      //name of the pdf file
      $pdf_file=$_SESSION[PAGE]['course_id'].".pdf";

      $pdf->Output($pdf_file, 'I');
      //$pdf->Output(TMP."/".$pdf_file, 'F');
      return;
   }elseif(isset($_REQUEST['action'])&&$_REQUEST['action']=='print'){
      echo  $report;
      echo "<script type='text/javascript' >window.print();</script>";
   }else{
      echo  $report;
   }
}

//Columns of the mark uploading table
$report_types=array(
   'LMS'=>array(
   ),
   'FULL'=>array(
      'can_release',
      'assignment_mark',
      'paper_mark',
      'final_mark',
      'push'
   ),
   'VERIFY'=>array(
      'can_release',
      'final_mark',
      'push'
   ),
);

$columns=$report_types['FULL'];
if(isset($_SESSION[PAGE]['report_type'])){
   $columns=$report_types[$_SESSION[PAGE]['report_type']];
}

//Generating report for the given course in given exam
function print_detailed_marks($course_id){
   global $columns;
   $arr_state_count=exec_query("SELECT state,COUNT(state) count FROM ".$GLOBALS['P_TABLES']['marks']."  WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND course_id='".$_SESSION[PAGE]['course_id']."' GROUP BY state;",Q_RET_ARRAY,null,'state');
   $state_arr=array('PR'=>0,'AB'=>0,'MC'=>0,'EO'=>0);
   $report="<table class='clean' border='1'>";
   $report.="<tr><th>PRESENT</th><th>ABSENT</th><th>MEDICAL</th><th>OFFENDED</th></tr>";
   foreach($arr_state_count as $state => $arr){
      $state_arr[$state]=$arr['count'];
   }
   $report.="<tr><td>";
   $report.=implode("</td><td>",array_values($state_arr));
   $report.="</td></tr>";
   $report.="</table>";

    //Get the statistics of the course
   $arr_statistics=exec_query("SELECT MAX(final_mark) MAX,MIN(final_mark) MIN,ROUND(AVG(final_mark),2) AVG,ROUND(STD(final_mark),2) STD FROM ".$GLOBALS['P_TABLES']['marks']." WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND course_id='".$_SESSION[PAGE]['course_id']."' AND state='PR';",Q_RET_ARRAY);

   $report.="<table class='clean' border='1'>";
   $report.="<tr><th>";
   $report.=implode("</th><th>",array_keys($arr_statistics[0]));
   $report.="</th></tr>";
   $report.="<tr><td>";
   $report.=implode("</td><td>",array_values($arr_statistics[0]));
   $report.="</td></tr>";
   $report.="</table>";

   //Get grade count for the course
   $arr_grade_count=exec_query("SELECT g.grade grade,COUNT(g.grade) count FROM ".$GLOBALS['P_TABLES']['marks']." m,".$GLOBALS['P_TABLES']['grades']." g WHERE m.exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND course_id='".$_SESSION[PAGE]['course_id']."' AND m.state='PR' AND (m.final_mark+m.push)=g.mark GROUP BY g.grade,m.course_id ORDER BY grade;",Q_RET_ARRAY,null,'grade');

   $report.="<table class='clean' border='1'>";
   $head="<tr>";
   $data="<tr>";
   foreach($arr_grade_count as $grade => $count){
      $head.="<th>$grade</th>";
      $data.="<td>".$count['count']."</td>";
   }
   $report.=$head."</tr>";
   $report.=$data."</tr>";
   $report.="</table>";

   //Printing the marks
   $blank_tds='<td></td>';
   $report.="<table class='report_table' border='1' cellpadding='2' style='border-collapse:collapse;'>\n<tr><th>#</th><th>Index No</th>";
   foreach($columns as $key => $value){   
      $report.="<th>".style_text($value)."</th>";
      $blank_tds.='<td></td>';
   }
   $report.="<th>Grade</th></tr>\n";


   //Marks for all the student for a given course in a given exam
   $marks_arr=exec_query("SELECT * FROM ".$GLOBALS['P_TABLES']['marks']." WHERE course_id='".$_SESSION[PAGE]['course_id']."' AND exam_hid='".$_SESSION[PAGE]['exam_hid']."'",Q_RET_ARRAY,null,'index_no');

   $serial_no=1;
   foreach($marks_arr as $index_no => $row){
      $report.="<tr><td>".$serial_no++."</td>";
      $report.="<td>".$index_no."</td>";
      foreach($columns as $column){
         $report.="<td>".$marks_arr[$index_no][$column]."</td>";
      }
      $report.='<td>'.getGradeC($marks_arr[$index_no]['final_mark']+$marks_arr[$index_no]['push'],$_SESSION[PAGE]['course_id']).'</td>';
      $report.="</tr>\n";
   }
   $report.="</table>";
   return $report;
}

//Grades to be published in LMS should be presented as multiple columns
function print_lms_marks($course_ids){
   global $columns;
   $course_ids_arr=explode(',',$course_ids);
   //Printing the marks
   $report="<table class='report_table' border='1' cellpadding='2' style='border-collapse:collapse;'>\n<tr><th>#</th><th>Index No</th>";
   $report.="<th>".implode("</th><th>",$course_ids_arr)."</th></tr>\n";


   //Marks for all the student for a given course in a given exam
   $marks_arr=exec_query("SELECT * FROM ".$GLOBALS['P_TABLES']['marks']." WHERE course_id in('".implode("','",$course_ids_arr)."') AND exam_hid='".$_SESSION[PAGE]['exam_hid']."'",Q_RET_ARRAY);
   $grades_studentwise_arr=array();
   foreach($marks_arr as $row){
      if(is_numeric($row['final_mark'])){
         $grades_studentwise_arr[$row['index_no']][$row['course_id']]=getGradeC($row['final_mark']+$row['push'],$row['course_id']);
      }else{
         $grades_studentwise_arr[$row['index_no']][$row['course_id']]=$row['final_mark'];
      }
   }

   $serial_no=1;
   foreach($grades_studentwise_arr as $index_no => $grades){
      $report.="<tr><td>".$serial_no++."</td>";
      $report.="<td>".$index_no."</td>";
      foreach($course_ids_arr as $course_id){
         if(isset($grades[$course_id])){
            $report.="<td>".@$grades[$course_id]."</td>";
         }else{
            $report.="<td>-</td>";
         }
      }
      $report.="</tr>\n";
   }
   $report.="</table>";
   return $report;

}


//Fill the available marks to the table while generating the table
function gen_course_summery(){
   $report="
<style type='text/css'>
.report_table{
}

.report_table td{
   text-align:center;
}

.report_table th{
   text-align:center;
   font-weight:bold;
}
</style>   
   ";


   $report.="<div align='center'><h4 class='coolh'>Courses ".$_SESSION[PAGE]['course_ids']."</h4></div>\n";

   if(isset($_SESSION[PAGE]['report_type']) && $_SESSION[PAGE]['report_type']=='LMS'){
      $report.=print_lms_marks($_SESSION[PAGE]['course_ids']);
   }else{
      //Get state count of the courses
      foreach(explode(",",$_SESSION[PAGE]['course_ids']) as $course_id){
         $_SESSION[PAGE]['course_id']= $course_id;
         $report.=print_detailed_marks($course_id);
      }
   }

   $report.="<center>".date("M d, Y")."</center>";
   if(isset($_REQUEST['action'])&&$_REQUEST['action']=='pdf'){
      include A_CLASSES."/letterhead_pdf_class.php";
      $letterhead=new Letterhead("A4","P");
   
      //insert the content to the pdf
      $letterhead->include_content(str_replace("'","\"",$report));
   
      //Acquire pdf document
      $pdf=$letterhead->getPdf();
   
      //name of the pdf file
      $pdf_file=$_SESSION[PAGE]['exam_hid'].".pdf";
   
      $pdf->Output($pdf_file, 'I');
      //$pdf->Output(TMP."/".$pdf_file, 'F');
      return;
   }elseif(isset($_REQUEST['action'])&&$_REQUEST['action']=='print'){
      echo  $report;
      echo "<script type='text/javascript' >window.print();</script>";
   }else{
      echo  $report;
   }
}


//id table mapper array
$table_of_id=array(
   'course_id'=>$GLOBALS['P_TABLES']['marks'],
   'exam_hid'=>$GLOBALS['P_TABLES']['exam']
);

//Map filter for the given id
$course_id_filter=isset($_SESSION[PAGE]['exam_hid'])?"exam_hid='".$_SESSION[PAGE]['exam_hid']."'":null;
$filter_map=array(
   'course_id'=>$course_id_filter,
);

//Map order_by for the given id of combobox
$order_by_map=array(
   'exam_hid'=>'ORDER BY exam_id DESC',
);

if(isset($_REQUEST['form'])){
   switch($_REQUEST['form']){
   case 'main':
      if(isset($_REQUEST['action'])){
         switch($_REQUEST['action']){
         case 'pdf':
         case 'print':
         case 'html':
            if(isset($_SESSION[PAGE]['course_ids']) || isset($_REQUEST['course_ids'])){
               gen_course_summery();
               $_SESSION[PAGE]['gen']='COURSE';
            }else{
               gen_exam_summery();
               $_SESSION[PAGE]['gen']='EXAM';
            }
         break;
         case 'param':
            $_SESSION[PAGE][$_REQUEST['param']]=$_REQUEST[$_REQUEST['param']];
            if($_REQUEST['param'] == 'exam_hid'){
               $admission_year=exec_query("SELECT student_year,semester FROM ".$GLOBALS['P_TABLES']['exam']." WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."'");
               $_SESSION[PAGE]['student_year']=$admission_year[0]['student_year'];
               $_SESSION[PAGE]['semester']=$admission_year[0]['semester'];
               //empty courseid  
               unset($_SESSION[PAGE]['course_ids']);
            }
            return_status_json('OK',"Set ".$_REQUEST['param']."=".$_REQUEST[$_REQUEST['param']]);
         break;
         case 'store':
            $filter=null;
            if(isset($filter_map[$_REQUEST['id']])){
               $filter=$filter_map[$_REQUEST['id']];
            }

            $order_by=null;
            if(isset($order_by_map[$_REQUEST['id']])){
               $order_by=$order_by_map[$_REQUEST['id']];
            }
            $xhr_combobox->json_data($table_of_id[$_REQUEST['id']],$_REQUEST['id'],$filter,$order_by);
         break;

         }
      }   
   break;
   }
}else{
echo "<div align='center'><div id='marks_frm' jsId='marks_frm' dojoType='dijit.form.Form'>";
if(isset($_SESSION[PAGE]['gen'])&&$_SESSION[PAGE]['gen']=='COURSE'){
   gen_course_summery();
}elseif(isset($_SESSION[PAGE]['gen'])&&$_SESSION[PAGE]['gen']=='EXAM'){
   gen_exam_summery();
}else{
   echo "Click Help button for help.";
}
echo "</div></div>";

//Generate course selection tooltip dialog
if(!isset($_SESSION[PAGE]['exam_hid'])){
   $_SESSION[PAGE]['exam_hid']=null;
}

$arr=exec_query("SELECT DISTINCT course_id FROM ".$GLOBALS['P_TABLES']['marks']." WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."'",Q_RET_ARRAY,null,'course_id');
$course_id_checkboxes="";
$cols=3;
$td=0;
$course_id_arr=array_keys($arr);
foreach($course_id_arr as $course_id){
   if($td == 0){
      $course_id_checkboxes.="<tr>";
   }
   $course_id_checkboxes.="<td><input type='checkbox' dojoType='dijit.form.CheckBox' value='$course_id' id='".$course_id."_'><label for='".$course_id."_'>".$course_id."&nbsp;</lebel></td>";
   $td++;
   if($td==$cols || !next($course_id_arr)){
      $course_id_checkboxes.="</tr>";
      $td=0;
   }
}

d_r("dijit.form.CheckBox");
d_r("dijit.form.Button");
d_r("dijit.form.DropDownButton");
$submit_button="<center><button dojoType='dijit.form.Button' type='submit' onClick='handle_ttdialog()'>OK</button></center>";
?>

<script type='text/javascript' >
dojo.addOnLoad(function() {
   //reference to our toolbar
   var toolbar = dijit.byId('toolbar');

<?php
   $xhr_combobox->gen_xhr_combobox('exam_hid',"Exam",$xhr_combobox->get_val('exam_hid'),110,20,array('exam_hid'),'marks_frm');
?>



   var reload_button=new dijit.form.Button({
      iconClass:'dijitIcon dijitIconFunction',
      label: 'Reload',
      onClick:function(){window.open('<?php echo gen_url(); ?>','_parent');},
   });

   toolbar.addChild(reload_button);
   //Tooltip dialog to select multiple course_ids
   var ttdialog = new dijit.TooltipDialog({
      id:"ttdialog",
      jsId:"ttdialog",
      content:"<table><? echo $course_id_checkboxes; ?></table><center><button dojoType='dijit.form.Button' type='submit' onClick='handle_ttdialog()'>OK</button></center>"
   });
    
   //Drop down button to hold tooltip dialog
   var ddbutton = new dijit.form.DropDownButton({
       label: "Select Courses",
       dropDown: ttdialog
   });
   toolbar.addChild(ddbutton);
   
   //Hidden textbox to hold the course_ids selected using tooltip dialog
   var course_ids = new dijit.form.TextBox({
      type:"hidden",
      id:"course_ids",
      jsId:"course_ids",
      name:"course_ids"
   });
   
   toolbar.addChild(course_ids);


<?php
   //Different report types to be select
   $item_array=array('FULL','VERIFY','LMS');
   $xhr_combobox->gen_xhr_static_combo('report_type','Report Type',$xhr_combobox->get_val('report_type'),110,$item_array,array('exam_hid','course_ids','report_type'),'marks_frm');
?>
});
<?php
   //$xhr_combobox->html_requester();
   $xhr_combobox->param_setter();$xhr_combobox->html_requester();
   $xhr_combobox->form_submitter('marks_frm');
?>
//Function to be called when pressing ok button of tooltip dialog
function handle_ttdialog(){
   var ttdialog = document.getElementById("ttdialog");
   var my_array = ttdialog.getElementsByTagName("input");
   var retvalue = new Array();
   var i;
   var course_ids="";
   var comma="";
   for(i = 0; i < my_array.length; i++){
         if(my_array[i].checked == true){
            course_ids+=comma+my_array[i].value;
            comma=",";
         }
   }
   document.getElementById("course_ids").value=course_ids;
   set_param("course_ids",course_ids);
   request_html("marks_frm",null,'html');
}

</script>
<?php
}
?>
