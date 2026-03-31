<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Domain\Repository;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Category;
use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Domain\Repository\AbstractRepository;
use FelixNagel\T3extblog\Domain\Repository\CategoryRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(PostRepository::class)]
#[CoversClass(AbstractRepository::class)]
final class PostRepositoryTest extends AbstractRepositoryTestCase
{
    protected function getRepository(): PostRepository
    {
        return $this->get(PostRepository::class);
    }

    #[Test]
    public function findByUidReturnsExistingPost(): void
    {
        $post = $this->getRepository()->findByUid(2);

        self::assertInstanceOf(Post::class, $post);
        self::assertSame('Second Post', $post->getTitle());
    }

    #[Test]
    public function findByUidReturnsNullForNonExistentPost(): void
    {
        $post = $this->getRepository()->findByUid(999);

        self::assertNull($post);
    }

    #[Test]
    public function findByUidRespectsEnableFieldsByDefault(): void
    {
        // Post uid=3 is hidden
        $post = $this->getRepository()->findByUid(3);

        self::assertNull($post);
    }

    #[Test]
    public function findByUidReturnsHiddenPostWhenEnableFieldsIgnored(): void
    {
        $post = $this->getRepository()->findByUid(3, false);

        self::assertInstanceOf(Post::class, $post);
        self::assertSame('Draft Post', $post->getTitle());
    }

    #[Test]
    public function findDraftsReturnsDraftPosts(): void
    {
        $results = $this->getRepository()->findDrafts(pid: 1, until: '-99 years');

        self::assertCount(1, $results);
        self::assertSame('Draft Post', $results->getFirst()->getTitle());
    }

    #[Test]
    public function findByUidReturnsNullForDeletedPostWhenEnableFieldsIgnored(): void
    {
        $post = $this->getRepository()->findByUid(6, false);

        self::assertNull($post);
    }

    #[Test]
    public function findByTagReturnsMatchingPosts(): void
    {
        $results = $this->getRepository()->findByTag('security');

        self::assertCount(1, $results);
        self::assertSame('Tagged Post', $results->getFirst()->getTitle());
    }

    #[Test]
    public function findByTagReturnsMultiplePosts(): void
    {
        // 'php' appears in posts 1, 3 (hidden), 4, 5, 6 (deleted) → only visible: 1, 4, 5
        $results = $this->getRepository()->findByTag('php');

        self::assertCount(3, $results);
    }

    #[Test]
    public function findByTagReturnsEmptyResultForUnknownTag(): void
    {
        $results = $this->getRepository()->findByTag('nonexistent-tag');

        self::assertCount(0, $results);
    }

    #[Test]
    public function relatedPostsReturnPostsWithSameTags(): void
    {
        $firstPost = $this->getRepository()->findByUid(1);
        self::assertInstanceOf(Post::class, $firstPost);

        // First post has "php,typo3", second post has "typo3" - should be related
        $relatedPosts = $this->getRepository()->relatedPosts($firstPost);

        self::assertNotNull($relatedPosts);
        $titles = [];
        foreach ($relatedPosts as $post) {
            $titles[] = $post->getTitle();
        }
        self::assertContains('Second Post', $titles);
    }

    #[Test]
    public function relatedPostsDoesNotIncludePostItself(): void
    {
        $firstPost = $this->getRepository()->findByUid(1);
        self::assertInstanceOf(Post::class, $firstPost);

        $relatedPosts = $this->getRepository()->relatedPosts($firstPost);

        self::assertNotNull($relatedPosts);
        foreach ($relatedPosts as $post) {
            self::assertNotSame(1, $post->getUid());
        }
    }

    #[Test]
    public function findByFilterWithCategoryCallsFindByCategory(): void
    {
        $category = $this->get(CategoryRepository::class)->findByUid(1);
        self::assertInstanceOf(Category::class, $category);

        $result = $this->getRepository()->findByFilter($category);
        self::assertSame(2, $result->count());
    }
}
