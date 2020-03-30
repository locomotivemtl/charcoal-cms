<?php

namespace Charcoal\Cms;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;
use Charcoal\Translator\Translator;

/**
 *
 */
trait MetatagTrait
{
    /**
     * @var Translation|string|null
     */
    protected $metaTitle;

    /**
     * @var Translation|string|null
     */
    protected $metaDescription;

    /**
     * @var Translation|string|null
     */
    protected $metaImage;

    /**
     * @var Translation|string|null
     */
    protected $metaAuthor;

    /**
     * @var Translation|string|null
     */
    protected $metaTags;

    /**
     * @var string $facebookAppId
     */
    protected $facebookAppId;

    /**
     * @var Translation|string|null
     */
    protected $opengraphTitle;

    /**
     * @var Translation|string|null
     */
    protected $opengraphSiteName;

    /**
     * @var Translation|string|null
     */
    protected $opengraphDescription;

    /**
     * @var string
     */
    protected $opengraphType;

    /**
     * @var Translation|string|null
     */
    protected $opengraphImage;

    /**
     * @var Translation|string|null
     */
    protected $opengraphAuthor;

    /**
     * @var Translation|string|null
     */
    protected $opengraphPublisher;

    /**
     * @var Translation|string|null
     */
    protected $opengraphTags;

    /**
     * @var Translation|string|null
     */
    protected $twitterCardImage;

    /**
     * @var Translation|string|null
     */
    protected $twitterCardType;

    /**
     * @return string
     */
    abstract public function canonicalUrl();

    /**
     * @return Translation|string|null
     */
    abstract public function defaultMetaTitle();

    /**
     * @return Translation|string|null
     */
    abstract public function defaultMetaDescription();

    /**
     * @return Translation|string|null
     */
    abstract public function defaultMetaImage();

    /**
     * @param  mixed $title The meta tile (localized).
     * @return self
     */
    public function setMetaTitle($title)
    {
        $this->metaTitle = $this->translator()->translation($title);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function metaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param  mixed $description The meta description (localized).
     * @return self
     */
    public function setMetaDescription($description)
    {
        $this->metaDescription = $this->translator()->translation($description);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function metaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param  mixed $image The meta image (localized).
     * @return self
     */
    public function setMetaImage($image)
    {
        $this->metaImage = $this->translator()->translation($image);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function metaImage()
    {
        return $this->metaImage;
    }

    /**
     * @param  mixed $author The meta author (localized).
     * @return self
     */
    public function setMetaAuthor($author)
    {
        $this->metaAuthor = $this->translator()->translation($author);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function metaAuthor()
    {
        return $this->metaAuthor;
    }

    /**
     * @param  string $tags Comma separated list of tags.
     * @return string
     */
    public function setMetaTags($tags)
    {
        $this->metaTags = $this->translator()->translation($tags);
        return $this;
    }

    /**
     * @return string
     */
    public function metaTags()
    {
        return $this->metaTags;
    }

    /**
     * @param  string $appId The facebook App ID (numeric string).
     * @return self
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
     * @param  mixed $title The opengraph title (localized).
     * @return self
     */
    public function setOpengraphTitle($title)
    {
        $this->opengraphTitle = $this->translator()->translation($title);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function opengraphTitle()
    {
        return $this->opengraphTitle;
    }

    /**
     * @param  mixed $siteName The site name (localized).
     * @return self
     */
    public function setOpengraphSiteName($siteName)
    {
        $this->opengraphSiteName = $this->translator()->translation($siteName);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function opengraphSiteName()
    {
        return $this->opengraphSiteName;
    }

    /**
     * @param  mixed $description The opengraph description (localized).
     * @return self
     */
    public function setOpengraphDescription($description)
    {
        $this->opengraphDescription = $this->translator()->translation($description);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function opengraphDescription()
    {
        return $this->opengraphDescription;
    }

    /**
     * @param  string $type The opengraph type.
     * @return self
     */
    public function setOpengraphType($type)
    {
        $this->opengraphType = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function opengraphType()
    {
        return $this->opengraphType;
    }

    /**
     * @param  mixed $image The opengraph image (localized).
     * @return self
     */
    public function setOpengraphImage($image)
    {
        $this->opengraphImage = $this->translator()->translation($image);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function opengraphImage()
    {
        return $this->opengraphImage;
    }

    /**
     * @param  mixed $author The opengraph author (localized).
     * @return self
     */
    public function setOpengraphAuthor($author)
    {
        $this->opengraphAuthor = $this->translator()->translation($author);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function opengraphAuthor()
    {
        return $this->opengraphAuthor;
    }

    /**
     * @param  mixed $publisher The opengraph publisher (localized).
     * @return self
     */
    public function setOpengraphPublisher($publisher)
    {
        $this->opengraphPublisher = $this->translator()->translation($publisher);
        return $this;
    }

    /**
     * @return Translation|string|null
     */
    public function opengraphPublisher()
    {
        return $this->opengraphPublisher;
    }

    /**
     * @param  string $type The twitter card type.
     * @return self
     */
    public function setTwitterCardType($type)
    {
        $this->twitterCardType = $type;
        return $this;
    }

    /**
     * Retrieve the object's {@link https://dev.twitter.com/cards/types card type},
     * for the "twitter:card" meta-property.
     *
     * @return string|null
     */
    public function twitterCardType()
    {
        return $this->twitterCardType;
    }

    /**
     * @param  mixed $image The twitter card image (localized).
     * @return self
     */
    public function setTwitterCardImage($image)
    {
        $this->twitterCardImage = $this->translator()->translation($image);
        return $this;
    }

    /**
     * Retrieve the URL to the object's social image for the "twitter:image" meta-property.
     *
     * @return string|null
     */
    public function twitterCardImage()
    {
        return $this->twitterCardImage;
    }

    /**
     * Generates the default metatags for the current object.
     * Prevents some problem where the defaultMetaTag method
     * content isn't set at the moment of setting the meta.
     * Should be called on preSave and preUpdate of the object.
     *
     * @return self $this.
     */
    public function generateDefaultMetaTags()
    {
        if ($this->isEmptyMeta($this->metaTitle)) {
            $this->setMetaTitle($this->defaultMetaTitle());
        }
        if ($this->isEmptyMeta($this->metaDescription)) {
            $this->setMetaDescription($this->defaultMetaDescription());
        }
        if ($this->isEmptyMeta($this->metaImage)) {
            $this->setMetaImage($this->defaultMetaImage());
        }
        return $this;
    }

    /**
     * Check if the meta is empty. Method exists
     * because at this point we don't really know
     * what's in the meta.
     * Possible param type:
     * - [ lang => value, lang => value ]
     * - Translation
     * - null
     *
     * @param  mixed $meta Current meta value.
     * @return boolean       Empty or not.
     */
    public function isEmptyMeta($meta)
    {
        if (!$meta) {
            return true;
        }

        // From back-end form which post meta_description[lang]=value
        // Gives [ lang => value ] as value
        if (is_array($meta)) {
            $meta = $this->translator()->translation($meta);
        }

        // If one value is set in whatever language,
        // this is NOT empty.
        if ($meta instanceof Translation) {
            $empty = true;
            foreach ($meta->data() as $lang => $val) {
                if ($val && $val != '') {
                    $empty = false;
                }
            }
            return $empty;
        }
        return true;
    }

    /**
     * @return Translator
     */
    abstract protected function translator();
}
