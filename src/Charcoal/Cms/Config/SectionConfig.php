<?php
namespace Charcoal\Cms\Config;

// dependencies from `charcoal-config`
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
     * @return string Section object type.
     */
    public function objType()
    {
        return $this->objType;
    }
}