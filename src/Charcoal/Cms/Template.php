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
    * @var string $_ident
    */
    protected $_ident;
    /**
    * @var TranslationString $_title
    */
    protected $_title;
    /**
    * @var array $_options
    */
    protected $_options;

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
    * @param array $data
    * @return Template Chainable
    */
    public function set_data(array $data)
    {
        parent::set_data($data);
        if(isset($data['ident']) && $data['ident'] !== null) {
            $this->set_ident($data['ident']);
        }
        if(isset($data['title']) && $data['title'] !== null) {
            $this->set_title($data['title']);
        }
        if(isset($data['options']) && $data['options'] !== null) {
            $this->set_options($data['options']);
        }

        return $this;
    }

    /**
    * @param string $ident
    * @return TemplateInterface Chainable
    */
    public function set_ident($ident)
    {
        if(!is_string($ident)) {
            throw new InvalidArgumentException('Ident must be a string.');
        }
        $this->_ident = $ident;
        return $this;
    }

    /**
    * @return string
    */
    public function ident()
    {
        return $this->_ident;
    }

    /**
    * @param mixed $title
    * @return TemplateInterface Chainable
    */
    public function set_title($title)
    {
        $this->_title = new TranslationString($title);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function title()
    {
        return $this->_title;
    }

    /**
    * @param array $options
    * @return TemplateInterface Chainable
    */
    public function set_options($options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
    * @return array
    */
    public function options()
    {
        return $this->_options;
    }
}
