<?php

namespace Charcoal\Cms;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * Default implementation, as Trait, of the SearchableInterface
 */
trait SearchableTrait
{
    /**
     * @var array
     */
    private $searchProperties = [];

    /**
     * @var Translation|string|null
     */
    private $searchKeywords;

    /**
     * @param  array $properties The properties to search into.
     * @return self
     */
    public function setSearchProperties(array $properties)
    {
        $this->searchProperties = $properties;
        return $this;
    }

    /**
     * @return array
     */
    public function searchProperties()
    {
        return $this->searchProperties;
    }

    /**
     * @param  mixed $keywords The search keywords (localized).
     * @return self
     */
    public function setSearchKeywords($keywords)
    {
        $this->searchKeywords = $this->translator()->translation($keywords);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function searchKeywords()
    {
        return $this->searchKeywords;
    }
}
