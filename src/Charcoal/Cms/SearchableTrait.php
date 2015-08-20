<?php

namespace Charcoal\Cms;

/**
*Default implementation, as Trait, of the SearchableInterface
*/
trait SearchableTrait
{
    /**
    * @var array $_search_properties
    */
    protected $_search_properties = [];
    /**
    * @var TranslationString $_search_keywords
    */
    protected $_search_keywords;

    /**
    * @param array $data;
    */
    public function set_searchable_data(array $data)
    {
        if(isset($data['search_properties']) && $data['search_properties'] !== null) {
            $this->set_search_properties($data['search_properties']);
        }
        if(isset($data['search_keywords']) && $data['search_keywords'] !== null) {
            $this->set_search_keywords($data['search_keywords']);
        }
        return $this;
    }

    /**
    * @param array $_search_properties
    * @return SearchableInterface Chainable
    */
    public function set_search_properties(array $properties)
    {
        $this->_search_properties = $properties;
        return $this;
    }

    /**
    * @return array
    */
    public function search_properties()
    {
        return $this->_search_properties;
    }

    /**
    * @param mixed $keywords
    * @return SearchableInterface Chainable
    */
    public function set_search_keywords($keywords)
    {
        $this->_search_keywords = new TranslationString($keywords);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function search_keywords()
    {
        return $this->_search_keywords;
    }
}
