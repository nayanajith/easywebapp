<?php
include A_CLASSES."/xhr_combobox_class.php";
$xhr_combobox=new XHR_Combobox();

$eligibility_arr=array(
   'YEAR-2'=>'bcsc_year_2_eligibility_class.php',
   'YEAR-3'=>'bcsc_year_3_eligibility_class.php',
   'YEAR-4'=>'bcsc_year_4_eligibility_class.php',
   'DEGREE-3'=>'bcsc_degree_3_eligibility_class.php',
   'DEGREE-4'=>'bcsc_degree_4_eligibility_class.php',
);

/*
   Student registraton form generator
*/
function gen_eligibility_list($eligibility_arr){

   if(isset($_SESSION[PAGE]['batch_id']) && isset($_SESSION[PAGE]['eligibility'])){
   }else{
      return;
   }
   $_SESSION[PAGE]['filter']=isset($_SESSION[PAGE]['filter'])?$_SESSION[PAGE]['filter']:'ALL';

   include MOD_CLASSES."/".$eligibility_arr[$_SESSION[PAGE]['eligibility']];
   $RULE=$RULE;
   $KEYS=$KEYS;

   $arr=exec_query("SELECT index_no FROM ".$GLOBALS['P_TABLES']['student']." WHERE batch_id='".$_SESSION[PAGE]['batch_id']."'",Q_RET_ARRAY,null,'index_no');
   $report="";
   switch($_SESSION[PAGE]['filter']){
   case 'ALL':
      $report.= "<tr><th>Index No</th><th>Eligible</th><th>".style_text($KEYS[1])."</th><th>".style_text($KEYS[2])."</th></tr>";
      foreach(array_keys($arr) as $index_no){
         $report.= "<tr><td>".$index_no."</td>";
         $eligibility=new Eligibility($index_no);
         $info      =$eligibility->get_eligibility();

         $state   =$info['state']?'YES':'NO';

         $report.= '<td>'.$state.'</td><td>'.$info[$KEYS[1]].'</td><td>'.$info[$KEYS[2]].'</td></tr>';
      }
   break;
   case 'ELIGIBLE':
      $report.= "<tr><th>Index No</th><th>".style_text($KEYS[1])."</th><th>".style_text($KEYS[2])."</th></tr>";
      foreach(array_keys($arr) as $index_no){
         $eligibility=new Eligibility($index_no);
         $info=$eligibility->get_eligibility();

         if($info['state']){
            $report.= '<tr><td>'.$index_no.'</td><td>'.$info[$KEYS[1]].'</td><td>'.$info[$KEYS[2]].'</td></tr>';
         }
      }
   break;
   case 'NOT_ELIGIBLE':
      $report.= "<tr><th>Index No</th><th>".style_text($KEYS[1])."</th><th>".style_text($KEYS[2])."</th></tr>";
      foreach(array_keys($arr) as $index_no){
         $eligibility=new Eligibility($index_no);
         $info=$eligibility->get_eligibility();

         if(!$info['state']){
            $report.= '<tr><td>'.$index_no.'</td><td>'.$info[$KEYS[1]].'</td><td>'.$info[$KEYS[2]].'</td></tr>';
         }
      }

   break;
   }

   if(isset($_REQUEST['action']) && $_REQUEST['action']=='print'){
      echo "<h3 class='coolh'>$RULE</h3>";
      echo "<table border='1' style='border-collapse:collapse'>";
      echo $report;
      echo "</table>";
      echo  "<script type='text/javascript' >window.print();</script>'";
   }elseif(isset($_REQUEST['action']) && $_REQUEST['action']=='csv'){
      $report=trim($report);
      $report=str_replace(array('</td><td>','</th><th>'),"','",$report);
      $report=str_replace(array('<tr><td>','<tr><th>'),"'",$report);
      $report=str_replace(array('</td></tr>','</th></tr>'),"'\n",$report);

      header('Content-Type', 'application/vnd.ms-excel');
      header('Content-Disposition: attachment; filename='.$_SESSION[PAGE]['batch_id'].'-'.$_SESSION[PAGE]['eligibility'].'.csv');
      header("Pragma: no-cache");
      header("Expires: 0");
      echo $report;
   }else{
      echo "<h3 class='coolh'>$RULE</h3>";
      echo "<table border='1' style='border-collapse:collapse'>";
      echo $report;
      echo "</table>";
   }
}


/*
Function to save the registration request from the frontend
*/
function save_eligibility_list(){
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
   'index_no'=>$GLOBALS['P_TABLES']['student'],
);

//Map filter for the given id for returning json data
$filter_map=array(
   'index_no'=>isset($_SESSION[PAGE]['batch_id'])?" batch_id='".$_SESSION[PAGE]['batch_id']."'":null,
);

//order the given id for returning json data
$order_by_map=array(
   'batch_id'=>" ORDER BY batch_id DESC"
);


//Switch the functionality according to the request
if(isset($_REQUEST['action'])){
   switch($_REQUEST['action']){
      case 'modify':
         save_eligibility_list();
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
      case 'html':
      case 'csv':
      case 'print':
         gen_eligibility_list($eligibility_arr);
      break;
      case 'param':
         $_SESSION[PAGE][$_REQUEST['param']]=$_REQUEST[$_REQUEST['param']];

         //exceptional cases
         switch($_REQUEST['param']){
            case 'batch_id':
            break;
         }
         return_status_json('OK',$_REQUEST['param'].'='.$_REQUEST[$_REQUEST['param']]);
      break;
   }
}else{
      //Print html when requested
      echo "<div align='center'><div dojoType='dijit.form.Form' id='eligibility_frm' name='eligibility_frm' jsId='eligibility_frm' >";
         gen_eligibility_list($eligibility_arr);
      echo "</div></div>";

      //Index number selector in toolbar
      echo "<script type='text/javascript' type='text/javascript'>";
      echo "dojo.addOnLoad(function() {";

      //function gen_xhr_combobox($id,$label,$value,$width,$page_size,$source_array=null,$target=null);
      $xhr_combobox->gen_xhr_combobox('batch_id',"Batch",$xhr_combobox->get_val('batch_id'),80,20,array('batch_id','eligibility'),'eligibility_frm');
      //$xhr_combobox->gen_xhr_combobox('index_no',"Index No",$xhr_combobox->get_val('index_no'),80,20,array('index_no'),'eligibility_frm');

      $xhr_combobox->gen_xhr_static_combo('eligibility','Eligibility',$xhr_combobox->get_val('eligibility'),110,array_keys($eligibility_arr),array('batch_id','eligibility'),'eligibility_frm');

      $item_array=array('ALL','ELIGIBLE','NOT_ELIGIBLE');
      $xhr_combobox->gen_xhr_static_combo('filter','Filter',$xhr_combobox->get_val('filter'),110,$item_array,array('batch_id','filter'),'eligibility_frm','filter');


      $xhr_combobox->param_setter();$xhr_combobox->html_requester();
      echo "});";
      $xhr_combobox->form_submitter('eligibility_frm');
      echo "</script>";

   }
?>
