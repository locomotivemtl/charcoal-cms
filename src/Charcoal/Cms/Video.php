<?php

namespace Charcoal\Cms;

// From 'charcoal-cms'
use Charcoal\Cms\AbstractVideo;
use Charcoal\Cms\VideoCategory;

/**
 *
 */
final class Video extends AbstractVideo
{
    /**
     * @return string
     */
    public function categoryType()
    {
        return VideoCategory::class;
    }
}
