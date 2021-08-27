<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2021 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CommonFeatures\View\Helper;

use CommonFeatures\Service\Serializer;
use JMS\Serializer\SerializationContext;
use Laminas\View\Helper\AbstractHelper;

/**
 * Class Serializer
 * @package CommonFeatures\View
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class SerializerHelper extends AbstractHelper
{
    /**
     * @var array
     */
    protected $allowedFormats = [
        'json',
        'yml',
        'xml',
    ];

    /**
     * @var Serializer
     */
    protected $serializerManager;

    /**
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializerManager = $serializer;
    }

    /**
     * @param $object
     * @param string $format
     * @param SerializationContext|null $context
     * @return mixed|string
     */
    public function __invoke($object, $format = 'json', SerializationContext $context = null)
    {
        if (! in_array($format, $this->allowedFormats)) {
            $format = 'json';
        }

        return $this->serializerManager->getSerializer()->serialize($object, $format, $context);
    }
}
