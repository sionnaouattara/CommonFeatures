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

namespace CommonFeatures\Provider;

use Interop\Container\ContainerInterface;

/**
 * Trait ContainerAwareTrait
 * @package CommonFeatures\Provider
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
trait ContainerAwareTrait
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
    * Retrieve container instance
    *
    * @return ContainerInterface
    */
    public function getContainer()
    {
        return $this->container;
    }

    /**
    * Set service manager instance
    *
    * @param ContainerInterface $container
    * @return $this
    */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }
}
