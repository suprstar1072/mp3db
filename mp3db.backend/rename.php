#!/usr/bin/php
<?php

include 'CONSTS.php';

$counter=0;

$res=select("select * from mp3s order by f_path");
foreach ($res as $row) {
	$counter++;
	print "$counter -----------------------------------------------------------------------------------\n";
	print_r($row);
    $newdir="/DWH/MP3New/";

	$init=strtolower(substr($row['f_artist'],0,1));
	if ( strpos("x0123456789",$init) )
		$init="0-9";

	// calculate new dirname
	$newdir.=$init."/".$row['f_artist']."/";
	if ( $row['f_album'] )
		$newdir .= $row['f_album']."/";

	// create new dir if necessary
	if ( is_dir($newdir) ) {
		print $newdir." exists\n";
	} else {
		if ( mkdir($newdir,0777,true) )
			print "Dir $newdir made\n";
		else
			print "ERROR creating $newdir.\n";
	}

	// calculate new filename
	$filename="";
	if ( $row['f_track'] ) {
		if ( strlen($row['f_track'])==1 )
			$filename.="0";
		$filename.=$row['f_track']." - ";
	}
	if ( $row['f_title'] )
		$filename.=$row['f_title'].".mp3";

	// check for new path, iterate an index if file exists.
	$newpath=$newdir.$filename;
	while ( file_exists($newpath) ) {
		print "ERROR - file $newpath exists!\n";
		$iter="2";
		$newpath=substr($newpath,0,strlen($newpath)-4).$iter.".mp3";
	}

	// verify paths
	print "old: ".$row['f_path']."\n";
    print "new: ".$newpath."\n";

	// actually copy the file
	if ( copy($row['f_path'],$newpath) )
		print "File copied!\n";
	else
		print "ERROR - file didn't copy..\n";

}
