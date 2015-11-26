<?php

namespace Charccoal\Cms;

/**
*
*/
interface BlockInterface
{
    /**
    * @param string $type
    * @return BlockInterface Chainable
    */
    public function set_parent_type($type);

    /**
    * @return string
    */
    public function parent_type();

    /**
    * @param mixed $id
    * @return BlockInterface Chainable
    */
    public function set_parent_id($id);

    /**
    * @return mixed
    */
    public function parent_id();

    /**
    * @param string $type
    * @return Chainable
    */
    public function set_block_type($type);

    /**
    * @return string
    */
    public function block_type();

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
    * @param mixed $content
    * @return SectionInterface Chainable
    */
    public function set_content($content);

    /**
    * @return TranslationString
    */
    public function content();
}
