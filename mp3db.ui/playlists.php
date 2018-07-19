<?php
include 'header.php';

$op=$_POST['op'];
$playlist=$_GET['playlist'];


if ( $op ) {
	print "op=$op<BR><BR>";
	$local=$_POST['local'];
	print "local=$local<BR><BR>";

	if ( $op=="deploy" ) {
		$plids=$_POST['pl_id'];
		$DEPLOY_DIR=$_POST['deploydir'];
		if ( !$DEPLOY_DIR ) {
			print "No directory specified..";
		} else {
			foreach ($plids as $pl) {
				$q="select * from playlists,mp3s where p_name='$pl' and p_md5=f_md5";
				if ( $dir )
					$q .= " and f_path like '".$dir."%'";
				$res=select($q,array());
				if ( !$local ) { // deploying standalone playlist
					print "deploying playlist '".$pl."'<BR>";
					$fh=fopen($DEPLOY_DIR."/".$pl.".m3u","w");
					fwrite($fh,"#EXTM3U ".$pl."\n");
					print "- found ".count($res)." songs:<BR>";
					foreach ($res as $song) {
						print " - ".$song['f_path']." to $DEPLOY_DIR<BR>";
						if ( file_exists($DEPLOY_DIR."/".substr($song['f_path'],strrpos($song['f_path'],"/")+1)) )
							print " skipping existing song....<BR>";
						else
							copy($song['f_path'],$DEPLOY_DIR."/".substr($song['f_path'],strrpos($song['f_path'],"/")+1));
						fwrite($fh,substr($song['f_path'],strrpos($song['f_path'],"/")+1)."\n");
					}
					fclose($fh);
				} else { // making local playlist referencing /4TB-RAID5/MP3
					print "creating local playlist '".$pl."'<BR>";
					$fh=fopen($DEPLOY_DIR."/".$pl.".m3u","w");
					fwrite($fh,"#EXTM3U ".$pl."\n");
					foreach ($res as $song) {
						print " - ".$song['f_path']." to $DEPLOY_DIR<BR>";
						//copy($song['f_path'],$DEPLOY_DIR."/".substr($song['f_path'],strrpos($song['f_path'],"/")+1));
						fwrite($fh,$song['f_path']."\n");
					}
					fclose($fh);
				} // end if is local
			} // end foreacl plid
		} // end if deploy dir was in POST
	} // end if op==deploy
} // end if op
else if ( isset($playlist) ) {
	$sortby=$_GET['sortby'];
	print "<A HREF='play2.php?playlist=".$playlist."'><H3>$playlist</H3></A>";
	print "<A HREF='weblist.php?playlist=$playlist'>Get a web playlist</A><BR><BR>";

	$q="select * from playlists,mp3s where p_name='$playlist' and p_md5=f_md5";
	if ( $dir )
		$q .=  " and f_path like '".$dir."%'";
	if ( $sortby )
		$q .=  " order by $sortby";
	$res=select($q,array());

	//print "renderTable($res,playlists.php?playlist=$playlist,$sortby,$playlist)";
	renderTable($res,"playlists.php?playlist=$playlist",$sortby,$playlist);

	//print "<TABLE id='songtable'>";
	//renderTableHeaders("playlists.php?playlist=$playlist",$sortby);
	//foreach ($res as $pl) {
	//  renderRow($pl);
	//}
	//print "</TABLE>";

// end if displaying a playlist
} else {
	print "<H3>All Playlists:</H3>";

	print "<A HREF='autoplaylist.php'>AutoGenerate playlists for $dir</A><BR><BR>";

	print "<FORM NAME=\"playlist\" ACTION=\"playlists.php\" METHOD=\"POST\" ENCTYPE=\"application/x-www-form-urlencoded\">";
	print "<INPUT type=\"hidden\" name=\"op\" id=\"op\" VALUE=\"deploy\">";
	print "Deploy dir:<INPUT TYPE=\"TEXT\" name=\"deploydir\" id=\"deploydir\" value=\"/mnt/32g\"> ";
	print "<input type=\"checkbox\" name=\"local\" /> Local?<BR>";
	print "<INPUT class=\"smallButton\" TYPE=\"SUBMIT\" VALUE=\"Deploy\"><BR><BR>";

	$q="select p_name,count(*) c from playlists group by p_name order by p_name";
	$res=select($q,array());

	print "<TABLE>";
	foreach ($res as $pl) {
		print "<TR>";

		print "<TD><input type=\"checkbox\" name=\"pl_id[]\" value=\"".$pl['p_name']."\" />";
		print "<A HREF='playlists.php?playlist=".$pl['p_name']."'>".$pl['p_name']."</TD>";
		print "<TD>".$pl['c']."</TD>";
		print "</TR>";
	}
	print "</TABLE>";
} // end if NO get/post args passed in, (list all playlists / track counts)

include 'footer.php';
?>
