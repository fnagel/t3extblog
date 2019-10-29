<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Messaging\FlashMessage;

/**
 * SubscriberController.
 */
class SubscriberController extends AbstractController
{
    /**
     * feUserService.
     *
     * @var \FelixNagel\T3extblog\Service\AuthenticationServiceInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $authentication;

    /**
     * @var \FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $blogSubscriberRepository;

    /**
     * @var \FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $postSubscriberRepository;

    /**
     * Displays a list of all posts a user subscribed to.
     */
    public function listAction()
    {
        if (!$this->authentication->isValid()) {
            $this->forward('list', 'PostSubscriber');
        }

        $email = $this->authentication->getEmail();

        $postSubscriber = $this->postSubscriberRepository->findByEmail($email);
        $blogSubscriber = $this->blogSubscriberRepository->findOneByEmail($email);

        $this->view->assign('email', $email);
        $this->view->assign('postSubscriber', $postSubscriber);
        $this->view->assign('blogSubscriber', $blogSubscriber);
    }

    /**
     * Error action.
     */
    public function errorAction()
    {
        if (!$this->hasFlashMessages()) {
            $this->addFlashMessageByKey('invalidAuth', FlashMessage::ERROR);
        }
    }

    /**
     * Invalidates the auth and redirects user.
     */
    public function logoutAction()
    {
        $this->processErrorAction('logout', FlashMessage::INFO);
    }

    /**
     * Redirects user when no auth was possible.
     *
     * @param string $message  Flash message key
     * @param int    $severity Severity code. One of the FlashMessage constants
     */
    protected function processErrorAction($message = 'invalidAuth', $severity = FlashMessage::ERROR)
    {
        $this->authentication->logout();

        $this->addFlashMessageByKey($message, $severity);
        $this->redirect('error');
    }
}
