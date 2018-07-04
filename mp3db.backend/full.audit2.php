#!/usr/bin/php

# This script compares the contents of the filesystem to the database and reports and
# reports any missing files that have db recs, or files with missing db recs.

<?php
require_once 'CONSTS.php';
require_once 'mp3.class.php';

$PATHS=array('/DWH/MP3.car32/New');//,'/4TB-RAID5/MP3.D-list');


// getting FILES array:
print "getting FILES array:\n";
$MP3s=array();
$counter=0;

foreach ($PATHS as $PATH) {
	print "Reading file info from $PATH \n\n";
	$rdi = new RecursiveDirectoryIterator($PATH);
	foreach (new RecursiveIteratorIterator($rdi) as $filename => $file) {

		$counter++;
		if ( $counter % 10==0 )
			print ".";
		if ( $counter % 100==0 )
			print " ";
		if ( $counter % 1000==0 )
			print $counter."\n";


		if ( substr($filename,-3)=="/.." || substr($filename,-2)=="/." ) {
			continue;
		}

		if ( strtolower(substr($filename,-4))!=".mp3" ) {
			continue;
		}
		//print "Creating mp3 object.\n";
		$MP3s[$filename]=new MP3($filename);
		$MP3s[$filename]->display();

	} // end foreach file
} // end foreach PATH...
print "\n\nMP3s array complete. [".$counter."][".count($MP3s)."]\n\n";


// getting DB array:
//$link = mysqli_connect($DB_HOST,$DB_USER,$DB_PASS,$DB_DB);
foreach ($PATHS as $PATH) {
	print "getting DB array:\n";
	$res=select("select * from mp3s where f_path like '".$PATH."%'");
	$DBRECS=array();

	$counter=0;

	foreach ( $res as $rec ) {

		$counter++;
		if ( $counter % 10==0 )
			print ".";
		if ( $counter % 100==0 )
			print " ";
		if ( $counter % 1000==0 )
			print $counter."\n";

		$DBRECS[$rec['f_path']]=array(
			"f_path"=>$rec['f_path'],
			"f_md5"=>$rec['f_md5']
		);
	}
} // end foreach PATH
print "DB array complete. [".$counter."][".count($DBRECS)."\n\n";


// comparing db and files arrays:
print "Comparing ".count($MP3s)." FILES to ".count($DBRECS)." DBRECS:\n";
$errors=0;
$acounter=0;


// checking files against db:
foreach ( $MP3s as $mp3 ) {

	$mp3data=$mp3->getMp3Data();

	print "mp3data:\n";
	print_r($mp3data);

	if ( !isset($DBRECS[$mp3data['filename']]) ) {
		print "================================================\n";
		print "ERROR: File ".$mp3data['filename']." has no DB rec..\n";
		print "================================================\n";
		$errors++;
		$mp3->save();
		continue;
	}

	$rec=$DBRECS[$mp3data['filename']];
	if ( $mp3data['md5'] != $rec['f_md5'] ) {
		print "ERROR: File ".$mp3data['filename']." dbrec mismatch:\n";
		print "--------------------- FILE: ----------------------\n";
		$mp3->display();
		print "--------------------- REC: ----------------------\n";
		print_r($rec);
		print "-------------------------------------------------\n";
		$errors++;
	} else {
		//print "File has a matching DB rec: ".$file['f_md5']." - ".$file['f_path']."\n";
	}
}
print "Found ".$errors." errors checking files against db..\n\n";

return;

// checking db against files:
print "Comparing ".count($DBRECS)." DBRECS to ".count($FILES)." FILES:\n";
$errors=0;
$acounter=0;
foreach ( $DBRECS as $rec ) {
	$acounter++;
	if ( !isset($FILES[$rec['f_path']]) ) {
		print "================================================\n";
		print "ERROR: Rec ".$rec['f_path']." has no file..\n";
		print "================================================\n";
		continue;
	}
	$file=$FILES[$rec['f_path']];
	if ( $file['f_md5'] != $rec['f_md5'] ) {
		print "ERROR Rec ".$rec['f_path']." file mismatch:\n";
		print "--------------------- REC: ----------------------\n";
		print_r($rec);
		print "--------------------- FILE: ----------------------\n";
		print_r($file);
		print "-------------------------------------------------\n";
		$errors++;
	} else {
		//print "DB rec has a matching file: ".$rec['f_md5']." - ".$rec['f_path']."\n";
	}
}

print "Found ".$errors." errors..\n\n";
print "COMPLETE - ".count($allfiles)." files read!\n";
?>
