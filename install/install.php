<?php 
/*--------------------------Enable disable Errors ----------------------------*/
ini_set('display_errors',1);
ini_set('memory_limit','512M');
error_reporting(E_ALL|E_STRICT);

include_once A_CORE."/common.php";
include_once A_CORE."/database_schema.php";
include_once A_CORE."/database.php";

/*If the databse configuration files already available do not do enything but show this message and return*/
if(file_exists(DB_CONF)){
   echo "<br><br><br><br><center>System have already installed!<br>If you want to reinstall the system please delete db_config.php</center>";
   return;
}

/*If the installation requestes with all required information is received  preceed with the instalation*/
if(isset($_REQUEST['action']) && $_REQUEST['action']=='install'){
   if(
      isset($_REQUEST['db_host']) && $_REQUEST['db_host']!='' &&
      isset($_REQUEST['db_root']) && $_REQUEST['db_root']!='' &&
      isset($_REQUEST['db_name']) && $_REQUEST['db_name']!='' &&
      isset($_REQUEST['db_password']) && $_REQUEST['db_password']!=''
   ){
      //print_r($_REQUEST);

      /*Im not creating databses since I can not write the configuration file!*/
      /*
      $owner_write=(fileperms(A_ROOT) & 0x0080);
      $group_write=(fileperms(A_ROOT) & 0x0010);
      $world_write=(fileperms(A_ROOT) & 0x0002);   

      $user_info=posix_getpwuid(posix_getuid());
      $file_gourp=posix_getgrgid(filegroup(A_ROOT));
      $file_user=posix_getpwuid(fileowner(A_ROOT));
      /*Check world have rights to write*/

      /*
      if(!$world_write){
         /*Check owner is apache user and he have rights to write*/
         /*
         if(!($user_info['name']==$file_info['name'] && $owner_write)){
            echo "Please set write permision to ".A_ROOT." for the user ".$user_info['name']."\n";
            echo "chmod o+w ".A_ROOT."; or\n";
            echo "chown www-data ".A_ROOT.";\nchmod u+w ".A_ROOT.";";
            return;
         }
      }
      */

      /*Check file permission to write*/
      if(!is_writable(A_ROOT)){
         $user_info=posix_getpwuid(posix_getuid());
         echo "Please set write permision to ".A_ROOT." for the user ".$user_info['name']."\n";
         return;
      }

      /*connect to the mysql database server. */
      $con             = mysql_connect($_REQUEST['db_host'],"root",$_REQUEST['db_root']);

      /*create the database. */
      $created         = mysql_query("CREATE DATABASE ".$_REQUEST['db_name'],$con);

      /*add user to access the database. */
      $granted         = mysql_query("GRANT ALL ON ".$_REQUEST['db_name'].".* TO ".$_REQUEST['db_user']."@".$_REQUEST['db_host']." IDENTIFIED BY '".$_REQUEST['db_password']."'",$con);

      /*Print errors*/
      if(!$con){
         echo "<li> Database connection failed! \n<li> Please recheck the host name and root's password of the database...\n";
      }elseif(!$created){
         echo "<li> Database creation failed!\n";
      }elseif(!$granted){
         echo "<li> Database user creation failed!\n";
      }

      /*Creating the configuration if the database creation and user adding is successful*/
      if($created && $granted){
         echo "<li> Successfully created the database and user added!\n";

         /*Applying current configuration */
         $GLOBALS['DB']      = $_REQUEST['db_name'];
         $GLOBALS['DB_HOST'] = $_REQUEST['db_host'];
         $GLOBALS['DB_USER'] = $_REQUEST['db_user'];
         $GLOBALS['DB_PASS'] = $_REQUEST['db_password'];
 
         /*Database configuration string*/
         $db_config      ="<?php 
\$GLOBALS['DB']      = '".$GLOBALS['DB']     ."';
\$GLOBALS['DB_HOST'] = '".$GLOBALS['DB_HOST']."';
\$GLOBALS['DB_USER'] = '".$GLOBALS['DB_USER']."';
\$GLOBALS['DB_PASS'] = '".$GLOBALS['DB_PASS']."';
?>";

         /*Creating system table set*/
         create_system_tables();
         $con             = mysql_connect($GLOBALS['DB_HOST'],$GLOBALS['DB_USER'],$GLOBALS['DB_PASS']);
         $admin_add      = mysql_query("INSERT INTO ".$GLOBALS['DB'].".users(username,password,permission,theme,layout)values('admin',md5('admin'),'SUPER','claro','app')",$con);
         echo "<li> Successfully added the administrator with SUPER power!\n";
         echo "<ul><li>Username:admin<li>Password:admin</ul>\n";
         echo "<li><font color='red'>PLEASE CHANGE THE PASSWORD OF THE ADMINISTRATOR AT THE FIRST LOGIN </font>\n";
      
         /*Write configuration to the file*/
         $file_handler = fopen(DB_CONF, 'w');
         fwrite($file_handler, $db_config);
         fclose($file_handler);

         if(file_exists(DB_CONF)){
            echo "<li> Successfully saved the configuration!";
         }else{
            echo "<li> Failed Creating Configuration file in ".A_ROOT."!";
         }
      }
   }else{
      echo "<li>Required fields are not provided!";
   }
return;
}

/*If no installation request received show the data entry form of the databse creation*/
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
   <head>
      <title>UCSCSIS INSTALLER</title>
      <link rel="search" href="/search" >

