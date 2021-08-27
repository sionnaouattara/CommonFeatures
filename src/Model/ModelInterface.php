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

namespace CommonFeatures\Model;

/**
 * Interface ModelInterface
 * @package CommonFeatures\Model
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
interface ModelInterface
{
    public function prePersist();

    public function preUpdate();

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt();

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt();

    /**
     * @param array $data
     * @return mixed
     */
    public function exchangeArray($data);

    /**
     * @return array
     */
    public function getArrayCopy();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return string
     */
    public function __toString();

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return string
     */
    public function getClassName();

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getClassFields();
}
