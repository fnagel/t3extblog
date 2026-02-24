<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use Psr\Http\Message\ResponseInterface;

/**
 * TagController.
 */
class TagController extends AbstractController
{
    public function __construct(protected PostRepository $postRepository)
    {
    }

    public function cloudAction(): ResponseInterface
    {
        $settings = $this->settings['tags'];
        $limit = isset($settings['limit']) ? (int)$settings['limit'] : 30;
        $minimum = isset($settings['min']) ? (int)$settings['min'] : 1;

        $this->addCacheTags('tx_t3blog_tags');
        $this->addCacheTags($this->postRepository);

        $tags = $this->postRepository->tagCloud($limit, $minimum);
        // Not using shuffle as we always the same randomization
        if ($settings['random'] ?? false) {
            $tags = array_map(function ($tag) {
                $tag['md5'] = md5($tag['title']);
                return $tag;
            }, $tags);
            usort($tags, fn ($a, $b) => strcmp($a['md5'], $b['md5']));
        }

        $this->view->assign('tags', $tags);

        return $this->htmlResponse();
    }
}
