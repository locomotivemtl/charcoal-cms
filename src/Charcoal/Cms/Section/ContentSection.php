<?php

namespace Charcoal\Cms\Section;

// From `charcoal-core`
use \Charcoal\Translation\TranslationString;

// Parant namespace dependencies
use \Charcoal\Cms\AbstractSection;

class ContentSection extends AbstractSection
{
    /**
    * @return string
    */
    public function section_type()
    {
        return AbstractSection::TYPE_CONTENT;
    }

    /**
    * @param mixed $content
    * @return Section Chainable
    */
    public function set_content($content)
    {
        $this->content = new TranslationString($content);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function content()
    {
        return $this->content;
    }
}
