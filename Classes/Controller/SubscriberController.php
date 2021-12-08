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
use FelixNagel\T3extblog\Service\AuthenticationServiceInterface;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Http\ForwardResponse;

/**
 * SubscriberController.
 */
class SubscriberController extends AbstractController
{
    protected AuthenticationServiceInterface $authentication;

    protected BlogSubscriberRepository $blogSubscriberRepository;

    protected PostSubscriberRepository $postSubscriberRepository;

    /**
     * SubscriberController constructor.
     *
     * @param AuthenticationServiceInterface $authentication
     * @param BlogSubscriberRepository $blogSubscriberRepository
     * @param PostSubscriberRepository $postSubscriberRepository
     */
    public function __construct(
        AuthenticationServiceInterface $authentication,
        BlogSubscriberRepository $blogSubscriberRepository,
        PostSubscriberRepository $postSubscriberRepository
    ) {
        $this->authentication = $authentication;
        $this->blogSubscriberRepository = $blogSubscriberRepository;
        $this->postSubscriberRepository = $postSubscriberRepository;
    }

    /**
     * Displays a list of all posts a user subscribed to.
     */
    public function listAction(): ResponseInterface
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

        return $this->htmlResponse();
    }

    /**
     * Error action.
     */
    protected function errorAction(): ResponseInterface
    {
        if (!$this->hasFlashMessages()) {
            $this->addFlashMessageByKey('invalidAuth', FlashMessage::ERROR);
        }

        return $this->htmlResponse();
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
     * @param int $severity Severity code. One of the FlashMessage constants
     */
    protected function processErrorAction($message = 'invalidAuth', $severity = FlashMessage::ERROR)
    {
        // @extensionScannerIgnoreLine
        $this->authentication->logout();

        $this->addFlashMessageByKey($message, $severity);
        $this->redirect('error');
    }
}
