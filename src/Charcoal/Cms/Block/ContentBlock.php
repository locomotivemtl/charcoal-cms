<?php

namespace Charcoal\Cms\Block;

use \Charcoal\Cms\AbstractBlock;

class ContentBlock extends AbstractBlock
{
    /**
    * @return string
    */
    public function block_type()
    {
        return AbstractBlock::TYPE_CONTENT;
    }
}
