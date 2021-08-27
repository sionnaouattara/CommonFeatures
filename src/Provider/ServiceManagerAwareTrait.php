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

use Laminas\ServiceManager\ServiceManager;

/**
 * Trait ServiceManagerAwareTrait
 * @package CommonFeatures\Provider
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
trait ServiceManagerAwareTrait
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
    * Retrieve service manager instance
    *
    * @return ServiceManager
    */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
    * Set service manager instance
    *
    * @param ServiceManager $serviceManager
    * @return ServiceManagerAwareTrait
    */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
