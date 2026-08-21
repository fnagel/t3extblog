<?php

declare(strict_types=1);

use FelixNagel\T3extblog\Dashboard\NumberWithIconWidget;
use FelixNagel\T3extblog\Dashboard\Provider\DraftPostListDataProvider;
use FelixNagel\T3extblog\Dashboard\Provider\LatestCommentListDataProvider;
use FelixNagel\T3extblog\Dashboard\Provider\LatestPostListDataProvider;
use FelixNagel\T3extblog\Dashboard\Provider\ModuleButtonProvider;
use FelixNagel\T3extblog\Dashboard\Provider\PendingCommentListDataProvider;
use FelixNagel\T3extblog\Dashboard\Provider\PendingCommentNumberWithIconDataProvider;
use FelixNagel\T3extblog\Dashboard\Provider\StatisticChartDataProvider;
use FelixNagel\T3extblog\Dashboard\Provider\SubscriberListDataProvider;
use FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository;
use FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository;
use FelixNagel\T3extblog\EventListener\VisualEditor\ModifyNewContentElementWizardUrlParameter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Backend\Controller\Event\ModifyNewContentElementWizardItemsEvent;
use TYPO3\CMS\Dashboard\Widgets\DoughnutChartWidget;
use TYPO3\CMS\Dashboard\Widgets\ListWidget;
use TYPO3\CMS\Dashboard\WidgetRegistry;

