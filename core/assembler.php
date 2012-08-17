<?php
//If any of the sections value returned null re run the total process and fill the view array
$content_found=true;

//First run to verify and return the section
if(isset($_REQUEST['section'])){
   switch($_REQUEST['section']){
   case 'TOOLBAR_TOP':
      if(get_toolbar_tl(false) != '' || get_toolbar_tr(false) != ''){
         ?>
            <!--div id='toolbar_top' id='toolbar_top' dojoType='dijit.Toolbar' style='border-left:0px;padding-left:1px;height:35px;background-color:#5B92C8'-->
            <div id='toolbar_top' id='toolbar_top' dojoType='dijit.Toolbar' style='border-left:0px;padding-left:1px;height:35px'>
               <table width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                     <td width="30%">
                        <?php
                           echo get_toolbar_tl();
                        ?>
                     </td>
                     <td width="40%"  align="center">
                        <button dojoType="dijit.form.Button" style="font-size:14px;font-weight:bold">
                        <img src="<?php echo $GLOBALS['LOGO']; ?>" height=30px>
                        <?php echo $GLOBALS['TITLE']; ?>
                        </button>
                     </td>
                     <td width="30%">
                        <div style="float:right" >
                           <?php
                              echo get_toolbar_tr();
                           ?>
                        </div>
                     </td>
                  </tr>
               </table>
            </div>
         <?php
      }else{
         $content_found=false;
      }
   break;
   case 'MENUBAR':
      if(get_menubar(false) != ''){
         echo "<div id='menubar' dojoType='dijit.MenuBar' style='height:26px;padding-left:1px;border-right:0px;border-left:0px;border-top:1px solid whitesmoke;'>";
         echo get_menubar();
         echo "</div>";
      }else{
         $content_found=false;
      }
   break;
   case 'TOOLBAR':
      if(get_toolbar(false) != ''){
         echo "<div id='toolbar' dojoType='dijit.Toolbar' style='height:46px'>";
         echo get_toolbar();
         echo "</div>";
      }else{
         $content_found=false;
      }
   break;
   case 'LAYOUT':
      //echo get_layout('app2');
      $content_found=false;
   break;
   case 'NOTIFY':
      print_r(get_notify(false));
      echo "<sub><hr style='padding:0px'>".date("d-m-y")."</sub>";
   break;
   case 'ISNOTIFY':
      echo "{'count':'".sizeof(get_notify(false))."'}";
   break;
   case 'FILTER':
      if(!is_null(get_filter())){
         echo "<p>".get_filter()."</p>
            <button dojoType='dijit.form.Button' type='submit'>
               <script type='dojo/method' event='onClick' args='item'> 
                     if(typeof grid__GRID === 'undefined'){
                        s_f_c_add('ok',reload_main);
                     }else{
                        s_f_c_add('ok',reload_grid,grid__GRID);
                     }
                     s_f_c_add('ok',w_d,toolbar__del_filter);
                     submit_form('del_filter');
               </script>
                  Delete Filter
            </button>
            ";
      }else{
         echo "No filter added!"; 
      }
   break;
   case 'DIALOG':
      if(get_dialog(false) != ''){
         echo "<p>".get_dialog()."</p>
            <button dojoType='dijit.form.Button' type='submit'>
               <script type='dojo/method' event='onClick' args='item'> 
                  console.log('ok');
               </script>
                 OK 
            </button>
            ";
      }else{
         $content_found=false;
      }
   break;
   case 'DYNAMIC_JS':
      if(get_from_view($_REQUEST['section'],false) != ''){
			set_file_header('dynamic.js');
         echo get_from_view($_REQUEST['section']);
      }else{
         $content_found=false;
      }
	break;
   case 'MAIN_TOP':
   case 'MAIN_LEFT':
   case 'MAIN_RIGHT':
   case 'MAIN_BOTTOM':
   case 'MAIN_TOP':
      if(get_from_view($_REQUEST['section'],false) != ''){
         echo get_from_view($_REQUEST['section']);
      }else{
         $content_found=false;
      }
   break;
   default:
      //Custom views sections  which added by add_to_cview function
      echo get_from_cview($_REQUEST['section']);
   break;
   }
}


