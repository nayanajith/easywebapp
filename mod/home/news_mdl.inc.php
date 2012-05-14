<?php
$news_tpl="";
$arr=exec_query("SELECT * FROM ".s_t('news')." WHERE display_to >= curdate() ",Q_RET_ARRAY);
$news="";
foreach($arr as $row){
   $news.="
<tr>
   <td align='left' class='news_title'>
      ".$row['title']."
   </td>
</tr>
<tr>
   <td class='news_body'>
      <p>".$row['content']."</p>
      <div style='float:right;color:gray;'>Posted on ".$row['display_from']."</div>
   </td>
</tr>
";

}

$news="<table width='100%'>".$news."</table>";



add_to_main_bottom($news);
?>
