<?php
$program_=$_REQUEST["program"];
$source   ="courseadmin";
$dest      ="sis";
$pc      ="cs";
$cc      ="SCS";
switch($program_){
   case 'bict':
      $pc="it";
      $cc="ICT";
   break;
   case 'bit':
      $pc="bit";
      $cc="IT";
   break;
}
$source   =isset($_REQUEST['source_db'])?$_REQUEST['source_db']:'';
$dest      =isset($_REQUEST['dest_db'])?$_REQUEST['dest_db']:'';

$migrate_queries=array(
   //If the tables are filled with any data clean all
   "prepare1"   =>"DELETE FROM ".$program_."_student",
   "prepare2"   =>"DELETE FROM ".$program_."_marks",
   "prepare3"   =>"DELETE FROM ".$program_."_exam",
   "prepare4"   =>"DELETE FROM ".$program_."_batch",
   "prepare5"   =>"DELETE FROM ".$program_."_gpa",
   "prepare6"   =>"DELETE FROM ".$program_."_course",
   "prepare7"   =>"DELETE FROM ".$program_."_student_state",

   //Reset auto increment numbers
   "prepare7"   =>"ALTER TABLE ".$program_."_exam AUTO_INCREMENT = 1",
   
   //migrate student information
   "student"   =>"REPLACE INTO ".$program_."_student(index_no,registration_no,initials,last_name,full_name,date_of_regist,date_of_graduation,date_of_birth,status,batch_id) SELECT IndexNo,RegNo,Initials,Name,fullname,dreg,dgrad,dob,upper(Status),LEFT(IndexNo,2) FROM ".$source.".".$pc."student",

   //Migrate course information
   "course"      =>"REPLACE INTO ".$program_."_course(course_id,student_year,semester,course_name,prerequisite,lecture_credits,practical_credits,maximum_students,compulsory,alt_course_id,offered_by,non_grade) SELECT CourseId,SYear,Semester,CourseName,Prerequisite,Credits_L,Credits_P,MaxStudents,Compulsory,AltCourseId,OfferedBy,GPACon FROM ".$source.".courses where courseid like '".$cc."%' or courseid like 'ENH%'",

   "course2"   =>"UPDATE ".$program_."_course c,(SELECT MIN(course_id) course_id,course_name FROM ".$program_."_course GROUP BY course_name ORDER BY course_name,course_id) AS r SET c.alt_course_id=r.course_id WHERE c.course_name=r.course_name",

   //Migrate student marks
   "marks1"      =>"REPLACE INTO ".$program_."_marks(exam_hid,index_no,course_id,assignment_mark,paper_mark,final_mark,push) SELECT concat('20',LEFT(ExamId,2),'-01-01',':',LEFT(RIGHT(ExamId,2),1),':',RIGHT(ExamId,1)),IndexNo,CourseId,Marks1,Marks2,Marks3,Adjustment FROM ".$source.".".$pc."marks WHERE ".$source.".".$pc."marks.final NOT IN('NA','WH','CH','AB','MC','EO')  ",

   "marks2"      =>"REPLACE INTO ".$program_."_marks(exam_hid,index_no,course_id,assignment_mark,paper_mark,final_mark) SELECT concat('20',LEFT(examid,2),'-01-01',':',LEFT(RIGHT(examid,2),1),':',RIGHT(examid,1)),indexno,courseid,marks1,marks2,final FROM ".$source.".".$pc."marks WHERE ".$source.".".$pc."marks.final IN('NA','CM','NC')  ",

   "marks3"      =>"REPLACE INTO ".$program_."_marks(exam_hid,index_no,course_id,state) SELECT concat('20',LEFT(examid,2),'-01-01',':',LEFT(RIGHT(examid,2),1),':',RIGHT(examid,1)),indexno,courseid,final FROM ".$source.".".$pc."marks WHERE ".$source.".".$pc."marks.final IN('AB','MC','EO')  ",

   //Extracting exam information from  marks table
   "exam1"      =>"REPLACE INTO ".$program_."_exam(exam_hid,exam_date,semester,student_year) SELECT DISTINCT exam_hid,CONCAT((LEFT(exam_hid,10)),'-01','-01'),RIGHT(exam_hid,1),(LEFT(RIGHT(exam_hid,3),1)) FROM ".$program_."_marks",

   //Set new exam_hid s in marks table
//   "marks4"      =>"UPDATE ".$program_."_marks m, ".$program_."_exam e set m.exam_hid=e.exam_hid where m.exam_old_id=e.exam_old_id;",

   //Extract batch ids from student information table
   "batch"      =>"REPLACE INTO ".$program_."_batch(batch_id,admission_year) SELECT DISTINCT LEFT(index_no,2),CONCAT('20',LEFT(index_no,2)) FROM ".$program_."_student",

   //Generate gpv information of the students
   "final_grade_gpv" =>"UPDATE ".$program_."_marks m,".$program_."_grades g,".$program_."_course c SET m.grand_final_mark=m.final_mark+m.push,m.grade=g.grade,m.gpv=g.gpv*(c.lecture_credits+c.practical_credits)  WHERE m.course_id=c.course_id AND (m.final_mark+m.push)=g.mark",

   //Generate gpa information of the students
   "gpa2"=>"REPLACE INTO ".$program_."_gpa(`index_no`,`year`,`degree_gpv`,`credits`,`degree_gpa`)(SELECT r.index_no,r.year,SUM(r.degree_gpv),SUM(r.credits),(SUM(r.degree_gpv)/SUM(r.credits)) FROM(SELECT m.index_no,MAX(m.degree_gpv) degree_gpv,c.student_year year,c.lecture_credits+c.practical_credits credits FROM ".$program_."_marks m,".$program_."_course c WHERE m.course_id=c.course_id GROUP BY m.index_no,c.alt_course_id,c.student_year) as r group by r.index_no,r.year)",

   "gpa3"=>"REPLACE INTO ".$program_."_gpa(`index_no`,`credits`,`degree_gpv`,`degree_gpa`,`year`)(SELECT index_no,SUM(credits) credits ,SUM(degree_gpv) degree_gpv ,SUM(degree_gpv)/SUM(credits) degree_gpa, if(SUM(year)=6,'3T',if(SUM(year)=10,'4T',0)) year FROM ".$program_."_gpa WHERE year NOT IN('4T','3T') GROUP BY index_no HAVING(SUM(year) >=6))";

);

