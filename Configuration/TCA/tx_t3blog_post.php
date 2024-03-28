<?php

use TYPO3\CMS\Core\Resource\FileType;
use FelixNagel\T3extblog\Domain\Model\Post;

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'translationSource' => 'l10n_source',
        'prependAtCopy' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.prependAtCopy',
        'hideAtCopy' => true,
        'default_sortby' => 'ORDER BY date DESC',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'typeicon_classes' => [
            'default' => 'extensions-t3extblog-post',
        ],
        'searchFields' => 'title',
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '', 'value' => 0],
                ],
                'foreign_table' => 'tx_t3blog_post',
                'foreign_table_where' => 'AND {#tx_t3blog_post}.{#pid}=###CURRENT_PID### AND {#tx_t3blog_post}.{#sys_language_uid} IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => '',
            ],
        ],
        'l10n_source' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
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
                'default' => '1',
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
        'fe_group' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 5,
                'maxitems' => 20,
                'items' => [
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                        'value' => -1,
                    ],
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                        'value' => -2,
                    ],
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                        'value' => '--div--',
                    ],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
            ],
        ],
        'title' => [
            'exclude' => 1,
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.title',
            'config' => [
                'type' => 'input',
                'required' => true,
            ],
        ],
        'url_segment' => [
            'exclude' => true,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.url_segment',
            'config' => [
                'type' => 'slug',
                'size' => 30,
                'max' => 255,
                'eval' => 'uniqueInSite',
                'fallbackCharacter' => '-',
                'generatorOptions' => [
                    'fields' => ['title'],
                    'replacements' => [
                        '/' => '-'
                    ],
                ],
            ],
        ],
        'tagClouds' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.tagClouds',
            'config' => [
                'placeholder' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.tagClouds.placeholder',
                'type' => 'input',
                'size' => 150,
                'max' => 200,
                'eval' => 'trim, lower',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'author' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.author',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'be_users',
                'foreign_table_where' => ' AND be_users.disable = 0 '.
                    'AND (be_users.username != "_cli_lowlevel" AND be_users.username != "_cli_scheduler" AND be_users.username != "_cli_") '.
                    'ORDER BY be_users.username',
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
                'prepend_tname' => false,
            ],
        ],
        'date' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.date',
            'config' => [
                'type' => 'datetime',
                'default' => mktime(date('H'), date('i'), 0, date('m'), date('d'), date('Y')),
            ],
        ],
        'content' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.content',
            'config' => [
                'type' => 'inline',
                'allowed' => 'tt_content',
                'foreign_table' => 'tt_content',
                'foreign_table_field' => 'irre_parenttable',
                'foreign_field' => 'irre_parentid',
                'foreign_sortby' => 'sorting',
                'minitems' => 0,
                'maxitems' => 99,
                'appearance' => [
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                    'levelLinksPosition' => 'bottom',
                    'useSortable' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1,
                    'showSynchronizationLink' => 1,
                    'enabledControls' => [
                        'info' => false,
                    ],
                ],
                'behaviour' => [
                    'enableCascadingDelete' => true,
                    'allowLanguageSynchronization' => true,
                ],
                'foreign_selector_fieldTcaOverride' => [
                    'l10n_mode' => 'prefixLangTitle',
                ],
                'default' => '',
                'overrideChildTca' => [],
            ],
        ],
        'allow_comments' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.allow_comments',
            'config' => [
                'type' => 'radio',
                'default' => 0,
                'items' => [
                    [
                        'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.allow_comments.I.0',
                        'value' => Post::ALLOW_COMMENTS_EVERYONE,
                    ],
                    [
                        'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.allow_comments.I.1',
                        'value' => Post::ALLOW_COMMENTS_NOBODY,
                    ],
                    [
                        'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.allow_comments.I.2',
                        'value' => Post::ALLOW_COMMENTS_LOGIN,
                    ],
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'cat' => [
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.cat',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectTree',
                'treeConfig' => [
                    'parentField' => 'parent_id',
                    'appearance' => [
                        'expandAll' => true,
                        'showHeader' => true,
                    ],
                ],
                'MM' => 'tx_t3blog_post_cat_mm',
                'foreign_table' => 'tx_t3blog_cat',
                'foreign_table_where' => ' AND tx_t3blog_cat.pid = ###CURRENT_PID### AND tx_t3blog_cat.hidden = 0 AND tx_t3blog_cat.deleted = 0 AND (tx_t3blog_cat.sys_language_uid = 0 OR tx_t3blog_cat.l18n_parent = 0) ORDER BY tx_t3blog_cat.sorting',
                'size' => 10,
                'minitems' => 1,
                'maxitems' => 20,
            ],
        ],
        'trackback' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.trackback',
            'config' => [
                'type' => 'text',
                'cols' => 45,
                'rows' => 3,
                'wrap' => 'off',
                'softref' => 'url',
            ],
        ],
        'trackback_hash' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.trackback_hash',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 130,
                'eval' => 'trim',
            ],
        ],
        'number_views' => [
            'exclude' => 1,
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.number_views',
            'config' => [
                'type' => 'number',
                'size' => 8,
            ],
        ],
        'meta_description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.meta_description',
            'config' => [
                'type' => 'text',
                'cols' => 45,
                'rows' => 3,
            ],
        ],
        'meta_keywords' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.meta_keywords',
            'config' => [
                'placeholder' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.meta_keywords.placeholder',
                'type' => 'text',
                'cols' => 45,
                'rows' => 2,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'preview_mode' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.preview_mode',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    [
                        'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.preview_mode.0',
                        'value' => '0',
                    ],
                    [
                        'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.preview_mode.1',
                        'value' => '1',
                    ],
                    [
                        'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.preview_mode.2',
                        'value' => '2',
                    ],
                    [
                        'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.preview_mode.3',
                        'value' => '3',
                    ],
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'preview_text' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.preview_text',
            'config' => [
                'type' => 'text',
                'cols' => 45,
                'rows' => 10,
                'enableRichtext' => '1',
                'richtextConfiguration' => 'default',
                'softref' => 'typolink_tag,email[subst],url',
            ],
        ],
        'preview_image' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.preview_image',
            'config' => [
                'type' => 'file',
                'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                'maxitems' => 1,
                'overrideChildTca' => [
                    'types' => [
                        FileType::IMAGE->value => [
                            'showitem' => '
                                --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;t3extblogPostPreviewImagePalette,
                                --palette--;;filePalette'
                        ],
                    ],
                ],
                'appearance' => [
                    'collapseAll' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1,
                    'showSynchronizationLink' => 1,
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'mails_sent' => [
            'displayCond' => 'HIDE_FOR_NON_ADMINS',
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.mails_sent',
            'config' => [
                'type' => 'check',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => '
            --div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.tabs.post,
                --palette--;;meta,
                title,url_segment,content,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                --palette--;;language,
            --div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.tabs.preview,
                preview_mode,preview_image,preview_text,
            --div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.tabs.category,
                tagClouds,cat,
            --div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.tabs.interactive,
                allow_comments,trackback,number_views,mails_sent,
            --div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.tabs.meta,
                meta_description,meta_keywords,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility;visibility,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.access;access'
        ],
    ],
    'palettes' => [
        'meta' => [
            'showitem' => 'date,author',
        ],
        'language' => [
            'showitem' => 'sys_language_uid,l18n_parent',
        ],
        'visibility' => [
            'showitem' => 'hidden',
        ],
        'access' => [
            'showitem' => '
				starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.starttime_formlabel,
				endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.endtime_formlabel,
				--linebreak--, fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.fe_group_formlabel',
        ],
    ],
];
