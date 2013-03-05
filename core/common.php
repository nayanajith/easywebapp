<?php
/**
 * Return effective theme
 */
function get_theme(){
   return isset($_SESSION['THEME'])?$_SESSION['THEME']:$GLOBALS['THEME'];
}

/**
 * Return layout
 */

function get_layout_name(){
   return isset($_SESSION['LAYOUT'])?$_SESSION['LAYOUT']:$GLOBALS['LAYOUT'];
}

/**
 * Get help content
 */
function get_help($module=null,$page=null){
   if(is_null($module))$module=MODULE;
   if(is_null($page))$page=PAGE;

   //Get the page name
   global $menu_array;
   $page_label=$menu_array[PAGE];
   if(is_array($page_label)){
      $page_label=$page['label'];
   }
   
   //Get the module name
   $module_label=$GLOBALS['MODULES'][MODULE];
   if(is_array($module_label)){
      $module_label=$module_label['MODULE'];
   }
   
   $help="<div class='help round' id='help' style='height:500px;overflow:auto;padding:10px' ><div>";
   $help.="<div style='padding-top:10px;font-size:20px;font-wight:bold;border-bottom:1px solid silver' >User Guide for the [".$module_label." / ".$page_label."]</div>";
   include_once "markdown.php";
   $help_arr=exec_query("SELECT * FROM ".s_t('user_doc')." WHERE module_id='".$module."' AND page_id='".$page."'",Q_RET_ARRAY);
   foreach($help_arr as $key=>$row){
      $help.=Markdown($row['doc']);
   }

   //If there is a help file created acordance to the page it will also be loaded
   $doc_file=get_doc_file($module,$page);
   if(file_exists($doc_file)){
      $fh=fopen($doc_file,'r');
      $content=fread($fh,filesize($doc_file));
      fclose($fh);
      $help.=Markdown($content);
   }
   $help.="</div>";
   return $help;
}

/**
 * Delete array element by value
 */
function del_by_value(&$arr,$value){
   if(($key = array_search($value, $arr)) !== false) {
       unset($arr[$key]);
   }
}

/**
 * Return the path to the doc file of effective page 
 */
function get_doc_file($module=null,$page=null){
   $doc_ext="_doc.txt";
   if(is_null($module) || is_null($page)){
      return A_MODULES."/".MODULE."/".PAGE.$doc_ext;
   }else{
      return A_MODULES."/".$module."/".$page.$doc_ext;
   }
}


/**
 * Return a session variable values set by set_param() frontend function (javascript) 
 */
function set_param($key,$value){
   $_SESSION[MODULE][PAGE][$key]=$value;
}

/**
 * Store session variable relative to the MODULE and PAGE
 */
function get_param($key){
   if(isset($_SESSION[MODULE][PAGE][$key])){
      return $_SESSION[MODULE][PAGE][$key];
   }else{
      return null;
   }
}

/**
 * Delete session variable relative to the MODULE and PAGE
 */
function del_param($key){
   if(isset($_SESSION[MODULE][PAGE][$key])){
      unset($_SESSION[MODULE][PAGE][$key]);
      return true;
   }else{
      return false;
   }
}

/**
 * Return the primary key of effective form
 */
function get_pri_keys(){
   if(isset($GLOBALS['MODEL']) && isset($GLOBALS['MODEL']['KEYS'])){
      if(isset($GLOBALS['MODEL']['KEYS']['PRI']) && is_array($GLOBALS['MODEL']['KEYS']['PRI'])){
         return $GLOBALS['MODEL']['KEYS']['PRI'][0];
      }elseif(isset($GLOBALS['MODEL']['KEYS']['PRIMARY_KEY'])){
         return $GLOBALS['MODEL']['KEYS']['PRIMARY_KEY'];
      }
   }else{
      return null; 
   }
}

/**
 * Return the effective unique keys
 */
function get_uni_keys(){
   if(isset($GLOBALS['MODEL']) && isset($GLOBALS['MODEL']['KEYS']) && isset($GLOBALS['MODEL']['KEYS']['UNI'])){
      return $GLOBALS['MODEL']['KEYS']['UNI'];
   }else{
      return array(); 
   }
}

/**
 * Return the foreign keys (references) 
 */
function get_for_keys(){
   if(isset($GLOBALS['MODEL']) && isset($GLOBALS['MODEL']['KEYS']) && isset($GLOBALS['MODEL']['KEYS']['FOR'])){
      return $GLOBALS['MODEL']['KEYS']['FOR'];
   }else{
      return array(); 
   }

}

/**
 * layouts html/dojo parameters
 * data-dojo-props='id:"border1-left", region:"left", style:"background-color: #acb386; border: 10px green solid; width: 100px;",
 *       splitter:true, minSize:150, maxSize:250'
 */

if(!isset($_REQUEST['section'])){
   $_SESSION['LAYOUT_PROPERTIES']['app2']=array(
      "MAIN_TOP"     =>array(
         "style"=>array("padding"=>"0px","height"=>"0%"),
         "splitter"=>"false",
      ),
      "MAIN_BOTTOM"  =>array(
         "style"=>array("padding"=>"0px","height"=>"0%","padding-top"=>"5px"),
         "splitter"=>"false",
      ),
      "MAIN_LEFT"    =>array(
         "style"=>array("padding"=>"0px","width"=>"40%","padding-right"=>"5px"),
         "splitter"=>"false",
      ),
      "MAIN_RIGHT"   =>array(
         "style"=>array("padding"=>"0px","width"=>"60%"),
         "splitter"=>"false",
         //"minSize"=>"0",
         //"maxSize"=>"850",
      ),
   );
}

function set_layout_property($layout='app2',$section,$p1,$p2,$p3=null){
   if(!is_null($p3)){
      $_SESSION['LAYOUT_PROPERTIES'][$layout][$section][$p1][$p2]=$p3;
   }else{
      $_SESSION['LAYOUT_PROPERTIES'][$layout][$section][$p1]=$p2;
   }
}

/**
 * return totla layout as a json
 */
function get_layout($layout='app2'){
   return json_encode($_SESSION['LAYOUT_PROPERTIES'][$layout]);
}

/**
 * Return selected property of the layout
 */
