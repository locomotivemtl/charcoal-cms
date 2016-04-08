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
    * @param mixed $title
    * @return MetatagInterface Chainable
    */
    public function setMetaTitle($title);

    /**
    * @return TranslationString
    */
    public function metaTitle();

    /**
    * @param mixed $description
    * @return MetatagInterface Chainable
    */
    public function setMetaDescription($description);

    /**
    * @return TranslationString
    */
    public function metaDescription();

    /**
    * @param mixed $image
    * @return MetatageInterface Chainable
    */
    public function setMetaImage($image);

    /**
    * @return TranslationString
    */
    public function metaImage();

    /**
    * @param mixed $author
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
    * @param string $appId The facebook App ID (numeric string)
    * @return MetatagInterface Chainable
    */
    public function setFacebookAppId($appId);

    /**
    * @return string
    */
    public function facebookAppId();

    /**
    * @param mixed $title
    * @return MetatagInterface Chainable
    */
    public function setOpengraphTitle($title);

    /**
    * @return TranslationString
    */
    public function opengraphTitle();

    /**
    * @param mixed $siteName
    * @return MetatagInterface Chainable
    */
    public function setOpengraphSiteName($siteName);

    /**
    * @return TranslationString
    */
    public function opengraphSiteName();

    /**
    * @param mixed $description
    * @return MetatagInterface Chainable
    */
    public function setOpengraphDescription($description);
    public function opengraphDescription();
    public function setOpengraphType($type);
    public function opengraphType();
    public function setOpengraphImage($image);
    public function opengraphImage();
    public function setOpengraphAuthor($author);
    public function opengraphAuthor();
    public function setOpengraphPulisher($publisher);
    public function opengraphPublisher();

    /**
    * @return string
    */
    public function opengraphTags();
}
