<?php
require_once 'CONSTS.php';

$playlist=$_GET['playlist'];
if ( !$playlist )
  $playlist="eminem";


$file="/tmp/".$playlist.".web.m3u";

$res=select("select * from playlists,mp3s where p_name='$playlist' and p_md5=f_md5",array());
$fh=fopen($file,"w");
fwrite($fh,"#EXTM3U ".$pl."\n");
foreach ($res as $song) {
	//print " - ".$song['f_path']." to $DEPLOY_DIR<BR>";
	//if ( file_exists($DEPLOY_DIR."/".substr($song['f_path'],strrpos($song['f_path'],"/")+1)) )
	//    print " skipping existing song....<BR>";
	//else
	//    copy($song['f_path'],$DEPLOY_DIR."/".substr($song['f_path'],strrpos($song['f_path'],"/")+1));
	fwrite($fh,"#EXTINF:-1,".$song['f_artist']." - ".$song['f_title']."\n");
	fwrite($fh,$BASE_URL."/play.php?md5=".$song['f_md5']."\n");
}

fclose($fh);

header("Content-Disposition: attachment; filename=\"".basename($file)."\"");

readfile($file);