function get_layout_property($layout='app2',$section='MAIN_TOP',$key=null,$key2=null){
   $out="";
   if(!is_null($key2)){
      return $key2."='".$_SESSION['LAYOUT_PROPERTIES'][$layout][$section][$key][$key2]."' ";
   }elseif(!is_null($key)){
      if(is_array($_SESSION['LAYOUT_PROPERTIES'][$layout][$section][$key]) && in_array(strtolower($key) ,array('style'))){
         $out=$key."'";
         foreach($_SESSION['LAYOUT_PROPERTIES'][$layout][$section][$key] as $key_ => $value_){
            $out.=$key_.":".$value_.";";
         }
         $out.="'";
         return $out;
      }else{
         return $key."='".$_SESSION['LAYOUT_PROPERTIES'][$layout][$section][$key]."' ";
      }
   }else{
      foreach($_SESSION['LAYOUT_PROPERTIES'][$layout][$section] as $key => $value){
         $out.=$key."='";
         if(is_array($_SESSION['LAYOUT_PROPERTIES'][$layout][$section][$key]) && in_array(strtolower($key) ,array('style'))){
            foreach($_SESSION['LAYOUT_PROPERTIES'][$layout][$section][$key] as $key_ => $value_){
               $out.=$key_.":".$value_.";";
            }
         }else{
            $out.=$_SESSION['LAYOUT_PROPERTIES'][$layout][$section][$key];
         }
         return $out.="' ";
      }
      return $out;
   }
}

/**
 * wrapper for isset() function which will return value if is set else return null
 * TODO
 */
/**
 * Return effective system table name
 */
function s_t($key){
   return system_table($key);
}
function s_table($key){
   return system_table($key);
}
function system_table($key){
   if(isset($GLOBALS['S_TABLES']) && isset($GLOBALS['S_TABLES'][$key])){
      return $GLOBALS['S_TABLES'][$key];
   }else{
      return null;
   }
}

/**
 * Returen Module system table
 */
function m_s_t($key){
   return module_system_table($key);
}
function m_s_table($key){
   return module_system_tabl($key);
}
function module_system_table($key){
   if(isset($GLOBALS['MOD_S_TABLES']) && isset($GLOBALS['MOD_S_TABLES'][$key])){
      return $GLOBALS['MOD_S_TABLES'][$key];
   }else{
      return null;
   }
}

/**
 * Return effective program table name
 */
function p_t($key){
   return program_table($key);
}
function p_table($key){
   return program_table($key);
}
function program_table($key){
   if(isset($GLOBALS['P_TABLES']) && isset($GLOBALS['P_TABLES'][$key])){
      return $GLOBALS['P_TABLES'][$key];
   }else{
      return null;
   }
}

/**
 * Return module program tables
 */

function m_p_t($key){
   return module_program_table($key);
}
function m_p_table($key){
   return module_program_table($key);
}
function module_program_table($key){
   if(isset($GLOBALS['MOD_P_TABLES']) && isset($GLOBALS['MOD_P_TABLES'][$key])){
      return $GLOBALS['MOD_P_TABLES'][$key];
   }else{
      return null;
   }
}


/**
 * Return page name for a page_id
 */
function page_name($page_id){
   $page_name=$GLOBALS['MENU_ARRAY'][$page_id];
   if(is_array($page_name) && isset($page_name['label'])){
      $page_name=$page_name['label'];
   }
   return $page_name;
}

/**
 * Return module name for a module_id
 */
function module_name($module_id){
   $module_name=$GLOBALS['MODULES'][$module_id];
   if(is_array($module_name)){
      $module_name=$module_name['MODULE'];
   }
   return $module_name;
}
/*
 * Print html top
 */
function print_top($title=null){
   echo "
      <!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>
      <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' >
      <html>
         <head>
            <title>$title</title>
            <style type='text/css'>
               @import '".W_ROOT."/css/common_css.php';
            </style>
           <link rel='shortcut icon' href='".W_ROOT."/img/favicon.ico'type='image/x-icon' >
           <script src='".W_ROOT."/js/common_js.php' type='text/javascript'></script>
         </head>
         <body>
      ";
}

/**
Bottom of the print html
*/
function print_bottom(){
   echo "
            <script type='text/javascript'> 
               setTimeout('print()',2000);
            </script>
         </body>
         </html>
      ";
}

/*
 * Adopt print output into standard html
 */
function gen_print_html($content,$title){
   return "
      <!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>
      <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' >
      <html>
         <head>
            <title>$title</title>
            <style type='text/css'>
               @import '".W_ROOT."/css/common_css.php';
            </style>
           <link rel='shortcut icon' href='".W_ROOT."/img/favicon.ico'type='image/x-icon' >
           <script src='".W_ROOT."/js/common_js.php' type='text/javascript'></script>
         </head>
         <body>
            <center><h2>$title</h2></center>
            $content
            <script type='text/javascript'> 
               setTimeout('print()',2000);
            </script>
         </body>
         </html>
      ";
}
/**
 * $filter_array=array('id1','id2') -> and id1='val_id1' and id2='val_id2'
 * $filter_ids: array of filter ides
 * $array: array which the values of the filter ids are stored (default is $_SESSION[MODULE][PAGE])
 * $start_and: starts the filter with AND
 * $must: Filter must set to acquire values for the given widget 
 */
function gen_and_filter($filter_ids,$array=null,$start_and=false,$must=false){
   if(is_null($array) && isset($_SESSION[MODULE][PAGE])){
      $array=$_SESSION[MODULE][PAGE];
   }elseif(is_null($array)){
      return "";
   }

   $and="";
   $filter="";
   if(!is_array($filter_ids)){
      $id=$filter_ids;
      if(isset($array[$id])&&!is_null($array[$id])&&$array[$id]!='_ALL_'){
         if($array[$id]=='NULL'){
            $filter="(ISNULL($id) OR $id='')";
         }else{
            $filter="$id='".$array[$id]."'";
         }
      }elseif($must){
         $filter="($id='' OR ISNULL($rid))";
      }
   }else{
      foreach($filter_ids as $id){
         if(isset($array[$id])&&!is_null($array[$id])&&$array[$id]!='_ALL_'){
            if($array[$id]=='NULL'){
               $filter.= " $and (ISNULL($id) OR $id='')";
            }else{
               $filter.= " $and $id='".$array[$id]."'";
            }
            $and="AND";
         }elseif($must){
            $filter.= " $and ($id='' OR ISNULL($id))";
            $and="AND";
         }
      }
   }

   //Prepend and if requested
   if($start_and && $filter!=''){
      $filter="AND ".$filter;
   }

   return $filter;
}



