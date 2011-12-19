<?php
function input_td($input,$field,$value){
        $msg="<span style='color:red;'>&lt;</span>
        <span style='background:Khaki;border:1px solid red;font-size:9px;' class=mesg>Fleld empty</span>";
        if($value!=''){
                $msg='';
        echo "<tr>
                        <td>"
                        .style_text($field).
                        "</td>
                        <td id=".$field."_td>
                        $input
                        <span class='field_msg'>$msg</span>
                        </td>
                        </tr>";
function input_gen($field_info,$value){
        $field_array=explode(":", $field_info);
        $type=$field_array[0];
        $field=$field_array[1];
        $width=$field_array[2];
        $validate=$field_array[3];
        $css_class='generic_field';
        switch($validate){
                case 'nonempty':
                        $css_class='nonempty_field';
                        break;
                case 'passwd':
                        $css_class='password_field';
                        break;
                case 'number':
                        $css_class='number_field';
                        break;
                case 'email':
                        $css_class='email_field';
                        break;
        switch($type){
                case 'text':
                        $input="<input type=text name='$field' id='$field' class='$css_class' size='$width' value='$value'></input>";
                        input_td($input,$field,$value);
                        break;
                case 'password':
                        $input="<input type=password name=confirmation id=confirmation size='$width' ></input>
   Confirm:<input type=password name='$field' id='$field' class='$css_class' size='$width' ></input>";
                        input_td($input,$field,$value);
                        break;
                case 'hidden':
                        $input="<input type='hidden' name='$field'  class='$css_class' value='$value' >";
                        input_td($input,$field,$value);
                        break;
                case 'file':
                        $input="<input type='file' name='$field'  class='$css_class' value='$value' >";
                        input_td($input,$field,$value);
                        break;
                case 'textarea':
                        $input="<textarea name='$field' id='$field' rows='10'  class='$css_class' cols='$width'>$value</textarea>";
                        input_td($input,$field,$value);
                        break;
                case 'radio':
                        $input="<input type='radio' name='$field' id='$field' class='$css_class' value='$value' >";
                        input_td($input,$field,$value);
                        break;
                case 'checkbox':
                        $input="<input type='checkbox' name='$field' id='$field' class='$css_class' value='$value' >";
                        input_td($input,$field,$value);
                        break;
                case 'optgroup':
                        echo "
  <select name='$field'  class='$css_class' >
   <optgroup label='Swedish Cars'>
      <option value='volvo'>Volvo</option>
      <option value='saab'>Saab</option>
   </optgroup>
   <optgroup label='German Cars'>
      <option value='mercedes'>Mercedes</option>
      <option value='audi'>Audi</option>
   </optgroup>
  </select> 
   ";
                        break;
                case 'select':
                        $input="
   <select name='$field'  class='$css_class' onchange='submit_me(\"$field\")'>
   $value
   </select>
   ";
   input_td($input,$field,$value);
   break;
                case 'option':
                        echo "
  <select   name='$field' class='$css_class' >
   <option>Volvo</option>
   <option>Saab</option>
   <option>Mercedes</option>
   <option>Audi</option>
  </select> 
   ";
                        break;
$form=array(
"no_auto_fields"        =>array("text:course_id:10:nonempty","text:course:30:","text:examiner:30:","text:remarks:30:"),
"auto_fields"           =>array("hidden:ID:10:"),
"action"                                =>'page_gen.php?page=mcq/mcq_course',
"order_by"                      =>'ID',
"order"                         =>'DESC',
"list_prefix"           =>'list_',
"list_by"                       =>'course_id',
"table"                         =>'mcq_course'
echo "<form>";
echo "<table><tr><td id='courseid_td'>";
$options="<option value='New'>New</option>";
$SQL="SELECT * FROM ".$form['table']." ORDER BY ".$form['order_by']." ".$form['order'];
openDB2("mcq_t");
$RESULT=mysql_query($SQL,$CONNECTION);
while( $ROW = mysql_fetch_array($RESULT) ) {
        $CUR_PARAM=$ROW[$LIST_BY];
        if($list_course_id == $CUR_PARAM ){
                $options.="<option value='".$CUR_PARAM."' selected='selected'>".$CUR_PARAM."</option>";
^C

