dependencies = {
	stripConsole: "normal",
	layers: [
		{
			name: "../dijit/ucscis.js",
			dependencies: [
				"dijit.layout.ContentPane",
            "dijit.layout.BorderContainer",
            "dijit.layout.TabContainer",
            "dijit.layout.AccordionContainer",
            "dijit.layout.AccordionPane",
				"dijit.Dialog",
 	 	 	 	"dijit.MenuBar",
 	 	 	 	"dijit.PopupMenuBarItem",
 	 	 	 	"dijit.Menu",
 	 	 	 	"dijit.MenuItem",
 	         "dijit.MenuSeparator",
 	 	 	 	"dijit.PopupMenuItem",
 	 	 	 	"dijit.Toolbar",
            "dijit.ToolbarSeparator"
 	 	 	   "dijit.Tree",
				"dijit.InlineEditBox",
				"dijit.TooltipDialog",
				"dijit.Tooltip",
				"dijit.form.TimeTextBox",
 	 	 	 	"dijit.form.ComboButton",
            "dijit.form.FilteringSelect",
            "dijit.form.ComboBox",
 	 	 	 	"dijit.form.RadioButton",
 	 	 	 	"dijit.form.CheckBox",
 	 	 	 	"dijit.form.TextBox",
 	 	 	 	"dijit.form.Textarea",
 	 	 	 	"dijit.form.DateTextBox",
            "dijit.form.NumberTextBox",
 	 	 	 	"dijit.form.NumberSpinner",
            "dijit.form.HorizontalSlider",
 	 	 	 	"dijit.form.ValidationTextBox",
 	 	 	 	"dijit.form.Form",
            "dijit.form.Button",
            "dijit.form.DropDownButton",
				"dijit.ProgressBar",
            "dijit._base",
  			]
		},
		{
			name: "../dojo/ucscis.js",
			dependencies: [
 	 	 	 	"dojo.data.ItemFileReadStore",
 	 	 	 	"dojo.data.ItemFileWriteStore",
 	 	 	 	"dojo.io.iframe",
            "dojo.selector.acme",
            "dojo.cldr.nls.en.number",
            "dojo.fx.Toggler",
            "dojo.dnd.AutoSource",
            "dojo.dnd.Target",
			]
		},
		{
			name: "../dojox/ucscis.js",
			dependencies: [
 	 	 	 	"dojox.grid.DataGrid",
            "dojox.grid.EnhancedGrid",
            "dojox.grid.enhanced.plugins.exporter.CSVWriter",
            "dojox.grid.enhanced.plugins.Printer",
            "dojox.data.HtmlTableStore",
 	 	 	 	"dojox.data.QueryReadStore",
 	 	 	 	"dojox.data.CsvStore",
            "dojox.form.PasswordValidator",
            "dojox.widget.PlaceholderMenuItem",
			]
		}
	],

	prefixes: [
		//[ "dojo", "../dojo" ],
		[ "dijit", "../dijit" ],
		[ "dojox", "../dojox" ]
	]
};
