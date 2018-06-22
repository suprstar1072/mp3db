<?
include 'header.php';

  $artist=$_POST['artist'];
  $album=$_POST['album'];
  $title=$_POST['title'];
  $playlist=$_POST['playlist'];
  $playlisth=$_POST['playlisth'];
  $fids=$_POST['f_id'];
  $op=$_POST['submitb'];
  print "op=$op <BR><BR>";

  if ( $op=="Delete Selected" ) {
    $fidss="(";
    for ( $i=0 ; $i<count($fids) ; $i++ ) {
  	$fidss=$fidss.$fids[$i].",";
    }
    $fidss=substr($fidss,0,strlen($fidss)-1).")";
    print "$fidss<BR><BR>";
    $res=select("select * from mp3s where f_id in $fidss",1);
    foreach ( $res as $row ) {
	print "Deleting ".$row[f_id]." - ".$row['f_path']."<BR>";
	unlink($row['f_path']);
    }
    runSQL("delete from mp3s where f_id in $fidss",1);
  } else if ( $op=="Remove from Playlist" ) {
    print_r($_POST);
    $playlisth=$_POST['playlisth'];
    print "<H3>Removing tracks from playlist:</H3>";
    $fidss="(";
    for ( $i=0 ; $i<count($fids) ; $i++ ) {
        $fidss=$fidss."'".$fids[$i]."',";
    }
    $fidss=substr($fidss,0,strlen($fidss)-1).")";
    print $fidss."<BR><BR>";
    print "from playlist $playlisth<BR><BR>";

    runSQL("delete from playlists where p_md5 in $fidss and p_name='".$playlisth."'");

  } else if ( $op=="Update Selected" ) {
    if ( $artist ) {
      print "Updating artist: $artist<BR>";
    }
    if ( $album ) {
      print "Updating album: $album<BR>";
    }
    if ( $playlist ) {
      print "Updating playlist: $playlist<BR>";
    }

    print "<BR>For tracks:</BR>";
    if ( count($fids)==0 ) {
      print "No tracks selected..<BR><BR>";
      $fidss="";
    } else {
      $fidss="(";
      for ( $i=0 ; $i<count($fids) ; $i++ ) {
  	$fidss=$fidss.$fids[$i].",";
      }
      $fidss=substr($fidss,0,strlen($fidss)-1).")";
      print "$fidss<BR><BR>";
    }

    $res=select("select * from mp3s where f_id in $fidss",1);

    for ( $i=0 ; $i<count($res) ; $i++ ) {
      $row=$res[$i];
      print "=======================================================================================<BR>";
      print $row['f_path']."<BR>";

      $output=array();
      $ret=0;
      if ( $artist ) {
  	$cmd="eyeD3 --no-color -a ".escapeshellarg($artist)." ".escapeshellarg($row['f_path']);
  	print $cmd."<BR>";
	exec($cmd,$output,$ret);
	if ( $ret==0 ) {
          $md5=explode("  ", exec("md5sum ".escapeshellarg($row['f_path'])));
	  $fs=filesize($row['f_path']);
          print "new md5 = [".$md5[0]."]<BR>";
	  runSQL("update mp3s set f_artist='".addslashes($artist)."', f_md5='".$md5[0]."', f_size=$fs where f_id=".$row['f_id']);
	}
	print "<PRE>";
	print_r($output);
	print "Process returned $ret";
	print "</PRE>";
      }
      if ( $album ) {
	$cmd="eyeD3 --no-color -A ".escapeshellarg($album)." ".escapeshellarg($row['f_path']);
	print $cmd."<BR>";
	exec($cmd,$output,$ret);
	if ( $ret==0 ) {
          $md5=explode("  ", exec("md5sum ".escapeshellarg($row['f_path'])));
	  $fs=filesize($row['f_path']);
          print "new md5 = [".$md5[0]."]<BR>";
	  runSQL("update mp3s set f_album='".addslashes($album)."',f_md5='".$md5[0]."', f_size=$fs where f_id=".$row['f_id']);
	}
	print "<PRE>";
	print_r($output);
	print "Process returned $ret";
	print "</PRE>";
      }
      if ( $title ) {
	$cmd="eyeD3 --no-color -t ".escapeshellarg($title)." ".escapeshellarg($row['f_path']);
	print $cmd."<BR>";
	exec($cmd,$output,$ret);
	if ( $ret==0 ) {
          $md5=explode("  ", exec("md5sum ".escapeshellarg($row['f_path'])));
	  $fs=filesize($row['f_path']);
          print "new md5 = [".$md5[0]."]<BR>";
	  runSQL("update mp3s set f_title='".addslashes($title)."',f_md5='".$md5[0]."', f_size=$fs where f_id=".$row['f_id']);
	}
	print "<PRE>";
	print_r($output);
	print "Process returned $ret";
	print "</PRE>";
      }
      if ( $playlist ) {
	runSQL("insert into playlists values (null,'$playlist','".$row['f_md5']."')");
	//$err=mysql_error();
	//if ( $err ) {
	  //if ( substr($err,0,9)=="Duplicate" )
	    //print "<BR>Track already exists on the playlist.<BR>";
	  //else
	    //print ".".substr($err,0,9).".<BR>";
	//}
	//else
	print "Track added to playlist successfully.<BR>";
      }

      print "</TR>";
    }
    print "</TABLE>";
  } // end if op==update..

include 'footer.php';
?>
