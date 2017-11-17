<?php

namespace Charcoal\Cms;

// From 'charcoal-object'
use Charcoal\Object\Content;
use Charcoal\Object\CategoryInterface;
use Charcoal\Object\CategoryTrait;

// From 'charcoal-cms'
use Charcoal\Cms\Event;

/**
 * Event Category
 */
class EventCategory extends Content implements CategoryInterface
{
    use CategoryTrait;

    /**
     * Translatable
     * @var string[] $name
     */
    protected $name;

    /**
     * CategoryTrait > itemType()
     *
     * @return string
     */
    public function itemType()
    {
        return Event::class;
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
