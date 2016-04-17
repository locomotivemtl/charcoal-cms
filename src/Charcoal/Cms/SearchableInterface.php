<?php

namespace Charcoal\Cms;

/**
 *
 */
interface SearchableInterface
{
    /**
     * @param array $properties The properties to search into.
     * @return SearchableInterface Chainable
     */
    public function setSearchProperties(array $properties);

    /**
     * @return array
     */
    public function searchProperties();

    /**
     * @param mixed $keywords The search keywords (localized).
     * @return SearchableInterface Chainable
     */
    public function setSearchKeywords($keywords);

    /**
     * @return TranslationString
     */
    public function searchKeywords();
}
