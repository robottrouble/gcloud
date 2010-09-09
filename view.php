<?php

if(array_key_exists('file', $_GET))
        $baseFile = $_GET['file'];
else
        Header('Location: index.php');


$xskew=0;
$yskew=0;
$mag=1;
$layer = 1;

if($_POST)
{
        $controls = array("layer", "mag", "xskew", "yskew");
        foreach($controls as $control)
        {
                if(array_key_exists($control, $_POST))
                {
                        if($_POST[$control] == "+")
                                $$control = $_POST['cur_' . $control] + 1;
                        else if($_POST[$control] == "-")
                                $$control = $_POST['cur_' . $control] - 1;
                } else {
                        $$control = $_POST['cur_' . $control];
                }
        }
}
if($layer < 1)
        $layer = 1;

if($mag < 1)
        $mag = 1;

$currentFile = $baseFile . "." . $layer . ".php";

$dirh = opendir("uploads/" . $baseFile );

$layerCount=-2;

while(($file = readdir($dirh)))
{
        $layerCount++;
}
?>
<table width="100%" border=1 height="100%">
<tr>
<td>
        <iframe class="result_output" width="100%" height="100%" frameborder="0" name="view" src="uploads/<?php echo $baseFile . "/" . $currentFile; ?>?xskew=<?php echo $xskew * (100 / $mag) * -1;?>&yskew=<?php echo $yskew * (100 / $mag) * -1;?>&mag=<?php echo $mag ?>"></iframe>
</td>
<td align="center" width="10%">
        <form method="post">
        <input type="hidden" name="cur_xskew" value="<?php echo $xskew; ?>">
        <input type="hidden" name="cur_yskew" value="<?php echo $yskew; ?>">
        <input type="hidden" name="cur_mag" value="<?php echo $mag; ?>">
        <input type="hidden" name="cur_layer" value="<?php echo $layer; ?>">
        <table border=0>
                 <tr>
                        <td>Layer:</td>
                </tr>
                <tr>
                        <td><input type=text disabled size="2" value="<?php echo $layer; ?>"></td>
                        <td>
                                <?php if($layer >= $layerCount) { ?>
                                        <input type=Submit name="layer" value="+" disabled>
                                <?php } else { ?>
                                        <input type=Submit name="layer" value="+">
                                <?php } ?>
                        </td>
                        <td>
                                <?php if($layer <= 1) { ?>
                                        <input type=Submit name="layer" value="-" disabled>
                                <?php } else { ?>
                                        <input type=Submit name="layer" value="-">
                                <?php } ?>
                        </td>
                </tr>
        
                <tr>
                        <td>Zoom:</td>
                </tr>
                <tr>
                        <td>&nbsp;</td>
                        <td>
                                <input type=Submit name="mag" value="+">
                        </td>
                        <td>
                                <input type=Submit name="mag" value="-">
                        </td>
                </tr>
                <tr>
                        <td>Move:</td>
                </tr>
                <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><input type="Submit" name="yskew" value="+"></td>
                        <td>&nbsp;</td>
                </tr>
                <tr>
                        <td>&nbsp;</td>
                        <td><input type="Submit" name="xskew" value="+"></td>
                        <td>&nbsp;</td>
                        <td><input type="Submit" name="xskew" value="-"></td>
                </tr>
                <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><input type="Submit" name="yskew" value="-"></td>
                        <td>&nbsp;</td>
                </tr>
       </td>
       </table>
                
</td>
</tr>
</table>
