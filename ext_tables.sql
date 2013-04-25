#
# Table structure for table 'tx_t3extblog_domain_model_blog'
#
CREATE TABLE tx_t3extblog_domain_model_blog (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	posts int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	t3_origuid int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_t3blog_post'
#
CREATE TABLE tx_t3blog_post (

	blog int(11) unsigned DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	author varchar(255) DEFAULT '' NOT NULL,
	publish_date int(11) DEFAULT '0' NOT NULL,
	allow_comments tinyint(1) unsigned DEFAULT '0' NOT NULL,
	tag_cloud text NOT NULL,
	number_of_views int(11) DEFAULT '0' NOT NULL,
	content int(11) unsigned DEFAULT '0' NOT NULL,
	category int(11) unsigned DEFAULT '0' NOT NULL,
	comments int(11) unsigned DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (

	title_text varchar(255) DEFAULT '' NOT NULL,

);

#
# Table structure for table 'tx_t3blog_cat'
#
CREATE TABLE tx_t3blog_cat (

	name varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,

);

#
# Table structure for table 'tx_t3blog_com'
#
CREATE TABLE tx_t3blog_com (

	posts int(11) unsigned DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	author varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	website varchar(255) DEFAULT '' NOT NULL,
	date int(11) DEFAULT '0' NOT NULL,
	text text NOT NULL,
	approved tinyint(1) unsigned DEFAULT '0' NOT NULL,
	spam tinyint(1) unsigned DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'tx_t3blog_post'
#
CREATE TABLE tx_t3blog_post (

	blog  int(11) unsigned DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'tx_t3blog_com'
#
CREATE TABLE tx_t3blog_com (

	posts  int(11) unsigned DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'tx_t3extblog_posts_content_mm'
#
CREATE TABLE tx_t3extblog_posts_content_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_t3extblog_posts_category_mm'
#
CREATE TABLE tx_t3extblog_posts_category_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);