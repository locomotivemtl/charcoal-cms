<?php

namespace Charcoal\Cms\Route;

use Exception;

// From Pimple
use Pimple\Container;

// From PSR-7
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// From 'charcoal-translator'
use Charcoal\Translator\TranslatorAwareTrait;

// From 'charcoal-app'
use Charcoal\App\Route\TemplateRoute;

// From 'charcoal-object'
use Charcoal\Object\RoutableInterface;

// From 'charcoal-cms'
use Charcoal\Cms\EventInterface;

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
        if ($event === null) {
            return $response->withStatus(404);
        }

        $templateIdent      = (string)$event['templateIdent'];
        $templateController = (string)$event['templateIdent'];

        if (!$templateController) {
            $container['logger']->warning(sprintf(
                '[%s] Missing template controller on model [%s] for ID [%s]',
                get_class($this),
                get_class($event),
                $event['id']
            ));
            return $response->withStatus(500);
        }

        $templateFactory = $container['template/factory'];

        $template = $templateFactory->create($templateController);
        $template->init($request);

        // Set custom data from config.
        $template->setData($config['template_data']);
        $template->setEvent($event);

        $templateContent = $container['view']->render($templateIdent, $template);
        if ($templateContent === $templateIdent || $templateContent === '') {
            $container['logger']->warning(sprintf(
                '[%s] Missing or bad template identifier on model [%s] for ID [%s]',
                get_class($this),
                get_class($event),
                $templateIdent
            ));
            return $response->withStatus(500);
        }

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
        if ($this->event === null) {
            $config  = $this->config();
            $objType = (isset($config['obj_type']) ? $config['obj_type'] : $this->objType);

            try {
                $model = $container['model/factory']->create($objType);
                $langs = $container['translator']->availableLocales();
                $lang  = $model->loadFromL10n('slug', $this->path, $langs);

                if ($lang) {
                    $container['translator']->setLocale($lang);
                }

                if ($model->id()) {
                    $this->event = $model;
                    return $model;
                }
            } catch (Exception $e) {
                $container['logger']->debug(sprintf(
                    '[%s] Unable to load model [%s] for path [%s]',
                    get_class($this),
                    get_class($model),
                    $this->path
                ));
            }

            $this->event = false;
        }

        return $this->event ?: null;
    }
}
