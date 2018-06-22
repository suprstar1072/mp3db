<?
require_once 'CONSTS.php';
include 'header.php';
print "<A HREF='index.php'>A-List</A><BR>";
print "<BR>";


if ( isset($_GET['artist']) ) {
  $artist=$_GET['artist'];
  showArtist($artist);
  print "<BR><BR>New Artist Name:<BR>";
  include "changeartistform.php";
} else if ( isset($_POST['oldartist']) && isset($_POST['newartist']) ) {
  $olda=$_POST['oldartist'];
  $newa=$_POST['newartist'];
  print "Changing '$olda' to '$newa'<BR>";

  $res=select("select * from mp3s where f_artist='".addslashes($olda)."'");
  print "Changing ".count($res)." records:<BR>";

  for ( $i=0 ; $i<count($res) ; $i++ ) {
    print "-------------------------------------------------------------------<BR>";
    $rec=$res[$i];
    print $rec['f_path']."<BR>";
    $output=array();
    $ret=0;
    $cmd="eyeD3 --no-color -a \"".addslashes($newa)."\" \"".$rec['f_path']."\"";
    print $cmd."<BR><BR>";
    exec($cmd,$output,$ret);
    print "<PRE>";
    print_r($output);
    print "</PRE>";
    if ( $ret!=0 ) {
	print "ERROR: non-zero return value....<BR>";
    } else {
	runSQL("update mp3s set  f_artist='".addslashes($newa)."' where f_id=".$rec['f_id']);
    }
  }
  print "Updates complete.<BR><BR>";

  showArtist($newa);

} else {
  print "Invalid call:<BR><BR>";
  print_r($_POST);
  print "<BR><BR>";
  print_r($_GET);
}

include 'footer.php';
?>
