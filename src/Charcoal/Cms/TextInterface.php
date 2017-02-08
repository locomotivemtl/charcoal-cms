<?php

namespace Charcoal\Cms;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 *
 */
interface TextInterface
{
    /**
     * @param  mixed $title Text title (localized).
     * @return self
     */
    public function setTitle($title);

    /**
     * @return Translation|string|null
     */
    public function title();

    /**
     * @param mixed $subtitle Text subtitle (localized).
     * @return self
     */
    public function setSubtitle($subtitle);

    /**
     * @return Translation|string|null
     */
    public function subtitle();

    /**
     * @param  mixed $content Text content (localized).
     * @return self
     */
    public function setContent($content);

    /**
     * @return Translation|string|null
     */
    public function content();
}
