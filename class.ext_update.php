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
     * @var \TYPO3\CMS\Dbal\Database\DatabaseConnection
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

        $this->renderPostSection();
        $this->renderCommentSection();

        $output .= $this->generateMessages();
        $output .= implode('<br>', $this->sectionArray);

        return $output;
    }

    /**
     */
    protected function renderPostSection()
    {
        if (!$this->isFieldAvailable('tx_t3blog_post', 'mails_sent')) {
            return;
        }

        $key = 'post';
        if (GeneralUtility::_POST('migration') === $key) {
            $this->updatePostRecords();
        }

        $this->sectionArray[] = $this->renderForm(
            $key, 'Set "mails_sent" flag for existing posts (use when updating to v2.1.0)'
        );
    }

    /**
     */
    protected function updatePostRecords()
    {
        $this->database->exec_UPDATEquery('tx_t3blog_post', 'mails_sent IS NULL', array('mails_sent' => 1));

        $message = $this->database->sql_affected_rows().' posts have been updated';
        $this->messageArray[] = [FlashMessage::INFO, 'Posts updated', $message];
    }

    /**
     */
    protected function renderCommentSection()
    {
        if (!$this->isFieldAvailable('tx_t3blog_com', 'mails_sent')) {
            return;
        }

        $key = 'comment';
        if (GeneralUtility::_POST('migration') === $key) {
            $this->updateCommentRecords();
        }

        $this->sectionArray[] = $this->renderForm(
            $key, 'Set "mails_sent" flag for existing comments (use when migrating from EXT:t3blog)'
        );
    }

    /**
     */
    protected function updateCommentRecords()
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
				<button class="btn">'.$message.'</button>
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

        foreach ($this->messageArray as $messageItem) {
            /** @var \TYPO3\CMS\Core\Messaging\FlashMessage $flashMessage */
            $flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
                $messageItem[2],
                $messageItem[1],
                $messageItem[0]
            );

            $output .= $flashMessage->render();
        }

        return $output;
    }
}
