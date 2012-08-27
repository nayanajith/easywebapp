/*
//Browser detection
dojo.isIE
dojo.isMozilla
dojo.isFF
dojo.isOpera
dojo.isSafari
dojo.isKhtml
dojo.isAIR 
dojo.isQuirks
dojo.isBrowser
dojo.isWebKit
dojo.isChrome

alert(dojo.isIE+':'+
dojo.isMozilla+':'+
dojo.isFF+':'+
dojo.isOpera+':'+
dojo.isSafari+':'+
dojo.isKhtml+':'+
dojo.isAIR +':'+
dojo.isQuirks+':'+
dojo.isBrowser+':'+
dojo.isWebKit+':'+
dojo.isChrome);
*/
//path to page genenrator aka the controler php (index.php)
var page_gen      ='<?php echo W_ROOT."/".$GLOBALS['PAGE_GEN']; ?>';

//halt reloading the page or grid temporaly 
var halt_page_reloading=true;

//halt parameter setting temporaly 
var halt_set_param=true;



//timeout for XHR request is 60 secons
var timeout_ = 100*1000;

/**
 * Turn on reloading the page
 */
function reloading_on(){
   halt_page_reloading=false;
	//MAIN.set('href',gen_url()+"data=html&section=main");
}

/**
 * Turn off reloading the page
 */
function reloading_off(){
   halt_page_reloading=true;
}


/**
 * parameter setting is enabled under users request
 */
function set_param_on(){
   halt_set_param=false;
}

/**
 * parameter setting is enabled under users request
 */
function set_param_off(){
   halt_set_param=true;
}



/**
 * find maching element in array
 */
function in_array(needle, haystack){
    var length = haystack.length;
    for(var i = 0; i < length; i++){
        if(haystack[i] == needle) return true;
    }
    return false;
}

/*--show page xhr dialogbox--*/
function show_xhr_dialog(url_,title,width,height,no_buttons){
	dojo.xhrPost({
      url 		: url_,
  	   handleAs :'text',
      timeout  : timeout_,
  	   load 		: function(response, ioArgs) {	     
         xhr_Dialog = new dijit.Dialog({
            title: title,
            style: "width:"+width+"px;height:"+height+"px;"
         });

         if(width > 15){
            width=width-15;
         }

         if(height > 80){
            height=height-80;
         }
         
         var content="<center><button dojoType='dijit.form.Button' onClick=\"xhr_Dialog.hide()\" >OK</button></center>";

         if(no_buttons == true){
            content="";
         }

         content="<div style='width:"+width+"px;min-height:"+height+"px;'>"+response+"</div>"+content;

         xhr_Dialog.set("content", content);
         xhr_Dialog.show();
  	   },
  	   error : function(response, ioArgs) {
  	  			update_status_bar('ERROR',response);
  	   }
   });
}

/*--help viewer--*/
function show_help_dialog(){
	dojo.xhrPost({
      url 		: gen_url(),
      content  : {help:'true'},
  	   handleAs :'text',
      timeout  : timeout_,
  	   load 		: function(response, ioArgs) {	     
         help_Dialog = new dijit.Dialog({
            title: "Help",
            style: "width: 800px;"
         });

         var button="<br/><center><button dojoType='dijit.form.Button' onClick=\"window.open(gen_url()+'help=true&fullscreen=true','help_window');help_Dialog.hide()\" >Show in Fullscreen</button><button dojoType='dijit.form.Button' onClick=\"help_Dialog.hide()\" >OK</button></center>";
         help_Dialog.set("content", response+button);
         help_Dialog.show();
  	   },
  	   error : function(response, ioArgs) {
  	  			update_status_bar('ERROR',response);
  	   }
   });
}



/*-- Seemless Downloader --*/

//Seamless download using  iframe
function download(url){
	update_status_bar('OK','Downloading...');

   //Traditional way of downloading
   if(dojo.isChrome){
      window.open(url);return;
   }

   //dojo iframe is creating to set the source of it   
	var iframe = dojo.io.iframe.create("downloader");

   //Downloading the file using iframe
   //TODO: this is sometime not working for chrome use browser detection and swtich the downloading method
	dojo.io.iframe.setSrc(iframe, url, true);
	update_status_bar('OK','Done');

   //DEBUG ONLY
	//update_status_bar('OK',url);
	update_progress_bar(100);
}

/*-----------------------------------view_class mostly using these functions-----------------------------*/




//Info dialog will  show the information as floating dialog box
function info_dialog(info,title,more_buttons,width,height){
   if(!more_buttons)more_buttons="";
   if(!title)title="Information";
   if(!width)width=300;
   if(!height)height=200;
   infoDialog = new dijit.Dialog({
      title: title,
      style: "width:"+width+"px;height:"+height+"px;"
   });

   var buttons="<br/><center>"+more_buttons+"<button dojoType='dijit.form.Button' onClick=\"infoDialog.hide()\" >OK</button></center>";
   infoDialog.set("content", info+buttons);
   infoDialog.show();
}


//This function allow to copy the content of given object to clipboard
//Only work with IE TODO: try to fix with suppor of flash
function copy_to_clipboard(object){
   var obj=dojo.byId(object);
   obj.select();
   if(window.clipboardData){ 
      var r=clipboardData.setData('Text',obj.value);
      return 1; 
   }else{ 
      alert('please copy manually');
   }
}

/**
 * change the user using XHR requests in backend
 */
