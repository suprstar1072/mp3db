<?php
$MYSQL_HOST="localhost";
$MYSQL_USER="mp3dbuser";
$MYSQL_PASS="70833b53e76f06c967b925f";
$MYSQL_DB="mp3db";

// these must be specified now..
//$DEPLOY_DIR="/4TB-RAID5/tmp/mp3db.pls";
//$LOCAL_DIR="/tmp";

$BASE_URL="http://www.supr-star.com/mp3db";

function renderTableHeaders($url="",$sortby="",$dir="") {
  if ( !$url ) {
    print "<TR>";
    print "<TH>FID</TH>";
    print "<TH>Album</TH>";
    print "<TH>Album</TH>";
    print "<TH>Track</TH>";
    print "<TH>Title</TH>";
    print "<TH>Bytes</TH>";
    print "<TH>Path</TH>";
    print "</TR>";
  } else {

    print "<TR>";

    // fid header
    print "<TH><A HREF='$url";
    if ( $dir )
	print "&dir=".$dir;
    print "&sortby=f_id";
    if ( $sortby=="f_id" )
	print " desc";
    print "'><B>FID</A></TH>";

    // artist header
    print "<TH><A HREF='$url";
    if ( $dir )
	print "&dir=".$dir;
    print "&sortby=f_artist";
    if ( $sortby=="f_artist" )
	print " desc";
    print "'><B>Artist</A></TH>";

    // album header
    print "<TH><A HREF='$url&";
    if ( $dir )
        print "&dir=".$dir;
    print "sortby=f_album";
    if ( $sortby=="f_album" )
	print " desc";
    print "'><B>album</A></TH>";

    // track number header
    print "<TH><A HREF='$url";
    if ( $dir )
        print "&dir=".$dir;
    print "&sortby=f_track";
    if ( $sortby=="f_track" )
	print " desc";
    print "'><B>Track</A></TH>";

    // title header
    print "<TH><input type='checkbox' id='togglecb' name='togglecb' onClick=\"toggle(this)\" /><A HREF='$url";
    print "&sortby=f_title";
    if ( $sortby=="f_title" )
	print " desc";
    print "'><B>Song Title</A></TH>";

    // size header
    print "<TH><A HREF='$url";
    if ( $dir )
        print "&dir=".$dir;
    print "&sortby=f_size";
    if ( $sortby=="f_size" )
	print " desc";
    print "'><B>Size</A></TH>";

    // path header
    print "<TH><A HREF='$url";
    if ( $dir )
        print "&dir=".$dir;
    print "&sortby=f_path";
    if ( $sortby=="f_path" )
	print " desc";
    print "'><B>Filename</A></TH>";
    print "</TR>";
  }
}

// headers in this function go with renderRow($row)
function renderTable($recs,$url="",$sortby="",$playlisth,$dir) {
  //print "renderTable($recs,$url,$sortby,$playlist)";
  print "<script>";
  print "function toggle(source) {";
  print "    checkboxes = document.getElementById('massupdate')['f_id[]'];";
  print "    for(var i in checkboxes)";
  print "        checkboxes[i].checked = source.checked;";
  print "}";
  print "</script>";


  print "<FORM ID=\"massupdate\" NAME=\"massupdate\" ACTION=\"massupdate.php\" METHOD=\"POST\" ENCTYPE=\"application/x-www-form-urlencoded\">";
  print "<INPUT type=\"hidden\" name=\"playlisth\" id=\"playlisth\" value=\"$playlisth\">";

  print "received dir=$dir<BR>";
  // form items:

  if ( strpos($url,"playlists.php")===0 )
    print " <INPUT name='submitb' TYPE=\"SUBMIT\" VALUE=\"Remove from Playlist\"><BR><BR>";
  else {
    print "<TABLE>";
    print "<TR>";
    print "<TD ALIGN=\"right\" class=\"small\">New Artist:</TD>";
    print "<TD><INPUT class=\"smallTextBox\" TYPE=\"TEXT\" NAME=\"artist\" SIZE=\"64\"></TD>";
    print "</TR>";
    print "<TR>";
    print "<TD ALIGN=\"right\" class=\"small\">New Album:</TD>";
    print "<TD><INPUT class=\"smallTextBox\" TYPE=\"TEXT\" NAME=\"album\" SIZE=\"64\"></TD>";
    print "</TR>";
    print "<TR>";
    print "<TD ALIGN=\"right\" class=\"small\">New Title:</TD>";
    print "<TD><INPUT class=\"smallTextBox\" TYPE=\"TEXT\" NAME=\"title\" SIZE=\"64\"></TD>";
    print "</TR>";
    print "<TR>";
    print "<TD ALIGN=\"right\" class=\"small\">Add to Playlist:</TD>";
    print "<TD><INPUT class=\"smallTextBox\" TYPE=\"TEXT\" NAME=\"playlist\" SIZE=\"64\"></TD>";
    print "</TR>";
    print "<TR>";
    print "<TD ALIGN=\"right\" class=\"small\">&nbsp;</TD>";
    print "<TD><INPUT class=\"smallButton\" name='submitb' TYPE=\"SUBMIT\" VALUE=\"Update Selected\">";
    print " | <INPUT name='submitb' TYPE=\"SUBMIT\" VALUE=\"Delete Selected\">";
    print "</TABLE><BR><BR>";
    //print "</FORM>";
  }

  $field="f_id";
  if ( strpos($url,"playlists.php")===0 )
    $field="f_md5";

  print "passing in dir=$dir<BR>";
  //print $url."<BR>".$field."<BR><BR>";
  print "<TABLE id='songtable'>";
  renderTableHeaders($url,$sortby,$dir);

  $counter=0;

  foreach ($recs as $row) {
    renderRow($row,$field);
    $counter++;
  }
  print "</TABLE>";
  print "</FORM>";
  print "$counter rows found.<BR><BR>";
}

