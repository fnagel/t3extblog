<?php

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
     * The TYPO3_DB database connection.
     *
     * @var \TYPO3\CMS\Dbal\Database\DatabaseConnection|\TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $database;

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
        $this->database = $GLOBALS['TYPO3_DB'];
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

        $this->renderCommentUrlValidationSection();
        $this->renderPostMailsSentSection();
        $this->renderCommentMailsSentSection();

        $output .= $this->generateMessages();
        $output .= implode('<br>', $this->sectionArray);

        return $output;
    }

    /**
     * @return void
     */
    protected function renderCommentUrlValidationSection()
    {
        $key = 'comment_url_validation';
        if (GeneralUtility::_POST('migration') === $key) {
            $this->findCommentRecordsForUrlValidation();
        }

        $this->sectionArray[] = $this->renderForm(
            $key, 'Find existing comments with invalid website URLs'
        );
    }

    /**
     * @return void
     */
    protected function findCommentRecordsForUrlValidation()
    {
        $where = 'website != "" AND NOT (website LIKE "http%" OR website LIKE "https%")';
        $rows = $this->database->exec_SELECTgetRows('*', 'tx_t3blog_com', $where);

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
        if (GeneralUtility::_POST('migration') === $key) {
            $this->updatePostRecordsForMailsSent();
        }

        $this->sectionArray[] = $this->renderForm(
            $key, 'Set "mails_sent" flag for existing posts (use when updating to v2.1.0)'
        );
    }

    /**
     * @return void
     */
    protected function updatePostRecordsForMailsSent()
    {
        $this->database->exec_UPDATEquery('tx_t3blog_post', 'mails_sent IS NULL', array('mails_sent' => 1));

        $message = $this->database->sql_affected_rows() . ' posts have been updated';
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
        if (GeneralUtility::_POST('migration') === $key) {
            $this->updateCommentRecordsForMailsSent();
        }

        $this->sectionArray[] = $this->renderForm(
            $key, 'Set "mails_sent" flag for existing comments (use when migrating from EXT:t3blog)'
        );
    }

    /**
     * @return void
     */
    protected function updateCommentRecordsForMailsSent()
    {
        $this->database->exec_UPDATEquery('tx_t3blog_com', 'mails_sent IS NULL', array('mails_sent' => 1));

        $message = $this->database->sql_affected_rows().' comments have been updated';
        $this->messageArray[] = [FlashMessage::INFO, 'Comments updated', $message];
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
        return array_key_exists($field, $this->database->admin_get_fields($table));
    }

    /**
     * Generates output by using flash messages.
     *
     * @return string
     */
    protected function generateMessages()
    {
        $output = '';
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

            if (version_compare(TYPO3_branch, '8.6', '<')) {
                $output .= $flashMessage->getMessageAsMarkup();
            }
        }

        if (version_compare(TYPO3_branch, '8.6', '>=')) {
            /** @var \TYPO3\CMS\Core\Messaging\Renderer\FlashMessageRendererInterface $renderer */
            $renderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                \TYPO3\CMS\Core\Messaging\FlashMessageRendererResolver::class
            )->resolve();
            $output = $renderer->render($flashMessages);
        }

        return $output;
    }
}
