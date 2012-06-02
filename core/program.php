<?php
/*
This will include in to $GLOBALS['PAGE_GEN']
*/

/*
Generate programs array to be listed
*/
//$res=exec_query("SELECT short_name,table_prefix FROM ".$system_tables['program'],Q_RET_ARRAY);
$res=exec_query("SELECT short_name,table_prefix FROM program",Q_RET_ARRAY);

$programs=array();
if(is_array($res)){
   foreach($res as $arr){
      $programs[$arr['short_name']]=$arr['table_prefix'];
   }
}else{//No programs added yet
   $programs['Deafult']='Deafult';
}

$GLOBALS['PROGRAMS']=$programs;

function program_select($program){
   d_r('dijit.form.FilteringSelect');
   echo "<select dojoType='dijit.form.FilteringSelect' 
   style='width:100px;'
	title='Select Program'
	data-dojo-props=\"placeHolder:'Program'\"
   onChange='change_program(this.get(\"value\"),this.get(\"displayedValue\"))'>\n";

   foreach($GLOBALS['PROGRAMS'] as $key => $value){
      if($program == $key){
         echo "<option value='$value' selected=true>$key</option>\n";
      }else{
         echo "<option value='$value' >$key</option>\n";
      }
   }
   echo "</select>\n";
}

//assign program prefix to each table

function gen_program_tables($program){
   if(isset($GLOBALS['P_TABLES'])){
      foreach($GLOBALS['P_TABLES'] as $table => $p_table){
         if(isset($GLOBALS['PROGRAMS'][$program])){
            $GLOBALS['P_TABLES'][$table]=sprintf($p_table,$GLOBALS['PROGRAMS'][$program]."_");
         }
      }
   }
}

?>