return function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder) {
    if (!$containerBuilder->hasExtension('visual_editor')) {
        $containerBuilder->removeDefinition(ModifyNewContentElementWizardUrlParameter::class);
        $containerBuilder->removeDefinition(ModifyNewContentElementWizardItemsEvent::class);
    }

    // Check if WidgetRegistry is defined, which means that EXT:dashboard is available.
    // Registration directly in Services.yaml will break without EXT:dashboard installed!
    if (!$containerBuilder->hasDefinition(WidgetRegistry::class)) {
        return;
    }

    $services = $configurator->services();
    $services->defaults()->autowire()->autoconfigure();

    // Register Dashboard classes (excluded from Services.yaml autowire scanning)
    $services->load('FelixNagel\\T3extblog\\Dashboard\\', '../Classes/Dashboard/*');

    // Button provider
    $services->set('dashboard.buttons.t3extblog.latestPosts', ModuleButtonProvider::class)
        ->arg('$title', 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.latestPosts.moreItems')
        ->arg('$linkArguments', ['controller' => 'BackendPost', 'action' => 'index']);

    $services->set('dashboard.buttons.t3extblog.draftPosts', ModuleButtonProvider::class)
        ->arg('$title', 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.draftPosts.moreItems')
        ->arg('$linkArguments', ['controller' => 'BackendPost', 'action' => 'index']);

    $services->set('dashboard.buttons.t3extblog.latestComments', ModuleButtonProvider::class)
        ->arg('$title', 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.latestComments.moreItems')
        ->arg('$linkArguments', ['controller' => 'BackendComment', 'action' => 'index']);

    $services->set('dashboard.buttons.t3extblog.pendingComments', ModuleButtonProvider::class)
        ->arg('$title', 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.pendingComments.moreItems')
        ->arg('$linkArguments', ['controller' => 'BackendComment', 'action' => 'index']);

    $services->set('dashboard.buttons.t3extblog.postSubscriber', ModuleButtonProvider::class)
        ->arg('$title', 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.postSubscriber.moreItems')
        ->arg('$linkArguments', ['controller' => 'BackendSubscriber', 'action' => 'indexPostSubscriber']);

    $services->set('dashboard.buttons.t3extblog.blogSubscriber', ModuleButtonProvider::class)
        ->arg('$title', 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.blogSubscriber.moreItems')
        ->arg('$linkArguments', ['controller' => 'BackendSubscriber', 'action' => 'indexBlogSubscriber']);

    // Widgets
    $services->set('dashboard.widget.t3extblog.latestPosts', ListWidget::class)
        ->arg('$dataProvider', new Reference(LatestPostListDataProvider::class))
        ->arg('$buttonProvider', new Reference('dashboard.buttons.t3extblog.latestPosts'))
        ->arg('$options', ['partial' => 'PostWidget'])
        ->tag('dashboard.widget', [
            'identifier' => 't3extblogLatestPosts',
            'groupNames' => 't3extblog',
            'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.latestPosts.title',
            'description' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.latestPosts.description',
            'iconIdentifier' => 'content-widget-text',
            'height' => 'medium',
        ]);

    $services->set('dashboard.widget.t3extblog.draftPosts', ListWidget::class)
        ->arg('$dataProvider', new Reference(DraftPostListDataProvider::class))
        ->arg('$buttonProvider', new Reference('dashboard.buttons.t3extblog.draftPosts'))
        ->arg('$options', ['partial' => 'PostWidget'])
        ->tag('dashboard.widget', [
            'identifier' => 't3extblogDraftPosts',
            'groupNames' => 't3extblog',
            'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.draftPosts.title',
            'description' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.draftPosts.description',
            'iconIdentifier' => 'content-widget-text',
            'height' => 'medium',
        ]);

    $services->set('dashboard.widget.t3extblog.latestComments', ListWidget::class)
        ->arg('$dataProvider', new Reference(LatestCommentListDataProvider::class))
        ->arg('$buttonProvider', new Reference('dashboard.buttons.t3extblog.latestComments'))
        ->arg('$options', ['partial' => 'CommentWidget'])
        ->tag('dashboard.widget', [
            'identifier' => 't3extblogLatestComments',
            'groupNames' => 't3extblog',
            'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.latestComments.title',
            'description' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.latestComments.description',
            'iconIdentifier' => 'content-widget-text',
            'height' => 'medium',
        ]);

    $services->set('dashboard.widget.t3extblog.pendingComments', ListWidget::class)
        ->arg('$dataProvider', new Reference(PendingCommentListDataProvider::class))
        ->arg('$buttonProvider', new Reference('dashboard.buttons.t3extblog.pendingComments'))
        ->arg('$options', ['partial' => 'CommentWidget'])
        ->tag('dashboard.widget', [
            'identifier' => 't3extblogPendingComments',
            'groupNames' => 't3extblog',
            'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.pendingComments.title',
            'description' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.pendingComments.description',
            'iconIdentifier' => 'content-widget-text',
            'height' => 'medium',
        ]);

    $services->set('dashboard.widget.t3extblog.pendingCommentsNumberWithIcon', NumberWithIconWidget::class)
        ->arg('$dataProvider', new Reference(PendingCommentNumberWithIconDataProvider::class))
        ->arg('$options', [
            'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.pendingCommentsNumberWithIcon.title',
            'subtitle' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.pendingCommentsNumberWithIcon.subtitle',
            'icon' => 'extensions-t3extblog-plugin',
        ])
        ->arg('$buttonProvider', new Reference('dashboard.buttons.t3extblog.pendingComments'))
        ->tag('dashboard.widget', [
            'identifier' => 't3extblogPendingCommentsNumberWithIconWidget',
            'groupNames' => 't3extblog',
            'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.pendingCommentsNumberWithIcon.title',
            'description' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.pendingCommentsNumberWithIcon.description',
            'iconIdentifier' => 'content-widget-number',
        ]);

    $services->set('dashboard.provider.t3extblog.postSubscriber', SubscriberListDataProvider::class)
        ->arg('$subscriberRepository', new Reference(PostSubscriberRepository::class));

    $services->set('dashboard.widget.t3extblog.postSubscriber', ListWidget::class)
        ->arg('$dataProvider', new Reference('dashboard.provider.t3extblog.postSubscriber'))
        ->arg('$buttonProvider', new Reference('dashboard.buttons.t3extblog.postSubscriber'))
        ->arg('$options', ['table' => 'tx_t3blog_com_nl', 'partial' => 'SubscriberWidget'])
        ->tag('dashboard.widget', [
            'identifier' => 't3extblogPostSubscriber',
            'groupNames' => 't3extblog',
            'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.postSubscriber.title',
            'description' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.postSubscriber.description',
            'iconIdentifier' => 'content-widget-text',
            'height' => 'medium',
        ]);

    $services->set('dashboard.provider.t3extblog.blogSubscriber', SubscriberListDataProvider::class)
        ->arg('$subscriberRepository', new Reference(BlogSubscriberRepository::class));

    $services->set('dashboard.widget.t3extblog.blogSubscriber', ListWidget::class)
        ->arg('$dataProvider', new Reference('dashboard.provider.t3extblog.blogSubscriber'))
        ->arg('$buttonProvider', new Reference('dashboard.buttons.t3extblog.blogSubscriber'))
        ->arg('$options', ['table' => 'tx_t3blog_blog_nl', 'partial' => 'SubscriberWidget'])
        ->tag('dashboard.widget', [
            'identifier' => 't3extblogBlogSubscriber',
            'groupNames' => 't3extblog',
            'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.blogSubscriber.title',
            'description' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.blogSubscriber.description',
            'iconIdentifier' => 'content-widget-text',
            'height' => 'medium',
        ]);

    $services->set('dashboard.widget.t3extblog.statisticChart', DoughnutChartWidget::class)
        ->arg('$dataProvider', new Reference(StatisticChartDataProvider::class))
        ->tag('dashboard.widget', [
            'identifier' => 't3extblogStatisticChart',
            'groupNames' => 't3extblog',
            'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.statisticChart.title',
            'description' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:widget.statisticChart.description',
            'iconIdentifier' => 'content-widget-chart-pie',
            'height' => 'medium',
        ]);
};
