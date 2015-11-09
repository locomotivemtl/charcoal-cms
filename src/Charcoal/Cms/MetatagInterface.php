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
    public function canonical_url();

    /**
    * @param mixed $title
    * @return MetatagInterface Chainable
    */
    public function set_meta_title($title);

    /**
    * @return TranslationString
    */
    public function meta_title();

    /**
    * @param mixed $description
    * @return MetatagInterface Chainable
    */
    public function set_meta_description($description);

    /**
    * @return TranslationString
    */
    public function meta_description();

    /**
    * @param mixed $image
    * @return MetatageInterface Chainable
    */
    public function set_meta_image($image);

    /**
    * @return TranslationString
    */
    public function meta_image();

    /**
    * @param mixed $author
    * @return MetatagInterface Chainable
    */
    public function set_meta_author($author);

    /**
    * @return TranslationString
    */
    public function meta_author();

    /**
    * @return string
    */
    public function meta_tags();

    /**
    * @param string $app_id The facebook App ID (numeric string)
    * @return MetatagInterface Chainable
    */
    public function set_facebook_app_id($app_id);

    /**
    * @return string
    */
    public function facebook_app_id();

    /**
    * @param mixed $title
    * @return MetatagInterface Chainable
    */
    public function set_opengraph_title($title);

    /**
    * @return TranslationString
    */
    public function opengraph_title();

    /**
    * @param mixed $site_name
    * @return MetatagInterface Chainable
    */
    public function set_opengraph_site_name($site_name);

    /**
    * @return TranslationString
    */
    public function opengraph_site_name();

    /**
    * @param mixed $description
    * @return MetatagInterface Chainable
    */
    public function set_opengraph_description($description);
    public function opengraph_description();
    public function set_opengraph_type($type);
    public function opengraph_type();
    public function set_opengraph_image($image);
    public function opengraph_image();
    public function set_opengraph_author($author);
    public function opengraph_author();
    public function set_opengraph_pulisher($publisher);
    public function opengraph_publisher();

    /**
    * @return string
    */
    public function opengraph_tags();
}
