<?php

namespace Charcoal\Cms\Section;

// From 'charcoal-cms'
use Charcoal\Cms\AbstractSection;
use Charcoal\Cms\Mixin\ContentSectionInterface;

/**
 * Content section
 */
class ContentSection extends AbstractSection
{
    /**
     * @return string
     */
    public function sectionType()
    {
        return AbstractSection::TYPE_CONTENT;
    }
}
