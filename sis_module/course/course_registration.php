<?php
include A_CLASSES."/xhr_combobox_class.php";
$xhr_combobox=new XHR_Combobox();

/*
   Student registraton form generator
*/
function gen_registration_form(){
   if(isset($_SESSION[PAGE]['exam_hid']) && isset($_SESSION[PAGE]['index_no'])){
   }else{
      return;
   }


   $compulsory_credits=0;
   $registered_credits=0;

   //Get all course ids already registered for the given student
   $reged_courses_arr=exec_query("SELECT c.course_id FROM ".$GLOBALS['P_TABLES']['marks']." c,".$GLOBALS['P_TABLES']['exam']." e  WHERE c.exam_hid=e.exam_hid AND e.student_year='".$_SESSION[PAGE]['student_year']."' AND c.index_no='".$_SESSION[PAGE]['index_no']."'",Q_RET_ARRAY,null,null,'all');
   $reg_arr=array();

   //Put all the course ids in an array
   foreach($reged_courses_arr as $t_arr ){
      $reg_arr[]=$t_arr['course_id'];
   }


   //Array of elements which should be displayed in the course selection table
   $display=array(
      'course_id',        
      'semester',       
      'course_name',     
      'prerequisite',    
      'lecture_credits',  
      'practical_credits',
      'compulsory'       
   );

   //Get the total course array available for the given year
   $arr=exec_query("SELECT ".implode(',',$display)." FROM ".$GLOBALS['P_TABLES']['course']." WHERE student_year='".$_SESSION[PAGE]['student_year']."'",Q_RET_ARRAY);
   $curr_reg_arr=exec_query("SELECT course_id,COUNT(*) count FROM ".$GLOBALS['P_TABLES']['marks']." WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."' GROUP BY course_id",Q_RET_ARRAY,null,'course_id');

   //Table of courses to be registred or unregistered 
   $report= "<h4 style='background-color:#C9D7F1;padding:2px;text-align:center' class='bgCenter'>".$_SESSION[PAGE]['index_no']."</h4>";
   $report.= "<table style='border-collapse:collapse;border:0px solid #C9D7F1;' border=1>";
   $report.= "<tr>";
   foreach($display as $value){
      $report.= "<th>".style_text($value)."</th>";
   }
   $report.= "<th>Count</th><th>Choose</th>";
   $report.= "</tr>";

   d_r('dijit.form.CheckBox');
   //Print all the courses with details where as compulsory courses will be automatically selected and the previousely registred courses will also be selected 
   //Total of the already selecte and compulsory courses will also be returned
   foreach($arr as $row){
      $report.= "<tr><td>";
      $report.= implode('</td><td>',array_values($row));
      //$report.= "<td>".$curr_reg_arr[$row['course_id']]['count']."</td>";
      $report.= "<td>".(isset($curr_reg_arr[$row['course_id']]['count'])?$curr_reg_arr[$row['course_id']]['count']:0)."</td>";
      if(strtoupper($row['compulsory'])){
         $report.= "</td><td><input dojoType='dijit.form.CheckBox' type='checkbox' name='".$row['course_id']."' checked='true' ></input></td></tr>";
         $compulsory_credits+=$row['lecture_credits']+$row['practical_credits'];
      }else{
         if(in_array($row['course_id'],$reg_arr)){
            $report.= "</td><td><input dojoType='dijit.form.CheckBox' type='checkbox' name='".$row['course_id']."' checked='true' onClick='count_credits(this.name,this.checked,\"".$row['lecture_credits']."\",\"".$row['practical_credits']."\")'></input></td></tr>";
            $registered_credits+=$row['lecture_credits']+$row['practical_credits'];
         }else{
            $report.= "</td><td><input dojoType='dijit.form.CheckBox' type='checkbox' name='".$row['course_id']."' onClick='count_credits(this.name,this.checked,\"".$row['lecture_credits']."\",\"".$row['practical_credits']."\")'></input></td></tr>";
         }
      }
   }
   $report.= "</table>";

   //Sum of compulsory and already selected courses will be return in this hidden field
   $report.="<input type='hidden' id='compulsory_credits' value='".($compulsory_credits+$registered_credits)."' >";
   echo $report;
}

/*
   Student registraton form generator
*/
function gen_bulk_registration_form(){
   if(isset($_SESSION[PAGE]['exam_hid']) && isset($_SESSION[PAGE]['batch_id'])){
   }else{
      return;
   }

   $compulsory_credits=0;
   $registered_credits=0;

   //Array of elements which should be displayed in the course selection table
   $display=array(
      'course_id',        
      'semester',       
      'course_name',     
      'prerequisite',    
      'lecture_credits',  
      'practical_credits',
      'compulsory'       
   );

   //Get the total course array available for the given year
   $arr=exec_query("SELECT ".implode(',',$display)." FROM ".$GLOBALS['P_TABLES']['course']." WHERE student_year='".$_SESSION[PAGE]['student_year']."' AND semester='".$_SESSION[PAGE]['semester']."'",Q_RET_ARRAY);

   //Get the count of currently registered students
   $curr_reg_arr=exec_query("SELECT course_id,COUNT(*) count FROM ".$GLOBALS['P_TABLES']['marks']." WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."' GROUP BY course_id",Q_RET_ARRAY,null,'course_id');

   //Table of courses to be registred or unregistered 
   $report= "<h4 style='background-color:#C9D7F1;padding:2px;text-align:center' class='bgCenter'>Bulk course registration for batch-".$_SESSION[PAGE]['batch_id']."</h4>";
   $report.= "<table style='border-collapse:collapse;border:0px solid #C9D7F1;' border=1>";
   $report.= "<tr>";
   foreach($display as $value){
      $report.= "<th>".style_text($value)."</th>";
   }
   $report.= "<th>Count</th><th>Choose</th>";
   $report.= "</tr>";

   //Print all the courses with details where as compulsory courses will be automatically selected and the previousely registred courses will also be selected 
   //Total of the already selecte and compulsory courses will also be returned
   foreach($arr as $row){
      $report.= "<tr><td>";
      $report.= implode('</td><td>',array_values($row));
      $report.= "<td>".(isset($curr_reg_arr[$row['course_id']]['count'])?$curr_reg_arr[$row['course_id']]['count']:0)."</td>";
      if(strtoupper($row['compulsory'])){
         $report.= "</td><td><input dojoType='dijit.form.CheckBox' type='checkbox' name='".$row['course_id']."' checked='true' ></input></td></tr>";
         $compulsory_credits+=$row['lecture_credits']+$row['practical_credits'];
      }else{
         $report.= "</td><td><input dojoType='dijit.form.CheckBox' type='checkbox' name='".$row['course_id']."' onClick='count_credits(this.name,this.checked,\"".$row['lecture_credits']."\",\"".$row['practical_credits']."\")'></input></td></tr>";
      }
   }
   $report.= "</table>";

   //Sum of compulsory and already selected courses will be return in this hidden field
   $report.= "<input type='hidden' id='compulsory_credits' value='".($compulsory_credits+$registered_credits)."' >";
   $report.= "<input type='hidden' name='bulk' value='true' >";
   echo  $report;
}


/*
Function to save the registration request from the frontend
*/
function save_batch_selection(){
   $error=array();
   $arr=exec_query("SELECT course_id,semester FROM ".$GLOBALS['P_TABLES']['course']." WHERE student_year='".$_SESSION[PAGE]['student_year']."'",Q_RET_ARRAY);

   $index_arr=exec_query("SELECT index_no FROM ".$GLOBALS['P_TABLES']['student']." WHERE batch_id='".$_SESSION[PAGE]['batch_id']."'",Q_RET_ARRAY,null,'index_no');
   //clear previouse data
   exec_query("DELETE FROM ".$GLOBALS['P_TABLES']['marks']."(index_no,course_id,exam_hid)values('".$index_no."','".$c_arr['course_id']."','".$_SESSION[PAGE]['exam_hid']."')",Q_RET_MYSQL_RES);

   //Clean previousely registered courses before re setting the courses
   foreach($arr as $c_arr){
      if(isset($_REQUEST[$c_arr['course_id']])){
         //Registering the courses
         foreach(array_keys($index_arr) as $index_no){
            exec_query("REPLACE INTO ".$GLOBALS['P_TABLES']['marks']."(index_no,course_id,exam_hid)values('".$index_no."','".$c_arr['course_id']."','".$_SESSION[PAGE]['exam_hid']."')",Q_RET_MYSQL_RES);
            if(!is_query_ok()){
               $error[]=get_sql_error();
            }
         }
      }
   }

   //Return the registration status as json
   if(sizeof($error)>0){
      return_status_json('ERROR',implode(',',$error));
   }else{
      return_status_json('OK','Updated successfully!');
   }
}



/*
Function to save the registration request from the frontend
*/
function save_selection(){
   $arr=exec_query("SELECT course_id,semester FROM ".$GLOBALS['P_TABLES']['course']." WHERE student_year='".$_SESSION[PAGE]['student_year']."'",Q_RET_ARRAY);
   $error=array();

   //Clean previousely registered courses before re setting the courses
   exec_query("DELETE FROM ".$GLOBALS['P_TABLES']['marks']." WHERE exam_hid='".$_SESSION[PAGE]['exam_hid']."' AND index_no='".$_SESSION[PAGE]['index_no']."'",Q_RET_MYSQL_RES);
   if(!is_query_ok()){
      $error[]=get_sql_error();
   }
   foreach($arr as $c_arr){
      if(isset($_REQUEST[$c_arr['course_id']])){
         //Registering the courses
         exec_query("INSERT INTO ".$GLOBALS['P_TABLES']['marks']."(index_no,course_id,exam_hid)values('".$_SESSION[PAGE]['index_no']."','".$c_arr['course_id']."','".$_SESSION[PAGE]['exam_hid']."')",Q_RET_MYSQL_RES);

         if(!is_query_ok()){
            $error[]=get_sql_error();
         }
      }
   }

   //Return the registration status as json
   if(sizeof($error)>0){
      return_status_json('ERROR',implode(',',$error));
   }else{
      return_status_json('OK','Updated successfully!');
   }
}

function reset_batch(){
   return_status_json('ERROR','batch reset');
}

function reset_student(){
   return_status_json('ERROR','student reset');

}

//id table mapper array
$table_of_id=array(
   'index_no'=>$GLOBALS['P_TABLES']['student'],
   'batch_id'=>$GLOBALS['P_TABLES']['batch'],
   'exam_hid'=>$GLOBALS['P_TABLES']['exam'],
);

//Map filter for the given id for returning json data
$filter_map=array(
   'index_no'=>isset($_SESSION[PAGE]['batch_id'])?"index_no LIKE '".$_SESSION[PAGE]['batch_id']."%'":null,
   //'exam_hid'=>isset($_SESSION[PAGE]['admission_year']) && $_SESSION[PAGE]['admission_year'] !='' ?"academic_year>='".$_SESSION[PAGE]['admission_year']."'":"academic_year>='".(date('Y')-1)."'"
);

//order the given id for returning json data
$order_by_map=array(
   'exam_hid'=>" ORDER BY exam_hid DESC"
);


//Switch the functionality according to the request
if(isset($_REQUEST['action'])){
   switch($_REQUEST['action']){
      case 'modify':
         if(isset($_REQUEST['bulk']) && $_REQUEST['bulk'] == 'true'){
            save_batch_selection();
         }else{
            save_selection();
         }
      break;
      case 'reset':
         if(isset($_REQUEST['bulk']) && $_REQUEST['bulk'] == 'true'){
            reset_batch();
         }else{
            reset_student();
         }
      break;
      case 'store':
         $filter   =null;
         $order_by=null;
         if(isset($filter_map[$_REQUEST['id']])){
            $filter=$filter_map[$_REQUEST['id']];
         }
         if(isset($order_by_map[$_REQUEST['id']])){
            $order_by=$order_by_map[$_REQUEST['id']];
         }
         $xhr_combobox->json_data($table_of_id[$_REQUEST['id']],$_REQUEST['id'],$filter,$order_by);
      break;
      break;
      case 'html':
         if(isset($_REQUEST['index_no'])){
            $_SESSION[PAGE]['exam_hid']=$_REQUEST['exam_hid'];
            $_SESSION[PAGE]['index_no']=$_REQUEST['index_no'];
            $_SESSION[PAGE]['gen']='SINGLE';
            gen_registration_form();
         }else{
            $_SESSION[PAGE]['exam_hid']=$_REQUEST['exam_hid'];
            $_SESSION[PAGE]['gen']='BULK';
            gen_bulk_registration_form();
         }
      break;
      case 'param':
         $_SESSION[PAGE][$_REQUEST['param']]=$_REQUEST[$_REQUEST['param']];

         //exceptional cases
         switch($_REQUEST['param']){
            case 'batch_id':
               $admission_year=exec_query("SELECT * FROM ".$GLOBALS['P_TABLES']['batch']." WHERE batch_id='".$_SESSION[PAGE][$_REQUEST['param']]."'",Q_RET_ARRAY);
               $_SESSION[PAGE]['code']=$admission_year[0]['code'];
               $_SESSION[PAGE]['admission_year']=$admission_year[0]['admission_year'];
            break;
            case 'exam_hid':   
               $student_year=exec_query("SELECT * FROM ".$GLOBALS['P_TABLES']['exam']." WHERE exam_hid='".$_SESSION[PAGE][$_REQUEST['param']]."'",Q_RET_ARRAY);
               $_SESSION[PAGE]['student_year']=$student_year[0]['student_year'];
               $_SESSION[PAGE]['semester']=$student_year[0]['semester'];
               $_SESSION[PAGE]['academic_year']=$student_year[0]['academic_year'];
            break;
         }

         return_status_json('OK',$_REQUEST['param'].'='.$_REQUEST[$_REQUEST['param']]);
      break;
   }
}else{
      //Print html when requested
      echo "<div align='center'><div dojoType='dijit.form.Form' id='course_selection_frm' name='course_selection_frm' jsId='course_selection_frm' >";
         if(isset($_SESSION[PAGE]['gen'])&&$_SESSION[PAGE]['gen']=='SINGLE'){
            gen_registration_form();
         }else{
            gen_bulk_registration_form();
         }
      echo "</div></div>";

      //Index number selector in toolbar
      echo "<script type='text/javascript'>";
      echo "dojo.addOnLoad(function() {";

      //function gen_xhr_combobox($id,$label,$value,$width,$page_size,$source_array=null,$target=null);
      //$xhr_combobox->gen_xhr_combobox('student_year',"Student Year",$xhr_combobox->get_val('student_year'),30,20,null,null);
      $xhr_combobox->gen_xhr_combobox('batch_id',"Batch",$xhr_combobox->get_val('batch_id'),80,20,null,null);
      $xhr_combobox->gen_xhr_combobox('exam_hid',"Exam",$xhr_combobox->get_val('exam_hid'),110,20,array('batch_id','exam_hid'),'course_selection_frm');
      $xhr_combobox->gen_xhr_combobox('index_no',"Index No",$xhr_combobox->get_val('index_no'),80,20,array('batch_id','exam_hid','index_no'),'course_selection_frm');
      echo "
      var credit_count_label=new dijit.form.Button({
         label: 'Credit Count',
         disabled:true
      });
      toolbar.addChild(credit_count_label);

      var credit_count = new dijit.form.TextBox({
         id:'credit_count',
         jsId:'credit_count',
         name:'credit_count',
         style:'width:40px;'
      });
   
      toolbar.addChild(credit_count);
      ";


      $xhr_combobox->param_setter();
      $xhr_combobox->html_requester();
      echo "});";
      $xhr_combobox->form_submitter('course_selection_frm');
      echo "
      function count_credits(course_id,state,lec_cred,prac_cred){
         var compulsory_credits=dojo.byId('compulsory_credits');
         var prev_value =parseInt(compulsory_credits.value);
         var sel_value  =parseInt(lec_cred)+parseInt(prac_cred);
         if(state==true){
            compulsory_credits.value=prev_value+sel_value;
         }else{
            compulsory_credits.value=prev_value-sel_value;
         }
         dojo.byId('credit_count').value=dojo.byId('compulsory_credits').value;
      }";

      echo "</script>";

   }
?>
