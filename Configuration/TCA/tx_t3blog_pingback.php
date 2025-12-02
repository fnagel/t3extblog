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
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'typeicon_classes' => [
            'default' => 'extensions-t3extblog-trackback',
        ],
        'searchFields' => 'title,url,text,',
    ],
    'columns' => [
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
