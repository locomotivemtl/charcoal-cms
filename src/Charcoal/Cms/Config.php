<?php

namespace Charcoal\Cms;

// dependencies from `charcoal-base`
use Charcoal\Object\Content;

// dependencies from `charcoal-translation`
use Charcoal\Translation\TranslationString;

/**
 * Class Config
 */
class Config extends Content
{
    /**
     * @var string $defaultMetaTitle
     */
    protected $defaultMetaTitle;

    /**
     * @var string $defaultMetaDescription
     */
    protected $defaultMetaDescription;

    /**
     * @var string $defaultMetaImage
     */
    protected $defaultMetaImage;

    /**
     * @var string $defaultMetaUrl
     */
    protected $defaultMetaUrl;

    // ==========================================================================
    // SETTERS
    // ==========================================================================

    /**
     * @param mixed $defaultMetaTitle The default meta title.
     * @return self
     */
    public function setDefaultMetaTitle($defaultMetaTitle)
    {
        $this->defaultMetaTitle = $this->parseAsTranslatable($defaultMetaTitle);

        return $this;
    }

    /**
     * @param mixed $defaultMetaDescription The default meta description.
     * @return self
     */
    public function setDefaultMetaDescription($defaultMetaDescription)
    {
        $this->defaultMetaDescription = $this->parseAsTranslatable($defaultMetaDescription);

        return $this;
    }

    /**
     * @param mixed $defaultMetaImage The default meta image.
     * @return self
     */
    public function setDefaultMetaImage($defaultMetaImage)
    {
        $this->defaultMetaImage = $defaultMetaImage;

        return $this;
    }

    /**
     * @param mixed $defaultMetaUrl The default meta url.
     * @return self
     */
    public function setDefaultMetaUrl($defaultMetaUrl)
    {
        $this->defaultMetaUrl = $this->parseAsTranslatable($defaultMetaUrl);

        return $this;
    }

    // ==========================================================================
    // GETTERS
    // ==========================================================================

    /**
     * @return mixed
     */
    public function defaultMetaTitle()
    {
        return $this->defaultMetaTitle;
    }

    /**
     * @return mixed
     */
    public function defaultMetaDescription()
    {
        return $this->defaultMetaDescription;
    }

    /**
     * @return mixed
     */
    public function defaultMetaImage()
    {
        return $this->defaultMetaImage;
    }

    /**
     * @return mixed
     */
    public function defaultMetaUrl()
    {
        return $this->defaultMetaUrl;
    }

    // ==========================================================================
    // UTILS
    // ==========================================================================

    /**
     * Parse the property value as a "L10N" value type.
     *
     * @param  mixed $value The value being localized.
     * @return TranslationString|string
     */
    public function parseAsTranslatable($value)
    {
        if (TranslationString::isTranslatable($value)) {
            return new TranslationString($value);
        } else {
            return '';
        }
    }
}