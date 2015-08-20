<?php

namespace Charcoal\Cms;

// Module `charcoal-core` dependencies
use \Charcoal\Translation\TranslationString as TranslationString;

/**
*
*/
trait MetatagTrait
{
    /**
    * @var TranslationString $_meta_title
    */
    protected $_meta_title;
    /**
    * @var TranslationString $_meta_description
    */
    protected $_meta_description;
    /**
    * @var TranslationString $_meta_image
    */
    protected $_meta_image;
    /**
    * @var TranslationString $_meta_author
    */
    protected $_meta_author;

    protected $_facebook_app_id;

    protected $_opengraph_title;
    protected $_opengraph_site_name;
    protected $_opengraph_description;
    protected $_opengraph_type;
    protected $_opengraph_image;
    protected $_opengraph_author;
    protected $_opengraph_publisher;

    /**
    * @param array $data
    * @return MetatagInterface Chainable
    */
    public function set_metatag_data(array $data)
    {
        if(isset($data['meta_title']) && $data['meta_title'] !== null) {
            $this->set_meta_title($data['meta_title']);
        }
        if(isset($data['meta_description']) && $data['meta_description'] !== null) {
            $this->set_meta_description($data['meta_description']);
        }
        if(isset($data['meta_image']) && $data['meta_image'] !== null) {
            $this->set_meta_image($data['meta_image']);
        }
        if(isset($data['meta_author']) && $data['meta_author'] !== null) {
            $this->set_meta_author($data['meta_author']);
        }
        return $this;
    }

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
        $this->_meta_title = new TranslationString($title);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function meta_title()
    {
        return $this->_meta_title();
    }

    /**
    * @param mixed $description
    * @return MetatagInterface Chainable
    */
    public function set_meta_description($description)
    {
        $this->_meta_description = new TranslationString($description);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function meta_description()
    {
        return $this->_meta_description();
    }

    /**
    * @param mixed $image
    * @return MetatagInterface Chainable
    */
    public function set_meta_image($image)
    {
        $this->_meta_image = new TranslationString($image);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function meta_image()
    {
        return $this->_meta_image();
    }

    /**
    * @param mixed $author
    * @return MetatagInterface Chainable
    */
    public function set_meta_author($author)
    {
        $this->_meta_author = new TranslationString($author);
        return $this;
    }

    /**
    * @return TranslationString
    */
    public function meta_author()
    {
        return $this->_meta_author();
    }

    /**
    * @return string
    */
    public function meta_tags()
    {
        $tags = '';
        return $tags;
    }
}
