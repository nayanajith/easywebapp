<?php
include A_CLASSES."/xhr_combobox_class.php";
include A_CLASSES."/student_class.php";
$xhr_combobox=new XHR_Combobox();


//Columns of the mark uploading table
$columns=array(
'1'=>'#',
'2'=>'index_no',
'3'=>'assignment_mark',
'4'=>'paper_mark',
'5'=>'final_mark',
'6'=>'grade',
'7'=>'push',
'8'=>'NOTE',
'9'=>'can_release'
);

function gen_mark_tr($index_no,$serial_no,$marks_arr,$state,$is_non_grade){
   $style         ='';
   $ab            ='';
   $onchange      =" onChange='re_calculate(\"".$serial_no."\")' ";
   $dojoType      =" dojoType='dijit.form.NumberTextBox'";
   $dojoCheckBox  =" dojoType='dijit.form.CheckBox'";
   $dojoCheckBox  ="";
   $dojoType      ="";

   //Nongrade courses will not recalculated onchage
   if($is_non_grade){
      $onchange      ="";
   }


   switch($state){
      case 'BLANK':
         $marks_arr=array('paper_mark'=>'','assignment_mark'=>'','final_mark'=>'','push'=>'0','can_release'=>'1');
      break;
      case 'AB':
         $style="disabled='disabled' style='color:red;'";
         $marks_arr=array('paper_mark'=>'','assignment_mark'=>'','final_mark'=>'','push'=>'0','can_release'=>'1');
         $ab='AB';
      case 'MC':
         $style="disabled='disabled' style='color:red;'";
         $marks_arr=array('paper_mark'=>'','assignment_mark'=>'','final_mark'=>'','push'=>'0','can_release'=>'1');
         $ab='MC';
      break;
      default:
         if(is_numeric($marks_arr['final_mark'])){
            $ab=getGradeC($marks_arr['final_mark']+$marks_arr['push'],$_SESSION[PAGE]['course_id']);
         }else{
            $ab=$marks_arr['final_mark'];
         }
      break;
   }
   $checked="";
   if($marks_arr['can_release']=='1'){
      $checked="checked";
   }
   echo "<td><input type='text' class='cell' $style name='2:".$serial_no."' id='2:".$serial_no."' value='".$index_no."'></td>
   <td><input type='text' $dojoType $onchange class='cell' $style name='3:".$serial_no."' id='3:".$serial_no."' value='".$marks_arr['paper_mark']."'></td>
   <td><input type='text' $dojoType $onchange class='cell' $style name='4:".$serial_no."' id='4:".$serial_no."' value='".$marks_arr['assignment_mark']."'></td>
   <td><input type='text' $dojoType $onchange class='cell' $style name='5:".$serial_no."' id='5:".$serial_no."' value='".$marks_arr['final_mark']."'></td>
   <td class='cell_td' id='6:".$serial_no."' >$ab</td>
   <td><input type='text' $dojoType $onchange class='cell' $style name='7:".$serial_no."' id='7:".$serial_no."' value='".$marks_arr['push']."'></td>
   <td class='cell_td' id='8:".$serial_no."'></td>
   <td class='cell_td'><input type='checkBox' $dojoCheckBox $onchange class='cell'  $style name='9:".$serial_no."' id='9:".$serial_no."' value='1' $checked></td>";

}

