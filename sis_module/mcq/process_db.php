<?php
include MOD_CLASSES."/process_db_class.php";

if(isset($_REQUEST['data']) && $_REQUEST['data']=='csv'){
   $table   ='marks';
   $fields   =array('index_no','paper_id','section','mark');

   $headers="";
   $comma="";

   foreach($fields as $field){
      $headers.=$comma."'$field' AS $field";
      $comma=",";
   }
   
   $columns=implode(",",$fields);
   $query="SELECT $headers FROM ".$GLOBALS['MOD_P_TABLES'][$table]."  UNION SELECT $columns FROM ".$GLOBALS['MOD_P_TABLES'][$table]." WHERE paper_id='".$_REQUEST['paper_id']."' AND section='".$_REQUEST['section_id']."'";
   
   $csv_file= tempnam(sys_get_temp_dir(), 'ucscsis').".csv";
   db_to_csv($query,$csv_file);
   header('Content-Type', 'application/vnd.ms-excel');
   header('Content-Disposition: attachment; filename='.$GLOBALS['MOD_P_TABLES'][$table].'.csv');
   if(file_exists($csv_file)){
      readfile($csv_file);
   }
   exit();
}

if(isset($_REQUEST['form']) && isset($_REQUEST['action'])){

   $arr=explode('@',$_REQUEST['paper_id']);
   $SQL="SELECT paper_id FROM ".$GLOBALS['MOD_P_TABLES']['paper']." WHERE course_id='".$arr[0]."' AND exam_id='".$arr[1]."'";
   $arr=exec_query($SQL,Q_RET_ARRAY);
   $paper_id=$arr[0]['paper_id'];

   
    $mcq_paper = new MCQ_paper($paper_id);
   switch($_REQUEST['action']){
      case 'extract':
         if($mcq_paper->extract_answers() && $mcq_paper->extract_marking_logic()){
            return_status_json('OK','extraction of answers and logic ok');
         }else{
            return_status_json('ERRROR','extraction of answers and/or logic failed');
         }
      break;
      case 'item':
         //echo "item";
         $mcq_paper->print_stat($mcq_paper->gen_stat(0));
      break;
      case 'mark':
         /*Mark all sections*/
         $info='';
         $comma='';
         $is_error=false;

         /*Mark all sections seperately*/
         for($i=1;$i<=$mcq_paper->get_num_sections();$i++)
         {
            if($mcq_paper->mark_answers($i)){
               $info.="<a href=\'".gen_url()."&data=csv&paper_id=".$paper_id."&section_id=$i\'>marks of section $i</a><br>";
            }else{
               $info.="error in section $i<br>";
               $is_error=true;
            }
         }
         if(!$is_error){
            return_status_json('OK',$info);
         }else{
            return_status_json('ERROR',$info);
         }
      break;
   }   
   exit();
}
?>

<div id='process_frm' name='process_frm' dojoType='dijit.form.Form'>
<select name='paper_id' id='paper_id' jsId='paper_id' dojoType='dijit.form.ComboBox'>
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
      if(action == 'item'){
         window.open('<?php echo gen_url(); ?>&form=main&action='+action+'&paper_id='+paper_id.getValue(),'stat','location=0,status=0,scrollbars=1,width=800,height=600');
         return;
      }

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
                  update_status_bar('marking ok');
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