<!--_________________________________CSS_____________________________________-->
      <?php 
         include A_CORE."/style.php";
      ?>
<!--______________________________FAVICON____________________________________-->
      <link rel="shortcut icon" href="<?php echo $GLOBALS['FAVICON']; ?>"type="image/x-icon" >

<!--______________________DOJO JAVASCRIPT load modules_______________________-->
      <?php 
         include A_CORE."/loading.php";
         include A_CORE."/dojo_require.php";
      ?>
<script type='text/javascript' >
   //content:"<ul>"+msg+"</ul><br><center><button dojoType='dijit.form.Button' onClick=\"stausDialog.hide();window.open('<?php echo W_ROOT;?>','_self')\" type='button'>OK</button></center>"
   function status_dialog(msg){
      stausDialog = new dijit.Dialog({
         title: "Status report",
         style: "width: 600px;",
         content:"<ul>"+msg+"</ul><br><center><button dojoType='dijit.form.Button' onClick=\"stausDialog.hide()\" type='button'>OK</button></center>"
      });
      stausDialog.show();
   }
   function submit_form(action){
      if(dijit.byId('install_frm').validate()) {
         if(dijit.byId('db_password').value == dijit.byId('verify_password').value){
            dojo.xhrGet({
               url         : '?action='+action, 
               handleAs      : 'text',
               form         : 'install_frm', 
         
               handle: function(response){
                  //dijit.byId('".$table."_frm').attr('value', response); 
                  status_dialog(response);
                  //alert(response);
               },
               load: function(response) {
                  //alert('Form successfully submitted');
               }, 
               error: function() {
                  alert('Error on submission');
               }
            });
         return false;
      }else{
         alert('Verification password does not match!');
         return false;
      }
   }else{
      alert('Form contains invalid data.  Please correct first');
      return false;
   }
   return true;
}

function show_dialog(){
   dijit.byId("license_dialog").show();
   dijit.byId('btn_install').attr('disabled',false);
}

</script>
<style type='text/css'>
.f_label{text-align:right;}
.field{width:100px;}
</style>

   </head>
<!--_____________________BODY with dojo border container_____________________-->
   <body class="<?php echo $GLOBALS['THEME']; ?>" >
<!--__________________________start loading ________________________________-->
   <?php
      d_r('dijit.layout.AccordionContainer');
      d_r("dijit.layout.AccordionPane");
      d_r("dijit.form.ValidationTextBox");
      d_r("dijit.form.Form");
      d_r("dijit.form.Button");
      d_r("dijit.Dialog");
   ?>
<!--____________________________end loading ________________________________-->

<center>
      <div style="width:350px;height:380px;padding-top:100px;">
      <div dojoType="dijit.layout.AccordionContainer" style="height:460px;" >
      <div dojoType="dijit.layout.AccordionPane" title="Install UCSCSIS">
         <p style="text-ailgn:left;font-weight:bold">
         University of Colombo Schoool of Computing Student Information System Installation Panel.
         Please read the license before installing the system.
         </p>
           <div dojoType="dijit.form.Form" jsId="install_frm" id="install_frm"> 
            <table cellpadding=0 cellspacing=5>
               <tr>
                  <td class=f_label width=200><label for=db_host >Database Host</label></td>
                  <td class=field><input id=db_host name=db_host type=text dojoType="dijit.form.ValidationTextBox"  required='true' ></td>
               </tr>
               <tr>
                  <td class=f_label><label for=db_root >Root Password</label></td>
                  <td class=field><input id=db_root name=db_root type=password dojoType="dijit.form.ValidationTextBox" required='true' ></td>
               </tr>
               <tr>
                  <td colspan=2><hr></td>
               </tr>
               <tr>
                  <td class=f_label><label for=db_name>Database</label></td>
                  <td class=field><input dojoType="dijit.form.ValidationTextBox" type=text id=db_name name=db_name required='true' ></td>
               </tr>
               <tr>
                  <td class=f_label><label for=db_user>Database User Name</label></td>
                  <td class=field><input dojoType="dijit.form.ValidationTextBox" type=text id=db_user name=db_user required='true' ></td>
               </tr>
               <tr>
                  <td class=f_label><label for=db_password >Database Password</label></td>
                  <td class=field><input id=db_password  name=db_password type=password dojoType="dijit.form.ValidationTextBox" required='true' ></td>
               </tr>
                  <td class=f_label><label for=verify_password >Verify Password</label></td>
                  <td class=field><input id=verify_password type=password dojoType="dijit.form.ValidationTextBox" required='true' ></td>
               </tr>
               <tr><td colspan=2><hr></td></tr>
               <tr>
                  <td align='center' colspan='2'>
                     <button dojoType="dijit.form.Button" onClick="show_dialog()" type="button">
                     Read the   License before install
                     </button>
                     <button dojoType="dijit.form.Button" onClick="submit_form('install')" type="button" jsId='btn_install' id='btn_install' disabled=true>
                        Install
                     </button>
                  </td>
               </tr>
            </table>

         </div> <!-- end form -->
         </div> <!-- end accordianpane -->
         </div><!-- end accordiancontainer -->
         </div><!-- end sizer div -->
<div id="license_dialog" dojoType="dijit.Dialog" title="LICENSE" style="width:580px;height:450px;padding:0px;">
<div style="height:400px;width:540px;padding:10px;overflow:scroll">
<?php include "LICENSE";?>
</div>
</div>

</center>
<!--_______________________________parse dojo________________________________-->
      <?php parse_dojo(); ?>
</body>
</html>