function switch_user(value) {
   dojo.xhrPost({
         url      : page_gen+'/home/news', 
         content  : {form:'system',action:'switch_user',data:'json',user_id:value},
         handleAs : 'json',
         timeout  : timeout_,
         load     : function(response, ioArgs) {        
            update_status_bar(response.status,response.info);
            if(response.status == 'OK'){
               reload_page();
               update_progress_bar(100);
            }
         },
         error : function(response, ioArgs) {
            update_status_bar(response.status,response.info);
         }
   });
}   

//wrapper for filter form callbacks
function s_p_c_add(callback_name,callback_function,param){
   add_callback(set_param_callback,callback_name,callback_function,param);
}


/**
 * Populate the data in form for the selected key
 */
var set_param_callback={
   'ok':[],
   'error':[],
   'before':[]
};

/**
 * Set session parameters using XHR requests in backend
 */
function set_param(key,value) {
   if(halt_set_param)return;

   var s_p_c=set_param_callback;

   var url=gen_url();

   callback(s_p_c,'before');
   //content array to set explicit variables to the form
   var contentArr={form:'main',action:'param',data:'json'};

   //when key is also a variable you have to tweak it externally
   contentArr['param']  =key;
   contentArr[key]      =value;

   dojo.xhrPost({
        url      : url,
        content  : contentArr,
        handleAs : 'json',
        timeout  : timeout_,
        load     : function(response, ioArgs) {        
            update_status_bar(response.status_code,response.info);
            if(response.status_code == 'OK'){
               callback(s_p_c,'ok');
               update_progress_bar(100);
            }
            callback(s_p_c,'error');
         },
         error : function(response, ioArgs) {
            callback(s_p_c,'error');
            update_status_bar(response.status,response.info);
         }
   });
}   

/**
request html from the backend
@target: target ID
@source_array array of IDs of input fields which should be submitted
@action_ probably html or any
*/
function request_html(target,source_array,action_) {
   var content_obj   =document.getElementById(target);
   if(content_obj == null){
      var content_obj=target;
   }
   var param='';   
   var url=gen_url();
   if(source_array != null){
      for(var i=0;i< source_array.length;i++){
         alert(source_array[i]);
         var tmp_val   =document.getElementById(source_array[i]).value;
         if(tmp_val=='')return;
         param+='&'+source_array[i]+'='+tmp_val;
      }
   }

   //Action will be html by default unless it is set explicitly
   var action='html';
   if(action_){
      action=action_;
   }

   //Update the progress bar 
   update_status_bar('OK','Processing...');
   update_progress_bar(10);

   //If index number is blank return 
   dojo.xhrPost({
      url      : url,
      content  : {form:'main',data:'json',action:action+param},
      handleAs :'text',
      timeout  : timeout_,
      load     : function(response, ioArgs) {        
         update_status_bar('OK','Done');
         update_progress_bar(100);
         content_obj.innerHTML=response;
         dojo.parser.parse(content_obj);
      },
      error : function(response, ioArgs) {
         update_status_bar('ERROR',response);
      }
   });
}

function submit_display_values(action){
   var form='main';
   var url_=gen_url()+'&form=main&action='+action;
   dojo.forEach(dijit.byId(form).getChildren(), function(widget) {
      if(widget.store){
         if(widget.get('displayedValue')){
            url_=url_+'&'+widget.get('name')+'='+widget.get('displayedValue');
         }
      }else if(widget.get('value')){
         var value=widget.get('value');
         if(isNaN(widget.get('value'))){
            value='';
         } 
         url_=url_+'&'+widget.get('name')+'='+value;
      }
   });


   dojo.xhrPost({
      url         : url_, 
      handleAs    : 'json',
      timeout     : timeout_,

      handle: function(response,ioArgs){
         update_status_bar(response.status_code,response.info);
         if(response.status_code == 'OK'){
            if(!target_module && !target_page){
               update_progress_bar(100);
            }else{
               window.open(gen_url(),'_parent');
            }
         }else{
            update_status_bar('ERROR',response.info);
            if(document.getElementById('captcha_image'))reload_captcha();
            //update_status_bar('ERROR','Duplicate Entry!');
         }
      },
   
      load: function(response) {
         if(!target_module && !target_page){
            update_status_bar('OK','rquest sent successfully');
            update_progress_bar(50);
         }
      }, 
      error: function(response,ioArgs) {
         if(!target_module && !target_page){
            update_status_bar('ERROR','Error on submission!');
            update_progress_bar(0);
         }
      }
   });
   return true;
}
/**
 * reload the page
 */
function reload_page(){
   if(halt_page_reloading==true){
      return;
   }
   var sections=['TOOLBAR_TOP','MENUBAR','TOOLBAR','STATUSBAR','MAIN_LEFT','MAIN_RIGHT','MAIN_BOTTOM','MAIN_TOP'];
   //var sections=['MAIN_LEFT','MAIN_RIGHT','MAIN_BOTTOM','MAIN_TOP'];
	if(dijit.byId('MAIN_LEFT').get('href') && false){
   	update_status_bar("OK","Reloading page...");
      reload_sections(sections);
   	update_status_bar("OK","Page reloaded");
	}else{
   	setTimeout('window.location.reload()',200); 
   	setTimeout('update_status_bar("OK","Reloading page...")',200);
	}
   halt_page_reloading==true;
}

/**
 * Fill filtering selects in toolbar 
 */
function toolbar_load_selected(){
   dojo.forEach(dijit.byId('toolbar').getChildren(),function(widget){
      switch(widget.declaredClass){
      case 'dijit.form.FilteringSelect':
         load_selected_value(widget,widget._lastValueReported);
      break;
      default:
      break;
      }  
   });
}
/**
 * Reload set of sections as requested in the array
 */
