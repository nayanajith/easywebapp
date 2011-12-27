<?php
//include A_CLASSES."/data_entry_class.php";
include A_CLASSES."/xhr_combobox_class.php";
$xhr_combobox=new XHR_Combobox();


//id table mapper array
$table_of_id=array(
   'batch_id'=>$GLOBALS['P_TABLES']['batch'],
);

//Map filter for the given id
$filter_map=array(
);



//Request functoin switcher
if(isset($_REQUEST['form'])){
   switch($_REQUEST['form']){
      case 'main':
      if(isset($_REQUEST['action'])){
         switch($_REQUEST['action']){
         case 'process':
            process_all();
         break;
         case 'html':
            $_SESSION[PAGE]['batch_id']=$_REQUEST['batch_id'];
            print_process_status();
         break;
         case 'store':
            $filter=null;
            if(isset($filter_map[$_REQUEST['id']])){
               $filter=$filter_map[$_REQUEST['id']];
            }
            $xhr_combobox->json_data($table_of_id[$_REQUEST['id']],$_REQUEST['id'],$filter);
         break;
         case 'param':
            $_SESSION[PAGE][$_REQUEST['param']]=$_REQUEST[$_REQUEST['param']];
            return_status_json('OK',"Set ".$_REQUEST['param']."=".$_REQUEST[$_REQUEST['param']]);
         break;
         }
      }
   }
}else{
   echo "<div align='center'><div id='gpa_frm' jsId='gpa_frm' dojoType='dijit.form.Form' >";
   print_process_status();
   echo "</div></div>";

   echo "<script type='text/javascript'>";
   echo "dojo.addOnLoad(function() {";

   //function gen_xhr_combobox($id,$label,$value,$width,$page_size,$source_array=null,$target=null);
   //$xhr_combobox->gen_xhr_combobox('student_year',"Student Year",$xhr_combobox->get_val('student_year'),30,20,null,null);
   $xhr_combobox->gen_xhr_combobox('batch_id',"Batch",$xhr_combobox->get_val('batch_id'),80,20,array('batch_id'),'gpa_frm');
   echo "
   var reload_button=new dijit.form.Button({
      iconClass:'dijitIcon dijitIconFunction',
      label: 'Reload',
      onClick:function(){request_html('gpa_frm',new Array('batch_id'),null);},
   });
   toolbar.addChild(reload_button);";

   $xhr_combobox->param_setter();
   $xhr_combobox->html_requester();
   echo "});";
   $xhr_combobox->form_submitter('gpa_frm');
   echo "</script>";
}