// if you change the fields rendered, change the headers in renderTableHeaders()
function renderRow($row,$field) {
    print "<TR>";
    print "<TD><A HREF='showrec.php?f_id=".$row['f_id']."'>".$row['f_id']."</A></TD>";
    print "<TD><A HREF='globalsearch.php?search=".$row['f_artist']."'>".$row['f_artist']."</A></TD>";
    print "<TD>".$row['f_album']."</TD>";
    print "<TD>".$row['f_track']."</TD>";
    print "<TD>$field<input type=\"checkbox\" id=\"f_id[]\" name=\"f_id[]\" value=\"".$row[$field]."\" />".$row['f_title']."</TD>";
    print "<TD>".$row['f_size']."</TD>";
    print "<TD><A target='_new' HREF='play.php?md5=".$row['f_md5']."'>".$row['f_path']."</A></TD>";
    print "</TR>";
}


function showArtist($artist,$sortby="",$dir="") {
  print "showArtist '$artist'<BR>";
  $q="select * from mp3s ";
  $q.="where f_artist = '".addslashes($artist)."' ";
  if ( $dir )
    $q.="and f_path like '".$dir."%' ";
  if ( $sortby )
    $q.="order by $sortby";
  else
    $q.="order by f_album,f_track,f_title";

  print "$q <BR><BR>";

  $res=select($q,array());
  print "showArtist passing dir=$dir to renderTable<BR>";
  renderTable($res,"showartist.php?artist=$artist",$sortby,"",$dir);

}


function runSQL($q,$debug=0) {
  $sdt=getCurrentServerTime(1);
  if ($debug) print "entering insertSQL()<BR>$q<BR>";
  global $MYSQL_HOST;
  global $MYSQL_USER;
  global $MYSQL_PASS;
  global $MYSQL_DB;

  $link = mysqli_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASS,$MYSQL_DB);
  //mysql_select_db($MYSQL_DB, $link);
  if ($debug) print "$q<BR>";
  mysqli_query($link,$q);
  $err=mysqli_error($link);
  if ($err) {
    print $err;
  }
  return $err;
}



function select($q,$debug=0) {
  $sdt=getCurrentServerTime(1);
  //print "entering select()<BR>";
  global $MYSQL_HOST;
  global $MYSQL_USER;
  global $MYSQL_PASS;
  global $MYSQL_DB;

  //print "linking:<BR>";
  $link = mysqli_connect($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASS,$MYSQL_DB);
  //print "selecting db:<BR>";
  //mysql_select_db($MYSQL_DB, $link);
  //print "querying:";
  if ($debug) print "$q<BR><BR>";
  $result=mysqli_query($link,$q);
  //print(mysql_error());

  $err=mysqli_error($link);
  if ($err) {
    print "Error running<BR>$q<BR>";
    print($err."<BR>");
  }

  $ret=array();
  //print "building return array:<BR>";
  //print "found ".mysql_num_rows($result)." rows.<BR>";
  while ( $row=mysqli_fetch_assoc($result) ) {
    //if ($debug) print "iter....<BR>";
    $keys=array_keys($row);
    for ($i=0 ; $i<count($keys) ; $i++ ) {
        $row[$keys[$i]]=urldecode($row[$keys[$i]]);
    }
    $ret[]=$row;
  }
  //print "returning ".count($ret)." rows";
  sqllog($sdt,$q);
  //print_r($ret);
  return $ret;
}

function sqllog($sdt, $q) {
  global $SQL_LOG;
  $edt=getCurrentServerTime(1);
  $fh = fopen($SQL_LOG, 'a');
  fwrite($fh,"$sdt\t$edt\t$q\n");
  fclose($fh);
}

function getCurrentServerTime($micros=1) {
  if ( $micros ) {
    list($usec, $sec) = explode(' ', microtime());
    return date('Y-m-d H:i:s', $sec) . "." . substr($usec,2,6);
  }
  return date("Y-m-d H:i:s");
}

?>
