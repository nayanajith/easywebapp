<?php

/*
Generate json for the main menu tree
eg:

{
identifier:'id',
label: 'name',
items: [
{id:'home',name:'Home',type:'module',children:[{_reference:'home_about'},{_reference:'home_help'}]},
{id:'home_about',name:'About',url:'module=home&page=about',type:'page'},
{id:'home_help',name:'Help',url:'module=home&page=help',type:'page'},
{id:'configure',name:'Configure',type:'module',children:[{_reference:'configure_program'},{_reference:'configure_user'}]},
{id:'configure_program',name:'Program',url:'module=configure&page=program',type:'page'},
{id:'configure_user',name:'Users',url:'module=configure&page=user',type:'page'},
{id:'student',name:'Student',type:'module',children:[{_reference:'student_manage_student'},{_reference:'student_push'},{_reference:'student_bit_push'}]},
{id:'student_manage_student',name:'Manage student',url:'module=student&page=manage_student',type:'page'},
{id:'student_push',name:'Push',url:'module=student&page=push',type:'page'},
{id:'student_bit_push',name:'BIT Push',url:'module=student&page=bit_push',type:'page'},
{id:'eligibility',name:'Eligibility',type:'module',children:[{_reference:'eligibility_manage_push'},{_reference:'eligibility_check_push'},{_reference:'eligibility_pass_list'},{_reference:'eligibility_year3'},{_reference:'eligibility_year4'}]},
{id:'eligibility_manage_push',name:'Manage push',url:'module=eligibility&page=manage_push',type:'page'},
{id:'eligibility_check_push',name:'Check push',url:'module=eligibility&page=check_push',type:'page'},
{id:'eligibility_pass_list',name:'Pass list',url:'module=eligibility&page=pass_list',type:'page'},
{id:'eligibility_year3',name:'Year 3',url:'module=eligibility&page=year3',type:'page'},
{id:'eligibility_year4',name:'Year 4',url:'module=eligibility&page=year4',type:'page'},
....
....

]
}
*/

