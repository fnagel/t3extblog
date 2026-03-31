<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\ViewHelpers\Frontend;

use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\ViewHelpers\Frontend\CommentAllowedViewHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;

#[CoversClass(CommentAllowedViewHelper::class)]
class CommentAllowedViewHelperTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    protected RenderingContextInterface $renderingContextMock;
    protected VariableProviderInterface $variableProviderMock;
    protected Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renderingContextMock = $this->createMock(RenderingContextInterface::class);
        $this->variableProviderMock = $this->createMock(VariableProviderInterface::class);
        $this->renderingContextMock->method('getVariableProvider')
            ->willReturn($this->variableProviderMock);

        $this->post = new Post();
        $this->post->setPublishDate(new \DateTime('-14 days'));
    }

    protected function getDefaultSettings(bool $commentsAllowed = true, string $allowedUntil = '+1 month'): array
    {
        return [
            'blogsystem' => [
                'comments' => [
                    'allowed' => $commentsAllowed ? 1 : 0,
                    'allowedUntil' => $allowedUntil,
                ],
            ],
        ];
    }

    #[Test]
    public function testVerdictReturnsFalseWhenSettingsDisallowComments(): void
    {
        $this->post->setAllowComments(Post::ALLOW_COMMENTS_EVERYONE);
        $this->variableProviderMock->method('get')->with('settings')
            ->willReturn($this->getDefaultSettings(false));

        $result = CommentAllowedViewHelper::verdict(['post' => $this->post], $this->renderingContextMock);

        self::assertFalse($result);
    }

    #[Test]
    public function testVerdictReturnsFalseWhenPostDisallowsComments(): void
    {
        $this->post->setAllowComments(Post::ALLOW_COMMENTS_NOBODY);
        $this->variableProviderMock->method('get')->with('settings')
            ->willReturn($this->getDefaultSettings());

        $result = CommentAllowedViewHelper::verdict(['post' => $this->post], $this->renderingContextMock);

        self::assertFalse($result);
    }

    #[Test]
    public function testVerdictReturnsTrueWhenEveryoneCanComment(): void
    {
        $this->post->setAllowComments(Post::ALLOW_COMMENTS_EVERYONE);
        $this->variableProviderMock->method('get')->with('settings')
            ->willReturn($this->getDefaultSettings());

        $result = CommentAllowedViewHelper::verdict(['post' => $this->post], $this->renderingContextMock);

        self::assertTrue($result);
    }

    #[Test]
    public function testVerdictReturnsFalseAfterAllowedUntil(): void
    {
        $this->post->setAllowComments(Post::ALLOW_COMMENTS_EVERYONE);
        $this->variableProviderMock->method('get')->with('settings')
            ->willReturn($this->getDefaultSettings(true, '+7 days'));

        $result = CommentAllowedViewHelper::verdict(['post' => $this->post], $this->renderingContextMock);

        self::assertFalse($result);
    }

    #[Test]
    public function testVerdictReturnsFalseWhenLoginRequiredAndUserNotLoggedIn(): void
    {
        // In unit test context, FrontendUtility::isUserLoggedIn() returns false
        // (no frontend user session available)
        $this->post->setAllowComments(Post::ALLOW_COMMENTS_LOGIN);
        $this->variableProviderMock->method('get')->with('settings')
            ->willReturn($this->getDefaultSettings());

        $result = CommentAllowedViewHelper::verdict(['post' => $this->post], $this->renderingContextMock);

        self::assertFalse($result);
    }
}
