<?php

namespace FelixNagel\T3extblog\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * PostSubscriber.
 */
class PostSubscriber extends AbstractSubscriber
{
    /**
     * name.
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate NotEmpty
     */
    protected $name;

    /**
     * postUid.
     *
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate NotEmpty
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