//Keep the filter key=value paires for future use this array may initialized in model_class.php
//$_SESSION[PAGE]['FILTER_ARRAY']=array();
//
//Filter exceptions are stored in FILTER_ARRAY_EXP which will override the default values
//$_SESSION[PAGE]['FILTER_ARRAY_EXP']=array();

/**
 * Regenerate the filter with customizations
 */
function get_filter($table_as=null,$start_and=false){
   if(isset($_SESSION[MODULE][PAGE]['FILTER_ARRAY']) && is_array($_SESSION[MODULE][PAGE]['FILTER_ARRAY']) && sizeof($_SESSION[MODULE][PAGE]['FILTER_ARRAY']) > 0){ 
   }else{
      return null;
   }   

   //return if request with the table name prefix
   if(!is_null($table_as)){
      $table_as=$table_as.".";
   }   

   $filter="";
   $and="";
   foreach($_SESSION[MODULE][PAGE]['FILTER_ARRAY'] as $key => $value){
      //override the default values with the exceptions
      if(isset($_SESSION[MODULE][PAGE]['FILTER_ARRAY_EXP']) && isset($_SESSION[MODULE][PAGE]['FILTER_ARRAY_EXP'][$key])){
         $value=$_SESSION[MODULE][PAGE]['FILTER_ARRAY_EXP'][$key]; 
      }elseif(in_array($value,array('~','NULL'))){//'~' is considered as null value request
         $value=$table_as." (ISNULL(`".$key."`) OR `".$key."`='') ";
      }else{
         if(defined('FILTER_AUTO') && FILTER_AUTO=='YES'){
            $value=$table_as."`".$key."` LIKE '%".$value."%'"; 
         }else{
            $value=$table_as."`".$key."` LIKE '".$value."'"; 
         }
      }   

      $filter.=$and.$value;
      $and=' AND ';
   }   

   //Filter will start with AND to join with the front part of the query
   if($start_and){
      return " AND ".$filter; 
   }
   return $filter; 
}

/**
 * Delete temporary filter for the submitted values
 */

function del_filter($table_as=null){
   if(isset($_SESSION[MODULE][PAGE]['FILTER_ARRAY'])){
      unset($_SESSION[MODULE][PAGE]['FILTER']);
      unset($_SESSION[MODULE][PAGE]['FILTER_ARRAY']);
   }
}

/**
 * Add sessionwide filter
 */
function add_filter(){

  //Reset the global filter array
   $_SESSION[MODULE][PAGE]['FILTER_ARRAY']=array();

   foreach($GLOBALS['MODEL']['FORM'] as $key => $arr){
      //if($key != get_pri_keys() && isset($_REQUEST[$key]) && $_REQUEST[$key] != '' && $_REQUEST[$key] != 'NULL' && $_REQUEST[$key] != '_ALL_' ){
      if($key != get_pri_keys() && isset($_REQUEST[$key]) && $_REQUEST[$key] != '' && $_REQUEST[$key] != '_ALL_' ){

         //Handle checkboxes (on -> 1)
         if($arr['dojoType']=='dijit.form.CheckBox'){
            if(in_array($_REQUEST[$key],array('on','true','1'))){
         	   $_REQUEST[$key]="1";
            }else{
         	   $_REQUEST[$key]="0";
            }
			}

         //Handle dates 
         if($arr['dojoType']=='dijit.form.DateTextBox'){
         	$_REQUEST[$key]=$_REQUEST[$key]."%";
			}

         //Handle combobx visualkey rid problem
         if($arr['dojoType']=='dijit.form.ComboBox' && isset($arr['key_sql'])){
            $res=exec_query(sprintf($arr['key_sql'],$_REQUEST[$key]),Q_RET_ARRAY);
            if(isset($res[0])){
               $_REQUEST[$key]=$res[0][$arr['ref_key']];
            }
         }

         $_SESSION[MODULE][PAGE]['FILTER_ARRAY'][$key]=$_REQUEST[$key];
      }
   }
}

//Array to keep the view entries before puting in VIEW array 
$GLOBALS['MODEL']=array(
   'KEYS'   =>array(),
   'FORM'   =>array(),
   'GRIDS'  =>array(),
   'TOOLBAR'=>array(),
   'NOTIFY'=>array(),
   'CALLBACKS'=>array(),
);

/**
 * Add value to model add a value to a given lear of the model tree
 */
function set_mdl_property($path,$value){
   return set_property($GLOBALS['MODEL'],$path,$value);
}

/**
 * Get value from model
 * Brows thour the array and return the value if available else return null
 * $path is an arry of nodes 
 */
function get_mdl_property($path){
   return get_property($GLOBALS['MODEL'],$path);
}

/**
 * Set property from the model array
 */
function set_property(&$arr,$path,$value){
   if(!is_array($path)){
      $path=explode('.',$path);
   }
   foreach($path as $key){
      //Create new array element if not available
      if(!isset($arr[$key])){
         $arr[$key]=null;
      }
      $arr=&$arr[$key];
   }
   $arr=$value;
}

/**
 * Get property from the (model,view) array
 */
function get_property($arr,$path){
   if(!is_array($path)){
      $path=explode('.',$path);
   }

   foreach($path as $key){
      if(!isset($arr[$key])){
         //If no property found in the given path return null
         return null;
      }else{
         $arr=$arr[$key];
      }
   }
   return $arr;
}

/**
 * call callback functions and store returned results
 * example callback array:
 'CALLBACKS'=>array(
      "add_record"=>array(
         "OK"     =>array(
            "func"=>"test_func",
            "vars"=>array(1,2),
            "status"=>false,
            "return"=>null
         ),
         "ERROR"  =>null,
      ),
      "modify_record"=>array(
         "OK"     =>null,
         "ERROR"  =>null,
      ),
      "delete_record"=>array(
         "OK"     =>array(
            "func"=>"test_func",
            "vars"=>array(1,2),
            "status"=>false,
            "return"=>null
         ),
         "ERROR"  =>null,
      )
   ),
 */
