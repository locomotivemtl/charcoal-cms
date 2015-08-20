<?php

namespace Charcoal\Cms;

interface TemplateInterface
{
    /**
    * @param string $ident
    * @return TemplateInterface Chainable
    */
    public function set_ident($ident);

    /**
    * @return string
    */
    public function ident();

    /**
    * @param mixed $title
    * @return TemplateInterface Chainable
    */
    public function set_title($title);
    /**
    * @return TranslationString
    */
    public function title();

    /**
    * @param array $options
    * @return TemplateInterface Chainable
    */
    public function set_options($options);

    /**
    * @return array
    */
    public function options();
}
