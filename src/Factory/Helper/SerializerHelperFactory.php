<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2021 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CommonFeatures\Factory\Helper;

use CommonFeatures\View\Helper\SerializerHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class SerializerHelperFactory
 * @package CommonFeatures\Factory\Helper
 *  @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class SerializerHelperFactory implements FactoryInterface
{

    /**
     * Create SerializerHelper
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return SerializerHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $serializer = $container->get('jms_serializer.serializer');
        return new SerializerHelper($serializer);
    }
}