//Special case for BIT
if($program_ == 'bit'){
   $migrate_queries['student']  ="REPLACE INTO ".$program_."_student(index_no,registration_no,initials,last_name,full_name,batch_id) SELECT IndexNo,RegNo,Initials,Name,FName,LEFT(IndexNo,2) FROM ".$source.".".$pc."student";
}

$error='';

if(isset($_REQUEST['action'])){ /*haldle requests*/
   switch($_REQUEST['action']){
      case 'migrate_db':
         //cleanup the tables
         foreach(array("student","marks","exam","batch","course","gpa") as $table){
            $GLOBALS['CONNECTION'] = mysql_connect("localhost", "root", $_REQUEST['root_pwd']);
            if($GLOBALS['CONNECTION'] && mysql_select_DB($dest, $GLOBALS['CONNECTION'])){
               if(!exec_query("DELETE FROM ".$program_."_$table",Q_RET_NON,$db=null,$array_key=null,$deleted=null,$no_connect=true)){
                  $create=false;
                  $error.=get_sql_error();
               }   
            }
         }

         $create=true;
         $error="";
         $GLOBALS['CONNECTION'] = mysql_connect("localhost", "root", $_REQUEST['root_pwd']);
         if($GLOBALS['CONNECTION'] && mysql_select_DB($dest, $GLOBALS['CONNECTION'])){
            foreach($migrate_queries as $key => $query){
               if(!exec_query($query,Q_RET_NON,$db=null,$array_key=null,$deleted=null,$no_connect=true)){
                  $create=false;
                  $error.=get_sql_error();
               }   
            }
            //find the rubric and add to the rubric table
            $query="SELECT exam_hid,course_id,paper_mark,assignment_mark,final_mark FROM ".$program."_marks WHERE paper_mark <> 0 AND assignment_mark <> 0 GROUP BY exam_hid,course_id";
            $exam_course=exec_query($query,Q_RET_ARRAY,$db=null,$array_key=null,$deleted=null,$no_connect=true);
            foreach($exam_course as $key => $row){
               $exam_hid=$row['exam_hid'];
               $a_r=@round(100*($row['final_mark']-$row['paper_mark'])/($row['assignment_mark']-$row['paper_mark']),0);
               $x=$a_r;
               if($a_r%10 > 5){
                  $a_r=$a_r-($a_r%10)+10;
               }else{
                  $a_r=$a_r-($a_r%10);
               }
               $p_r=100-$a_r;
               $query="REPLACE INTO ".$program_."_rubric(exam_hid,course_id,paper,assignment)VALUES('$exam_hid','".$row['course_id']."','$p_r','$a_r')";
               if(!exec_query($query,Q_RET_NON,$db=null,$array_key=null,$deleted=null,$no_connect=true)){
                  $error.=get_sql_error();
               }   
            }
            closedb();
         }
         if(!$create){
            return_status_json('ERROR',$error);
         }else{
            return_status_json('OK','Database migrated successfully!');
         }
      break;
   }
return;
}
d_r('dijit.form.Form');
d_r('dijit.form.Button');

?>

<form id='test_frm' dojoType='dijit.form.Form'>
<table>
<tr>
<td>Source Database</td><td><input type='text' dojoType='dijit.form.ValidationTextBox' name='source_db' id='source_db' jsId='source_db' value='courseadmin'></td>
</tr>
<tr>
<td>Destination Database</td><td><input type='text' dojoType='dijit.form.ValidationTextBox' name='dest_db' id='dest_db' jsId='dest_db' value='sis'></td>
</tr>

<tr>
<td>Database root password:</td><td><input type='password' dojoType='dijit.form.ValidationTextBox' name='root_pwd' id='root_pwd' jsId='root_pwd'></td>
</tr>
</tr>
<td>Program:</td><td>
<select dojoType='dijit.form.Select' name='program' >
<option value='bcsc'>BCSC</option>
<option value='bict'>BICT</option>
<option value='bit'>BIT</option>
</select>
</td></tr></table>
</form>
<script type='text/javascript' >
   function submit_form(action){
      update_status_bar('OK','...');
      update_progress_bar(10);
      if (dijit.byId('test_frm').validate()) {
         dojo.xhrGet({
         url         : '<?php echo gen_url(); ?>&action='+action, 
         handleAs      : 'json',
         form         : 'test_frm', 

         handle: function(response,ioArgs){
            update_status_bar(response.status,response.info);
            if(response.status=='ERROR'){   
               //update_progress_bar(0);
            }else{
               update_progress_bar(100);
            }
         },

         load: function(response) {
            update_status_bar('OK','rquest sent successfully');
            update_progress_bar(50);
         }, 
         error: function() {
            update_status_bar('ERROR','error on submission');
            update_progress_bar(0);
         }
      });

      return false;
   }else{
      update_status_bar('ERROR','Form contains invalid data.  Please correct first');
      return false;
   }
   return true;
}
</script>
