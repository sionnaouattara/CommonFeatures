<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2020 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CommonFeatures;

use CommonFeatures\EventListener\DoctrineExtensionsListener;
use CommonFeatures\EventManager\DoctrineEvents;
use Doctrine\Persistence\ObjectManager;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements
    ConfigProviderInterface,
    BootstrapListenerInterface
{
    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return void
     */
    public function onBootstrap(EventInterface $e)
    {
        $sm = $e->getTarget()->getServiceManager();

        $objectManager = null;

        if ($sm->has('cf_auth_service')) {
            if ($sm->has('doctrine.entitymanager.orm_default')) {
                $objectManager = $sm->get('doctrine.entitymanager.orm_default');
            } elseif ($sm->has('doctrine.documentmanager.odm_default')) {
                $objectManager = $sm->get('doctrine.documentmanager.odm_default');
            }

            /* @var $objectManager ObjectManager */
            if ($objectManager instanceof ObjectManager) {
                $dem = $objectManager->getEventManager();
                $dem->addEventListener(
                    array(DoctrineEvents::PRE_FLUSH),
                    new DoctrineExtensionsListener($sm)
                );
            }
        }
    }
}
