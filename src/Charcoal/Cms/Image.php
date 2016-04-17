<?php

namespace Charcoal\Cms;

use \Charcoal\Cms\AbstractImage;

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
        return 'charcoal/cms/image-category';
    }
}
