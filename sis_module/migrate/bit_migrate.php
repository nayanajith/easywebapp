<?php
/*
return semester array
*/
function get_sem_array($prefix,$sem,$sufix){
   $ret_sem=array();
   if($sem==3){
      for($i=1;$i<=5;$i++){
         $ret_sem[]=$prefix.$sem.$i.$sufix;   
      }
   }else{
      for($i=1;$i<=4;$i++){
         $ret_sem[]=$prefix.$sem.$i.$sufix;   
      }
   }
   return $ret_sem;
}

/*
@$a_year: academic(actual) year which exam is held
@$s_year: students year of exam
@$semester: semester of the exam

@return : Unique id as exam_hid

eg:
get_exam_hid(2009,3,1);
*/

function get_exam_hid($a_year,$s_year,$semester){
   if(strlen($a_year) == 2){
      //$a_year=substr($a_year,-2,2);
      $a_year="20".$a_year;
   }
   return $a_year."-01-01:".$s_year.":".$semester;
}

/*
BIT semester year mapping
SEM -> YEAR
*/
$bit_sem_year=array(
   1=>1,
   2=>1,
   3=>2,
   4=>2,
   5=>3,
   6=>3
);

/*
@$course_id : courseid of bit;

@return : array of breakdown of the courseid

eg: break_course_id('IT1204');
*/
function break_course_id($course_id){
   global $bit_sem_year;
   //$course_regexp="/^[MASEmase]|IT|it{1}[0-9]{4}$/";
   $course_regexp="/^[a-zA-Z]+[0-9]{4}$/";

   if(preg_match($course_regexp,$course_id)){
      $course_id=substr($course_id,-4,4);
      $semester=substr($course_id,0,1);

      $course_break=array(
         'year'      =>$bit_sem_year[$semester],   
         'semester'   =>$semester,   
         'revision'   =>substr($course_id,2,2)   
      );
      return $course_break;
   }else{
      return -1;
   }
}

