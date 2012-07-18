<?php
$role='NULL';
if(isset($_SESSION['role_id'])){
   $role=$_SESSION['role_id'];
}

$news_tpl="";
$arr=exec_query("SELECT * FROM ".s_t('news')." WHERE display_until >= curdate() AND display_from <= curdate() AND role_id='$role'",Q_RET_ARRAY);
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

if(trim($news) ==''){
   $news ='No news available to display';
}

$news="<h3>News</h3><table width='100%'>".$news."</table>";

add_to_main_top($news);
set_layout_property('app2','MAIN_TOP','style','height','100%');
set_layout_property('app2','MAIN_BOTTOM','style','height','0%');
?>
