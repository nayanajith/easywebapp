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
         case 'pdf':
         case 'csv':
         case 'print':
         case 'html':
            @$_SESSION[PAGE]['batch_id']=$_REQUEST['batch_id'];
            print_gpa();
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
   echo "<div align='center'>";
   echo "<div id='gpa_frm' jsId='gpa_frm' dojoType='dijit.form.Form' >";
   if(isset($_SESSION[PAGE]['batch_id'])){
      print_gpa();
   }   
   echo "</div></div>";

   echo "<script type='text/javascript'>";
   echo "dojo.addOnLoad(function() {";

   //function gen_xhr_combobox($id,$label,$value,$width,$page_size,$source_array=null,$target=null);
   //$xhr_combobox->gen_xhr_combobox('student_year',"Student Year",$xhr_combobox->get_val('student_year'),30,20,null,null);
   $xhr_combobox->gen_xhr_combobox('batch_id',"Batch",$xhr_combobox->get_val('batch_id'),80,20,array('batch_id'),'gpa_frm');

   $item_array=array('1','2','3','4','3T','4T');
   $xhr_combobox->gen_xhr_static_combo('year','Year',$xhr_combobox->get_val('year'),120,$item_array,array('batch_id','year'),'gpa_frm');

   $item_array=array('index_no','degree_gpa desc','class_gpa desc','degree_gpa asc','class_gpa asc');
   $xhr_combobox->gen_xhr_static_combo('order_by','Order by',$xhr_combobox->get_val('order_by'),120,$item_array,array('batch_id','year','order_by'),'gpa_frm');


   echo "
   var reload_button=new dijit.form.Button({
      iconClass:'dijitIcon dijitIconFunction',
      label: 'Reload',
      onClick:function(){request_html('gpa_frm',new Array('batch_id'),null);},
   });
   toolbar.addChild(reload_button);";


   $xhr_combobox->param_setter();$xhr_combobox->html_requester();
   echo "});";
   $xhr_combobox->form_submitter('gpa_frm');
   echo "</script>";
}

function print_gpa(){
   //the details which want to view
   $row=array(
      'index_no',
      'year',
      'degree_gpv',
      'degree_gpa',
      'class_gpv',
      'class_gpa',
      'credits',
   );

   //filter by year
   $year="";
   if(isset($_SESSION[PAGE]['year'])){
      $year="AND year='".$_SESSION[PAGE]['year']."'"; 
   }

   //order by index_no /gpa/gpv
   $order_by="";
   if(isset($_SESSION[PAGE]['order_by'])){
      $order_by="ORDER BY ".$_SESSION[PAGE]['order_by']; 
   }


   $arr=exec_query("SELECT ".implode($row,",")." FROM ".$GLOBALS['P_TABLES']['gpa']." WHERE index_no LIKE '".$_SESSION[PAGE]['batch_id']."%' $year $order_by",Q_RET_ARRAY);

   $report= "<tr><th>#</th><th>".implode($row,"</th><th>")."</th></tr>";
   $no=1;
   foreach($arr as $row){
      $report.= "<tr><td>".$no++."</td><td>".implode(array_values($row),"</td><td>")."</td></tr>";
   }

   if(isset($_REQUEST['action'])&&$_REQUEST['action']=='csv'){
      table_to_csv($report,"GPA-".$_SESSION[PAGE]['batch_id']);
   }elseif(isset($_REQUEST['action'])&&$_REQUEST['action']=='pdf'){
      include A_CLASSES."/letterhead_pdf_class.php";
      $letterhead=new Letterhead("A4","P");

      //insert the content to the pdf
      $report="<h3 class='coolh'>GPA of the students in batch ".$_SESSION[PAGE]['batch_id']."</h3><table class='clean' border=1>$report</table>";
      $letterhead->include_content(str_replace("'","\"",$report));

      //Acquire pdf document
      $pdf=$letterhead->getPdf();

      //name of the pdf file
      $pdf_file="GPA-".$_SESSION[PAGE]['batch_id'].".pdf";

      $pdf->Output($pdf_file, 'I');
      //$pdf->Output(TMP."/".$pdf_file, 'F');
      return;
   }else{
      echo "<h3 class='coolh'>GPA of the students in batch ".$_SESSION[PAGE]['batch_id']."</h3><table class='clean' border=1>$report</table>";
   }
}
?>
