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

namespace CommonFeatures\Service;

/**
 * Interface LogEntryServiceInterface
 * @package CommonFeatures\Service
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
interface LogEntryServiceInterface extends AbstractServiceInterface
{
    /**
     * @param $resultset
     * @return array
     */
    public function resultWrapper($resultset);

    /**
     * Filter
     *
     * @param array $filters
     * @return mixed
     */
    public function filter(array $filters = null);
}
