<?php
class Data_grid{
   public function __construct(){
     d_r('dojox.widget.PlaceholderMenuItem');
     d_r('dojox.grid.EnhancedGrid');
     d_r("dojox.grid.enhanced.plugins.exporter.CSVWriter");
     d_r("dojox.grid.enhanced.plugins.Printer");
   }
   //type -> CSV,TABLE
   public function gen_data_grid($field_array,$csv_url_or_tableid,$key,$type,$title=''){
       $output= "
        <script type='text/javascript' >
            //acction to perform when the user clicked on the grid
            function grid_clicked(e){
               var selectedValue = grid.store.getValue(grid.getItem(e.rowIndex),'".$key."');
               alert('selected cell Value is '+selectedValue);
            }

            //Expor the gird to csv and insert it in the textarea so the user can copy and paste
            function csv_grid(){
               info_dialog(
                  '<textarea cols=\"150\" rows=\"30\"id=\"output\"></textarea>',
                  'CSV of Grid | Copy and paste',
                  '<button dojoType=\"dijit.form.Button\" onClick=\"dojo.byId(\'output\').select()\">Select all</button>',
                  800,
                  600 
               );
               dijit.byId('grid').exportGrid(
                  'csv', 
                  {writerArgs: {separator:',',enclosure:'\''}},
                  function(str){
                     dojo.byId('output').value = str;
                  }
               );
            }
            
            //style to be used when printing the grid
            var cssFiles = [
               '".CSS."/grid_print.css'
            ];

            //Prit the grid 
            function print_grid(){
               dijit.byId('grid').printGrid({
                  title:'".str_replace('\'','"',$title)."',
                  cssFiles: cssFiles
                  });
             }

            //Add two buttons to toolbar in order to  print the grid and export the grid to csv
            dojo.addOnLoad(function() {
               //reference to our toolbar
               var toolbar = dijit.byId('toolbar');

               var print_grid_btn=new dijit.form.Button({
                  iconClass:'dijitIcon dijitIconPrint',
                  label: 'Print Grid',
                  onClick:print_grid,
               });

               toolbar.addChild(print_grid_btn);


               var csv_grid_btn=new dijit.form.Button({
                  iconClass:'dijitIcon dijitIconFunction',
                  label: 'CSV Grid',
                  onClick:csv_grid,
               });

               toolbar.addChild(csv_grid_btn);
            });

         </script>
         ";

      if($type == 'CSV'){
         d_r('dojox.data.CsvStore');
         $output.= "<span dojoType='dojox.data.CsvStore' jsId='gridStore' url='".$csv_url_or_tableid."'></span>";
      }else{
         d_r('dojox.data.HtmlTableStore');
         $output.= "<span dojoType='dojox.data.HtmlTableStore' tableId='".$csv_url_or_tableid."' jsId='gridStore'></span>";
         $output.= "<script type='text/javascript'>dojo.byId('".$csv_url_or_tableid."').style.display='none';</script>";
      }
        $output.="<div dojoType='dijit.Menu' jsid='gridMenu' id='gridMenu' style='display: none;'>
            <div dojoType='dojox.widget.PlaceholderMenuItem' label='GridColumns'></div>
         </div>

         <table 
            dojoType    ='dojox.grid.EnhancedGrid' 
            jsId        ='grid' 
            id          ='grid' 
            store       ='gridStore' 
            query       ='{ ".$key.": \"*\" }'
            rowsPerPage ='40' 
            clientSort  ='true' 
            style       ='width:100%;height:400px' 
            onClick     ='grid_clicked'
            rowSelector ='20px'
            columnReordering='true'
            headerMenu  ='gridMenu'
            plugins     ='{printer:true,exporter:true}'
         >
         <thead>
            <tr>";
            /*Set labels for the table header if available in fileds array*/
            foreach($field_array as $key => $label){
               $width='auto';
               if(isset($label['width'])){
                  $width=$label['width'];
                  $label=$label['label'];
               }
               $output.= "<th width='$width' field='$key'>$label</th>";
            }
            //<th width='auto' field='sex' cellType='dojox.grid.cells.Select' options='Male,Female' editable='true'>Sex</th>
            $output.= "</tr>
         </thead>
         </table>";
        return $output;
    }
}

?>