load_permission();
function gen_tree(){
$modules=$GLOBALS['MODULES'];
/*-----------------generate json-------------------*/
   $json = "{
identifier:'id',
label: 'name',
items: [
";
   $comma1="";
   foreach ($modules as $mod_key => $mod) {
      $module_visible=true;

      /*Module discriptor may be an array*/
      if(is_array($mod)){
         $module_visible=$mod['VISIBLE'];
         $mod=$mod['MODULE'];
      }


      /*Skip not permitted modules*/
      if(!is_module_permitted($mod_key) || !$module_visible){
         continue;   
      }

      $menu_array=array("null"=>"null");
      $json               .=$comma1."\n{id:'$mod_key',name:'$mod',type:'module'";
      $module_menu_file   =A_MODULES."/".$mod_key."/menu.php";
      if(file_exists($module_menu_file)){
         include($module_menu_file);
         $children      =",children:[";
         $page_string   ="";
         $comma2         ="";
         foreach($menu_array as $page_key => $page){
            if(is_array($page)){
                if(isset($page['VISIBLE']) && $page['VISIBLE']=='false'){
                  continue;
               }else{
                  $page=$page['PAGE'];
               }
            }
            /*Skip not permitted pages*/
            if(!is_page_permitted($mod_key,$page_key)){
               continue;   
            }

            $children      .=$comma2."{_reference:'".$mod_key."_".$page_key."'}";
            $page_string   .=$comma2."\n{id:'".$mod_key."_".$page_key."',name:'$page',url:'module=$mod_key&page=$page_key',type:'page'}";
            $comma2         =",";
         }
         $comma1         =",";
         $children      .="]},";
         //$page_string   .="}\n";
         $json            .=$children.$page_string;
      }
   }
   $json .="]\n}\n";
/*-----------------generate json-------------------*/
   /*
   $json_file=A_ROOT."/module_tree.json";
   $file_handler = fopen($json_file, 'w');
   fwrite($file_handler,$json);
   fclose($file_handler);
   */
   header('Content-Type', 'text/json');
   header("Content-Disposition: attachment; filename=\"module_tree.json\"");
   echo $json;
}

function gen_module_array(){
   $modules=$GLOBALS['MODULES'];
   $tabs=array();
   foreach ($modules as $mod_key => $mod) {
      $module_visible=true;

      /*Module discriptor may be an array*/
      if(is_array($mod)){
         $module_visible=$mod['VISIBLE'];
         $mod=$mod['MODULE'];
      }

      /*Skip not permitted modules*/
      if(!is_module_permitted($mod_key) ||  !$module_visible){
         continue;   
      }

      $module_menu_file   =A_MODULES."/".$mod_key."/menu.php";
      if(file_exists($module_menu_file)){
         include($module_menu_file);
         foreach($menu_array as $page_key => $page){

            /*pages can be arrays to express the visibility*/
            if(is_array($page)){
               $page=$page['PAGE'];
            }

            /*Skip not permitted pages*/
            if(!is_page_permitted($mod_key,$page_key)){
               continue;   
            }

            $tabs[$mod_key][$page_key]=$page;
         }
      }
   }
   return $tabs;
}

function gen_visible_module_array(){
   $modules=$GLOBALS['MODULES'];
   $tabs=array();
   foreach ($modules as $mod_key => $mod) {
      $module_visible=true;

      /*Module discriptor may be an array*/
      if(is_array($mod)){
         $module_visible=$mod['VISIBLE'];
         $mod=$mod['MODULE'];
      }

      /*Skip not permitted modules*/
      if(!is_module_permitted($mod_key))continue;

      //TODO:logic seems not working
      if(!$module_visible)continue;
            
      $module_menu_file=A_MODULES."/".$mod_key."/menu.php";

      if(file_exists($module_menu_file)){
         include($module_menu_file);
         foreach($menu_array as $page_key => $page){

            /*pages can be arrays to express the visibility*/
            if(is_array($page)){
                if(isset($page['VISIBLE']) && $page['VISIBLE']=='false'){
                  continue;
               }
            }

            if(!is_page_permitted($mod_key,$page_key)){
               continue;   
            }else{
               $tabs[$mod_key][$page_key]=$page;
            }
         }
      }
   }
   return $tabs;
}

/*Module requre */
/*
Array
(
    [home] => Array
        (
            [news] => News
            [about] => About
            [help] => Help
            [slogin] => Login
        )

    [system] => Array
        (
            [manage_users] => System Users
            [manage_permission] => System Users Permission
            [activity] => Activity Log
            [system_log] => System Log
            [init_db] => >Regenerate Database<
        )
*/
/**
 * Convention of class names :with first letter upper case ends with _class.php
 * Convention of function files :camel case with first letter lower case  ends with .php
 * - $uri:module path using java convention eg: uis.student.registration.batch
 */
$module_hierarchy;
function uis_require($uri,$strict=null){
   global $module_hierarchy;

   //If strict is false camel case will be converted in to underscore and returned if camel case is not available
   $strict=false;

   //If the module array alrady loaded it will not loaded for this session
   if(!isset($module_hierarchy)){
      $module_hierarchy=gen_module_array();
   }
   //Seperate the tokens by '.'
   $break   =explode(".",$uri);

   //Go through the module array to find it's availability
   /*
   $index=0;
   $base =null;
   if(isset($module_hierarchy[$break[$index]])){
      $base =$module_hierarchy[$break[$index++]];
      while(isset($base[$break[$index]])){
         $base=$base[$break[$index++]];
         print_r($base);
      }
   }
    */

   $file    =$break[sizeof($break)-1];
   $module  =$break[sizeof($break)-2];

   $file=A_ROOT."/".str_replace(".","/",$uri).".php";
   if(is_file($file)){
      include_once($file);
      return $file; 
   }else{
      return false; 
   }
}
echo uis_require("home.news");


/**
 * This function will return the names of the classes in the given file
 */
function get_php_classes($php_file) {
  $classes = array();
  $tokens = token_get_all($php_file);
  $count = count($tokens);
  for ($i = 2; $i < $count; $i++) {
    if (   $tokens[$i - 2][0] == T_CLASS
        && $tokens[$i - 1][0] == T_WHITESPACE
        && $tokens[$i][0] == T_STRING) {

        $classes[] = $tokens[$i][1];
    }
  }
  return $classes;
}

/*Module provide */
function uis_provide(){
}



/*Generat JSON for module tree*/
if($GLOBALS['DATA']==true){
   if(isset($_REQUEST['mod_tree'])){
      gen_tree();
   }
}


?>
