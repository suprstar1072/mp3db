<?
require_once 'CONSTS.php';
include 'header.php';

if ( isset($_POST['search']) || isset($_GET['search']) ) {

  $s=$_POST['search'];
  if ( !$s )
    $s=$_GET['search'];
  $sortby=$_GET['sortby'];

  print "Searching ";
  if ( $dir )
    print "$dir ";
  print "for <B>$s</B>: ($sortby)<BR><BR>";

  $q="select * from mp3s where f_artist like '%$s%' or f_album like '%$s%' or f_title like '%$s%' ".
                " or f_path like '%$s%' order by ";
  if ( $sortby )
    $q .= $sortby;
  else
    $q .= "f_artist,f_album,f_track,f_title";

  $res=select($q);
  renderTable($res,"globalsearch.php?search=$s",$sortby,$dir);



} else {
  print "No search parameters found..";
}

include 'footer.php';
?>