function reload_sections(sections){
   var layout;
   //Loading the layout of the env
   dojo.xhrPost({
      url      :gen_url()+'section=LAYOUT', 
      sync     :true,
      handleAs :'json',
      load: function(response) {
         if(response != null && response){
           layout=response; 
         }
      },
      error: function(response) {
         console.error("Error loading layout SECTION");
      }
   });

   for(var i in sections){
      //section id
      var section=dijit.byId(sections[i]);
	   if(section.get('href')){
         /*
         dojo.fadeOut({ 
            node: sections[i], 
            sync:true,
            duration:1000,
         }).play();
         */

         switch(sections[i]){
            case 'MAIN_RIGHT':
               var parent_node = section.getParent();
               section.destroy();
               var right_pane = new dijit.layout.ContentPane({
                  'id':sections[i],
                   'region': 'right',
                   'gutters':false,
                   'style':layout[sections[i]].style,
                   'href':gen_url()+'section='+sections[i]
               }, sections[i]);
               parent_node.addChild(right_pane);
            break;
            case 'MAIN_BOTTOM':
               var parent_node = section.getParent();
               section.destroy();
               var bottom_pane = new dijit.layout.ContentPane({
                  'id':sections[i],
                   'region': 'bottom',
                   'gutters':false,
                   'style':layout[sections[i]].style,
                   'href':gen_url()+'section='+sections[i]
               }, sections[i]);
               parent_node.addChild(bottom_pane);
            break;
            case 'TOOLBAR':
               set_param_off();
               reloading_off();
   	         section.refresh(); 
            break;
            default:
   	         section.refresh(); 
            break;
         }

         //Set the style, layout, and properties
         if(layout[sections[i]]){
            var section_layout=layout[sections[i]];
            for(var j in section_layout){
               switch(j){
               case 'style':
                  dojo.style(sections[i],section_layout[j]);
               break;
               default:
                  //section.set(j,section_layout[j]);
               break;
               }
            }
         }
         
         /*
         dojo.fadeIn({ 
            node: sections[i], 
            sync:true,
            duration:2000,
         }).play();
         */
      }
   }
   //Loading dynamic javascript
   dojo.xhrPost({
      url      :gen_url()+'section=DYNAMIC_JS', 
      sync     :true,
      handleAs :'javascript',
		headers	:{ "Content-Type":"application/javascript"},
      load: function(response) {
      },
      error: function(response) {
         console.log("Error loading layout DYNAMIC_JS"+response);
      }
   });
}

function reload_dialog(){
   dijit.byId('DIALOG').show();
}
function reload_main_right(){
   reload_sections(['MAIN_RIGHT']);
}
function reload_main_bottom(){
   reload_sections(['MAIN_BOTTOM']);
}
function reload_main(){
   reload_sections(['MAIN_TOP','MAIN_LEFT','MAIN_RIGHT','MAIN_BOTTOM']);
}
function reload_toolbar(){
   reload_sections(['TOOLBAR']);
}
function reload_menubar(){
   reload_sections(['MENUBAR']);
}
function reload_toolbar_top(){
   reload_sections(['TOOLBAR_TOP']);
}
function reload_toolbar_top(){
   reload_sections(['TOOLBAR_TOP']);
}

/*
 * callback arrays are in the following form the callback function will check whether the function is set and if so it will execute the function
var fill_form_callback={
   'ok':[f1,f2],
   'error':[],
   'reset':[f3,f4],
   'before':[]
};
*/
function callback(callback_array,function_name,response){
   if(callback_array && function_name && callback_array[function_name] && callback_array[function_name] != null){
      for(var i in callback_array[function_name]){
         var cb=callback_array[function_name][i];
         cb['func'](cb['param'],response);
      }
      callback_array[function_name]=[];
   }
}

/**
 * Add to callback array
 */
function add_callback(callback_array,callback_name,callback_function,param){
   var cb={'func':callback_function,'param':param};
   callback_array[callback_name].push(cb);
}

/**
 * clear cllaback array
 */
function clear_callback(callback_array){
   for(key in callback_array){
      callback_array[key]=[];
   }
}

//wrapper for submit_form callbacks
function s_f_c_add(callback_name,callback_function,param){
   add_callback(submit_form_callback,callback_name,callback_function,param);
}

/** Submit the given form
* tartget_module and target_page are optional. these parameters are used by public layout
*/
//cllback array for this function
var submit_form_callback={
   'ok':[],
   'error':[],
   'before':[]
};

