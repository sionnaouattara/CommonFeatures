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

namespace CommonFeatures\EventManager;

use Laminas\EventManager\ResponseCollection;

/**
 * Trait TriggerEventTrait
 * @package CommonFeatures\EventManager
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
trait TriggerEventTrait
{
    /**
     * Trigger an event more easily :
     * - $target is $this by default
     *
     * @param  string $event
     * @param  array|object $argv
     * @param  object|string $target
     * @param  null|callable $callback
     * @return ResponseCollection
     */
    public function triggerEvent($event, $argv = array(), $target = null, $callback = null)
    {
        if (! method_exists($this, 'getEventManager')) {
            throw new Exception\InvalidArgumentException(
                'CommonFeatures\EventManager\TriggerEventTrait requires the class that uses it to implement 
                Laminas\EventManager\EventManagerAwareInterface'
            );
        }

        if (is_null($target)) {
            $target = $this;
        }

        return $this->getEventManager()->trigger($event, $target, $argv, $callback);
    }
}
