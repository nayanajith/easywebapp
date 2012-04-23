<?php
/**
 * wrapper for isset() function which will return value if is set else return null
 * TODO
 */

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
               @import '/uis/css/common_css.php';
            </style>
           <link rel='shortcut icon' href='/uis/img/favicon.ico'type='image/x-icon' >
           <script src='/uis/js/common_js.php' type='text/javascript'></script>
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
//Keep the filter key=value paires for future use this array may initialized in model_class.php
//$_SESSION[PAGE]['FILTER_ARRAY']=array();
//
//Filter exceptions are stored in FILTER_ARRAY_EXP which will override the default values
//$_SESSION[PAGE]['FILTER_ARRAY_EXP']=array();

/**
 * Regenerate the filter with customizations
 */
function gen_filter($table_as=null){
   if(isset($_SESSION[PAGE]['FILTER_ARRAY']) && is_array($_SESSION[PAGE]['FILTER_ARRAY']) && sizeof($_SESSION[PAGE]['FILTER_ARRAY']) > 0){ 
   }else{
      return null;
   }   

   //return if request with the table name prefix
   if(!is_null($table_as)){
      $table_as=$table_as.".";
   }   

   $filter="";
   $and="";
   foreach($_SESSION[PAGE]['FILTER_ARRAY'] as $key => $value){
      //override the default values with the exceptions
      if(isset($_SESSION[PAGE]['FILTER_ARRAY_EXP']) && $_SESSION[PAGE]['FILTER_ARRAY_EXP'][$key]){
         $value=$_SESSION[PAGE]['FILTER_ARRAY_EXP'][$key]; 
      }else{
         $value=$table_as."`".$key."` LIKE '%".$value."%'"; 
      }   

      $filter.=$and.$value;
      $and=' AND ';
   }   
   return $filter; 
}

/**
 * Delete temporary filter for the submitted values
 */

function del_temp_filter($table_as=null){
   if(isset($_SESSION[PAGE]['FILTER_ARRAY'])){
      unset($_SESSION[PAGE]['FILTER']);
      unset($_SESSION[PAGE]['FILTER_ARRAY']);
   }
}

/**
 * Generate temporary filter for the submitted values
 */
function get_temp_filter($table_as=null){

   //Reset the global filter array
   $_SESSION[PAGE]['FILTER_ARRAY']=array();

   //return if request with the table name prefix
   if(!is_null($table_as)){
      $table_as=$table_as.".";
   }

   $filter="";
   $and="";
   foreach(array_keys($GLOBALS['MODEL']['MAIN_LEFT']) as $key){
      if($key != $GLOBALS['MODEL']['KEYS']['PRIMARY_KEY'] && isset($_REQUEST[$key]) && $_REQUEST[$key] != '' && $_REQUEST[$key] != 'NULL' ){
         $filter.=$and.$table_as."`".$key."` LIKE '%".$_REQUEST[$key]."%'";
         $and=' AND ';
         //keep the filter array for future use
         $_SESSION[PAGE]['FILTER_ARRAY'][$key]=$_REQUEST[$key];
      }
   }
   return $filter; 
}


//Array to keep the view entries before puting in VIEW array 
$GLOBALS['PREVIEW']=array(

);

/*--create and fill view global array which contains all parts of the fintend-*/
$GLOBALS['VIEW']=array(
   'CSS'       =>'',
   'JS'        =>'',
   'LOADING'   =>'',
   'LOGIN'     =>'',
   'PROGRAM'   =>'',
   'BREADCRUMB'=>'',
   'NAVIGATOR' =>'',
   'MAIN_TOP' =>'',
   'MAIN_LEFT' =>'',
   'MAIN_RIGHT'=>'',
   'MAIN_BOTTOM'=>'',
   'WIDGETS'   =>'',
   'MENUBAR'   =>'',
   'TOOLBAR'   =>'',
   'STATUSBAR' =>'',
   'FOOTER'    =>''
);

/*
 * View_id : one of the ids in above array
 * contet   : any html/css/js content or a include file which will generate any of the contet
 * before : true/false
 */

function add_to_view($view_id,$content,$before=false){
   //IF the contet is a file then include and get the output to array using ob_func
   if(isset($GLOBALS['VIEW'][$view_id])){
      if(is_file($content)){
         ob_start();
         include $content;
         $content=ob_get_contents();
         if($before){
            $GLOBALS['VIEW'][$view_id] = $content.$GLOBALS['VIEW'][$view_id];
         }else{
            $GLOBALS['VIEW'][$view_id] .= $content;
         }
         ob_end_clean();
      }elseif(!is_null($content) || $content != ''){
         if($before){
            $GLOBALS['VIEW'][$view_id] = $content.$GLOBALS['VIEW'][$view_id];
         }else{
            $GLOBALS['VIEW'][$view_id] .= $content;
         }
      }
   }else{
      return "key[$view_id] error!"; 
   }
}

/**
 * Clear the view with blank
 */