function callback($caller,$status,$func_array=null){
   if(is_null($func_array)){
      //if no callback arrays set return 
      if(is_null(get_mdl_property(array('CALLBACKS'))))return;
      $func_array=get_mdl_property(array('CALLBACKS',$caller,$status));
      if(is_null($func_array))return;
   }

   //Callback the function and set returning value back in the array as return
   set_mdl_property(
      array('CALLBACKS',$caller,$status,'return'),
      call_user_func_array($func_array['func'],$func_array['vars'])
   );

   //Set callback function status as true to denote it is executed
   set_mdl_property(
      array('CALLBACKS',$caller,$status,'status'),
      true
   );
} 

//Array to keep the view entries before puting in VIEW array 
$GLOBALS['PREVIEW']=array(
   'KEYS'   =>array(),
   'FORM'   =>array(),
   'GRIDS'  =>array(),
   'TOOLBAR'=>array(),
   'NOTIFY' =>array(),
);

/**
 * Set value to preview,up to 3 levels can be set
 */
function set_pviw_property($path,$value){
   set_property($GLOBALS['PREVIEW'],$path,$value);
}

/**
 * Get array of elements from preview,up to 3 levels can be get
 */
function get_pviw_property($path){
   //Brows thour the array and return the value if available else return null
   return get_property($GLOBALS['PREVIEW'],$path);
}




/*--create and fill view global array which contains all parts of the fintend-*/
if(!isset($_REQUEST['section'])){
   $_SESSION['VIEW']=array(
      'CSS'          =>'',
      'JS'           =>'',
      'DYNAMIC_JS'   =>'',
      'LOADING'      =>'',
      'LOGIN'        =>'',
      'PROGRAM'      =>'',
      'BREADCRUMB'   =>'',
      'NAVIGATOR'    =>'',
      'MAIN_TOP'     =>'',
      'MAIN_LEFT'    =>'',
      'MAIN_RIGHT'   =>'',
      'MAIN_BOTTOM'  =>'',
      'NOTIFY'       =>'',
      'MENUBAR'      =>'',
      'TOOLBAR_TR'   =>'',
      'TOOLBAR_TL'   =>'',
      'TOOLBAR'      =>'',
      'STATUSBAR'    =>'',
      'FOOTER'       =>'',
      'DIALOG'       =>'',
      //Custom view section array
      'CUSTOM'       =>array()
   );
}

/*
 * View_id : one of the ids in above array
 * contet   : any html/css/js content or a include file which will generate any of the contet
 * before : true/false
 */

function add_to_view($view_id,$content,$before=false){
   //IF the contet is a file then include and get the output to array using ob_func
   if(isset($_SESSION['VIEW'][$view_id])){
      if(is_file($content)){
         ob_start();
         include $content;
         $content=ob_get_contents();
         if($before){
            $_SESSION['VIEW'][$view_id] = $content.$_SESSION['VIEW'][$view_id];
         }else{
            $_SESSION['VIEW'][$view_id] .= $content;
         }
         ob_end_clean();
      }elseif(!is_null($content) || $content != ''){
         if($before){
            $_SESSION['VIEW'][$view_id] = $content.$_SESSION['VIEW'][$view_id];
         }else{
            $_SESSION['VIEW'][$view_id] .= $content;
         }
      }
   }else{
      return "key[$view_id] error!"; 
   }
}

/**
 * Return content from view
 */
function get_from_view($view_id,$clear=true){
   $section=$_SESSION['VIEW'][$view_id];
   if($clear){
      clear_view($view_id);
   }
   return $section;
}

/*
 * Add content to custom array which is consists of user defined view ids
 * View_id : one of the ids in above array
 * contet   : any html/css/js content or a include file which will generate any of the contet
 * before : true/false
 */

function add_to_cview($view_id,$content,$before=false){
   //IF the contet is a file then include and get the output to array using ob_func
   if(!isset($_SESSION['VIEW']['CUSTOM'][$view_id])){
      $_SESSION['VIEW']['CUSTOM'][$view_id]='';
   }
   if(is_file($content)){
      ob_start();
      include $content;
      $content=ob_get_contents();
      if($before){
         $_SESSION['VIEW']['CUSTOM'][$view_id] = $content.$_SESSION['VIEW']['CUSTOM'][$view_id];
      }else{
         $_SESSION['VIEW']['CUSTOM'][$view_id] .= $content;
      }
      ob_end_clean();
   }elseif(!is_null($content) || $content != ''){
      if($before){
         $_SESSION['VIEW']['CUSTOM'][$view_id] = $content.$_SESSION['VIEW']['CUSTOM'][$view_id];
      }else{
         $_SESSION['VIEW']['CUSTOM'][$view_id] .= $content;
      }
   }
}

/**
 * Return content from custom view
 */
function get_from_cview($view_id,$clear=true){
   $section='';
   if(isset($_SESSION['VIEW']['CUSTOM'][$view_id])){
      $section=$_SESSION['VIEW']['CUSTOM'][$view_id];
      if($clear){
         clear_cview($view_id);
      }
   }
   return $section;
}

/**
 * Clear the view with blank
 */
function clear_cview($view_id=null){
   //If $view_id is null reset all
   if(is_null($view_id)){
      foreach($_SESSION['VIEW']['CUSTOM'] as $vid => $bla){
         $_SESSION['VIEW']['CUSTOM'][$vid]='';
      }
   }else{
      if(isset($_SESSION['VIEW']['CUSTOM'][$view_id])){
         $_SESSION['VIEW']['CUSTOM'][$view_id] = '';
      }else{
         return "key[$view_id] error!"; 
      }
   }
}


/**
 * Clear the view with blank
 */
function clear_view($view_id=null){
   //If $view_id is null reset all
   if(is_null($view_id)){
      foreach($_SESSION['VIEW'] as $vid => $bla){
         $_SESSION['VIEW'][$vid]='';
      }
   }else{
      if(isset($_SESSION['VIEW'][$view_id])){
         $_SESSION['VIEW'][$view_id] = '';
      }else{
         return "key[$view_id] error!"; 
      }
   }
}


/**
 * Wrapper function to make it easy to add a contet to each section of the view
 */
