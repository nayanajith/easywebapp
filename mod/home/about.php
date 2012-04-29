<table>
<tr><td rowspan="4"><img src="<?php echo IMG."/black_feather.svg"; ?>" width="150px"></td><td style="font-size:25px">YAPE</td></tr>
<tr><td style="color:silver">you are currently on the release 3.0</td></tr>
<tr><td>YAPE is a web application framework which allow users to create a high grade web application in few minuts.</td></tr>
<tr><td >
<h3>Modules enabled</h3>
<div style="overflow:scroll;height:100px">
<ul>
<?php
foreach($GLOBALS['MODULES'] as $key =>$name){
   echo "<li>".$name;
}
?>
</ul>
</div>
</td></tr>
<tr><td colspan="2" style="color:gray; background-color:silver;font-size:8px">Yape and yape logos are trade marks of UCSC. Developed by Nayanajith Mahendra Laxaman, mail: nml@ucsc.cmb.ac.lk</td></tr>
</table>