//form can be changed explicitly before submitting the form
var form='main';
function submit_form(action,param1,param2){
   var target_module =param1;
   var target_page   =param2;
   //submit form callback array
   var s_f_c=submit_form_callback;

   //call the before callback function
   callback(s_f_c,'before');

   var url=gen_url();
   switch(action){
      case 'print':
			dojo.xhrPost({
            url         : url,
            content     : {form:form,action:action},
            handleAs    : 'text',
            timeout     : timeout_,
            load: function(response,ioArgs) {
               popup(response);
            } 
         });
         //window.open(url+'form='+form+'&action='+action,'width=800px,height=600px');
         return;
      break;   
      case 'csv':
         download(url+'form='+form+'&action='+action+'&list='+param1);
         return;
      break;
      case 'pdf':
         download(url+'form='+form+'&action='+action);
         return;
      break;   
      case 'reload':
         request_html(form,source_array);
         return;
      break;   
      case 'delete':
      break;   
   }

   update_status_bar('OK','Processing...');
   update_progress_bar(50);

   /*User should confirm deletion*/
   if(action=='delete' && !confirm('Confirm Deletion!')){
      update_status_bar('ERROR','deletion canceled');
      update_progress_bar(0);

      //call the error callback function
      callback(s_f_c,'error');
      return false;   
   }

   //Some actions do not require form submission
   if (action=='del_filter' ) {
    dojo.xhrPost({
         url         : url,
         content     : {form:form,action:action},
         handleAs    : 'json',
         timeout     : timeout_,

         handle: function(response,ioArgs){
         },
         load: function(response,ioArgs) {
            update_status_bar(response.status_code,response.info);

            //call the error callback function
            callback(s_f_c,'ok');

         }, 
         error: function(response,ioArgs) {
            update_status_bar('ERROR','Error on submission!');
            update_progress_bar(0);

            //call the error callback function
            callback(s_f_c,'error',response);
         }
      });
      return;
   }

   if (action=='delete' || action=='add_filter' || action=='add_backup' || action=='del_backup' || dijit.byId(form).validate()) {
      dojo.xhrPost({
         url         : url, 
         //postData    : 'form='+form+'&action='+action,
         content     : {form:form,action:action},
         handleAs    : 'json',
         form        : form, 
         timeout     : timeout_,
      
         handle: function(response,ioArgs){
         },
      
         load: function(response,ioArgs) {
            update_status_bar(response.status_code,response.info);
            if(response.status_code == 'OK'){
               if(action=='add_filter'){
                  //reload_page(); 
               }
               if(!target_module && !target_page){
                  update_progress_bar(100);
               }else{
                  window.open(gen_url(),'_parent');
               }

               //call the error callback function
               callback(s_f_c,'ok');

            }else{
               update_status_bar('ERROR',response.info);
               if(document.getElementById('captcha_image'))reload_captcha();
               //update_status_bar('ERROR','Duplicate Entry!');

               //call the error callback function
               callback(s_f_c,'error');

            }
         }, 
         error: function(response,ioArgs) {
            if(!target_module && !target_page){
               update_status_bar('ERROR','Error on submission!');
               update_progress_bar(0);

               //call the before callback function
               callback(s_f_c,'error',response);

            }
         }
      });
      return false;
   }else{
      update_status_bar('ERROR','Form contains invalid data.  Please correct them and submit');

      //call the before callback function
      callback(s_f_c,'error');
      return false;
   }
   return true;
}

//wrapper for xhr callbacks
function xhr_c_add(callback_name,callback_function,param){
   add_callback(xhr_generic_callback,callback_name,callback_function,param);
}

/**
 * Generic xhr function
 */
var xhr_generic_callback={
   'ok':[],
   'error':[],
   'before':[]
};

function xhr_generic(submit_form,action,handle_as){
   var x_g_c=xhr_generic_callback;
   callback(x_g_c,'before');

   var url=gen_url();
   update_progress_bar(0);
   var handleAs_='text';
   if(handle_as){
      handleAs_=handle_as;
   }
   dojo.xhrPost({
      url         : url,
      content     : {form:submit_form,action:action,data:'true'},
      handleAs    : handleAs_,
      form        : submit_form, 
      timeout     : timeout_,

      handle: function(response,ioArgs){
      },
      load: function(response,ioArgs) {
         if(handleAs_ == 'text'){
            update_status_bar('OK','Request handled successfully');
            update_progress_bar(100);
            //call the error callback function
            callback(x_g_c,'ok',response);
         }else{ //'json'
            update_status_bar(response.status_code,response.info);
            if(response.status_code == 'ERROR'){
               update_progress_bar(20);
               //call the error callback function
               callback(x_g_c,'error',response);
            }else{
               update_progress_bar(100);
               //call the error callback function
               callback(x_g_c,'ok',response);
            }
         }
      }, 
      error: function(response,ioArgs) {
         update_status_bar('ERROR','Error on submission!');
         update_progress_bar(0);

         //call the error callback function
         callback(x_g_c,'error',response);
      }
   });
   return;
}

/**
 * export visible rows of the grid to csv
 */
function grid_to_csv(grid_id){
   if(grid_id){
      grid_id.exportGrid('csv', {
         fetchArgs: {
            start: grid_id.query.start, 
            count: grid_id.query.count
         },
         writerArgs: {
            separator: ","
         }
      }, function(str){
         info_dialog("Copy CSV from here<center><textarea cols='105' rows='29'>"+str+"</textarea></center>",'Grid to CSV',null,700,500);
      });
   }
}

/**
 * export visible rows of the grid to html
 */
function grid_to_table(grid_id,title){
   if(grid_id){
      grid_id.exportToHTML({
         title: title,
         body:{'align':'center'},
         cssFiles: ['<?php echo CSS ?>/grid_print.css'],
         fetchArgs: {
            start: grid_id.query.start, 
            count: grid_id.query.count
         }
      }, function(str){
         //info_dialog("<div>"+str+"</div>",'Grid to table',null,700,500);
         popup(str);
      });
   }
}

/**
 * Print visible rows of the grid
 */
function grid_print(grid_id,title){
   if(grid_id){
      grid_id.printGrid({
         title: title,
         cssFiles: ['<?php echo CSS ?>/grid_print.css'],
         fetchArgs: {
            start: grid_id.query.start, 
            count: grid_id.query.count
         }
      });
   }
}

/**
 * reload the grid
 */
function reload_grid(grid){
   //update_status_bar('OK','Reloading grid...');
   //update_progress_bar(50);

   var url_=grid.store.url;
   //var new_store = new dojox.data.CsvStore({url: url_ });
   var new_store = new dojox.data.QueryReadStore({url: url_ });

   //setTimeout(function(){grid.setStore(new_store)},2000); 
   grid.setStore(new_store);
   //update_progress_bar(100);
}