//Fill the available marks to the table while generating the table
function gen_mark_in_form(){
   global $columns;
   $arr_rubric=exec_query("SELECT * FROM ".$GLOBALS['P_TABLES']['rubric']." WHERE  course_id='".$_SESSION[PAGE]['course_id']."' AND exam_hid='".$_SESSION[PAGE]['exam_hid']."'",Q_RET_ARRAY);
   if(!isset($arr_rubric[0])){
      echo "Rubric not set!";   
      return;
   }

      echo "
<style type="text/css">
.cell{
   padding:0px;
   margin:0px;
   width:100px;
   border:none;
   text-align:center;
}
.cell_ab{
   padding:0px;
   margin:0px;
   width:100px;
   background-color:silver;
   color:red;
   text-align:center;
   
}

.cell_td{
   text-align:center;
}
</style>   
   ";
   //Registered students for a given course in given exam
   $reg_arr=exec_query("SELECT index_no,state FROM ".$GLOBALS['P_TABLES']['marks']." WHERE  course_id='".$_SESSION[PAGE]['course_id']."' AND exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND state='PR'",Q_RET_ARRAY,null,'index_no');

   if(!isset($reg_arr) || get_num_rows()<1){
      echo "No students registered for ".$_SESSION[PAGE]['course_id'];
      return;
   }

   echo "<h4 style='background-color:#C9D7F1;padding:2px;text-align:center' class='bgCenter'>".$_SESSION[PAGE]['course_id']."&nbsp;|&nbsp;Rubric:".$arr_rubric[0]['assignment']."(Paper),".$arr_rubric[0]['paper']."(Assignment)&nbsp;|&nbsp;Number of Students:".get_num_rows()." </h4>";

   //Pass hidden parameters back to the form
   echo "<input type='hidden' id='assignment_rubric' value='".$arr_rubric[0]['assignment']."'/>";
   echo "<input type='hidden' id='paper_rubric' value='".$arr_rubric[0]['paper']."'/>";
   echo "<input type='hidden' id='student_count' value='".get_num_rows()."'/>";


   //Marks for all the student for a given course in given exam
   $marks_arr=exec_query("SELECT * FROM ".$GLOBALS['P_TABLES']['marks']." WHERE course_id='".$_SESSION[PAGE]['course_id']."' AND exam_hid='".$_SESSION[PAGE]['exam_hid']."'",Q_RET_ARRAY,null,'index_no');

   echo "<table border='1' style='border-collapse:collapse;'><tr>";
   foreach($columns as $key => $value){   
      echo "<th>".style_text($value)."</th>";
   }
   echo "</tr>";

   $serial_no=1;
   foreach($reg_arr as $index_no => $row){
      echo "<tr><td id='1:".$serial_no."'>".$serial_no."</td>";
      gen_mark_tr($index_no,$serial_no,$marks_arr[$index_no],'',isNonGrade($_SESSION[PAGE]['course_id']));
      echo "</tr>";
      $serial_no++;
   }
   echo "</table>";

   if(isset($_REQUEST['action'])&&$_REQUEST['action']=='print'){
      echo "<script type="text/javascript" >window.print();</script>";
   }
}


