<?php
$program_=$_REQUEST["program"];
$source   ="pg";
$dest      ="sis";
$cc='';
$ic='';
switch($program_){
   case 'mit':
      $cc='MIT';
      $ic='55';
   break;
   case 'mcs':
      $cc='MCS';
      $ic='44';
   break;
   case 'mis':
      $cc='MIS';
      $ic='66';
   break;
}

$source   =isset($_REQUEST['source_db'])?$_REQUEST['source_db']:'';
$dest      =isset($_REQUEST['dest_db'])?$_REQUEST['dest_db']:'';

$migrate_queries=array(
   "student"   =>"REPLACE INTO ".$program_."_student(index_no,registration_no,initials,last_name,full_name,date_of_graduation,status,NID,current_address,title,email,designation,permanent_address,phone,work_place) SELECT indexno,regno,initials,lastname,fullname,dateofaward,status,nic,contactaddress,title,email,designation,address,telephone,workplace FROM ".$source.".students WHERE regno like '%".$cc."%'",
   "marks"      =>"REPLACE INTO ".$program_."_marks(exam_hid,index_no,course_id,final_mark) SELECT concat(exam+2000,'-01-01:',RIGHT(LEFT(code,4),1),':',IF(RIGHT(LEFT(code,4),1) >2,2,1)),indexno,code,marks FROM ".$source.".markbook WHERE indexno regexp '^0[0-9]".$ic."[0-9]*$';",
   "exam1"      =>"REPLACE INTO ".$program_."_exam(exam_hid,semester,student_year,exam_date) SELECT DISTINCT exam_hid,RIGHT(exam_hid,1),LEFT(RIGHT(exam_hid,3),1),LEFT(exam_hid,10) FROM ".$program_."_marks",
   "batch"      =>"REPLACE INTO ".$program_."_batch(batch_id,admission_year) SELECT  distinct LEFT(index_no,2),(LEFT(index_no,2)+2000) FROM ".$program_."_student",
   "course"      =>"REPLACE INTO ".$program_."_course(course_id,semester,student_year,lecture_credits,compulsory) SELECT code,semester,IF(RIGHT(LEFT(code,4),1) >2,2,1),credits,IF(category='C',1,0) FROM ".$source.".courses where code like '".$cc."%'"
);

//select case compulsory when 'x'  then 'a' when 'N' then 'b' end from sis.bcsc_course

$error="";
if(isset($_REQUEST['action'])){ /*haldle requests*/
   switch($_REQUEST['action']){
      case 'migrate_db':
         //cleanup the tables
         foreach(array("student","marks","exam","batch","course") as $table){
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
         foreach($migrate_queries as $key => $query){
            $GLOBALS['CONNECTION'] = mysql_connect("localhost", "root", $_REQUEST['root_pwd']);
            if($GLOBALS['CONNECTION'] && mysql_select_DB($dest, $GLOBALS['CONNECTION'])){
               if(!exec_query($query,Q_RET_NON,$db=null,$array_key=null,$deleted=null,$no_connect=true)){
                  $create=false;
                  $error.=get_sql_error();
               }   
            }
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
<td>Source Database</td><td><input type='text' dojoType='dijit.form.ValidationTextBox' name='source_db' id='source_db' jsId='source_db' value='pg'></td>
</tr>
<tr>
<td>Destination Database</td><td><input type='text' dojoType='dijit.form.ValidationTextBox' name='dest_db' id='dest_db' jsId='dest_db' value='sis'></td>
</tr>

<tr>
<td>Database root password:</td><td><input type='password' dojoType='dijit.form.ValidationTextBox' name='root_pwd' id='root_pwd' jsId='root_pwd'></td>
</tr>
</tr>
<td>Program:</td><td>
<select dojoType='dijit.form.ComboBox' name='program' >
<option value='mit'>mit</option>
<option value='mcs'>mcs</option>
<option value='mis'>mis</option>
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
