<?
include 'header.php';
require_once 'CONSTS.php';

$artist=$_POST['artist'];
$album=$_POST['album'];
$title=$_POST['title'];
$track=$_POST['track'];
$year=$_POST['year'];
$playlist=$_POST['playlist'];
$playlisth=$_POST['playlisth'];
$fids=$_POST['f_id'];
$op=$_POST['submitb'];
print "op=$op <BR><BR>";


// see what mp3's were checked, make an sql snippet.  return if none checked.
$fidss="";
if ( count($fids)==0 ) {
	print "No tracks selected..<BR><BR>";
	$fidss="";
	include 'footer.php';
	return;
} else {
	$fidss="(";
	for ( $i=0 ; $i<count($fids) ; $i++ ) {
		if ( strlen($fids[$i])==32 )
			$fidss=$fidss."'".$fids[$i]."',";
		else
			$fidss=$fidss.$fids[$i].",";
	}
	$fidss=substr($fidss,0,strlen($fidss)-1).")";
	print "$fidss<BR><BR>";
}
print "$fidss<BR><BR>";


// if files are to be deleted:
if ( $op=="Delete Selected" ) {
	$res=select("select * from mp3s where f_id in $fidss",1);
	foreach ( $res as $row ) {
		print "Deleting ".$row[f_id]." - ".$row['f_path']."<BR>";
		unlink($row['f_path']);
	}
	runSQL("delete from mp3s where f_id in $fidss",1);
	// also needto delete records from playlists if the exist.


// if removing from playlist:
} else if ( $op=="Remove from Playlist" ) {
	print_r($_POST);
	$playlisth=$_POST['playlisth'];
	print "<H3>Removing tracks from playlist:</H3>";
	print "from playlist $playlisth<BR><BR>";

	print "<BR>fidss=".$fidss."<BR><BR>";
	runSQL("delete from playlists where p_md5 in $fidss and p_name='".$playlisth."'",1);


// if updating any field:
} else if ( $op=="Update Selected" ) {
	if ( $artist ) {
		print "Updating artist: $artist<BR>";
	}
	if ( $album ) {
		print "Updating album: $album<BR>";
	}
	if ( $track ) {
		print "Updating tracknum: $track<BR>";
	}
	if ( $year ) {
		print "Updating year: $year<BR>";
	}
	if ( $playlist ) {
		print "Updating playlist: $playlist<BR>";
	}


	print "<BR>For tracks:</BR>";
	$res=select("select * from mp3s where f_id in $fidss",1);

	for ( $i=0 ; $i<count($res) ; $i++ ) {
		$row=$res[$i];
		print "=======================================================================================<BR>";
		print $row['f_path']."<BR>";

		$output=array();
		$ret=0;

		// construct the eyeD3 command:
		$cmd="";
		if ( $artist ) {
			if ( !$cmd )
				$cmd="eyeD3 --no-color -a ".escapeshellarg($artist)." ";
		}
		if ( $album ) {
			if ( !$cmd )
				$cmd="eyeD3 --no-color -A ".escapeshellarg($album)." ";
			else
				$cmd .= "-A ".escapeshellarg($album)." ";
		}
		if ( $title ) {
			if ( !$cmd )
				$cmd="eyeD3 --no-color -t ".escapeshellarg($title)." ";
			else
				$cmd .= "-t ".escapeshellarg($title)." ";
		}
		if ( $track ) {
			if ( !$cmd )
				$cmd="eyeD3 --no-color -n ".escapeshellarg($track)." ";
			else
				$cmd .= "-n ".escapeshellarg($track)." ";
		}
		if ( $year ) {
			if ( !$cmd )
				$cmd="eyeD3 --no-color -Y ".escapeshellarg($year)." ";
			else
				$cmd .= "-Y ".escapeshellarg($year)." ";
		}

		// in case they checked tracks but nothing is in the update form:
		if ( $cmd ) {
			$cmd.=escapeshellarg($row['f_path']);
			print "FINAL command=".$cmd."<BR>";
			exec($cmd,$output,$ret);
			if ( $ret==0 ) {
				$md5=explode("  ", exec("md5sum ".escapeshellarg($row['f_path'])));
				$fs=filesize($row['f_path']);
				print "new md5 = [".$md5[0]."]<BR>";
				$q="update mp3s set ";
				if ( $artist )
					$q.="f_artist='".addslashes($artist)."', ";
				if ( $album )
					$q.="f_album='".addslashes($album)."', ";
				if ( $title )
					$q.="f_title='".addslashes($title)."', ";
				if ( $track )
					$q.="f_track='".addslashes($track)."', ";
				if ( $year )
					$q.="f_year='".addslashes($year)."', ";

				$q.="f_md5='".$md5[0]."', f_size=$fs where f_id=".$row['f_id'];
				print "mp3s q=".$q."<BR>";
				runSQL($q);
				$q="update playlists set p_md5='".$md5[0]."' where p_md5='".$row['f_md5']."'";
				print "playlists q=".$q."<BR>";
				runSQL($q);
			}
			print "<PRE>";
			print_r($output);
			print "Process returned $ret";
			print "</PRE>";
		}


		if ( $playlist ) {
			$q="insert into playlists values (null,'$playlist','".$row['f_md5']."','".$row['f_path']."')";
			print "<BR>q=".$q."<BR>";
			runSQL($q);
			print "Track added to playlist successfully.<BR>";
		}
		print "</TR>";
	}
	print "</TABLE>";
} // end if op==update..

include 'footer.php';
?>
