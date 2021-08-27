<?php
/**
 * This file is part of the SmartStock project
 * Copyright (c) 2021 OAS
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CommonFeatures\Listener;

use CommonFeatures\View\Model\PdfModel;
use CommonFeatures\View\Renderer\PdfRender;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\View\ViewEvent;

/**
 * Class PdfStrategy
 * @package CommonFeatures\Listener
 * @author Sionna Ouattara <sionnaouattara@gmail.com>
 */
class PdfStrategy implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var PdfRender
     */
    protected $renderer;

    /**
     * PdfStrategy constructor.
     * @param PdfRender $renderer
     */
    public function __construct(PdfRender $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Attach the aggregate to the specified event manager
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RENDERER, [$this, 'selectRenderer'], $priority);
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RESPONSE, [$this, 'injectResponse'], $priority);
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Detect if we should use the PdfRenderer based on model type
     *
     * @param ViewEvent $event
     * @return PdfRender|null
     */
    public function selectRenderer(ViewEvent $event)
    {
        $model = $event->getModel();

        if ($model instanceof PdfModel) {
            return $this->renderer;
        }
        return null;
    }

    /**
     * Inject the response with the PDF payload and appropriate Content-Type header
     *
     * @param ViewEvent $event
     * @return void
     */
    public function injectResponse(ViewEvent $event)
    {
        $renderer = $event->getRenderer();
        if ($renderer !== $this->renderer) {
            // Discovered renderer is not ours; do nothing
            return;
        }

        $result = $event->getResult();

        if (! is_string($result)) {
            // No output to display. Good bye!
            return;
        }

        $response = $event->getResponse();
        $response->setContent($result);
        $model   = $event->getModel();
        $options = $model->getOptions();
        $fileName        = $options['fileName'];
        $dispositionType = $options['display'];
        if (substr($fileName, -4) != '.pdf') {
            $fileName .= '.pdf';
        }
        $headerValue = sprintf('%s; filename="%s"', $dispositionType, $fileName);
        $response->getHeaders()->addHeaderLine('Content-Disposition', $headerValue);
        $response->getHeaders()->addHeaderLine('Content-Length', strlen($result));
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/pdf');
    }
}
