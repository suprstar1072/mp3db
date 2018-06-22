<?
require_once('CONSTS.php');
$md5=$_GET['md5'];
if ( !$md5 ) {
  include 'header.php';
  print "Error1 - file not found.....";
  include 'footer.php';
} else {
  $mp3=select("select * from mp3s where f_md5='$md5' limit 1");
  if ( count($mp3)==1 ) {
    $m=$mp3[0];
    $file=$m['f_path'];
    header("Content-type: audio/mpeg");
    header("Content-length: " . filesize($file));
    header("Cache-Control: no-cache");
    header("Content-Transfer-Encoding: binary"); 
    readfile($file);
  } else {
    include 'header.php';
    print "Error2 - file not found.....";
    include 'footer.php';
  }
}
?>
