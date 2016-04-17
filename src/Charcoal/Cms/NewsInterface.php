<?php

namespace Charcoal\Cms;

/**
 *
 */
interface NewsInterface
{
    /**
     * @param mixed $title News title (localized).
     * @return TranslationString
     */
    public function setTitle($title);

    /**
     * @return TranslationString
     */
    public function title();

    /**
     * @param mixed $subtitle News subtitle (localized).
     * @return NewsInterface Chainable
     */
    public function setSubtitle($subtitle);

    /**
     * @return TranslationString
     */
    public function subtitle();

    /**
     * @param mixed $content News content (localized).
     * @return NewsInterface Chainable
     */
    public function setContent($content);

    /**
     * @return TranslationString
     */
    public function content();

    /**
     * @param mixed $newsDate The news date.
     * @return ObjectRevision Chainable
     */
    public function setNewsDate($newsDate);

    /**
     * @return DateTime|null
     */
    public function newsDate();

    /**
     * @param mixed $url The URL that provides additional information ont he news (localized).
     * @return NewsInterface Chainable
     */
    public function setInfoUrl($url);

    /**
     * @return TranslationString
     */
    public function infoUrl();
}
