<?php

namespace Charcoal\Cms;

use InvalidArgumentException;

// From 'charcoal-core'
use Charcoal\Model\Collection;
use Charcoal\Loader\CollectionLoader;

// From 'charcoal-object'
use Charcoal\Object\Content;
use Charcoal\Object\HierarchicalTrait;
use Charcoal\Object\RoutableTrait;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * A Section is a unique, reachable page.
 *
 * ## Types of sections
 * There can be different types of section. 4 exists in the CMS module:
 * - `blocks`
 * - `content`
 * - `empty`
 * - `external`
 *
 * ## External implementations
 * Sections implement the following _Interface_ / _Trait_:
 * - From the `Charcoal\Object` namespace (in `charcoal-object`)
 *   - `Hierarchical`
 *   - `Routable`
 * - From the local `Charcoal\Cms` namespace
 *   - `Metatag`
 *   - `Searchable`
 *
 */
abstract class AbstractSection extends Content implements SectionInterface
{
    use HierarchicalTrait;
    use MetatagTrait;
    use RoutableTrait;
    use SearchableTrait;
    use TemplateableTrait;

    const TYPE_BLOCKS = 'charcoal/cms/section/blocks-section';
    const TYPE_CONTENT = 'charcoal/cms/section/content-section';
    const TYPE_EMPTY = 'charcoal/cms/section/empty-section';
    const TYPE_EXTERNAL = 'charcoal/cms/section/external-section';
    const DEFAULT_TYPE = self::TYPE_CONTENT;

    /**
     * @var string
     */
    private $sectionType = self::DEFAULT_TYPE;

    /**
     * @var Translation|string|null
     */
    private $title;

    /**
     * @var Translation|string|null
     */
    private $subtitle;

    /**
     * @var Translation|string|null
     */
    private $content;

    /**
     * @var Translation|string|null
     */
    private $image;

    /**
     * The menus this object is shown in.
     *
     * @var string[]
     */
    protected $inMenu;

    /**
     * @var array
     */
    protected $keywords;

    /**
     * @var Translation|string $summary
     */
    protected $summary;

    /**
     * @var string $externalUrl
     */
    protected $externalUrl;

    /**
     * @var boolean $locked
     */
    protected $locked;

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
     * Determine if the object can be deleted.
     *
     * @return boolean
     */
    public function isDeletable()
    {
        return !!$this->id() && !$this->locked();
    }

    /**
     * Retrieve the object's title.
     *
     * @return string
     */
    public function hierarchicalLabel()
    {
        return str_repeat('— ', ($this->hierarchyLevel() - 1)).$this->title();
    }

    /**
     * HierarchicalTrait > loadChildren
     *
     * @return \ArrayAccess|\Traversable
     */
    public function loadChildren()
    {
        $loader = new CollectionLoader([
            'logger'  => $this->logger,
            'factory' => $this->modelFactory()
        ]);
        $loader->setModel($this);

        $loader->addFilter([
            'property' => 'master',
            'value'      => $this->id()
        ]);
        $loader->addFilter([
            'property' => 'active',
            'value'      => true
        ]);

        $loader->addOrder([
            'property' => 'position',
            'mode'     => 'asc'
        ]);

        return $loader->load();
    }

    /**
     * Set the section's type.
     *
     * @param  string $type The section type.
     * @throws InvalidArgumentException If the section type is not a string or not a valid section type.
     * @return self
     */
    public function setSectionType($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException(
                'Section type must be a string'
            );
        }

        $this->sectionType = $type;

