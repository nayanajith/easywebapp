<?php
if(isset($_REQUEST['form']) && isset($_REQUEST['action'])){
   //include MOD_CLASSES."/degree_cert_fpdf_class.php";
   include MOD_CLASSES."/degree_cert_pdf_class.php";
   $degree_cert=new Degree_cert();
   //insert the content to the pdf

   $name_in_en="DEVAPURAGE MANOJ NILANGA FERNANDO";
   $date_in_ta="DATE";
   $name_in_si="දේවපුරගේ මනෝජ් නිලංග ප්‍රනාන්දු";
   $date_in_si="DATE";
   $name_in_ta="தேவபுறகே  மனோஜ்  நிலங்க  பெர்னாண்டோ";
   $date_in_ta="DATE";

   
   $degree_cert->include_content($name_in_en,$date_in_ta,$name_in_si,$date_in_si,$name_in_ta,$date_in_ta);

   //Acquire pdf document
   $pdf=$degree_cert->getPdf();

   //Return pdf document to the output stream
   $pdf->Output('test_pdf.pdf', 'I');
}

?>
<script type='text/javascript' >
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
