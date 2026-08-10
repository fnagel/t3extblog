<?php

use FelixNagel\T3extblog\EventListener\VisualEditor\ModifyNewContentElementWizardUrlParameter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Backend\Controller\Event\ModifyNewContentElementWizardItemsEvent;

return function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder) {
    if (!$containerBuilder->hasExtension('visual_editor')) {
        $containerBuilder->removeDefinition(ModifyNewContentElementWizardUrlParameter::class);
        $containerBuilder->removeDefinition(ModifyNewContentElementWizardItemsEvent::class);
    }
};
