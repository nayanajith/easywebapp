<?php
/*
This will include in to $GLOBALS['PAGE_GEN']
*/
d_r('dojo.data.ItemFileReadStore');
d_r('dijit.tree.ForestStoreModel');
d_r('dijit.Tree');
?>
<!--Load JSON file into ItemFileReadStore-->
<span    dojoType="dojo.data.ItemFileReadStore" 
      jsId="moduleTreeStore" 
      url="<?php echo $GLOBALS['PAGE_GEN']."?data=json&mod_tree=true"; ?>">
</span>

<!--Load JSON file from ItemFileReadStore into ForestStoreModel-->
<span    dojoType="dijit.tree.ForestStoreModel" 
      jsId="moduleTreeModel" 
      store="moduleTreeStore"
      query="{type:'module'}" 
      rootId="moduleTreeRoot" 
      rootLabel="Modules"
      childrenAttrs="children">
</span>


<div dojoType="dijit.Tree" id="moduleTree" model="moduleTreeModel" showRoot="false" openOnClic="false">
   <script type="dojo/method" event="onClick" args="item">
      //Open the requested module/page from tree
      if(moduleTreeStore.getValue(item, "url")){
         //url="<?php echo $GLOBALS['PAGE_GEN']; ?>?"+moduleTreeStore.getValue(item, "url")+"&program=<?php echo PROGRAM; ?>&xhr=true";
         url="<?php echo W_ROOT."/".$GLOBALS['PAGE_GEN']; ?>?"+moduleTreeStore.getValue(item, "url")+"&program=<?php echo PROGRAM; ?>";
         open(url,'_self');

         //ajax
         /*
         dojo.xhrGet({
            url: url, 
            //handleAs:'json',
            handleAs:'text',

            handle: function(response){
               var data_body=document.getElementById('data_body');
               data_body.innerHTML=response;
               var frm= dijit.byId('bcsc_student');
               frm.startup();
            },

            load: function(response) {
               //alert('Form successfully submitted');
            }, 
            error: function() {
               alert('Error on submission');
            }
         });
         */
      }
   </script>
   <script type="dojo/method" event="onLoad" args="item">
      //Prefocus the actieve module/page onload
      var mt = dijit.byId('moduleTree');
      mt.attr('path', ['moduleTreeRoot', '<?php echo MODULE; ?>', '<?php echo MODULE; ?>_<?php echo PAGE; ?>']);
   </script>
</div>