/**
 * Select value in  a filtering select connected to store programatically
 */
function load_combo_value(field,value_to_load){
   //TODO: checking for the field with store has a bug
   if(!field || !field.store)return;
   field.store.fetch({
      query:{ 'id': value_to_load },
      onItem : function(item, request) {
         if(field.store._labelAttr !== undefined){
            //for query read store
            field.set('value',item['i'][field.store._labelAttr]);
         }else{
            //for native store (when using <option></option>)
            field.set('value',value_to_load);
         }
         return;
      }
   });
}

/**
 * Select value in  a filtering select connected to store programatically
 */
function load_selected_value(field,value_to_load){
   //TODO: checking for the field with store has a bug
   if(!field || !field.store){
      field.set('value',value_to_load);
      return;
   }else{
      field.store.fetch({
         query:{ 'id': value_to_load },
         onItem : function(item, request) {
            var searchKey;
            for(var key in item['i']){
                  searchKey=key;
                  break;
            }
            //TODO:remove  undefined != request.store and find alternative method
           if (undefined != request.store && request.store.getValue(item, searchKey) == value_to_load) {
               field.set('value',request.store.getValue(item, searchKey));
               //field.set('value',request.store.getValue(item, searchKey));
               return;
           }
         }
      });
   }
}

//wrapper for filter form callbacks
function f_f_c_add(callback_name,callback_function){
   add_callback(fill_form_callback,callback_name,callback_function);
}


/**
 * Populate the data in form for the selected key
 */
var fill_form_callback={
   'ok':[],
   'error':[],
   'reset':[],
   'before':[]
};

function fill_form(rid,form) {
   //callback function array
   var f_f_c=fill_form_callback;

   //call the before callback function
   callback(f_f_c,'before');

   if(!form){
      form='main';
   }

   if(!(rid == '' || rid == 'new' || rid == "-none-" || rid == "-all-")){
   dojo.xhrPost({
      url      : gen_url(),
      content  : {form:form,action:'filler',data:'json',id:rid},
      handleAs :'json',
      load       : function(response, ioArgs) {        
         if(response.status && response.status == 'ERROR'){
            update_status_bar(response.status,response.info);
            update_progress_bar(50);
            //calling the error callback function
            callback(f_f_c,'error');
            return;
         }

         /*reset form*/
			/*
         dojo.forEach(dijit.byId(form).getChildren(), function(widget) {
            if(widget.store){
               widget.set('value', 'NULL');
            }else{
               widget.set('value', null);
            }
         });
			*/
         /*fill the form with returned values from json*/
         for(var key in response){
            //Clean the field values if responce does not contain values for the given key (blank or null)
            if(dijit.byId(key) && (response[key] == '' || response[key] =='null' || response[key] =='NULL' || response[key] == null)){
               var widget=dijit.byId(key);
               if(widget.store){
                  widget.set('value', 'NULL');
               }else{
                  switch(widget.declaredClass){
                  case 'dijit.form.SimpleTextarea':
                     widget.set('value', '');
                  break;
                  case 'dojox.form.Uploader':
                     dijit.byId(key).reset();
                     //Set rid for the uploading file
                     document.getElementById(key+'_rid').value=response['rid'];
                     //remove file exists url
                     document.getElementById(key+'_info').innerHTML="";
                  break;
                  default:
                     widget.set('value', null);
                  break;
                  }
               }
            }

            if(response[key] && dijit.byId(key)){
               if(response[key]['_type']=='Date'){
                  //Convert ISO standard date string to javascript Date object
                  dijit.byId(key).set('value',dojo.date.stamp.fromISOString(response[key]['_value'])); 
               }else{
                  //Handle different types of fields
                  switch(dijit.byId(key).declaredClass){
                     case 'dijit.form.CheckBox':
                        switch(response[key]){
                           case '1':
                           case 'on':
                           case 'true':
                              dijit.byId(key).set('checked',true); 
                           break;
                           case '0':
                           case 'off':
                           case 'false':
                           default:
                              dijit.byId(key).set('checked',false); 
                           break;
                        }
                     break;
                     case 'dijit.form.RadioButton':
                     break;
                     case 'dijit.form.ComboBox':
                        //dijit.byId(key).set('value',response[key]); 
                        load_combo_value(dijit.byId(key),response[key]);
                     break;
                     case 'dijit.form.FilteringSelect':
                        //dijit.byId(key).set('value',response[key]); 
                        load_selected_value(dijit.byId(key),response[key]);
                     break;
                     case 'dojox.form.Uploader':
                        //Add a url to download the file if the file is already uploaded
                        var path=document.getElementById(key+'_path').value;
                        //document.getElementById(key+'_info').innerHTML="File exists: <a href='"+path+"/"+response[key]+"'>"+response[key]+"</a>";  
                        var src=path+"/"+response[key];
                        document.getElementById(key+'_info').innerHTML="File exists: <a href='"+src+"'>"+response[key]+"</a><br><img src='"+src+"' width='128px'>"; 
                     break;
                     default:
                        dijit.byId(key).set('value',response[key]); 
                     break;
                  }
               }
            }
         }

         //calling the ok callback function
         callback(f_f_c,'ok');
      },
      error : function(response, ioArgs) {
         update_status_bar('ERROR',response);
         //calling the ok callback function
         callback(f_f_c,'error');
      }
   });
   }else{
      /*reset form*/
      clear_form(form,null);
      /*
      dojo.forEach(dijit.byId(form).getChildren(), function(widget) {
         if(!widget.store){
            widget.set('value', null);
         }
      });
      */

      //calling the reset callback function
      callback(f_f_c,'reset');
   }
}