if(isset($_REQUEST['section']) && $content_found){
   exit();
}


///////////////////////////////NO cached section not found so fill the cache with new content/////////////////////////////////
//CLear everything in view
clear_view();

//main page selection logic
$main='';
//TODO: group wise main file
if($_SESSION['LAYOUT']=='pub'){
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
add_to_main_left($main);

//Page footer
add_to_footer(A_CORE."/footer.php");

//Status bar  for seb/app layouts
add_to_statusbar(A_CORE."/status_bar.php");

//Loading animation for all reloads
add_to_loading(A_CORE."/loading.php");

//Javascript of requiring dojo and other custom scripts
add_to_js(A_CORE."/dojo_require.php");

//Javascript for status bar functions
add_to_js(A_CORE."/status_bar_func.php");

//Stylesheets for the page
add_to_css(A_CORE."/style.php");

//Fill the view according to different layouts
switch($_SESSION['LAYOUT']){
case 'pub':
   //Stylesheets for the page
   add_to_breadcrumb(A_CORE."/breadcrumb.php");

   //Navigator for public layout is a link list
   add_to_navigator(A_CORE."/module_link_list.php");

   if(isset($_SESSION['username'])){
      add_to_login("You are loged in as ".$_SESSION['username']."<br>");
      add_to_login("<a href=\"?page=".PAGE."&module=".MODULE."&logout=logout\">Logout</a>");
   }

break;
case 'app':
   //Program selector
   if(defined('P_SELECTOR') && P_SELECTOR=='YES'){
      ob_start();
      echo "Change Program:";
      program_select($_SESSION['PROGRAM']); 
      add_to_program(ob_get_contents());
      ob_end_clean();
   }

   //login/logout 
   ob_start();
   if (isset($_SESSION['username'])){
      echo after_login();
   }else{
      echo before_login();
   }
   add_to_login(ob_get_contents());
   ob_end_clean();

   //Navigator tree
   add_to_navigator(A_CORE."/module_tree.php");

   //Menubar
   add_to_navigator(A_CORE."/menubar_left.php");

   //WIdgetst column
   add_to_widgets(A_CORE."/widget_column.php");

   //Tool bar for web/app layouts
   add_to_toolbar(A_CORE."/toolbar.php");
break;
case 'web':
   //Program selector
   if(defined('P_SELECTOR') && P_SELECTOR=='YES'){
      ob_start();
      echo "Change Program:";
      program_select(PROGRAM); 
      add_to_program(ob_get_contents());
      ob_end_clean();
   }

   //login/logout 
   ob_start();
   if (isset($_SESSION['username'])){
      echo after_login();
   }else{
      echo before_login();
   }
   add_to_login(ob_get_contents());
   ob_end_clean();

   //Navigator for web layout is a tab bar
   add_to_navigator(A_CORE."/module_tab_bar.php");

   //Tool bar for web/app layouts
   add_to_toolbar(A_CORE."/toolbar.php");
break;
case 'app2':
   //Program selector
   if(defined('P_SELECTOR') && P_SELECTOR=='YES'){
      ob_start();
      program_select(PROGRAM); 
      add_to_program(ob_get_contents());
      ob_end_clean();
   }

   //login/logout 
   ob_start();
   if (isset($_SESSION['username'])){
      echo after_login();
   }else{
      echo before_login();
   }
   add_to_login(ob_get_contents());
   ob_end_clean();

   //Navigator for web layout is a tab bar
   //add_to_navigator(A_CORE."/module_tab_bar.php");

   //Tool bar for web/app layouts
   add_to_menubar(A_CORE."/module_menubar.php");

   //Top right corner of the menubar is a toolbar
   add_to_toolbar_tr(A_CORE."/toolbar_tr.php");

   //Top left corner of the menubar is a toolbar
   add_to_toolbar_tl(A_CORE."/toolbar_tl.php");

   //Tool bar for web/app layouts
   add_to_toolbar(A_CORE."/toolbar.php");
break;
case 'clean':
break;

}

//Second run to return the section
if(isset($_REQUEST['section'])){

   switch($_REQUEST['section']){
   case 'TOOLBAR_TOP':
      ?>
         <!--div id='toolbar_top' id='toolbar_top' dojoType='dijit.Toolbar' style='border-left:0px;padding-left:1px;height:35px;background-color:#5B92C8'-->
         <div id='toolbar_top' id='toolbar_top' dojoType='dijit.Toolbar' style='border-left:0px;padding-left:1px;height:35px'>
            <table width="100%" cellpadding="0" cellspacing="0">
               <tr>
                  <td width="30%">
                     <?php
                     echo get_toolbar_tl();
                     ?>
                  </td>
                  <td width="40%"  align="center">
                     <button dojoType="dijit.form.Button" style="font-size:14px;font-weight:bold">
                     <img src="<?php echo $GLOBALS['LOGO']; ?>" height=30px>
                     <?php echo $GLOBALS['TITLE']; ?>
                     </button>
                  </td>
                  <td width="30%">
                     <div style="float:right" >
                        <?php
                        echo get_toolbar_tr();
                        ?>
                     </div>
                  </td>
               </tr>
            </table>
         </div>
      <?php
   break;
   case 'MENUBAR':
      echo "<div id='menubar' dojoType='dijit.MenuBar' style='height:26px;padding-left:1px;border-right:0px;border-left:0px;border-top:1px solid whitesmoke;'>";
      echo get_menubar();
      echo "</div>";
   break;
   case 'TOOLBAR':
      echo "<div id='toolbar' dojoType='dijit.Toolbar' style='height:46px'>";
      echo get_toolbar();
      echo "</div>";
   break;
   case 'LAYOUT':
      echo get_layout('app2');
   break;
   case 'NOTIFY':
      print_r(get_notify(false));
      echo "<sub><hr style='padding:0px'>".date("d-m-y")."</sub>";
   break;
   case 'ISNOTIFY':
      echo "{'count':'".sizeof(get_notify(false))."'}";
   break;
   case 'FILTER':
      if(!is_null(get_filter())){
         echo "<p>".get_filter()."</p>
            <button dojoType='dijit.form.Button' type='submit'>
               <script type='dojo/method' event='onClick' args='item'> 
                     if(typeof grid__GRID === 'undefined'){
                        s_f_c_add('ok',reload_main);
                     }else{
                        s_f_c_add('ok',reload_grid,grid__GRID);
                     }
                     s_f_c_add('ok',w_d,toolbar__del_filter);
                     submit_form('del_filter');
               </script>
                  Delete Filter
            </button>
            ";

      }else{
         echo "No filter added!"; 
      }
   break;
   case 'DIALOG':
      if(!is_null(get_dialog(false))){
         echo "<p>".get_dialog()."</p>
            <button dojoType='dijit.form.Button' type='submit'>
               <script type='dojo/method' event='onClick' args='item'> 
                  alert('kk')
               </script>
                 OK 
            </button>
            ";
      }
   break;
   case 'DYNAMIC_JS':
		set_file_header('dynamic.js');
      echo get_from_view($_REQUEST['section']);
	break;
   case 'MAIN_TOP':
   case 'MAIN_LEFT':
   case 'MAIN_RIGHT':
   case 'MAIN_BOTTOM':
   case 'MAIN_TOP':
      echo get_from_view($_REQUEST['section']);
   break;
   default:
      //Custom views sections  which added by add_to_cview function
      echo get_from_cview($_REQUEST['section']);
   break;
   }
   //exit from all
   exit();
}

?>
