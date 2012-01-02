<?php
require_once "Mail.php";
class Ucsc_mail{
   protected $smtp;

   function __construct(){
      $this->smtp = Mail::factory('smtp',$GLOBALS['MAIL_CONF']);
   }

   
   function attach_file(){
   }

   
   function set_body2($tamplate,$merge_arr){
      $this->body=sprintf($tamplate,$merge_arr);
   }

   function set_body($body){
      $this->body=$body;
   }


   function send_mail($from,$to,$subject,$body){

       $headers = array (
         'From'       => $from,
         'To'          => $to,
         'Subject'    => $subject
      );

       $mail = $this->smtp->send($to, $headers, $body);
      $status=array();   

      /*Errors and status*/
       if (PEAR::isError($mail)) {
         $status['status']='ERROR';
         $status['info']=$mail->getMessage();
      }else{
         $status['status']='OK';
         $status['info']=$mail->getMessage();
      }
   }
}


$ucsc_mail= new Ucsc_mail();
$ucsc_mail->send_mail("nml@ucsc.lk","nmlaxaman@gmail.com","testsub","testBody");
?>
