<?php

namespace Charcoal\Cms;

/**
 *
 */
interface MetatagInterface
{
    /**
     * @return string
     */
    public function canonicalUrl();

    /**
     * @return TranslationString
     */
    public function defaultMetaTitle();

    /**
     * @return TranslationString
     */
    public function defaultMetaDescription();

    /**
     * @return TranslationString
     */
    public function defaultMetaImage();

    /**
     * @param mixed $title The meta title (localized).
     * @return MetatagInterface Chainable
     */
    public function setMetaTitle($title);

    /**
     * @return TranslationString
     */
    public function metaTitle();

    /**
     * @param mixed $description The meta description (localized).
     * @return MetatagInterface Chainable
     */
    public function setMetaDescription($description);

    /**
     * @return TranslationString
     */
    public function metaDescription();

    /**
     * @param mixed $image The meta image (localized).
     * @return MetatageInterface Chainable
     */
    public function setMetaImage($image);

    /**
     * @return TranslationString
     */
    public function metaImage();

    /**
     * @param mixed $author The meta author (localized).
     * @return MetatagInterface Chainable
     */
    public function setMetaAuthor($author);

    /**
     * @return TranslationString
     */
    public function metaAuthor();

    /**
     * @return string
     */
    public function metaTags();

    /**
     * @param string $appId The facebook App ID (numeric string).
     * @return MetatagInterface Chainable
     */
    public function setFacebookAppId($appId);

    /**
     * @return string
     */
    public function facebookAppId();

    /**
     * @param mixed $title The opengraph title (localized).
     * @return MetatagInterface Chainable
     */
    public function setOpengraphTitle($title);

    /**
     * @return TranslationString
     */
    public function opengraphTitle();

    /**
     * @param mixed $siteName The opengraph site name (localized).
     * @return MetatagInterface Chainable
     */
    public function setOpengraphSiteName($siteName);

    /**
     * @return TranslationString
     */
    public function opengraphSiteName();

    /**
     * @param mixed $description The opengraph description (localized).
     * @return MetatagInterface Chainable
     */
    public function setOpengraphDescription($description);

    /**
     * @return TranslationString
     */
    public function opengraphDescription();

    /**
     * @param string $type The opengraph type.
     * @return MetatagInterface Chainable
     */
    public function setOpengraphType($type);

    /**
     * @return string
     */
    public function opengraphType();

    /**
     * @param mixed $image The opengraph image (localized).
     * @return MetatagInterface Chainable
     */
    public function setOpengraphImage($image);

    /**
     * @return TranslationString
     */
    public function opengraphImage();

    /**
     * @param mixed $author The opengraph author (localized).
     * @return MetatagInterface Chainable
     */
    public function setOpengraphAuthor($author);

    /**
     * @return TranslationString
     */
    public function opengraphAuthor();

    /**
     * @param mixed $publisher The opengraph publisher (localized).
     * @return MetatagInterface
     */
    public function setOpengraphPulisher($publisher);

    /**
     * @return TranslationString
     */
    public function opengraphPublisher();

    /**
     * @return string
     */
    public function opengraphTags();
}
