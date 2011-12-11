<?php 
d_r('dijit.Dialog');
d_r('dijit.form.Button');
?>
<script language='javascript'>
   
   /*Update status bar*/
   var max_status_length=150;
   var stausDialog;

   function update_status_bar(status_code,info){
      //Sometimes status_code and info is not defined due to json decode error
      if(typeof status_code === 'undefined' || typeof info === 'undefined') {
         status_code='NOT_DEFINED';
         info='Undefined error!';
      }

      status_code=status_code.toUpperCase();
      var orig_info=info;
      switch(status_code){
         case 'OK':
            info=info;
         break;
         case 'ERROR':
            info="<font color='red'>"+info+"</font>";
            <?php if($GLOBALS['LAYOUT']!='pub'){ ?>
            busy.innerHTML="<img src='<?php echo IMG."/busy-stopped.gif"; ?>' >";
            <?php }?>
         break;
         case 'NOT_DEFINED':
            info="<font color='orange'>"+info+"</font>";
         break;
         default:
            info="<font color='green'>"+info+"</font>";
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
   
      <?php if($GLOBALS['LAYOUT']!='pub' ){ ?>
      jsProgress.update({maximum: 100, progress: progress });
      <?php } ?>
      var busy = document.getElementById('busy') ;
      if(progress == 100 || progress == 0){
         busy.innerHTML="<img src='<?php echo IMG."/busy-stopped.gif"; ?>' >";
      }else{
         busy.innerHTML="<img src='<?php echo IMG."/busy.gif"; ?>' >";
      }
   }

</script>
