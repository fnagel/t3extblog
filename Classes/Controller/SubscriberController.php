<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity as Message;
use FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository;
use FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository;
use FelixNagel\T3extblog\Service\AuthenticationServiceInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * SubscriberController.
 */
class SubscriberController extends AbstractController
{
    public function __construct(
        protected AuthenticationServiceInterface $authentication,
        protected BlogSubscriberRepository $blogSubscriberRepository,
        protected PostSubscriberRepository $postSubscriberRepository
    ) {
    }

    /**
     * Displays a list of all posts a user subscribed to.
     */
    public function listAction(): ResponseInterface
    {
        if (!$this->authentication->isValid()) {
            return (new ForwardResponse('list'))->withControllerName('PostSubscriber');
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
     * Error action (with actual template so no parent class call!).
     *
     * @todo Test this in TYPO3 v12!
     */
    protected function errorAction(): ResponseInterface
    {
        if (!$this->hasFlashMessages()) {
            $this->addFlashMessageByKey('invalidAuth', Message::ERROR);
        }

        return $this->htmlResponse()->withStatus(400);
    }

    /**
     * Invalidates the auth and redirects user.
     */
    public function logoutAction(): ResponseInterface
    {
        return $this->processErrorAction('logout', Message::INFO);
    }

    /**
     * Redirects user when no auth was possible.
     *
     * @param string $message Flash message key
     * @param int $severity Severity code. One of the FlashMessage constants
     */
    protected function processErrorAction(string $message = 'invalidAuth', ?int $severity = null): ResponseInterface
    {
        // @extensionScannerIgnoreLine
        $this->authentication->logout();

        $severity = is_null($severity) ? Message::ERROR : $severity;
        $this->addFlashMessageByKey($message, $severity);

        return $this->errorAction();
    }
}
