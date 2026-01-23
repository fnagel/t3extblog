<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "t3extblog".
 *
 * Auto generated 18-02-2014 01:36
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'T3Extblog (T3Blog Extbase)',
    'description' => 'A record based blog extension for TYPO3 CMS. Easy to use and packed with features (incl.
        comments, subscriptions for comments and posts, Wordpress like subscription manager, reasonable email sending
        in FE and BE, GDPR ready, BE modules, Dashboard widgets, RSS, Sitemap, ...). Flexible and powerful!',
    'category' => 'plugin',
    'author' => 'Felix Nagel',
    'author_email' => 'info@felixnagel.com',
    'author_company' => '',
    'state' => 'stable',
    'version' => '10.0.1-dev',
    'constraints' => [
        'depends' => [
            'php' => '8.2.0-8.4.99',
            'typo3' => '14.0.0-14.1.99',
            'dashboard' => '',
        ],
        'conflicts' => [
            't3blog' => '',
            'vhs' => '7.0.0-7.0.1',
        ],
        'suggests' => [
            'seo' => '',
        ],
    ],
];
