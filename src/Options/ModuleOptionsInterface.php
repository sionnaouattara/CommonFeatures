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

/**
 * Interface ModuleOptionsInterface
 * @package CommonFeatures\Options
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
interface ModuleOptionsInterface
{
    /**
     * @return string
     */
    public function getLogEntryEntityClass();

    /**
     * @param string $logEntryEntityClass
     * @return ModuleOptions
     */
    public function setLogEntryEntityClass($logEntryEntityClass);

    /**
     * @return string
     */
    public function getUserEntityClass();

    /**
     * @param string $userEntityClass
     * @return ModuleOptions
     */
    public function setUserEntityClass($userEntityClass);
}
