#!/usr/bin/php
<?php

require 'CONSTS.php';

$fh=fopen("/DWH/MP3/list","r");
$counter=0;
while (($line = fgets($fh)) !== false) {
	$counter++;
//	print "$line";
	$res=select("select * from mp3s where f_path='".addslashes(trim($line))."'");
	if ( count($res)!=1 ) {
		print "file not found in DB: ".$line;
//		print "Attempting to create:\n";
//		$mp3=new MP3(addslashes(trim($line)));
//		$mp3->create(1);
	}
}

print "Found $counter lines.\n";
?>
