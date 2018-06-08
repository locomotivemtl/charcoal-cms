<?php
namespace Charcoal\Cms\Config;

// From 'charcoal-config'
use Charcoal\Config\AbstractConfig;

/**
 * Section Config
 */
class SectionConfig extends AbstractConfig
{
    /**
     * @var mixed
     */
    private $baseSection;

    /**
     * Different section type available
     * They should be extending the baseSection
     * @var mixed
     */
    private $sectionTypes;

    /**
     * @var string
     */
    private $objType;

    /**
     * Base section ID.
     * Used in section loader to retrieve sections
     * relative to this specific section. (usually: home)
     * @param mixed $baseSection Base section ID.
     * @return SectionConfig
     */
    public function setBaseSection($baseSection)
    {
        $this->baseSection = $baseSection;

        return $this;
    }

    /**
     * Set the available section types
     *
     * @param  mixed $sectionTypes Section types.
     * @return SectionConfig
     */
    public function setSectionTypes($sectionTypes)
    {
        $this->sectionTypes = $sectionTypes;
        return $this;
    }

    /**
     * @param string $objType Section object type.
     * @return SectionConfig
     */
    public function setObjType($objType)
    {
        $this->objType = $objType;

        return $this;
    }

    /**
     * @return mixed ID to base section.
     */
    public function baseSection()
    {
        return $this->baseSection;
    }

    /**
     * Available section types.
     * @return mixed Section types. Could be null.
     */
    public function sectionTypes()
    {
        return $this->sectionTypes;
    }

    /**
     * @return string Section object type.
     */
    public function objType()
    {
        return $this->objType;
    }
}
