<?
require_once 'CONSTS.php';

$md5=$_GET['md5'];
if ( !$md5 ) {
	include 'header.php';
	print "Error1 - file not found.....";
	include 'footer.php';
} else {
	$mp3=select("select * from mp3s where f_md5='$md5' limit 1");
	//print_r($mp3);
	if ( count($mp3)==1 ) {
		//include 'header.php';
		$m=$mp3[0];
		//print $m."\n";
		$file=$m['f_path'];
		//print "filesize=".filesize($file)."\n";
		//print "<TITLE>".$m['f_path']."</TITLE>";
		header("Content-type: audio/mpeg");
		header("Content-length: " . filesize($file));
		header("Cache-Control: no-cache");
		header("Content-Transfer-Encoding: binary");
		readfile($file);
		//include 'footer.php';
	} else {
		include 'header.php';
		print "Error2 - file not found.....";
		include 'footer.php';
	}
}
?>