/*-----------------------related to filter form---------------------*/
function show_dialog(){
   formDlg = dijit.byId('filterDialog');
   formDlg.show();
}

/*clear the form first*/
function clear_form(frm,selector_field){
   //load_selected_value(selector_field,'NULL');
   dojo.forEach(dijit.byId(frm).getChildren(),function(widget){
      switch(widget.declaredClass){
      case 'dijit.form.CheckBox':
         widget.set('checked', false);
      break;
      default:
         widget.set('value', null);
         if (typeof widget.setValue == 'function'){
            widget.set('value',null);
         }
         widget.set('value',null);
      break;
      }  
   });
}   

/*
Sending filter data as json   
*/
function dialog_submit(arg_form,action){
   /*User should confirm deletion*/
   if(action=='delete'&&!confirm('Confirm deletion!')){
      return;
   }
   if(arg_form.validate()){
      var json_req=dojo.toJson(arg_form.getValues(), true);
      dojo.xhrPost({
         url      : gen_url(), 
         content  : {form:'filter',action:action,filter:json_req,xhr:'true'},
         handleAs :'text',
         handle: function(response){
            update_status_bar('OK',response.info);
         },

         load: function(response) {
            update_status_bar('OK','form successfully submitted');
         }, 
         error: function(response,ioArgs) {
            update_status_bar('ERROR','Error on submission!');
         }
      });
/*
      for(var key in arg_array){
         update_status_bar(key);   
      }
      */
   }else{
      update_status_bar('ERROR','found invalid filed values');      
   }
}

/**
 * Populate the data in form for the selected key
 */
function fill_filter_form(form) {
   if(!form){
      form='main';
   }

   dojo.xhrPost({
      url      : gen_url(),
      content  : {form:form,action:'filter_filler',data:'json'},
      handleAs : 'json',
      load       : function(response, ioArgs) {        
         if(!response){
            return;
         }

         if(response.status_code && response.status_code == 'ERROR'){
            update_status_bar(response.status_code,response.info);
            update_progress_bar(50);
            return;
         }

         /*reset form*/
         dojo.forEach(dijit.byId(form).getChildren(), function(widget) {
            if(widget.store){
               widget.set('value', 'NULL');
            }else{
               widget.set('value', null);
            }
         });

         /*fill the form with returned values from json*/
         for(var key in response){
            if(response[key] && dijit.byId(key)){
               if(response[key]['_type']=='Date'){
                  //Convert ISO standard date string to javascript Date object
                      dijit.byId(key).set('value',dojo.date.stamp.fromISOString(response[key]['_value'])); 
               }else{
                  //Handle different types of fields
                  switch(dijit.byId(key).declaredClass){
                     case 'dijit.form.CheckBox':
                        switch(response[key]){
                           case '1':
                           case 'on':
                              dijit.byId(key).set('checked',true); 
                           break;
                           case '0':
                           case 'off':
                           default:
                              dijit.byId(key).set('checked',false); 
                           break;
                        }
                     break;
                     case 'dijit.form.RadioButton':
                     break;
                     case 'dijit.form.FilteringSelect':
                        dijit.byId(key).set('value',response[key]); 
                        load_selected_value(dijit.byId(key),response[key]);
                     break;
                     default:
                        dijit.byId(key).set('value',response[key]); 
                     break;
                  }
               }
            }
         }
      },
      error : function(response, ioArgs) {
           update_status_bar('ERROR',response);
      }
   });
}


/*-------------------------------------------------------------------------------------*/

/**
 * Pupub generated with some content written to it
 */
function popup(content1){
   var content=content1;
   var myWin=window.open('','RINT','width=1024,height=600,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1,location=0');
   myWin.document.writeln(content);
   myWin.document.close();
   return myWin;
}

//Get a requested value from the url
function get_request_value( name ){
   return get_url_value( name ,window.location.href);
}

//acquire GET request key,values from the current url  rest
function get_url_value( name , url){
   url = url.replace("//","/").replace("//","/");
   var regexS  = "<?php echo $GLOBALS['PAGE_GEN'] ?>/.*";
   var regex   = new RegExp( regexS );
   var results = regex.exec( url );
   if(results){
      var mpp     = results[0].split("/");
      switch(name){
      case 'module':
         return mpp[1];
      break;
      case 'page':
         return mpp[2];
      break;
      case 'program':
         return mpp[3];
      break;
      }
   }
}

//acquire GET request key,values from the current  key=value
function get_url_value_( name , url){
   name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
   var regexS = "[\\?&]"+name+"=([^&#]*)";
   var regex = new RegExp( regexS );
   var results = regex.exec( url );
   if( results == null ){
		return "";
   }else{
		return results[1];
	}
}

/**
 * javascript version of gen_url
 * This returns the basic target from the url
 */
function gen_url(module,page,program){
   if(!module || module == null || module == ''){
      module        =get_request_value('module');
   }
   if(!page || page == null || page == ''){
      page          =get_request_value('page');
   }

   if(!program || program == null || program == ''){
      program       =get_request_value('program');
   }

   if(program != ''){
      program="/"+program;
   }

   //return page_gen+"/"+module+"/"+page+program+"?";
   return page_gen+"?";
}

/**
 * Set Program module and page
 */
