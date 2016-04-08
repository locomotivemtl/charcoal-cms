<?php

namespace Charcoal\Cms;

use \Charcoal\Translation\TranslationString;

/**
*
*/
trait MetatagTrait
{
    /**
    * @var TranslationString $metaTitle
    */
    private $metaTitle;
    /**
    * @var TranslationString $metaDescription
    */
    private $metaDescription;
    /**
    * @var TranslationString $metaImage
    */
    private $metaImage;
    /**
    * @var TranslationString $metaAuthor
    */
    private $metaAuthor;

    /**
    * @var string $facebookAppId
    */
    private $facebookAppId;

    /**
    * @var TranslationString $opengraphTitle
    */
    private $opengraphTitle;

    /**
    * @var TranslationString $siteName
    */
    private $opengraphSiteName;
    private $opengraphDescription;
    private $opengraphType;
    private $opengraphImage;
    private $opengraphAuthor;
    private $opengraphPublisher;


    /**
    * @return string
    */
    abstract public function canonicalUrl();

    /**
    * @param mixed $title
    * @return MetatagInterface Chainable
    */
    public function setMetaTitle($title)
    {
        $this->metaTitle = new TranslationString($title);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function metaTitle()
    {
        return $this->metaTitle();
    }

    /**
    * @param mixed $description
    * @return MetatagInterface Chainable
    */
    public function setMetaDescription($description)
    {
        $this->metaDescription = new TranslationString($description);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function metaDescription()
    {
        return $this->metaDescription();
    }

    /**
    * @param mixed $image
    * @return MetatagInterface Chainable
    */
    public function setMetaImage($image)
    {
        $this->metaImage = new TranslationString($image);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function metaImage()
    {
        return $this->metaImage();
    }

    /**
    * @param mixed $author
    * @return MetatagInterface Chainable
    */
    public function setMetaAuthor($author)
    {
        $this->metaAuthor = new TranslationString($author);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function metaAuthor()
    {
        return $this->metaAuthor();
    }

    /**
    * @return string
    */
    public function metaTags()
    {
        $tags = '';
        return $tags;
    }

    /**
    * @param string $appId The facebook App ID (numeric string)
    * @return MetatagInterface Chainable
    */
    public function setFacebookAppId($appId)
    {
        $this->facebookAppId = $appId;
        return $this;
    }

    /**
    * @return string
    */
    public function facebookAppId()
    {
        return $this->facebookAppId;
    }

    /**
    * @param mixed $title
    * @return MetatagInterface Chainable
    */
    public function setOpengraphTitle($title)
    {
        $this->opengraphTitle = new TranslationString($title);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function opengraphTitle()
    {
        return $this->opengraphTitle;
    }

    /**
    * @param mixed $siteName
    * @return MetatagInterface Chainable
    */
    public function setOpengraphSiteName($siteName)
    {
        $this->opengraphSiteName = new TranslationString($siteName);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function opengraphSiteName()
    {
        return $this->opengraphSiteName;
    }

    /**
    * @param mixed $description
    * @return MetatagInterface Chainable
    */
    public function setOpengraphDescription($description)
    {
        $this->opengraphDescription = new TranslationString($description);
    }

    /**
    * @return TranslationString
    */
    public function opengraphDescription()
    {
        return $this->opengraphDescription;
    }

    public function setOpengraphType($type)
    {
        $this->opengraphType = $type;
        return $this;
    }

    public function opengraphType()
    {
        return $this->opengraphType;
    }

    public function setOpengraphImage($image)
    {
        $this->opengraphImage = $image;
        return $this;
    }

    public function opengraphImage()
    {
        return $this->opengraphImage;
    }

    public function setOpengraphAuthor($author)
    {
        $this->opengraphAuthor = $author;
        return $this;
    }

    public function opengraphAuthor()
    {
        return $this->opengraphAuthor;
    }

    public function setOpengraphPulisher($publisher)
    {
        $this->opengraphPublisher = $publisher;
        return $this;
    }

    public function opengraphPublisher()
    {
        return $this->opengraphPublisher;
    }

    /**
    * @return string
    */
    public function opengraphTags()
    {
        return '';
    }
}
