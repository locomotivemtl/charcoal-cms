<?php
namespace Charcoal\Cms\Config;

// From 'charcoal-config'
use Charcoal\Config\AbstractConfig;

/**
 * News Config
 */
class NewsConfig extends AbstractConfig
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
    private $defaultExpiry;

    /**
     * @var string
     */
    private $median;

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
     * @return string News expiry.
     */
    public function defaultExpiry()
    {
        return $this->defaultExpiry;
    }

    /**
     * @return string Datetime value.
     */
    public function median()
    {
        return $this->median;
    }

    /**
     * @return string News Object type.
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
     * @return string News parent section slug.
     */
    public function parentSectionSlug()
    {
        return $this->parentSectionSlug;
    }

    /**
     * @param integer $numPerPage Number of news per page.
     * @return NewsConfig
     */
    public function setNumPerPage($numPerPage)
    {
        $this->numPerPage = $numPerPage;

        return $this;
    }

    /**
     * @param boolean $entryCycle Cycle news or not.
     * @return NewsConfig
     */
    public function setEntryCycle($entryCycle)
    {
        $this->entryCycle = $entryCycle;

        return $this;
    }

    /**
     * Accept all DateTime string.
     * @param string $defaultExpiry Expiry.
     * @return NewsConfig
     */
    public function setDefaultExpiry($defaultExpiry)
    {
        $this->defaultExpiry = $defaultExpiry;

        return $this;
    }

    /**
     * @param string $median DateTime string.
     * @return NewsConfig
     */
    public function setMedian($median)
    {
        $this->median = $median;

        return $this;
    }

    /**
     * @param string $objType News object type.
     * @return NewsConfig
     */
    public function setObjType($objType)
    {
        $this->objType = $objType;

        return $this;
    }

    /**
     * @param string $category News category object.
     * @return NewsConfig
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Might be overkill.
     * @param string $configFeatIdent Config property containing featured news.
     * @return NewsConfig
     */
    public function setConfigFeatIdent($configFeatIdent)
    {
        $this->configFeatIdent = $configFeatIdent;

        return $this;
    }

    /**
     * resize -> width.
     * @param array $thumbnail News thumbnail size.
     * @return NewsConfig
     */
    public function setThumbnail(array $thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @param string $parentSectionSlug News parent section (slug).
     * @return NewsConfig
     */
    public function setParentSectionSlug($parentSectionSlug)
    {
        $this->parentSectionSlug = $parentSectionSlug;

        return $this;
    }
}