function clear_view($view_id){
   if(isset($GLOBALS['VIEW'][$view_id])){
      $GLOBALS['VIEW'][$view_id] = '';
   }else{
      return "key[$view_id] error!"; 
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
function add_to_widgets($content,$before=false){
   add_to_view('WIDGETS',$content,$before);
}
function add_to_menubar($content,$before=false){
   add_to_view('MENUBAR',$content,$before);
}
function add_to_toolbar($content,$before=false){
   add_to_view('TOOLBAR',$content,$before);
}
function add_to_statusbar($content,$before=false){
   add_to_view('STATUSBAR',$content,$before);
}
function add_to_footer($content,$before=false){
   add_to_view('FOOTER',$content,$before);
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
function clear_widgets(){
   clear_view('WIDGETS');
}
function clear_menubar(){
   clear_view('MENUBAR');
}
function clear_toolbar(){
   clear_view('TOOLBAR');
}
function clear_statusbar(){
   clear_view('STATUSBAR');
}
function clear_footer(){
   clear_view('FOOTER');
}

/**
 * Add return the field for the given field_id from $GLOBALS['PREVIEW']['MAIN_LEFT']
 */
function get_field($field_id){
   if(isset($GLOBALS['PREVIEW']['MAIN_LEFT'][$field_id])){
      return $GLOBALS['PREVIEW']['MAIN_LEFT'][$field_id]['field'];
   }
}

/**
 * Add return the label for the given field_id from $GLOBALS['PREVIEW']['MAIN_LEFT']
 */
function get_label($field_id){
   if(isset($GLOBALS['PREVIEW']['MAIN_LEFT'][$field_id])){
      return $GLOBALS['PREVIEW']['MAIN_LEFT'][$field_id]['label'];
   }
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
      $content_type='application/vnd.ms-excel';
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
   default:
      $content_type='text/json';
   break;
   }

  header('Content-Type',$content_type );
  header('Content-Disposition: attachment; filename='.$file_name);
  //header("Content-type: application/octet-stream");
  //header("Content-Disposition: attachment; filename=your_desired_name.xls");
  //header("Content-Length: ".@filesize($file));
  //header("Content-Transfer-Encoding: binary");
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
function get_common_list($list_name){
   $arr=exec_query("SELECT list_title,json FROM ".$GLOBALS['S_TABLES']['common_lists']." WHERE list_name='".$list_name."'",Q_RET_ARRAY);
   if(isset($arr[0])){
      return array(
         'title'  =>$arr[0]['list_title'],
         'list'   =>json_decode($arr[0]['json'])
      );
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
function gen_select_inner($arr,$label=null,$without_none=false){
   //validation  $arr must be an array
   if(!is_array($arr)){
      return null;
   }

   $select='<option value="NULL">-none-</option>';
   if($without_none){
      $select='';
   }

   if(is_assoc_array($arr)){
      //Direct compatibility with  returning array of exec_query
      if(is_array($arr[key($arr)])){
         foreach($arr as $key=>$value ){
            $select.="<option value=\"$key\">$value[$label]</option>";
         }
      }else{
         //Associative array with ke=>value
         foreach($arr as $key=>$value ){
            $select.="<option value=\"$key\">$value</option>";
         }
      }
   }else{
      //1D array with values
      foreach($arr as $value ){
         $select.="<option value=\"$value\">$value</option>";
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
function gen_url($type=null){
   switch($type){
      case 3:
         $url=$GLOBALS['PAGE_GEN']."?";
         $amp='';
         $skip=array('filter_name');
         foreach($_REQUEST as $key=>$value){
            if(in_array($key,$skip))continue;
            $url.=$amp.$key.'='.$value;
            $amp='&';
         }
         return $url;
      break;

      case 2:
         $url=$GLOBALS['PAGE_GEN']."?";
         $amp='';
         foreach($_REQUEST as $key=>$value){
            $url.=$amp.$key.'='.$value;
            $amp='&';
         }
         return $url;
      break;
      case 1:
      default :
         $filter_name="";
         if(isset($_REQUEST['filter_name'])){
            $filter_name="&filter_name=".$_REQUEST['filter_name'];
         }
         return $GLOBALS['PAGE_GEN']."?module=".MODULE."&page=".PAGE."&program=".PROGRAM.$filter_name;
      break;
   }
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

/*
 * Log a message in log file 
 */
function log_msg($id=null,$msg=null,$color=null){
   if(LOG_ENABLED == 'NO')return;
   $date_time=date("d-M-Y h:i:s");

   //Adjust for the single
   if(is_null($msg)){
      $msg=$id;

      //find the function which called the log_msg
      $trace   =debug_backtrace();
      $caller  =array_shift($trace);
      $caller  =array_shift($trace);
      $class   ="";
      if(isset($caller['class'])){
         $class   =$caller['class'].'.';
      }
      $id=$class.$caller['function'];
   }

   $file_handler =null;
   if(file_exists(LOG)){
      $file_handler = fopen(LOG, 'a');
   }else{
      $file_handler = fopen(LOG, 'w');
   }

   //log array content if msg is an array
   if(is_array($msg)){
      @ob_start();
      @print_r($msg);
      $msg = @ob_get_contents();
      @ob_end_clean();
   }

   fwrite($file_handler, "[$date_time] $id :$msg\n");
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
   $table=str_replace(array('</td><td>','</th><th>'),"','",$table);
   $table=str_replace(array('<tr><td>','<tr><th>'),"'",$table);
   $table=str_replace(array('</td></tr>','</th></tr>'),"'\n",$table);

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

?>
