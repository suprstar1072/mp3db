
create database mp3db;

drop table mp3s;
CREATE TABLE `mp3s` (
	`f_id` int(10) unsigned NOT NULL AUTO_INCREMENT primary key COMMENT 'file primary id',
	`f_path` varchar(512),
	`f_size` int(16),
	`f_md5` varchar(32),
	`f_artist` varchar(64),
	`f_album` varchar(64),
	`f_title` varchar(128),
	`f_track` int(2),
	`f_year` int(4)
) ENGINE=MyISAM
;
create unique index file_path on mp3s (f_path);
create index file_md5_size on mp3s (f_md5,f_size);
create index file_song_alt on mp3s (f_artist,f_album,f_title);



drop table playlists;
CREATE TABLE `playlists` (
	`p_id` int(10) unsigned NOT NULL AUTO_INCREMENT primary key COMMENT 'file primary id',
	`p_name` varchar(32) not null,
	`p_fid` int(10) not null
) ENGINE=MyISAM
;
create unique index pl_name_fid on playlists (p_name,p_fid);
