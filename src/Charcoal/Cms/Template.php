<?php

namespace Charcoal\Cms;

// Dependencies from `PHP`
use \InvalidArgumentException as InvalidArgumentException;

// Module `charcoal-core` dependencies
use \Charcoal\Translation\TranslationString as TranslationString;

// Module `charcoal-base` dependencies
use \Charcoal\Object\Content as Content;

// Local namespace dependencies
use \Charcoal\Cms\TemplateInterface as TemplateInterface;

/**
*
*/
class Template extends Content implements TemplateInterface
{
    /**
    * @var string $ident
    */
    private $ident;
    /**
    * @var TranslationString $title
    */
    private $title;
    /**
    * @var array $options
    */
    private $options;

    /**
    * IndexableInterface > key()
    *
    * @return string
    */
    public function key()
    {
        return 'ident';
    }

    /**
    * @param string $ident
    * @return TemplateInterface Chainable
    */
    public function set_ident($ident)
    {
        if (!is_string($ident)) {
            throw new InvalidArgumentException(
                'Ident must be a string.'
            );
        }
        $this->ident = $ident;
        return $this;
    }

    /**
    * @return string
    */
    public function ident()
    {
        return $this->ident;
    }

    /**
    * @param mixed $title
    * @return TemplateInterface Chainable
    */
    public function set_title($title)
    {
        $this->title = new TranslationString($title);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function title()
    {
        return $this->title;
    }

    /**
    * @param array $options
    * @return TemplateInterface Chainable
    */
    public function set_options($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
    * @return array
    */
    public function options()
    {
        return $this->options;
    }
}
