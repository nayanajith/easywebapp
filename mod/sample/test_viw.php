<?php 
//Add form to the MAIN TOP section
//get_field and get_label is used to acquire the field and label from the form
add_to_main_top('MAIN_TOP'."
<div dojoType='dijit.form.Form' id='main' jsId='main' encType='multipart/form-data' method='POST' >
".get_field('rid')."
<table>
<tr><td>".get_label('name')."</td><td align='left'>".get_field('name')."</td></tr>
<tr><td>".get_label('note')."</td><td>".get_field('note')."</td></tr>
</table></div>");

//MAIN BOTTOM section only contain the name
add_to_main_bottom('MAIN_BOTTOM');

//MAIN LEFt section only contain the name
add_to_main_left('MAIN_LEFT');

//The grid is added to the MAIN RIGHT
//The grid was acquired useing the get_pviw_property function wich return the properties from pview array which is 
//generated when the model is parsed
add_to_main_right('MAIN_RIGHT'.get_pviw_property(array('GRIDS','GRID')));


//Layout styling can be done through the set_layout_property function. Mainly the width and height of each section is set
//using this function

set_layout_property(
   'app2',
   'MAIN_TOP',
   'style',array(
      'height'=>'20%',
      'border'=>'1px solid red',
      'text-align'=>'center',
   )
);

set_layout_property(
   'app2',
   'MAIN_BOTTOM',
   'style',array(
      'height'=>'20%',
      'border'=>'1px solid red',
      'text-align'=>'center',
   )
);

set_layout_property(
   'app2',
   'MAIN_LEFT',
   'style',array(
      'width'=>'50%',
      'border'=>'1px solid red',
      'text-align'=>'center',
   )
);

set_layout_property(
   'app2',
   'MAIN_RIGHT',
   'style',array(
      'width'=>'50%',
      'border'=>'1px solid red',
      'text-align'=>'center',
   )
);
?>
