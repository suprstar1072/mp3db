<?php
require_once 'mp3.class.php';
require_once 'sql.php7.php';
require_once 'functions.php';

///////// REQUIRED VARIABLES: //////////

// db parameters required:
$GLOBALS['dbuser']='mp3dbuser';
$GLOBALS['dbpass']='70833b53e76f06c967b925f';
$GLOBALS['dbhost']='localhost';
$GLOBALS['dbdb']='mp3db';

// base url to your mp3db site
$GLOBALS['baseurl']='http://www.supr-star.com/mp3db';

// path to your music library:
$GLOBALS['basedir']='/DWH/CODE/mp3db';

// path to your music library:
$GLOBALS['mp3path']='/DWH/MP3.car32';


// page limit for reports and searches
$GLOBALS['pagelimit']=1000;


///////// OPTIONAL VARIABLES: //////////

// if you comment this out, no log will be kept.
//$GLOBALS['sqllog']='/var/log/sql.log';


?>
