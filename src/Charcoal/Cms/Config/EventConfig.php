<?php
namespace Charcoal\Cms\Config;

// From 'charcoal-config'
use Charcoal\Config\AbstractConfig;

/**
 * Event Config
 */
class EventConfig extends AbstractConfig
{
    /**
     * @var integer
     */
    private $numPerPage;

    /**
     * @var string
     */
    private $entryCycle;

    /**
     * @var string
     */
    private $lifespan;

    /**
     * @var string
     */
    private $objType;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $configFeatIdent;

    /**
     * @var array
     */
    private $thumbnail;

    /**
     * l10n
     * @var string
     */
    private $parentSectionSlug;

    /**
     * @return integer Number of items per page.
     */
    public function numPerPage()
    {
        return $this->numPerPage;
    }

    /**
     * @return boolean Entry cycle.
     */
    public function entryCycle()
    {
        return $this->entryCycle;
    }

    /**
     * Valid DateTime string
     * @return string Event expiry.
     */
    public function lifespan()
    {
        return $this->lifespan;
    }

    /**
     * @return string Event Object type.
     */
    public function objType()
    {
        return $this->objType;
    }

    /**
     * @return string Category object.
     */
    public function category()
    {
        return $this->category;
    }

    /**
     * @return string Config property.
     */
    public function configFeatIdent()
    {
        return $this->configFeatIdent;
    }

    /**
     * @return array Thumbnail generation values.
     */
    public function thumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @return string Event parent section slug.
     */
    public function parentSectionSlug()
    {
        return $this->parentSectionSlug;
    }

    /**
     * @param integer $numPerPage Number of event per page.
     * @return EventConfig
     */
    public function setNumPerPage($numPerPage)
    {
        $this->numPerPage = $numPerPage;

        return $this;
    }

    /**
     * @param boolean $entryCycle Cycle event or not.
     * @return EventConfig
     */
    public function setEntryCycle($entryCycle)
    {
        $this->entryCycle = $entryCycle;

        return $this;
    }

    /**
     * Accept all DateTime string.
     * @param string $lifespan Event expiry.
     * @return EventConfig
     */
    public function setLifespan($lifespan)
    {
        $this->lifespan = $lifespan;

        return $this;
    }

    /**
     * @param string $objType Event object type.
     * @return EventConfig
     */
    public function setObjType($objType)
    {
        $this->objType = $objType;

        return $this;
    }

    /**
     * @param string $category Event category object.
     * @return EventConfig
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Might be overkill.
     * @param string $configFeatIdent Config property containing featured event.
     * @return EventConfig
     */
    public function setConfigFeatIdent($configFeatIdent)
    {
        $this->configFeatIdent = $configFeatIdent;

        return $this;
    }

    /**
     * resize -> width.
     * @param array $thumbnail Event thumbnail size.
     * @return EventConfig
     */
    public function setThumbnail(array $thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @param string $parentSectionSlug Event parent section (slug).
     * @return EventConfig
     */
    public function setParentSectionSlug($parentSectionSlug)
    {
        $this->parentSectionSlug = $parentSectionSlug;

        return $this;
    }
}
