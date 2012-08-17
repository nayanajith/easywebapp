dependencies={
	stripConsole: "normal",
	selectorEngine:"acme",
	layers: [
		{
			name: "../dijit/ucscis.js",
			dependencies: [
             "dijit.MenuBar",
             "dijit.Menu",
             "dijit.MenuItem",
             "dijit.PopupMenuBarItem",
             "dijit.DropDownMenu",
             "dijit.Toolbar",
             "dijit.form.NumberTextBox",
             "dijit.form.ValidationTextBox",
             "dijit.form.Select",
             "dijit.form.FilteringSelect",
             "dijit.form.DateTextBox",
             "dijit.form.TimeTextBox",
             "dijit.form.Form",
             "dijit.TooltipDialog",
             "dijit.form.CheckBox",
             "dijit.form.DropDownButton",
             "dijit.ProgressBar",
             "dijit.Dialog",
             "dijit.form.Button",
             "dijit.Toolbar",
             "dijit.MenuBar",
             "dijit.Menu",
             "dijit.MenuItem",
             "dijit.PopupMenuBarItem",
             "dijit.DropDownMenu",
             "dijit.layout.ContentPane",
             "dijit.layout.BorderContainer",
             "dijit.form.NumberSpinner",
             "dijit.form.SimpleTextarea",
             "dijit.form.ComboBox",
             "dijit.TitlePane",
             "dijit.Editor",
             "dijit.popup",
             "dijit._editor.plugins.FontChoice",
             "dijit._editor.plugins.TextColor",
             "dijit._editor.plugins.LinkDialog",
  			]
		},
		{
			name: "../dojo/ucscis.js",
			dependencies: [
 	 	 	 	"dojo.io.iframe",
            "dojo.fx",
            "dojo.cookie",
            "dojo.query",
			]
		},
		{
			name: "../dojox/ucscis.js",
			dependencies: [
            "dojox.grid.EnhancedGrid",
            "dojox.grid.enhanced.plugins.Pagination",
            "dojox.grid.enhanced.plugins.NestedSorting",
            "dojox.grid.enhanced.plugins.Printer",
            "dojox.grid.enhanced.plugins.exporter.CSVWriter",
            "dojox.grid.enhanced.plugins.exporter.TableWriter",
            "dojox.form.PasswordValidator",
            "dojox.widget.PlaceholderMenuItem",
            "dojox.data.QueryReadStore",
            "dojox.data.JsonRestStore",
            "dojox.widget.PlaceholderMenuItem",
            "dojox.grid.EnhancedGrid",
            "dojox.grid.enhanced.plugins.Pagination",
            "dojox.grid.enhanced.plugins.Filter",
            "dojox.grid.enhanced.plugins.NestedSorting",
            "dojox.grid.enhanced.plugins.Printer",
            "dojox.grid.enhanced.plugins.exporter.CSVWriter",
            "dojox.grid.enhanced.plugins.exporter.TableWriter",
            "dojox.form.Uploader",
            "dojox.form.uploader.plugins.IFrame",
            "dojox.form.uploader.FileList"
			]
		}
	],

	prefixes: [
		[ "dijit", "../dijit" ],
		[ "dojox", "../dojox" ]
	]
}
