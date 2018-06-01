<?php

namespace Charcoal\Cms\Support\Interfaces;

// From 'charcoal-cms'
use Charcoal\Cms\SectionInterface;

interface SectionLoaderAwareInterface
{
    /**
     * @return \ArrayAccess|\Traversable
     */
    public function sections();

    /**
     * @return \ArrayAccess|\Traversable
     */
    public function masterSections();

    /**
     * @return \ArrayAccess|\Traversable
     */
    public function childrenSections();

    /**
     * Gets the latest route for the given slug.
     * @return \ArrayAccess|\Traversable
     */
    public function routes();

    /**
     * Gets the current section based on AbstractTemplate::Section and context.
     * @param boolean $raw Option the receive the non-formatted section.
     * @return array|SectionInterface
     */
    public function currentSection($raw = false);

    /**
     * @param string $slug The section slug to load from.
     * @return SectionInterface|array
     */
    public function sectionFromSlug($slug);
}
