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
 * News Route
 */
class NewsRoute extends TemplateRoute
{

    /**
     * @var string $path
     */
    private $path;

    /**
     * @var \Charcoal\Cms\NewsInterface $news
     */
    private $news;

    /**
     * @var string $objType
     */
    private $objType = 'charcoal/cms/news';

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
        $news = $this->loadNewsFromPath($container);
        return !!$news->id();
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

        $news = $this->loadNewsFromPath($container);

        $templateIdent      = $news->templateIdent();
        $templateController = $news->templateIdent();

        $templateFactory = $container['template/factory'];

        $template = $templateFactory->create($templateController);
        $template->setDependencies($container);

        // Set custom data from config.
        $template->setData($config['template_data']);
        $template->setNews($news);

        $templateContent = $container['view']->render($templateIdent, $template);

        $response->write($templateContent);

        return $response;
    }

    /**
     * @param Container $container Pimple DI container.
     * @return \Charcoal\Cms\NewsInterface
     */
    protected function loadNewsFromPath(Container $container)
    {
        if (!$this->news) {
            $this->news = $container['model/factory']->create($this->objType);
            $this->news->loadFrom('slug_fr', $this->path);
        }
        return $this->news;
    }
}
