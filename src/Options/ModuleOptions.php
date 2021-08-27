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

namespace CommonFeatures\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 * @package CommonFeatures\Options
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class ModuleOptions extends AbstractOptions implements ModuleOptionsInterface
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

    /**
     * @var string
     */
    protected $logEntryEntityClass = 'CommonFeatures\Entity\LogEntry';

    /**
     * @var string
     */
    protected $userEntityClass = 'Identity\Entity\User';

    /**
     * @return string
     */
    public function getLogEntryEntityClass()
    {
        return $this->logEntryEntityClass;
    }

    /**
     * @param string $logEntryEntityClass
     * @return ModuleOptions
     */
    public function setLogEntryEntityClass($logEntryEntityClass)
    {
        $this->logEntryEntityClass = $logEntryEntityClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserEntityClass()
    {
        return $this->userEntityClass;
    }

    /**
     * @param string $userEntityClass
     * @return ModuleOptions
     */
    public function setUserEntityClass($userEntityClass)
    {
        $this->userEntityClass = $userEntityClass;
        return $this;
    }
}
