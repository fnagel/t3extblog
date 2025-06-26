<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation as Extbase;

/**
 * PostSubscriber.
 */
class PostSubscriber extends AbstractSubscriber
{
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected ?string $name = null;

    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected ?int $postUid  = null;

    #[Lazy]
    protected ?Post $post = null;

    /**
     * @var ?ObjectStorage<Comment>
     */
    #[Lazy]
    protected ?ObjectStorage $postComments = null;

    /**
     * @var ObjectStorage<Comment>
     */
    #[Lazy]
    protected ?ObjectStorage $postPendingComments = null;

    public function __construct(int $postUid)
    {
        $this->postUid = $postUid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPostUid(): int
    {
        return $this->postUid;
    }

    public function getPost(): Post
    {
        if ($this->post === null) {
            $this->post = $this->getPostRepository()->findByLocalizedUid($this->postUid);
        }

        return $this->post;
    }

    /**
     * @return ObjectStorage<Comment>
     */
    public function getPostComments(): ObjectStorage
    {
        if ($this->postComments === null) {
            $postComments = $this->getCommentRepository()->findValidByEmailAndPostId($this->email, $this->postUid);

            $this->postComments = new ObjectStorage();
            foreach ($postComments as $comment) {
                $this->postComments->attach($comment);
            }
        }

        return $this->postComments;
    }

    /**
     * Returns the post pending comments.
     *
     * @return ObjectStorage<Comment> $comments
     */
    public function getPostPendingComments(): ObjectStorage
    {
        if ($this->postPendingComments === null) {
            $postPendingComments = $this->getCommentRepository()->findPendingByEmailAndPostId($this->email, $this->postUid);

            $this->postPendingComments = new ObjectStorage();
            foreach ($postPendingComments as $comment) {
                $this->postPendingComments->attach($comment);
            }
        }

        return $this->postPendingComments;
    }

    public function setPostUid(int $postUid): void
    {
        $this->postUid = $postUid;
    }
}
