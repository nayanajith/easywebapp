<?php
//Select effective theme
$theme=$GLOBALS['THEME'];
if(isset($_SESSION['THEME'])){
   $theme=$_SESSION['THEME'];
}

echo "<style type='text/css'>
	@import '".JS."/dijit/themes/".$theme."/".$theme.".css';

	@import '".JS."/dojox/grid/resources/".$theme."Grid.css';
	@import '".JS."/dojox/grid/resources/Grid.css';

   @import '".JS."/dojox/grid/enhanced/resources/".$theme."/EnhancedGrid.css';
   @import '".JS."/dojox/grid/enhanced/resources/EnhancedGrid_rtl.css';

	@import '".CSS."/common_css.php';
</style>"
?>
