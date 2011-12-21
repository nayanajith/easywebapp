<style type='text/css'>
    @import "<?php echo JS; ?>/dojox/grid/resources/Grid.css";
    @import "<?php echo JS; ?>/dojox/grid/resources/claroGrid.css";
    .dojoxGrid table { margin: 0; } html, body { width: 100%; height: 100%;
    margin: 0; }
</style>


<script type='text/javascript'>
   dojo.require("dojox.grid.DataGrid");
   dojo.require("dojo.data.ItemFileWriteStore");
</script>




<span dojoType='dojo.data.ItemFileWriteStore' jsId='store4' url='/sis/mod/student/bcsc_student_data.json'></span>
<table dojoType='dojox.grid.DataGrid' jsId='grid4' store='store4' query="{ name: '*' }"
   rowsPerPage='20' clientSort='true' style='width: 310px; height: 300px;'
   rowSelector='20px'>
    <thead>
        <tr>
            <th width='auto' field='id'>
               Index No    
            </th>
            <th width='auto' field='name'>
               Full Name
            </th>
            <th width='auto' field='sex' cellType='dojox.grid.cells.Select' options='Male,Female' editable='true'>
               Sex
            </th>
        </tr>
    </thead>
   </table>



   <span dojoType="dojo.data.ItemFileWriteStore" jsId="store3" url="/sis/mod/student/countries.json">
   </span>
        <p class="info">
            This example shows, how to make the column "Type" editable. In order to
            select a new value, you have to double click on the current value in the
            second column.
        </p>
        <table dojoType="dojox.grid.DataGrid" jsId="grid3" store="store3" query="{ name: '*' }"
        rowsPerPage="20" clientSort="true" style="width: 100%; height: 300px;"
        rowSelector="20px">
            <thead>
                <tr>
                    <th width="200px" field="name">
                        Country/Continent Name
                    </th>
                    <th width="auto" field="type" cellType="dojox.grid.cells.Select" options="country,city,continent"
                    editable="true">
                        Type
                    </th>
                </tr>
            </thead>
        </table>
