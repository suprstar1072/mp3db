<?
require_once 'CONSTS.php';
include 'header.php';

$artist=$_GET['artist'];
$sortby=$_GET['sortby'];

showArtist($artist,$sortby,$dir);

include 'footer.php';
?>
