<table>
<tr><td rowspan="4"><img src="<?php echo IMG."/black_feather.png"; ?>"></td><td style="font-size:20px"><h2>YAPE</h2></td></tr>
<tr><td style="color:silver">you are currently on the release 3.0</td></tr>
<tr><td>YAPE is a web application framework which allow users to create a high grade web application in few minuts.</td></tr>
<tr><td>
<h3>Modules enabled</h3>
<ol>
<?php
foreach($GLOBALS['MODULES'] as $key =>$name){
   echo "<li>".$name;
}
?>
</ol>
</td></tr>
<tr><td colspan="2" style="color:gray; background-color:silver">Yape and yape logos are trade marks of UCSC. Developed by Nayanajith Mahendra Laxaman, mail: nml@ucsc.cmb.ac.lk</td></tr>
</table>
