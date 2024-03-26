<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Service\BlogNotificationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use FelixNagel\T3extblog\Domain\Model\Post;
use Psr\Http\Message\ResponseInterface;

/**
 * BackendPostController.
 */
class BackendPostController extends AbstractBackendController
{
    /**
     * Displays posts.
     */
    public function indexAction(int $page = 1): ResponseInterface
    {
        return $this->paginationHtmlResponse(
            'Post/Index',
            $this->postRepository->findByPage($this->pageId, false),
            $this->settings['backend']['posts']['paginate'],
            $page
        );
    }

    /**
     * Send post notification mails.
     */
    public function sendPostNotificationsAction(Post $post): ResponseInterface
    {
        /* @var $notificationService BlogNotificationService */
        $notificationService = GeneralUtility::makeInstance(BlogNotificationService::class);
        $amount = $notificationService->notifySubscribers($post);

        $this->addFlashMessage(LocalizationUtility::translate('module.post.emailsSent', 'T3extblog', [$amount]));

        return $this->redirect('index');
    }
}
