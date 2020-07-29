#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
    irre_parentid int(11) DEFAULT '0' NOT NULL,
    irre_parenttable varchar(20) DEFAULT '' NOT NULL
);


#
# Table structure for table 'tx_t3blog_post'
#
CREATE TABLE tx_t3blog_post (
    title tinytext NOT NULL,
    url_segment varchar(255) DEFAULT '' NOT NULL,
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
    mails_sent tinyint(3) DEFAULT NULL
);


#
# Table structure for table 'tx_t3blog_cat'
#
CREATE TABLE tx_t3blog_cat (
    parent_id int(11) DEFAULT '0' NOT NULL,
    catname tinytext NOT NULL,
    url_segment varchar(255) DEFAULT '' NOT NULL,
    description tinytext NOT NULL
);


#
# Table structure for table 'tx_t3blog_com'
#
CREATE TABLE tx_t3blog_com (
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
    privacy_policy_accepted tinyint(4) DEFAULT '0' NOT NULL
);


#
# Table structure for table 'tx_t3blog_com_nl'
#
CREATE TABLE tx_t3blog_com_nl (
    email tinytext NOT NULL,
    name tinytext NOT NULL,
    post_uid int(11) DEFAULT '0' NOT NULL,
    lastsent int(11) DEFAULT '0' NOT NULL,
    code varchar(32) DEFAULT '' NOT NULL,
    privacy_policy_accepted tinyint(4) DEFAULT '0' NOT NULL
);


#
# Table structure for table 'tx_t3blog_blog_nl'
#
CREATE TABLE tx_t3blog_blog_nl (
    email tinytext NOT NULL,
    lastsent int(11) DEFAULT '0' NOT NULL,
    code varchar(32) DEFAULT '' NOT NULL,
    privacy_policy_accepted tinyint(4) DEFAULT '0' NOT NULL
);


#
# Table structure for table 'tx_t3blog_pingback'
#
CREATE TABLE tx_t3blog_pingback (
    deleted tinyint(1) DEFAULT '0' NOT NULL,
    hidden tinyint(1) DEFAULT '0' NOT NULL,
    starttime int(11) DEFAULT '0' NOT NULL,
    endtime int(11) DEFAULT '0' NOT NULL,
    title tinytext NOT NULL,
    url tinytext NOT NULL,
    date int(11) DEFAULT '0' NOT NULL,
    text text NOT NULL
);


#
# Table structure for table 'tx_t3blog_trackback'
#
CREATE TABLE tx_t3blog_trackback (
    fromurl varchar(100) DEFAULT '' NOT NULL,
    text varchar(255) DEFAULT '' NOT NULL,
    title varchar(50) DEFAULT '' NOT NULL,
    blogname varchar(100) DEFAULT '' NOT NULL,
    postid int(11) DEFAULT '0' NOT NULL
);


#
# Table structure for table 'tx_t3blog_post_cat_mm'
#
CREATE TABLE tx_t3blog_post_cat_mm (
    uid_local int(11) DEFAULT '0' NOT NULL,
    uid_foreign int(11) DEFAULT '0' NOT NULL,
    tablenames varchar(30) DEFAULT '' NOT NULL,
    sorting int(11) DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
