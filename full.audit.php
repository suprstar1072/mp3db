#!/usr/bin/php

# This script compares the contents of the filesystem to the database and reports and
# reports any missing files that have db recs, or files with missing db recs.

<?php
require 'CONSTS.php';

$PATHS=array('/DWH/MP3.car32/');//,'/4TB-RAID5/MP3.D-list');
$link = mysqli_connect($DB_HOST,$DB_USER,$DB_PASS,$DB_DB);
$counter=0;


// getting FILES array:
print "getting FILES array:\n";
$FILES=array();
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

		$md5=explode("  ", exec("md5sum ".escapeshellarg($filename)));

		$output=array();

		$FILES[$filename]=array(
			"f_path"=>$filename,
			"f_md5"=>$md5[0]
		);

	} // end foreach file
} // end foreach PATH...
print "\n\nFILES array complete. [".$counter."][".count($FILES)."]\n\n";


// getting DB array:
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
print "Comparing ".count($FILES)." FILES to ".count($DBRECS)." DBRECS:\n";
$errors=0;
$acounter=0;


// checking files against db:
foreach ( $FILES as $file ) {
	if ( !isset($DBRECS[$file['f_path']]) ) {
		print "================================================\n";
		print "ERROR: File ".$file['f_path']." has no DB rec..\n";
		print "================================================\n";
		$errors++;
		continue;
	}
	$rec=$DBRECS[$file['f_path']];
	if ( $file['f_md5'] != $rec['f_md5'] ) {
		print "ERROR: File ".$file['f_path']." dbrec mismatch:\n";
		print "--------------------- FILE: ----------------------\n";
		print_r($file);
		print "--------------------- REC: ----------------------\n";
		print_r($rec);
		print "-------------------------------------------------\n";
		$errors++;
	} else {
		print "File has a matching DB rec: ".$file['f_md5']." - ".$file['f_path']."\n";
	}
}
print "Found ".$errors." errors checking files against db..\n\n";


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
		print "DB rec has a matching file: ".$rec['f_md5']." - ".$rec['f_path']."\n";
	}
}

print "Found ".$errors." errors..\n\n";
print "COMPLETE - ".count($allfiles)." files read!\n";
?>
