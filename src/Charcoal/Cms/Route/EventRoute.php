<?php

namespace Charcoal\Cms\Route;

// PSR-7 (http messaging) dependencies
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

// Dependencies from `Pimple`
use \Pimple\Container;

// Dependency from 'charcoal-app'
use \Charcoal\App\Route\TemplateRoute;

/**
 * Event Route
 */
class EventRoute extends TemplateRoute
{

    /**
     * @var string $path
     */
    private $path;

    /**
     * @var \Charcoal\Cms\EventInterface $event
     */
    private $event;

    /**
     * @var string $objType
     */
    private $objType = 'charcoal/cms/event';

    /**
     * @param array|\ArrayAccess $data Class depdendencies.
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->path = ltrim($data['path'], '/');
    }

    /**
     * @param  Container $container A DI (Pimple) container.
     * @return boolean
     */
    public function pathResolvable(Container $container)
    {
        $event = $this->loadEventFromPath($container);
        return !!$event->id();
    }

    /**
     * @param  Container         $container A DI (Pimple) container.
     * @param  RequestInterface  $request   A PSR-7 compatible Request instance.
     * @param  ResponseInterface $response  A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function __invoke(Container $container, RequestInterface $request, ResponseInterface $response)
    {
        $config = $this->config();

        $event = $this->loadEventFromPath($container);

        $templateIdent      = $event->templateIdent();
        $templateController = $event->templateIdent();

        $templateFactory = $container['template/factory'];

        $template = $templateFactory->create($templateController);
        $template->setDependencies($container);

        // Set custom data from config.
        $template->setData($config['template_data']);
        $template->setEvent($event);

        $templateContent = $container['view']->render($templateIdent, $template);

        $response->write($templateContent);

        return $response;
    }

    /**
     * @param Container $container Pimple DI container.
     * @return \Charcoal\Cms\EventInterface
     */
    protected function loadEventFromPath(Container $container)
    {
        if (!$this->event) {
            $this->event = $container['model/factory']->create($this->objType);
            $this->event->loadFrom('slug', $this->path);
        }
        return $this->event;
    }
}
