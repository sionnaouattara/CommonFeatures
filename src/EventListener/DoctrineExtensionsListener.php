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

namespace CommonFeatures\EventListener;

use Doctrine\Common\EventArgs;
use Gedmo\Blameable\BlameableListener;
use Gedmo\Loggable\LoggableListener;
use Laminas\Authentication\AuthenticationServiceInterface;

/**
 * Class DoctrineExtensionsListener
 * @package CommonFeatures\EventListener
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class DoctrineExtensionsListener
{

    /**
     * @var BlameableListener
     */
    protected $blameableListener = null;

    /**
     * @var LoggableListener
     */
    protected $loggableListener = null;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authenticationService;


    /**
     * @param AuthenticationServiceInterface $authenticationService
     */
    public function __construct(AuthenticationServiceInterface $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param EventArgs $event
     * @return void
     */
    public function preFlush(EventArgs $event)
    {
        $this->onEvent($event);
    }

    /**
     * @param EventArgs $event
     * @return void
     */
    public function onFlush(EventArgs $event)
    {
        $this->onEvent($event);
    }

    /**
     * @param EventArgs $event
     * @return void
     */
    protected function onEvent(EventArgs $event)
    {
        try {
            if ($this->authenticationService->hasIdentity()) {
                return;
            }

            $identity = $this->authenticationService->getIdentity();

            if (method_exists($event, 'getEntityManager')) {
                $objectManager = $event->getEntityManager();
            } else {
                $objectManager = $event->getDocumentManager();
            }

            $evtManager = $objectManager->getEventManager();
            foreach ($evtManager->getListeners() as $listeners) {
                foreach ($listeners as $listener) {
                    if ($listener instanceof BlameableListener) {
                        $this->blameableListener = $listener;
                        continue;
                    }
                    if ($listener instanceof LoggableListener) {
                        $this->loggableListener = $listener;
                        continue;
                    }
                }
            }

            if (null !== $this->blameableListener) {
                $this->blameableListener->setUserValue($identity);
                $evtManager->addEventSubscriber($this->blameableListener);
            }

            if (null !== $this->loggableListener) {
                $this->loggableListener->setUsername($identity);
                $evtManager->addEventSubscriber($this->loggableListener);
            }
        } catch (\Exception $ex) {
        }

        return;
    }
}
