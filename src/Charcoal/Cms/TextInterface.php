<?php

namespace Charcoal\Cms;

use \Charcoal\Cms\DocumentInterface;

/**
 *
 */
interface TextInterface
{
    /**
     * @param mixed $title News title (localized).
     * @return TranslationString
     */
    public function setTitle($title);

    /**
     * @return TranslationString
     */
    public function title();

    /**
     * @param mixed $subtitle News subtitle (localized).
     * @return NewsInterface Chainable
     */
    public function setSubtitle($subtitle);

    /**
     * @return TranslationString
     */
    public function subtitle();

    /**
     * @param mixed $content News content (localized).
     * @return NewsInterface Chainable
     */
    public function setContent($content);

    /**
     * @return TranslationString
     */
    public function content();
}
