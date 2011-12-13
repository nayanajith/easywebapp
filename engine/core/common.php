<?php
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

function d_r($module){
   dojo_require($module);
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


/*
 * Print debug messages
 */
function debug($msg,$id,$color){
   if(isset($GLOBALS['DEBUG']) && $GLOBALS['DEBUG']){
      echo "<span style='color:".$color."'>[".$id."]</span>".$msg."<br/>";
   }
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
 * Convert number to text for 1,2,3
 */
function num_to_text($num){
   $text="";
   switch ($num){
      case 1:
         $text="First";
         break;
      case 2:
         $text="Second";
         break;
      case 3:
         $text="Third";
         break;
      case 4:
         $text="Fourth";
         break;
   }
   return $text;
}


/*
 * Extract Examination id and return ac year ex year and semester
 */
function exam_detail($eid){
   return array(
      "semester"=>num_to_text(substr($eid, -1, 1)),
      "ac_year" =>num_to_text(substr($eid, -2, 1)),
      "ex_year" =>2000+(int)substr($eid, 0, -2)
   );
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
function log_msg($id,$msg,$color=null){
   if(LOG_ENABLED == 'NO')return;
   $date_time=date("d-M-Y h:i:s");

   $file_handler =null;
   if(file_exists(LOG)){
      $file_handler = fopen(LOG, 'a');
   }else{
      $file_handler = fopen(LOG, 'w');
   }

   //log array content if msg is an array
   if(is_array($msg)){
      ob_start('ob_gzhandler');
      print_r($msg);
      $msg = ob_get_contents();
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
<br/><table>
<tr><td align=right><img src='".$GLOBALS['logo']."' height=60 /></td>
<td><h3>".TITLE_LONG."</h3></td></tr>
<tr><td colspan=2 align=center><h4>$title</h4></td></tr></table><hr/>";   
}

/*
 * Print Footer of the reports
 */
function print_footer(){
   echo "<hr/><h4>".date('D jS \of F Y')."</h4></div></body></html>";
}

//reg no format YYSSSSSC : Y-> year S-> sequence C-> check digit
function gen_reg_no($sequence){
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

function table_to_csv($table){
   $table=trim($table);
   $table=str_replace(array('</td><td>','</th><th>'),"','",$table);
   $table=str_replace(array('<tr><td>','<tr><th>'),"'",$table);
   $table=str_replace(array('</td></tr>','</th></tr>'),"'\n",$table);

   header('Content-Type', 'application/vnd.ms-excel');
   header('Content-Disposition: attachment; filename='.$_SESSION[PAGE]['batch_id'].'-'.$_SESSION[PAGE]['eligibility'].'.csv');
   header("Pragma: no-cache");
   header("Expires: 0");
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

   $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
       "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
       "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 
       "Nineteen"); 
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


?>
