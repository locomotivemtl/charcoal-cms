<?php

namespace Charcoal\Cms\Support;

use InvalidArgumentException;

// From 'psr/http-message'
use Psr\Http\Message\UriInterface;

// From 'charcoal-object'
use Charcoal\Object\RoutableInterface;

/**
 * Provides awareness of locales.
 *
 * ## Requirements
 *
 * - Translator (e.g., {@see \Charcoal\Translator\TranslatorAwareTrait})
 */
trait LocaleAwareTrait
{
    /**
     * Available languages as defined by the application configset.
     *
     * @var array
     */
    protected $locales = [];

    /**
     * Store the processed link structures to translations
     * for the current route, if any.
     *
     * @var array
     */
    protected $alternateTranslations;

    /**
     * Retrieve the available locales.
     *
     * @return array
     */
    protected function locales()
    {
        return $this->locales;
    }

    /**
     * Set the available locales.
     *
     * @param  array $locales The list of language structures.
     * @return self
     */
    protected function setLocales(array $locales)
    {
        $this->locales = [];
        foreach ($locales as $langCode => $localeStruct) {
            $this->locales[$langCode] = $this->parseLocale($localeStruct, $langCode);
        }

        return $this;
    }

    /**
     * Parse the given locale.
     *
     * @see    \Charcoal\Admin\Widget\FormSidebarWidget::languages()
     * @see    \Charcoal\Admin\Widget\FormGroupWidget::languages()
     * @param  array  $localeStruct The language structure.
     * @param  string $langCode     The language code.
     * @throws InvalidArgumentException If the locale does not have a language code.
     * @return array
     */
    private function parseLocale(array $localeStruct, $langCode)
    {
        $trans = 'locale.'.$langCode;

        /** Setup the name of the language in the current locale */
        if (isset($localeStruct['name'])) {
            $name = $this->translator()->translate($localeStruct['name']);
        } else {
            $name = $this->translator()->translate($trans);
            if ($trans === $name) {
                $name = strtoupper($langCode);
            }
        }

        /** Setup the native name of the language */
        if (isset($localeStruct['native'])) {
            $native = $this->translator()->translate($localeStruct['native'], [], null, $langCode);
        } else {
            $native = $this->translator()->translate($trans, [], null, $langCode);
            if ($trans === $native) {
                $native = strtoupper($langCode);
            }
        }

        if (!isset($localeStruct['locale'])) {
            $localeStruct['locale'] = $langCode;
        }

        $localeStruct['name']   = $name;
        $localeStruct['native'] = $native;
        $localeStruct['code']   = $langCode;

        return $localeStruct;
    }

    /**
     * Retrieve the translator service.
     *
     * @return array
     */
    protected function availableLanguages()
    {
        return array_keys($this->locales());
    }

    /**
     * Build the alternate translations associated with the current route.
     *
     * This method _excludes_ the current route's canonical URI.
     *
     * @return array
     */
    protected function buildAlternateTranslations()
    {
        $translations = [];

        $context  = $this->contextObject();
        $origLang = $this->currentLanguage();

        $this->translator()->isIteratingLocales = true;
        foreach ($this->locales() as $langCode => $localeStruct) {
            if ($langCode === $origLang) {
                continue;
            }

            $this->translator()->setLocale($langCode);

            $translations[$langCode] = $this->formatAlternateTranslation($context, $localeStruct);
        }

        $this->translator()->setLocale($origLang);
        unset($this->translator()->isIteratingLocales);

        return $translations;
    }

    /**
     * Retrieve the alternate translations associated with the current route.
     *
     * This method _excludes_ the current route's canonical URI.
     *
     * @return array
     */
    protected function getAlternateTranslations()
    {
        if ($this->alternateTranslations === null) {
            $this->alternateTranslations = $this->buildAlternateTranslations();
        }

        return $this->alternateTranslations;
    }

    /**
     * Format an alternate translation for the given translatable model.
     *
     * Note: The application's locale is already modified and will be reset
     * after processing all available languages.
     *
     * @param  mixed $context      The translated {@see \Charcoal\Model\ModelInterface model}
     *     or array-accessible structure.
     * @param  array $localeStruct The currently iterated language.
     * @return array Returns a link structure.
     */
    protected function formatAlternateTranslation($context, array $localeStruct)
    {
        return [
            'id'       => ($context['id']) ? : $this->templateName(),
            'title'    => ((string)$context['title']) ? : $this->title(),
            'url'      => $this->formatAlternateTranslationUrl($context, $localeStruct),
            'hreflang' => $localeStruct['code'],
            'locale'   => $localeStruct['locale'],
            'name'     => $localeStruct['name'],
            'native'   => $localeStruct['native'],
        ];
    }

    /**
     * Format an alternate translation URL for the given translatable model.
     *
     * Note: The application's locale is already modified and will be reset
     * after processing all available languages.
     *
     * @param  mixed $context      The translated {@see \Charcoal\Model\ModelInterface model}
     *     or array-accessible structure.
     * @param  array $localeStruct The currently iterated language.
     * @return string Returns a link.
     */
    protected function formatAlternateTranslationUrl($context, array $localeStruct)
    {
        $isRoutable = ($context instanceof RoutableInterface && $context->isActiveRoute());
        $langCode   = $localeStruct['code'];
        $path       = ($isRoutable ? $context->url($langCode) : ($this->currentUrl() ? : $langCode));

        if ($path instanceof UriInterface) {
            $path = $path->getPath();
        }

        return $this->baseUrl()->withPath($path);
    }

    /**
     * Yield the alternate translations associated with the current route.
     *
     * @return Generator|null
     */
    public function alternateTranslations()
    {
        foreach ($this->getAlternateTranslations() as $langCode => $transStruct) {
            yield $langCode => $transStruct;
        }
    }

    /**
     * Determine if there exists alternate translations associated with the current route.
     *
     * @return boolean
     */
    public function hasAlternateTranslations()
    {
        return !empty($this->getAlternateTranslations());
    }

    /**
     * Retrieve the translator service.
     *
     * @see    \Charcoal\Translator\TranslatorAwareTrait
     * @return \Charcoal\Translator\Translator
     */
    abstract protected function translator();

    /**
     * Retrieve the template's identifier.
     *
     * @return string
     */
    abstract public function templateName();

    /**
     * Retrieve the title of the page (from the context).
     *
     * @return string
     */
    abstract public function title();

    /**
     * Retrieve the current URI of the context.
     *
     * @return \Psr\Http\Message\UriInterface|string
     */
    abstract public function currentUrl();

    /**
     * Retrieve the current object relative to the context.
     *
     * @return \Charcoal\Model\ModelInterface|null
     */
    abstract public function contextObject();

    /**
     * Retrieve the base URI of the project.
     *
     * @throws RuntimeException If the base URI is missing.
     * @return UriInterface|null
     */
    abstract public function baseUrl();
}
