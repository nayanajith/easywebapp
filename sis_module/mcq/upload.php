<?php
$sText='';
foreach ($_FILES as $k => $v)
{
$sText.=$k.'\n';
foreach ($v as $kk => $vv)
if ($kk != 'tmp_name')
$sText.=' '.$kk.'='.$vv.'\n';
}
?>
<html>
<head>
<script type="text/javascript">isLoaded = true;</script>
</head>
<body>
<textarea>{'status':'Good','textdata':'<?php echo $sText; ?>'}</textarea>
</body>
</html>
