<?php
include A_CLASSES."/xhr_combobox_class.php";
$xhr_combobox=new XHR_Combobox();

/*
   Student registraton form generator
*/
function gen_pass_list_form(){
   if(isset($_SESSION[PAGE]['batch_id']) && isset($_SESSION[PAGE]['degree'])){
   }else{
      return;
   }

   include MOD_CLASSES."/student_eligibility_class.php";
   echo $_SESSION[PAGE]['batch_id']."-".$_SESSION[PAGE]['degree']; 
   $arr=exec_query("SELECT index_no FROM ".$GLOBALS['P_TABLES']['student']." WHERE batch_id='".$_SESSION[PAGE]['batch_id']."' LIMIT 1",Q_RET_ARRAY,null,'index_no');
   foreach(array_keys($arr) as $index_no){
      echo $index_no."<br>";
      $eligibility = new Eligibility($index_no,'YEAR-4');
   }

}


/*
Function to save the registration request from the frontend
*/
function save_pass_list(){
   $error=array();
   if(!is_query_ok()){
      $error[]=get_sql_error();
   }

   //Return the registration status as json
   if(sizeof($error)>0){
      return_status_json('ERROR',implode(',',$error));
   }else{
      return_status_json('OK','Updated successfully!');
   }
}

//id table mapper array
$table_of_id=array(
   'batch_id'=>$GLOBALS['P_TABLES']['batch'],
);

//Map filter for the given id for returning json data
$filter_map=array(
);

//order the given id for returning json data
$order_by_map=array(
   'batch_id'=>" ORDER BY batch_id DESC"
);


//Switch the functionality according to the request
if(isset($_REQUEST['action'])){
   switch($_REQUEST['action']){
      case 'modify':
         save_pass_list();
      break;
      case 'store':
         $filter   =null;
         $order_by=null;
         if(isset($filter_map[$_REQUEST['id']])){
            $filter=$filter_map[$_REQUEST['id']];
         }
         if(isset($bordr_by_map[$_REQUEST['id']])){
            $order_by=$bordr_by_map[$_REQUEST['id']];
         }

         $xhr_combobox->json_data($table_of_id[$_REQUEST['id']],$_REQUEST['id'],$filter,$order_by);
      break;
      break;
      case 'pdf':
      case 'csv':
      case 'html':
         gen_pass_list_form();
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
         }

         return_status_json('OK',$_REQUEST['param'].'='.$_REQUEST[$_REQUEST['param']]);
      break;
   }
}else{
      //Print html when requested
      echo "<div align='center'><div dojoType='dijit.form.Form' id='pass_list_frm' name='pass_list_frm' jsId='pass_list_frm' >";
         gen_pass_list_form();
      echo "</div></div>";

      //Index number selector in toolbar
      echo "<script type='text/javascript'>";
      echo "dojo.addOnLoad(function() {";

      //function gen_xhr_combobox($id,$label,$value,$width,$page_size,$source_array=null,$target=null);
      $xhr_combobox->gen_xhr_combobox('batch_id',"Batch",$xhr_combobox->get_val('batch_id'),80,20,null,null);

      $item_array=array('GENERAL','HONEST');
      $xhr_combobox->gen_xhr_static_combo('degree','Degree',$xhr_combobox->get_val('degree'),110,$item_array,array('batch_id','degree'),'pass_list_frm');



      $xhr_combobox->param_setter();$xhr_combobox->html_requester();
      echo "});";
      $xhr_combobox->form_submitter('pass_list_frm');
      echo "</script>";

   }
?>
