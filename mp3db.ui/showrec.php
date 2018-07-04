<?php
require_once 'CONSTS.php';
include 'header.php';

$f_id=$_GET['f_id'];

$res=select("select * from mp3s where f_id=".$f_id);
print "<PRE>";
print_r($res);
print "</PRE>";

include 'footer.php';
?>
