<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2021 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CommonFeatures\Service;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

/**
 * Class Serializer
 * @package CommonFeatures\Service
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class Serializer
{
    /**
     * @var \JMS\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @var array
     */
    protected $serializerFormat = ['json', 'xml', 'yml'];

    /**
     * @return SerializerInterface
     */
    public function __invoke()
    {
        return $this->getSerializer();
    }

    /**
     * @return \JMS\Serializer\SerializerInterface
     */
    public function getSerializer()
    {
        if (! $this->serializer) {
            $this->setSerializer();
        }
        return $this->serializer;
    }

    /**
     * @param mixed $serializer
     * @return $this
     */
    public function setSerializer($serializer = null)
    {
        if (! $serializer) {
            $serializer = \JMS\Serializer\SerializerBuilder::create()
                ->setPropertyNamingStrategy(
                    new \JMS\Serializer\Naming\SerializedNameAnnotationStrategy(
                        new \JMS\Serializer\Naming\IdenticalPropertyNamingStrategy()
                    )
                )
                ->setCacheDir(getcwd() . '/data/JMSSerializer')
                ->build();
        }
        $this->serializer = $serializer;
        return $this;
    }

    /**
     * @param array| $entity
     * @param string $format
     * @param null $groups
     * @return mixed|string
     * @throws \Exception
     */
    public function serialize($entity, $format = 'json', $groups = null)
    {
        if (! in_array($format, $this->serializerFormat)) {
            throw new Exception\InvalidArgumentException('Format ' . $format . ' is not valid');
        }
        $serializer = $this->getSerializer();

        if (! $serializer instanceof SerializerInterface) {
            throw new \Exception('Serializer service must be instance of ' .
                get_class(SerializerInterface::class));
        }

        $context = SerializationContext::create()->enableMaxDepthChecks();
        $groups = (array) $groups;
        if (count($groups)) {
            $context->setGroups($groups);
        }
        $serialize  = $serializer->serialize($entity, $format, $context);

        if ($format === 'json') {
            $serialize = json_decode($serialize);
        }
        return $serialize;
    }
}