function add_to_main($content,$before=false){
   add_to_main_top($content,$before);
}
function add_to_main_top($content,$before=false){
   add_to_view('MAIN_TOP',$content,$before);
}
function add_to_main_left($content,$before=false){
   add_to_view('MAIN_LEFT',$content,$before);
}
function add_to_main_bottom($content,$before=false){
   add_to_view('MAIN_BOTTOM',$content,$before);
}
function add_to_main_right($content,$before=false){
   add_to_view('MAIN_RIGHT',$content,$before);
}
function add_to_css($content,$before=false){
   add_to_view('CSS',$content,$before);
}
function add_to_js($content,$before=false){
   add_to_view('JS',$content,$before);
}
function js($content,$before=false){
   add_to_dynamic_js($content,$before);
}
function add_to_dynamic_js($content,$before=false){
   add_to_view('DYNAMIC_JS',$content,$before);
}
function add_to_loading($content,$before=false){
   add_to_view('LOADING',$content,$before);
}
function add_to_login($content,$before=false){
   add_to_view('LOGIN',$content,$before);
}
function add_to_program($content,$before=false){
   add_to_view('PROGRAM',$content,$before);
}
function add_to_breadcrumb($content,$before=false){
   add_to_view('BREADCRUMB',$content,$before);
}
function add_to_navigator($content,$before=false){
   add_to_view('NAVIGATOR',$content,$before);
}
function add_to_notify($content,$before=false){
   add_to_view('NOTIFY',$content,$before);
}
function add_to_menubar($content,$before=false){
   add_to_view('MENUBAR',$content,$before);
}
function add_to_toolbar($content,$before=false){
   add_to_view('TOOLBAR',$content,$before);
}
function add_to_toolbar_tl($content,$before=false){
   add_to_view('TOOLBAR_TL',$content,$before);
}
function add_to_toolbar_tr($content,$before=false){
   add_to_view('TOOLBAR_TR',$content,$before);
}
function add_to_statusbar($content,$before=false){
   add_to_view('STATUSBAR',$content,$before);
}
function add_to_footer($content,$before=false){
   add_to_view('FOOTER',$content,$before);
}
function add_to_dialog($content,$before=false){
   add_to_view('DIALOG',$content,$before);
}

/**
 * Wrapper function to make it easy to get contet from the view
 */

function get_main_top($clear=true){
   return get_from_view('MAIN_TOP',$clear);
}
function get_main_left($clear=true){
   return get_from_view('MAIN_LEFT',$clear);
}
function get_main_bottom($clear=true){
   return get_from_view('MAIN_BOTTOM',$clear);
}
function get_main_right($clear=true){
   return get_from_view('MAIN_RIGHT',$clear);
}
function get_css($clear=true){
   return get_from_view('CSS',$clear);
}
function get_js($clear=true){
   return get_from_view('JS',$clear);
}
function get_dynamic_js($clear=true){
   return get_from_view('DYNAMIC_JS',$clear);
}
function get_loading($clear=true){
   return get_from_view('LOADING',$clear);
}
function get_login($clear=true){
   return get_from_view('LOGIN',$clear);
}
function get_program($clear=true){
   return get_from_view('PROGRAM',$clear);
}
function get_breadcrumb($clear=true){
   return get_from_view('BREADCRUMB',$clear);
}
function get_navigator($clear=true){
   return get_from_view('NAVIGATOR',$clear);
}
function get_notify($clear=true){
   return get_from_view('NOTIFY',$clear);
}
function get_menubar($clear=true){
   return get_from_view('MENUBAR',$clear);
}
function get_toolbar($clear=true){
   return get_from_view('TOOLBAR',$clear);
}
function get_toolbar_tl($clear=true){
   return get_from_view('TOOLBAR_TL',$clear);
}
function get_toolbar_tr($clear=true){
   return get_from_view('TOOLBAR_TR',$clear);
}
function get_statusbar($clear=true){
   return get_from_view('STATUSBAR',$clear);
}
function get_footer($clear=true){
   return get_from_view('FOOTER',$clear);
}
function get_dialog($clear=true){
   return get_from_view('DIALOG',$clear);
}


/**
 * Wrapper function to make it easy to clear each section
 */
function clear_main(){
   clear_view('MAIN_TOP');
   clear_view('MAIN_LEFT');
   clear_view('MAIN_RIGHT');
   clear_view('MAIN_BOTTOM');
}
function clear_main_left(){
   clear_view('MAIN_LEFT');
}
function clear_main_top(){
   clear_view('MAIN_TOP');
}
function clear_main_bottom(){
   clear_view('MAIN_BOTTOM');
}
function clear_main_right(){
   clear_view('MAIN_RIGHT');
}
function clear_css(){
   clear_view('CSS');
}
function clear_js(){
   clear_view('JS');
}
function clear_loading(){
   clear_view('LOADING');
}
function clear_login(){
   clear_view('LOGIN');
}
function clear_program(){
   clear_view('PROGRAM');
}
function clear_breadcrumb(){
   clear_view('BREADCRUMB');
}
function clear_navigator(){
   clear_view('NAVIGATOR');
}
function clear_notify(){
   clear_view('NOTIFY');
}
function clear_menubar(){
   clear_view('MENUBAR');
}
function clear_toolbar(){
   clear_view('TOOLBAR');
}
function clear_toolbar_tl(){
   clear_view('TOOLBAR_TL');
}
function clear_toolbar_tr(){
   clear_view('TOOLBAR_TR');
}
function clear_statusbar(){
   clear_view('STATUSBAR');
}
function clear_footer(){
   clear_view('FOOTER');
}

/**
 * Add return the field for the given field_id from $GLOBALS['PREVIEW']['FORM']
 */
function get_field($field_id){
   return get_pviw_property(array('FORM',$field_id,'field'));
}

/**
 * Add return the label for the given field_id from $GLOBALS['PREVIEW']['FORM']
 */
function get_label($field_id){
   return get_pviw_property(array('FORM',$field_id,'label'));
}

/**
 * Different headers are required by files generation
 * $file_name : name of the file with the extention;
 */
function set_file_header($file_name){
   $ext=explode('.',$file_name);
   $ext=$ext[1];
   $content_type=null;
   switch($ext){
   case 'csv':
      //$content_type="application/octet-stream";
      //$content_type='application/vnd.ms-excel';
      $content_type='txt/csv';
   break;
   case 'json':
      $content_type='application/json';
   break;
   case 'pdf':
      $content_type='application/pdf';
   break;
   case 'jpg':
      $content_type='image/jpg';
   break;
   case 'png':
      $content_type='image/png';
   break;
   case 'js':
      $content_type='text/javascript';
   break;
   default:
      $content_type='text/json';
   break;
   }

  header('Content-Type:'.$content_type.' charset=utf-8' );
  header('Content-Disposition: attachment; filename='.$file_name);
  //header("Content-type: application/octet-stream");
  //header("Content-Disposition: attachment; filename=your_desired_name.xls");
  //header("Content-Length: ".@filesize($file));
  header("Content-Transfer-Encoding: binary");
  header("Pragma: no-cache");
  header("Expires: 0");
}




