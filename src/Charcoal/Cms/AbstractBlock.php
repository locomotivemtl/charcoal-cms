<?php

namespace Charcoal\Cms;

// Dependencies from `PHP`
use \InvalidArgumentException;

// Module `charcoal-base` dependencies
use \Charcoal\Object\Content;

/**
*
*/
abstract class AbstractBlock extends Content implements BlockInterface
{
    const TYPE_CONTENT = 'charcoal/cms/block/content';
    const TYPE_GALLERY = 'charcoal/cms/block/gallery';
    const DEFAULT_TYPE = self::TYPE_CONTENT;

    /**
    * @var string $parent_type
    */
    private $parent_type;

    /**
    * @var mixed $parent_id
    */
    private $parent_id;

    /**
    * @var string $block_type
    */
    private $block_type = self::DEFAULT_TYPE;

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
    * @param string $type
    * @return BlockInterface Chainable
    */
    public function set_parent_type($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException(
                'Parent type must be a string.'
            );
        }
        $this->parent_type = $type;
        return $this;
    }

    /**
    * @return string
    */
    public function parent_type()
    {
        return $this->parent_type;
    }

    /**
    * @param mixed $id
    * @return BlockInterface Chainable
    */
    public function set_parent_id($id)
    {
        if (!is_scalar($id)) {
            throw new InvalidArgumentException(
                'Parent ID must be a string or a number (a scalar).'
            );
        }
        $this->parent_id = $id;
        return $this;
    }

    /**
    * @return mixed
    */
    public function parent_id()
    {
        return $this->parent_id;
    }

    /**
    * @param string $type
    * @return Chainable
    */
    public function set_block_type($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException(
                'Block type must be a string.'
            );
        }
        $this->block_type = $type;
        return $this;
    }

    /**
    * @return string
    */
    public function block_type()
    {
        return $this->block_type;
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
