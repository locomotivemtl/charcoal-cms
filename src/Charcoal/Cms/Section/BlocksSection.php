<?php

namespace Charcoal\Cms\Section;

// Parent namespace dependencies
use \Charcoal\Cms\AbstractSection;

/**
*
*/
class BlocksSection extends AbstractSection
{
    /**
    * @param Collection $blocks
    */
    private $blocks;

    /**
    * Overrides `AbstractSection::section_type()`
    *
    * @return string
    */
    public function section_type()
    {
        return AbstractSection::TYPE_BLOCKS;
    }

    /**
    * @return Collection List of `Block` objects
    */
    public function blocks()
    {
        if ($this->blocks === null) {
            $this->blocks = $this->load_blocks();
        }
        return $this->blocks;
    }

    public function load_blocks()
    {
        // @todo
    }
}
