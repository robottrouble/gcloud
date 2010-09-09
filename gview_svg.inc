<?php


function gcode_to_svg($inFile)
{
        $fh = fopen($inFile, 'r');
        $cur[X]="";
        $cur[Y]="";
        $last[X]="";
        $last[Y]="";
        $last[Z]="";
        $cur[Z]="";
        $max[X]=0;
        $max[Y]=0;
        $max[Z]="";
        $min[X]=0;
        $min[Y]=0;
        $min[Z]="";
        $resolution = 3;


        $xskew = 0;
        $yskew = 0;
        $xmag = 1;
        $ymag = $xmag;

        $layers=array();

        while(!feof($fh))
        {
                $buf = fgets($fh, 1024);
                $bufsplit = explode(" ", $buf);

                if(is_array($bufsplit) && sizeof($bufsplit) > 0 && preg_match("/^G[1|0]$/", $bufsplit[0]))
                {
                        for($i=1; $i<sizeof($bufsplit); $i++)
                        {
                                $curcode = substr($bufsplit[$i], 0, 1);
                                $val = substr($bufsplit[$i], 1, strlen($bufsplit[$i]));
                                $cur[$curcode] = $val;
                        }

                        $displayLine = true;

                        if($cur['Z'] != $last['Z'])
                                $displayLine = false;

			$layers[$cur['Z']][] = array( 'X' => $cur['X'], 'Y' => $cur['Y'], 'Z' => $cur['Z'], 'display' => $displayLine );

                        if($min['X'] == '' || $cur['X'] < $min['X'])
                        {
                                $min['X'] = $cur['X'];
                        }

                        if($min['Y'] == '' || $cur['Y'] < $min['Y'])
                        {
                                $min['Y'] = $cur['Y'];
                        }

                        $last['X'] = $cur['X'];
                        $last['Y'] = $cur['Y'];
                        $last['Z'] = $cur['Z'];
                }
        }

        if(!is_numeric($min['X']))
                $min['X'] = 0;

        if(!is_numeric($min['Y']))
                $min['Y'] = 0;

        if($min['X'] < 0)
                $min['X'] = $min['X'] * -1;

        if($min['Y'] < 0)
                $min['Y'] = $min['Y'] * -1;

        $min['X'] += 10;
        $min['Y'] += 10;

        $layerCount = 1;
        ksort($layers);

        $svg_template='<?php 
Header("Content-type: image/svg+xml");
$xskew=' . $min['X'] * $resolution . ';
$yskew=' . $min['Y'] * $resolution . ';
if(is_array($_GET))
{
        $xskew+=$_GET["xskew"];
        $yskew+=$_GET["yskew"];
        $mag=$_GET["mag"];
}

if($mag < 1)
        $mag=1;
?>
<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">

<svg width="100%" height="100%" version="1.1" xmlns="http://www.w3.org/2000/svg">
<rect width="100%" height="100%"
style="fill:rgb(0,0,0);stroke-width:1;
stroke:rgb(0,0,0)"/>
<g transform="scale(<?php echo $mag?>)">
<g transform="translate(<?php echo $xskew . ", " . $yskew; ?>)">';

        foreach($layers as $layer => $coords)
        {
                if(sizeof($coords) < 4)
                        continue;

	
                $last['X'] = '';
                $last['Y'] = '';
	
                $outParts = explode(".", $inFile);
                array_pop($outParts);
                $dirName = implode(".", $outParts) . "/";
                if(!is_dir($dirName))
                {
                        mkdir($dirName);
                }
                $outParts[] = $layerCount;
                $outParts[] = 'php';
                $outFile = $dirName . basename(implode(".", $outParts));

                if(!$fh = fopen($outFile, "w+"))
                {
                        echo "Failed to create file $outFile";
                        return 1;
                }

                fputs($fh, $svg_template);

                foreach($coords as $cur)
                {
                        if($last['X'] != '' && $last['Y'] != '')
                        {
                                $color="255,0,0";

                                if($cur['display'])
                                        $line = sprintf('<line x1="%s" y1="%s" x2="%s" y2="%s" style="stroke:rgb(%s);stroke-width:.1%%"/>', $last['X'] * $resolution, $last['Y'] * $resolution, $cur['X'] * $resolution, $cur['Y'] * $resolution, $color);
                                fputs($fh, $line . "\n");
                        }
                        $last['X'] = $cur['X'];
                        $last['Y'] = $cur['Y'];
                }

                fputs($fh, '</g></g></svg>');

                $layerCount++;
        }

        return true;
}
