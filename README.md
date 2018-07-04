# mp3db
A simple iTunes like lightweight web app written in PHP7 / MySQL..

Requires eyeD3.  Tested and working on 0.6.18:

ss@ss-btcd2:~/code/mp3db/mp3db.ui$ eyeD3 --version
eyeD3 0.6.18 (C) Copyright 2002-2011 Travis Shirk <travis@pobox.com>
This program comes with ABSOLUTELY NO WARRANTY! See COPYING for details.
Run with --help/-h for usage information or see the man page 'eyeD3(1)'



-------------------------------------------------------------------------
-------------------------------------------------------------------------
-------------------------------------------------------------------------


# mp3db.backend
This is the piece that will go thru your music directory and index all your
music.  You need php7, mysql, and eyeD3 installed.


-------------------------------------------------------------------------
-------------------------------------------------------------------------
-------------------------------------------------------------------------


# mp3db.ui
This is the directory you put in web space.  Navigate to the URL containing
this directory to browse and play music, create playlists, edit mp3 tag
data, sync to an Android device, SD card, or any filesystem, and more..

