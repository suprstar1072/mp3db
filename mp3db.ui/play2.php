<?php
require 'CONSTS.php';

if ( !isset($_GET['playlist']) ) {
	include 'header.php';
	print "Playlist not found in URL..<BR><BR>";
	include 'footer.php';
} else {
	$res=select("select * from mp3s where f_md5 in (select p_md5 from playlists where p_name='".$_GET['playlist']."')");
	if ( count($res)==0 ) {
		include 'header.php';
		print "Playlist not found in mp3db..<BR><BR>";
		include 'footer.php';
	} else {

		include 'header.php';

		print "<PRE>";
		print_r($res);
		shuffle($res);
		print_r($res);
		print "</PRE>";

		$bytes=0;
		foreach ( $res as $mp3 )
			$bytes+=$mp3['f_size'];

		//print "bytes=".$bytes."<BR>";

		//header("Content-type: audio/mpeg");
		//header("Content-length: " . $bytes);
		//header("Cache-Control: no-cache");
		//header("Content-Transfer-Encoding: binary");
		//ob_clean();
		//flush();
		//foreach ( $res as $mp3 )
		//	echo readfile($mp3['f_path']);

		include 'footer.php';

	}
}

// bytes=2276966 2276966
?>
