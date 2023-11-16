<?php

namespace FelixNagel\T3extblog\Dashboard\Provider;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;

class ModuleButtonProvider implements ButtonProviderInterface
{
    use ProviderTrait;

    public function __construct(private string $title, private array $linkArguments, private string $target = '')
    {
    }

    protected function getModuleLink(int $id = null, array $arguments = []): string
    {
        $parameters = [];
        $route = '';
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        if ($id === null) {
            $pages = $this->getBlogPageUids();

            if (count($pages) === 1) {
                $id = (int)current($pages);
            }
        }

        if (is_int($id)) {
            $parameters['id'] = $id;
        }

        if ($arguments !== [] && array_key_exists('controller', $arguments)) {
            $route = '.'.$arguments['controller'];

            if (array_key_exists('action', $arguments)) {
                $route .= '_'.$arguments['action'];
            }
        }

        return (string)$uriBuilder->buildUriFromRoute('web_T3extblogBlogsystem'.$route, $parameters);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLink(): string
    {
        return $this->getModuleLink(null, $this->linkArguments);
    }

    public function getTarget(): string
    {
        // @extensionScannerIgnoreLine
        return $this->target;
    }
}
