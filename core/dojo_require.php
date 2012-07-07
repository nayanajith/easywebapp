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
<script src="<?php echo gen_url() ?>action=js_d&form=main" type='text/javascript'></script>
<?php


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
   dojo.byId('loaderInner').innerHTML += " done.";
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
