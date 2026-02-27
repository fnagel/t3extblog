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

        $min = PHP_INT_MAX;
        $max = 0;
        $tags = array_map(function ($tag) use (&$min, &$max) {
            $tag['md5'] = md5($tag['title']);

            $min = min($min, $tag['posts']);
            $max = max($max, $tag['posts']);

            return $tag;
        }, $tags);

        // Not using shuffle as we want always the same randomization
        if ($settings['random'] ?? false) {
            usort($tags, fn ($a, $b) => strcmp($a['md5'], $b['md5']));
        }

        $this->view->assign('tags', $tags);

        $three = round(($max - $min) / 3);
        $six = round(($three * 2) / 3);
        $this->view->assign('statistic', [
            'min' => $min,
            'max' => $max,
            'count' => count($tags),
            'threshold' => [
                1 => $min + $six,
                2 => round($min + $six * 2),
                3 => round($min + $three * 2)],
        ]);

        return $this->htmlResponse();
    }
}
