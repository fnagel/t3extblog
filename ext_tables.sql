#
# Table structure for table 'tx_t3blog_post_content_mm'
#
#
CREATE TABLE tx_t3blog_post_content_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);


#
# Table structure for table 'tx_t3blog_post_cat_mm'
#
#
CREATE TABLE tx_t3blog_post_cat_mm (
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	KEY uid_all (uid_local,uid_foreign)
);


#
# Table structure for table 'tx_t3blog_post'
#
CREATE TABLE tx_t3blog_post (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(1) DEFAULT '0' NOT NULL,
	hidden tinyint(1) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	author int(11) DEFAULT '0' NOT NULL,
	date int(11) DEFAULT '0' NOT NULL,
	content text NOT NULL,
	allow_comments int(11) DEFAULT '0' NOT NULL,
	cat int(11) DEFAULT '0' NOT NULL,
	tagClouds text NOT NULL,
	trackback text NOT NULL,
	trackback_hash varchar(130) DEFAULT '' NOT NULL,
	number_views int(11) DEFAULT '0' NOT NULL,
	meta_description text,
	meta_keywords varchar(255) DEFAULT '',
	preview_mode tinyint(4) DEFAULT '0' NOT NULL,
	preview_text text,
	preview_image int(11) unsigned DEFAULT '0',
	PRIMARY KEY (uid),
	KEY be_date (pid,`date`)
);


#
# Table structure for table 'tx_t3blog_blogroll'
#
CREATE TABLE tx_t3blog_blogroll (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	url tinytext NOT NULL,
	image blob NOT NULL,
	description text NOT NULL,
	xfn varchar(20) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid)
);


#
# Table structure for table 'tx_t3blog_cat'
#
CREATE TABLE tx_t3blog_cat (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	parent_id int(11) DEFAULT '0' NOT NULL,
	catname tinytext NOT NULL,
	description tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY sel01 (pid,deleted,hidden),
	KEY catname (catname(24))
);


#
# Table structure for table 'tx_t3blog_com'
#
CREATE TABLE tx_t3blog_com (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(1) DEFAULT '0' NOT NULL,
	hidden tinyint(1) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	author tinytext NOT NULL,
	email tinytext NOT NULL,
	website tinytext NOT NULL,
	date int(11) DEFAULT '0' NOT NULL,
	text text NOT NULL,
	approved tinyint(3) DEFAULT '0' NOT NULL,
	spam tinyint(3) DEFAULT '0' NOT NULL,
	fk_post int(11) DEFAULT '0' NOT NULL,
	mails_sent tinyint(3) DEFAULT NULL,

	PRIMARY KEY (uid),
	KEY sel01 (pid,deleted,hidden,crdate),
	KEY sel02 (`date`),
	KEY be_list (fk_post,deleted)
);


#
# Table structure for table 'tx_t3blog_com_nl'
#
CREATE TABLE tx_t3blog_com_nl (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	email tinytext NOT NULL,
	name tinytext NOT NULL,
	post_uid int(11) DEFAULT '0' NOT NULL,
	lastsent int(11) DEFAULT '0' NOT NULL,
	code tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_t3blog_blog_nl'
#
CREATE TABLE tx_t3blog_blog_nl (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	email tinytext NOT NULL,
	lastsent int(11) DEFAULT '0' NOT NULL,
	code tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_t3blog_pingback'
#
CREATE TABLE tx_t3blog_pingback (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(1) DEFAULT '0' NOT NULL,
	hidden tinyint(1) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	url tinytext NOT NULL,
	date int(11) DEFAULT '0' NOT NULL,
	text text NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_t3blog_trackback'
#
CREATE TABLE tx_t3blog_trackback (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	fromurl varchar(100) DEFAULT '' NOT NULL,
	text varchar(255) DEFAULT '' NOT NULL,
	title varchar(50) DEFAULT '' NOT NULL,
	blogname varchar(100) DEFAULT '' NOT NULL,
	postid int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	irre_parentid int(11) DEFAULT '0' NOT NULL,
	irre_parenttable tinytext NOT NULL,

	KEY t3blog (irre_parentid,sorting)
);
