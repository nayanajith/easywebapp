<?php 
d_r('dijit.Dialog');
d_r('dijit.form.Button');
?>
<script language='javascript'>
   
   /*Update status bar*/
   var max_status_length=150;
   var stausDialog;
   //If the public kayout used set the javascript variable to used with
   var pub=false;
   <?php if($GLOBALS['LAYOUT']=='pub'){ ?>
   pub=true;
   <?php }?>

   function update_status_bar(status_code,info){
      //Sometimes status_code and info is not defined due to json decode error
      if(typeof status_code === 'undefined' || typeof info === 'undefined') {
         return;
         //status_code='NOT_DEFINED';
         //info='Undefined error!';
      }

      status_code=status_code.toUpperCase();
      var orig_info=info;

      //Set the color to the message according to the status
      switch(status_code){
         case 'OK':
         default:
            info=info;
         break;
         case 'ERROR':
            info="<font color='red'>"+info+"</font>";
            if(!pub){
               set_busy(false);
            }
         break;
         case 'NOT_DEFINED':
            info="<font color='orange'>"+info+"</font>";
         break;
      }
      <?php if($GLOBALS['LAYOUT']!='pub'){ ?>
      /*If the message too lengthy show it as a dialog*/
      if(info.length < max_status_length){
         var status_bar = document.getElementById('status_bar') ;
         status_bar.innerHTML=info;
         status_bar.title=orig_info;
      }else{
         /*Create dialog only if not initialized*/
         if(typeof stausDialog === 'undefined') {
            stausDialog = new dijit.Dialog({
               title: "Status report",
               style: "width: 400px;"
            });
         }
      
         var button="<br/><center><button dojoType='dijit.form.Button' onClick=\"stausDialog.hide()\" >OK</button></center>";
         stausDialog.attr("content", info+button);
         stausDialog.show();
      }
      <?php }else{ ?>
         /*Create dialog only if not initialized*/
         if(typeof stausDialog === 'undefined') {
            stausDialog = new dijit.Dialog({
               title: "Status report",
               style: "width: 400px;"
            });
         }
      
         var button="<br/><center><button dojoType='dijit.form.Button' onClick=\"stausDialog.hide()\" >OK</button></center>";
         stausDialog.attr("content", info+button);
         stausDialog.show();
      <?php } ?>
   }

   /*Update progress bar and busying image (rotating arrows)*/
   function update_progress_bar(progress){
      if(pub){
         return;
      }else{
         jsProgress.update({maximum: 100, progress: progress });
         if(progress == 100 || progress == 0){
            set_busy(false);
         }else{
            set_busy(true);
         }
      }
   }

   /*Update the busy state of the icon*/
   function set_busy(is_busy){
      if(pub){
         return;
      }else{
         var busy = document.getElementById('busy') ;
         if(is_busy){
            busy.innerHTML="<img src='<?php echo IMG."/busy.gif"; ?>' >";
         }else{
            busy.innerHTML="<img src='<?php echo IMG."/busy-stopped.gif"; ?>' >";
         }
      }
   }

</script>
