<?php

namespace Charcoal\Cms;

use ArrayIterator;
use RuntimeException;

// From 'psr/http-message'
use Psr\Http\Message\UriInterface;

// From 'pimple/pimple'
use Pimple\Container;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-translator'
use Charcoal\Translator\TranslatorAwareTrait;

// From 'charcoal-app'
use Charcoal\App\AppConfig;
use Charcoal\App\DebugAwareTrait;
use Charcoal\App\Template\AbstractTemplate;

// From 'charcoal-core'
use Charcoal\Model\ModelFactoryTrait;

// From 'charcoal-cms'
use Charcoal\Cms\MetatagInterface;
use Charcoal\Cms\Support\ContextualTemplateTrait;
use Charcoal\Cms\Support\DocumentTrait;
use Charcoal\Cms\Support\LocaleAwareTrait;

/**
 * Hypertext Template Controller
 *
 * This class acts as an enhancer to the basic abstract template.
 */
abstract class AbstractWebTemplate extends AbstractTemplate
{
    use ContextualTemplateTrait;
    use DebugAwareTrait;
    use DocumentTrait;
    use LocaleAwareTrait;
    use ModelFactoryTrait;
    use TranslatorAwareTrait;

    /**
     * The default image for social media sharing.
     *
     * @var string
     */
    const DEFAULT_SOCIAL_MEDIA_IMAGE = '';

    /**
     * The application's configuration container.
     *
     * @var AppConfig
     */
    protected $appConfig;

    /**
     * The base URI.
     *
     * @var UriInterface|null
     */
    protected $baseUrl;

    /**
     * Additional SEO metadata.
     *
     * @var array
     */
    private $seoMetadata = [];

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setAppConfig($container['config']);
        $this->setBaseUrl($container['base-url']);
        $this->setDebug($container['debug']);
        $this->setModelFactory($container['model/factory']);
        $this->setTranslator($container['translator']);
        $this->setLocales($this->translator()->locales());

