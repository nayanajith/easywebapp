<?php
if(isset($_REQUEST['action'])){ /*haldle requests*/
	switch($_REQUEST['action']){
      case 'generate_db':
			$drop		=drop_tables($GLOBALS["MOD_P_TABLES"]);
			$create	=create_tables($system_table_schemas);

			if(!$create){
				return_status_json('ERROR','Error in table recreation process, it may partially done!');
			}else{
				return_status_json('OK','Table re-generation completed successfully');
			}
		break;
	}
return;
}
d_r('dijit.form.Form');
d_r('dijit.form.Button');
?>
<form id='test_frm' dojoType='dijit.form.Form'>
<button dojoType='dijit.form.Button' onClick='submit_form("generate_db")'>
Regenerate database
</button>
</form>
<script language="javascript">
	function submit_form(action){
		update_status_bar('OK','...');
		update_progress_bar(10);
		if (dijit.byId('test_frm').validate()) {
			dojo.xhrGet({
			url			: '<?php echo gen_url(); ?>&action='+action, 
			handleAs		: 'json',
			form			: 'test_frm', 

			handle: function(response,ioArgs){
				update_status_bar(response.status,response.info);
				if(response.status=='ERROR'){	
					//update_progress_bar(0);
				}else{
					update_progress_bar(100);
				}
			},

			load: function(response) {
				update_status_bar('OK','rquest sent successfully');
				update_progress_bar(50);
			}, 
			error: function() {
				update_status_bar('ERROR','error on submission');
				update_progress_bar(0);
			}
		});

		return false;
	}else{
		update_status_bar('ERROR','Form contains invalid data.  Please correct first');
		return false;
	}
	return true;
}
</script>

