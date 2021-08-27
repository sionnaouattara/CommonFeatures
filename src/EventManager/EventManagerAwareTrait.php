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

use Laminas\EventManager;
use Laminas\ServiceManager;

/**
 * Trait EventManagerAwareTrait
 * @package CommonFeatures\EventManager
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
trait EventManagerAwareTrait
{
    use EventManager\EventManagerAwareTrait;

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * If the class implements ServiceManager, it adds the global dibber shared
     * event manager to it.
     *
     * @return EventManager\EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->events instanceof EventManager\EventManagerInterface) {
            $this->setEventManager(new EventManager\EventManager());
            if ($this instanceof ServiceManager\ServiceLocatorInterface) {
                $this->getEventManager()->setSharedManager($this->getServiceManager()->get('cf_event_manager'));
            }
        }
        return $this->events;
    }
}
