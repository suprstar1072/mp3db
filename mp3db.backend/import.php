#!/usr/bin/php
<?php
require 'CONSTS.php';

//$rdi = new RecursiveDirectoryIterator($GLOBALS['mp3path']);

$rdi = new RecursiveDirectoryIterator("/DWH/MP3");
//$rdi = new RecursiveDirectoryIterator("/DWH/MP3.dlist/");
//$rdi = new RecursiveDirectoryIterator("/DWH/MP3.dlist/");

$counter=$mp3count=0;
foreach (new RecursiveIteratorIterator($rdi) as $filename => $file) {
	$counter++;

	print $counter." ===================================================================================\n";
	print $filename."\n";

//	if ( $counter%10==0 )
//		print ".";
//	if ( $counter%100==0 )
//		print " ";
//	if ( $counter%1000==0 )
//		print "$counter\n";


	if ( substr($filename,-3)=="/.." || substr($filename,-2)=="/." ) {
		print "ERROR: skipping /.. or /.\n";
        continue;
	}

    if ( strtolower(substr($filename,-4))!=".mp3" ) {
        print "ERROR: skipping unknown file type ".substr($filename,-4)."\n";
        continue;
    }

	print "Creating mp3 object:\n";
	$mp3=new MP3($filename);
	$mp3->create(1);
	$mp3count++;
	print "MP3 saved successfully.\n";

} // end foreach file in $PATH

print "Found $counter files and $mp3count mp3s.\n";


?>
