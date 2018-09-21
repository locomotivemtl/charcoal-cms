<?php

namespace Charcoal\Cms;

use Exception;

// From 'charcoal-cms'
use Charcoal\Object\CategoryTrait;
use Charcoal\Object\Content;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * CMS Tag
 */
abstract class AbstractTag extends Content implements TagInterface
{
    use CategoryTrait;

    /**
     * The tag's name.
     *
     * @var Translation|string|null
     */
    protected $name;

    /**
     * The tag's color.
     *
     * @var string
     */
    protected $color;

    /**
     * The tag's variations & variations
     *
     * @var mixed
     */
    protected $variations;

    /**
     * To change the orders of the result, when searching through tags.
     *
     * @var integer
     */
    protected $searchWeight;

    /**
     * @param array $data The object's data options.
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        $this->setData($this->defaultData());
    }

    /**
     * @throws Exception If function is called.
     * @return void
     */
    public function loadCategoryItems()
    {
        throw new Exception(
            'Cannot use loadCategoryItems with tags.'
        );
    }

    /**
     * @param  mixed $name The name of the tag.
     * @return self
     */
    public function setName($name)
    {
        $this->name = $this->translator()->translation($name);
        return $this;
    }

    /**
     * Retrieve the tag's name.
     *
     * @return Translation|string|null
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param  string $color The color in HEX format as a string.
     * @return self
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Retrieve the tag's color.
     *
     * @return mixed
     */
    public function color()
    {
        return $this->color;
    }

    /**
     * @param mixed $variations The tag's variations.
     * @return self
     */
    public function setVariations($variations)
    {
        $this->variations = $this->translator()->translation($variations);
        return $this;
    }

    /**
     * @return Translation|null
     */
    public function variations()
    {
        return $this->variations;
    }

    /**
     * @param integer $weight Search priority / weight of this tag.
     * @return self
     */
    public function setSearchWeight($weight)
    {
        $this->searchWeight = $weight;
        return $this;
    }

    /**
     * @return string
     */
    public function searchWeight()
    {
        return $this->searchWeight;
    }
}
