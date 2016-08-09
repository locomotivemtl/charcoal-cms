<?php

namespace Charcoal\Cms\Route;

// From PSR-7 (HTTP Messaging)
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

// From Pimple
use \Pimple\Container;

// From 'charcoal-app'
use \Charcoal\App\Route\TemplateRoute;

/**
 * Event Route Handler
 */
class EventRoute extends TemplateRoute
{
    /**
     * URI path.
     *
     * @var string
     */
    private $path;

    /**
     * The event object matching the URI path.
     *
     * @var \Charcoal\Cms\EventInterface|\Charcoal\Object\RoutableInterface
     */
    private $event;

    /**
     * The event model.
     *
     * @var string
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
     * Determine if the URI path resolves to an object.
     *
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
        $template->init($request);

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
            $config  = $this->config();
            $objType = (isset($config['obj_type']) ? $config['obj_type'] : $this->objType);

            $this->event = $container['model/factory']->create($objType);

            $translator = TranslationConfig::instance();

            $langs = $translator->availableLanguages();
            $lang  = $this->event->loadFromL10n('slug', $this->path, $langs);
            $translator->setCurrentLanguage($lang);
        }

        return $this->event;
    }
}
