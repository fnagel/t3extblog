<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Index\Traversing;

use Lochmueller\Index\Traversing\Extender\RecordExtender;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Extender preset for the page traversing of EXT:index (frontend and HTTP indexing).
 *
 * The storage pages of the posts need to be configured via "recordStorages" in the index configuration.
 */
#[AutoconfigureTag(name: 'index.extender')]
class PostExtender extends RecordExtender
{
    public function getName(): string
    {
        return 't3extblog-posts';
    }

    protected function getBaseConfiguration(): array
    {
        return [
            'table' => 'tx_t3blog_post',
            'arguments' => [
                'tx_t3extblog_blogsystem' => [
                    'controller' => 'Post',
                    'action' => 'show',
                    'post' => '{uid}',
                ],
            ],
        ];
    }
}
