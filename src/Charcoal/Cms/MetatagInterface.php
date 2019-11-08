<?php

namespace Charcoal\Cms;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 *
 */
interface MetatagInterface
{
    const DEFAULT_OPENGRAPH_TYPE = 'website';

    /**
     * @return string
     */
    public function canonicalUrl();

    /**
     * @return Translation|string|null
     */
    public function defaultMetaTitle();

    /**
     * @return Translation|string|null
     */
    public function defaultMetaDescription();

    /**
     * @return Translation|string|null
     */
    public function defaultMetaImage();

    /**
     * @param  mixed $title The meta title (localized).
     * @return self
     */
    public function setMetaTitle($title);

    /**
     * @return Translation|string|null
     */
    public function metaTitle();

    /**
     * @param  mixed $description The meta description (localized).
     * @return self
     */
    public function setMetaDescription($description);

    /**
     * @return Translation|string|null
     */
    public function metaDescription();

    /**
     * @param  mixed $image The meta image (localized).
     * @return self
     */
    public function setMetaImage($image);

    /**
     * @return Translation|string|null
     */
    public function metaImage();

    /**
     * @param  mixed $author The meta author (localized).
     * @return self
     */
    public function setMetaAuthor($author);

    /**
     * @return Translation|string|null
     */
    public function metaAuthor();

    /**
     * @param  mixed $tags The meta tags (localized).
     * @return self
     */
    public function setMetaTags($tags);

    /**
     * @return string
     */
    public function metaTags();

    /**
     * @param  string $appId The facebook App ID (numeric string).
     * @return self
     */
    public function setFacebookAppId($appId);

    /**
     * @return string
     */
    public function facebookAppId();

    /**
     * @param  mixed $title The opengraph title (localized).
     * @return self
     */
    public function setOpengraphTitle($title);

    /**
     * @return Translation|string|null
     */
    public function opengraphTitle();

    /**
     * @param  mixed $siteName The opengraph site name (localized).
     * @return self
     */
    public function setOpengraphSiteName($siteName);

    /**
     * @return Translation|string|null
     */
    public function opengraphSiteName();

    /**
     * @param  mixed $description The opengraph description (localized).
     * @return self
     */
    public function setOpengraphDescription($description);

    /**
     * @return Translation|string|null
     */
    public function opengraphDescription();

    /**
     * @param  string $type The opengraph type.
     * @return self
     */
    public function setOpengraphType($type);

    /**
     * @return string
     */
    public function opengraphType();

    /**
     * @param  mixed $image The opengraph image (localized).
     * @return self
     */
    public function setOpengraphImage($image);

    /**
     * @return Translation|string|null
     */
    public function opengraphImage();

    /**
     * @param  mixed $author The opengraph author (localized).
     * @return self
     */
    public function setOpengraphAuthor($author);

    /**
     * @return Translation|string|null
     */
    public function opengraphAuthor();

    /**
     * @param  mixed $publisher The opengraph publisher (localized).
     * @return self
     */
    public function setOpengraphPublisher($publisher);

    /**
     * @return Translation|string|null
     */
    public function opengraphPublisher();

    /**
     * @param  string $type The twitter card type.
     * @return self
     */
    public function setTwitterCardType($type);

    /**
     * Retrieve the object's {@link https://dev.twitter.com/cards/types card type},
     * for the "twitter:card" meta-property.
     *
     * @return string|null
     */
    public function twitterCardType();

    /**
     * @param  mixed $image The twitter card image (localized).
     * @return self
     */
    public function setTwitterCardImage($image);

    /**
     * Retrieve the URL to the object's social image for the "twitter:image" meta-property.
     *
     * @return string|null
     */
    public function twitterCardImage();
}
