<?php

namespace Charcoal\Cms;

use Charcoal\Object\ContentInterface;
use Charcoal\Object\CategoryInterface;

/**
 *
 */
interface TagInterface extends
    CategoryInterface,
    ContentInterface
{
    /**
     * @param  mixed $name The name of the tag.
     * @return self
     */
    public function setName($name);

    /**
     * Retrieve the tag's name.
     *
     * @return \Charcoal\Translator\Translation|string|null
     */
    public function name();

    /**
     * @param  string $color The color in HEX format as a string.
     * @return self
     */
    public function setColor($color);

    /**
     * Retrieve the tag's color.
     *
     * @return mixed
     */
    public function color();

    /**
     * @param mixed $variations The tag's variations.
     * @return self
     */
    public function setVariations($variations);

    /**
     * Retrieve the tag's variations.
     *
     * @return mixed
     */
    public function variations();

    /**
     * @param integer $priority Priority (type).
     * @return self
     */
    public function setSearchWeight($priority);

    /**
     * @return string
     */
    public function searchWeight();
}
