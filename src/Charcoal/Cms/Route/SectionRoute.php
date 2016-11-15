<?php

namespace Charcoal\Cms\Route;

use \Pimple\Container;

// From PSR-7 (HTTP Messaging)
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

// From 'charcoal-app'
use \Charcoal\App\Route\TemplateRoute;

// From 'charcoal-translation'
use \Charcoal\Translation\TranslationConfig;

/**
 * Section Route Handler
 */
class SectionRoute extends TemplateRoute
{
    /**
     * URI path.
     *
     * @var string
     */
    private $path;

    /**
     * The section object matching the URI path.
     *
     * @var \Charcoal\Cms\SectionInterface|\Charcoal\Object\RoutableInterface
     */
    private $section;

    /**
     * The section model.
     *
     * @var string
     */
    private $objType = 'charcoal/cms/section';

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
        $section = $this->loadSectionFromPath($container);
        return !!$section->id();
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

        $section = $this->loadSectionFromPath($container);

        $templateIdent      = (string)$section->templateIdent();
        $templateController = (string)$section->templateIdent();

        if (!$templateController) {
            return $response->withStatus(404);
        }

        $templateFactory = $container['template/factory'];
        $templateFactory->setDefaultClass($config['default_controller']);

        $template = $templateFactory->create($templateController);
        $template->init($request);

        // Set custom data from config.
        $template->setData($config['template_data']);
        $template->setSection($section);

        $templateContent = $container['view']->render($templateIdent, $template);

        $response->write($templateContent);

        return $response;
    }

    /**
     * @param Container $container Pimple DI container.
     * @return \Charcoal\Cms\SectionInterface
     */
    protected function loadSectionFromPath(Container $container)
    {
        if (!$this->section) {
            $config  = $this->config();
            $objType = (isset($config['obj_type']) ? $config['obj_type'] : $this->objType);

            $this->section = $container['model/factory']->create($objType);

            $translator = TranslationConfig::instance();

            $langs = $translator->availableLanguages();
            $lang  = $this->section->loadFromL10n('slug', $this->path, $langs);
            $translator->setCurrentLanguage($lang);
        }

        return $this->section;
    }
}
