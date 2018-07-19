<?
require_once 'CONSTS.php';
include 'header.php';

print "<H3>Auto-generating playlists for $dir</H3>";

$dirs=scandir($dir);

foreach ( $dirs as $d ) {
	if ( strpos($d,".")===0 )
		continue;

	print "Processing $d:<BR>";

	$songs=scandir($dir.$d);
	foreach ( $songs as $s ) {
		if ( strpos($s,".")===0 )
			continue;
		print " - adding $s<BR>";
	}
	print "<BR>";
}

include 'footer.php';
?>
