<?
require_once 'CONSTS.php';
include 'header.php';

$perpage=2000;

$rpt=$_GET['rpt'];
$sortby=$_GET['sortby'];
$pg=$_GET['pg'];
if (!$pg)
	$pg=1;


if ( !$rpt ) {
	print "<H5>Match Reports</H5>";
	print "<A HREF='reports.php?rpt=md5dups'>List Exact md5 match duplicates</A><BR>";
	print "<A HREF='reports.php?rpt=atsdups'>DELETE exact md5 match duplicates</A><BR>";
	print "<BR>";
	print "<A HREF='reports.php?rpt=atdups'>Artist / title match duplicates</A><BR>";
	print "<A HREF='reports.php?rpt=atscount'>Artist / title / filesize match duplicates</A><BR>";
	print "<BR>";
	print "<A HREF='reports.php?rpt=noalbum'>Mp3s with no album</A><BR>";
	print "<A HREF='reports.php?rpt=notitle'>Mp3s with no title</A><BR>";
	print "<A HREF='reports.php?rpt=notrack'>Mp3s with no track</A><BR>";
	print "<BR>";
	print "<A HREF='reports.php?rpt=orphpl'>Orphaned Playlist records (no mp3s rec)</A><BR>";
	print "<A HREF='reports.php?rpt=orphmp3'>Orphaned MP3 records (no file)</A><BR>";


} else if ( $rpt=="orphmp3" ) {
	print "<H3>MP3 records with no file</H3>";
	$res=select("select * from mp3s");
	$good=0;
	$errors=0;
	foreach ($res as $row) {
		if ( !file_exists($row['f_path']) ) {
			$errors++;
			print "ERROR: file not found: ".$row['f_path']."<BR>";
		} else {
			$good++;
		}
	}
	print "<BR>".$errors." Errors found, $good files successfully found.<BR>";





} else if ( $rpt=="orphpl" ) {
	print "<H3>Playlist records with no MP3</H3>";
	$res=select("select * from playlists where p_md5 in (select p_md5 from playlists where p_md5 not in (select f_md5 from mp3s));");
	renderGenericTable($res);

} else if ( $rpt=="noalbum" ) {
	print "<H3>Mp3s with no album</H3>";
	//$res=select("select * from mp3s where f_album is null");
	$res=select("select * from mp3s where f_album is null and f_path not like '%/Unknown%Album/%'");
	renderTable($res,"reports.php?rpt=$rpt",$sortby);

} else if ( $rpt=="notitle" ) {
	print "<H3>Mp3s with no title</H3>";
	$q="select * from mp3s where f_title is null";
	if ( $sortby )
		$q.=" order by ".$sortby;
	$res=select($q);
	renderTable($res,"reports.php?rpt=$rpt",$sortby);

} else if ( $rpt=="notrack" ) {
	print "<H3>Mp3s with no track</H3>";
	if ($pg>1) {
		print "<A HREF='reports.php?rpt=notrack&pg=".($pg-1)."'>Previous</A> | ";
	}
	print "<A HREF='reports.php?rpt=notrack&pg=".($pg+1)."'>Next</A> | ";

	$start=($pg-1)*$GLOBALS['pagelimit']=1000;
	$q="select * from mp3s where f_track is null limit ".$start.",".$GLOBALS['pagelimit']=1000;
	print "q=".$q."<BR><BR>";
	$res=select($q);

	renderTable($res,"reports.php?rpt=$rpt",$sortby);

} else if ( $rpt=="md5dups" ) {
	print "<H3>Duplicate files (exact md5/filesize matches)</H3>";


	$q=	"select * from mp3s where f_md5 in ( ".
		"    select m ".
		"    from ( ".
        " 		select f_size s,f_md5 m,count(*) c from mp3s".
	$q.="        where f_path like '/DWH/MP3/%' ";
	$q.="		 group by f_size,f_md5 ".
		"    ) xx ".
		"    where c>1 ".
		") ";
	if ( $sortby ) {
    	$q.="order by $sortby";
	}
	print $q."<BR><BR>";
	$res=select($q);
	renderTable($res,"reports.php?rpt=$rpt",$sortby);


} else if ( $rpt=="atdups" ) {
	print "<H3>Duplicate song names (artist/title match)</H3>";

	$q=	"SELECT mp3s.* FROM mp3s INNER JOIN ( ".
		"SELECT f_artist,f_title,COUNT(*) c FROM mp3s GROUP BY f_artist,f_title HAVING COUNT(*)>1) as m2 ".
		"ON mp3s.f_artist = m2.f_artist and mp3s.f_title = m2.f_title ";
	if ( $sortby ) {
		$q.="order by $sortby";
	}

	$res=select($q);
	renderTable($res,"reports.php?rpt=$rpt",$sortby);


} else if ( $rpt=="atscount" ) {
	print "<H3>Duplicate song names (artist/title/filesize match)</H3>";

	$q=	"SELECT mp3s.* FROM mp3s ";
	$q.="    INNER JOIN ( ".
		"    SELECT f_artist,f_title,f_size,COUNT(*) c FROM mp3s where f_path like '/DWH/MP3/%' GROUP BY f_artist,f_title,f_size HAVING COUNT(*)>1) as m2 ".
		"ON mp3s.f_artist = m2.f_artist and mp3s.f_title = m2.f_title ";
//  $q.=	"where f_path like '/DWH/MP3/%' ";

	if ( $sortby ) {
		$q.="order by $sortby";
	} else {
		$q.="order by f_size,f_path";
	}

	$res=select($q);
	renderTable($res,"reports.php?rpt=$rpt",$sortby);






// this function will delete dup's so use with caution....

} else if ( $rpt=="atsdups" ) {
	print "<H3>Duplicate song names (artist/title/filesize match)</H3>";

	$q=	"SELECT f_artist,f_title,f_size,COUNT(*) c FROM mp3s GROUP BY f_artist,f_title,f_size HAVING COUNT(*)=2 ";
	$res=select($q);

	foreach ($res as $row) {
//		print "======================================================================<BR><BR>";
//		print $row['f_artist']." / ".$row['f_title']." / ".$row['c']."<BR>";

		$dup=select("select * from mp3s where f_artist='".addslashes($row['f_artist']).
					"' and f_title='".addslashes($row['f_title'])."' and f_size=".$row['f_size']);
		//renderTable($dup,"reports.php?rpt=$rpt",$sortby);

//		print "<PRE>";
//		print_r($dup);
//		print "</PRE>";

		//print "row[c]=".$dup['c']."<BR>";
		if ( $row['c']=="2" ) {
			//print "HERE!<BR>";
			//print_r($dup[0]);
			//print "HERE2!<BR>";
			$f1=strpos($dup[0]['f_path'],'/DWH/MP3.car32');
			$f2=strpos($dup[1]['f_path'],'/DWH/MP3.car32');
			$f3=strpos($dup[0]['f_path'],'/DWH/MP3/');
			$f4=strpos($dup[1]['f_path'],'/DWH/MP3/');

			//print "f1 - ".$f1."<BR>";
			//print "f2 - ".$f2."<BR>";
			//print "f3 - ".$f3."<BR>";
			//print "f4 - ".$f4."<BR>";

//			print $dup[0]['f_md5']." - ".$dup[0]['f_path']."<BR>";
//			print $dup[1]['f_md5']." - ".$dup[1]['f_path']."<BR>";

			$delfid=$dup[0]['f_id'];
			$delrec=0;
			if ( $f4==0 ) {
				$delrec=1;
				$delfid=$dup[1]['f_id'];
			}


			if ( ($f1==0||$f2==0) && ($f3==0||$f4==0) ) {
//				print "Deleting dup:<BR><BR>";


				$q="delete from mp3s where f_id=".$delfid;

				print "NOT DELETING records/files!\n";

//				print $q."<BR>";
//				print "unlink(".addslashes($dup[$delrec]['f_path']).")<BR><BR>";

//				if ( unlink($dup[$delrec]['f_path']) ) {
//					runSQL($q,1);
//					print "SQL ran.<BR><BR>";
//				} else {
//					print "unlink returned false:<BR>";
//					print $dup[$delrec]['f_path']."<BR>";
//				}

			}
		}
	}


} else {
		print "Report $rpt not implemented..<BR><BR>";
}

include 'footer.php';
?>
