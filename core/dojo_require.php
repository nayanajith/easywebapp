<script type='text/javascript'> 
<!--
/*
parseOnLoad:true -> parse dojo while loading
parseOnLoad:false -> dojo will not parse while loading 
It is possible to parse after loading using dojo.parser.parse();
check in (index.php or loading.php)
*/

djConfig = {
   isDebug:true, 
   parseOnLoad:true
}; 
-->
</script>
<!--script src="http://ajax.googleapis.com/ajax/libs/dojo/1.7.2/dojo/dojo.js"></script-->
<script src="<?php echo JS; ?>/dojo/dojo.js" type='text/javascript'></script>
<script src="<?php echo JS; ?>/dojo/ucscis.js" type='text/javascript'></script>
<script src="<?php echo JS; ?>/dijit/ucscis.js" type='text/javascript'></script>
<script src="<?php echo JS; ?>/dojox/ucscis.js" type='text/javascript'></script>
<script src="<?php echo gen_url() ?>action=js&form=main" type='text/javascript'></script>
<script src="<?php echo gen_url() ?>section=DYNAMIC_JS" type='text/javascript' id='DYNAMIC_JS'></script>


<?php
d_r("dijit.MenuBar");
d_r("dijit.Menu");
d_r("dijit.MenuItem");
d_r("dijit.PopupMenuBarItem");
d_r("dijit.DropDownMenu");
d_r("dijit.layout.ContentPane");
d_r("dijit.Toolbar");
d_r('dijit.form.NumberTextBox');
d_r('dijit.form.ValidationTextBox');
d_r('dijit.form.Select');
d_r('dijit.form.FilteringSelect');
d_r('dijit.form.DateTextBox');
d_r('dijit.form.TimeTextBox');
d_r('dijit.form.Form');
d_r('dijit.TooltipDialog');
d_r('dijit.form.CheckBox');
d_r('dijit.form.DropDownButton');
d_r('dijit.ProgressBar');
d_r('dijit.Dialog');
d_r('dijit.form.Button');
d_r('dijit.Toolbar');
d_r('dijit.layout.BorderContainer');
d_r('dijit.MenuBar');
d_r('dijit.Menu');
d_r('dijit.MenuItem');
d_r('dijit.PopupMenuBarItem');
d_r('dijit.DropDownMenu');
d_r('dijit.layout.ContentPane');
d_r('dijit.form.NumberSpinner');
d_r('dijit.form.SimpleTextarea');
d_r('dijit.form.ComboBox');
d_r('dijit.TitlePane');
d_r('dijit.Editor');
d_r('dijit.popup');
d_r('dijit._editor.plugins.FontChoice');
d_r('dijit._editor.plugins.TextColor');
d_r('dijit._editor.plugins.LinkDialog');

d_r('dojox.grid.EnhancedGrid');
d_r('dojox.grid.enhanced.plugins.Pagination');
d_r('dojox.grid.enhanced.plugins.NestedSorting');
d_r('dojox.grid.enhanced.plugins.Printer');
d_r('dojox.grid.enhanced.plugins.exporter.CSVWriter');
d_r('dojox.grid.enhanced.plugins.exporter.TableWriter');
d_r('dojox.form.PasswordValidator');
d_r('dojox.widget.PlaceholderMenuItem');
d_r('dojox.data.QueryReadStore');
d_r('dojox.data.JsonRestStore');
d_r('dojox.widget.PlaceholderMenuItem');
d_r('dojox.grid.EnhancedGrid');
d_r('dojox.grid.enhanced.plugins.Pagination');
d_r('dojox.grid.enhanced.plugins.Filter');
d_r("dojox.grid.enhanced.plugins.NestedSorting");
d_r("dojox.grid.enhanced.plugins.Printer");
d_r("dojox.grid.enhanced.plugins.exporter.CSVWriter");
d_r("dojox.grid.enhanced.plugins.exporter.TableWriter");
d_r("dojox.form.Uploader");
d_r("dojox.form.uploader.plugins.IFrame");
//d_r("dojox.form.MultiComboBox");

d_r('dojo.cookie');
d_r('dojo.query');
d_r('dojo.io.iframe');
d_r("dojo.fx");


/*call this function in page where you want to parse dojo javascript*/
/*$dojo_required was generated in core/dojo_require.php*/
function parse_dojo(){
   global $dojo_required;
   $dojo_required[]='dojo.parser';
   $req="";
   foreach($dojo_required as $module){
      $req.="dojo.require('$module');\n";
   }

$html = <<<EOD
<script type='text/javascript'>
<!--
/*----------------------Parse dojo after loading the page----------------------*/
/*set djConfig=parseOnload:false and this will parse after loading*/
$req
dojo.addOnLoad(function() {
   var start = +new Date();

   /*Parse dojo for total page and keep the loading message until parsing*/
   //dojo.parser.parse();

   /*Printing loading time to the firebug console*/
   console.info("Total parse time: " + (+new Date() - start) + "ms");

   /*print done and fade out the loading message and hide*/
   //dojo.byId('loaderInner').innerHTML += " done.";
   setTimeout(function hideLoader(){
      dojo.fadeOut({ 
         node: 'loader', 
         duration:200,
         onEnd: function(n){
            n.style.display = "none";
         }
      }).play();
   }, 250);
});

-->
</script>
EOD;

echo $html;
}
?>
