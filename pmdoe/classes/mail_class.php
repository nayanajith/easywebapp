<?php
class Mail_native{

   function __construct(){

   }   

   /**
   @param from : your email address
   @param to   : If multiple mail addresses provide an array or string for single email
   @param cc   : If multiple mail addresses provide an array or string for single email can be null
   @param bcc   : If multiple mail addresses provide an array or string for single email can be null
   @param subject : subject of the mail
   @param message : mail body HTML or plain text
   */
   function send_mail($from,$to,$cc=null,$bcc=null,$subject,$message){
      // To send HTML mail, the Content-type header must be set
      $headers  = "MIME-Version: 1.0\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

      $_TO="";
      if(is_array($to)){
         $_TO=implode(',',$to);
      }else{
         $_TO=$to;
      }

      // Additional headers
      $headers .= "To: $_TO"."\r\n";
      $headers .= "From: $from" . "\r\n";

      //adding cc to header
      $_CC="";
      if($cc != ''){
         if(is_array($cc)){
            $_CC=implode(',',$cc);
         }else{
            $_CC=$cc;
         }
         $headers .= "Cc: $_CC"."\r\n";
      }

      //adding bcc to header
      $_BCC="";
      if($bcc != ''){
         if(is_array($bcc)){
            $_BCC=implode(',',$bcc);
         }else{
            $_BCC=$bcc;
         }
         $headers .= "Bcc: $_BCC"."\r\n";
      }
      //sending mail
      $mail_sent = mail($_TO, $subject, $message, $headers);
      return $mail_sent;
   }
function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    $file = $path."/".$filename;
/*
   $finfo      =finfo_open(FILEINFO_MIME_TYPE); 
   $mime_type   =finfo_file($finfo, $file);
*/
    $content = chunk_split(base64_encode(file_get_contents($file)));
    $uid = md5(uniqid(time()));
    $name = basename($file);
    $header = "From: ".$from_name." ".$from_mail."\r\n";
    $header .= "Reply-To: ".$replyto."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-type:text/html; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message."\r\n\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
    $header .= $content."\r\n\r\n";
    $header .= "--".$uid."--";

   //log_msg('mail',$header);

    return mail($mailto, $subject, "", $header);
}

/*
//--HELP--
$my_file = "somefile.zip";
$my_path = $_SERVER['DOCUMENT_ROOT']."/your_path_here/";
$my_name = "Olaf Lederer";
$my_mail = "my@mail.com";
$my_replyto = "my_reply_to@mail.net";
$my_subject = "This is a mail with attachment.";
$my_message = "Hallo,\r\ndo you like this script? I hope it will help.\r\n\r\ngr. Olaf";
mail_attachment($my_file, $my_path, "recipient@mail.org", $my_mail, $my_name, $my_replyto, $my_subject, $my_message);
*/
}
?>

