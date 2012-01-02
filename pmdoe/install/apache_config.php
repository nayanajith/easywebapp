<?php

$html = <<<EOD
Alias /ucscsis "/path/to/system/dir"
<Directory "/path/to/system/dir">
   #Ignore indexing while allowing symlink following
   Options -Indexes MultiViews FollowSymLinks
   AllowOverride None

   #Disable access from un authorized ips
   Order deny,allow
   Deny from all
   Allow from 127.0.0.0/255.0.0.0 ::1/128

   #Further ignore index
   IndexIgnore *

   #Disable all php file access
   <FilesMatch "\.(php|log)$">  
      Order Deny,Allow
      Deny from All
   </FilesMatch>

   #Allow only *index.php|html files and (eg: web_index.php and app_index.php )
   <FilesMatch "\index.(php|html)$">  
      Order Allow,Deny
      Allow from All
   </FilesMatch>
</Directory>
EOD;
?>
