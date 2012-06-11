<?php
function read_file($file, $lines) {
    //global $fsize;
    $handle = fopen($file, "r");
    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = array();
    while ($linecounter > 0) {
        $t = " ";
        while ($t != "\n") {
            if(fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true; 
                break; 
            }
            $t = fgetc($handle);
            $pos --;
        }
        $linecounter --;
        if ($beginning) {
            rewind($handle);
        }
        $text[$lines-$linecounter-1] = fgets($handle);
        if ($beginning) break;
    }
    fclose ($handle);
    return array_reverse($text);
}
$numLines=isset($_REQUEST['lines'])?$_REQUEST['lines']:20;
$lines = read_file(LOG,$numLines);
$report.="<div id='dd'><pre >";
foreach ($lines as $line) {
    $report.=$line;
}
$report.="</pre></div>";

$report.="<script type='text/javascript'>
function get_lines(){
   var lines=document.getElementById('toolbar.numLines').value;
   url='<?php echo gen_url();?>&lines='+lines;
   open(url,'_self');
}

function print_data(){
   var mywin=window.open('','blank');
   mywin.innerHTML=document.getElementById('dd').innerHTML;
   mywin.print();
}
</script>";
add_to_main_top($report);
?>