//Process all
function process_all(){
   $activity="";

   //Selecting repeat max for all courses in all exams for all students
   $activity.='<li>Selecting repeat max for all courses in all exams for all students...';
   exec_query("UPDATE ".$GLOBALS['P_TABLES']['marks']."  SET repeat_max=0",Q_RET_NON);
   $set_repeat_max="
      UPDATE ".$GLOBALS['P_TABLES']['marks']." m,(
         SELECT m.exam_hid, m.index_no, m.base_course_id, m.grand_final_mark, (
            SELECT COUNT(base_course_id) 
            FROM ".$GLOBALS['P_TABLES']['marks']." 
            WHERE index_no=m.index_no AND base_course_id=m.base_course_id
            GROUP BY base_course_id
         ) AS count
         FROM ".$GLOBALS['P_TABLES']['marks']." m
         WHERE m.state='PR' 
            AND m.exam_hid=(
               SELECT exam_hid 
               FROM ".$GLOBALS['P_TABLES']['marks']." 
               WHERE index_no=m.index_no AND base_course_id=m.base_course_id 
               ORDER BY grand_final_mark DESC 
               LIMIT 1
            )
         GROUP BY m.index_no,m.base_course_id
      ) AS r
      SET m.repeat_max=r.count
      WHERE m.exam_hid=r.exam_hid AND m.index_no=r.index_no AND m.base_course_id=r.base_course_id
   ";
   exec_query($set_repeat_max,Q_RET_NON);
   $activity.="<br>.".get_sql_error();

   //calculate grand_final_mark, degree_grade and degree_gpv for the uploaded marks
   $activity.='<li>Calculating grand_final, degree_grade and degree_gpv...';
   $calculate_grand_final="
      UPDATE ".$GLOBALS['P_TABLES']['marks']." m,".$GLOBALS['P_TABLES']['grades']." g,".$GLOBALS['P_TABLES']['course']." c 
      SET m.grand_final_mark=m.final_mark+m.push,m.degree_grade=g.grade,m.degree_gpv=g.gpv*(c.lecture_credits+c.practical_credits),m.class_grade=g.grade,class_gpv=g.gpv*(c.lecture_credits+c.practical_credits) 
      WHERE NOT ISNULL(m.final_mark) AND m.course_id=c.course_id AND (m.final_mark+m.push)=g.mark AND m.state='PR'";
   exec_query($calculate_grand_final,Q_RET_NON);
   $activity.="<br>.".get_sql_error();

   //calculate class_final_mark,class_grade and class_gpv for the uploaded marks
   $activity.='<li>Calculating class_final, class_grade and class_gpv...';
   $calculate_class_final="
   UPDATE ".$GLOBALS['P_TABLES']['marks']." m,(
      SELECT exam_hid, index_no, course_id, g.gpv class_gpv, g.grade class_grade 
      FROM(
         SELECT m.exam_hid, m.index_no, m.course_id, MAX(m.grand_final_mark) grand_final_mark,IF(m.grand_final_mark>=50,50,m.grand_final_mark) class_final_mark 
         FROM ".$GLOBALS['P_TABLES']['marks']." 
         INNER JOIN ".$GLOBALS['P_TABLES']['course']." c
         USING(course_id) 
         WHERE state='PR' AND repeat_max=2
      ) r,".$GLOBALS['P_TABLES']['grades']." g 
      WHERE g.mark=r.class_final_mark
   ) r,".$GLOBALS['P_TABLES']['course']." c 
   SET m.class_grade=r.class_grade, m.class_gpv=(r.class_gpv*(c.lecture_credits+practical_credits)) 
   WHERE m.exam_hid=r.exam_hid AND m.index_no=r.index_no AND m.course_id=r.course_id AND m.course_id=c.course_id";
   exec_query($calculate_class_final,Q_RET_NON);
   $activity.="<br>.".get_sql_error();




   //Calculating degree GPA for all students affected
   $activity.='<li>Generating degree GPA...';
   $calculate_gpa="
      REPLACE INTO ".$GLOBALS['P_TABLES']['gpa']."(`index_no`,`year`,`degree_gpv`,`credits`,`degree_gpa`)(
         SELECT r.index_no,r.year,SUM(r.degree_gpv),SUM(r.credits),(SUM(r.degree_gpv)/SUM(r.credits)
      )FROM(
         SELECT m.index_no,MAX(m.degree_gpv) degree_gpv,c.student_year year,c.lecture_credits+c.practical_credits credits 
         FROM ".$GLOBALS['P_TABLES']['marks']." m, ".$GLOBALS['P_TABLES']['course']." c 
         WHERE  NOT ISNULL(m.degree_gpv) AND m.course_id=c.course_id  AND m.state='PR'
         GROUP BY m.index_no,c.alt_course_id,c.student_year
      ) as r group by r.index_no,r.year
      )";
   exec_query($calculate_gpa,Q_RET_NON);
   $activity.="<br>.".get_sql_error();


   //Calculating degree GPAT for all students affected
   $activity.='<li>Generating degreee GPAT...';
   $calculate_gpat="
      REPLACE INTO ".$GLOBALS['P_TABLES']['gpa']."(`index_no`,`credits`,`degree_gpv`,`degree_gpa`,`year`)(
         SELECT index_no,SUM(credits) credits ,SUM(degree_gpv) degree_gpv ,SUM(degree_gpv)/SUM(credits) degree_gpa, if(SUM(year)=3,'2T',if(SUM(year)=6,'3T',if(SUM(year)=10,'4T',0))) year 
         FROM ".$GLOBALS['P_TABLES']['gpa']." 
         WHERE year NOT IN('4T','3T','2T') 
         GROUP BY index_no 
         HAVING SUM(year) >= 3
      )";
   exec_query($calculate_gpat,Q_RET_NON);
   $activity.="<br>.".get_sql_error();

   //Calculating class GPA for all students affected
   $activity.='<li>Generating class GPA...';
   $calculate_gpa="
      UPDATE ".$GLOBALS['P_TABLES']['gpa']." AS g,(
         SELECT r.index_no,r.year,SUM(r.class_gpv) class_gpv,SUM(r.credits) credits,(SUM(r.class_gpv)/SUM(r.credits)) class_gpa 
         FROM(
            SELECT m.index_no,MAX(m.class_gpv) class_gpv,c.student_year year,c.lecture_credits+c.practical_credits credits 
            FROM ".$GLOBALS['P_TABLES']['marks']." m,".$GLOBALS['P_TABLES']['course']." c 
            WHERE m.course_id=c.course_id AND m.state='PR'
            GROUP BY m.index_no,m.course_id,c.student_year
         ) as r 
         GROUP BY r.index_no,r.year
      ) AS p 
      SET g.class_gpv=p.class_gpv,g.class_gpa=p.class_gpa 
      WHERE g.index_no=p.index_no AND g.year=p.year;";
   exec_query($calculate_gpa,Q_RET_NON);
   $activity.="<br>.".get_sql_error();


   //Calculating class GPAT for all students affected
   $activity.='<li>Generating class GPAT...';
   $calculate_gpat="
      UPDATE ".$GLOBALS['P_TABLES']['gpa']." AS g,(
         SELECT index_no,SUM(credits) credits ,SUM(class_gpv) class_gpv ,SUM(class_gpv)/SUM(credits) class_gpa, if(SUM(year)=6,'2T',if(SUM(year)=6,'3T',if(SUM(year)=10,'4T',0))) year,'C' 
         FROM ".$GLOBALS['P_TABLES']['gpa']." 
         WHERE year NOT IN('4T','3T','2T') 
         GROUP BY index_no 
      ) AS p 
      SET g.class_gpv=p.class_gpv, g.class_gpa=p.class_gpa 
      WHERE g.year=p.year AND g.index_no=p.index_no";
   exec_query($calculate_gpat,Q_RET_NON);
   $activity.="<br>.".get_sql_error();



   /*year 1 pass*/
   $year1_pass="SELECT index_no,class_gpa,(SELECT MAX(exam_hid) FROM ".$GLOBALS['P_TABLES']['marks_course_view']." WHERE index_no=bit_gpa.index_no AND student_year=1) FROM ".$GLOBALS['P_TABLES']['gpa']." WHERE year=1 AND class_gpa >= 1.5";

   /*YEAR 2 pass*/
   $year2_pass="SELECT index_no,class_gpa,(SELECT MAX(exam_hid) FROM ".$GLOBALS['P_TABLES']['marks_course_view']." WHERE index_no=bit_gpa.index_no AND student_year=2) FROM ".$GLOBALS['P_TABLES']['gpa']." WHERE year='2T' AND class_gpa >= 1.5";

   /*YEAR 3 pass*/
   $bit_pass="SELECT index_no,class_gpa,(SELECT MAX(exam_hid) FROM ".$GLOBALS['P_TABLES']['marks_course_view']." WHERE index_no=bit_gpa.index_no AND student_year=3) FROM ".$GLOBALS['P_TABLES']['gpa']." WHERE year='3T' AND class_gpa >= 2";





   return_status_json('OK',$activity);
}

