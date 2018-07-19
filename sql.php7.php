<?php

require 'CONSTS.php';


function runSQL($q,$debug=0) {
	$sdt=getCurrentServerTime(1);
	if ($debug) print "entering insertSQL()<BR>$q<BR>";

	$link = mysqli_connect($GLOBALS['dbhost'],$GLOBALS['dbuser'],$GLOBALS['dbpass'],$GLOBALS['dbdb']);
	if ($debug) print "$q<BR>";
	mysqli_query($link,$q);

	$err=mysqli_error($link);
	if ($err) {
		print $err;
		print "\n";
	}
	return $err;
}



function select($q,$debug=0) {

	$sdt=getCurrentServerTime(1);
	$link = mysqli_connect($GLOBALS['dbhost'],$GLOBALS['dbuser'],$GLOBALS['dbpass'],$GLOBALS['dbdb']);
	if ($debug) print "$q<BR><BR>";
	$result=mysqli_query($link,$q);

	$err=mysqli_error($link);
	if ($err) {
	    print "Error running<BR>$q<BR>";
	    print($err."<BR>");
	}

	$ret=array();
	while ( $row=mysqli_fetch_assoc($result) ) {
		$keys=array_keys($row);
		for ($i=0 ; $i<count($keys) ; $i++ ) {
			$row[$keys[$i]]=$row[$keys[$i]];
		}
		$ret[]=$row;
	}
	sqllog($sdt,$q);


	if ( $debug )
		print_r($ret);

	return $ret;
}


function sqllog($sdt, $q) {
	$edt=getCurrentServerTime(1);
	if ( isset($GLOBALS['sqllog']) ) {
		$fh = fopen($GLOBALS['sqllog'], 'a');
		fwrite($fh,"$sdt\t$edt\t$q\n");
		fclose($fh);
	}
}


function getCurrentServerTime($micros=1) {
	if ( $micros ) {
		list($usec, $sec) = explode(' ', microtime());
		return date('Y-m-d H:i:s', $sec) . "." . substr($usec,2,6);
	}
	return date("Y-m-d H:i:s");
}

?>
