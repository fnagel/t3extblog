<?php

return [
    \FelixNagel\T3extblog\Domain\Model\Post::class => [
        'tableName' => 'tx_t3blog_post',
        'properties' => [
            'title' => [
                'fieldName' => 'title'
            ],
            'author' => [
                'fieldName' => 'link'
            ],
            'publishDate' => [
                'fieldName' => 'date'
            ],
            'content' => [
                'fieldName' => 'content'
            ],
            'allowComments' => [
                'fieldName' => 'allow_comments'
            ],
            'categories' => [
                'fieldName' => 'cat'
            ],
            'tagCloud' => [
                'fieldName' => 'tagClouds'
            ],
            'numberOfViews' => [
                'fieldName' => 'number_views'
            ],
            'mailsSent' => [
                'fieldName' => 'mails_sent'
            ],
            'metaDescription' => [
                'fieldName' => 'meta_description'
            ],
            'metaKeywords' => [
                'fieldName' => 'meta_keywords'
            ],
            'previewMode' => [
                'fieldName' => 'preview_mode'
            ],
            'previewText' => [
                'fieldName' => 'preview_text'
            ],
            'previewImage' => [
                'fieldName' => 'preview_image'
            ],
        ],
    ],

    \FelixNagel\T3extblog\Domain\Model\Content::class => [
        'tableName' => 'tt_content',
        'properties' => [
            'colPos' => [
                'fieldName' => 'colPos'
            ],
            'CType' => [
                'fieldName' => 'CType'
            ],
        ],
    ],

    \FelixNagel\T3extblog\Domain\Model\Category::class => [
        'tableName' => 'tx_t3blog_cat',
        'properties' => [
            'sorting' => [
                'fieldName' => 'sorting'
            ],
            'name' => [
                'fieldName' => 'catname'
            ],
            'description' => [
                'fieldName' => 'description'
            ],
            'parentId' => [
                'fieldName' => 'parent_id'
            ],
        ],
    ],

    \FelixNagel\T3extblog\Domain\Model\Comment::class => [
        'tableName' => 'tx_t3blog_com',
        'properties' => [
            'title' => [
                'fieldName' => 'title'
            ],
            'postId' => [
                'fieldName' => 'fk_post'
            ],
            'author' => [
                'fieldName' => 'author'
            ],
            'email' => [
                'fieldName' => 'email'
            ],
            'website' => [
                'fieldName' => 'website'
            ],
            'date' => [
                'fieldName' => 'date'
            ],
            'text' => [
                'fieldName' => 'text'
            ],
            'approved' => [
                'fieldName' => 'approved'
            ],
            'spam' => [
                'fieldName' => 'spam'
            ],
            'mailsSent' => [
                'fieldName' => 'mails_sent'
            ],
            'privacyPolicyAccepted' => [
                'fieldName' => 'privacy_policy_accepted'
            ],
        ],
    ],

    \FelixNagel\T3extblog\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users',
    ],

    \FelixNagel\T3extblog\Domain\Model\PostSubscriber::class => [
        'tableName' => 'tx_t3blog_com_nl',
        'properties' => [
            'name' => [
                'fieldName' => 'name'
            ],
            'email' => [
                'fieldName' => 'email'
            ],
            'postUid' => [
                'fieldName' => 'post_uid'
            ],
            'lastSent' => [
                'fieldName' => 'lastsent'
            ],
            'code' => [
                'fieldName' => 'code'
            ],
            'privacyPolicyAccepted' => [
                'fieldName' => 'privacy_policy_accepted'
            ],
        ],
    ],

    \FelixNagel\T3extblog\Domain\Model\BlogSubscriber::class => [
        'tableName' => 'tx_t3blog_blog_nl',
        'properties' => [
            'email' => [
                'fieldName' => 'email'
            ],
            'postUid' => [
                'fieldName' => 'post_uid'
            ],
            'lastSent' => [
                'fieldName' => 'lastsent'
            ],
            'code' => [
                'fieldName' => 'code'
            ],
            'privacyPolicyAccepted' => [
                'fieldName' => 'privacy_policy_accepted'
            ],
        ],
    ],
];
