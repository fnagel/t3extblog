<?php

use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ext_update.
 *
 * Performs update tasks for extension t3extblog
 */
class ext_update
{
    /**
     * The database connection.
     *
     * @var \TYPO3\CMS\Core\Database\ConnectionPool
     */
    protected $connectionPool;

    /**
     * Array of flash messages (params) array[][status,title,message].
     *
     * @var array
     */
    protected $messageArray = [];

    /**
     * Array of sections (HTML).
     *
     * @var array
     */
    protected $sectionArray = [];

    /**
     * Called by the extension manager to determine
     * if the update menu entry should by showed.
     *
     * @return bool
     */
    public function access()
    {
        return $GLOBALS['BE_USER']->isAdmin();
    }

    /**
     * Contructor.
     */
    public function __construct()
    {
        $this->connectionPool =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Database\ConnectionPool::class
        );
    }

    /**
     * Executes the update script.
     *
     * @return string
     */
    public function main()
    {
        $output = '';

        $message = 'These wizards will alter the database. Be careful in production environments!';
        $this->messageArray[] = [FlashMessage::WARNING, 'Database update wizards', $message];

        $this->renderCreateMissingPostSlugsSection();
        $this->renderCreateMissingCategorySlugsSection();
        $this->renderCommentUrlValidationSection();
        $this->renderPostMailsSentSection();
        $this->renderCommentMailsSentSection();
        $this->renderCommentAuthorOrEmailInvalidSection();

        $output .= $this->generateMessages();
        $output .= implode('<br>', $this->sectionArray);

        return $output;
    }

    /**
     * @return void
     */
    protected function renderCreateMissingPostSlugsSection()
    {
        if (!$this->isFieldAvailable('tx_t3blog_post', 'url_segment')) {
            return;
        }

        $key = 'create_missing_post_slugs';
        $this->sectionArray[] = $this->renderForm(
            $key,
            'Create missing post URL slugs (use when updating to version 5.0)'
        );
        if (GeneralUtility::_POST('migration') === $key) {
            $this->createMissingSlugs('tx_t3blog_post', 'title', 'url_segment', 'post records', 100);
        }
    }

    /**
     * @return void
     */
    protected function renderCreateMissingCategorySlugsSection()
    {
        if (!$this->isFieldAvailable('tx_t3blog_cat', 'url_segment')) {
            return;
        }

        $key = 'create_missing_category_slugs';
        $this->sectionArray[] = $this->renderForm(
            $key,
            'Create missing category URL slugs (use when updating to version 5.0)'
        );
        if (GeneralUtility::_POST('migration') === $key) {
            $this->createMissingSlugs('tx_t3blog_cat', 'catname', 'url_segment', 'category records');
        }
    }
    
    /**
     * @param string $table
     * @param string $field
     * @param string $slug
     * @param string $name
     * @param int $limit
     * @return void
     */
    protected function createMissingSlugs(
        $table = 'tx_t3blog_cat',
        $field = 'title',
        $slug = 'url_segment',
        $name = 'records',
        $limit = 50
    ) {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $constraint = $queryBuilder->expr()->eq($slug, $queryBuilder->createNamedParameter(''));

        $rows = $queryBuilder
            ->select('*')
            ->from($table)
            ->where($constraint)
            ->setMaxResults($limit)
            ->execute()
            ->fetchAll();

        if (count($rows) === 0) {
            $message = 'All '.$name.' have an valid URL slug!';
            $this->messageArray[] = [FlashMessage::OK, 'All '.$name.' valid!', $message];
            return;
        }

        $fieldConfig = $GLOBALS['TCA'][$table]['columns'][$slug]['config'];
        $slugService = GeneralUtility::makeInstance(SlugHelper::class, $table, $slug, $fieldConfig);

        foreach ($rows as $row) {
            $queryBuilder
                ->update($table)
                ->where(
                    $constraint,
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($row['uid'], \PDO::PARAM_INT))
                )
                ->set($slug, $slugService->generate($row, $row['pid']))
                ->execute();
        }

        $message = count($rows).' '.$name.' have been updated';
        $this->messageArray[] = [FlashMessage::INFO, 'Records updated', $message];
    }

    /**
     * @return void
     */
    protected function renderCommentUrlValidationSection()
    {
        $key = 'comment_url_validation';
        $this->sectionArray[] = $this->renderForm(
            $key,
            'Find existing comments with invalid website URLs'
        );
        if (GeneralUtility::_POST('migration') === $key) {
            $this->findCommentRecordsForUrlValidation();
        }
    }

    /**
     * @return void
     */
    protected function findCommentRecordsForUrlValidation()
    {
        $table = 'tx_t3blog_com';
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->neq('website', $queryBuilder->createNamedParameter('')),
                $queryBuilder->expr()->notLike(
                    'website',
                    $queryBuilder->createNamedParameter('http%')
                ),
                $queryBuilder->expr()->notLike(
                    'website',
                    $queryBuilder->createNamedParameter('https%')
                )
            );

        $rows = $queryBuilder->execute()->fetchAll();

        if (count($rows) === 0) {
            $message = 'All comment URLs look valid. Good job!';
            $this->messageArray[] = [FlashMessage::OK, 'All comments valid!', $message];
            return;
        }

        $commentList = '';
        foreach ($rows as $comment) {
            $commentList .= '<li>';
            $commentList .= 'uid=' . $comment['uid'] . ', pid=' . $comment['pid'];
            $commentList .= ', post=' . $comment['fk_post'] . ', deleted=' . $comment['deleted'];
            $commentList .= '</li>';
        }

        $this->sectionArray[] = '<ul>' . $commentList . '</ul>';

        $message = count($rows) . ' comments with invalid website URLs have been found.';
        $message .= ' Make sure to remove or fix those URLs!';
        $this->messageArray[] = [FlashMessage::ERROR, 'Invalid comments!', $message];
    }

    /**
     * @return void
     */
    protected function renderPostMailsSentSection()
    {
        if (!$this->isFieldAvailable('tx_t3blog_post', 'mails_sent')) {
            return;
        }

        $key = 'post_mails_sent';
        $this->sectionArray[] = $this->renderForm(
            $key,
            'Set "mails_sent" flag for existing posts (use when updating to v2.1.0)'
        );
        if (GeneralUtility::_POST('migration') === $key) {
            $this->updatePostRecordsForMailsSent();
        }
    }

    /**
     * @return void
     */
    protected function updatePostRecordsForMailsSent()
    {
        $table = 'tx_t3blog_post';
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $count = $queryBuilder
            ->update($table)
            ->where(
                $queryBuilder->expr()->isNull('mails_sent')
            )
            ->set('mails_sent', 1)
            ->execute();

        $message = $count . ' posts have been updated';
        $this->messageArray[] = [FlashMessage::INFO, 'Posts updated', $message];
    }

    /**
     * @return void
     */
    protected function renderCommentMailsSentSection()
    {
        if (!$this->isFieldAvailable('tx_t3blog_com', 'mails_sent')) {
            return;
        }

        $key = 'comment_mails_sent';
        $this->sectionArray[] = $this->renderForm(
            $key,
            'Set "mails_sent" flag for existing comments (use when migrating from EXT:t3blog)'
        );
        if (GeneralUtility::_POST('migration') === $key) {
            $this->updateCommentRecordsForMailsSent();
        }
    }

    /**
     * @return void
     */
    protected function updateCommentRecordsForMailsSent()
    {
        $table = 'tx_t3blog_com';
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $count = $queryBuilder
            ->update($table)
            ->where(
                $queryBuilder->expr()->isNull('mails_sent')
            )
            ->set('mails_sent', 1)
            ->execute();

        $message = $count.' comments have been updated';
        $this->messageArray[] = [FlashMessage::INFO, 'Comments updated', $message];
    }

    /**
     * @return void
     */
    protected function renderCommentAuthorOrEmailInvalidSection()
    {
        $key = 'comment_author_email_invalid';
        $this->sectionArray[] = $this->renderForm(
            $key,
            'Find existing comments with invalid author or email (use when migrating from EXT:t3blog)'
        );
        if (GeneralUtility::_POST('migration') === $key) {
            $this->findCommentAuthorOrEmailInvalid();
        }
    }

    /**
     * @return void
     */
    protected function findCommentAuthorOrEmailInvalid()
    {
        $table = 'tx_t3blog_com';
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('author', $queryBuilder->createNamedParameter('')),
                    $queryBuilder->expr()->eq('email', $queryBuilder->createNamedParameter(''))
                )
            );

        $rows = $queryBuilder->execute()->fetchAll();

        if (count($rows) === 0) {
            $message = 'All comment records look valid. Good job!';
            $this->messageArray[] = [FlashMessage::OK, 'All comments valid!', $message];
            return;
        }

        $commentList = '';
        foreach ($rows as $comment) {
            $commentList .= '<li>';
            $commentList .= 'uid=' . $comment['uid'] . ', pid=' . $comment['pid'];
            $commentList .= ', post=' . $comment['fk_post'] . ', deleted=' . $comment['deleted'];
            $commentList .= '</li>';
        }

        $this->sectionArray[] = '<ul>' . $commentList . '</ul>';

        $message = count($rows) . ' comments with invalid author or email have been found.';
        $message .= ' Make sure to fix those records!';
        $this->messageArray[] = [FlashMessage::ERROR, 'Invalid comments!', $message];
    }

    /**
     * @return string
     */
    protected function renderForm($key, $message)
    {
        return
            '<form action="'.GeneralUtility::getIndpEnv('REQUEST_URI').'" method="POST">
				<input type="hidden" name="migration" value="'.$key.'" />
				<button class="btn btn-default">'.$message.'</button>
			</form>';
    }

    /**
     * Check if a tale field is available.
     *
     * @param string $table
     * @param string $field
     *
     * @return bool
     */
    protected function isFieldAvailable($table, $field)
    {
        return array_key_exists(
            $field,
            $this->connectionPool->getConnectionForTable($table)->getSchemaManager()->listTableColumns($table)
        );
    }

    /**
     * Generates output by using flash messages.
     *
     * @return string
     */
    protected function generateMessages()
    {
        $flashMessages = [];

        foreach ($this->messageArray as $messageItem) {
            /** @var FlashMessage $flashMessage */
            $flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                FlashMessage::class,
                $messageItem[2],
                $messageItem[1],
                $messageItem[0]
            );
            $flashMessages[] = $flashMessage;
        }

        /** @var \TYPO3\CMS\Core\Messaging\Renderer\FlashMessageRendererInterface $renderer */
        $renderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Messaging\FlashMessageRendererResolver::class
        )->resolve();

        return $renderer->render($flashMessages);
    }
}
