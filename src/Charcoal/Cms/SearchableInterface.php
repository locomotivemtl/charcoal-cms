<?php

namespace Charcoal\Cms;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 *
 */
interface SearchableInterface
{
    /**
     * @param  array $properties The properties to search into.
     * @return self
     */
    public function setSearchProperties(array $properties);

    /**
     * @return array
     */
    public function searchProperties();

    /**
     * @param  mixed $keywords The search keywords (localized).
     * @return self
     */
    public function setSearchKeywords($keywords);

    /**
     * @return Translation|string|null
     */
    public function searchKeywords();
}
