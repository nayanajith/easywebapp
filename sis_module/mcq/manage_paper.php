<?php
include A_CLASSES."/data_entry_class.php";
include MOD_CLASSES."/file_uploader_class.php";
$table            =$GLOBALS['MOD_P_TABLES']['paper'];
$file               ="paper";
$key1               ='paper_id';
$grid_array         =array('paper_id','course_id','exam_id','state');
$grid_array_long   =array('paper_id','state');
$formgen          = new Formgenerator($table,$key1,$file);
$filter_string      ="";

/*Extract filter according to the filter_id in request string*/
if(isset($_REQUEST['filter_name']) && $_REQUEST['filter_name'] != ''){
   $filter_string=$formgen->ret_filter($_REQUEST['filter_name']);
}

/*generate csv with column headers*/
if(isset($_REQUEST['data']) && $_REQUEST['data']=='csv'){
   $filter_str=$filter_string!=""?" WHERE ".$filter_string:"";
   include $table."_modif.php";
   $columns=array_keys($fields);
   $headers="";
   $comma="";

   foreach($columns as $column){
      $headers.=$comma."'$column' AS $column";
      $comma=",";
   }
   
   $fields=implode(",",$columns);
   $query="SELECT $headers FROM ".$GLOBALS['MOD_P_TABLES'][$table]." UNION SELECT $fields FROM ".$GLOBALS['MOD_P_TABLES'][$table] .$filter_str;
   
   $csv_file= tempnam(sys_get_temp_dir(), 'ucscsis').".csv";
   db_to_csv($query,$csv_file);
   header('Content-Type', 'application/vnd.ms-excel');
   header('Content-Disposition: attachment; filename='.$GLOBALS['MOD_P_TABLES'][$table].'.csv');
   readfile($csv_file);
   return;
}

if(isset($_REQUEST['form'])){
   switch($_REQUEST['form']){
      case 'file':
         if(isset($_REQUEST['action'])){
            switch($_REQUEST['action']){
               case 'delete':
                  $file=str_replace('1','',$_REQUEST['file_name'])."_".$_REQUEST['file_id'].".csv";
                  $file_uploader      = new File_uploader(MOD_A_CSV,$file);
                  $ret=$file_uploader->delete_file();
                  if($ret==-1){
                     return_status_json('ERROR','');
                  }else{
                     exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES'][$table]." SET ".str_replace('1','',$_REQUEST['file_name'])."='' WHERE paper_id='".$_REQUEST['file_id']."' ",Q_RET_MYSQL_RES);
                     return_status_json('OK','');
                  }
               break;
               case 'upload':
               default:
                  $file=str_replace('1','',$_REQUEST['file_name'])."_".$_REQUEST['file_id'].".csv";
                  $file_uploader      = new File_uploader(MOD_A_CSV,$file);

                  $ret=$file_uploader->server_side_handle(array('text/csv'),(1024*1024*500),$_REQUEST['file_name'],true);

                  if(is_array($ret)){
                     echo "<textarea>";
                     return_status_json('OK','');
                     echo "</textarea>";
                     exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES'][$table]." SET ".str_replace('1','',$_REQUEST['file_name'])."='$file' WHERE paper_id='".$_REQUEST['file_id']."' ",Q_RET_MYSQL_RES);
                  }else{
                     echo "<textarea>";
                     return_status_json('ERROR',$ret);
                     echo "</textarea>";
                  }
               break;
            }
         }
         exit();
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

            }   
         }else{
            if(isset($_REQUEST['data'])&&$_REQUEST['data']=='json'){
               if(isset($_REQUEST['id'])){
                  
                  $formgen->xhr_form_filler_data($_REQUEST['id']);
               }else{
                  $formgen->xhr_filtering_select_data(null,null,$filter_string);
               }
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
                  $filter_string.="table_name='".$GLOBALS['MOD_P_TABLES'][$table]."'";
                  $formgen->xhr_filtering_select_data($GLOBALS['MOD_P_TABLES']['filter'],'filter_name',$filter_string);
               }

            }
         }
      break;
      case 'grid':
         $json_url=$formgen->gen_json($grid_array_long,$filter_string,false);
         echo $formgen->gen_data_grid($grid_array_long,$json_url,$key1);
         filter_selector();

      break;
      case 'select_filter':
         $formgen->xhr_filtering_select_data($GLOBALS['MOD_P_TABLES']['filter'],'filter_name',"table_name='".$GLOBALS['MOD_P_TABLES'][$table]."'");
      break;
   }
}else{
echo "<table width=100%><tr><td height='100%' style='vertical-align:top'>";
   echo $formgen->gen_form(false,true);
   echo $formgen->gen_filter();
   echo "
      <script type="text/javascript" >
         function grid(){
            url='".gen_url().(isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:"")."&form=grid';
            open(url,'_self');
         }
      </script>
   ";
echo "</td><td width=40% style='height:150;vertical-align:top'>";
   $json_url=$formgen->gen_json($grid_array,$filter_string,false,null);
   echo $formgen->gen_data_grid($grid_array,$json_url,$key1);
echo "</td></tr></table>";
filter_selector();

include $file."_help.php";
}

function filter_selector(){
?>

<!--_____________________________start filter select___________________________-->

<script type="text/javascript" type="text/javascript">
dojo.addOnLoad(function() {
   toolbar = new dijit.byId("toolbar");
   var filterStore = new dojox.data.QueryReadStore({
      url: "<?php echo gen_url(); ?>&data=json&form=select_filter"
   });
   var filteringSelect = new dijit.form.FilteringSelect({
       id: "filter_select",
       name: "state",
       value: "<?php echo isset($_REQUEST['filter_name'])?$_REQUEST['filter_name']:"aa"; ?>",
       store: filterStore,
       searchAttr: "filter_name",
       pageSize: '20',
       onChange:function(){change_filter(this.get("displayedValue"))},
   },"stateSelect");

   toolbar.addChild(filteringSelect);
   filteringSelect.setValue("<?php echo isset($_REQUEST['filter_name'])?$_REQUEST['filter_name']:"aa"; ?>");
});

function change_filter(filter_name){
   if(filter_name != ''){
      URL='<?php echo gen_url().(isset($_REQUEST['form'])?"&form=".$_REQUEST['form']:"");?>&filter_name='+filter_name;
      open(URL,'_self');
   }
}

function get_csv(){
   url='<?php echo gen_url().(isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:""); ?>&data=csv';
   open(url,'_self');
}
</script>

<!--_______________________________end filter select___________________________-->
<?php
}
?>
