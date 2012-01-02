<?php
/*
This will include in to $GLOBALS['PAGE_GEN']
*/
$ENABLE=false;
$html=<<<EOF
<div dojoType="dijit.MenuBar" id="navMenu" >
   <div dojoType="dijit.PopupMenuBarItem">
      <span>
      File
      </span>
      <div dojoType="dijit.Menu" id="fileMenu">
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
      <div dojoType="dijit.Menu" id="editMenu">
         <div dojoType="dijit.MenuItem" onClick="alert('edit 1')">
         Edit #1
         </div>
         <div dojoType="dijit.MenuItem" onClick="alert('edit 2')">
         Edit #2
         </div>
      </div>
   </div>
</div>
EOF;

if($ENABLE){
d_r("dijit.MenuBar");
d_r("dijit.Menu");
d_r("dijit.MenuItem");
d_r("dijit.PopupMenuBarItem");

echo $html;
}

?>
