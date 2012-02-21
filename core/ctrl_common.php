<?php
/**
//Sample array to provide all the configuration of the page

$GLOBALS['PAGE']=array(
   'name'                =>'acc_year';
   'table'               =>$GLOBALS['P_TABLES'][$NAME];
   'primary_key'         =>'id';
   'filter_table'        =>$GLOBALS['P_TABLES']['filter'];
   'filter_primary_key'  =>'id';
);
*/

include A_CLASSES."/model_class.php";


//DEBUG: find where a class is declared
/*
$reflector = new ReflectionClass('Model');
echo $reflector->getFileName();
echo $reflector->getStartLine();
 */


$model    = new Model(
   $GLOBALS['PAGE']['table'],
   $GLOBALS['PAGE']['primary_key'],
   $GLOBALS['PAGE']['name'],
   $GLOBALS['PAGE']['filter_table'],
   $GLOBALS['PAGE']['filter_primary_key']
);


if(isset($_REQUEST['form']) && isset($_REQUEST['action'])){
   switch($_REQUEST['form']){
   case 'main':
      switch($_REQUEST['action']){
         case 'add':
           return $model->add_record();
         break;
         case 'modify':
           return $model->modify_record();
         break;
         case 'delete':
           return $model->delete_record(true);
         break;
      }
   break;
   case 'filter':
      switch($_REQUEST['action']){
         case 'add':
           return $model->add_filter();
         break;
         case 'modify':
           return $model->modify_filter();
         break;
         case 'delete':
           return $model->delete_filter(null,true);
         break;
      }
   }
}elseif(!isset($_REQUEST['id'])){
   include A_CLASSES."/view_class.php";
   $view = new View($GLOBALS['PAGE']['table'],$GLOBALS['PAGE']['name']);
   $view->gen_form();
}
?>
