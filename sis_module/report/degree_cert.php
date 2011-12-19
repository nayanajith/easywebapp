<?php
if(isset($_REQUEST['form']) && isset($_REQUEST['action'])){
   //include MOD_CLASSES."/degree_cert_fpdf_class.php";
   $img_file = MOD_W_RESOURCE.'/bit_cert_front.jpg';
   echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' >";
   echo "<img src='$img_file' style='width:297mm;height:420mm;position:absolute;left:0mm;top:0mm;'>";
   //insert the content to the pdf

   $name_in_en="DEVAPURAGE MANOJ NILANGA FERNANDO";
   $date_in_ta="DATE";
   $name_in_si="දේවපුරගේ මනෝජ් නිලංග ප්‍රනාන්දු";
   $date_in_si="DATE";
   $name_in_ta="தேவபுறகே  மனோஜ்  நிலங்க  பெர்னாண்டோ";
   $date_in_ta="DATE";

   echo "<div style='position:absolute;top:170mm;text-align:center;width:285mm;font-family:lklug;font-size:22px;' >$name_in_si</div>";
}

?>
<script type="text/javascript" >
   function submit_form(action){
      update_status_bar('...');
      update_progress_bar(10);
      if(action == 'generate'){
         //window.open('<?php echo gen_url(); ?>&form=main&action='+action,'stat','location=0,status=0,scrollbars=1,width=800,height=600');
         window.open('<?php echo gen_url(); ?>&form=main&action='+action,'stat');
         return;
      }
   }
</script>
