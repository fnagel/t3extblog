<?php

namespace FelixNagel\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Felix Kopp <felix-source@phorax.com>
 *  (c) 2013-2018 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use FelixNagel\T3extblog\Service\BlogNotificationService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use FelixNagel\T3extblog\Domain\Model\Post;

/**
 * BackendPostController.
 */
class BackendPostController extends BackendBaseController
{
    /**
     * Main Backendmodule: displays posts and pending comments.
     */
    public function indexAction()
    {
        $this->view->assignMultiple([
            'posts' => $this->postRepository->findByPage($this->pageId, false),
        ]);
    }

    /**
     * Send post notification mails.
     *
     * @param Post $post
     */
    public function sendPostNotificationsAction(Post $post)
    {
        /* @var $notificationService BlogNotificationService */
        $notificationService = $this->objectManager->get(BlogNotificationService::class);
        $amount = $notificationService->notifySubscribers($post);

        $this->addFlashMessage(LocalizationUtility::translate('module.post.emailsSent', 'T3extblog', [$amount]));

        $this->redirect('index');
    }
}
