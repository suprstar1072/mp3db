<?
require_once 'CONSTS.php';
include 'header.php';

$rpt=$_GET['rpt'];
$sortby=$_GET['sortby'];

if ( !$rpt ) {
  print "<A HREF='reports.php?rpt=md5dups'>Exact md5/filesize match duplicates</A><BR>";
  print "<A HREF='reports.php?rpt=atdups'>Artist / title match duplicates</A><BR>";

} else if ( $rpt=="md5dups" ) {
  print "<H3>Duplicate files (exact md5/filesize matches)</H3>";


  $q=	"SELECT mp3s.* FROM mp3s INNER JOIN ( ".
	"SELECT f_md5,f_size,COUNT(*) c FROM mp3s GROUP BY f_md5,f_size HAVING COUNT(*)>1) as m2 ".
	"ON mp3s.f_md5 = m2.f_md5 and mp3s.f_size = m2.f_size ";
  if ( $sortby ) {
    $q.="order by $sortby";
  }

  $res=select($q,1);
  renderTable($res,"reports.php?rpt=$rpt",$sortby);

} else if ( $rpt=="atdups" ) {
  print "<H3>Duplicate song names (artist/title match)</H3>";

  $q=	"SELECT mp3s.* FROM mp3s INNER JOIN ( ".
	"SELECT f_artist,f_title,COUNT(*) c FROM mp3s GROUP BY f_artist,f_title HAVING COUNT(*)>1) as m2 ".
	"ON mp3s.f_artist = m2.f_artist and mp3s.f_title = m2.f_title ";
  if ( $sortby ) {
    $q.="order by $sortby";
  }


  $res=select($q,1);
  renderTable($res,"reports.php?rpt=$rpt",$sortby);
} else {
  print "Report '$rpt' not implemented..<BR><BR>";
}

include 'footer.php';
?>
