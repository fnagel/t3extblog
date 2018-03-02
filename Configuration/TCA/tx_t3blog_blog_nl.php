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
        'showRecordFieldList' => 'sys_language_uid,hidden,email,lastsent,code',
    ),
    'columns' => array(
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => array(
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0]
                ),
                'default' => 0,
            ),
        ),
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
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
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,required',
                'size' => 13,
            ),
        ),
        'code' => array(
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.code',
            'config' => array(
                'type' => 'input',
                'readOnly' => true,
                'size' => 30,
            ),
        ),
    ),
    'types' => array(
        '0' => array('showitem' => '
			sys_language_uid, email, hidden,
			--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.tabs.meta,
				lastsent, code'),
    ),
);
