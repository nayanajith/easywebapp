<table>
   <tr>
      <td rowspan="4">
         <img src="<?php echo IMG."/black_feather.svg"; ?>" width="150px">
      </td>
      <td style="font-size:25px">
         YAPE
      </td>
   </tr>
   <tr>
      <td style="color:gray">
         You are currently on the release 3.0 <a href="http://kammala.cmb.ac.lk">Find updates</a>
      </td>
   </tr>
   <tr>
      <td>
         YAPE is a web application framework which allow users to create a high grade web application in few minuts.
      </td>
   </tr>
   <tr>
      <td >
         <div style="font-weight:bold">Modules enabled</div>
         <div style="overflow:auto;overflow-x:hidden;height:110px">
            <ul>
<?php
foreach($GLOBALS['MODULES'] as $key =>$name){
   echo "<li>".$name;
}
?>
            </ul>
         </div>
      </td>
   </tr>
   <tr>
      <td colspan="2" style="color:gray; background-color:whitesmoke;font-size:8px;align:center" align="center">
         Yape and yape logos are trade marks of UCSC.<br>Developed by Nayanajith Mahendra Laxaman, mail: nml@ucsc.cmb.ac.lk
      </td>
   </tr>
</table>
