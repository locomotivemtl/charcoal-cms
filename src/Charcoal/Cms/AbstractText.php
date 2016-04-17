<?php

namespace Charcoal\Cms;

// Dependencies from `charcoal-translation`
use \Charcoal\Translation\TranslationString;

// Module `charcoal-base` dependencies
use \Charcoal\Object\Content;
use \Charcoal\Object\CategorizableInterface;
use \Charcoal\Object\CategorizableTrait;
use \Charcoal\Object\PublishableInterface;
use \Charcoal\Object\PublishableTrait;

// Intra-module (`charcoal-cms`) dependencies
use \Charcoal\Cms\SearchableInterface;
use \Charcoal\Cms\SearchableTrait;
use \Charcoal\Cms\TextInterface;

/**
 * Text
 */
abstract class AbstractText extends Content implements
    CategorizableInterface,
    PublishableInterface,
    SearchableInterface,
    TextInterface
{
    use CategorizableTrait;
    use PublishableTrait;
    use SearchableTrait;

    /**
     * @var TranslationString $title
     */
    private $title;

    /**
     * @var TranslationString $title
     */
    private $subtitle;

    /**
     * @var TranslationString $content
     */
    private $content;

    /**
     * @param mixed $title The news title (localized).
     * @return TranslationString
     */
    public function setTitle($title)
    {
        $this->title = new TranslationString($title);
        return $this;
    }

    /**
     * @return TranslationString
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * @param mixed $subtitle The news subtitle (localized).
     * @return Event Chainable
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = new TranslationString($subtitle);
        return $this;
    }

    /**
     * @return TranslationString
     */
    public function subtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param mixed $content The news content (localized).
     * @return Event Chainable
     */
    public function setContent($content)
    {
        $this->content = new TranslationString($content);
        return $this;
    }

    /**
     * @return TranslationString
     */
    public function content()
    {
        return $this->content;
    }
}
