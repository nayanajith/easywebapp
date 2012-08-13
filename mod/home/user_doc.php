<?php
$GLOBALS['PAGE']=array(
   'name'                =>'user_doc',
   //'table'               =>s_t('user_doc'),
   'table'               =>null,
);
/*
(
    [rid] => 5
    [module_id] => home
    [page_id] => news_manage
    [doc] => Manage news \n====================\n\n* News can be added for each role, for display in a given duration.  If the role is not selected, then all the users can read that news \n* Use WYSIWYG editor to format the news as a rich content. It will be saved as HTML.\n* News will be automatically disappear as expire according to the given duration.\n\n
    [form] => main
    [action] => modify
    [program] => bcsc
    [module] => home
    [page] => user_doc
)
*/

if(isset($_REQUEST['form'])){
   if(isset($_REQUEST['action'])){
      switch($_REQUEST['action']){
      case 'add':
        add_help();
        unset($_REQUEST['action']);
      break;
      case 'modify':
        save_help();
        unset($_REQUEST['action']);
      break;
      case 'delete':
        delete_help();
        unset($_REQUEST['action']);
      break;
      }
   }
}

//Common control swithces included
include A_CORE."/ctrl_common.php";

?>