        $metatags = $this->appConfig('cms.metatags');
        if (is_array($metatags)) {
            $this->setSeoMetadata($metatags);
        }
    }

    /**
     * Retrieve the title of the page (the context).
     *
     * @return string|null
     */
    public function title()
    {
        $context = $this->contextObject();

        if ($context && isset($context['title'])) {
            return $context['title'];
        }

        return '';
    }

    /**
     * Retrieve the current URI of the context.
     *
     * @return \Psr\Http\Message\UriInterface|null
     */
    public function currentUrl()
    {
        $context = $this->contextObject();

        if ($context && isset($context['url'])) {
            return $this->createAbsoluteUrl($context['url']);
        }

        return null;
    }

    /**
     * Retrieve the current locale.
     *
     * @return string|null
     */
    public function currentLocale()
    {
        $langCode = $this->translator()->getLocale();
        $locales  = $this->translator()->locales();
        if (isset($locales[$langCode])) {
            $locale = $locales[$langCode];
            if (isset($locale['locale'])) {
                return $locale['locale'];
            } else {
                return $langCode;
            }
        }

        return null;
    }

    /**
     * Retrieve the current locale's language code.
     *
     * @return string
     */
    public function currentLanguage()
    {
        return $this->translator()->getLocale();
    }



    // Metadata
    // =========================================================================

    /**
     * Retrieve the canonical URI of the object.
     *
     * @return \Psr\Http\Message\UriInterface|string|null
     */
    public function canonicalUrl()
    {
        return $this->currentUrl();
    }

    /**
     * Parse the document title parts.
     *
     * @return string[]
     */
    protected function documentTitleParts()
    {
        return [
            'title' => $this->metaTitle(),
            'site'  => $this->siteName(),
        ];
    }

    /**
     * Retrieve the name or title of the object.
     *
     * @return string|null
     */
    public function metaTitle()
    {
        $context = $this->contextObject();
        $title   = null;

        if ($context instanceof MetatagInterface) {
            $title = (string)$context['metaTitle'];
        }

        if (!$title) {
            $title = (string)$this->fallbackMetaTitle();
        }

        return $title;
    }

    /**
     * Hook called as a fallback if no meta title is set on the object.
     *
     * This method should be extended by child controllers.
     *
     * @return string|null
     */
    protected function fallbackMetaTitle()
    {
        return (string)$this->title();
    }

    /**
     * Retrieve the description of the object.
     *
     * @return string|null
     */
    public function metaDescription()
    {
        $context = $this->contextObject();

        $desc = null;
        if ($context instanceof MetatagInterface) {
            $desc = (string)$context['metaDescription'];
        }

        if (!$desc) {
            $desc = (string)$this->fallbackMetaDescription();
        }

        return $desc;
    }

    /**
     * Hook called as a fallback if no meta description is set on the object.
     *
     * This method should be extended by child controllers.
     *
     * @return string|null
     */
    protected function fallbackMetaDescription()
    {
        return null;
    }

    /**
     * Retrieve the URL to the image representing the object.
     *
     * @return string|null
     */
    public function metaImage()
    {
        $context = $this->contextObject();

        $img = null;
        if ($context instanceof MetatagInterface) {
            $img = (string)$context['metaImage'];
        }

        if (!$img) {
            $img = (string)$this->fallbackMetaImage();
        }

        return $this->resolveMetaImage($img);
    }

    /**
     * Hook called as a fallback if no meta image is set on the object.
     *
     * This method should be extended by child controllers.
     *
     * @return string|null
     */
    protected function fallbackMetaImage()
    {
        return null;
    }

    /**
     * Retrieve the URL to the image representing the object.
     *
     * @param  string|null $img A path to an image.
     * @return string|null
     */
    protected function resolveMetaImage($img = null)
    {
        if (!$img) {
            $img = static::DEFAULT_SOCIAL_MEDIA_IMAGE;
        }

        if ($img) {
            $uri = $this->baseUrl();
            return $uri->withPath(strval($img));
        }

        return null;
    }

    /**
     * Retrieve the object's {@link https://developers.facebook.com/docs/reference/opengraph/ OpenGraph type},
     * for the "og:type" meta-property.
     *
     * @return string|null
     */
    public function opengraphType()
    {
        $context = $this->contextObject();

        $type = null;

        if ($context instanceof MetatagInterface) {
            $type = $context['opengraphType'];
        }

        if (!$type) {
            $type = MetatagInterface::DEFAULT_OPENGRAPH_TYPE;
        }

        return $type;
    }

    /**
     * Retrieve the URL to the object's social image for the "og:image" meta-property.
     *
     * This method can fallback onto {@see MetadataInterface::defaultMetaImage()}
     * for a common image between web annotation schemas.
     *
     * @return string|null
     */
    public function opengraphImage()
    {
        $context = $this->contextObject();

        $img = null;
        if ($context instanceof MetatagInterface) {
            $img = (string)$context['opengraphImage'];
        }

        if (!$img) {
            $img = (string)$this->fallbackOpengraphImage();
        }

        if ($img) {
            $uri = $this->baseUrl();
            return $uri->withPath(strval($img));
        }

        return $this->metaImage();
    }

    /**
     * Hook called as a fallback if no social image is set on the object.
     *
     * This method should be extended by child controllers.
     *
     * @return string|null
     */
    protected function fallbackOpengraphImage()
    {
        return null;
    }

    /**
     * Set additional SEO metadata.
     *
     * @return iterable
     */
    public function seoMetadata()
    {
        return $this->seoMetadata;
    }

    /**
     * Determine if we have additional SEO metadata.
     *
     * @return boolean
     */
    public function hasSeoMetadata()
    {
        if ($this->seoMetadata instanceof ArrayIterator) {
            return (count($this->seoMetadata) > 0);
        }

        return !empty($this->seoMetadata);
    }

    /**
     * Set additional SEO metadata.
     *
     * @param  array $metadata Map of metadata keys and values.
     * @return self
     */
    protected function setSeoMetadata(array $metadata)
    {
        if (is_array($this->seoMetadata)) {
            $this->seoMetadata = new ArrayIterator($this->seoMetadata);
        }

        foreach ($metadata as $key => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }

            $this->seoMetadata[] = [
                'name'    => $key,
                'content' => (string)$value
            ];
        }

        return $this;
    }



    // App
    // =========================================================================

    /**
     * Set the application's configset.
     *
     * @param  AppConfig $appConfig A Charcoal application configset.
     * @return self
     */
    protected function setAppConfig(AppConfig $appConfig)
    {
        $this->appConfig = $appConfig;

        return $this;
    }

    /**
     * Retrieve the application's configset or a specific setting.
     *
     * @param  string|null $key     Optional data key to retrieve from the configset.
     * @param  mixed|null  $default The default value to return if data key does not exist.
     * @return mixed|AppConfig|SettingsInterface
     */
    public function appConfig($key = null, $default = null)
    {
        if ($key) {
            if (isset($this->appConfig[$key])) {
                return $this->appConfig[$key];
            } else {
                if (!is_string($default) && is_callable($default)) {
                    return $default();
                } else {
                    return $default;
                }
            }
        }

        return $this->appConfig;
    }

    /**
     * Set the base URI of the project.
     *
     * @see    \Charcoal\App\ServiceProvider\AppServiceProvider `$container['base-url']`
     * @param  UriInterface $uri The base URI.
     * @return self
     */
    protected function setBaseUrl(UriInterface $uri)
    {
        $this->baseUrl = $uri;

        return $this;
    }

    /**
     * Retrieve the base URI of the project.
     *
     * @throws RuntimeException If the base URI is missing.
     * @return UriInterface|null
     */
    public function baseUrl()
    {
        if (!isset($this->baseUrl)) {
            throw new RuntimeException(sprintf(
                'The base URI is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->baseUrl;
    }

    /**
     * Prepend the base URI to the given path.
     *
     * @param  string $uri A URI path to wrap.
     * @return UriInterface
     */
    public function createAbsoluteUrl($uri)
    {
        $uri = strval($uri);
        if ($uri === '') {
            $uri = $this->baseUrl()->withPath('');
        } else {
            $parts = parse_url($uri);
            if (!isset($parts['scheme'])) {
                if (!in_array($uri[0], [ '/', '#', '?' ])) {
                    $path  = isset($parts['path']) ? $parts['path'] : '';
                    $query = isset($parts['query']) ? $parts['query'] : '';
                    $hash  = isset($parts['fragment']) ? $parts['fragment'] : '';
                    $uri   = $this->baseUrl()->withPath($path)->withQuery($query)->withFragment($hash);
                }
            }
        }

        return $uri;
    }
}
