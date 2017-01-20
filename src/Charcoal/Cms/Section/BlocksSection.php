<?php

namespace Charcoal\Cms\Section;

// Parent namespace dependencies
use Charcoal\Cms\AbstractSection;

/**
 * Blocks-content section
 */
class BlocksSection extends AbstractSection
{
    /**
     * @var Collection $blocks
     */
    private $blocks;

    /**
     * Overrides `AbstractSection::section_type()`
     *
     * @return string
     */
    public function sectionType()
    {
        return AbstractSection::TYPE_BLOCKS;
    }

    /**
     * @return Collection List of `Block` objects
     */
    public function blocks()
    {
        if ($this->blocks === null) {
            $this->blocks = $this->loadBlocks();
        }
        return $this->blocks;
    }

    /**
     * @return Collection
     */
    public function loadBlocks()
    {
        // @todo
        return [];
    }
}
