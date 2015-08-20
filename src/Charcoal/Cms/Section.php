<?php

namespace Charcoal\Cms;

// Dependencies from `PHP`
use \InvalidArgumentException as InvalidArgumentException;

// Module `charcoal-core` dependencies
use \Charcoal\Translation\TranslationString as TranslationString;

// Module `charcoal-base` dependencies
use \Charcoal\Object\Content as Content;
use \Charcoal\Object\CategorizableInterface as CategorizableInterface;
use \Charcoal\Object\CategorizableTrait as CategorizableTrait;
use \Charcoal\Object\HierarchicalInterface as HierarchicalInterface;
use \Charcoal\Object\HierarchicalTrait as HierarchicalTrait;
use \Charcoal\Object\RoutableInterface as RoutableInterface;
use \Charcoal\Object\RoutableTrait as RoutableTrait;

// Local namespace dependencies
use \Charcoal\Cms\MetatagInterface as MetatagInterface;
use \Charcoal\Cms\SearchableInterface as SearchableInterface;
use \Charcoal\Cms\SectionInterface as SectionInterface;

/**
* A Section is a unique, reachable page.
*/
class Section extends Content implements
    SectionInterface,
    //MetatagInterface,
    SearchableInterface,
    CategorizableInterface,
    HierarchicalInterface,
    RoutableInterface
{
    use CategorizableTrait;
    use HierarchicalTrait;
    use RoutableTrait;

    use MetatagTrait;
    use SearchableTrait;

    const TYPE_CONTENT = 'content';
    const TYPE_EMPTY = 'empty';
    const TYPE_EXTERNAL = 'external';
    const DEFAULT_TYPE = self::TYPE_CONTENT;

    protected $_section_type = self::DEFAULT_TYPE;

    /**
    * @var TranslationString $_title
    */
	protected $_title;
    /**
    * @var TranslationString $_subtitle
    */
	protected $_subtitle;
    /**
    * @var TranslationString $_content
    */
    protected $_content;

    /**
    * @var mixed $_template
    */
    protected $_template;
    /**
    * @var array $_template_options
    */
    protected $_template_options = [];

    /**
    * @param array
    * @return Section Chainable
    */
    public function set_data(array $data)
    {
        parent::set_data($data);
        $this->set_metatag_data($data);
        $this->set_searchable_data($data);
        $this->set_categorizable_data($data);
        $this->set_hierarchical_data($data);
        $this->set_routable_data($data);

        if(isset($data['section_type']) && $data['section_type'] !== null) {
            $this->set_section_type($data['section_type']);
        }
        if(isset($data['title']) && $data['title'] !== null) {
            $this->set_title($data['title']);
        }
        if(isset($data['subtitle']) && $data['subtitle'] !== null) {
            $this->set_subtitle($data['subtitle']);
        }
        if(isset($data['content']) && $data['content'] !== null) {
            $this->set_content($data['content']);
        }
        return $this;
    }

    /**
    * @param string $section_type
    * @throws InvalidArgumentException If the section type is not a string or not a valid section type.
    * @return SectionInterface Chainable
    */
    public function set_section_type($section_type)
    {
        if(!is_string($section_type)) {
            throw new InvalidArgumentException('Section type must be a string');
        }
        $valid_types = [
            self::TYPE_CONTENT,
            self::TYPE_EMPTY,
            self::TYPE_EXTERNAL
        ];
        if(!in_array($section_type, $valid_types)) {
            throw new InvalidArgumentException('Section type is not valid');
        }

        $this->_section_type = $section_type;
        return $this;
    }

    /**
    * @return string
    */
    public function section_type()
    {
        return $this->_section_type;
    }

    /**
    * @param mixed $title
    * @return TranslationString
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
    * @param mixed $subbtitle
    * @return Section Chainable
    */
    public function set_subtitle($subtitle)
    {
        $this->_subtitle = new TranslationString($subtitle);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function subtitle()
    {
        return $this->_subtitle;
    }

    /**
    * @param mixed $content
    * @return Section Chainable
    */
    public function set_content($content)
    {
        $this->_content = new TranslationString($content);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function content()
    {
        return $this->_content;
    }

    /**
    * @param mixed $template
    * @return SectionInterface Chainable
    */
    public function set_template($template)
    {
        $this->_template = $template;
        return $this;
    }

    /**
    * @return mixed
    */
    public function template()
    {
        return $this->_template;
    }

    /**
    * @param array $template_options
    * @return SectionInterface Chainable
    */
    public function set_template_options(array $template_options)
    {
        $this->_template_options = $template_options;
        return $this;
    }

    /**
    * @return array
    */
    public function template_options()
    {
        return $this->_template_options;
    }

    /**
    * HierarchicalTrait > load_children
    *
    * @return array
    */
    public function load_children()
    {
        $source = clone($this->source());
        $source->reset();
        $source->set_filters([
            [
                'property'=>'master',
                'val'=>$this->id()
            ],
            [
                'property'=>'active',
                'val'=>1
            ]
        ]);
        $source->set_orders([
            [
                'property'=>'position',
                'mode'=>'asc'
            ]
        ]);
        $children = $source->load_items();
        return $children;
    }

    /**
    * MetatagTrait > canonical_url
    *
    * @return string
    */
    public function canonical_url()
    {
        return '';
    }
}