/*Return staus as json for XHR request or io.iframe request */
/*
Possible status
/**
@param status_code: OK, ERROR, NOT_DEFINED 
@param info: information about the status
*/
function return_status_json($status,$info){
   $status=strtoupper($status);
   if($status == 'OK' || $status == 'ERROR'){
      echo "{'status_code':'$status','info':'$info'}";
   }else{
      echo "{'status_code':'NOT_DEFINED','info':'$info','nstatus':'$status'}";
   }
}


/*Load dojo module which required to be loaded to parse gui*/
/*it will create an array of modules which will used to generate the javascript code required at the end */
$dojo_required=array();
function dojo_require($module){
   global  $dojo_required;
   if(!in_array($module,$dojo_required)){
      $dojo_required[]=$module;
   }
}

//simplyfied version of dojo_require
function d_r($module){
   dojo_require($module);
}

//dojo have set of icons which can used with buttons and so on
$dijitIcons=array(
   "Save",
   "Print",
   "Cut",
   "Copy",
   "Clear",
   "Delete",
   "Undo",
   "Edit",
   "NewTask",
   "EditTask",
   "EditProperty",
   "Task",
   "Filter",
   "Configure",
   "Search",
   "Application",
   "Bookmark",
   "Chart",
   "Connector",
   "Database",
   "Documents",
   "Mail",
   "File",
   "Function",
   "Key",
   "Package",
   "Sample",
   "Table",
   "Users",
   "FolderClosed",
   "FolderOpen"
);

//Editor icons
$dijitEditorIcons=array(
   "Sep",
   "Save",
   "Print",
   "Cut",
   "Copy",
   "Paste",
   "Delete",
   "Cancel",
   "Undo",
   "Redo",
   "SelectAll",
   "Bold",
   "Italic",
   "Underline",
   "Strikethrough",
   "Superscript",
   "Subscript",
   "JustifyCenter",
   "JustifyFull",
   "JustifyLeft",
   "JustifyRight",
   "Indent",
   "Outdent",
   "ListBulletIndent",
   "ListBulletOutdent",
   "ListNumIndent",
   "ListNumOutdent",
   "TabIndent",
   "LeftToRight",
   "RightToLeft",
   "ToggleDir",
   "BackColor",
   "ForeColor",
   "HiliteColor",
   "NewPage",
   "InsertImage",
   "InsertTable",
   "Space",
   "InsertHorizontalRule",
   "InsertOrderedList",
   "InsertUnorderedList",
   "CreateLink",
   "Unlink",
   "ViewSource",
   "RemoveFormat",
   "FullScreen",
   "Wikiword"
);

/**
 * return the css classes which represent the relevent button icon
 */
function get_icon_class($name){
   global $dijitEditorIcons;
   global $dijitIcons;
   $name=ucfirst($name); 
   if(array_search($name,$dijitIcons)){
      return 'dijitIcon dijitIcon'.$name;
   }elseif(array_search($name,$dijitEditorIcons)){
      return 'dijitEditorIcon dijitEditorIcon'.$name;   
   }else{
      return 'dijitIcon dijitIconFunction';   
   }
}

/*vefiry captcha by matching code submitted by the user  and avail in session*/
function verify_captcha($custom_param=null){
   if(isset($_SESSION['captcha'])){
      $user_code="";
      if($custom_param != ''){
         $user_code=$_REQUEST[$custom_param];
      }else{
         $user_code=$_REQUEST['captcha'];
      }
      
      if($user_code == $_SESSION['captcha']){
         unset($_SESSION['captcha']);
         return true;   
      }else{
         unset($_SESSION['captcha']);
         return false;   
      }
      return false;
   }else{
      return true;   
   }
}

/**
 * return a commen list array with title according to the common list_name
 */
function get_common_list($list_name,$no_title=false){
   if($GLOBALS['DATA'])return array(); //Do not resolv common lists when there is a data request
   $arr=exec_query("SELECT base_key,base_value FROM ".s_t('base_data')." WHERE base_class='LIST' AND base_key='".$list_name."'",Q_RET_ARRAY);
   if(isset($arr[0])){
      if($no_title){
         return json_decode($arr[0]['base_value'],true);
      }else{
         return array(
            'title'  =>style_text($arr[0]['base_key']),
            'list'   =>json_decode($arr[0]['base_value'],true)
         );
      }
   }
}

/**
 * Check if the array is associtated array
 */
function is_assoc_array($arr){
   if(is_array($arr) && sizeof($arr)>0){
      return array_keys($arr) !== range(0, count($arr) - 1);
   }else{
      return false;
   }
}


/**
 * Generate a Select box for a given array of values and return the html
 * arr: associative array with key=>value
 */
function gen_select_inner($arr,$label=null,$null_select=false){
   //validation  $arr must be an array
   if(!is_array($arr) || $GLOBALS['DATA']){
      return null;
   }

   $select="<option value='_ALL_'>-all-</option>\n<option value='NULL'>-none-</option>\n";
   if($null_select===true){
      $select='';
   }elseif($null_select !== true && $null_select != ""){
      $select="<option value='_ALL_'>-all-</option>\n<option value='NULL'>$null_select</option>\n";
   }

   if(is_assoc_array($arr)){
      //Direct compatibility with  returning array of exec_query
      if(is_array($arr[key($arr)])){
         //case: exec_query("SELECT rid,batch_id FROM ".p_t('batch'),Q_RET_ARRAY,null,'rid');
         if($label != null){
            foreach($arr as $key=>$value ){
               $select.="<option value=\"$key\">".$value[$label]."</option>\n";
            }
         }else{
            //case: exec_query("SELECT batch_id FROM ".p_t('batch'),Q_RET_ARRAY,null,'batch_id');
            foreach($arr as $key=>$value ){
               $select.="<option value=\"$key\">$key</option>\n";
            }
         }
      }else{
         //Associative array with ke=>value
         foreach($arr as $key=>$value ){
            $select.="<option value=\"$key\">$value</option>\n";
         }
      }
   }else{
      //1D array with values
      foreach($arr as $value ){
         $select.="<option value=\"$value\">$value</option>\n";
      }
   }
   return $select;
}


