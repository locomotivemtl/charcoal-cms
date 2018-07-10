<?php

namespace Charcoal\Cms;

use Charcoal\Object\ContentInterface;

/**
 *
 */
interface TagInterface extends ContentInterface
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
}
