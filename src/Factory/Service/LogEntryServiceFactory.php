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

namespace CommonFeatures\Factory\Service;

use Interop\Container\ContainerInterface;
use CommonFeatures\Service\LogEntryService;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;


/**
 * Class LogEntryServiceFactory
 * @package CommonFeatures\Factory\Service
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class LogEntryServiceFactory implements FactoryInterface
{
    /**
     * Create LogEntryService
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $om = $container->has('doctrine.entitymanager.orm_default') ?
            $container->get('doctrine.entitymanager.orm_default') : $container->get('doctrine.documentmanager.odm_default');
        $options = $container->get('CommonFeatures\Options\ModuleOptions');
        return new LogEntryService($container, $om, $options);
    }
}
