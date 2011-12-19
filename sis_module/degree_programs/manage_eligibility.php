<?php
include A_CLASSES."/data_entry_class.php";

$super_table      ="eligibility";
$key1               ='eligibility_name';
$grid_array         =array('eligibility_name','GPA');
$grid_array_long   =array('eligibility_name','GPA');

$table            =$GLOBALS['P_TABLES'][$super_table];
$filter_table      =$GLOBALS['P_TABLES']['filter'];
$formgen          = new Formgenerator($table,$key1,$super_table,null);
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
   $headers="";
   $comma="";

   foreach($columns as $column){
      $headers.=$comma."'$column' AS $column";
      $comma=",";
   }
   
   $fields=implode(",",$columns);
   $query="SELECT $headers FROM ".$table." UNION SELECT $fields FROM ".$table.$filter_str;
   
   $csv_file= tempnam(sys_get_temp_dir(), 'ucscsis').".csv";
   db_to_csv($query,$csv_file);
   header('Content-Type', 'application/vnd.ms-excel');
   header('Content-Disposition: attachment; filename='.$table.'.csv');
   readfile($csv_file);
   return;
}

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

            }   
         }else{
            if(isset($_REQUEST['data'])&&$_REQUEST['data']=='json'){
               if(isset($_REQUEST['id'])){
                  $formgen->xhr_form_filler_data($_REQUEST['id']);
                  exit();
               }else{
                  $formgen->xhr_filtering_select_data(null,null,$filter_string);
                  exit();
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
                  exit();
               }else{
                  $filter_string.="table_name='".$table."'";
                  $formgen->xhr_filtering_select_data($filter,'filter_name',$filter_string);
                  exit();
               }

            }
         }
      break;
      case 'grid':
         $json_url=$formgen->gen_json($grid_array_long,$filter_string,false);
         echo $formgen->gen_data_grid($grid_array_long,$json_url,$key1);
         $formgen->filter_selector();
      break;
      case 'select_filter':
         $formgen->xhr_filtering_select_data($filter,'filter_name',"table_name='".$table."'");
         exit();
      break;
   }
}else{
d_r('dijit.layout.BorderContainer');
d_r('dijit.layout.ContentPane');
echo "<table><tr><td>";
echo $formgen->gen_form(false,true);

if($GLOBALS['LAYOUT']='app'){
echo $formgen->gen_filter();
echo "
      <script type="text/javascript" >
         function grid(){
            url='".gen_url().(isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:"")."&form=grid';
            open(url,'_self');
         }
      </script>
   ";
$formgen->filter_selector();
//generate help tips
include $help_file;
$formgen->set_help_tips($help_array);

echo "</td><td>";

$json_url=$formgen->gen_json($grid_array,$filter_string,false,null);
echo $formgen->gen_data_grid($grid_array,$json_url,$key1);
}

echo "</td></tr></table>";
}


/*Select Grades for the course A+ ~ E*/
$grades="<option value='-NO-'>NO</option>";
foreach(array_keys($minGradeMark) as $value){
   $grades.="<option value='$value'>$value</option>";
}

/*Generate check box and drop down select of grade to select course and grade*/
$cols=1;
$count=1;
$res=exec_query("SELECT * FROM ".$GLOBALS['P_TABLES']['course'],Q_RET_MYSQL_RES);
$course=array('1'=>'','2'=>'','3'=>'','4'=>'');
d_r('dijit.form.ComboBox');
while($row=mysql_fetch_assoc($res)){
   $course[$row['student_year']].="<tr><td>".$row['course_id']."</td><td>:<select dojoType='dijit.form.ComboBox' name='".$row['course_id']."' id='".$row['course_id']."' jsId='".$row['course_id']."' >$grades</select></td></tr>";
}

/*Add drop down buttons to the form using js for all years in $course*/
/*Add buttons for each textarea to pop-up dialog box*/
d_r('dijit.Dialog');
d_r('dijit.form.TextBox');
d_r('dijit.form.Button');
foreach($course as $key => $options){
   if($key == '')continue;
   echo "
   <div dojoType='dijit.Dialog' id='course_dialog_year$key' jsId='course_dialog_year$key' title='Courses Year $key' align=center>
   <table>
   <tr><td>Optional</td><td>:<input type=text dojoType='dijit.form.TextBox' value='all' name='O' ></td></tr>
   <tr><td>Compulsory</td><td>:<input type=text dojoType='dijit.form.TextBox' value='all' name='C' ></td></tr>
   $options
   </table>
   <button  dojoType='dijit.form.Button'  onClick='fill_courses($key)'>
   Ok
   </button>
   
   <script type="text/javascript">
   var button = new dijit.form.Button({
          label: \"select\",
      onClick:function(){ 
         show_course_dialog($key);
      }
   });

     dojo.byId(\"td_in_courses_year$key\").appendChild(button.domNode);
   </script>
   </div>
   ";
}

echo "<script type="text/javascript">
function show_course_dialog(key){
   formDlg = dijit.byId('course_dialog_year'+key);
   /*extract values from textarea*/
   var course_year_values=dijit.byId('courses_year'+key).attr('value').replace(/&quot;/g,'\"');
   /*if textarea is blank not try to set values to the dialog*/
   if(course_year_values != ''){
   /*Convert sting of 'course_year'+key textarea into json*/
      var obj = eval('(' + course_year_values + ')');
      /*Set values of the dialog from textarea*/
      formDlg.setValues(obj);
   }
   formDlg.show();
}

function fill_courses(key){
   /*access dialog*/
   var formDlg = dijit.byId('course_dialog_year'+key);
   /*get values from dialog as json string*/
   var value_of_dialog=dojo.toJson(formDlg.getValues(), true);
   /*access textarea*/
   var course_year_textarea=dojo.byId('courses_year'+key);
   /*set value of textarea*/
   course_year_textarea.value=value_of_dialog;
   //course_year_textarea.innerHTML=value_of_dialog;
   formDlg.hide();
}
";

echo "</script>";

?>
