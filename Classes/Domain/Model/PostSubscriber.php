<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation as Extbase;

/**
 * PostSubscriber.
 */
class PostSubscriber extends AbstractSubscriber
{
    /**
     * name.
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $name;

    /**
     * postUid.
     *
     * @var int
     * @Extbase\Validate("NotEmpty")
     */
    protected $postUid;

    /**
     * post.
     *
     * @var \FelixNagel\T3extblog\Domain\Model\Post
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $post = null;

    /**
     * comments.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Comment>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $postComments = null;

    /**
     * comments.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Comment>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $postPendingComments = null;

    /**
     * __construct.
     *
     * @param int $postUid
     */
    public function __construct($postUid)
    {
        $this->postUid = $postUid;
    }

    /**
     * Returns the name.
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the postUid.
     *
     * @return int $postUid
     */
    public function getPostUid()
    {
        return $this->postUid;
    }

    /**
     * Returns the post.
     *
     * @return Post $post
     */
    public function getPost()
    {
        if ($this->post === null) {
            $this->post = $this->getPostRepository()->findByLocalizedUid($this->postUid);
        }

        return $this->post;
    }

    /**
     * Returns the post comments.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Comment> $comments
     */
    public function getPostComments()
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
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Comment> $comments
     */
    public function getPostPendingComments()
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

    /**
     * Sets the postUid.
     *
     * @param int $postUid
     */
    public function setPostUid($postUid)
    {
        $this->postUid = $postUid;
    }
}
