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
use Charcoal\Cms\NewsInterface;
use Charcoal\Object\RoutableInterface;

/**
 * News Route Handler
 */
class NewsRoute extends TemplateRoute
{
    use TranslatorAwareTrait;

    /**
     * URI path.
     *
     * @var string
     */
    private $path;

    /**
     * The news entry matching the URI path.
     *
     * @var NewsInterface|RoutableInterface
     */
    private $news;

    /**
     * The news entry model.
     *
     * @var string
     */
    private $objType = 'charcoal/cms/news';

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
        $news = $this->loadNewsFromPath($container);
        return !!$news->id();
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

        $news = $this->loadNewsFromPath($container);

        $templateIdent      = $news->templateIdent();
        $templateController = $news->templateIdent();

        if (!$templateController) {
            return $response->withStatus(404);
        }

        $templateFactory = $container['template/factory'];

        $template = $templateFactory->create($templateController);
        $template->init($request);

        // Set custom data from config.
        $template->setData($config['template_data']);
        $template->setNews($news);

        $templateContent = $container['view']->render($templateIdent, $template);

        $response->write($templateContent);

        return $response;
    }

    /**
     * @todo   Add support for `@see setlocale()`; {@see GenericRoute::setLocale()}
     * @param  Container $container Pimple DI container.
     * @return NewsInterface
     */
    protected function loadNewsFromPath(Container $container)
    {
        if (!$this->news) {
            $config  = $this->config();
            $objType = (isset($config['obj_type']) ? $config['obj_type'] : $this->objType);

            $this->news = $container['model/factory']->create($objType);

            $langs = $container['translator']->availableLocales();
            $lang  = $this->news->loadFromL10n('slug', $this->path, $langs);
            $container['translator']->setLocale($lang);
        }

        return $this->news;
    }
}
