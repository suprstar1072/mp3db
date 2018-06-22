<?php

// db parameters required:
$DB_USER='root';
$DB_PASS='T@5tytr3@t5';
$DB_HOST='localhost';
$DB_DB='mp3db';

// path to your music library:
$PATH='/DWH/MP3.car32';

// optional.  if you comment this out, no log will be kept.
$SQL_LOG='/var/log/sql.log';




////////////////////////////////////
//                                //
//   Don't edit functions below   //
//                                //
////////////////////////////////////




// runs any select statement, returns results in a 2 dimensional array.
function select($q,$debug=0) {
	//$debug=1;
	if ( $debug ) print "entering select()<BR>";

	$sdt=getCurrentServerTime(1);

	global $DB_HOST;
	global $DB_USER;
	global $DB_PASS;
	global $DB_DB;
	global $SQL_LOG;

	if ( $debug ) print "linking:<BR>";
	$link = mysqli_connect($DB_HOST,$DB_USER,$DB_PASS,$DB_DB);

	if ( $debug ) print "querying:";

	if ($debug) print "$q<BR><BR>";
	$result=mysqli_query($link,$q);

	$err=mysqli_error($link);
	if ($err) {
		print "Error running<BR>$q<BR>";
		print($err."<BR>");
	}

	$ret=array();
	if ( $debug ) {
    	print "building return array:<BR>";
		print "found ".mysqli_num_rows($result)." rows.<BR>";
	}

	while ($row=mysqli_fetch_assoc($result)) {
		if ($debug) print "iter....<BR>";
		$keys=array_keys($row);
		for ($i=0 ; $i<count($keys) ; $i++ ) {
			$row[$keys[$i]]=urldecode($row[$keys[$i]]);
		}
		$ret[]=$row;
	}

	if ( $debug ) print "returning ".count($ret)." rows\n";

	if ( $SQL_LOG )
		sqllog($sdt,$q);

	if ( $debug ) print_r($ret);

	return $ret;
}

// returns server time to the microsecond
function getCurrentServerTime($micros=1) {
  if ( $micros ) {
    list($usec, $sec) = explode(' ', microtime());
    return date('Y-m-d H:i:s', $sec) . "." . substr($usec,2,6);
  }
  return date("Y-m-d H:i:s");
}

// write start time, end time, and query
function sqllog($sdt, $q) {
  global $SQL_LOG;
  $edt=getCurrentServerTime(1);
  $fh = fopen($SQL_LOG, 'a');
  fwrite($fh,"$sdt\t$edt\t$q\n");
  fclose($fh);
}

?>
