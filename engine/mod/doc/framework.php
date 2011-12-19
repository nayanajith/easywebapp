<html>
<style type="text/css">
p{
   text-align:justify;
}
</style>
   <body>
      <ul>
         <li><a href='#installation'>Installation</a></li>   
         <li><a href='#engine'>Engine</a></li>   
         <li><a href='#index'>Index</a></li>   
         <li><a href='#configuration'>Configuration</a></li>   
         <li><a href='#themes'>Themes</a></li>   
         <li><a href='#layouts'>Layouts</a></li>   
         <ul>
            <li><a href='#pub_Layout'>PUB Layout</a></li>   
            <li><a href='#web_Layout'>WEB Layout</a></li>   
            <li><a href='#app_Layout'>APP Layout</a></li>   
         </ul>
         <li><a href='#modules_and_pages'>Modules and Pages</a></li>   
         <li><a href='#sis_Module'>SIS Module</a></li>   
         <li><a href='#uis_Module'>UIS Module</a></li>   
         <ul>
            <li><a href='#module_architecture'>Module Architecture</a></li>   
            <li><a href='#configuration'>Configuration</a></li>   
            <li><a href='#menu'>Menu</a></li>   
            <li><a href='#database_schema'>Database Schema</a></li>   
            <li><a href='#database_mapping'>Database Mapping</a></li>   
         </ul>
         <li><a href='#security'>Security</a></li>   
         <ul>
            <li><a href='#sql_injection_prevention'>SQL Injection Prevention</a></li>   
            <li><a href='#apache_authorization'>Apache Authorization</a></li>   
            <li><a href='#module_page_permission'>Module/Page Permission</a></li>   
            <li><a href='#authentication'>Authentication</a></li>   
            <ul>
               <li><a href='#local_system_authentication'>Local System Authentication</a></li>   
               <li><a href='#local_module_authentication'>Local Module Authentication</a></li>   
               <li><a href='#external_moodle_authentication'>External/moodle Authentication</a></li>   
               <li><a href='#ldap_zimbra_authentication'>LDAP/Zimbra Authentication</a></li>   
            </ul>
         </ul>
         <li><a href='#automations'>Automations</a></li>   
         <ul>
            <li><a href='#form_generation'>Form Generation</a></li>   
            <li><a href='#form_validation'>Form Validation</a></li>   
            <li><a href='#form_tool_tip_help'>Form Tool tip Help</a></li>   
            <li><a href='#data_grids'>Data Grids</a></li>   
            <li><a href='#data_sheets_csv'>Data Sheets(csv)</a></li>   
            <li><a href='#pdf_generation'>PDF Generation</a></li>   
            <li><a href='#xhr_combo_boxes'>XHR Combo Boxes</a></li>   
         </ul>
         <li><a href='#javascript_integration'>Javascript Integration</a></li>   
         <ul>
            <li><a href='#dojo'>DOJO</a></li>   
            <li><a href='#dojo_optimization'>DOJO Optimization</a></li>   
            <li><a href='#dojo_custom_build'>DOJO Custom Build</a></li>   
         </ul>
         <li><a href='#third_party_php_libraries'>Third party PHP Libraries</a></li>   
         <ul>
            <li><a href='#tcpdf'>TCPDF</a></li>   
         </ul>
         <li><a href='#debugging'>Debugging</a></li>   
         <ul>
            <li><a href='#errors'>Errors</a></li>   
            <li><a href='#activity_log'>Activity Log</a></li>   
         </ul>
      </ul>
<!-- end of menu and starting of the documentation -->
<hr>