/*
If all required parameters are set, migration will start otherwise prompt for the parameters
*/
if(
isset($_REQUEST['old_db']) &&
isset($_REQUEST['old_table']) &&
isset($_REQUEST['new_db']) &&
isset($_REQUEST['new_table']) &&
isset($_REQUEST['root_pwd'])
){


$GLOBALS['DB_USER']='root';
$GLOBALS['DB_PASS']=$_REQUEST['root_pwd'];

/*
define course notations
for marks, registration, ... etc
*/
define('MARK'   ,'M');
define('REG'   ,'A');
define('YEAR'   ,'S');
define('GRADE'   ,'E');
define('COURSE','IT');

/*
Insrt index numbers from old bit database to bit_student database
*/

$index_query="INSERT INTO ".$_REQUEST['new_db'].".bit_student(index_no) SELECT a.Index_No FROM ".$_REQUEST['old_db'].".bit_all AS a WHERE a.Reg='R';";

$res=exec_query($index_query,Q_RET_NON);

/*
Formatted data will be exported to this csv before importing back to the database
*/

$bit_marks_csv="/tmp/".$_REQUEST['old_db']."_marks.csv";

/*
Query to be used to select data 
*/
$marks_query="SELECT * FROM ".$_REQUEST['old_db'].".bit_all WHERE Reg='R'";

   $res=exec_query($marks_query,Q_RET_MYSQL_RES);
   if(get_num_rows()==0){
      echo "No rows returned!";
      return;
   }
   $file_handler = fopen($bit_marks_csv, 'w');
   //fwrite($file_handler,"'index_no','exam_hid','course_id','final_mark'\n" );
   
   while($row=mysql_fetch_assoc($res)){
      foreach($row as $key => $value){
         /*Do the task for the courseids starts with 'M' this is used to extract columns with marks*/
         if( preg_match("/^M[0-9]{4}$/",strtoupper($key)) ){
            /*Breakdown the courseid to identify semester and year*/
            $course_break   = break_course_id($key);
            /*Check the validity of the course id*/
            if($course_break != -1){ //course id is valid
               $s_year         = $course_break['year'];
               $a_year         = $row['Y'.$course_break['year']];
               /*Check the validity of the accedemic year*/
               if(preg_match("/^[0-9]{4}$/",strtoupper($a_year))){ //a_year is valid
                  //$exam_hid         = get_exam_hid($a_year,$s_year,$course_break['semester']);
                  $exam_hid         = get_exam_hid($a_year,$s_year,$course_break['semester']);
                  $course_id       = str_replace(MARK,COURSE,$key);
                  /*insert marks only have numerical value*/
                  //if(preg_match("/^[0-9]{1,3}$/",$value))
                  if($value != '')
                  {
                     fwrite($file_handler,"'".$row['Index_No']."','$exam_hid','$course_id','$value'\n" );
                  }
               }else{
                  //echo $a_year;
               }
            }
         }else{
            continue;   
         }   
      }
   }
   fclose($file_handler);
   /*
   Import data from csv to the new table
   */
//   csv_to_db($bit_marks_csv,$_REQUEST['new_table'],array('index_no','exam_hid','course_id','final_mark'),$_REQUEST['new_db']);

   //Migrating exams
   $exam_query="INSERT INTO ".$_REQUEST['new_db'].".bit_exam(exam_hid,student_year,semester,exam_date) SELECT DISTINCT exam_hid,LEFT(RIGHT(exam_hid,3),1),RIGHT(exam_hid,1),LEFT(exam_hid,10)  FROM ".$_REQUEST['new_db'].".".$_REQUEST['new_table'].";";
   exec_query($exam_query,Q_RET_NON);

   //Migrating course ids
   $course_query="INSERT INTO ".$_REQUEST['new_db'].".bit_course(course_id,lecture_credits) SELECT DISTINCT course_id,4  FROM ".$_REQUEST['new_db'].".".$_REQUEST['new_table'].";";
   exec_query($course_query,Q_RET_NON);

   $course_exam="INSERT INTO ".$_REQUEST['new_db'].".bit_batch(batch_id) SELECT DISTINCT LEFT(index_no,2) FROM ".$_REQUEST['new_db'].".bit_student;";
   exec_query($course_exam,Q_RET_NON);

   $student   ="REPLACE INTO ".$_REQUEST['new_db'].".bit_student(index_no,registration_no,initials,last_name,full_name,NID,sex,title,batch_id) SELECT IndexNo,RegNo,Initials,Name,Fname,NID,Gender,Title,LEFT(IndexNo,2) FROM courseadmin.bitstudent;";
   exec_query($student,Q_RET_NON);

   $course   ="REPLACE INTO ".$_REQUEST['new_db'].".bit_course(course_id,student_year,semester,course_name,prerequisite,lecture_credits,practical_credits,maximum_students,compulsory,alt_course_id,offered_by,GPA_con) SELECT CourseId,SYear,Semester,CourseName,Prerequisite,Credits_L,Credits_P,MaxStudents,Compulsory,AltCourseId,OfferedBy,GPACon FROM courseadmin.courses where courseid like 'IT%'";
   exec_query($course,Q_RET_NON);
}else{
/*
The form to be presented if the parameters are not present
*/
?>
<div    dojoType="dijit.form.Form" 
      name='frm_db_migrate' 
      id='frm_db_migrate' 
      jsId='frm_db_migrate'
      encType='multipart/form-data'
      action='<?php echo $GLOBALS['PAGE_GEN']; ?>';
      method='GET'>

   <script type="dojo/method" event="onSubmit">
   return true;
   </script>
   <input type=hidden name=module    value="<?php echo MODULE;    ?>" />
   <input type=hidden name=page       value="<?php echo PAGE;    ?>" />
   <input type=hidden name=program    value="<?php echo PROGRAM; ?>" />
   <table>
      <tr><td><label for='old_db'>Old Database</label></td><td><input dojotype="dijit.form.TextBox" name='old_db'    id='old_db'    value='bit'       /></td></tr>
      <tr><td><label for='old_table'>Old Table</label></td><td><input dojoType="dijit.form.TextBox" name='old_table' id='old_table' value='bit_all'    /></td></tr>
      <tr><td><label for='new_db'>New Database</label></td><td><input dojoType="dijit.form.TextBox" name='new_db'    id='new_db'    value='sis'    /></td></tr>
      <tr><td><label for='new_table'>New Table</label></td><td><input dojoType="dijit.form.TextBox" name='new_table' id='new_table' value='bit_marks'   /></td></tr>
      <tr><td><label for='root_pwd'>DB root password</label></td><td><input dojoType="dijit.form.TextBox" name='root_pwd' id='root_pwd' type='password'   /></td></tr>
   </table>
</div>
<script>
function submit_form(){
/*
     status_ = dijit.byId("status_bar");
   status_.innerHTML="<img src='<?php echo IMG; ?>/loading.gif' />";
*/
     formDlg = dijit.byId("frm_db_migrate");
   formDlg.submit();
//   status_.innerHTML="done";
}
</script>
<?php
}

?>