//Function to save marks 
function save_marks(){
   global $columns;
   $db_colomns=$columns;
   unset($db_colomns[1]);
   unset($db_colomns[6]);
   unset($db_colomns[8]);
   /*
$db_columns=array(
'2'=>'index_no',
'3'=>'assignment_mark',
'4'=>'paper_mark',
'5'=>'final_mark',
'6'=>'grade',
'7'=>'push',
'9'=>'can_release',
);
   */

   $index_arr=exec_query("SELECT index_no FROM ".$GLOBALS['P_TABLES']['marks']." WHERE  course_id='".$_SESSION[PAGE]['course_id']."' AND exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND state not in('AB','MC')",Q_RET_ARRAY,null,'index_no');
   $studnt_count=get_num_rows();

   $query    ='';
   $keys     =implode(',',array_values($db_colomns));
   $keys     =$keys.',course_id,exam_hid';
   $values   ='';
   $values_comma='';
   $blank   =false;


   $activity='<ol><li>Saving marks of the student...';
   for($i=1;$i<=$studnt_count;$i++){
      //If can_release is false the value for the request will not set so we set it as 0 
      $_REQUEST['9:'.$i]=isset($_REQUEST['9:'.$i])?1:0;
      $_REQUEST['7:'.$i]=isset($_REQUEST['7:'.$i])&&$_REQUEST['7:'.$i]!=''?$_REQUEST['7:'.$i]:null;

      /*
       [2:1] => 08000018
       [3:1] => 50
       [4:1] => 50
       [5:1] => 50
       [7:1] => 
       [9:1] => 1
     */
      $values   .=$values_comma.'(';
      $value_comma='';
      foreach($db_colomns as $key => $bla){
         if(isset($_REQUEST[$key.':'.$i])){
            $value=trim($_REQUEST[$key.':'.$i]);   
            if($value==''){
               $blank   =true;
            }
            $values.=$value_comma."'".$value."'";   
            $value_comma=',';
         }else{
            $blank   =true;
         }
      }

      if(!$blank){
         $values=$values.",'".$_SESSION[PAGE]['course_id']."','".$_SESSION[PAGE]['exam_hid']."'";
      }

      $blank=false;   
      $values.=')';
      $values_comma=',';
   }

   //Insert marks to the database if exists with same keys replace with new values
   exec_query("REPLACE INTO ".$GLOBALS['P_TABLES']['marks']."(".$keys.")values".$values,Q_RET_NON);
   $activity.="<br>.".get_sql_error();

   //calculate grand_final_mark,grade and gpv for the uploaded marks
   $activity.='<li>Calculating grand_final, grade and gpv...';
   exec_query("UPDATE bcsc_marks m,bcsc_grades g,bcsc_course c SET m.grand_final_mark=m.final_mark+m.push,m.grade=g.grade,m.gpv=g.gpv*(c.lecture_credits+c.practical_credits)  WHERE m.course_id=c.course_id AND (m.final_mark+m.push)=g.mark and m.exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND m.course_id='".$_SESSION[PAGE]['course_id']."'",Q_RET_NON);
   $activity.="<br>.".get_sql_error();


   //Reset repeat_max
   $activity.='<li>Finding repeat max...';
   exec_query("UPDATE bcsc_marks SET repeat_max=false WHERE course_id='".$_SESSION[PAGE]['course_id']."'",Q_RET_NON);
   $activity.="<br>.".get_sql_error();


   //Find repeat max for the students in effect of current save
   $query_repeat_max="UPDATE bcsc_marks m,(SELECT exam_hid, index_no, course_id, MAX(grand_final_mark) grand_final_mark,COUNT(*) count_ FROM bcsc_marks WHERE  course_id='".$_SESSION[PAGE]['course_id']."' GROUP BY index_no,course_id) r SET m.repeat_max=1 WHERE r.count_ > 1 AND m.exam_hid=r.exam_hid AND m.course_id=r.course_id AND m.index_no=r.index_no";
   exec_query($query_repeat_max,Q_RET_NON);
   $activity.="<br>.".get_sql_error();

   //Calculating gpa for all students affected
   $activity.='<li>Generating GPA for the affected students...';
   $calculate_gpa="REPLACE INTO bcsc_gpa2(`index_no`,`year`,`gpv`,`credits`,`gpa`)(SELECT r.index_no,r.year,SUM(r.gpv),SUM(r.credits),(SUM(r.gpv)/SUM(r.credits)) FROM(SELECT m.index_no,MAX(m.gpv) gpv,c.student_year year,c.lecture_credits+c.practical_credits credits FROM bcsc_marks m,bcsc_course c WHERE m.course_id=c.course_id AND index_no IN(SELECT index_no FROM ".$GLOBALS['P_TABLES']['marks']." WHERE  course_id='".$_SESSION[PAGE]['course_id']."' AND exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND state not in('AB','MC')) GROUP BY m.index_no,m.course_id,c.student_year) as r group by r.index_no,r.year);";
   exec_query($calculate_gpa,Q_RET_NON);
   $activity.="<br>.".get_sql_error();


   //Get grade count for the selected course in selected exam
   $arr_grade_count=exec_query("SELECT GROUP_CONCAT(grade) grades,GROUP_CONCAT(count) counts FROM(SELECT grade,COUNT(grade) count FROM ".$GLOBALS['P_TABLES']['marks']." WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND course_id='".$_SESSION[PAGE]['course_id']."' AND NOT ISNULL(grade) GROUP BY grade) AS r;",Q_RET_ARRAY);
   $activity.="<br>.".get_sql_error();
   $arr_grade_count=array_combine(explode(',',$arr_grade_count[0]['grades']),explode(',',$arr_grade_count[0]['counts']));
   $grade_count_json=json_encode($arr_grade_count);


   //Get state count for selected course in  selected exam
   $arr_state_count=exec_query("SELECT GROUP_CONCAT(state) states,GROUP_CONCAT(count) counts FROM(SELECT state,COUNT(state) count FROM ".$GLOBALS['P_TABLES']['marks']." WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND course_id='".$_SESSION[PAGE]['course_id']."' GROUP BY state) AS r;",Q_RET_ARRAY);
   $activity.="<br>.".get_sql_error();
   $arr_state_count=array_combine(explode(',',$arr_state_count[0]['states']),explode(',',$arr_state_count[0]['counts']));
   $arr_state=array(
      'PR'=>'0',
      'AB'=>'0',
      'MC'=>'0',
      'EO'=>'0',
   );
   $arr_state_count=array_merge($arr_state,$arr_state_count);


   //Get the statistics of selected course of the selected exam
   $arr_statistics=exec_query("SELECT MAX(grand_final_mark) max,MIN(grand_final_mark) min,ROUND(AVG(grand_final_mark),2) avg,ROUND(STD(grand_final_mark),2) std FROM ".$GLOBALS['P_TABLES']['marks']." WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND course_id='".$_SESSION[PAGE]['course_id']."' AND state='PR';",Q_RET_ARRAY);
   $arr_statistics=$arr_statistics[0];


   //Update the satistics data  of this course in this exam
   $activity.='<li>Updating statistics...';
   $update_stat_query="REPLACE INTO ".$GLOBALS['P_TABLES']['marks_stat']."(`exam_hid`,`course_id`,`std`,`avg`,`max`,`min`,`present`,`absent`,`medical`,`offended`,`grade_count`)values('".$_SESSION[PAGE]['exam_hid']."','".$_SESSION[PAGE]['course_id']."','".$arr_statistics['std']."','".$arr_statistics['avg']."','".$arr_statistics['max']."','".$arr_statistics['min']."','".$arr_state_count['PR']."','".$arr_state_count['AB']."','".$arr_state_count['MC']."','".$arr_state_count['EO']."','".$grade_count_json."')";
   exec_query($update_stat_query,Q_RET_NON);
   $activity.="<br>.".get_sql_error();


   //Return json status
   /*
   if(trim($activity) != '' ){
      return_status_json('ERROR',$activity);
   }else{
    */
      return_status_json('OK',$activity."</ol>");
   //}
}

