<?php

namespace Charcoal\Cms;

use \Charcoal\Cms\AbstractDocument;

/**
 *
 */
final class Document extends AbstractDocument
{
    /**
     * @return string
     */
    public function categoryType()
    {
        return 'charcoal/cms/document-category';
    }
}
