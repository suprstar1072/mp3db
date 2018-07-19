<?
session_start();
require_once 'CONSTS.php';
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">
<HTML>
<HEAD>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=iso-8859-1">
        <META NAME="GENERATOR" Content="Emacs">
        <link rel="SHORTCUT ICON" href="favicon.ico" />
        <link rel='stylesheet' href='sa.css'>
</HEAD>
<BODY>


<?
if ( isset($_GET['dir']) )
	$dir=$_SESSION['dir']=$_GET['dir'];

print "<TABLE><TR>";
print "<TD VALIGN='top'><A HREF='index.php'>ALL</A></TD>";
print "<TD VALIGN='top'><A HREF='index.php?dir=/DWH/MP3.car32/'>Car32</A></TD>";
print "<TD VALIGN='top'><A HREF='index.php?dir=/DWH/MP3/'>MP3</A></TD>";
print "<TD VALIGN='top'><A HREF='index.php?dir=/DWH/MP3.dlist/'>DList</A></TD>";
print "<TD VALIGN='top'><A HREF='playlists.php'>Playlists</A></TD>";
print "<TD VALIGN='top'><A HREF='reports.php'>Tools</A></TD>";
print "<TD VALIGN='middle'>";
include 'globalsearchform.php';
print "</TD>";

if ( isset($dir) )
	print "<TD VALIGN='top'> Current dir=$dir</TD>";

print "</TR></TABLE>";
print "<BR><BR>";

?>
