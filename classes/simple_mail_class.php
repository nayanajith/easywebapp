<?php
class SMTP_Client{

   function SMTP_Client ($conf_arr){
      $this->SmtpServer = $conf_arr['host'];
      $this->SmtpUser    = base64_encode ($conf_arr['username']);
      $this->SmtpPass    = base64_encode ($conf_arr['password']);

      if ($conf_arr['port'] == ""){
         $this->PortSMTP = 25;
      }else{
         $this->PortSMTP = $conf_arr['port'];
      }
   }
                   

   function send_mail ($from, $to, $subject, $body){
      $this->from    = $from;
      $this->to       = $to;

      $this->subject = $subject;
      $this->body    = $body;

      if ($SMTPIN = fsockopen ($this->SmtpServer, $this->PortSMTP)){
         fputs ($SMTPIN, "EHLO ".$_SERVER['HTTP_HOST']."\r\n");  
         $talk["hello"] = fgets ( $SMTPIN, 1024 ); 
                   
         fputs($SMTPIN, "auth login\r\n");
         $talk["res"]=fgets($SMTPIN,1024);
         fputs($SMTPIN, $this->SmtpUser."\r\n");
         $talk["user"]=fgets($SMTPIN,1024);
          
         fputs($SMTPIN, $this->SmtpPass."\r\n");
         $talk["pass"]=fgets($SMTPIN,256);
                   
         fputs ($SMTPIN, "MAIL FROM: <".$this->from.">\r\n");  
         $talk["From"] = fgets ( $SMTPIN, 1024 );  
         fputs ($SMTPIN, "RCPT TO: <".$this->to.">\r\n");  
         $talk["To"] = fgets ($SMTPIN, 1024); 
           
         fputs($SMTPIN, "DATA\r\n");
         $talk["data"]=fgets( $SMTPIN,1024 );
           
         
         fputs($SMTPIN, "To: <".$this->to.">\r\nFrom: <".$this->from.">\r\nSubject:".$this->subject."\r\n\r\n\r\n".$this->body."\r\n.\r\n");
         $talk["send"]=fgets($SMTPIN,256);
           
         //CLOSE CONNECTION AND EXIT ... 
         
         fputs ($SMTPIN, "QUIT\r\n");  
         fclose($SMTPIN); 
      }  
      return $talk;
   }        
}
?>

