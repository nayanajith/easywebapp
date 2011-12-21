<?php
include A_CLASSES."/data_entry_class.php";
$super_table      ='rubric';
$keys               =array('exam_hid','course_id');
$key1               ='exam_hid';
$grid_array         =array('exam_hid','course_id','assignment','paper');
$grid_array_long   =array('exam_hid','course_id','assignment','paper');

$table            =$GLOBALS['P_TABLES'][$super_table];
$formgen          = new Formgenerator($table,$keys,$super_table,null);
$help_file         =$super_table."_help.php";
$modif_file         =$super_table."_modif.php";

$filter_string      ="";

/*Extract filter according to the filter_id in request string*/
if(isset($_REQUEST['filter_name']) && $_REQUEST['filter_name'] != ''){
   $filter_string=$formgen->ret_filter($_REQUEST['filter_name']);
}

/*generate csv with column headers*/
if(isset($_REQUEST['data']) && $_REQUEST['data']=='csv'){
   $filter_str=$filter_string!=""?" WHERE ".$filter_string:"";
   include $modif_file;
   $columns=array_keys($fields);
   $fields=implode(",",$columns);

   $query="SELECT $fields FROM ".$table.$filter_str;
   $csv_file= $table.".csv";
   db_to_csv_nr($query,$csv_file);
   return;
}

//id table mapper array
$table_of_id=array(
   'exam_hid'=>$GLOBALS['P_TABLES']['exam'],
   'course_id'=>$GLOBALS['P_TABLES']['course']
);

//Map filter for the given id
$filter_map=array(
   'course_id'=>isset($_SESSION[PAGE]['student_year'])?"student_year='".$_SESSION[PAGE]['student_year']."'":null,
);

//id table mapper array
$order_by_map=array(
   'exam_hid'=>'ORDER BY exam_hid DESC',
);




if(isset($_REQUEST['form'])){
   switch($_REQUEST['form']){
      case 'main':
         if(isset($_REQUEST['action'])){
            switch($_REQUEST['action']){
             case 'add':
               return $formgen->add_record();
             break;
             case 'modify':
               return $formgen->modify_record();
             break;
             case 'delete':
               return $formgen->delete_record();
             break;
            case 'combo':
               $filter_str=null;
               if(isset($filter_map[$_REQUEST['id']])){
                  $filter_str=$filter_map[$_REQUEST['id']];
               }

               $order_by=null;
               if(isset($order_by_map[$_REQUEST['id']])){
                  $order_by=$order_by_map[$_REQUEST['id']];
               }

               $formgen->xhr_filtering_select_data($table_of_id[$_REQUEST['id']],$_REQUEST['id'],$filter_str,$order_by);
            break;
            case 'param':
               $_SESSION[PAGE][$_REQUEST['param']]=$_REQUEST[$_REQUEST['param']];
               //exceptional cases
               switch($_REQUEST['param']){
                  case 'exam_hid':   
                     $admission_year=exec_query("SELECT student_year FROM ".$GLOBALS['P_TABLES']['exam']." WHERE exam_hid='".$_SESSION[PAGE][$_REQUEST['param']]."'",Q_RET_ARRAY);
                     $_SESSION[PAGE]['student_year']=$admission_year[0]['student_year'];
                  break;
               }
               return_status_json('OK',"Set ".$_REQUEST['param']."=".$_REQUEST[$_REQUEST['param']]);
            break;
            }   
         }else{
            if(isset($_REQUEST['id'])){
               $formgen->xhr_form_filler_data($_REQUEST['id'],null,'course_id');
            }
         }
      break;
      case 'filter':
         if(isset($_REQUEST['action'])){
            switch($_REQUEST['action']){
             case 'add':
               return $formgen->add_filter();
             break;
             case 'modify':
               return $formgen->modify_filter();
             break;
             case 'delete':
               return $formgen->delete_filter();
             break;
            }   
         }else{
            if(isset($_REQUEST['data'])&&$_REQUEST['data']=='json'){
               if(isset($_REQUEST['id'])){
                  $formgen->xhr_filter_filler_data($_REQUEST['id']);
               }else{
                  $filter_string.="table_name='".$table."'";
                  $formgen->xhr_filtering_select_data($GLOBALS['P_TABLES']['filter'],'filter_name',$filter_string);
               }
            }
         }
      break;
      case 'grid':
         echo $formgen->gen_data_grid($grid_array_long,null,$key1);
         echo $formgen->filter_selector();
      break;
   }
}else{
echo "<table width=100%><tr><td>";
   echo $formgen->gen_form(false,false);
   echo $formgen->gen_xhr_form_filler('fill_form',$table,'course_id',false);
   echo $formgen->gen_filter();
   echo "
      <script type='text/javascript' >
         function grid(){
            url='".gen_url().(isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:"")."&form=grid';
            open(url,'_self');
         }
      </script>
   ";
echo "</td><td width=40%>";
   //$json_url=$formgen->gen_json($grid_array,$filter_string,false,null);
   echo $formgen->gen_data_grid($grid_array,null,$key1);
echo "</td></tr></table>";
echo $formgen->filter_selector();

//generate help tips 
include $help_file;
$formgen->set_help_tips($help_array);
echo "<script type='text/javascript' >";
echo $formgen->param_setter();
echo "</script>";
}
?>

