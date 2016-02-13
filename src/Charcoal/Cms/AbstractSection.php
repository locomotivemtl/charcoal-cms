<?php

namespace Charcoal\Cms;

use \InvalidArgumentException;

use \Charcoal\Model\Collection;
use \Charcoal\Translation\TranslationString;
use \Charcoal\Object\Content;
use \Charcoal\Object\CategorizableInterface;
use \Charcoal\Object\CategorizableTrait;
use \Charcoal\Object\HierarchicalInterface;
use \Charcoal\Object\HierarchicalTrait;
use \Charcoal\Object\RoutableInterface;
use \Charcoal\Object\RoutableTrait;

use \Charcoal\Cms\MetatagInterface;
use \Charcoal\Cms\SearchableInterface;
use \Charcoal\Cms\SectionInterface;

/**
* A Section is a unique, reachable page.
*
* ## Types of sections
* There can be different types of ection. 4 exists in the CMS module:
* - `blocks`
* - `content`
* - `empty`
* - `external`
*
* ## External implementations
* Sections imlpement the following _Interface_ / _Trait_:
* - From the `Charcoal\Object` namespace (in `charcoal-base`)
*   - `Categorizable`
*   - `Hierarchical`
*   - `Routable`
* - From the local `Charcoal\Cms` namespace
*   - `Metatag`
*   - `Searchable`
*
*/
abstract class AbstractSection extends Content implements
    CategorizableInterface,
    HierarchicalInterface,
    MetatagInterface,
    RoutableInterface,
    SearchableInterface,
    SectionInterface
{
    use CategorizableTrait;
    use HierarchicalTrait;
    use MetatagTrait;
    use RoutableTrait;
    use SearchableTrait;

    const TYPE_BLOCKS = 'charcoal/cms/section/blocks';
    const TYPE_CONTENT = 'charcoal/cms/section/content';
    const TYPE_EMPTY = 'charcoal/cms/section/empty';
    const TYPE_EXTERNAL = 'charcoal/cms/section/external';
    const DEFAULT_TYPE = self::TYPE_CONTENT;

    /**
    * @var string $section_type
    */
    private $section_type = self::DEFAULT_TYPE;

    /**
    * @var TranslationString $title
    */
    private $title;
    /**
    * @var TranslationString $subtitle
    */
    private $subtitle;
    /**
    * @var TranslationString $content
    */
    private $content;

    /**
    * @var mixed $template
    */
    private $template;
    /**
    * @var array $template_options
    */
    private $template_options = [];


    /**
    * @param string $section_type
    * @throws InvalidArgumentException If the section type is not a string or not a valid section type.
    * @return SectionInterface Chainable
    */
    public function set_section_type($section_type)
    {
        if (!is_string($section_type)) {
            throw new InvalidArgumentException('Section type must be a string');
        }
        $valid_types = [
            self::TYPE_CONTENT,
            self::TYPE_EMPTY,
            self::TYPE_EXTERNAL
        ];
        if (!in_array($section_type, $valid_types)) {
            throw new InvalidArgumentException('Section type is not valid');
        }

        $this->section_type = $section_type;
        return $this;
    }

    /**
    * @return string
    */
    public function section_type()
    {
        return $this->section_type;
    }

    /**
    * @param mixed $title
    * @return TranslationString
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
    * @param mixed $subbtitle
    * @return Section Chainable
    */
    public function set_subtitle($subtitle)
    {
        $this->subtitle = new TranslationString($subtitle);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function subtitle()
    {
        return $this->subtitle;
    }

    /**
    * @param mixed $template
    * @return SectionInterface Chainable
    */
    public function set_template($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
    * @return mixed
    */
    public function template()
    {
        return $this->template;
    }

    /**
    * @param array $template_options
    * @return SectionInterface Chainable
    */
    public function set_template_options(array $template_options)
    {
        $this->template_options = $template_options;
        return $this;
    }

    /**
    * @return array
    */
    public function template_options()
    {
        return $this->template_options;
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
    * @todo
    */
    public function canonical_url()
    {
        return '';
    }
}