/*
Return prefix url
default -> base url with module, page and program
2 -> with all current key,value pairs 
*/
define('NO_FILTER','3');
function gen_url($module=null,$page=null,$program=null){
   if(is_null($program) && PROGRAM != "" )$program=PROGRAM;
   if(is_null($module))$module=MODULE;
   if(is_null($page))$page=PAGE;

   if(!is_null($program) || $program != ''){
      $program="/".$program;
   }else{
      $program="";
   }
   //return W_ROOT."/".$GLOBALS['PAGE_GEN']."/".$module."/".$page.$program."?";
   return W_ROOT."/".$GLOBALS['PAGE_GEN']."?";
}



/*
 * XML marks and detail file paths
 */
function xml_marks(){
   return TMP.$_SESSION['username'].$_SESSION['password'].$GLOBALS['xml_marks'];
}
function xml_detail(){
   return TMP.$_SESSION['username'].$_SESSION['password'].$GLOBALS['xml_detail'];
}


/*
 * Detect Internet Explorer
 */
function is_msie() {
   $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
   if (strstr($user_agent, 'MSIE') != false) {
      return true;
   }
   return false;
}

/*
 * Detect crome browser
 */
function is_chrome() {
   $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
   if (strstr($user_agent, 'Chrome') != false) {
      return true;
   }
   return false;
}

/*
 * Detect opera browser
 */
function is_opera() {
   $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
   if (strstr($user_agent, 'Opera') != false) {
      return true;
   }
   return false;
}


/*
 * Drow information box with given information
 */
function drow_box($content, $title, $color, $width) {

   if ($width == 0)
   $width = "";
   elseif ($width == '%')
   $width = "width:100%";
   else
   $width = "width:".$width."px";

   $height = null;
   if (is_msie()) {
      $height = 'height:100px;';
   }
   echo "<div class='round' style='border:1px solid silver;min-width:50px; ".$width."; ".$height.";position:relative;background-color:".$color.";z-index:1'>";
   if ($title) {
      echo "<div class=menutitlebar style='background:#C9D7F1;'>".$title."</div>";
   }
   echo "<div style='padding:7px;color:gray'>";
   echo "$content";
   echo "</div>";
   echo "</div>";

}

/*
 * hover for msie
 */
function msie_hover($ht, $hb, $nt, $nb, $eid) {
   if (isMsie()) {
      return "id=".$eid." style='color:".$nt.";background-color:".$nb.";' onmouseover='".$eid.".style.color=\"".$ht."\"; ".$eid.".style.backgroundColor=\"".$hb."\"' onmouseout='".$eid.".style.color=\"".$nt."\"; ".$eid.".style.backgroundColor=\"".$nb."\"'";
   }
}

/*
 * Style table names of database
 */
function style_text($ROW_TEXT) {
   return str_replace("_", " ", ucfirst(strtolower($ROW_TEXT)));
}

//-----------------------------------------------------------
/**
 * generic array waker function walk any function on the array
 */

//walker function holder array
$GLOBALS['walker']="";

/**
 * walker walks a function over an array
 * $array : array to walk
 * $function : name of the function
 * $var: first variables to input the function
 */
function array_walk_(&$array,$function,$var=array()){
   $GLOBALS['walker']=$function;
   array_walk($array,'walk_helper',$var);
}

/**
 * Helper function for the walker
 */
function walk_helper(&$item,$key,$var=null){
   $var[]=$item;
   $item=call_user_func_array($GLOBALS['walker'],$var);
}

//-------------------------------------------------------------

/**
 * wrapper function for style_text function to insert in array walk 
 */
function walk_style_text(&$item,$key,$var=null){
   $item=style_text($item);
}

/*
 * Log a message in log file 
 */

function log_msg($msg=null,$level=null,$file=null){
   if(LOG_ENABLED == 'NO')return;

   //Set from Global log level if $level is not set
   if(defined('LOG_LEVEL') && is_null($level)){
      $level=LOG_LEVEL;
   }

   $date_time=date("d-M-Y h:i:s");

   //log more information of the callee function 
   $bt="";
   if(!is_null($level) && $level > 0 ){
      $bt_arr  =debug_backtrace();
      //TODO: get the correct array to extract information
      $i=1;
      if(isset($bt_arr[2])){
         $i=2;
      }
      switch($level){
      case 1:
         $class   ="";
         if(isset($bt_arr[$i]['class'])){
            $class   =$bt_arr[$i]['class'].'.';
         }
         $bt="[".$class.";".$bt_arr[$i]['function'].";".$bt_arr[$i-1]['line']."]";
      break;
      case 2:
         $class   ="";
         if(isset($bt_arr[$i]['class'])){
            $class   =$bt_arr[$i]['class'].'.';
         }
         $bt="[".$bt_arr[$i-1]['file'].";".$class.";".$bt_arr[$i]['function'].";".$bt_arr[$i-1]['line']."]";
      break;
      case 3:
         $class   ="";
         if(isset($bt_arr[$i]['class'])){
            $class   =$bt_arr[$i]['class'].'.';
         }
         $args="";
         if(isset($bt_arr[$i]['args'])){
            $args=implode(',',$bt_arr[$i]['args']);
         }
         $bt="[".$bt_arr[$i-1]['file'].";".$class.";".$bt_arr[$i]['function'].";".$bt_arr[$i]['line'].";".$args."]";
      break;
      case 4:
         @ob_start();
         debug_print_backtrace();
         $bt = @ob_get_contents();
         @ob_end_clean();
      break;
      default:
      break;
      }
   }

   //Log file
   $log_file=LOG;
   //custom log file
   if(!is_null($file)){
      $log_file=A_ROOT."/".$file;
   }

   $file_handler =null;
   if(file_exists($log_file)){
      $file_handler = fopen($log_file, 'a');
   }else{
      $file_handler = fopen($log_file, 'w');
   }

   //log array content if msg is an array
   if(is_array($msg)){
      @ob_start();
      @print_r($msg);
      $msg = @ob_get_contents();
      @ob_end_clean();
   }

   fwrite($file_handler, "[$date_time]$bt :$msg\n");
   fclose($file_handler);
}


/*
 * Print Header of the reports
 */
