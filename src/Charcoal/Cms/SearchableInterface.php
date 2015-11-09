<?php

namespace Charcoal\Cms;

/**
*
*/
interface SearchableInterface
{
    /**
    * @param array $data
    * @return SearchableInterface Chainable
    */
    public function set_searchable_data(array $data);

    /**
    * @param array $_search_properties
    * @return SearchableInterface Chainable
    */
    public function set_search_properties(array $properties);

    /**
    * @return array
    */
    public function search_properties();

    /**
    * @param mixed $keywords
    * @return SearchableInterface Chainable
    */
    public function set_search_keywords($keywords);

    /**
    * @return TranslationString
    */
    public function search_keywords();
}
