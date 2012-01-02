<?php
/*
This will include in to $GLOBALS['PAGE_GEN']
*/
?>
<?php 
$dijitIcons=array(
      "dijitIconSave",
      "dijitIconPrint",
      "dijitIconCut",
      "dijitIconCopy",
      "dijitIconClear",
      "dijitIconDelete",
      "dijitIconUndo",
      "dijitIconEdit",
      "dijitIconNewTask",
      "dijitIconEditTask",
      "dijitIconEditProperty",
      "dijitIconTask",
      "dijitIconFilter",
      "dijitIconConfigure",
      "dijitIconSearch",
      "dijitIconApplication",
      "dijitIconBookmark",
      "dijitIconChart",
      "dijitIconConnector",
      "dijitIconDatabase",
      "dijitIconDocuments",
      "dijitIconMail",
      "dijitIconFile",
      "dijitIconFunction",
      "dijitIconKey",
      "dijitIconPackage",
      "dijitIconSample",
      "dijitIconTable",
      "dijitIconUsers",
      "dijitIconFolderClosed",
      "dijitIconFolderOpen"
);

//Editor icons
$dijitEditorIcons=array(
      "dijitEditorIconSep",
      "dijitEditorIconSave",
      "dijitEditorIconPrint",
      "dijitEditorIconCut",
      "dijitEditorIconCopy",
      "dijitEditorIconPaste",
      "dijitEditorIconDelete",
      "dijitEditorIconCancel",
      "dijitEditorIconUndo",
      "dijitEditorIconRedo",
      "dijitEditorIconSelectAll",
      "dijitEditorIconBold",
      "dijitEditorIconItalic",
      "dijitEditorIconUnderline",
      "dijitEditorIconStrikethrough",
      "dijitEditorIconSuperscript",
      "dijitEditorIconSubscript",
      "dijitEditorIconJustifyCenter",
      "dijitEditorIconJustifyFull",
      "dijitEditorIconJustifyLeft",
      "dijitEditorIconJustifyRight",
      "dijitEditorIconIndent",
      "dijitEditorIconOutdent",
      "dijitEditorIconListBulletIndent",
      "dijitEditorIconListBulletOutdent",
      "dijitEditorIconListNumIndent",
      "dijitEditorIconListNumOutdent",
      "dijitEditorIconTabIndent",
      "dijitEditorIconLeftToRight",
      "dijitEditorIconRightToLeft",
      "dijitEditorIconToggleDir",
      "dijitEditorIconBackColor",
      "dijitEditorIconForeColor",
      "dijitEditorIconHiliteColor",
      "dijitEditorIconNewPage",
      "dijitEditorIconInsertImage",
      "dijitEditorIconInsertTable",
      "dijitEditorIconSpace",
      "dijitEditorIconInsertHorizontalRule",
      "dijitEditorIconInsertOrderedList",
      "dijitEditorIconInsertUnorderedList",
      "dijitEditorIconCreateLink",
      "dijitEditorIconUnlink",
      "dijitEditorIconViewSource",
      "dijitEditorIconRemoveFormat",
      "dijitEditorIconFullScreen",
      "dijitEditorIconWikiword"
);

//view all buttons
/*
      foreach($dijitIcons as $icon){
      $id=str_replace("dijitIcon","",$icon);
      echo '
         <div    dojoType="dijit.form.Button" id="toolbar1.'.$id.'" 
         iconClass="dijitIcon '.$icon.'" 
         showLabel="true">
         '.$id.'
         </div>
      ';
      
      }

      foreach($dijitEditorIcons as $icon){
      $id=str_replace("dijitEditorIcon","ed",$icon);
      echo '
         <div    dojoType="dijit.form.Button" id="toolbar1.'.$id.'" 
         iconClass="dijitEditorIcon '.$icon.'" 
         showLabel="true">
         '.$id.'
         </div>
      ';
      
      }
*/

