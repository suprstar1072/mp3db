#!/usr/bin/php
<?php
require 'CONSTS.php';

//$rdi = new RecursiveDirectoryIterator($GLOBALS['mp3path']);

$rdi = new RecursiveDirectoryIterator("/DWH/MP3.dlist");
//$rdi = new RecursiveDirectoryIterator("/DWH/MP3");
$counter=$mp3count=0;
foreach (new RecursiveIteratorIterator($rdi) as $filename => $file) {
	$counter++;

//	print $counter." ===================================================================================\n";
//	print $filename."\n";

	if ( $counter%10==0 )
		print ".";
	if ( $counter%100==0 )
		print " ";
	if ( $counter%1000==0 )
		print "$counter\n";


	if ( substr($filename,-3)=="/.." || substr($filename,-2)=="/." ) {
//		print "ERROR: skipping /.. or /.\n";
        continue;
	}

    if ( strtolower(substr($filename,-4))!=".mp3" ) {
//        print "ERROR: skipping unknown file type ".substr($filename,-4)."\n";
        continue;
    }

	$res="select * from mp3s where f_path='".addslashes($filename)."'";
	if ( count($res)!=1 )
		print "File not found: ".$filename."\n";


} // end foreach file in $PATH

//print "Found $counter files and $mp3count mp3s.\n";


?>
