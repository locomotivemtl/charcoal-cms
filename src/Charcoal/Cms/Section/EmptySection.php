<?php

namespace Charcoal\Cms;

// From 'charcoal-cms'
use Charcoal\Cms\AbstractSection;
use Charcoal\Cms\Mixin\EmptySectionInterface;

/**
 * Empty section
 *
 * Empty sections are linked to a static template and do not provide any dynamic content
 * (except standard section metadata).
 */
class EmptySection extends AbstractSection
{
}
