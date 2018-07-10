<?php

namespace Charcoal\Cms;

// From 'charcoal-object'
use Charcoal\Object\ContentInterface;
use Charcoal\Object\HierarchicalInterface;
use Charcoal\Object\RoutableInterface;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 *
 */
interface SectionInterface extends
    ContentInterface,
    HierarchicalInterface,
    RoutableInterface,
    MetatagInterface,
    SearchableInterface,
    TemplateableInterface
{
    /**
     * @param string $type The section type.
     * @return self
     */
    public function setSectionType($type);

    /**
     * @return string
     */
    public function sectionType();

    /**
     * @param mixed $title Section title (localized).
     * @return self
     */
    public function setTitle($title);

    /**
     * @return Translation|string|null
     */
    public function title();

    /**
     * @param mixed $subtitle Section subtitle (localized).
     * @return self
     */
    public function setSubtitle($subtitle);

    /**
     * @return Translation|string|null
     */
    public function subtitle();

    /**
     * Set the menus this object belongs to.
     *
     * @param  string|string[] $menu One or more menu identifiers.
     * @return self
     */
    public function setInMenu($menu);

    /**
     * Set the object's keywords.
     *
     * @param  string|string[] $keywords One or more entries.
     * @return self
     */
    public function setKeywords($keywords);

    /**
     * @param Translation|string $summary The summary.
     * @return self
     */
    public function setSummary($summary);

    /**
     * @param Translation|string $externalUrl The external url.
     * @return self
     */
    public function setExternalUrl($externalUrl);

    /**
     * Section is locked when you can't change the URL
     * @param boolean $locked Prevent new route creation about that object.
     * @return self
     */
    public function setLocked($locked);

    /**
     * @param mixed $content The section content (localized).
     * @return self
     */
    public function setContent($content);

    /**
     * @param mixed $image The section main image (localized).
     * @return self
     */
    public function setImage($image);

    /**
     * @return Translation
     */
    public function content();

    /**
     * @return Translation
     */
    public function image();

    /**
     * Retrieve the menus this object belongs to.
     *
     * @return string|Translation
     */
    public function inMenu();

    /**
     * Retrieve the object's keywords.
     *
     * @return string[]
     */
    public function keywords();

    /**
     * @return Translation
     */
    public function summary();

    /**
     * @return string
     */
    public function externalUrl();

    /**
     * @return boolean Or Null.
     */
    public function locked();

    /**
     * MetatagTrait > canonicalUrl
     *
     * @return string
     * @todo
     */
    public function canonicalUrl();

    /**
     * @return Translation
     */
    public function defaultMetaTitle();

    /**
     * @return Translation
     */
    public function defaultMetaDescription();

    /**
     * @return Translation
     */
    public function defaultMetaImage();
}
