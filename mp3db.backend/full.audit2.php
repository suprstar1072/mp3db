#!/usr/bin/php

# This script compares the contents of the filesystem to the database and reports and
# reports any missing files that have db recs, or files with missing db recs.

<?php
require_once 'CONSTS.php';
require_once 'mp3.class.php';

$debug=0;
$totalerrors=0;
$fixed=0;

//$PATHS=array($GLOBALS['mp3path']);
$PATHS=array("/DWH/MP3/","/DWH/MP3.car32");
//$PATHS=array("/DWH/MP3/");
//$PATHS=array("/DWH/MP3.car32");
//$PATHS=array("/DWH/MP3/r");
print "Auditing:\n";
print_r($PATHS);


// load MP3 array from filesystem:
print "getting FILES array:\n";
$MP3s=array();
$counter=0;

foreach ($PATHS as $PATH) {
	print "Reading file info from $PATH \n\n";
	$rdi = new RecursiveDirectoryIterator($PATH);
	foreach (new RecursiveIteratorIterator($rdi) as $filename => $file) {
		if ( substr($filename,-3)=="/.." || substr($filename,-2)=="/." ) {
			continue;
		}
		if ( strtolower(substr($filename,-4))!=".mp3" ) {
			continue;
		}

		$counter++;
		if ( !$debug ) {
			if ( $counter % 10==0 )
				print ".";
			if ( $counter % 100==0 )
				print " ";
			if ( $counter % 1000==0 )
				print $counter." - ".date("H:i:s")."\n";
		}

		if( $debug ) {
			print "$counter ==============================================================================================\n";
			print $filename."\n";
			print "Creating mp3 object.\n";
		}
		$MP3s[$filename]=new MP3($filename);
		if ( $debug )
			$MP3s[$filename]->display();

	} // end foreach file

	print " $counter files read from $PATH.\n\n";

} // end foreach PATH...
print "\n\nMP3s array complete. [".$counter."][".count($MP3s)."]\n\n";


// getting DB array:


$DBRECS=array();
$counter=0;
foreach ($PATHS as $PATH) {
	print "getting DB array:\n";
	$res=select("select * from mp3s where f_path like '".$PATH."%'");


	// converting from iterative array to keyed array:
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
			"f_md5"=>$rec['f_md5'],
			"f_id"=>$rec['f_id']
		);
	}
} // end foreach PATH
print "DB array complete.  Retrieved ".count($DBRECS)." database records\n\n";


// comparing db and files arrays:
print "Comparing ".count($MP3s)." FILES to ".count($DBRECS)." DBRECS:\n";
$fileerrors=0;
$counter=0;


// checking files against db:
foreach ( $MP3s as $mp3 ) {
	$counter++;
	$mp3data=$mp3->getMp3Data();
	if ( $debug ) {
		print "$counter ===============================================================================\n";
		print "mp3data from read-in file:\n";
		print_r($mp3data);
	}

	if ( !isset($DBRECS[$mp3data['filename']]) ) {
		print "ERROR: New file found - ".$mp3data['filename']." has no DB rec..\n";
		print_r($mp3data);
		$fileerrors++;
		print "Creating db record:\n";
		$mp3->create();
		$fixed++;
		continue;
	}

	$rec=$DBRECS[$mp3data['filename']];
	if ( $mp3data['md5'] != $rec['f_md5'] ) {
		print "ERROR: File ".$mp3data['filename']." dbrec mismatch:\n";
		print "MP3 object:\n";
		$mp3->display();
		print "DB rec:\n<PRE>";
		print_r($rec['f_md5']);
		print "</PRE>";

		print "Attempting to update playlists:\n";
		runSQL("update playlists set p_md5='".$mp3data['md5']."' where p_md5='".$rec['f_md5']."'",1);

		print "Attempting to delete/recreate db rec:\n";
		runSQL("delete from mp3s where f_id=".$rec['f_id'],1);
		$mp3->save();

		print "Updates complete.\n";
		print_r($rec);
		$fixed++;
		$fileerrors++;
	} else {
		if ( $debug )
			print "File has a matching DB rec: ".$mp3data['md5']." - ".$mp3data['filename']."\n";
	}
}
print "Found ".$errors." errors checking files against db..\n\n";
$totalerrors+=$fileerrors;





// checking db against files:
print "Comparing ".count($DBRECS)." DBRECS to ".count($MP3s)." FILES:\n";
$dberrors=0;
$dbfixed=0;
$counter=0;
foreach ( $DBRECS as $rec ) {
	$counter++;

	if ( $debug ) {
		print "$counter =====================================================================\n";
		print_r($rec);
	}
	if ( !isset($MP3s[$rec['f_path']]) ) {
		print "ERROR: DB rec ".$rec['f_path']." has no file..\n";
		runSQL("delete from mp3s where f_id=".$rec['f_id']);
		$dberrors++;
		$dbfixed++;
		continue;
	}
	$mp3=$MP3s[$rec['f_path']];
	$mp3data=$mp3->getMp3Data();
	if ( $mp3data['md5'] != $rec['f_md5'] ) {
		print "ERROR Rec ".$rec['f_path']." file mismatch:\n";
		print "DB rec:\n";
		print_r($rec);
		print "MP3 object:\n";
		print_r($mp3data);
		$dberrors++;
	} else {
		if ( $debug )
			print "DB rec has a matching file: ".$rec['f_md5']." - ".$rec['f_path']."\n";
	}
}
$totalerrors+=$errors;

print "\n---------------------------------------------------------------\n";
print "COMPLETE - ".count($MP3s)." files and ".count($DBRECS)." read!\n";
print "Found ".$fileerrors." file errors.\n";
print "Fixed ".$fixed." file errors.\n";
print "Found ".$dberrors." db errors.\n";
print "Fixed ".$dbfixed." db errors.\n";
print "---------------------------------------------------------------\n";

?>
