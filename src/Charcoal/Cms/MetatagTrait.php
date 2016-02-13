<?php

namespace Charcoal\Cms;

use \Charcoal\Translation\TranslationString;

/**
*
*/
trait MetatagTrait
{
    /**
    * @var TranslationString $meta_title
    */
    private $meta_title;
    /**
    * @var TranslationString $meta_description
    */
    private $meta_description;
    /**
    * @var TranslationString $meta_image
    */
    private $meta_image;
    /**
    * @var TranslationString $meta_author
    */
    private $meta_author;

    /**
    * @var string $facebook_app_id
    */
    private $facebook_app_id;

    /**
    * @var TranslationString $opengraph_title
    */
    private $opengraph_title;

    /**
    * @var TranslationString $site_name
    */
    private $opengraph_site_name;
    private $opengraph_description;
    private $opengraph_type;
    private $opengraph_image;
    private $opengraph_author;
    private $opengraph_publisher;


    /**
    * @return string
    */
    abstract public function canonical_url();

    /**
    * @param mixed $title
    * @return MetatagInterface Chainable
    */
    public function set_meta_title($title)
    {
        $this->meta_title = new TranslationString($title);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function meta_title()
    {
        return $this->meta_title();
    }

    /**
    * @param mixed $description
    * @return MetatagInterface Chainable
    */
    public function set_meta_description($description)
    {
        $this->meta_description = new TranslationString($description);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function meta_description()
    {
        return $this->meta_description();
    }

    /**
    * @param mixed $image
    * @return MetatagInterface Chainable
    */
    public function set_meta_image($image)
    {
        $this->meta_image = new TranslationString($image);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function meta_image()
    {
        return $this->meta_image();
    }

    /**
    * @param mixed $author
    * @return MetatagInterface Chainable
    */
    public function set_meta_author($author)
    {
        $this->meta_author = new TranslationString($author);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function meta_author()
    {
        return $this->meta_author();
    }

    /**
    * @return string
    */
    public function meta_tags()
    {
        $tags = '';
        return $tags;
    }

    /**
    * @param string $app_id The facebook App ID (numeric string)
    * @return MetatagInterface Chainable
    */
    public function set_facebook_app_id($app_id)
    {
        $this->facebook_app_id = $app_id;
        return $this;
    }

    /**
    * @return string
    */
    public function facebook_app_id()
    {
        return $this->facebook_app_id;
    }

    /**
    * @param mixed $title
    * @return MetatagInterface Chainable
    */
    public function set_opengraph_title($title)
    {
        $this->opengraph_title = new TranslationString($title);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function opengraph_title()
    {
        return $this->opengraph_title;
    }

    /**
    * @param mixed $site_name
    * @return MetatagInterface Chainable
    */
    public function set_opengraph_site_name($site_name)
    {
        $this->opengraph_site_name = new TranslationString($site_name);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function opengraph_site_name()
    {
        return $this->opengraph_site_name;
    }

    /**
    * @param mixed $description
    * @return MetatagInterface Chainable
    */
    public function set_opengraph_description($description)
    {
        $this->opengraph_description = new TranslationString($description);
    }

    /**
    * @return TranslationString
    */
    public function opengraph_description()
    {
        return $this->opengraph_description;
    }

    public function set_opengraph_type($type)
    {
        $this->opengraph_type = $type;
        return $this;
    }

    public function opengraph_type()
    {
        return $this->opengraph_type;
    }

    public function set_opengraph_image($image)
    {
        $this->opengraph_image = $image;
        return $this;
    }

    public function opengraph_image()
    {
        return $this->opengraph_image;
    }

    public function set_opengraph_author($author)
    {
        $this->opengraph_author = $author;
        return $this;
    }

    public function opengraph_author()
    {
        return $this->opengraph_author;
    }

    public function set_opengraph_pulisher($publisher)
    {
        $this->opengraph_publisher = $publisher;
        return $this;
    }

    public function opengraph_publisher()
    {
        return $this->opengraph_publisher;
    }

    /**
    * @return string
    */
    public function opengraph_tags()
    {
        return '';
    }
}
