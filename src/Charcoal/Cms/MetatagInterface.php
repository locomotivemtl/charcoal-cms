<?php

namespace Charcoal\Cms;

/**
*
*/
interface MetatagInterface
{
    /**
    * @param array $data
    * @return MetatagInterface Chainable
    */
    public function set_metatag_data(array $data);

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

    public function set_meta_author($author);
    public function meta_author();

    /**
    * @return string
    */
    public function meta_tags();

    public function set_facebook_app_id($app_id);
    public function facebook_app_id();

    public function set_opengraph_title($title);
    public function opengraph_title();
    public function set_opengraph_site_name($site_name);
    public function opengraph_site_name();
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
