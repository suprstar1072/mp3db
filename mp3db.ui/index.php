<?php
require_once 'CONSTS.php';
include 'header.php';

print "<A HREF='index.php'>ALL</A> | ";;
print "<A HREF='index.php?dir=/DWH/MP3.car32'>car32</A><BR>";
print "<BR>";

$q="select f_artist,count(*) c from mp3s ";
if ( $dir )
	$q.="where f_path like '".$dir."%' ";
$q .= "group by f_artist order by f_artist";

print $q."\n\n";
$res=select($q,array());

$tracks=0;

print "<TABLE id=\"artisttable\"><TR><TH>Artist</TH><TH>#Tracks</TH></TR>";
foreach ($res as $row) {
	$tracks+=$row['c'];
	print "<TR>";
	print "<TD>".$row['f_artist']."</TD>";
	print "<TD><A HREF='showartist.php?";
	if ( $dir )
		print "dir=$dir&";

	print "artist=". urlencode($row['f_artist']);

	print "'>".$row['c']."</A></TD>";
	print "</TR>";
}
print "</TABLE>";

print "<BR>".$tracks." tracks found.<BR>";

include 'footer.php';
?>
