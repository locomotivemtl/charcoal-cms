<?php

namespace Charcoal\Cms;

// From 'charcoal-object'
use Charcoal\Object\ContentInterface;
use Charcoal\Object\CategorizableInterface;
use Charcoal\Object\PublishableInterface;
use Charcoal\Object\RoutableInterface;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * CMS News
 */
interface NewsInterface extends
    CategorizableInterface,
    ContentInterface,
    PublishableInterface,
    RoutableInterface,
    MetatagInterface,
    SearchableInterface,
    TaggableInterface,
    TemplateableInterface
{
    /**
     * @param  mixed $title News title (localized).
     * @return self
     */
    public function setTitle($title);

    /**
     * @return Translation|string|null
     */
    public function title();

    /**
     * @param  mixed $subtitle News subtitle (localized).
     * @return self
     */
    public function setSubtitle($subtitle);

    /**
     * @return Translation|string|null
     */
    public function subtitle();

    /**
     * @param  mixed $content News content (localized).
     * @return self
     */
    public function setContent($content);

    /**
     * @return Translation|string|null
     */
    public function content();

    /**
     * @param  mixed $newsDate The news date.
     * @return self
     */
    public function setNewsDate($newsDate);

    /**
     * @return \DateTimeInterface|null
     */
    public function newsDate();

    /**
     * @param  mixed $url The URL that provides additional information ont he news (localized).
     * @return self
     */
    public function setInfoUrl($url);

    /**
     * @return Translation|string|null
     */
    public function infoUrl();
}
