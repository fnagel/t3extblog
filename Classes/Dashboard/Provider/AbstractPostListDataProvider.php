<?php

namespace FelixNagel\T3extblog\Dashboard\Provider;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\PostRepository;

abstract class AbstractPostListDataProvider extends AbstractListDataProvider
{
    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var array
     */
    protected $options = [
        'limit' => 10,
    ];

    /**
     * @param PostRepository $postRepository
     * @param array $options
     */
    public function __construct(PostRepository $postRepository, array $options = [])
    {
        $this->postRepository = $postRepository;
        $this->options = array_merge(
            $this->options,
            $options
        );
    }
}