dojo.require('dojo.cookie');
dojo.cookie('program', '<?php echo PROGRAM ?>', {expires: 10});
dojo.cookie('module', '<?php echo MODULE ?>', {expires: 10});
dojo.cookie('page', '<?php echo PAGE ?>', {expires: 10});

function p_m_p(module,page,program){
   
   //How to clean all
   /*
   dojo.query("#menubar > div > *").forEach(function(node, index, arr){
      dojo.style(node,{
         "fontWeight":"normal"
      });
   });
   dojo.query("#menubar > div > div > *").forEach(function(node, index, arr){
      dojo.style(node,{
         "fontWeight":"normal"
      });
   });
   */
   //Get previouse pmp
   var prid_old=dojo.cookie('program');
   var mid_old =dojo.cookie('module');
   var pid_old =dojo.cookie('page');

   //Set old values if no new value as default
   if(program == null)program = prid_old;
   if(module == null)module = mid_old;
   if(page == null)page = pid_old;

   pid_old=mid_old+'__'+pid_old;
   mid_old='menu__'+mid_old;

   //Clear(normal font) previuse selection
   dojo.style(mid_old,{"fontWeight":"normal"});
   dojo.style(pid_old,{"fontWeight":"normal"});

   var mid='menu__'+module;
   var pid=module+'__'+page;

   //max breadcrumb length
   var max_length=35;
   var bc=dijit.byId(mid).get('label')+" / "+dijit.byId(pid).get('label');

   //trim breadcrumb if longer than max
   if(bc.length > max_length){
      //last two letters
      var ll=bc.substr(bc.length-2,bc.length);
      bc=bc.substr(0,max_length)+"~"+ll;
   }

   //Set breadcrumb 
   dijit.byId('breadcrumb').set('label',bc);

   //Bind event to reload the selected module/page
   /*
   require(["dojo/on"], function(on){
   var handle = on(dijit.byId('breadcrumb'), "click", p_m_p(module,page,program));
   });
   */
   //handle.remove();

   //Set(bold font) current selection
   dojo.style(mid,{"fontWeight":"bold"});
   dojo.style(pid,{"fontWeight":"bold"});

   var program_='';

   if(program == '' || program == null || program ==undefined){
      program_='';
   }else{
      program_='/'+program;
   }
   if(page == '' || page == null)page='';

   //app2 layout have special section loading facility so setting the program,module, and page using xhr is enough
   if('<?php echo $_SESSION['LAYOUT'] ?>'=='app2'){
      dojo.xhrPost({
         url      : page_gen+'/'+module+'/'+page+program_, 
         content  : {data:'true',action:'p_m_p'},
         handleAs :'text',
         handle: function(response){
         },
         load: function(response) {
            reload_sections(['TOOLBAR','MAIN_TOP','MAIN_LEFT','MAIN_RIGHT','MAIN_BOTTOM']);
         }, 
         error: function(response,ioArgs) {
            update_status_bar('ERROR','Error on submission!');
         }
      });
   }else{
      window.open(page_gen+'/'+module+'/'+page+program_,'_parent');
   }

   //Set cookie for pmp
   dojo.cookie('program', program, {expires: 10});
   dojo.cookie('module', module, {expires: 10});
   dojo.cookie('page', page, {expires: 10});
}

/**
 * Load specific page in a module
 */
function load_page(module,page,program){
   if(program == '' || program == null || program ==undefined){
      program='';
   }else{
      program='/'+program;
   }
   if(page == '' || page == null)page='';

   //app2 layout have special section loading facility so setting the program,module, and page using xhr is enough
   if('<?php echo $_SESSION['LAYOUT'] ?>'=='app2'){
      dojo.xhrPost({
         url      : page_gen+'/'+module+'/'+page+program, 
         handleAs :'text',
         handle: function(response){
         },
         load: function(response) {
            reload_page();
         }, 
         error: function(response,ioArgs) {
            update_status_bar('ERROR','Error on submission!');
         }
      });
   }else{
      window.open(page_gen+'/'+module+'/'+page+program,'_parent');
   }
}

/**
 * Select the relevant columns and regquest a csv includig the selected columns
 */
function get_csv(){
   var field_list=new Array();
	nodes = dojo.query('table#csv__table input[type=checkbox]'); 
   dojo.forEach(nodes,function(node){
      if(dijit.getEnclosingWidget(node).get("checked") == true){
	      field_list.push(node.value);
      }
   });

	var comma='';
	var list	='';
	for(var key in field_list){
		list+=comma+field_list[key];
		comma=',';
	}
   submit_form('csv',list);
}

/**
 *populate the related data in form when a row in the grid is clicked
 */
function load_grid_item(grid,event_key,selector_id,e){
   set_param_on();
   var selectedValue = grid.store.getValue(grid.getItem(e.rowIndex),event_key);
   load_selected_value(selector_id,selectedValue);
}

/**
 * Array of functions to be polled
 */
var poll_function_array=[];

/**
 * wrapper function to add poll functions to array
 */
function p_f_add(callback_function,param){
   var cb={'func':callback_function,'param':param};
   poll_function_array.push(cb);
}

/**
 * poll given set of functions
 */
var secs=5;
var auto_save_timeout=secs*1000;
var stop_auto_save=false;
function poll(){
  if(stop_auto_save){
     return;
  }
  for(var i in poll_function_array){
     var pf=poll_function_array[i];
     pf['func'](pf['param']);
  }
  setTimeout('poll()',auto_save_timeout);
}

//--------------Starting poll function------------------
poll();
//--------------Starting poll function------------------

/**
 * Check for the available notifications and notify if available
 */

