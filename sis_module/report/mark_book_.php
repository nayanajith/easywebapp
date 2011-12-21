<?php
if(
isset($_REQUEST['student_year'])   &&
isset($_REQUEST['batch'])         &&
isset($_REQUEST['exam_held'])      &&
isset($_REQUEST['academic_year'])&&

$_REQUEST['student_year']   !=''   &&
$_REQUEST['batch']         !=''   &&
$_REQUEST['exam_held']      !=''   &&
$_REQUEST['academic_year']   !=''

){

include(MOD_CLASSES.'/mark_book_class.php');

/*Instantiate mark book class*/
$bit_mark_book=new Mark_book(PROGRAM, $_REQUEST['student_year'],$_REQUEST['batch'],$_REQUEST['academic_year'],$_REQUEST['exam_held']);

/*Get student list*/
$query="SELECT index_no FROM ".$GLOBALS['P_TABLES']["student"]." WHERE batch='".$_REQUEST['batch']."' LIMIT 1,100";
$res=exec_query($query,Q_RET_MYSQL_RES);

/*Generate mark book for the selected students*/
while($row=mysql_fetch_array($res)){
   $bit_mark_book->gen_student_array($row['index_no']);
   $bit_mark_book->add_student_record();
}


$pdf=$bit_mark_book->getPdf();
/*Close and output PDF document*/
$pdf->Output('mark_book.pdf', 'I');
/*Close and save PDF document*/
//$pdf->Output(getcwd().'/mark_book.pdf', 'F');
}else{

/*Generate options for the lists according to the given arrays*/
function options_gen($arr,$selected=null){
   $options="";
   foreach($arr as $key => $data){
      $val=$data[key($data)];
      if($selected != null && $selected == $val){
         $options.="<option value='$val' selected=selected>$val</option>";
      }else{
         $options.="<option value='$val'>$val</option>";
      }
   }
   echo $options;
}

/*Arrays to generate option lists*/
$study_year_arr   =exec_query("SELECT DISTINCT student_year FROM ".$GLOBALS['P_TABLES']["exam"],Q_RET_ARRAY);
$batch_arr         =exec_query("SELECT DISTINCT batch FROM ".$GLOBALS['P_TABLES']["student"],Q_RET_ARRAY);
$academic_year_arr=exec_query("SELECT DISTINCT academic_year FROM ".$GLOBALS['P_TABLES']["exam"],Q_RET_ARRAY);


/*Form submit through AJAX using dojo xhrGet*/
echo "
<script type='text/javascript' >
   function submit_form(){
      alert(dojo.toJson(dijit.byId('mark_book_frm').getValues(), true));
      if (dijit.byId('mark_book_frm').validate()) {
         dojo.xhrGet({
         url         : '".gen_url()."&data=true', 
         handleAs      : 'text',
         form         : 'mark_book_frm', 
         handle      : function(response){alert(response);},
         load         : function(){alert('Form successfully submitted');}, 
         error         : function(){alert('Error on submission');}
      });

      return false;
   }else{
      alert('Form contains invalid data.  Please correct first');
      return false;
   }
   return false;
}
</script>";

?>

<div id='mark_book_frm' action="<?php echo gen_url(); ?>" dojoType='dijit.form.Form' method='GET'>
<table>
<tr>
<td>Study Year</td>
<td>:<select dojoType='dijit.form.ComboBox' name='student_year'><?php options_gen($study_year_arr,null); ?></select></td>
</tr>
<tr>
<td>Batch</td>
<td>:<select dojoType='dijit.form.ComboBox' name='batch'><?php options_gen($batch_arr,null); ?></select></td>
</tr>
<tr>
<td>Exam Held</td>
<td>:<input type=text dojoType='dijit.form.ValidationTextBox' name='exam_held' value='11/08/2007-12/08/2007'></td>
</tr>
<tr>
<td>Academic Year</td>
<td>:<select dojoType='dijit.form.ComboBox' name='academic_year'><?php options_gen($academic_year_arr,null); ?></select></td>
</tr>
<tr>
<td><button dojoType='dijit.form.Button' onClick='submit_form()'>Generate Mark Book</button></td>
<td></td>
</tr>
</table>
</div>
<?php 
}
?>
