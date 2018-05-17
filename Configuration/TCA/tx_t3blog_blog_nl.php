<?php

return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl',
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'default_sortby' => 'ORDER BY crdate DESC',
        'languageField' => 'sys_language_uid',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'typeicon_classes' => [
            'default' => 'extensions-t3extblog-subscriber',
        ],
        'searchFields' => 'email',
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid,hidden,email,lastsent,code,privacy_policy_accepted',
    ),
    'columns' => array(
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cms/locallang_ttc.xlf:sys_language_uid_formlabel',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => array(
                    array(
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple',
                    ),
                ),
                'default' => 0,
            ),
        ),
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0',
            ),
        ),
        'email' => array(
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.email',
            'config' => array(
                'type' => 'input',
                'eval' => 'required',
            ),
        ),
        'lastsent' => array(
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.lastsent',
            'config' => array(
                'type' => 'input',
                'eval' => 'datetime,required',
	            'size' => 13,
	            'max' => 25,
            ),
        ),
        'code' => array(
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.code',
            'config' => array(
                'type' => 'input',
                'readOnly' => true,
                'size' => '30',
            ),
        ),
        'privacy_policy_accepted' => array(
            'label' => '...',
            'config' => array(
                'type' => 'check',
                'default' => 0,
            ),
        ),
    ),
    'types' => array(
        '0' => array('showitem' => '
			sys_language_uid, email, hidden,
			--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.tabs.meta,
				lastsent, code, privacy_policy_accepted'),
    ),
);