//Show whether there are any processing should done

function print_process_status(){
   //Reset session var
   $_SESSION[PAGE]['gpa']=false;
   $_SESSION[PAGE]['mark_stat']=false;
   $_SESSION[PAGE]['student_state']=false;
   echo "<ul>";

   //Checking if gpa calculation should be done
   $arr=exec_query("SELECT m.timestamp > g.timestamp update_avail FROM(SELECT MAX(timestamp) timestamp FROM ".$GLOBALS['P_TABLES']['marks'].") m, (SELECT MAX(timestamp) timestamp FROM ".$GLOBALS['P_TABLES']['gpa'].") g",Q_RET_ARRAY);
   if($arr[0]['update_avail']){
      $_SESSION[PAGE]['gpa']=true;
      echo "<li>GPA calculation should be done<br>";
   }


   //Checking if marks stat calculation should be done
   /*
   $arr=exec_query("SELECT m.timestamp > s.timestamp update_avail FROM(SELECT MAX(timestamp) timestamp FROM ".$GLOBALS['P_TABLES']['marks'].") m, (SELECT MAX(timestamp) timestamp FROM ".$GLOBALS['P_TABLES']['marks_stat'].") s",Q_RET_ARRAY);
   if($arr[0]['update_avail']){
      $_SESSION[PAGE]['mark_stat']=true;
      echo "<li>Mark statistics calculation should be done<br>";
   }
    */

   //Checking if student stat calculation should be done
   $arr=exec_query("SELECT g.timestamp > s.timestamp update_avail FROM(SELECT MAX(timestamp) timestamp FROM ".$GLOBALS['P_TABLES']['student_state'].") s, (SELECT MAX(timestamp) timestamp FROM ".$GLOBALS['P_TABLES']['gpa'].") g",Q_RET_ARRAY);
   if($arr[0]['update_avail']){
      $_SESSION[PAGE]['student_state']=true;
      echo "<li>Student state calculation should be done<br>";
   }
   echo "</ul>";


}

?>
