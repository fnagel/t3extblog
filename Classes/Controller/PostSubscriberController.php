<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository;
use FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository;
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * SubscriberController.
 */
class PostSubscriberController extends AbstractSubscriberController
{
    /**
     * subscriberRepository.
     *
     * @var PostSubscriberRepository
     */
    protected $subscriberRepository;

    /**
     * blogSubscriberRepository.
     */
    protected BlogSubscriberRepository $blogSubscriberRepository;

    /**
     * subscriber.
     *
     * @var PostSubscriber
     */
    protected $subscriber = null;

    /**
     * PostSubscriberController constructor.
     *
     */
    public function __construct(PostSubscriberRepository $subscriberRepository, BlogSubscriberRepository $blogSubscriberRepository)
    {
        $this->subscriberRepository = $subscriberRepository;
        $this->blogSubscriberRepository = $blogSubscriberRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeAction()
    {
        parent::initializeAction();

        $this->subscriptionSettings = $this->settings['subscriptionManager']['comment']['subscriber'];
    }

    /**
     * action list.
     */
    public function listAction()
    {
        $this->checkAuth();

        $this->redirect('list', 'Subscriber');
    }

    /**
     * action delete.
     *
     * @param PostSubscriber $subscriber
     */
    public function deleteAction($subscriber = null): ResponseInterface
    {
        parent::deleteAction($subscriber);

        return $this->htmlResponse();
    }

    /**
     * Finds existing subscriptions.
     *
     * @param PostSubscriber $subscriber
     */
    protected function findExistingSubscriptions($subscriber): QueryResultInterface
    {
        return $this->subscriberRepository->findExistingSubscriptions(
            $subscriber->getPostUid(),
            $subscriber->getEmail(),
            $subscriber->getUid()
        );
    }
}
