<?php
/*--create and fill view global array which contains all parts of the fintend-*/
$GLOBALS['VIEW']=array(
   'MAIN'      =>'',
   'CSS'       =>'',
   'JS'        =>'',
   'LOADING'   =>'',
   'LOGIN'     =>'',
   'PROGRAM'   =>'',
   'BREADCRUMB'=>'',
   'NAVIGATOR' =>'',
   'WIDGETS'   =>'',
   'MENUBAR'   =>'',
   'TOOLBAR'   =>'',
   'STATUSBAR' =>'',
   'FOOTER'    =>''
);

/**
 * Include the file and generated contet will placed in global view array
 */
function fill_view($view_id,$file){
   if(isset($GLOBALS['VIEW'][$view_id])){
      ob_start();
      include $file;
      $content=ob_get_contents();

      $GLOBALS['VIEW'][$view_id] .= $content;
      ob_end_clean();
   }else{
      return "key[$view_id] error!"; 
   }
}

//main page selection logic
$main='';

if($GLOBALS['LAYOUT']=='pub'){
   if(!file_exists(A_MODULES."/".MODULE."/".PAGE.".php")){
      $main="error.php";
   }else{
      $main=A_MODULES."/".MODULE."/".PAGE.".php";
   }
}else{
   if(!isset($_SESSION['username'])){
      if(file_exists(A_MODULES."/".MODULE."/about.php")){
         $main=A_MODULES."/".MODULE."/about.php";
      }else{
         $main="error.php";
      }
   }elseif(!file_exists(A_MODULES."/".MODULE."/".PAGE.".php")){
      $main="error.php";
   }else{
      $main=A_MODULES."/".MODULE."/".PAGE.".php";
   }
}


//Main file contains the module+page specific features and outputs which will affect the view 
fill_view('MAIN',       $main);



//Page footer
fill_view('FOOTER',     A_CORE."/footer.php");


//Status bar  for seb/app layouts
fill_view('STATUSBAR',  A_CORE."/status_bar.php");

//Loading animation for all reloads
fill_view('LOADING',    A_CORE."/loading.php");

//Javascript of requiring dojo and other custom scripts
fill_view('JS',         A_CORE."/dojo_require.php");

//Javascript for status bar functions
fill_view('JS',         A_CORE."/status_bar_func.php");

//Stylesheets for the page
fill_view('CSS',        A_CORE."/style.php");


//Fill the view according to different layouts
switch($GLOBALS['LAYOUT']){
case 'pub':
   
   //Stylesheets for the page
   fill_view('BREADCRUMB', A_CORE."/breadcrumb.php");

   //Navigator for public layout is a link list
   fill_view('NAVIGATOR',  A_CORE."/module_link_list.php");

   if(isset($_SESSION['username'])){
      $GLOBALS['VIEW']['LOGIN'] .="You are loged in as ".$_SESSION['username']."<br>";
      $GLOBALS['VIEW']['LOGIN'] .="<a href=\"?page=".PAGE."&module=".MODULE."&logout=logout\">Logout</a>";
   }

break;
case 'app':
   //Program selector
   ob_start();
   echo "Change Program:";
   program_select($program); 
   $GLOBALS['VIEW']['PROGRAM'] .= ob_get_contents();
   ob_end_clean();

   //login/logout 
   ob_start();
   if (isset($_SESSION['username'])){
      echo after_login();
   }else{
      echo before_login();
   }
   $GLOBALS['VIEW']['LOGIN'] .= ob_get_contents();
   ob_end_clean();

   //Navigator tree
   fill_view('NAVIGATOR',A_CORE."/module_tree.php");

   //Menubar
   fill_view('MENUBAR',  A_CORE."/menubar.php");

   //WIdgetst column
   fill_view('WIDGETS',  A_CORE."/widget_column.php");

   //Tool bar for web/app layouts
   fill_view('TOOLBAR',  A_CORE."/toolbar.php");
break;
case 'web':
   //Program selector
   ob_start();
   echo "Change Program:";
   program_select($program); 
   $GLOBALS['VIEW']['PROGRAM'] .= ob_get_contents();
   ob_end_clean();

   //login/logout 
   ob_start();
   if (isset($_SESSION['username'])){
      echo after_login();
   }else{
      echo before_login();
   }
   $GLOBALS['VIEW']['LOGIN'] .= ob_get_contents();
   ob_end_clean();

   //Navigator for web layout is a tab bar
   fill_view('NAVIGATOR',  A_CORE."/module_tab_bar.php");

   //Tool bar for web/app layouts
   fill_view('TOOLBAR',    A_CORE."/toolbar.php");
break;
}

?>
