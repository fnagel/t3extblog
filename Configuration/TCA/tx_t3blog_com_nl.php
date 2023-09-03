<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_com_nl',
        'label' => 'email',
        'label_alt' => 'post_uid',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'default_sortby' => 'ORDER BY crdate DESC',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'typeicon_classes' => [
            'default' => 'extensions-t3extblog-subscriber',
        ],
        'searchFields' => 'email,name',
    ],
    'columns' => [
        'crdate' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'tstamp' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'deleted' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'email' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_com_nl.email',
            'config' => [
                'type' => 'email',
                'required' => true,
            ],
        ],
        'name' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_com_nl.name',
            'config' => [
                'type' => 'input',
                'required' => true,
            ],
        ],
        'post_uid' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_com_nl.post_uid',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_t3blog_post',
                'foreign_table_where' => ' AND tx_t3blog_post.deleted = 0 AND tx_t3blog_post.pid=###CURRENT_PID###',
                'minitems' => 1,
                'maxitems' => 1,
                'size' => 1,
                'fieldControl' => ['addRecord' => ['disabled' => false]],
            ],
        ],
        'lastsent' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_com_nl.lastsent',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
        ],
        'code' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_com_nl.code',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
                'size' => 30,
            ],
        ],
        'privacy_policy_accepted' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_com_nl.privacy_policy_accepted',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => '
            post_uid,
            --palette--;;contact,
            --div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_com_nl.tabs.meta,
                --palette--;;subscription,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility;visibility'
        ],
    ],
    'palettes' => [
        'contact' => [
            'showitem' => 'name, email',
        ],
        'subscription' => [
            'showitem' => 'lastsent, code',
        ],
        'visibility' => [
            'showitem' => 'hidden, privacy_policy_accepted',
        ],
    ],
];