/*
TOOLBAR generator; which generates the toolbar according to the detail provided in <module>/menu.php -> $toolbar array
* the basic structure of the $toolbar array is as follows:
__________________________________________________________________________________________________
$toolbar   =array(
   //page es
   "manage_student"      =>array(
      //toolbar buttons
      'Add'=>array(
         //toolbar button attributes 
         'icon'      =>'NewPage',
         'action'      =>'js_func',
         'dojoType'   =>'dijit.form.Button',
         'element'   =>"<div dojoType='%s' id='toolbar.%s' iconClass='%s %s' showLabel='true' onClick='%s' >%s</div>"
      ),
      'Save'=>array('icon'=>'Save'),
      'Edit',
      'Delete',
      'Filter',
      'Search',
      'Database',
      'Table'
   ),

   "push"               =>array('Filter'),
   "bit_push"            =>array('Filter')
);
__________________________________________________________________________________________________

*/

//START toolbar
d_r('dijit.Toolbar');
d_r('dijit.form.Button');
d_r('dijit.form.NumberTextBox');

//For the following types labels will be added automatically
$labeld_types=array(
   'dijit.form.NumberTextBox',
   'dijit.form.TextBox'
);
echo "<div id='toolbar' jsId='toolbar' dojoType='dijit.Toolbar'>";
//Adding help button to the toolbar
echo "<div dojoType='dijit.form.Button' label='Help' showLabel='true' iconClass='dijitIcon dijitIconDocuments' onClick='help_dialog()'></div>";

if(!isset($toolbar[PAGE])){
   //toolbar is empty
}else{
   foreach($toolbar[PAGE] as $label => $attrib ){
      /*
         Set of attributes associated with each button of the toolbar   
      */
      $cur_attrib=array(
         'dojoType'   =>'dijit.form.Button',
         'id'         =>'act'.str_replace(array(" "),array("_"),$label),
         'iconClass'   =>'dijitIcon',
         'icon'      =>'dijitIconFunction',
         'action'      =>'alert("Not yet configured!")',
         'label'      =>$label,
         'element'   =>''
      );

      /*
      Six parameters will associate with each element of the toolbar
      %1 -> dojoType
      %2 -> id
      %3 -> iconClass
      %4 -> icon
      %5 -> action
      %6 -> label
      */
   
      $cur_attrib['element']   ="
      <div    dojoType='%s' 
            id='toolbar.%s' 
            iconClass='%s %s' 
            showLabel='true' 
            onClick='%s' 
            >
            %s
      </div>";


      /*
         If $attrib is an array do further computations to assign custom attributes to the button
         else only the label will set to the button and default parameters will be used
      */
      if(is_array($attrib)){
         if(isset($attrib['icon'])){
            /*
            select icon either from dijitIcons or dijitEditorIcons array given above
            according to the given icon name icon name is the latter part of the icon style of any array
            */
            if(array_search('dijitIcon'.$attrib['icon'],$dijitIcons)){
               $cur_attrib['iconClass']="dijitIcon";
               $cur_attrib['icon']      ='dijitIcon'.$attrib['icon'];   
            }elseif(array_search('dijitEditorIcon'.$attrib['icon'],$dijitEditorIcons)){
               $cur_attrib['iconClass']="dijitEditorIcon";
               $cur_attrib['icon']      ='dijitEditorIcon'.$attrib['icon'];   
            }
         }

         /*
         Attributes other than the icon will recursively asigned to the current attribute array to be
         presented to printf function bellow
         */
         foreach($cur_attrib as $key => $value){
            if( $key != 'icon' && isset($attrib[$key]) ){
               $cur_attrib[$key]=$attrib[$key];
            }
         }
      }else{
         /*
            if attrib is not an array consider as an string  and assign it to the label
         */
         $cur_attrib['label']      =$attrib;
      }

      if(in_array($cur_attrib['dojoType'],$labeld_types)){
         echo "<div dojoType='dijit.form.Button' label='".$cur_attrib['label']."' disabled='true' ></div>";
      }

      /*
      print the element with all custom attributes   
      */
        printf(
         $cur_attrib['element'],

         $cur_attrib['dojoType'], 
         $cur_attrib['id'], 
         $cur_attrib['iconClass'], 
         $cur_attrib['icon'], 
         $cur_attrib['action'], 
         $cur_attrib['label']
      );
   }
}

//END toolbar
echo "</div>";
?>
