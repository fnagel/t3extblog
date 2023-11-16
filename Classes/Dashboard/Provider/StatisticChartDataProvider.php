<?php

namespace FelixNagel\T3extblog\Dashboard\Provider;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository;
use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class StatisticChartDataProvider extends AbstractDataProvider implements ChartDataProviderInterface
{
    public function __construct(
        protected PostRepository $postRepository,
        protected CommentRepository $commentRepository,
        protected BlogSubscriberRepository $blogSubscriberRepository,
        protected PostSubscriberRepository $postSubscriberRepository
    ) {
    }

    public function getChartData(): array
    {
        $pids = $this->getStoragePids();

        $postsCount = $this->postRepository->findByPage($pids)->count();
        $commentsCount = $this->commentRepository->findValid($pids)->count();
        $blogSubscribersCount = $this->blogSubscriberRepository->findByPage($pids)->count();
        $postSubscribersCount = $this->postSubscriberRepository->findByPage($pids)->count();

        $chartColors = WidgetApi::getDefaultChartColors();

        return [
            'labels' => [
                $this->translate('widget.statisticChart.chart.posts'),
                $this->translate('widget.statisticChart.chart.comments'),
                $this->translate('widget.statisticChart.chart.blogSubscribers'),
                $this->translate('widget.statisticChart.chart.postSubscribers'),
            ],
            'datasets' => [
                [
                    'backgroundColor' => [
                        'lightgrey',
                        $chartColors[0],
                        $chartColors[1],
                        $chartColors[2],
                    ],
                    'data' =>[
                        $postsCount,
                        $commentsCount,
                        $blogSubscribersCount,
                        $postSubscribersCount,
                    ],
                ],
            ],
        ];
    }
}
