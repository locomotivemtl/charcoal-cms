<?php

namespace Charcoal\Cms;

// From 'charcoal-cms'
use Charcoal\Cms\AbstractImage;
use Charcoal\Cms\ImageCategory;

/**
 *
 */
final class Image extends AbstractImage
{
    /**
     * @return string
     */
    public function categoryType()
    {
        return ImageCategory::Class;
    }
}
