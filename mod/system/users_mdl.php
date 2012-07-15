<?php

d_r("dojox.form.PasswordValidator");
$password_custom='
<div dojoType="dijit.form.DropDownButton">
   <span>
       Change Password 
   </span>
   <div dojoType="dijit.TooltipDialog">
      <div dojoType="dojox.form.PasswordValidator" name="password_val" id="password_val" jsId="password_val">
         <table>
            <tr><td>Password</td><td>:<input type="password" pwType="new" ></td></tr>
            <tr><td>Validate</td><td>:<input type="password" pwType="verify" ></td></tr>
         </table>
      </div>
      <button dojoType="dijit.form.Button" type="submit">
         OK
           <script type="dojo/method" event="onClick" args="evt">
            var pval=dijit.byId("password_val").value;
            var pset=dijit.byId("password");
            pset.attr("value",pval);
            //alert(pset.value);
         </script>
      </button>
   </div>
</div>
';

$arr=array('USER'=>'');
$arr=array_merge($arr,exec_query("SELECT group_name,rid FROM ".s_t('role'),Q_RET_ARRAY,null,'group_name'));
$group_inner      =gen_select_inner(array_keys($arr),null,true);

$res=exec_query("SELECT short_name,rid FROM ".s_t('program'),Q_RET_ARRAY,null,'rid');
$program_inner  =gen_select_inner($res,'short_name');

$auth_mod_inner   =gen_select_inner(get_common_list('auth_mod',true),null,true);
$permission_inner =gen_select_inner(array("ADMIN","STAFF","STUDENT","GUEST"));
$theme_inner      =gen_select_inner(get_common_list('theme',true),null,true);
$layout_inner     =gen_select_inner(get_common_list('layout',true),null,true);
$title_inner      =gen_select_inner(get_common_list('title',true),null,true);

$syear_inner="";
$curr_year=date("Y");
for($i=($curr_year-2); $i < ($curr_year+2); $i++ ){
   $syear_inner.="<option value='$i'>$i</option>";
}



$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
      'PRIMARY_KEY'	=>'user_id',
      'UNIQUE_KEY'	=>array('username','ldap_user_id'),
      'MULTY_KEY'	=>array(''),
   ),
//--------------FIELDS TO BE INCLUDED IN FORM-------------------
//---------------THIS ALSO REFLECT THE TABLE--------------------
   'FORM'=>array(
      "user_id"=>array(
         "length"	=>"77",
         "dojoType"	=>"dijit.form.NumberTextBox",
         "type"	=>"hidden",
         "required"	=>"false",
         "label"	=>"User id",
         "label_pos"	=>"top",
         "value"=>""
      ),
      /*
      "syear"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.Select",
         "required"	=>"false",
         "label"	=>"Syear",
         "inner"=>$syear_inner,
         "label_pos"	=>"top",
         "value"=>""
      ),
      "current_school_id"=>array(
         "length"	=>"70",
         "required"	=>"false",
         "label"	=>"Current school id",
         "label_pos"	=>"top",
         "value"=>""
      ),
       */
      
      "first_name"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"First name",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "middle_names"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Middle names",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "last_name"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Last name",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "title"=>array(
         "length"	=>"35",
         "dojoType"	=>"dijit.form.Select",
         "inner"=>$title_inner,
         "required"	=>"false",
         "label"	=>"Title",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "email"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
		   "regExp"=>"\b[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}\b",
		   "invalidMessage"=>"Please enter a valid email address",
         "label"	=>"Email",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "username"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"Username",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "password_custom"=>array(
         "custom"=>"true",
         "inner"=>$password_custom,   
         "label"	=>"Password",
         "label_pos"	=>"left",
      ),

      "password"=>array(
         "type"=>"hidden",
         "length"	=>"350",
         "dojoType"=>"dijit.form.ValidationTextBox",
         "value"=>"",
      ),
      /*
      "phone"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Phone",
         "label_pos"	=>"top",
         "value"=>""
      ),
      */
      "ldap_user_id"=>array(
         "length"	=>"150",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Ldap user id",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "auth_mod"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.Select",
         "required"	=>"false",
         "inner"     =>$auth_mod_inner,
         "label"	   =>"Authentication mod",
         "tooltip"   =>"NOTE: AUTO mod will allow system to select them authentication mode.",
         "label_pos"	=>"top",
         "value"=>""
      ),
      /*
      "user_type"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"User type",
         "label_pos"	=>"top",
         "value"=>""
      ),
       */
      "role_id"=>array(
         "length"	=>"150",
         "dojoType"	=>"dijit.form.Select",
         "required"	=>"false",
         "inner"=>$group_inner,
         "label"	=>"Role",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "theme"=>array(
         "length"	=>"140",
         "dojoType"	=>"dijit.form.Select",
         "required"	=>"false",
         "inner"=>$theme_inner,
         "label"	=>"Theme",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "layout"=>array(
         "length"	=>"140",
         "dojoType"	=>"dijit.form.Select",
         "inner"=>$layout_inner,
         "required"	=>"false",
         "label"	=>"Layout",
         "label_pos"	=>"top",
         "value"=>""
      ),
      /*
      "homeroom"=>array(
         "length"	=>"35",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Room",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "programs"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.SimpleTextarea",
         "required"	=>"false",
         "label"	=>"Programs",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "last_login"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"Last login",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "failed_login"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Failed login",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "profile_id"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Profile id",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "rollover_id"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Rollover id",
         "label_pos"	=>"top",
         "value"=>""
      ),
      */
      "status"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.Select",
         "required"	=>"false",
         "label"	=>"Status",
         "inner" =>gen_select_inner($GLOBALS['STATUS'],null,false),
         "label_pos"	=>"right",
         "value"=>""
      ),
      "note"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Note",
         "label_pos"	=>"top",
         "value"=>""
      )
   ),
   'GRIDS'=>array(
       'GRID'=>array(
          'columns'      =>array('user_id'=>array('hidden'=>'true'),'username','email','ldap_user_id','role_id'),
          'filter'       =>isset($_SESSION[PAGE]['FILTER'])?$_SESSION[PAGE]['FILTER']:null,
          'selector_id'  =>'toolbar__user_id',
          'ref_table'    =>s_t('users'),
          'order_by'     =>' ORDER BY user_id DESC ',
          'event_key'    =>'user_id',
          'dojoType'     =>'dojox.grid.EnhancedGrid',
          'query'        =>'{ "user_id": "*" }',
          'rowsPerPage'  =>'40',
          'clientSort'   =>'true',
          'style'        =>'width:100%;height:400px',
          'onClick'      =>'load_grid_item',
          'rowSelector'  =>'20px',
          'columnReordering'=>'true',
          'headerMenu'   =>'gridMenu',
       ),
    ),
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'TOOLBAR'=>array(
      "user_id"=>array(
         "length"=>"170",
         "dojoType"=>"dijit.form.FilteringSelect",
         "required"=>"false",
         "label"=>"Username",
         "label_pos"=>"left",

         "onChange"=>'s_p_c_add("ok",fill_form,this.value);set_param(this.id,this.value)',
         "searchAttr"=>"label",
         "pageSize"=>"10",
         "store"=>"rid_store",

         "filter"=>isset($_SESSION[PAGE]['FILTER'])?" AND ".$_SESSION[PAGE]['FILTER']:null,
         "ref_table"=>s_t('users'),
         "ref_key"=>'user_id',
         "order_by"=>'ORDER BY user_id DESC',
         "vid"=>array('username'),
      ),  
   ),
   'WIDGETS'=>array(
   ),
);
?>
