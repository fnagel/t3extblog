<?php

namespace FelixNagel\T3extblog\Validation\Validator;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Comment;
use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\CMS\Extbase\Validation\Exception\InvalidValidationOptionsException;

/**
 * Validator for the comment email field that respects,
 * if the email requirement is enabled or not.
 */
class CommentEmailValidator extends AbstractValidator
{
    /**
     * Check if $value is valid. If it is not valid, needs to add an error
     * to result.
     *
     * @param Comment $value The value to check
     */
    protected function isValid($value)
    {
        if (!$value instanceof Comment) {
            throw new InvalidValidationOptionsException('No valid comment given!', 1592253083);
        }

        if (empty($value->getEmail())) {
            if ((bool)$this->getConfiguration('blogsystem.comments.requireEmail')) {
                $error = new Error('Email address is required.', 1592252730);
                $this->result->forProperty('email')->addError($error);
            } elseif ((bool)$this->getConfiguration('blogsystem.comments.subscribeForComments') &&
                $value->getSubscribe()
            ) {
                $error = new Error('Email address is required for subscription.', 1592252730);
                $this->result->forProperty('email')->addError($error);
            }
        }
    }
}
