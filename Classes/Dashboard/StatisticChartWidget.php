<?php

namespace FelixNagel\T3extblog\Dashboard;

use FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository;
use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class StatisticChartWidget extends AbstractDoughnutChartWidget
{
    protected $title = self::LOCALLANG_FILE . 'widget.statisticChart.title';
    protected $description = self::LOCALLANG_FILE . 'widget.statisticChart.description';

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     * @var BlogSubscriberRepository
     */
    protected $blogSubscriberRepository;

    /**
     * @var PostSubscriberRepository
     */
    protected $postSubscriberRepository;

    /**
     * @inheritDoc
     */
    protected function initialize()
    {
        parent::initialize();

        $this->postRepository = $this->objectManager->get(PostRepository::class);
        $this->commentRepository = $this->objectManager->get(CommentRepository::class);
        $this->blogSubscriberRepository = $this->objectManager->get(BlogSubscriberRepository::class);
        $this->postSubscriberRepository = $this->objectManager->get(PostSubscriberRepository::class);
    }

    /**
     * @inheritDoc
     */
    protected function prepareChartData(): void
    {
        $pids = $this->getStoragePids();

        $postsCount = $this->postRepository->findByPage($pids)->count();
        $commentsCount = $this->commentRepository->findValid($pids)->count();
        $blogSubscribersCount = $this->blogSubscriberRepository->findByPage($pids)->count();
        $postSubscribersCount = $this->postSubscriberRepository->findByPage($pids)->count();

        $this->chartData = [
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
                        $this->chartColors[0],
                        $this->chartColors[1],
                        $this->chartColors[2],
                    ],
                    'data' => [
                        $postsCount,
                        $commentsCount,
                        $blogSubscribersCount,
                        $postSubscribersCount,
                    ]
                ]
            ],
        ];
    }
}
