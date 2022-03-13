<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Backend\Avatar\AvatarProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Get avatar for backend user.
 */
class BackendUserAvatarViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments()
    {
        $this->registerArgument('uid', 'string', 'Uid of the backend user');
        $this->registerArgument('size', 'int', 'Width of the avatar image');
        $this->registerArgument('default', 'string', 'Default image');
    }

    /**
     * Render the avatar image.
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $uid = $arguments['uid'];
        $size = $arguments['size'];
        $default = $arguments['default'];
        $url = self::getAvatarUrl($uid, $size);

        if ($url !== null) {
            return $url;
        }

        return self::noAvatarFound($default);
    }

    /**
     * Get avatar url using TYPO3 avatar provider.
     *
     *
     */
    protected static function getAvatarUrl(int $uid, int $size): ?string
    {
        $backendUser = BackendUtility::getRecord('be_users', (int) $uid);

        foreach (self::getAvatarProviders() as $provider) {
            /* @var $provider AvatarProviderInterface */
            $avatarImage = $provider->getImage($backendUser, $size);

            if ($avatarImage !== null) {
                return $avatarImage->getUrl(true);
            }
        }

        return null;
    }

    /**
     * Taken from \TYPO3\CMS\Backend\Backend\Avatar\Avatar::validateSortAndInitiateAvatarProviders
     */
    protected static function getAvatarProviders()
    {
        $avatarProviders = [];
        $providers = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['avatarProviders'];

        $orderedProviders = GeneralUtility::makeInstance(DependencyOrderingService::class)
            ->orderByDependencies($providers);

        foreach ($orderedProviders as $configuration) {
            $avatarProviders[] = GeneralUtility::makeInstance($configuration['provider']);
        }

        return $avatarProviders;
    }

    /**
     * Called when no user avatar has been found.
     *
     * @param string $default Blank gif als fallback
     *
     */
    protected static function noAvatarFound(string $default = null): string
    {
        if ($default === null || strlen(trim($default)) < 10) {
            $default = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
        }

        return $default;
    }
}