//id table mapper array
$table_of_id=array(
   'course_id'=>$GLOBALS['P_TABLES']['course'],
   'exam_hid'=>$GLOBALS['P_TABLES']['exam']
);

//Map filter for the given id

$sem_filter=isset($_SESSION[PAGE]['semester'])?"semester='".$_SESSION[PAGE]['semester']."'":null;
$year_filter=isset($_SESSION[PAGE]['student_year'])?"student_year='".$_SESSION[PAGE]['student_year']."'":null;
$filter_map=array(
   'course_id'=>$sem_filter." AND ".$year_filter,
);

$order_by_map=array(
   'exam_hid'=>'ORDER BY exam_hid DESC'
);


if(isset($_REQUEST['form'])){
   switch($_REQUEST['form']){
   case 'main':
      if(isset($_REQUEST['action'])){
         switch($_REQUEST['action']){
         case 'modify':
            save_marks();
         break;
         case 'print':
         case 'html':
            gen_mark_in_form();
         break;
         case 'param':
            $_SESSION[PAGE][$_REQUEST['param']]=$_REQUEST[$_REQUEST['param']];

            if($_REQUEST['param'] == 'exam_hid'){
               $admission_year=exec_query("SELECT student_year,semester FROM ".$GLOBALS['P_TABLES']['exam']." WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."'");
               $_SESSION[PAGE]['student_year']=$admission_year[0]['student_year'];
               $_SESSION[PAGE]['semester']=$admission_year[0]['semester'];
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
   case 'filter':
   break;
   }
}else{

d_r('dijit.form.SimpleTextarea');
d_r('dijit.form.Button');
d_r('dijit.form.Form');
?>

<div align='center'><div id='marks_frm' jsId='marks_frm' dojoType='dijit.form.Form'>
<?php
if(isset($_SESSION[PAGE]['course_id'])&& $_SESSION[PAGE]['exam_hid']){
   gen_mark_in_form();
}
?>
</div>
</div>

<script type="text/javascript" >

// Grade ranges//
/*
 * 0<=E<=19 20<=D-<=29 30<=D<=39 40<=D+<=44 45<=C-<=49 50<=C<=54 55<=C+<=59
 * 60<=B-<=64 65<=B<=69 70<=B+<=74 75<=A-<=79 80<=A<=89 90<=A+<=100
 */
/*
 * param mark: int/float value return grade: eg E, D-,
 */
function get_grade(mark){
   if( 0<=mark && mark<=19){
      return "E";
   }else if(20<=mark && mark<=29){
      return "D-";
   }else if(30<=mark && mark<=39){
      return "D";
   }else if(40<=mark && mark<=44){
      return "D+";
   }else if(45<=mark && mark<=49){
      return "C-";
   }else if(50<=mark && mark<=54){
      return "C";
   }else if(55<=mark && mark<=59){
      return "C+";
   }else if(60<=mark && mark<=64){
      return "B-";
   }else if(65<=mark && mark<=69){
      return "B";
   }else if(70<=mark && mark<=74){
      return "B+";
   }else if(75<=mark && mark<=79){
      return "A-";
   }else if(80<=mark && mark<=89){
      return "A";
   }else if(90<=mark && mark<=100){
      return "A+";
   }
}


function find_index_no(index_no){
   var student_count    =document.getElementById('student_count').value;
   var index_found      =false;
   for( i=1;i<=student_count;i++){
      var sys_index_no=document.getElementById('2:'+i).value.trim();
      if(sys_index_no == index_no){
         index_found=i;
         break;
      }
   }
   return index_found;
}

/*
Extract marks from textarea to the table
'1'=>'#',
'2'=>'Index No',
'3'=>'Assignment Marks',
'4'=>'Paper Marks',
'5'=>'Final',
'6'=>'Grade',
'7'=>'Adjustment',
'8'=>'NOTE'

*/

function re_calculate(key){
   var rubric_assignment=document.getElementById('assignment_rubric').value;
   var rubric_paper      =document.getElementById('paper_rubric').value;

   //Assignment Marks
   var as_mark=document.getElementById('3:'+key).value;

   //Paper Marks
   var paper_mark=document.getElementById('4:'+key).value;


   //Calculation of the final
   var final_mark=((as_mark*rubric_assignment)+(paper_mark*rubric_paper))/100;
   var final_mark_round=Math.round(final_mark,0);
   //Margin fix
   if(final_mark_round == 48 || final_mark_round == 49 ){
      document.getElementById('5:'+key).title=final_mark_round+'->'+50;
      var final_mark_round=50;
      document.getElementById('8:'+key).innerHTML='48,49->50';
      document.getElementById('8:'+key).title='Marginal value 48 and 49 will be automaticall pushed to 50';
   }
   document.getElementById('5:'+key).value=final_mark_round;
   var push=document.getElementById('7:'+key).value;

   //Grade
   document.getElementById('6:'+key).innerHTML=get_grade(final_mark_round+(push*1));
   update_status_bar('OK',Updated +key);
}


function extract(){
   var col_offset          =2;
   var content             =dijit.byId('paste').getValue();
   dijit.byId('paste').setValue('');
   content=content.trim();
   var marks_array         =content.split("\n");
   var rubric_assignment   =document.getElementById('assignment_rubric').value;
   var rubric_paper        =document.getElementById('paper_rubric').value;
   var i;
   var j;

   
   for( i=1;i<=marks_array.length;i++){
      var row=marks_array[i-1];
      var row=row.replace(/  /g,',')
      var row=row.replace(/\t/g,',')
      row_arr=row.split(",");
      //check Index No
      var index_no=row_arr[0].trim();
      var key=find_index_no(index_no);
      if(key == false){
         update_status_bar('ERROR','Index number missmatch ['+index_no+" <=> "+row_arr[0]+"]");
         document.getElementById('2:'+i).style.color='red';
         document.getElementById('2:'+i).title='Index number missmatch ['+index_no+" <=> "+row_arr[0]+"]";
         continue;
      }else{
         document.getElementById('2:'+i).style.color='black';
         document.getElementById('2:'+i).title=null;
      }
      //document.getElementById('2:'+i).value=row_arr[0];

      //Assignment Marks
      document.getElementById('3:'+key).value=row_arr[1];

      //Paper Marks
      document.getElementById('4:'+key).value=row_arr[2];
/*
Extract marks from textarea to the table
'1'=>'#',
'2'=>'Index No',
'3'=>'Assignment Marks',
'4'=>'Paper Marks',
'5'=>'Final',
'6'=>'Grade',
'7'=>'Adjustment',
'8'=>'NOTE'

*/
      //Nongrade courses will not calculate for the grade and final mark
      <?php if(isset($_SESSION[PAGE]['course_id']) && isNonGrade($_SESSION[PAGE]['course_id'])){ ?>
      //Calculation of the final
      var final_mark=row_arr[3];
      document.getElementById('5:'+key).value=final_mark;
      document.getElementById('6:'+key).innerHTML=final_mark;
      document.getElementById('8:'+key).innerHTML='NON_GRADE';
      <?php }else{ ?>
      var final_mark=((row_arr[1]*rubric_assignment)+(row_arr[2]*rubric_paper))/100;
      var final_mark_round=Math.round(final_mark,0);

      //Margin fix
      if(final_mark_round == 48 || final_mark_round == 49 ){
         document.getElementById('5:'+key).title=final_mark_round+'->'+50;
         var final_mark_round=50;
         document.getElementById('8:'+key).innerHTML='48,49->50';
         document.getElementById('8:'+key).title='Marginal value 48 and 49 will be automaticall pushed to 50';
      }
      document.getElementById('5:'+key).value=final_mark_round;

      //Grade
      document.getElementById('6:'+key).innerHTML=get_grade(final_mark_round);
      <?php } ?>
      update_progress_bar(i/(marks_array.length/100));

   }                                                            
}

dojo.addOnLoad(function() {
   //reference to our toolbar
   var toolbar = dijit.byId('toolbar');


   //Label for the exam selector
   var paste_label=new dijit.form.Button({
       label: 'Paste Here',
       disabled:true,
   });
   toolbar.addChild(paste_label);

   var paste_area=new dijit.form.SimpleTextarea({
      style:'width:100px;height:15px;',
      jsId:'paste',
      id:'paste',
      name:'paste',

   });
   toolbar.addChild(paste_area);

   //Label for the exam selector
   var extract_button=new dijit.form.Button({
      label: 'Extract',
      onClick:function(){extract()},
   });
   toolbar.addChild(extract_button);
<?php
   $xhr_combobox->gen_xhr_combobox('exam_hid',"Exam",$xhr_combobox->get_val('exam_hid'),110,20,null,null);
   //$xhr_combobox->gen_xhr_combobox('course_id',"Course",$xhr_combobox->get_val('course_id'),80,20,array('exam_hid','course_id'),'marks_frm');
   $xhr_combobox->gen_xhr_combobox('course_id',"Course",$xhr_combobox->get_val('course_id'),80,20,null,null);
?>
   var load_button=new dijit.form.Button({
      iconClass:'dijitIcon dijitIconFunction',
      label: 'Load',
      onClick:function(){window.open('<?php echo gen_url(); ?>','_parent');},
   });
   toolbar.addChild(load_button);

});
<?php
   $xhr_combobox->param_setter();$xhr_combobox->html_requester();
   $xhr_combobox->form_submitter('marks_frm');
?>
</script>
<?php
}
?>
