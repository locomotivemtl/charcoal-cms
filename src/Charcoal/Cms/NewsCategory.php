<?php

namespace Charcoal\Cms;

// From 'charcoal-object'
use Charcoal\Object\Content;
use Charcoal\Object\CategoryInterface;
use Charcoal\Object\CategoryTrait;

// From 'charcoal-cms'
use Charcoal\Cms\News;

/**
 * News Category
 */
class NewsCategory extends Content implements CategoryInterface
{
    use CategoryTrait;

    /**
     * Translatable
     * @var string[] $name
     */
    protected $name;

    /**
     * Section constructor.
     * @param array $data Init data.
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        if (is_callable([ $this, 'defaultData' ])) {
            $this->setData($this->defaultData());
        }
    }

    /**
     * CategoryTrait > itemType()
     *
     * @return string
     */
    public function itemType()
    {
        return News::class;
    }

    /**
     * @return \Charcoal\Model\Collection|array
     */
    public function loadCategoryItems()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param mixed $name The category name.
     * @return self
     */
    public function setName($name)
    {
        $this->name = $this->translator()->translation($name);

        return $this;
    }
}
