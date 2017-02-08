<?php

namespace Charcoal\Cms;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 *
 */
interface SectionInterface
{
    /**
     * @param  string $type The section type.
     * @return self
     */
    public function setSectionType($type);

    /**
     * @return string
     */
    public function sectionType();

    /**
     * @param  mixed $title Section title (localized).
     * @return self
     */
    public function setTitle($title);

    /**
     * @return Translation|string|null
     */
    public function title();

    /**
     * @param  mixed $subtitle Section subtitle (localized).
     * @return self
     */
    public function setSubtitle($subtitle);

    /**
     * @return Translation|string|null
     */
    public function subtitle();
}
