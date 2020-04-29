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
    'title' => 'T3Blog Extbase',
    'description' => 'A record based blog extension for TYPO3 CMS powered by Extbase / Fluid. Flexible and powerful!',
    'category' => 'plugin',
    'author' => 'Felix Nagel',
    'author_email' => 'info@felixnagel.com',
    'author_company' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'clearCacheOnLoad' => 0,
    'version' => '5.1.0',
    'constraints' => [
        'depends' => [
            'php' => '7.0.0-7.3.99',
            'typo3' => '9.4.0-9.5.99',
        ],
        'conflicts' => [
            't3blog' => '',
            'realurl' => '2.0.0-2.0.10',
        ],
        'suggests' => [
            'seo' => '',
        ],
    ],
];