function notify(){
   dojo.xhrPost({
      url      :gen_url(), 
      content  :{section:'ISNOTIFY'},
      handleAs :'json',
      handle: function(response){
      },
      load: function(response) {
         var notify_ddb=dijit.byId('notify_ddb');
         if(response != null && response && response.count > 0){
            //Set the the iconclass to notify red
            notify_ddb.set('iconClass',"notifyIconRed");
            
            //Fade IN,OUT animation of the drop dowb box
            dojo.fx.chain([
               dojo.fadeOut({ 
                  node: 'notify_ddb', 
                  duration:1000,
               }),
               dojo.fadeIn({ 
                  node: 'notify_ddb', 
                  duration:1000,
               })
            ]).play();

            //Set the count of notifications
            notify_ddb.set('label',response.count);
         
            //Popup the tooltip automatically
            /*
            dijit.popup.open({
               popup: notify_ttd,
               around: dojo.byId('notify_ddb')
            });
            */
         }else{
            //Set the the iconclass to notify red
            notify_ddb.set('iconClass',"notifyIcon");

            //Set the count of notifications
            notify_ddb.set('label','');

            //Popup the tooltip automatically
            /*
            dijit.popup.close({
               popup: notify_ttd,
               around: dojo.byId('notify_ddb')
            });
            */
         }
      }, 
      error: function(response,ioArgs) {
      }
   });
   
}

//Add notification function to the poll
if('<?php echo $_SESSION['LAYOUT'] ?>'=='app2'){
//   p_f_add(notify);
}

/**
 * Convert phrase to titlecase
 */
function to_title_case(str){
   return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}

/**
 * Disable widget
 */
function w_d(widget){
   if(widget){
      widget.set('disabled',true);
   }else if(dijit.byId(widget)){
      dijit.byId(widget).set('disabled',true);
   }
}

/**
 * Enable widget
 */
function w_e(widget){
   if(widget){
      widget.set('disabled',false);
   }else if(dijit.byId(widget)){
      dijit.byId(widget).set('disabled',false);
   }
}

/**
 * Inverse the checkbox selection of checkboxes in given table id
 */
function invert_selection(table_id){
 	nodes = dojo.query('table#'+table_id+' input[type=checkbox]'); 
   dojo.forEach(nodes,function(node){
      dijit.getEnclosingWidget(node).set("checked",!dijit.getEnclosingWidget(node).get("checked"));
   });
}

/**
 * Clear checkbox selection of checkboxes in given table id
 */
function clear_selection(table_id){
 	nodes = dojo.query('table#'+table_id+' input[type=checkbox]'); 
   dojo.forEach(nodes,function(node){
      dijit.getEnclosingWidget(node).set("checked",false);
   });
}


/*Update status bar*/
var max_status_length=100;
var stausDialog;
//If the public kayout used set the javascript variable to used with
var pub=false;
<?php if($_SESSION['LAYOUT']=='pub'){ ?>
pub=true;
<?php }?>

function update_status_bar(status_code,info){
   //Sometimes status_code and info is not defined due to json decode error
   if(typeof status_code === 'undefined' || typeof info === 'undefined') {
      return;
      //status_code='NOT_DEFINED';
      //info='Undefined error!';
   }

   status_code=status_code.toUpperCase();
   var orig_info=info;

   //Set the color to the message according to the status
   switch(status_code){
      case 'OK':
      default:
         info=info;
         update_progress_bar(100);
      break;
      case 'ERROR':
         info="<font color='red'>"+info+"</font>";
         if(!pub){
            set_busy(false);
         }
      break;
      case 'NOT_DEFINED':
         info="<font color='orange'>"+info+"</font>";
      break;
   }
   <?php if($_SESSION['LAYOUT']!='pub'){ ?>
   /*If the message too lengthy show it as a dialog*/
   var status_bar = document.getElementById('status_bar') ;
   if(info.length < max_status_length){
      status_bar.innerHTML=info;
      status_bar.title=orig_info;
   }else{
      /*Create dialog only if not initialized*/
      if(typeof stausDialog === 'undefined') {
         stausDialog = new dijit.Dialog({
            title: "Status report",
            style: "width: 400px;"
         });
      }
   
      var button="<br><center><button dojoType='dijit.form.Button' onClick=\"stausDialog.hide();status_bar.innerHTML='Done.'\" >OK</button></center>";
      stausDialog.set("content", info+button);
      stausDialog.show();
      status_bar.innerHTML='...etc';
   }
   <?php }else{ ?>
      /*Create dialog only if not initialized*/
      if(typeof stausDialog === 'undefined') {
         stausDialog = new dijit.Dialog({
            title: "Status report",
            style: "width: 400px;"
         });
      }
   
      var button="<br><center><button dojoType='dijit.form.Button' onClick=\"stausDialog.hide()\" >OK</button></center>";
      stausDialog.attr("content", info+button);
      stausDialog.show();
   <?php } ?>
}

/*Update progress bar and busying image (rotating arrows)*/
function update_progress_bar(progress){
   if(pub){
      return;
   }else{
      jsProgress.update({maximum: 100, progress: progress });
      if(progress == 100 || progress == 0){
         set_busy(false);
      }else{
         set_busy(true);
      }
   }
}

/*Update the busy state of the icon*/
function set_busy(is_busy){
   if(pub){
      return;
   }else{
      var busy = document.getElementById('busy') ;
      if(is_busy){
         busy.innerHTML="<img src='<?php echo IMG."/busy.gif"; ?>' title='Busy'>";
      }else{
         busy.innerHTML="<img src='<?php echo IMG."/busy-stopped.gif"; ?>' title='Not busy' >";
      }
   }
}
