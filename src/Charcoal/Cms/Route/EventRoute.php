<?php

namespace Charcoal\Cms\Route;

// From Pimple
use Pimple\Container;

// From PSR-7
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// From 'charcoal-translator'
use Charcoal\Translator\TranslatorAwareTrait;

// From 'charcoal-app'
use Charcoal\App\Route\TemplateRoute;

// From 'charcoal-cms'
use Charcoal\Cms\EventInterface;
use Charcoal\Object\RoutableInterface;

/**
 * Event Route Handler
 */
class EventRoute extends TemplateRoute
{
    use TranslatorAwareTrait;

    /**
     * URI path.
     *
     * @var string
     */
    private $path;

    /**
     * The event object matching the URI path.
     *
     * @var EventInterface|RoutableInterface
     */
    private $event;

    /**
     * The event model.
     *
     * @var string
     */
    private $objType = 'charcoal/cms/event';

    /**
     * @param array $data Class depdendencies.
     */
    public function __construct(array $data)
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
        return ($event instanceof EventInterface) && $event->id();
    }

    /**
     * @param  Container         $container A DI (Pimple) container.
     * @param  RequestInterface  $request   A PSR-7 compatible Request instance.
     * @param  ResponseInterface $response  A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function __invoke(
        Container $container,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $config = $this->config();

        $event = $this->loadEventFromPath($container);

        if (!$event) {
            return $response->withStatus(404);
        }

        $templateIdent      = $event->templateIdent();
        $templateController = $event->templateIdent();

        if (!$templateController) {
            return $response->withStatus(404);
        }

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
     * @todo   Add support for `@see setlocale()`; {@see GenericRoute::setLocale()}
     * @param  Container $container Pimple DI container.
     * @return EventInterface|null
     */
    protected function loadEventFromPath(Container $container)
    {
        if (!$this->event) {
            $config  = $this->config();
            $type   = (isset($config['obj_type']) ? $config['obj_type'] : $this->objType);
            $model  = $container['model/factory']->create($type);

            $langs = $container['translator']->availableLocales();
            $lang  = $model->loadFromL10n('slug', $this->path, $langs);
            $container['translator']->setLocale($lang);

            if ($model->id()) {
                $this->event = $model;
            }
        }

        return $this->event;
    }
}
