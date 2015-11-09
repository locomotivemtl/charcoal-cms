<?php

namespace Charcoal\Cms;

/**
*
*/
interface SectionInterface
{
    /**
    * @param string $section_type
    * @return SectionInterface Chainable
    */
    public function set_section_type($section_type);

    /**
    * @return string
    */
    public function section_type();

    /**
    * @param mixed $title
    * @return SectionInterface Chainable
    */
    public function set_title($title);

    /**
    * @return TranslationString
    */
    public function title();

    /**
    * @param mixed $subtitle
    * @return SectionInterface Chainable
    */
    public function set_subtitle($subtitle);

    /**
    * @return TranslationString
    */
    public function subtitle();

    /**
    * @param mixed $template
    * @return SectionInterface Chainable
    */
    public function set_template($template);

    /**
    * @return mixed
    */
    public function template();

    /**
    * @param array $template_options
    * @return SectionInterface Chainable
    */
    public function set_template_options(array $template_options);

    /**
    * @return array
    */
    public function template_options();
}
