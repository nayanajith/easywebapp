<?php
/*
This will include in to $GLOBALS['PAGE_GEN']
*/
$html=<<<EOF
   <div dojoType="dijit.PopupMenuBarItem">
      <span>
      File
      </span>
      <div dojoType="dijit.Menu" id="fileMenuR">
         <div dojoType="dijit.MenuItem" onClick="alert('file 1')">
         File #1
         </div>
         <div dojoType="dijit.MenuItem" onClick="alert('file 2')">
         File #2
         </div>
      </div>
   </div>
   <div dojoType="dijit.PopupMenuBarItem">
      <span>
         Edit
      </span>
      <div dojoType="dijit.Menu" id="editMenuR">
         <div dojoType="dijit.MenuItem" onClick="alert('edit 1')">
         Edit #1
         </div>
         <div dojoType="dijit.MenuItem" onClick="alert('edit 2')">
         Edit #2
         </div>
      </div>
   </div>
   <div dojoType="dijit.PopupMenuBarItem">
      <span>
        login 
      </span>
      <div dojoType="dijit.Menu" id="loginMenuR">
         <div dojoType="dijit.MenuItem" onClick="alert('edit 1')">
         Edit #1
         </div>
         <div dojoType="dijit.MenuItem" onClick="alert('edit 2')">
         Edit #2
         </div>
      </div>
   </div>

EOF;
echo $html;

?>
