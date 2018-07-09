<?php

namespace Charcoal\Cms;

use DateTimeInterface;

// From 'charcoal-object'
use Charcoal\Object\ContentInterface;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 *
 */
interface EventInterface  extends ContentInterface
{
    /**
     * @param  mixed $title Event title (localized).
     * @return self
     */
    public function setTitle($title);

    /**
     * @return Translation|string|null
     */
    public function title();

    /**
     * @param  mixed $subtitle Event subtitle (localized).
     * @return self
     */
    public function setSubtitle($subtitle);

    /**
     * @return Translation|string|null
     */
    public function subtitle();

    /**
     * @param  mixed $content Event content (localized).
     * @return self
     */
    public function setContent($content);

    /**
     * @return Translation|string|null
     */
    public function content();

    /**
     * @param  string|DateTimeInterface $startDate Event starting date.
     * @return self
     */
    public function setStartDate($startDate);

    /**
     * @return DateTimeInterface|null
     */
    public function startDate();

    /**
     * @param  string|DateTimeInterface $endDate Event end date.
     * @return self
     */
    public function setEndDate($endDate);

    /**
     * @return DateTimeInterface|null
     */
    public function endDate();
}
