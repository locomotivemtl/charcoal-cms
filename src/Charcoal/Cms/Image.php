<?php

namespace Charcoal\Cms;

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