<h2><a name='installation'>Installation</a>   </h2>
<p>Installation is done by invoking /install/install.php script. This will open a form which request the Database host, Database root password, name for the new database, username for the new database, and the password for the new database. After entering the required information and accepting the license you can proceed with the installation. This will create the system database structure and the information of the database connection will be saved in /core/db_confg.php.  The user who runs php should be able to write to the /core directory to write the database configuration. The installation will create the default admin user with the username: admin and the password: admin. After the successful installation you have to reload the page to start the system</p>
<h2><a name='engine'>Engine</a>   </h2>
<p>All the con</p>
<h2><a name='index'>Index</a>   </h2>
<p></p>
<h2><a name='configuration'>Configuration</a>   </h2>
<p></p>
<h2><a name='themes'>Themes</a>   </h2>
<p></p>
<h2><a name='layouts'>Layouts</a>   </h2>
<p></p>
<h3><a name='pub_Layout'>PUB Layout</a>   </h3>
<p></p>
<h3><a name='web_Layout'>WEB Layout</a>   </h3>
<p></p>
<h3><a name='app_Layout'>APP Layout</a>   </h3>
<p></p>
<h2><a name='modules_and_pages'>Modules and Pages</a>   </h2>
<p></p>
<h2><a name='sis_Module'>SIS Module</a>   </h2>
<p></p>
<h2><a name='uis_Module'>UIS Module</a>   </h2>
<p></p>
<h3><a name='module_architecture'>Module Architecture</a>   </h3>
<p></p>
<h3><a name='configuration'>Configuration</a>   </h3>
<p></p>
<h3><a name='menu'>Menu</a>   </h3>
<p></p>
<h3><a name='database_schema'>Database Schema</a>   </h3>
<p></p>
<h3><a name='database_mapping'>Database Mapping</a>   </h3>
<p></p>
<h2><a name='security'>Security</a>   </h2>
<p></p>
<h3><a name='sql_injection_prevention'>SQL Injection Prevention</a>   </h3>
<p></p>
<h3><a name='apache_authorization'>Apache Authorization</a>   </h3>
<p></p>
<h3><a name='module_page_permission'>Module/Page Permission</a>   </h3>
<p></p>
<h3><a name='authentication'>Authentication</a>   </h3>
<p></p>
<h4><a name='local_system_authentication'>Local System Authentication</a>   </h4>
<p></p>
<h4><a name='local_module_authentication'>Local Module Authentication</a>   </h4>
<p></p>
<h4><a name='external_moodle_authentication'>External/moodle Authentication</a>   </h4>
<p></p>
<h4><a name='ldap_zimbra_authentication'>LDAP/Zimbra Authentication</a>   </h4>
<p></p>
<h2><a name='automations'>Automations</a>   </h2>
<p></p>
<h3><a name='form_generation'>Form Generation</a>   </h3>
<p></p>
<h3><a name='form_validation'>Form Validation</a>   </h3>
<p></p>
<h3><a name='form_tool_tip_help'>Form Tool tip Help</a>   </h3>
<p></p>
<h3><a name='data_grids'>Data Grids</a>   </h3>
<p></p>
<h3><a name='data_sheets_csv'>Data Sheets(csv)</a>   </h3>
<p></p>
<h3><a name='pdf_generation'>PDF Generation</a>   </h3>
<p></p>
<h3><a name='xhr_combo_boxes'>XHR Combo Boxes</a>   </h3>
<p></p>
<h2><a name='javascript_integration'>Javascript Integration</a>   </h2>
<p></p>
<h3><a name='dojo'>DOJO</a>   </h3>
<p></p>
<h3><a name='dojo_optimization'>DOJO Optimization</a>   </h3>
<p></p>
<h3><a name='dojo_custom_build'>DOJO Custom Build</a>   </h3>
<p></p>
<h2><a name='third_party_php_libraries'>Third party PHP Libraries</a>   </h2>
<p></p>
<h3><a name='tcpdf'>TCPDF</a>   </h3>
<p></p>
<h2><a name='debugging'>Debugging</a>   </h2>
<p></p>
<h3><a name='errors'>Errors</a>   </h3>
<p></p>
<h3><a name='activity_log'>Activity Log</a>   </h3>
<p></p>

   </body>
</html>
