<?php
/** 
Use markdown [http://daringfireball.net/projects/markdown/syntax] syntax to write the documentation
Markdown is a text-to-HTML conversion tool for web writers
Markdown allows you to write using an easy-to-read, easy-to-write plain text format
*/
$doc=<<<EOS
Intorudction common layout of the system
========================================
Main Toolbar
------------

Menubar
-------

Page Toolbar
------------

StatusBar
---------
EOS;


include_once "markdown.php";
echo Markdown($doc);

?>
