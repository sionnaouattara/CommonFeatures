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

namespace CommonFeatures\Factory\Options;

use Interop\Container\ContainerInterface;
use CommonFeatures\Options\ModuleOptions;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ModuleOptionsFactory
 * @package CommonFeatures\Factory\Options
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class ModuleOptionsFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ModuleOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        return new ModuleOptions(isset($config['common_features']) ? $config['common_features'] : []);
    }
}
