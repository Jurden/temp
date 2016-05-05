<?php
header('Content-type: image/png');
$time_c = new DateTime('now');
$hour_c = $time_c->format('H');
$min_c = $time_c->format('i');
$cur=$hour_c*60+$min_c;
# Image is #number of minutes in 24 hours wide, plus some room for 
# Y labels. 1000 height to make it look nice.
$xsize = (60*24)+100;
$ysize = 1100;
$img = imagecreate($xsize, $ysize);
# Define some colors
$black = imagecolorallocate($img, 0, 0, 0);
$white = imagecolorallocate($img, 255, 255, 255);
$dimwhite = imagecolorallocate($img, 50, 50, 50);
$red = imagecolorallocate($img, 255, 0, 0);
$yellow = imagecolorallocate($img, 255, 255, 0);
$avg_size = 10;
$array = [];
for ($i=0;$i<$avg_size;$i++) {
	$array[$i]=0;
}
$i = 0;
# Get temperature
# Total number of points
$values = 24*60;
# Load sqlite database
$db = new PDO('sqlite:/var/log/temp.db');
if (!$db) die ("Could not open database");
# Get table values
$sq = $db->query('SELECT * FROM (SELECT * FROM temp ORDER BY time DESC limit '.$values.') as r ORDER BY r.time ASC');
# Y parallel grid
for ($x=100; $x<=($xsize); $x+=60) {
	imagestring($img, 5, ($x-$min_c+40), ($ysize-100), ($hour_c+($x-40)/60)%24, $white);
	imageline($img, $x+60-$min_c,  0, $x+60-$min_c, ($ysize-80), $white);
}
# Y label
imagestring($img, 5, 10, ($ysize/2), 'Temp, F', $white);
# X parallel grid
for ($y=0; $y<=($ysize-100); $y+=10) {
	if (($y%100) == 0) {
		imagestring($img, 5, 80, ($ysize-$y-120), ($y)/10, $white);
		imageline($img, 80,  $y, ($xsize), $y, $white);
	} else {	
		imageline($img, 100,  $y, ($xsize), $y, $dimwhite);
	}
}
# X label
imagestring($img, 5,  ($xsize/2)+50, ($ysize-50), 'Time, Hours', $white);
# Go through each row in temp
foreach ($sq as $row) {
	$sum = 0;
	# Shift oldest value out of array
	for ($j=1; $j<$avg_size; $j++) {
		$array[$j-1]=$array[$j];
		$sum+=$array[$j-1];
	}
	# Get temp from table and average it
	$array[$avg_size-1] = $row[1]*10;
	$sum+=$array[$avg_size-1];
	$sum= $sum/$avg_size;
	# Get time from table
	preg_match("/(\d{2}):(\d{2}):/", $row[0], $matches[]);
	$time = ($matches[$i][1]*60 + $matches[$i][2]);
	$min = $matches[$i][0];
	$hour = $matches[$i][1]; 
	# Keep track of number of loops
	$i++;
	# Scale time for graph
	$newtime =($time-$cur);
	if ($newtime>0) {
		$newtime+=100;
	} else {
		$newtime+=1540;
	}
	# Once there is enough samples
	if($i > 10){
		# Draw the line
		imageline($img, $newtime-1, $ysize-$tempold-100, $newtime, $ysize-$sum-100, $yellow);
	}
	# Save last value
	$tempold=$sum;
}
imagepng($img);
imagedestroy($img);
?>
