<?php

require_once 'CONSTS.php';
require_once 'sql.php7.php';

class MP3 {

	private $fid;
	private $filename;
	private $filesize;
	private $md5;
    private $artist;
    private $album;
	private $title;
	private $track;
	private $year;
	private $debug=1;

    public function __construct($filename, $debug=0) {
		if ( !file_exists($filename) ) {
			print "MP3::__construct($filename) failed - !file_exists..\n";
			return null;
		}
		$this->filename=$filename;
		$this->filesize=filesize($filename);
		$this->setEyed3Tags($filename);
		// set md5 AFTER the file gets updated.....
		$this->setMD5($filename);
		$this->setFid();
	}

	public function display() {
		print "\n";
		print "filename = ".$this->filename."\n";
		print "md5      = ".$this->md5."\n";
		print "artist   = ".$this->artist."\n";
		print "album    = ".$this->album."\n";
		print "title    = ".$this->title."\n";
		print "track    = ".$this->track."\n";
		print "year     = ".$this->year."\n";
		print "fid      = ".$this->fid."\n\n";
	}


	public function getMp3Data() {
		$ret=array();
		$ret['filename']=$this->filename;
		$ret['md5']=$this->md5;
		$ret['artist']=$this->artist;
		$ret['album']=$this->album;
		$ret['title']=$this->title;
		$ret['track']=$this->track;
		$ret['year']=$this->year;
		$ret['fid']=$this->fid;
		return $ret;
	}


	private function setEyed3Tags($filename) {
		if ( substr($this->filename,-3)=="/.." || substr($this->filename,-2)=="/." )
			return;
		if ( strtolower(substr($this->filename,-4))!=".mp3" ) {
			print "ERROR: unknown file type ".substr($this->filename,-4)."\n";
			return;
		}

        $output=array();
        $ret=0;
        $cmd="eyeD3 --to-v2.4 --no-color ".escapeshellarg($this->filename)." 2>&1";
        //print $cmd."\n";
        exec($cmd,$output,$ret);
        if ( $ret!=0 )
            print "ERROR: non-zero return value from cmd....\n";
        //print_r($output);
        //print "\n\n";

        for ( $i=0 ; $i<count($output) ; $i++ ) {
            if ( strpos($output[$i],"title")===0 )
                $taline=$i;
            if ( strpos($output[$i],"album")===0 )
                $ayline=$i;
            if ( strpos($output[$i],"track")===0 )
                $tline=$i;
        }


        if ( $taline ) {
            $artist=trim(substr($output[$taline],strpos($output[$taline],"artist:")+7));
            $title=trim(substr(trim(substr($output[$taline],0,strpos($output[$taline],"artist: "))),6));
        }

        if ( $ayline ) {
            $album=trim(substr(trim(substr($output[$ayline],0,strpos($output[$ayline],"year: "))),6));
            $year=trim(substr($output[$ayline],strpos($output[$ayline],"year: ")+5));
            if ( strlen($year)>4 || $year == "None" )
                $year="";
        }


        if ( $tline ) {
            $track="t".trim(substr($output[$tline],6,4));
            if ( strpos($track,"/") )
                $track=substr($track,0,strpos($track,"/"));
            $track=substr($track,1);
            if ( $track == "None" || $track == "g" )
                $track="";
        }

        //print "Artist = [".$artist."]\n";
        //print "Album  = [".$album."]\n";
        //print "Title  = [".$title."]\n";
        //print "Track  = [".$track."]\n";
        //print "Year   = [".$year."]\n";

		$this->artist=$artist;
		$this->album=$album;
		$this->title=$title;
		$this->track=$track;
		$this->year=$year;
	}



	private function setMD5() {
        $md5=explode("  ", exec("md5sum ".escapeshellarg($this->filename)));
        //print "md5    = [".$md5[0]."]\n";
		$this->md5=$md5[0];
	}


	public function updateFid($fid) {
		$this->fid=$fid;
	}


	private function setFid() {
		$q="select f_id from mp3s where f_md5='".$this->md5."'";

		$res=select($q);
		if ( count($res) )
			$this->fid=$res[0]['f_id'];
		else
			$this->fid="";

		return $this->fid;
	}


	public function save() {
		if ( $this->setFid() )
			$this->update();
		else
			$this->create();
	}

	private function update() {
		print "MP3::update() stub.\n";
	}


	public function create($debug=0) {

		if ( $debug )
			$this->display();

	    $q="insert into mp3s values (null,'".addslashes($this->filename)."',".$this->filesize.",'".$this->md5."',";

	    if ( $this->artist ) $q .= "'".addslashes($this->artist)."',";
	    else $q .= "null,";

	    if ( $this->album ) $q .= "'".addslashes($this->album)."',";
	    else $q .= "null,";

	    if ( $this->title ) $q .= "'".addslashes($this->title)."',";
	    else $q .= "null,";

	    if ( $this->track ) $q .= "'".$this->track."',";
	    else $q .= "null,";

	    if ( $this->year ) $q .= "'".$this->year."')";
	    else $q .= "null)";

		if ( $debug )
			print "\n\n".$q."\n\n";

		runSQL($q,1);

	}
}


//$mp3 = new MP3('/DWH/MP3.car32/Metal/Drifter.mp3');
//$mp3->display();
//print $mp3;

?>