function print_header($title){
   echo "
<body style='background:silver;'>
<div align=center class=a4stat >
<br><table>
<tr><td align=right><img src='".$GLOBALS['logo']."' height=60 ></td>
<td><h3>".TITLE_LONG."</h3></td></tr>
<tr><td colspan=2 align=center><h4>$title</h4></td></tr></table><hr>";   
}

/*
 * Print Footer of the reports
 */
function print_footer(){
   echo "<hr><h4>".date('D jS \of F Y')."</h4></div></body></html>";
}

//reg no format YYSSSSSC : Y-> year S-> sequence C-> check digit
function gen_reg_no($sequence){

   //rotate the numer in each  $SEQ_PER_YEAR
   $SEQ_PER_YEAR=99999;
   $sequence=$sequence%$SEQ_PER_YEAR;

   $reg_no_length   =8;
   $seq_length      =5;
   $year            =date("y");
   $modulus         =5;
   
   $composite_no=$year;
   for($j=$seq_length;$j>strlen($sequence);$j--){
      $composite_no.='0';
   }
   $composite_no.=$sequence;

   $check=0;
   foreach(str_split($composite_no) as $digit){
      $check+=(int)$digit;
   }
   $check=($check%$modulus);
   $composite_no.=$check;
   return $composite_no;
}

/*
File donload function $file: path to file
*/
function file_download_plain($file){
   $finfo      =finfo_open(FILEINFO_MIME_TYPE);
   $mime_type  =finfo_file($finfo, $file);
      
   if (file_exists($file)) {
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private",false);
      header("Content-Type: ".$mime_type);
      header("Content-Disposition: attachment; filename=\"".basename($file)."\";");
      header("Content-Transfer-Encoding: binary");
      header("Content-Length: ".@filesize($file));
      readfile($file);
   }else{
      return_status_json('ERROR',"File not found!");
   }
   exit;
}

function file_download($path,$fid){
   $file       =$path."/".base64_decode($fid);
   $finfo      =finfo_open(FILEINFO_MIME_TYPE);
   $mime_type  =finfo_file($finfo, $file);
      
   if (file_exists($file)) {
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private",false);
      header("Content-Type: ".$mime_type);
      header("Content-Disposition: attachment; filename=\"".basename($file)."\";");
      header("Content-Transfer-Encoding: binary");
      header("Content-Length: ".@filesize($file));
      readfile($file);
   }else{
      return_status_json('ERROR',"File not found!");
   }
   exit;
}
/**
 * convert table into csv TODO
 */

function table_to_csv($table,$filename){
   $table=trim($table);
   $table=str_replace(array('</td><td>','</th><th>','</th><td>','</td><th>'),'","',$table);
   $table=str_replace(array('<tr><td>','<tr><th>'),'"',$table);
   $table=str_replace(array('</td></tr>','</th></tr>'),"\"\n",$table);

   set_file_header($filename.".csv");
   echo  $table;
   exit();
}

/**
 * This function curtasy of : please find the source
 */
function number_to_text($number){ 
   if(($number < 0) || ($number > 999999999)){ 
     throw new Exception("Number is out of range");
   } 

   $Gn     = floor($number / 1000000);  /* Millions (giga) */ 
   $number -= $Gn * 1000000; 
   $kn     = floor($number / 1000);     /* Thousands (kilo) */ 
   $number -= $kn * 1000; 
   $Hn     = floor($number / 100);      /* Hundreds (hecto) */ 
   $number -= $Hn * 100; 
   $Dn     = floor($number / 10);       /* Tens (deca) */ 
   $n      = $number % 10;               /* Ones */ 

   $res = ""; 

   if($Gn){ 
       $res .= number_to_text($Gn) . " Million"; 
   } 

   if($kn){ 
      $res .= (empty($res) ? "" : " ") . 
      number_to_text($kn) . " Thousand"; 
   } 

   if($Hn){ 
      $res .= (empty($res) ? "" : " ") . 
      number_to_text($Hn) . " Hundred"; 
   } 

   $ones = array(
      "", 
      "One", 
      "Two", 
      "Three", 
      "Four", 
      "Five", 
      "Six", 
      "Seven", 
      "Eight", 
      "Nine", 
      "Ten", 
      "Eleven", 
      "Twelve", 
      "Thirteen", 
      "Fourteen", 
      "Fifteen", 
      "Sixteen", 
      "Seventeen", 
      "Eightteen", 
      "Nineteen"
   ); 

   $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
       "Seventy", "Eigthy", "Ninety"); 

   if($Dn || $n){ 
      if (!empty($res)){ 
         $res .= " and "; 
      } 

      if ($Dn < 2){ 
         $res .= $ones[$Dn * 10 + $n]; 
      }else{ 
         $res .= $tens[$Dn]; 

         if($n){ 
            $res .= "-" . $ones[$n]; 
         } 
      } 
   } 

   if(empty($res)){ 
      $res = "zero"; 
   } 
   return $res; 
} 

/**
 * In order to indent the generated code given number of tabs generated
 */
function tab($num){
   $tab='   ';
   $ret='';
   for($i=0;$i<$num;$i++){
      $ret.=$tab;
   }
   return $ret;
}

function on_each_td(&$item,$key,$var=null){
   $item=str_replace(',','<br>',$item);
}

/**
 * Turn query result into a html table
 */
function query_to_htable($query){
   $arr=exec_query($query,Q_RET_ARRAY);
   
   if(!isset($arr[0]))return false;

   $headers=array_keys($arr[0]);
   array_walk($headers,'walk_style_text');

   $report="<table class='clean' border='1' style='border-collapse:collapse;'><tr><th>#</th><th>";
   $report.=implode('</th><th>',$headers);
   $report.="</th></tr>";
   $i=1;
   foreach($arr as $row){
      $report.="<tr><td>".$i++."</td><td>";
      $values=array_values($row);
      array_walk($values,'on_each_td');
      $report.=implode('</td><td>',$values);
      $report.="</td></tr>";
   }
   $report.="</table>";
   return  $report;
}
/**
* Adopted from: http://www.jonasjohn.de/snippets/php/readable-filesize.htm
*/
function hr_filesize($size){
    $mod = 1024;
    $units = explode(' ','B KB MB GB TB PB');
    for ($i = 0; $size > $mod; $i++) {
        $size /= $mod;
    }
    return round($size, 2) . ' ' . $units[$i];
}

?>
