#!/usr/bin/php
<?
require 'CONSTS.php';

$link = mysqli_connect($DB_HOST,$DB_USER,$DB_PASS,$DB_DB);
$rdi = new RecursiveDirectoryIterator($PATH);

$counter=0;
foreach (new RecursiveIteratorIterator($rdi) as $filename => $file) {

	//if ( $counter>10 )
	//	break;

	if ( substr($filename,-3)=="/.." || substr($filename,-2)=="/." )
		continue;

	$counter++;
	print "$counter -----------------------------------------------------------------------------------\n";
	print $filename."\n";

	$pathss=preg_split('/\//',$filename);
	print_r($pathss);

	$artist=$album=$title=$track=$year="";
	$taline=$ayline=$tline=0;

	if ( strtolower(substr($filename,-4))!=".mp3" ) {
		print "ERROR: unknown file type ".substr($filename,-4)."\n";
		continue;
	} else {

		$output=array();
		$ret=0;
		$cmd="eyeD3 --to-v2.4 --no-color -G ".$pathss[3]." ".escapeshellarg($filename);
		print $cmd."\n";
		exec($cmd,$output,$ret);
		if ( $ret!=0 )
			print "ERROR: non-zero return value from cmd....\n";
		print_r($output);
		print "\n\n";

		for ( $i=0 ; $i<count($output) ; $i++ ) {
			if ( strpos($output[$i],"title")===0 )
				$taline=$i;
			if ( strpos($output[$i],"album")===0 )
				$ayline=$i;
			if ( strpos($output[$i],"track")===0 )
				$tline=$i;
		}


		if ( $taline ) {
			$artist=trim(substr($output[$taline],strpos($output[$taline],"artist:")+7));
			$title=trim(substr(trim(substr($output[$taline],0,strpos($output[$taline],"artist: "))),6));
		}

		if ( $ayline ) {
			$album=trim(substr(trim(substr($output[$ayline],0,strpos($output[$ayline],"year: "))),6));
			$year=trim(substr($output[$ayline],strpos($output[$ayline],"year: ")+5));
			if ( strlen($year)>4 || $year == "None" )
				$year="";
		}

		if ( $tline ) {
			$track="t".trim(substr($output[$tline],6,4));
			if ( strpos($track,"/") )
				$track=substr($track,0,strpos($track,"/"));
			$track=substr($track,1);
			if ( $track == "None" || $track == "g" )
				$track="";
		}

		$md5=explode("  ", exec("md5sum ".escapeshellarg($filename)));

		print "Artist = [".$artist."]\n";
		print "Album  = [".$album."]\n";
		print "Title  = [".$title."]\n";
		print "Track  = [".$track."]\n";
		print "Year   = [".$year."]\n";
		print "md5    = [".$md5[0]."]\n";
	} // end if the file is .mp3

	$q="insert into mp3s values (null,'".addslashes($filename)."',".$file->getSize().",'".$md5[0]."',";

	if ( $artist )
		$q .= "'".addslashes($artist)."',";
	else
		$q .= "null,";

	if ( $album )
		$q .= "'".addslashes($album)."',";
	else
		$q .= "null,";

	if ( $title )
		$q .= "'".addslashes($title)."',";
	else
		$q .= "null,";

	if ( $track )
		$q .= "'".$track."',";
	else
		$q .= "null,";

	if ( $year )
		$q .= "'".$year."')";
	else
		$q .= "null)";


	print $q."\n";
	mysqli_query($link,$q);

	$err=mysqli_error($link);
	if ( $err )
    	print "MYSQL ERROR: ". $err . "\n";
} // end foreach file in $PATH
?>
