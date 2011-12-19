<?php
//include A_CLASSES."/data_entry_class.php";
include A_CLASSES."/xhr_combobox_class.php";
$xhr_combobox=new XHR_Combobox();


//id table mapper array
$table_of_id=array(
   'batch_id'=>$GLOBALS['P_TABLES']['batch'],
   'from_exam_date'=>$GLOBALS['P_TABLES']['exam'],
   'to_exam_date'=>$GLOBALS['P_TABLES']['exam'],
);

//Map filter for the given id
$filter_map=array(
   'from_exam_date'=>isset($_SESSION[PAGE]['admission_year'])?" YEAR(exam_date)>='".$_SESSION[PAGE]['admission_year']."'":null,
);

//Map filter for the given id
$order_by_map=array(
   'batch_id'=>'ORDER BY batch_id DESC',
   'from_exam_date'=>'ORDER BY exam_date DESC',
   'to_exam_date'=>'ORDER BY exam_date DESC',
);


//Request functoin switcher
if(isset($_REQUEST['form'])){
   switch($_REQUEST['form']){
      case 'main':
      if(isset($_REQUEST['action'])){
         switch($_REQUEST['action']){
         case 'pdf':
            if(isset($_REQUEST['fid'])){
               file_download(MARKBOOKS,$_REQUEST['fid']);
            }
         break;
         case 'list':
            list_markbooks();
         break;
         case 'gen':
            generate_markbook();
         break;
         case 'store':
            $filter=null;
            if(isset($filter_map[$_REQUEST['id']])){
               $filter=$filter_map[$_REQUEST['id']];
            }

            if(isset($order_by_map[$_REQUEST['id']])){
               $order_by=$order_by_map[$_REQUEST['id']];
            }

            if($_REQUEST['id']=='from_exam_date' || $_REQUEST['id']=='to_exam_date'){
               $xhr_combobox->json_data($table_of_id[$_REQUEST['id']],'exam_date',$filter,$order_by,$_REQUEST['id']);
            }else{
               $xhr_combobox->json_data($table_of_id[$_REQUEST['id']],$_REQUEST['id'],$filter,$order_by);
            }
         break;
         case 'param':
            $_SESSION[PAGE][$_REQUEST['param']]=$_REQUEST[$_REQUEST['param']];
            if($_REQUEST['param']=='batch_id'){
               //Getting year of the batch to use with exam_hid filter
               $arr=exec_query("SELECT admission_year FROM ".$GLOBALS['P_TABLES']['batch']." WHERE batch_id='".$_REQUEST['batch_id']."'",Q_RET_ARRAY);
               $_SESSION[PAGE]['admission_year']=$arr[0]['admission_year'];
            }
            return_status_json('OK',"Set ".$_REQUEST['param']."=".$_REQUEST[$_REQUEST['param']]);
         break;
         }
      }
   }
}else{
   echo "<div align='center'>";
   echo "<div id='mark_book_frm' jsId='mark_book_frm' dojoType='dijit.form.Form' >";
   echo "<h3 class='coolh'>Previousely generated mark books</h3>";
   if(isset($_SESSION[PAGE]['batch_id'])){
      list_markbooks();
   }   
   echo "</div></div>";

   echo "<script type='text/javascript'>";
   echo "dojo.addOnLoad(function() {";

   $xhr_combobox->gen_xhr_combobox('batch_id',"Batch",$xhr_combobox->get_val('batch_id'),80,20,null,null);
   $xhr_combobox->gen_xhr_static_combo('student_year',"Study year",$xhr_combobox->get_val('student_year'),40,array(1,2,3,4),null,null);
   $xhr_combobox->gen_xhr_combobox('from_exam_date',"From exam",$xhr_combobox->get_val('from_exam_date'),80,20,null,null);
   $xhr_combobox->gen_xhr_combobox('to_exam_date',"To exam",$xhr_combobox->get_val('to_exam_date'),80,20,null,null);

   $xhr_combobox->param_setter();$xhr_combobox->html_requester();

   echo "
   var reload_button=new dijit.form.Button({
      iconClass:'dijitIcon dijitIconFunction',
      label: 'Generate',
      onClick:function(){submit_form('gen');},
   });
   toolbar.addChild(reload_button);";


   echo "});";
   $xhr_combobox->form_submitter('mark_book_frm');
   echo "</script>";
}

function generate_markbook(){
   include(MOD_CLASSES.'/mark_book_pdf_class.php');
   $_SESSION[PAGE]['academic_year'] ='2010/2011';
   $_SESSION[PAGE]['exam_held']     =$_SESSION[PAGE]['from_exam_date']."-".$_SESSION[PAGE]['to_exam_date'];
   /*Instantiate mark book class*/
   $bit_mark_book=new Mark_book(PROGRAM, $_SESSION[PAGE]['student_year'],$_SESSION[PAGE]['academic_year'],$_SESSION[PAGE]['batch_id'],$_SESSION[PAGE]['exam_held']);
   
   /*Get student list*/
   $query="SELECT index_no FROM ".$GLOBALS['P_TABLES']["student"]." WHERE batch_id='".$_SESSION[PAGE]['batch_id']."' LIMIT 1,100";
   $res=exec_query($query,Q_RET_MYSQL_RES);
   
   /*Generate mark book for the selected students*/
   while($row=mysql_fetch_array($res)){
      $bit_mark_book->gen_student_array($row['index_no']);
      $bit_mark_book->add_student_record();
   }

   $file=$_SESSION[PAGE]['student_year'].':'.$_SESSION[PAGE]['academic_year'].':'.$_SESSION[PAGE]['batch_id'].':'.$_SESSION[PAGE]['exam_held'].':'.'mark_book.pdf';   
   $file=str_replace('/','_',$file);
   $pdf=$bit_mark_book->getPdf();
   /*Close and output PDF document*/
   //$pdf->Output('mark_book.pdf', 'I');
   /*Close and save PDF document*/
   $pdf->Output(MARKBOOKS.'/'.$file, 'F');
   return_status_json('OK',"Mark book created!");
}
   
function list_markbooks(){
   $dir = MARKBOOKS;

   // Open a known directory, and proceed to read its contents
   if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
          while (($file = readdir($dh)) !== false) {
            if(filetype($dir."/".$file) == 'file'){
              echo "<a href='".gen_url()."&form=main&action=pdf&fid=".base64_encode($file)."'>$file</a><br>\n";
            }
          }
          closedir($dh);
      }
   }
}


?>
