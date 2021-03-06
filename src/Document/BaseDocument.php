<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2020 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CommonFeatures\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use CommonFeatures\Model\ModelInterface;
use CommonFeatures\Traits\SoftDeleteableDocument;

/**
 * @ODM\MappedSuperclass
 * @ODM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deleted_at", timeAware=false)
 */
abstract class BaseDocument implements ModelInterface
{

    use SoftDeleteableDocument;

    /**
     * @var \DateTime
     * @ODM\Field(name="created_at", type="date")
     * @JMS\Groups({"list", "details"})
     */
    protected $created_at;

    /**
     * @var \DateTime
     * @ODM\Field(name="updated_at", type="date")
     * @JMS\Groups({"list", "details"})
     */
    protected $updated_at;

    /**
     * @return string
     */
    abstract public function getId();

    /**
     * @ODM\PrePersist
     */
    public function prePersist()
    {
        $this->created_at = new \DateTime("now");
    }

    /**
     * @ODM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updated_at = new \DateTime("now");
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param array $data
     * @return mixed|void
     * @throws \ReflectionException
     */
    public function exchangeArray($data)
    {
        foreach ($data as $key => $val) {
            if (in_array($key, $this->getClassFields())) {
                $this->$key = $val;
            }
        }
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @return array|null
     * @throws \ReflectionException
     */
    public function toArray()
    {
        $data = array();
        foreach ($this->getClassFields() as $field) {
            $data[$field] = $this->$field;
        }
        return (count($data) > 0) ? $data : null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
         return (string) $this->getId();
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $created_at
     * @return BaseDocument
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updated_at
     * @return BaseDocument
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return get_class($this);
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getClassFields()
    {
        $reflection = new \ReflectionClass($this);
        $vars = $reflection->getDefaultProperties();
        $fields = [];
        foreach ($vars as $name => $val) {
            if (substr($name, 0, 1) !== '_') {
                $fields[] = $name;
            }
        }
        return $fields;
    }
}
