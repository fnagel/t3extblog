<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_blog_nl',
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'default_sortby' => 'ORDER BY crdate DESC',
        'languageField' => 'sys_language_uid',
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
        'searchFields' => 'email',
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
        'email' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_blog_nl.email',
            'config' => [
                'type' => 'email',
                'required' => true,
            ],
        ],
        'lastsent' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_blog_nl.lastsent',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
        ],
        'code' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_blog_nl.code',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
                'size' => 30,
            ],
        ],
        'privacy_policy_accepted' => [
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_blog_nl.privacy_policy_accepted',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => '
            sys_language_uid, email,
            --div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_blog_nl.tabs.meta,
                lastsent, code,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility;visibility'
        ],
    ],
    'palettes' => [
        'visibility' => [
            'showitem' => 'hidden, privacy_policy_accepted',
        ],
    ],
];