        return $this;
    }

    /**
     * Set the menus this object belongs to.
     *
     * @param  string|string[] $menu One or more menu identifiers.
     * @return self
     */
    public function setInMenu($menu)
    {
        $this->inMenu = $menu;

        return $this;
    }

    /**
     * Set the object's keywords.
     *
     * @param  string|string[] $keywords One or more entries.
     * @return self
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @param Translation|string|null $summary The summary.
     * @return self
     */
    public function setSummary($summary)
    {
        $this->summary = $this->translator()->translation($summary);

        return $this;
    }

    /**
     * @param Translation|string|null $externalUrl The external url.
     * @return self
     */
    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = $this->translator()->translation($externalUrl);

        return $this;
    }

    /**
     * Section is locked when you can't change the URL
     * @param boolean $locked Prevent new route creation about that object.
     * @return self
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * @param Translation|string|null $title The section title (localized).
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $this->translator()->translation($title);

        return $this;
    }

    /**
     * @param Translation|string|null $subtitle The section subtitle (localized).
     * @return self
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $this->translator()->translation($subtitle);

        return $this;
    }

    /**
     * @param Translation|string|null $content The section content (localized).
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $this->translator()->translation($content);

        return $this;
    }

    /**
     * @param mixed $image The section main image (localized).
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $this->translator()->translation($image);

        return $this;
    }

    /**
     * Retrieve the section's type.
     *
     * @return string
     */
    public function sectionType()
    {
        return $this->sectionType;
    }

    /**
     * @return Translation|string|null
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * @return Translation|string|null
     */
    public function subtitle()
    {
        return $this->subtitle;
    }

    /**
     * @return Translation|string|null
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * @return Translation|string|null
     */
    public function image()
    {
        return $this->image;
    }

    /**
     * Retrieve the menus this object belongs to.
     *
     * @return Translation|string|null
     */
    public function inMenu()
    {
        return $this->inMenu;
    }

    /**
     * Retrieve the object's keywords.
     *
     * @return string[]
     */
    public function keywords()
    {
        return $this->keywords;
    }

    /**
     * HierarchicalTrait > loadChildren
     *
     * @return Translation|string|null
     */
    public function summary()
    {
        return $this->summary;
    }

    /**
     * @return Translation|string|null
     */
    public function externalUrl()
    {
        return $this->externalUrl;
    }

    /**
     * @return boolean Or Null.
     */
    public function locked()
    {
        return $this->locked;
    }

    /**
     * MetatagTrait > canonicalUrl
     *
     * @todo
     * @return string
     */
    public function canonicalUrl()
    {
        return $this->url();
    }

    /**
     * @return Translation|string|null
     */
    public function defaultMetaTitle()
    {
        return $this->title();
    }

    /**
     * @return Translation|string|null
     */
    public function defaultMetaDescription()
    {
        $content = $this->translator()->translation($this['content']);
        if ($content instanceof Translation) {
            $desc = [];
            foreach ($content->data() as $lang => $text) {
                $desc[$lang] = strip_tags($text);
            }

            return $this->translator()->translation($desc);
        }

        return null;
    }

    /**
     * @return Translation|string|null
     */
    public function defaultMetaImage()
    {
        return $this->image();
    }

    /**
     * Route generated on postSave in case
     * it contains the ID of the section, which
     * you only get once you have save
     *
     * @return boolean
     */
    protected function postSave()
    {
        // RoutableTrait
        if (!$this->locked()) {
            $this->generateObjectRoute($this['slug']);
        }

        return parent::postSave();
    }

    /**
     * Check whatever before the update.
     *
     * @param  array|null $properties Properties.
     * @return boolean
     */
    protected function postUpdate(array $properties = null)
    {
        if (!$this->locked()) {
            $this->generateObjectRoute($this['slug']);
        }

        return parent::postUpdate($properties);
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function preSave()
    {
        if (!$this->locked()) {
            $this->setSlug($this->generateSlug());
        }

        return parent::preSave();
    }

    /**
     * {@inheritdoc}
     *
     * @param array $properties Optional properties to update.
     * @return boolean
     */
    protected function preUpdate(array $properties = null)
    {
        if (!$this->locked()) {
            $this->setSlug($this->generateSlug());
        }

        return parent::preUpdate($properties);
    }

    /**
     * Event called before _deleting_ the object.
     *
     * @see    \Charcoal\Model\AbstractModel::preDelete() For the "delete" Event.
     * @return boolean
     */
    protected function preDelete()
    {
        if ($this->locked()) {
            return false;
        }
        // Routable trait
        // Remove all unnecessary routes.
        $this->deleteObjectRoutes();

        return parent::preDelete();
    }
}
