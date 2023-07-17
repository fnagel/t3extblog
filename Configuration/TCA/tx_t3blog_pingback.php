<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_pingback',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'typeicon_classes' => [
            'default' => 'extensions-t3extblog-trackback',
        ],
        'searchFields' => 'title,url,text,',
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
            ],
        ],
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_pingback.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
        ],
        'url' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_pingback.url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'softref' => 'url',
            ],
        ],
        'date' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_pingback.date',
            'config' => [
                'type' => 'datetime',
                'default' => '0',
            ],
        ],
        'text' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_pingback.text',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'hidden,--palette--;;1,title,url,date,text'],
    ],
    'palettes' => [
        '1' => ['showitem' => 'starttime, endtime'],
    ],
];
